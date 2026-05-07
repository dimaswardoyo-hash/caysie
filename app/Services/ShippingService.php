<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class ShippingService
{
    // Zona pengiriman dari Gunungkidul
    private array $provinceZone = [
        'DAERAH ISTIMEWA YOGYAKARTA' => 1,
        'JAWA TENGAH' => 1,
        'JAWA TIMUR' => 2,
        'JAWA BARAT' => 2,
        'DKI JAKARTA' => 2,
        'BANTEN' => 2,
        'BALI' => 2,
        'LAMPUNG' => 3,
        'SUMATERA SELATAN' => 3,
        'SUMATERA BARAT' => 3,
        'SUMATERA UTARA' => 3,
        'RIAU' => 3,
        'KEPULAUAN RIAU' => 3,
        'JAMBI' => 3,
        'BENGKULU' => 3,
        'ACEH' => 3,
        'KEPULAUAN BANGKA BELITUNG' => 3,
        'KALIMANTAN BARAT' => 3,
        'KALIMANTAN SELATAN' => 3,
        'KALIMANTAN TENGAH' => 3,
        'KALIMANTAN TIMUR' => 3,
        'KALIMANTAN UTARA' => 3,
        'NUSA TENGGARA BARAT' => 3,
        'SULAWESI SELATAN' => 4,
        'SULAWESI UTARA' => 4,
        'SULAWESI TENGAH' => 4,
        'SULAWESI TENGGARA' => 4,
        'SULAWESI BARAT' => 4,
        'GORONTALO' => 4,
        'MALUKU' => 4,
        'MALUKU UTARA' => 4,
        'NUSA TENGGARA TIMUR' => 4,
        'PAPUA' => 4,
        'PAPUA BARAT' => 4,
        'PAPUA TENGAH' => 4,
        'PAPUA PEGUNUNGAN' => 4,
        'PAPUA SELATAN' => 4,
    ];

    // Tarif per kurir [zona1, zona2, zona3, zona4]
    // min_charge = biaya untuk 1kg pertama
    // next_kg    = tambahan per kg berikutnya
    private array $couriers = [
        'jne' => [
            'name' => 'JNE',
            'icon' => '🟡',
            'services' => [
                [
                    'code' => 'OKE',
                    'name' => 'Ongkos Kirim Ekonomis',
                    'description' => 'Hemat untuk kiriman reguler',
                    'min_charge' => [9000, 12000, 18000, 28000],
                    'next_kg' => [2000, 3000, 4000, 6000],
                    'etd' => ['4-5', '5-7', '7-14', '14-21'],
                ],
                [
                    'code' => 'REG',
                    'name' => 'Reguler',
                    'description' => 'Pengiriman reguler terpercaya',
                    'min_charge' => [11000, 15000, 22000, 35000],
                    'next_kg' => [2500, 3500, 5000, 8000],
                    'etd' => ['2-3', '3-4', '5-7', '7-14'],
                ],
                [
                    'code' => 'YES',
                    'name' => 'Yakin Esok Sampai',
                    'description' => 'Garansi tiba keesokan hari',
                    'min_charge' => [20000, 28000, 45000, 70000],
                    'next_kg' => [5000, 7000, 10000, 15000],
                    'etd' => ['1', '1', '1-2', '2-3'],
                ],
            ],
        ],
        'jnt' => [
            'name' => 'J&T Express',
            'icon' => '🔴',
            'services' => [
                [
                    'code' => 'EZ',
                    'name' => 'J&T EZ',
                    'description' => 'Pengiriman cepat dan andal',
                    'min_charge' => [10000, 14000, 20000, 32000],
                    'next_kg' => [2500, 3500, 5000, 7000],
                    'etd' => ['2-3', '3-4', '5-7', '7-10'],
                ],
                [
                    'code' => 'ECON',
                    'name' => 'J&T Economy',
                    'description' => 'Hemat ongkir lebih banyak',
                    'min_charge' => [8000, 11000, 16000, 25000],
                    'next_kg' => [2000, 2800, 4000, 6000],
                    'etd' => ['4-5', '5-6', '7-9', '10-14'],
                ],
            ],
        ],
        'sicepat' => [
            'name' => 'SiCepat',
            'icon' => '🟢',
            'services' => [
                [
                    'code' => 'BEST',
                    'name' => 'BEST',
                    'description' => 'Besok sampai tujuan',
                    'min_charge' => [10000, 14000, 21000, 33000],
                    'next_kg' => [2500, 3500, 5000, 7500],
                    'etd' => ['1', '1-2', '3-4', '5-7'],
                ],
                [
                    'code' => 'GOKIL',
                    'name' => 'Go Kilat',
                    'description' => 'Pengiriman super kilat',
                    'min_charge' => [15000, 22000, 35000, 55000],
                    'next_kg' => [4000, 5500, 8000, 12000],
                    'etd' => ['1', '1', '1-2', '2-3'],
                ],
                [
                    'code' => 'HALU',
                    'name' => 'Hemat & Luar Biasa',
                    'description' => 'Tarif paling hemat',
                    'min_charge' => [7500, 10000, 15000, 24000],
                    'next_kg' => [1800, 2500, 3500, 5500],
                    'etd' => ['5-7', '6-8', '9-12', '14-21'],
                ],
            ],
        ],
        'anteraja' => [
            'name' => 'Anteraja',
            'icon' => '🟠',
            'services' => [
                [
                    'code' => 'REG',
                    'name' => 'Reguler',
                    'description' => 'Pengiriman reguler Anteraja',
                    'min_charge' => [9500, 13000, 19000, 30000],
                    'next_kg' => [2200, 3200, 4500, 7000],
                    'etd' => ['2-3', '3-4', '5-7', '7-10'],
                ],
                [
                    'code' => 'NEXT',
                    'name' => 'Next Day',
                    'description' => 'Sampai keesokan harinya',
                    'min_charge' => [18000, 25000, 40000, 65000],
                    'next_kg' => [4500, 6000, 9000, 14000],
                    'etd' => ['1', '1', '2', '3'],
                ],
            ],
        ],
        'pos' => [
            'name' => 'POS Indonesia',
            'icon' => '🔵',
            'services' => [
                [
                    'code' => 'Kilat Khusus',
                    'name' => 'Kilat Khusus',
                    'description' => 'Kiriman kilat ke seluruh Indonesia',
                    'min_charge' => [9000, 12000, 17000, 27000],
                    'next_kg' => [2000, 3000, 4000, 6500],
                    'etd' => ['3-4', '4-5', '6-9', '9-15'],
                ],
                [
                    'code' => 'Jumbo Ekonomi',
                    'name' => 'Jumbo Ekonomi',
                    'description' => 'Hemat untuk paket besar',
                    'min_charge' => [7000, 9500, 14000, 22000],
                    'next_kg' => [1500, 2200, 3200, 5000],
                    'etd' => ['6-10', '8-12', '12-18', '18-30'],
                ],
            ],
        ],
        'tiki' => [
            'name' => 'TIKI',
            'icon' => '🟣',
            'services' => [
                [
                    'code' => 'REG',
                    'name' => 'Reguler',
                    'description' => 'Pengiriman reguler TIKI',
                    'min_charge' => [9000, 13000, 19000, 30000],
                    'next_kg' => [2200, 3200, 4500, 7000],
                    'etd' => ['2-3', '3-4', '5-8', '8-12'],
                ],
                [
                    'code' => 'ONS',
                    'name' => 'Over Night Service',
                    'description' => 'Pengiriman malam hari',
                    'min_charge' => [19000, 26000, 42000, 68000],
                    'next_kg' => [4500, 6500, 9500, 15000],
                    'etd' => ['1', '1', '1-2', '2-3'],
                ],
            ],
        ],
    ];

    public function calculate(string $provinceName, int $weightGram): array
    {
        $province = strtoupper(trim($provinceName));
        $zone = $this->getZone($province);
        $zoneIdx = $zone - 1;
        $weightKg = max(1, (int) ceil($weightGram / 1000));

        Log::info('[Shipping] Calculate', [
            'province' => $province,
            'zone' => $zone,
            'weight_g' => $weightGram,
            'weight_kg' => $weightKg,
        ]);

        $results = [];

        foreach ($this->couriers as $code => $courier) {
            foreach ($courier['services'] as $svc) {
                $minCharge = $svc['min_charge'][$zoneIdx];
                $nextKg = $svc['next_kg'][$zoneIdx];

                // Hitung total: 1kg pertama = min_charge, kg berikutnya = next_kg/kg
                $cost = $weightKg <= 1 ? $minCharge : $minCharge + ($weightKg - 1) * $nextKg;

                // Bulatkan ke atas ke kelipatan 500
                $cost = (int) (ceil($cost / 500) * 500);

                $results[] = [
                    'courier_code' => $code,
                    'courier_name' => $courier['name'],
                    'courier_icon' => $courier['icon'],
                    'service' => $svc['code'],
                    'service_name' => $svc['name'],
                    'description' => $svc['description'],
                    'cost' => $cost,
                    'estimate' => $svc['etd'][$zoneIdx],
                    'weight_kg' => $weightKg,
                ];
            }
        }

        usort($results, fn($a, $b) => $a['cost'] - $b['cost']);

        return $results;
    }

    public function getZone(string $provinceName): int
    {
        $province = strtoupper(trim($provinceName));

        // Exact match
        if (isset($this->provinceZone[$province])) {
            return $this->provinceZone[$province];
        }

        // Partial match (handle variasi nama)
        foreach ($this->provinceZone as $key => $zone) {
            if (str_contains($province, $key) || str_contains($key, $province)) {
                return $zone;
            }
        }

        return 3; // default zona 3 (luar jawa)
    }

    public function getZoneInfo(string $provinceName): array
    {
        $zone = $this->getZone($provinceName);
        return [
            'zone' => $zone,
            'desc' => match ($zone) {
                1 => 'DIY & Jawa Tengah (terdekat)',
                2 => 'Pulau Jawa & Bali',
                3 => 'Sumatera & Kalimantan',
                4 => 'Sulawesi, Maluku & Papua',
            },
        ];
    }
}
