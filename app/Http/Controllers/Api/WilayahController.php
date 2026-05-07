<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\BinderByteService;
use Illuminate\Http\JsonResponse;
use App\Services\BiteshipService;

class WilayahController extends Controller
{
    public function __construct(private BinderByteService $bb) {}

    public function provinces(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->bb->getProvinces(),
        ]);
    }

    public function cities(Request $request): JsonResponse
    {
        $request->validate(['province_id' => 'required|string']);
        return response()->json([
            'success' => true,
            'data' => $this->bb->getCities($request->province_id),
        ]);
    }

    public function districts(Request $request): JsonResponse
    {
        $request->validate(['city_id' => 'required|string']);
        return response()->json([
            'success' => true,
            'data' => $this->bb->getDistricts($request->city_id),
        ]);
    }

    public function villages(Request $request): JsonResponse
    {
        $request->validate(['district_id' => 'required|string']);
        return response()->json([
            'success' => true,
            'data' => $this->bb->getVillages($request->district_id),
        ]);
    }
}
