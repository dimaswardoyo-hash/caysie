@extends('layouts.admin')
@section('title', 'Manajemen Pesanan')

@section('content')

    @if (session('success'))
        <div class="mb-6 flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 px-5 py-4 rounded-2xl">
            <i class="fa-solid fa-circle-check text-green-500"></i>
            <span class="font-semibold text-sm">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Status Tabs --}}
    @php
        $tabColors = [
            'pending' => 'yellow',
            'confirmed' => 'blue',
            'processing' => 'purple',
            'shipped' => 'indigo',
            'delivered' => 'green',
            'cancelled' => 'red',
        ];
        $tabLabels = [
            'pending' => 'Menunggu Bayar',
            'confirmed' => 'Sudah Dibayar',
            'processing' => 'Diproses',
            'shipped' => 'Dikirim',
            'delivered' => 'Selesai',
            'cancelled' => 'Dibatalkan',
        ];
    @endphp

    <div class="flex gap-2 flex-wrap mb-6">
        <a href="{{ route('admin.orders.index') }}"
            class="px-4 py-2 rounded-xl text-sm font-bold transition
              {{ !request('status') ? 'bg-gray-800 text-white' : 'bg-white text-gray-500 border border-gray-200 hover:border-gray-400' }}">
            Semua
            <span class="ml-1 text-xs opacity-70">({{ $orders->total() }})</span>
        </a>
        @foreach ($tabLabels as $key => $label)
            <a href="{{ route('admin.orders.index', ['status' => $key]) }}"
                class="px-4 py-2 rounded-xl text-sm font-bold transition
              {{ request('status') === $key
                  ? 'bg-' . $tabColors[$key] . '-500 text-white'
                  : 'bg-white text-gray-500 border border-gray-200 hover:border-gray-400' }}">
                {{ $label }}
                @if ($statusCounts->get($key))
                    <span class="ml-1 text-xs opacity-80">({{ $statusCounts->get($key) }})</span>
                @endif
            </a>
        @endforeach
    </div>

    {{-- Search & Filter --}}
    <form method="GET" class="flex flex-wrap gap-3 mb-6">
        @if (request('status'))
            <input type="hidden" name="status" value="{{ request('status') }}">
        @endif
        <div class="relative flex-1 min-w-52">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Kode pesanan / nama user..."
                class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-primary transition">
        </div>
        <input type="date" name="date_from" value="{{ request('date_from') }}"
            class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-primary transition">
        <input type="date" name="date_to" value="{{ request('date_to') }}"
            class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-primary transition">
        <button type="submit"
            class="px-5 py-2.5 bg-primary text-white text-sm font-bold rounded-xl hover:bg-primary-dark transition">
            <i class="fa-solid fa-filter mr-1"></i> Filter
        </button>
        @if (request()->hasAny(['search', 'date_from', 'date_to']))
            <a href="{{ route('admin.orders.index', request('status') ? ['status' => request('status')] : []) }}"
                class="px-5 py-2.5 bg-gray-100 text-gray-600 text-sm font-bold rounded-xl hover:bg-gray-200 transition">Reset</a>
        @endif
    </form>

    {{-- Tabel --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        @if ($orders->isEmpty())
            <div class="py-20 text-center text-gray-400">
                <i class="fa-solid fa-receipt text-5xl mb-4 opacity-30"></i>
                <p class="font-semibold">Belum ada pesanan</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100 text-xs text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-4 text-left">Pesanan</th>
                            <th class="px-4 py-4 text-left">Pelanggan</th>
                            <th class="px-4 py-4 text-left">Total</th>
                            <th class="px-4 py-4 text-left">Kurir</th>
                            <th class="px-4 py-4 text-center">Status</th>
                            <th class="px-4 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($orders as $order)
                            @php $c = $tabColors[$order->status] ?? 'gray'; @endphp
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="px-6 py-4">
                                    <p class="font-bold text-gray-800 text-xs">{{ $order->order_code }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $order->created_at->isoFormat('D MMM Y') }}
                                    </p>
                                    <p class="text-xs text-gray-400">{{ $order->items->count() }} produk</p>
                                </td>
                                <td class="px-4 py-4">
                                    <p class="font-semibold text-gray-700 text-xs">{{ $order->user->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $order->receiver_city }}</p>
                                </td>
                                <td class="px-4 py-4">
                                    <p class="font-black text-gray-800 text-sm">{{ $order->formatted_total }}</p>
                                    @if ($order->payment_proof)
                                        <span class="text-xs text-green-600 font-semibold flex items-center gap-1 mt-0.5">
                                            <i class="fa-solid fa-paperclip text-xs"></i> Ada bukti
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-xs text-gray-600">
                                    {{ $order->courier_name }}<br>
                                    <span class="text-gray-400">{{ $order->courier_service }}</span>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <span
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold
                            bg-{{ $c }}-100 text-{{ $c }}-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-{{ $c }}-500"></span>
                                        {{ $tabLabels[$order->status] ?? $order->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('admin.orders.show', $order) }}"
                                            class="w-8 h-8 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center hover:bg-blue-100 transition"
                                            title="Detail">
                                            <i class="fa-solid fa-eye text-xs"></i>
                                        </a>
                                        @if ($order->status === 'pending' && $order->payment_proof)
                                            <form action="{{ route('admin.orders.confirm', $order) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <button type="submit"
                                                    class="w-8 h-8 bg-green-50 text-green-600 rounded-lg flex items-center justify-center hover:bg-green-100 transition"
                                                    title="Konfirmasi Bayar">
                                                    <i class="fa-solid fa-check text-xs"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if ($orders->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">{{ $orders->links() }}</div>
            @endif
        @endif
    </div>

@endsection
