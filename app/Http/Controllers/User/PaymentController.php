<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Services\XenditService;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct(private XenditService $xendit) {}

    // ── Halaman pembayaran (setelah checkout) ────────────────
    public function show(Order $order)
    {
        // Hanya pemilik order
        abort_if($order->user_id !== auth()->id(), 403);

        // Kalau sudah bayar, redirect ke orders
        if (in_array($order->status, ['paid', 'confirmed', 'processing', 'shipped', 'delivered'])) {
            return redirect()->route('user.orders.show', $order)->with('success', 'Pesanan ini sudah dibayar.');
        }

        // Kalau invoice_url sudah ada, langsung pakai
        if ($order->xendit_invoice_url) {
            return view('user.payment', compact('order'));
        }

        // Buat invoice baru di Xendit
        $items = $order->items
            ->map(
                fn($i) => [
                    'name' => $i->product_name . ' (' . $i->size . ')',
                    'quantity' => $i->quantity,
                    'price' => (int) $i->price,
                    'category' => 'Fashion',
                ],
            )
            ->toArray();

        $result = $this->xendit->createInvoice([
            'order_number' => $order->order_number,
            'amount' => (int) $order->total_amount,
            'customer_name' => $order->receiver_name,
            'customer_email' => $order->user->email,
            'customer_phone' => $order->receiver_phone,
            'items' => $items,
            'shipping_cost' => $order->shipping_cost,
            'courier' => $order->courier_name . ' ' . $order->courier_service,
            'description' => 'Pembayaran ' . $order->order_number . ' — Caysie Store',
        ]);

        if (!$result['success']) {
            return redirect()
                ->route('user.orders.show', $order)
                ->with('error', 'Gagal membuat halaman pembayaran: ' . $result['message']);
        }

        // Simpan invoice ke order
        $order->update([
            'xendit_invoice_id' => $result['invoice_id'],
            'xendit_invoice_url' => $result['invoice_url'],
            'xendit_expiry' => $result['expiry_date'],
            'status' => 'waiting_payment',
        ]);

        return view('user.payment', compact('order'));
    }

    // ── Webhook dari Xendit (POST, tanpa auth) ───────────────
    public function webhook(Request $request)
    {
        // Verifikasi token dari header Xendit
        $token = $request->header('x-callback-token', '');

        if (!$this->xendit->verifyWebhookToken($token)) {
            Log::warning('[Xendit] Webhook token tidak valid', ['ip' => $request->ip()]);
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $payload = $request->all();
        Log::info('[Xendit] Webhook received', [
            'event' => $payload['status'] ?? '-',
            'external_id' => $payload['external_id'] ?? '-',
        ]);

        $externalId = $payload['external_id'] ?? null;
        $status = strtoupper($payload['status'] ?? '');

        if (!$externalId) {
            return response()->json(['message' => 'OK']);
        }

        $order = Order::where('order_number', $externalId)->first();

        if (!$order) {
            Log::warning('[Xendit] Webhook: order tidak ditemukan', ['external_id' => $externalId]);
            return response()->json(['message' => 'OK']); // tetap 200 agar Xendit tidak retry terus
        }

        match ($status) {
            'PAID', 'SETTLED' => $order->update([
                'status' => 'confirmed',
                'paid_at' => now(),
                'payment_method' => $payload['payment_method'] ?? null,
                'xendit_payment_id' => $payload['id'] ?? null,
            ]),
            'EXPIRED' => $order->update(['status' => 'payment_expired']),
            default => null,
        };

        Log::info('[Xendit] Order status updated', [
            'order_number' => $externalId,
            'new_status' => $order->fresh()->status,
        ]);

        return response()->json(['message' => 'OK']);
    }

    // ── Halaman sukses (redirect dari Xendit) ────────────────
    public function success(Request $request)
    {
        // Xendit menyertakan ?external_id=ORD-xxx di query string
        $externalId = $request->query('external_id');
        $order = $externalId
            ? Order::where('order_number', $externalId)
                ->where('user_id', auth()->id())
                ->first()
            : null;

        return view('user.payment-success', compact('order'));
    }

    // ── Halaman gagal (redirect dari Xendit) ─────────────────
    public function failed(Request $request)
    {
        $externalId = $request->query('external_id');
        $order = $externalId
            ? Order::where('order_number', $externalId)
                ->where('user_id', auth()->id())
                ->first()
            : null;

        return view('user.payment-failed', compact('order'));
    }

    // ── Cek status pembayaran (polling AJAX) ─────────────────
    public function checkStatus(Order $order)
    {
        abort_if($order->user_id !== auth()->id(), 403);

        // Jika sudah confirmed dari webhook, langsung return
        if (in_array($order->status, ['confirmed', 'processing', 'shipped', 'delivered'])) {
            return response()->json([
                'paid' => true,
                'order_status' => $order->status,
                'redirect' => route('user.orders.show', $order),
            ]);
        }

        // Fallback: manual cek ke Xendit API
        if ($order->xendit_invoice_id) {
            $result = $this->xendit->getInvoice($order->xendit_invoice_id);
            if ($result['success']) {
                $xStatus = strtoupper($result['data']['status'] ?? '');
                if (in_array($xStatus, ['PAID', 'SETTLED'])) {
                    $order->update(['status' => 'confirmed', 'paid_at' => now()]);
                    return response()->json([
                        'paid' => true,
                        'order_status' => 'confirmed',
                        'redirect' => route('user.orders.show', $order),
                    ]);
                }
            }
        }

        return response()->json([
            'paid' => false,
            'order_status' => $order->status,
        ]);
    }
}
