<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::where('role', 'user')->count(),
            'total_products' => Product::count(),
            'total_orders' => 0,
            'total_revenue' => 0,
        ];
        return view('admin.dashboard', compact('stats'));
    }
}
