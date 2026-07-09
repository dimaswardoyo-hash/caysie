@extends('layouts.app')
@section('title', 'Pembayaran Berhasil' . ($order ? ' — ' . $order->order_number : ''))

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-green-50 to-emerald-50 py-10 px-4">
        <div class="max-w-2xl mx-auto">

            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden p-8 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-green-50 mb-6">
                    <i class="fa-solid fa-circle-check text-green-500 text-4xl"></i>
                </div>

                <h1 class="text-2xl font-black text-gray-800 mb-2">Pembayaran Berhasil!</h1>

                @if ($order)
                    <p class="text-gray-500 text-sm mb-1">
                        Pesanan <strong>{{ $order->order_number }}</strong> telah kami terima.
                    </p>
                    <p class="text-gray-500 text-sm mb-8">
                        Total pembayaran: <strong class="text-green-600">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</strong>
                    </p>
                @else
                    <p class="text-gray-500 text-sm mb-8">
                        Pembayaranmu telah kami terima. Terima kasih!
                    </p>
                @endif

                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    @if ($order)
                        <a href="{{ route('user.orders.show', $order) }}"
                            class="inline-flex items-center justify-center gap-2 bg-primary text-white font-black px-8 py-3.5 rounded-2xl hover:bg-primary-dark transition shadow-lg shadow-purple-200 text-sm">
                            <i class="fa-solid fa-box"></i>
                            Lihat Detail Pesanan
                        </a>
                    @endif
                    <a href="{{ route('user.shop') }}"
                        class="inline-flex items-center justify-center gap-2 bg-gray-100 text-gray-700 font-bold px-8 py-3.5 rounded-2xl hover:bg-gray-200 transition text-sm">
                        <i class="fa-solid fa-bag-shopping"></i>
                        Lanjut Belanja
                    </a>
                </div>
            </div>

        </div>
    </div>
@endsection
