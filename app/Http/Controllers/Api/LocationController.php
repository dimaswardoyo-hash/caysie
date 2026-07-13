<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\BiteshipService;

class LocationController extends Controller
{
    public function __construct(private BiteshipService $biteship) {}

    // ── Dipakai oleh search box "Cari Lokasi Tujuan" di checkout ──
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'keyword' => 'required|string|min:3',
        ]);

        $areas = $this->biteship->searchLocation($request->keyword);

        return response()->json([
            'success' => !empty($areas),
            'areas' => $areas,
        ]);
    }
}
