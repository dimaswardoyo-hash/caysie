<?php

namespace App\Console\Commands;

use App\Services\RajaOngkirService;
use Illuminate\Console\Command;

class FindRajaOngkirOrigin extends Command
{
    protected $signature = 'rajaongkir:find-origin {keyword=Gunungkidul}';

    protected $description = 'Cari ID lokasi (destination_id) di RajaOngkir berdasarkan nama kota/kabupaten, dipakai untuk mengisi RAJAONGKIR_ORIGIN_ID di .env';

    public function handle(RajaOngkirService $rajaOngkir)
    {
        $keyword = $this->argument('keyword');
        $this->info("Mencari lokasi untuk kata kunci: \"{$keyword}\" ...");

        $results = $rajaOngkir->searchDestination($keyword);

        if (empty($results)) {
            $this->error('Tidak ada hasil. Cek apakah RAJAONGKIR_API_KEY di .env sudah benar, atau coba kata kunci lain (misal: "Wonosari" atau "Gunung Kidul").');
            return 1;
        }

        $rows = [];
        foreach ($results as $r) {
            $rows[] = [$r['id'] ?? '-', $r['label'] ?? ($r['subdistrict_name'] ?? ($r['city_name'] ?? '-')), $r['province_name'] ?? '-', $r['zip_code'] ?? ($r['postal_code'] ?? '-')];
        }

        $this->table(['ID', 'Label', 'Provinsi', 'Kode Pos'], $rows);

        $this->newLine();
        $this->info('Salin nilai kolom "ID" yang paling cocok dengan lokasi toko kamu (Gunungkidul / Wonosari),');
        $this->info('lalu tempel ke .env sebagai:  RAJAONGKIR_ORIGIN_ID=<ID_disini>');

        return 0;
    }
}
