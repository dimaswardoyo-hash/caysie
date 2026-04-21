<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'user')->withCount('orders')->latest();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        $users = $query->paginate(15)->withQueryString();
        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->load(['orders' => fn($q) => $q->latest()->take(5)]);
        return view('admin.users.show', compact('user'));
    }

    public function toggleBan(User $user)
    {
        // Tambahkan kolom 'is_banned' ke migration users jika ingin fitur ban
        // Untuk sekarang kita nonaktifkan saja dengan soft approach
        return back()->with('success', 'Fitur ban user akan segera tersedia.');
    }
}
