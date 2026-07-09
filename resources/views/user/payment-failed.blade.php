@extends('layouts.app')
@section('title', 'Pembayaran Gagal' . ($order ? ' — ' . $order->order_number : ''))

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-red-50 to-orange-50 py-10 px-4">
        <div class="max-w-2xl mx-auto">

            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden p-8 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-red-50 mb-6">
                    <i class="fa-solid fa-circle-xmark text-red-500 text-4xl"></i>
                </div>

                <h1 class="text-2xl font-black text-gray-800 mb-2">Pembayaran Gagal</h1>

                @if ($order)
                    <p class="text-gray-500 text-sm mb-8">
                        Pembayaran untuk pesanan <strong>{{ $order->order_number }}</strong> belum berhasil atau
                        dibatalkan. Kamu bisa mencoba lagi dari halaman detail pesanan.
                    </p>
                @else
                    <p class="text-gray-500 text-sm mb-8">
                        Pembayaranmu belum berhasil atau dibatalkan. Silakan coba lagi.
                    </p>
                @endif

                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    @if ($order)
                        <a href="{{ route('user.payment.show', $order) }}"
                            class="inline-flex items-center justify-center gap-2 bg-primary text-white font-black px-8 py-3.5 rounded-2xl hover:bg-primary-dark transition shadow-lg shadow-purple-200 text-sm">
                            <i class="fa-solid fa-rotate-right"></i>
                            Coba Bayar Lagi
                        </a>
                    @endif
                    <a href="{{ route('user.orders') }}"
                        class="inline-flex items-center justify-center gap-2 bg-gray-100 text-gray-700 font-bold px-8 py-3.5 rounded-2xl hover:bg-gray-200 transition text-sm">
                        <i class="fa-solid fa-list"></i>
                        Lihat Pesanan Saya
                    </a>
                </div>
            </div>

        </div>
    </div>
@endsection
