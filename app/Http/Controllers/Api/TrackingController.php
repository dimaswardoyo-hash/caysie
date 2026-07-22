<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\BinderByteService;
use App\Services\BiteshipService;
use App\Models\Order;

class TrackingController extends Controller
{
    public function __construct(private BinderByteService $bb, private BiteshipService $biteship) {}

    // Cek resi manual (input bebas courier + awb dari mana saja) — tetap pakai BinderByte
    public function track(Request $request)
    {
        $request->validate([
            'courier' => 'required|string',
            'awb' => 'required|string',
        ]);

        $result = $this->bb->trackPackage($request->courier, $request->awb);

        if (isset($result['error'])) {
            return response()->json(['success' => false, 'message' => $result['error']], 422);
        }

        return response()->json(['success' => true, 'data' => $result]);
    }

    // Track dari order langsung.
    // Prioritas: kalau order dibuatkan resi otomatis lewat Biteship (ada biteship_order_id),
    // pakai tracking PRIVAT Biteship — ini yang paling akurat & tetap jalan di mode testing,
    // karena Biteship tahu status order miliknya sendiri walau resinya masih simulasi/sandbox.
    // Fallback ke BinderByte hanya untuk resi yang diinput manual oleh admin (bukan via Biteship).
    public function trackOrder(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }
        if (!$order->tracking_number || !$order->courier_code) {
            return response()->json(['success' => false, 'message' => 'Nomor resi belum tersedia.'], 404);
        }

        if ($order->biteship_order_id) {
            // Try a few possible BiteshipService method names to remain compatible
            $result = null;
            foreach (['trackByOrderId', 'trackOrderById', 'trackOrder', 'track', 'trackPackage'] as $method) {
                if (method_exists($this->biteship, $method)) {
                    $result = $this->biteship->{$method}($order->biteship_order_id);
                    break;
                }
            }

            if (isset($result) && !isset($result['error'])) {
                return response()->json([
                    'success' => true,
                    'data' => $this->normalizeBiteship($result, $order),
                    'source' => 'biteship',
                ]);
            }
        }

        // Fallback: BinderByte (untuk resi yang diinput manual, bukan hasil generate Biteship)
        $result = $this->bb->trackPackage($order->courier_code, $order->tracking_number);
        return response()->json(['success' => !isset($result['error']), 'data' => $result, 'source' => 'binderbyte']);
    }

    // ── Samakan bentuk respons Biteship dengan format yang sudah dipakai
    // frontend (order-detail.blade.php: summary.status/courier/awb +
    // history[].desc/location/date), supaya tampilan JS tidak perlu diubah
    // meskipun sumber datanya beda antara Biteship & BinderByte.
    private function normalizeBiteship(array $result, Order $order): array
    {
        $history = collect($result['history'] ?? [])
            ->map(
                fn($h) => [
                    'desc' => $h['note'] ?? '-',
                    'location' => null,
                    'date' => $h['updated_at'] ?? null,
                ],
            )
            // Biteship mengurutkan riwayat dari yang PALING LAMA ke PALING BARU,
            // sementara frontend menampilkan item pertama (index 0) sebagai
            // status TERBARU — jadi urutannya perlu dibalik dulu.
            ->reverse()
            ->values()
            ->all();

        $latestStatus = $result['history'][count($result['history']) - 1]['status'] ?? ($result['status'] ?? null);

        return [
            'summary' => [
                'status' => $latestStatus,
                'courier' => strtoupper($result['courier']['company'] ?? ($order->courier_name ?? '')),
                'awb' => $result['waybill_id'] ?? $order->tracking_number,
            ],
            'history' => $history,
        ];
    }
}
