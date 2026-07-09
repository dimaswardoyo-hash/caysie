<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Service untuk RajaOngkir API V2 (Komerce).
 * Dokumentasi: https://rajaongkir.komerce.id
 *
 * Menggunakan metode "Step-by-Step" (province -> city -> district -> subdistrict)
 * agar kompatibel langsung dengan UI cascade-dropdown yang sudah ada,
 * plus method calculateCost() untuk cek ongkir.
 */
class RajaOngkirService
{
    private string $apiKey;
    private string $baseUrl;

    // Daftar kode kurir yang didukung RajaOngkir (disesuaikan dengan paket akun kamu)
    public array $couriers = [
        'jne' => 'JNE',
        'pos' => 'POS Indonesia',
        'tiki' => 'TIKI',
        'jnt' => 'J&T Express',
        'sicepat' => 'SiCepat',
        'anteraja' => 'Anteraja',
        'ninja' => 'Ninja Xpress',
        'lion' => 'Lion Parcel',
        'ide' => 'ID Express',
        'sap' => 'SAP Express',
    ];

    public function __construct()
    {
        $this->apiKey = config('services.rajaongkir.api_key');
        $this->baseUrl = config('services.rajaongkir.base_url', 'https://rajaongkir.komerce.id/api/v1');
    }

    // ── WILAYAH (Step-by-Step Method) ─────────────────────

    public function getProvinces(): array
    {
        return Cache::remember('ro_provinces', now()->addWeek(), function () {
            return $this->parseList($this->get('/destination/province'), 'getProvinces');
        });
    }

    public function getCities(string $provinceId): array
    {
        return Cache::remember("ro_cities_{$provinceId}", now()->addWeek(), function () use ($provinceId) {
            return $this->parseList($this->get("/destination/city/{$provinceId}"), "getCities({$provinceId})");
        });
    }

    public function getDistricts(string $cityId): array
    {
        return Cache::remember("ro_districts_{$cityId}", now()->addWeek(), function () use ($cityId) {
            return $this->parseList($this->get("/destination/district/{$cityId}"), "getDistricts({$cityId})");
        });
    }

    public function getVillages(string $districtId): array
    {
        return Cache::remember("ro_villages_{$districtId}", now()->addWeek(), function () use ($districtId) {
            return $this->parseList($this->get("/destination/sub-district/{$districtId}"), "getVillages({$districtId})");
        });
    }

    // Dipakai oleh perintah artisan rajaongkir:find-origin untuk mencari ID kota toko sendiri
    public function searchDestination(string $keyword, int $limit = 10): array
    {
        $res = $this->get('/destination/domestic-destination', [
            'search' => $keyword,
            'limit' => $limit,
            'offset' => 0,
        ]);

        return $this->parseList($res, "searchDestination({$keyword})");
    }

    // ── CEK ONGKIR ─────────────────────────────────────────

    public function checkOngkir(string $origin, string $destination, int $weight, array $couriers = []): array
    {
        $couriersToCheck = empty($couriers) ? array_keys($this->couriers) : $couriers;
        $courierParam = implode(':', $couriersToCheck);

        try {
            $res = Http::timeout(20)
                ->withoutVerifying()
                ->withHeaders(['key' => $this->apiKey])
                ->asForm()
                ->post($this->baseUrl . '/calculate/domestic-cost', [
                    'origin' => $origin,
                    'destination' => $destination,
                    'weight' => $weight,
                    'courier' => $courierParam,
                    'price' => 'lowest',
                ]);

            if (!$res->successful()) {
                Log::warning('[RajaOngkir] checkOngkir HTTP ' . $res->status(), ['body' => substr($res->body(), 0, 500)]);
                return [];
            }

            $json = $res->json();
            $data = $json['data'] ?? [];

            Log::info('[RajaOngkir] checkOngkir parsed', ['count' => count($data)]);

            return $this->formatRates($data);
        } catch (\Exception $e) {
            Log::error('[RajaOngkir] checkOngkir exception: ' . $e->getMessage());
            return [];
        }
    }

    // ── PRIVATE HELPERS ────────────────────────────────────

    private function get(string $path, array $params = [])
    {
        return Http::timeout(20)
            ->withoutVerifying()
            ->withHeaders(['key' => $this->apiKey])
            ->get($this->baseUrl . $path, $params);
    }

    private function parseList($response, string $ctx = ''): array
    {
        if (!$response || !$response->successful()) {
            Log::warning("[RajaOngkir] {$ctx} HTTP gagal", ['status' => $response?->status()]);
            return [];
        }

        $json = $response->json();

        if (isset($json['data']) && is_array($json['data'])) {
            return $json['data'];
        }

        Log::warning("[RajaOngkir] {$ctx} format tidak dikenal", ['keys' => array_keys($json ?? [])]);
        return [];
    }

    private function formatRates(array $data): array
    {
        $results = [];

        foreach ($data as $item) {
            $code = strtolower($item['code'] ?? '');
            $name = $item['name'] ?? ($this->couriers[$code] ?? strtoupper($code));

            $results[] = [
                'courier_code' => $code,
                'courier_name' => $name,
                'service' => strtoupper($item['service'] ?? ''),
                'description' => $item['description'] ?? '',
                'cost' => (int) ($item['cost'] ?? 0),
                'estimate' => trim((string) ($item['etd'] ?? '-')),
                'note' => '',
            ];
        }

        usort($results, fn($a, $b) => $a['cost'] - $b['cost']);

        return $results;
    }
}
