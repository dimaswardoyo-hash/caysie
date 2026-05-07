<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\BinderByteService;
use App\Models\Order;

class TrackingController extends Controller
{
    public function __construct(private BinderByteService $bb) {}

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

    // Track dari order langsung
    public function trackOrder(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }
        if (!$order->tracking_number || !$order->courier_code) {
            return response()->json(['success' => false, 'message' => 'Nomor resi belum tersedia.'], 404);
        }

        $result = $this->bb->trackPackage($order->courier_code, $order->tracking_number);
        return response()->json(['success' => !isset($result['error']), 'data' => $result]);
    }
}
