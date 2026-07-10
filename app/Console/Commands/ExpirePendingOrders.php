<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExpirePendingOrders extends Command
{
    protected $signature = 'orders:expire';

    protected $description = 'Batalkan semua order pending yang sudah melewati payment_deadline dan kembalikan stoknya. Dijalankan terjadwal (lihat routes/console.php) agar tidak bergantung user membuka halaman pesanan.';

    public function handle(): int
    {
        $expired = Order::where('status', 'pending')
            ->whereNotNull('payment_deadline')
            ->where('payment_deadline', '<', now())
            ->get();

        if ($expired->isEmpty()) {
            $this->info('Tidak ada order pending yang kedaluwarsa.');
            return self::SUCCESS;
        }

        $count = 0;
        foreach ($expired as $order) {
            DB::beginTransaction();
            try {
                $order->restoreStock();
                $order->update([
                    'status' => 'cancelled',
                    'cancelled_at' => now(),
                    'cancel_reason' => 'Otomatis dibatalkan karena melewati batas waktu pembayaran.',
                    'cancelled_by' => 'system',
                ]);
                DB::commit();
                $count++;
            } catch (\Throwable $e) {
                DB::rollBack();
                Log::error('[ExpirePendingOrders] Gagal membatalkan order #' . $order->order_number . ': ' . $e->getMessage());
            }
        }

        $this->info("{$count} order pending berhasil dibatalkan otomatis & stok dikembalikan.");
        return self::SUCCESS;
    }
}
