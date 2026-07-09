<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\RajaOngkirService;
use Illuminate\Http\JsonResponse;

class WilayahController extends Controller
{
    public function __construct(private RajaOngkirService $rajaOngkir) {}

    public function provinces(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->rajaOngkir->getProvinces(),
        ]);
    }

    public function cities(Request $request): JsonResponse
    {
        $request->validate(['province_id' => 'required|string']);
        return response()->json([
            'success' => true,
            'data' => $this->rajaOngkir->getCities($request->province_id),
        ]);
    }

    public function districts(Request $request): JsonResponse
    {
        $request->validate(['city_id' => 'required|string']);
        return response()->json([
            'success' => true,
            'data' => $this->rajaOngkir->getDistricts($request->city_id),
        ]);
    }

    public function villages(Request $request): JsonResponse
    {
        $request->validate(['district_id' => 'required|string']);
        return response()->json([
            'success' => true,
            'data' => $this->rajaOngkir->getVillages($request->district_id),
        ]);
    }
}
