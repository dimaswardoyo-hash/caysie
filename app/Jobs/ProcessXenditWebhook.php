<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessXenditWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Jumlah percobaan ulang jika job gagal (misal DB sempat down).
     */
    public int $tries = 3;

    /**
     * Jeda antar percobaan ulang (detik): 10s, 30s, 60s.
     */
    public array $backoff = [10, 30, 60];

    public function __construct(private array $payload) {}

    public function handle(): void
    {
        $externalId = $this->payload['external_id'] ?? null;

        if (!$externalId) {
            Log::warning('[Xendit Webhook Job] Payload tanpa external_id, dilewati.', $this->payload);
            return;
        }

        $order = Order::where('order_number', $externalId)->first();

        if (!$order) {
            Log::warning('[Xendit Webhook Job] Order tidak ditemukan', ['external_id' => $externalId]);
            return;
        }

        $xenditStatus = strtoupper($this->payload['status'] ?? '');

        match ($xenditStatus) {
            'PAID' => $this->markPaid($order),
            'EXPIRED' => $this->markExpired($order),
            default => Log::info("[Xendit Webhook Job] Status tidak ditangani: {$xenditStatus}", ['order_number' => $externalId]),
        };
    }

    private function markPaid(Order $order): void
    {
        // Idempoten: kalau order sudah bukan pending (misal sudah diproses request
        // sebelumnya, atau race antara webhook & polling checkStatus), abaikan saja.
        if ($order->status !== 'pending') {
            return;
        }

        $order->update([
            'status' => 'confirmed',
            'paid_at' => now(),
            'payment_method' => $this->payload['payment_method'] ?? null,
            'payment_channel' => $this->payload['payment_channel'] ?? null,
        ]);

        Log::info('[Xendit Webhook Job] Order PAID', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'payment_method' => $this->payload['payment_method'] ?? null,
        ]);

        // Titik yang tepat untuk menambah efek samping tanpa memperlambat respons ke Xendit:
        // - Kirim notifikasi WA/email konfirmasi pembayaran ke customer
        // - Kirim notifikasi ke admin (order baru masuk & sudah dibayar)
    }

    private function markExpired(Order $order): void
    {
        if ($order->status !== 'pending') {
            return;
        }

        $order->restoreStock();
        $order->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancel_reason' => 'Invoice Xendit kedaluwarsa.',
            'cancelled_by' => 'system',
        ]);

        Log::info('[Xendit Webhook Job] Order EXPIRED/dibatalkan', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
        ]);
    }

    /**
     * Dipanggil kalau job tetap gagal setelah semua percobaan habis.
     * Penting untuk kasus pembayaran — jangan sampai gagal diam-diam.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('[Xendit Webhook Job] Gagal total setelah retry', [
            'payload' => $this->payload,
            'error' => $exception->getMessage(),
        ]);
    }
}
