<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\XenditService;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

class XenditWebhookController extends Controller
{
    public function __construct(private XenditService $xendit) {}

    /**
     * Xendit mengirim POST ke endpoint ini setiap ada perubahan status pembayaran.
     * Endpoint ini TIDAK boleh pakai middleware 'auth' — Xendit tidak login.
     * Tapi harus diverifikasi via X-CALLBACK-TOKEN.
     */
    public function handle(Request $request)
    {
        // 1. Verifikasi token
        $callbackToken = $request->header('x-callback-token') ?? '';

        if (!$this->xendit->verifyWebhook($callbackToken)) {
            Log::warning('[Xendit Webhook] Token tidak valid', [
                'ip' => $request->ip(),
                'token' => substr($callbackToken, 0, 10) . '...',
            ]);
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $payload = $request->all();

        Log::info('[Xendit Webhook] Diterima', [
            'external_id' => $payload['external_id'] ?? null,
            'status' => $payload['status'] ?? null,
        ]);

        // 2. Cari order berdasarkan external_id (= order_number kita)
        $externalId = $payload['external_id'] ?? null;
        if (!$externalId) {
            return response()->json(['message' => 'No external_id'], 400);
        }

        $order = Order::where('order_number', $externalId)->first();
        if (!$order) {
            Log::warning('[Xendit Webhook] Order tidak ditemukan', ['external_id' => $externalId]);
            return response()->json(['message' => 'Order not found'], 404);
        }

        // 3. Update status order sesuai status Xendit
        $xenditStatus = strtoupper($payload['status'] ?? '');

        match ($xenditStatus) {
            'PAID' => $this->markPaid($order, $payload),
            'EXPIRED' => $this->markExpired($order),
            default => Log::info("[Xendit Webhook] Status tidak ditangani: {$xenditStatus}"),
        };

        return response()->json(['message' => 'OK']);
    }

    private function markPaid(Order $order, array $payload): void
    {
        if ($order->status === 'pending') {
            $order->update([
                'status' => 'confirmed',
                'paid_at' => now(),
                'payment_method' => $payload['payment_method'] ?? null,
                'payment_channel' => $payload['payment_channel'] ?? null,
            ]);

            Log::info('[Xendit Webhook] Order PAID', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'payment_method' => $payload['payment_method'] ?? null,
            ]);
        }
    }

    private function markExpired(Order $order): void
    {
        if ($order->status === 'pending') {
            $order->update(['status' => 'cancelled']);

            Log::info('[Xendit Webhook] Order EXPIRED/dibatalkan', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
            ]);
        }
    }
}
