<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{

    public function index()
    {
        // Nanti diisi produk dari DB, sekarang kosong dulu
        $featuredProducts = collect(); // akan diganti: Product::where('is_featured', true)->take(6)->get();
        $newProducts = collect();

        return view('user.dashboard', compact('featuredProducts', 'newProducts'));
    }
}
