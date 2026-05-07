<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Services\BinderByteService;
use App\Services\ShippingService;

class OngkirController extends Controller
{
    // Origin: Gunungkidul → gunakan ID kota BinderByte
    // ID ini didapat dari endpoint /wilayah/kabupaten provinsi DIY
    // Ganti dengan ID yang benar dari hasil /api/wilayah/cities?province_id=<DIY_ID>
    private const ORIGIN_CITY_ID = '3403'; // Kode kabupaten Gunungkidul (BPS/BinderByte)

    public function __construct(private BinderByteService $bb) {}

    public function check(Request $request): JsonResponse
    {
        $request->validate([
            'destination_province' => 'required|string|min:2',
            'destination_city' => 'required|string|min:2',
            'destination_city_id' => 'required|string', // ID dari dropdown BinderByte
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

        Log::info('[Ongkir] Check', [
            'province' => $province,
            'city' => $city,
            'dest_id' => $destId,
            'weight_g' => $totalWeightGram,
        ]);

        // ── Panggil BinderByte ───────────────────────────────────
        // BinderByte /v1/ongkir menerima weight dalam gram
        $results = $this->bb->checkOngkir(origin: self::ORIGIN_CITY_ID, destination: $destId, weight: $totalWeightGram);

        // ── Fallback: jika BinderByte gagal, gunakan ShippingService lokal ──
        if (empty($results)) {
            Log::warning('[Ongkir] BinderByte kosong, fallback ke ShippingService lokal');

            /** @var \App\Services\ShippingService $local */
            $local = app(ShippingService::class);
            $results = $local->calculate($province, $totalWeightGram);
            $source = 'local';
        } else {
            $source = 'binderbyte';
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
