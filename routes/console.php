<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Batalkan order pending yang kedaluwarsa & kembalikan stok, tidak bergantung
// pada user membuka halaman pesanannya. Pastikan `php artisan schedule:run`
// dijalankan via cron (* * * * *) di server produksi.
Schedule::command('orders:expire')->everyFiveMinutes();
