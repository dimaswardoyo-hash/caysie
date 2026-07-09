<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Services\RajaOngkirService;
use App\Services\ShippingService;

class OngkirController extends Controller
{
    public function __construct(private RajaOngkirService $rajaOngkir) {}

    public function check(Request $request): JsonResponse
    {
        $request->validate([
            'destination_province' => 'required|string|min:2',
            'destination_city' => 'required|string|min:2',
            'destination_city_id' => 'required|string', // ID kota dari dropdown RajaOngkir
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

        $province = $request->destination_province;
        $city = $request->destination_city;
        $destId = $request->destination_city_id;
        $originId = config('services.rajaongkir.origin_id');

        Log::info('[Ongkir] Check', [
            'province' => $province,
            'city' => $city,
            'dest_id' => $destId,
            'weight_g' => $totalWeightGram,
        ]);

        // ── Panggil RajaOngkir (kalau origin sudah dikonfigurasi) ─
        $results = [];
        if (!empty($originId)) {
            $results = $this->rajaOngkir->checkOngkir(origin: (string) $originId, destination: $destId, weight: $totalWeightGram);
        } else {
            Log::warning('[Ongkir] RAJAONGKIR_ORIGIN_ID belum diisi di .env — pakai fallback lokal.');
        }

        // ── Fallback: jika RajaOngkir gagal/belum dikonfigurasi, pakai tarif lokal ──
        if (empty($results)) {
            Log::warning('[Ongkir] RajaOngkir kosong, fallback ke ShippingService lokal');

            /** @var \App\Services\ShippingService $local */
            $local = app(ShippingService::class);
            $results = $local->calculate($province, $totalWeightGram);
            $source = 'local';
        } else {
            $source = 'rajaongkir';
        }

        return response()->json([
            'success' => true,
            'source' => $source,
            'origin' => 'Gunungkidul, DI Yogyakarta',
            'destination' => $city . ', ' . $province,
            'total_weight' => $totalWeightGram,
            'total_found' => count($results),
            'data' => $results,
        ]);
    }
}
