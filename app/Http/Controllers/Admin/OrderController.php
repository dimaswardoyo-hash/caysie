<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    private array $statuses = ['pending', 'waiting_confirmation', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'];

    public function index(Request $request)
    {
        $query = Order::with(['user', 'items'])->latest();

        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('order_number', 'like', "%{$request->search}%")->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$request->search}%"));
            });
        }
        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->paginate(15)->withQueryString();
        $statuses = $this->statuses;

        // Hitung badge per status
        $statusCounts = Order::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');

        return view('admin.orders.index', compact('orders', 'statuses', 'statusCounts'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'items']);
        $statuses = $this->statuses;
        return view('admin.orders.show', compact('order', 'statuses'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:' . implode(',', $this->statuses),
        ]);

        $old = $order->status;
        $order->update(['status' => $request->status]);

        // Jika dikonfirmasi bayar
        if ($request->status === 'confirmed' && !$order->paid_at) {
            $order->update(['paid_at' => now()]);
        }

        return back()->with('success', "Status pesanan #{$order->order_number} berhasil diubah dari " . ucfirst($old) . ' → ' . ucfirst($request->status) . '.');
    }

    public function confirmPayment(Order $order)
    {
        if ($order->status !== 'confirmed' && $order->status !== 'pending') {
            return back()->with('error', 'Pesanan tidak dalam status yang bisa dikonfirmasi.');
        }

        $order->update([
            'status' => 'processing',
            'paid_at' => $order->paid_at ?? now(),
        ]);

        return back()->with('success', "Pembayaran pesanan #{$order->order_number} berhasil dikonfirmasi. Status → Diproses.");
    }
}
