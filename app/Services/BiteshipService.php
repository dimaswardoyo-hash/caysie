<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BiteshipService
{
    private string $apiKey;
    private string $baseUrl;
    private string $originPostalCode = '55813'; // Wonosari, Gunungkidul

    public function __construct()
    {
        $this->apiKey = config('services.biteship.api_key');
        $this->baseUrl = config('services.biteship.base_url', 'https://api.biteship.com');
    }

    // ── Search lokasi ────────────────────────────────────
    public function searchLocation(string $keyword): array
    {
        if (empty($this->apiKey)) {
            Log::error('[Biteship] API Key kosong! Cek .env BITESHIP_API_KEY');
            return [];
        }

        try {
            $res = $this->get('/v1/maps/areas', [
                'countries' => 'ID',
                'input' => $keyword,
                'type' => 'single',
            ]);

            $body = $res->body();
            $json = $res->json();

            Log::info('[Biteship] searchLocation', [
                'keyword' => $keyword,
                'status' => $res->status(),
                'success' => $json['success'] ?? null,
                'count' => count($json['areas'] ?? []),
                'body' => substr($body, 0, 300),
            ]);

            // Jika 401 → API key salah
            if ($res->status() === 401) {
                Log::error('[Biteship] 401 Unauthorized — cek API key');
                return [];
            }

            // Format response Biteship: {"success":true,"areas":[...]}
            if (isset($json['areas'])) {
                return $json['areas'];
            }

            // Format alternatif
            if (isset($json['data'])) {
                return $json['data'];
            }

            Log::warning('[Biteship] searchLocation format tidak dikenal', [
                'keys' => array_keys($json ?? []),
            ]);
            return [];
        } catch (\Exception $e) {
            Log::error('[Biteship] searchLocation exception: ' . $e->getMessage());
            return [];
        }
    }

    // ── Cari area_id tujuan dari data alamat yang SUDAH tersimpan ──
    // (kota/kecamatan dari dropdown RajaOngkir + kode pos yang sudah otomatis
    // tersimpan). Admin TIDAK perlu ketik ulang alamat di sini — hanya dipakai
    // di sisi admin untuk generate resi, tidak pernah ditampilkan ke customer.
    // Coba dari yang paling spesifik (kode pos) ke yang paling umum (nama kota),
    // supaya makin besar kemungkinan ketemu meski nama daerahnya kurang populer.
    public function resolveAreaId(string $city, string $district = '', string $postalCode = ''): array
    {
        $attempts = array_unique(array_filter([$postalCode, trim($district . ' ' . $city), $city]));

        foreach ($attempts as $keyword) {
            $areas = $this->searchLocation((string) $keyword);
            if (!empty($areas)) {
                return $areas; // Bisa >1 hasil — biarkan admin yang pilih yang paling cocok
            }
        }

        return [];
    }

    // ── Buat order pengiriman (generate AWB / nomor resi) ─
    public function createOrder(array $payload): array
    {
        if (empty($this->apiKey)) {
            Log::error('[Biteship] API Key kosong! Cek .env BITESHIP_API_KEY');
            return ['success' => false, 'error' => 'API key Biteship belum diset di .env'];
        }

        try {
            $res = $this->post('/v1/orders', $payload);
            $json = $res->json();

            Log::info('[Biteship] createOrder', [
                'status' => $res->status(),
                'success' => $json['success'] ?? null,
                'body' => substr($res->body(), 0, 500),
            ]);

            if ($res->status() === 401) {
                return ['success' => false, 'error' => 'API key tidak valid (401 Unauthorized)'];
            }

            if (!($json['success'] ?? false)) {
                return ['success' => false, 'error' => $json['error'] ?? 'Gagal membuat order di Biteship'];
            }

            return [
                'success' => true,
                'biteship_order_id' => $json['id'] ?? null,
                'waybill_id' => $json['courier']['waybill_id'] ?? null,
                'tracking_id' => $json['courier']['tracking_id'] ?? null,
                'status' => $json['status'] ?? ($json['courier']['status'] ?? null),
                'raw' => $json,
            ];
        } catch (\Exception $e) {
            Log::error('[Biteship] createOrder exception: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // ── Info kontak asal (toko) — dipakai saat createOrder ─
    public function getOriginContact(): array
    {
        return [
            'name' => config('services.biteship.origin_name', 'Caysie Store'),
            'phone' => config('services.biteship.origin_phone', ''),
            'address' => config('services.biteship.origin_address', ''),
        ];
    }

    // ── Cek ongkir ───────────────────────────────────────
    public function getRates(string $destinationAreaId, int $weightGram, string $couriers = 'anteraja,jne,jnt,sicepat,pos,tiki,ninja,lion,sap,id_express'): array
    {
        if (empty($this->apiKey)) {
            Log::error('[Biteship] API Key kosong!');
            return [];
        }

        try {
            $payload = [
                'origin_postal_code' => $this->originPostalCode,
                'destination_area_id' => $destinationAreaId,
                'couriers' => $couriers,
                'items' => [
                    [
                        'name' => 'Produk Caysie',
                        'value' => 50000,
                        'weight' => $weightGram,
                        'length' => 30,
                        'width' => 20,
                        'height' => 5,
                    ],
                ],
            ];

            $res = $this->post('/v1/rates/couriers', $payload);
            $json = $res->json();

            Log::info('[Biteship] getRates', [
                'area_id' => $destinationAreaId,
                'weight' => $weightGram,
                'status' => $res->status(),
                'success' => $json['success'] ?? null,
                'count' => count($json['pricing'] ?? []),
                'body' => substr($res->body(), 0, 500),
            ]);

            if ($res->status() === 401) {
                Log::error('[Biteship] 401 Unauthorized — cek API key');
                return [];
            }

            if (!($json['success'] ?? false)) {
                Log::warning('[Biteship] getRates error: ' . ($json['error'] ?? 'unknown'));
                return [];
            }

            return $this->formatRates($json['pricing'] ?? []);
        } catch (\Exception $e) {
            Log::error('[Biteship] getRates exception: ' . $e->getMessage());
            return [];
        }
    }

    // ── Tracking ─────────────────────────────────────────
    public function trackPackage(string $courierCode, string $waybillId): array
    {
        try {
            $res = $this->get("/v1/trackings/{$waybillId}", [
                'courier' => strtolower($courierCode),
            ]);
            $json = $res->json();
            return $json['success'] ?? false ? $json : ['error' => $json['error'] ?? 'Tidak ditemukan'];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    // ── Format pricing ───────────────────────────────────
    private function formatRates(array $pricing): array
    {
        $results = [];
        foreach ($pricing as $p) {
            if (!($p['available'] ?? true)) {
                continue;
            }
            $results[] = [
                'courier_code' => strtolower($p['courier_code'] ?? ''),
                'courier_name' => $p['courier_name'] ?? '',
                'service' => strtoupper($p['courier_service_code'] ?? ''),
                'service_name' => $p['courier_service_name'] ?? '',
                'description' => $p['description'] ?? '',
                'cost' => (int) ($p['price'] ?? 0),
                'estimate' => trim($p['shipment_duration_range'] ?? '-'),
                'logo' => $p['courier_image_url'] ?? '',
            ];
        }
        usort($results, fn($a, $b) => $a['cost'] - $b['cost']);
        return $results;
    }

    // ── HTTP helpers ─────────────────────────────────────
    private function headers(): array
    {
        return [
            'Authorization' => 'Biteship ' . $this->apiKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }

    private function get(string $path, array $params = [])
    {
        return Http::timeout(20)
            ->withoutVerifying()
            ->withHeaders($this->headers())
            ->get($this->baseUrl . $path, $params);
    }

    private function post(string $path, array $data = [])
    {
        return Http::timeout(20)
            ->withoutVerifying()
            ->withHeaders($this->headers())
            ->post($this->baseUrl . $path, $data);
    }

    public function getOriginPostalCode(): string
    {
        return $this->originPostalCode;
    }
}
