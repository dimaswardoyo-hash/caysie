<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Services\BiteshipService;
use App\Services\ShippingService;

class OngkirController extends Controller
{
    public function __construct(private BiteshipService $biteship) {}

    public function check(Request $request): JsonResponse
    {
        $request->validate([
            'destination_area_id' => 'required|string',
            'destination_name' => 'nullable|string',
        ]);

        // ── Hitung berat dari keranjang ──────────────────────────
        $carts = Cart::with('product')
            ->where('user_id', auth()->id())
            ->get();

        if ($carts->isEmpty()) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Keranjang kosong.',
                ],
                400,
            );
        }

        // Berat dalam gram, minimal 1000g (1kg)
        $totalWeightGram = (int) max(1000, $carts->sum(fn($c) => ($c->product->weight ?? 200) * $c->quantity));

        $areaId = $request->destination_area_id;
        $destinationName = $request->destination_name ?? $areaId;

        Log::info('[Ongkir] Check (Biteship)', [
            'area_id' => $areaId,
            'destination' => $destinationName,
            'weight_g' => $totalWeightGram,
        ]);

        // ── area_id sudah dipilih langsung oleh customer dari hasil pencarian ──
        // Biteship, jadi tidak perlu lagi proses "tebak" area seperti sebelumnya.
        $results = [];

        try {
            $results = $this->biteship->getRates($areaId, $totalWeightGram);
            foreach ($results as &$item) {
                $item['source'] = 'biteship';
            }
            unset($item);
        } catch (\Exception $e) {
            Log::error('[Ongkir] Biteship exception: ' . $e->getMessage());
        }

        // ── Fallback: kalau Biteship gagal total, pakai tarif lokal supaya checkout tidak buntu ──
        if (empty($results)) {
            Log::warning('[Ongkir] Biteship kosong untuk area ' . $areaId . ', fallback ke ShippingService lokal');

            /** @var \App\Services\ShippingService $local */
            $local = app(ShippingService::class);
            $results = $local->calculate($destinationName, $totalWeightGram);
            foreach ($results as &$item) {
                $item['source'] = 'local';
            }
            unset($item);
        }

        usort($results, fn($a, $b) => $a['cost'] - $b['cost']);

        return response()->json([
            'success' => true,
            'sources_used' => array_values(array_unique(array_column($results, 'source'))),
            'origin' => 'Gunungkidul, DI Yogyakarta',
            'destination' => $destinationName,
            'total_weight' => $totalWeightGram,
            'total_found' => count($results),
            'data' => $results,
        ]);
    }
}
