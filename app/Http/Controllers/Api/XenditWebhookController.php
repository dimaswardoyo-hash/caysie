<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\XenditService;
use App\Jobs\ProcessXenditWebhook;
use Illuminate\Support\Facades\Log;

class XenditWebhookController extends Controller
{
    public function __construct(private XenditService $xendit) {}

    /**
     * Xendit mengirim POST ke endpoint ini setiap ada perubahan status pembayaran.
     * Endpoint ini TIDAK boleh pakai middleware 'auth' — Xendit tidak login.
     * Tapi harus diverifikasi via X-CALLBACK-TOKEN.
     *
     * PENTING: handler ini HARUS tetap cepat & synchronous hanya untuk verifikasi.
     * Update status order (efek samping: kirim notif, dsb) dilempar ke queue supaya
     * Xendit langsung dapat respons 200 dan tidak retry berkali-kali kalau proses
     * di sisi kita lambat.
     */
    public function handle(Request $request)
    {
        // 1. Verifikasi token — wajib synchronous, ini garis pertahanan keamanan.
        $callbackToken = $request->header('x-callback-token') ?? '';

        if (!$this->xendit->verifyWebhook($callbackToken)) {
            Log::warning('[Xendit Webhook] Token tidak valid', [
                'ip' => $request->ip(),
                'token' => substr($callbackToken, 0, 10) . '...',
            ]);
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $payload = $request->all();

        // 2. Validasi dasar sebelum masuk antrian (fail fast, hemat job sia-sia)
        if (empty($payload['external_id'])) {
            return response()->json(['message' => 'No external_id'], 400);
        }

        Log::info('[Xendit Webhook] Diterima, dimasukkan ke antrian', [
            'external_id' => $payload['external_id'],
            'status' => $payload['status'] ?? null,
        ]);

        // 3. Lempar ke queue — controller selesai di sini, respons langsung dikirim.
        ProcessXenditWebhook::dispatch($payload);

        return response()->json(['message' => 'OK']);
    }
}
