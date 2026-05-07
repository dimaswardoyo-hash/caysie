<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class BinderByteService
{
    private string $apiKey;
    private string $baseUrl;

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
        $this->apiKey = config('services.binderbyte.api_key');
        $this->baseUrl = config('services.binderbyte.base_url', 'https://api.binderbyte.com');
    }

    // ── WILAYAH ──────────────────────────────────────────

    public function getProvinces(): array
    {
        return Cache::remember('bb_provinces', now()->addDay(), function () {
            return $this->parseWilayah($this->get('/wilayah/provinsi', []), 'getProvinces');
        });
    }

    public function getCities(string $provinceId): array
    {
        return Cache::remember("bb_cities_{$provinceId}", now()->addDay(), function () use ($provinceId) {
            return $this->parseWilayah($this->get('/wilayah/kabupaten', ['id_provinsi' => $provinceId]), "getCities({$provinceId})");
        });
    }

    public function getDistricts(string $cityId): array
    {
        return Cache::remember("bb_districts_{$cityId}", now()->addDay(), function () use ($cityId) {
            return $this->parseWilayah($this->get('/wilayah/kecamatan', ['id_kabupaten' => $cityId]), "getDistricts({$cityId})");
        });
    }

    public function getVillages(string $districtId): array
    {
        return Cache::remember("bb_villages_{$districtId}", now()->addDay(), function () use ($districtId) {
            return $this->parseWilayah($this->get('/wilayah/kelurahan', ['id_kecamatan' => $districtId]), "getVillages({$districtId})");
        });
    }

    // ── CEK ONGKIR ───────────────────────────────────────

    public function checkOngkir(string $origin, string $destination, int $weight, array $couriers = []): array
    {
        $couriersToCheck = empty($couriers) ? array_keys($this->couriers) : $couriers;

        $allResults = [];

        foreach ($couriersToCheck as $courierCode) {
            try {
                $response = $this->get('/v1/ongkir', [
                    'origin' => $origin,
                    'destination' => $destination,
                    'weight' => $weight,
                    'courier' => $courierCode,
                ]);

                if (!$response || !$response->successful()) {
                    Log::warning("[BB] Ongkir {$courierCode} HTTP {$response?->status()}");
                    continue;
                }

                $json = $response->json();
                $body = $response->body();

                Log::info("[BB] Ongkir RAW {$courierCode}", [
                    'code' => $json['code'] ?? null,
                    'msg' => $json['message'] ?? null,
                    'body' => substr($body, 0, 500),
                ]);

                // ── Cek API error ──────────────────────────
                $code = (string) ($json['code'] ?? '');
                if ($code !== '200') {
                    Log::warning("[BB] Ongkir {$courierCode} API error: " . ($json['message'] ?? $body));
                    continue;
                }

                // ── Ambil data ─────────────────────────────
                $data = $json['data'] ?? null;
                if (!$data) {
                    Log::warning("[BB] Ongkir {$courierCode} data kosong");
                    continue;
                }

                // ── Parse semua kemungkinan format ─────────
                $services = $this->parseOngkirData($data, $courierCode);

                Log::info("[BB] Ongkir {$courierCode} parsed: " . count($services) . ' services');

                $allResults = array_merge($allResults, $services);
            } catch (\Exception $e) {
                Log::error("[BB] Ongkir {$courierCode}: " . $e->getMessage());
                continue;
            }
        }

        usort($allResults, fn($a, $b) => ($a['cost'] ?? 0) - ($b['cost'] ?? 0));

        return $allResults;
    }

    // ── CEK RESI ─────────────────────────────────────────

    public function trackPackage(string $courier, string $awb): array
    {
        try {
            $response = $this->get('/v1/track', [
                'courier' => strtolower($courier),
                'awb' => $awb,
            ]);

            $json = $response->json();
            return $json['data'] ?? ['error' => $json['message'] ?? 'Resi tidak ditemukan.'];
        } catch (\Exception $e) {
            Log::error('[BB] trackPackage: ' . $e->getMessage());
            return ['error' => 'Koneksi gagal.'];
        }
    }

    // ── PRIVATE HELPERS ──────────────────────────────────

    private function get(string $path, array $params)
    {
        return Http::timeout(20)
            ->withoutVerifying()
            ->get($this->baseUrl . $path, array_merge(['api_key' => $this->apiKey], $params));
    }

    private function parseWilayah($response, string $ctx = ''): array
    {
        if (!$response || !$response->successful()) {
            Log::warning("[BB] {$ctx} HTTP gagal");
            return [];
        }

        $json = $response->json();

        if (isset($json['value']) && is_array($json['value'])) {
            return $json['value'];
        }
        if (isset($json['data']) && is_array($json['data'])) {
            return $json['data'];
        }
        if (is_array($json) && isset($json[0])) {
            return $json;
        }

        Log::warning("[BB] {$ctx} format tidak dikenal", ['keys' => array_keys($json ?? [])]);
        return [];
    }

    // ── Parse data ongkir — handle SEMUA format BinderByte

    private function parseOngkirData($data, string $courierCode): array
    {
        $courierName = $this->couriers[$courierCode] ?? strtoupper($courierCode);

        // ── Format 1: {"origin":{...},"destination":{...},"results":[...]}
        // Ini format paling umum BinderByte v1/ongkir
        if (isset($data['results']) && is_array($data['results'])) {
            Log::info("[BB] Ongkir {$courierCode} → Format 1 (results wrapper)");
            return $this->extractFromCourierArray($data['results'], $courierCode, $courierName);
        }

        // ── Format 2: [{"code":"jne","name":"JNE","costs":[...]}]
        if (is_array($data) && isset($data[0]['costs'])) {
            Log::info("[BB] Ongkir {$courierCode} → Format 2 (array of courier)");
            return $this->extractFromCourierArray($data, $courierCode, $courierName);
        }

        // ── Format 3: {"code":"jne","name":"JNE","costs":[...]}
        if (isset($data['costs'])) {
            Log::info("[BB] Ongkir {$courierCode} → Format 3 (single courier object)");
            return $this->extractFromCourierArray([$data], $courierCode, $courierName);
        }

        // ── Format 4: langsung array service
        // [{"service":"REG","description":"...","cost":[{"value":9000,"etd":"2-3"}]}]
        if (is_array($data) && isset($data[0]['service'])) {
            Log::info("[BB] Ongkir {$courierCode} → Format 4 (direct services)");
            $results = [];
            foreach ($data as $svc) {
                $item = $this->extractServiceItem($svc, $courierCode, $courierName);
                if ($item) {
                    $results[] = $item;
                }
            }
            return $results;
        }

        Log::warning("[BB] Ongkir {$courierCode} — tidak ada format yang cocok", [
            'type' => gettype($data),
            'keys' => is_array($data) ? array_keys($data) : [],
            'sample' => substr(json_encode($data), 0, 300),
        ]);

        return [];
    }

    private function extractFromCourierArray(array $couriers, string $defaultCode, string $defaultName): array
    {
        $results = [];

        foreach ($couriers as $courier) {
            $code = strtolower($courier['code'] ?? $defaultCode);
            $name = $courier['name'] ?? $defaultName;

            foreach ($courier['costs'] ?? [] as $svc) {
                $item = $this->extractServiceItem($svc, $code, $name);
                if ($item) {
                    $results[] = $item;
                }
            }
        }

        return $results;
    }

    private function extractServiceItem(array $svc, string $code, string $name): ?array
    {
        // cost bisa: int, atau [{value:X, etd:Y, note:Z}]
        $costRaw = $svc['cost'] ?? 0;

        if (is_array($costRaw)) {
            $costVal = (int) ($costRaw[0]['value'] ?? 0);
            $etd = (string) ($costRaw[0]['etd'] ?? '-');
            $note = (string) ($costRaw[0]['note'] ?? '');
        } else {
            $costVal = (int) $costRaw;
            $etd = (string) ($svc['etd'] ?? '-');
            $note = (string) ($svc['note'] ?? '');
        }

        // Bersihkan etd — kadang "2 - 3 HARI" jadi "2-3"
        $etd = trim(preg_replace('/\s*(hari|days|day)\s*/i', '', $etd));
        $etd = $etd ?: '-';

        return [
            'courier_code' => $code,
            'courier_name' => $name,
            'service' => strtoupper($svc['service'] ?? ''),
            'description' => $svc['description'] ?? '',
            'cost' => $costVal,
            'estimate' => $etd,
            'note' => $note,
        ];
    }
}
