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
