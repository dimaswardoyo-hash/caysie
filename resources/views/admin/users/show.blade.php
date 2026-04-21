@extends('layouts.admin')
@section('title', 'Detail User')

@section('content')

    <div class="mb-6">
        <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-400 hover:text-primary transition">
            ← Kembali ke daftar user
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Profil User --}}
        <div class="space-y-5">
            <div class="bg-white rounded-2xl p-6 border border-gray-100 text-center">
                <div
                    class="w-20 h-20 bg-primary rounded-full flex items-center justify-center text-white font-black text-3xl mx-auto mb-4">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <h2 class="text-xl font-black text-gray-800">{{ $user->name }}</h2>
                <p class="text-sm text-gray-400 mt-1">{{ $user->email }}</p>
                @if ($user->phone)
                    <p class="text-sm text-gray-400">{{ $user->phone }}</p>
                @endif
                <span class="inline-block mt-3 bg-blue-100 text-blue-700 text-xs font-bold px-3 py-1 rounded-full">
                    User
                </span>
            </div>

            <div class="bg-white rounded-2xl p-6 border border-gray-100">
                <h3 class="font-black text-gray-800 mb-4">Statistik</h3>
                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">Total Pesanan</span>
                        <span class="font-black text-gray-800">{{ $user->orders->count() }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">Total Belanja</span>
                        <span class="font-black text-primary">
                            Rp{{ number_format($user->orders->whereIn('status', ['paid', 'processing', 'shipped', 'delivered'])->sum('total'), 0, ',', '.') }}
                        </span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">Bergabung</span>
                        <span class="font-bold text-gray-700">{{ $user->created_at->isoFormat('D MMM Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Riwayat Pesanan --}}
        <div class="lg:col-span-2 bg-white rounded-2xl p-6 border border-gray-100">
            <h3 class="font-black text-gray-800 mb-5">Riwayat Pesanan</h3>
            @if ($user->orders->isEmpty())
                <div class="text-center text-gray-300 py-12">
                    <i class="fa-solid fa-receipt text-4xl mb-3"></i>
                    <p class="text-sm">Belum ada pesanan</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach ($user->orders as $order)
                        @php
                            $sc =
                                [
                                    'pending' => 'yellow',
                                    'paid' => 'blue',
                                    'processing' => 'purple',
                                    'shipped' => 'indigo',
                                    'delivered' => 'green',
                                    'cancelled' => 'red',
                                ][$order->status] ?? 'gray';
                            $sl =
                                [
                                    'pending' => 'Menunggu',
                                    'paid' => 'Dibayar',
                                    'processing' => 'Diproses',
                                    'shipped' => 'Dikirim',
                                    'delivered' => 'Selesai',
                                    'cancelled' => 'Dibatalkan',
                                ][$order->status] ?? $order->status;
                        @endphp
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                            <div>
                                <p class="font-bold text-gray-800 text-sm">{{ $order->order_code }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ $order->created_at->isoFormat('D MMM Y') }}</p>
                            </div>
                            <div class="text-center">
                                <span
                                    class="px-2.5 py-1 rounded-full text-xs font-bold bg-{{ $sc }}-100 text-{{ $sc }}-700">{{ $sl }}</span>
                            </div>
                            <div class="text-right">
                                <p class="font-black text-gray-800 text-sm">{{ $order->formatted_total }}</p>
                                <a href="{{ route('admin.orders.show', $order) }}"
                                    class="text-xs text-primary hover:underline font-semibold">Detail →</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

@endsection
