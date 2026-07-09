@extends('layouts.app')
@section('title', 'Keranjang')

@section('content')

    <div class="max-w-7xl mx-auto px-6 pt-8 pb-16">

        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-black text-gray-800 flex items-center gap-2">
                🛒 Keranjang Saya
                @if ($carts->count())
                    <span
                        class="text-sm font-semibold text-gray-400 bg-gray-100 px-2.5 py-0.5 rounded-full">{{ $carts->sum('quantity') }}
                        barang</span>
                @endif
            </h1>
        </div>

        @if ($carts->isEmpty())
            <div class="bg-white rounded-3xl py-24 text-center border border-gray-100">
                <div class="text-7xl mb-5">🛒</div>
                <h3 class="text-xl font-black text-gray-700 mb-2">Keranjangmu masih kosong</h3>
                <p class="text-gray-400 text-sm mb-6">Yuk, jelajahi koleksi kami dan temukan barang favoritmu!</p>
                <a href="{{ route('user.shop') }}"
                    class="inline-block bg-primary text-white font-bold px-8 py-3 rounded-2xl hover:bg-primary-dark transition shadow-lg shadow-purple-200">
                    Mulai Belanja
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

                {{-- Daftar Item --}}
                <div class="lg:col-span-2 space-y-3">
                    @foreach ($carts as $cart)
                        <div
                            class="bg-white rounded-2xl p-4 sm:p-5 border border-gray-100 hover:shadow-md hover:border-gray-200 transition flex flex-col sm:flex-row gap-4">

                            <div class="flex gap-4 flex-1 min-w-0">
                                {{-- Foto --}}
                                <a href="{{ route('user.product.show', $cart->product->slug) }}"
                                    class="w-20 h-20 rounded-xl overflow-hidden bg-gray-50 flex-shrink-0">
                                    @if ($cart->product->image)
                                        <img src="{{ asset('storage/' . $cart->product->image) }}"
                                            class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-3xl">👕</div>
                                    @endif
                                </a>

                                {{-- Info --}}
                                <div class="flex-1 min-w-0">
                                    <a href="{{ route('user.product.show', $cart->product->slug) }}"
                                        class="font-bold text-gray-800 hover:text-primary transition text-sm line-clamp-2">
                                        {{ $cart->product->name }}
                                    </a>

                                    <div class="flex items-center flex-wrap gap-2 mt-2 mb-3">
                                        <span
                                            class="text-xs bg-gray-50 text-gray-500 px-2.5 py-1 rounded-full font-semibold">
                                            Ukuran {{ $cart->productSize->size }}
                                        </span>
                                        @if ($cart->productSize->stock > 0)
                                            <span class="text-xs flex items-center gap-1 text-green-600 font-semibold">
                                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Stok tersedia
                                            </span>
                                        @else
                                            <span class="text-xs flex items-center gap-1 text-red-500 font-semibold">
                                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Stok habis
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Harga (mobile & desktop) --}}
                                    <div>
                                        @if ($cart->product->price_sale)
                                            <span
                                                class="text-base font-black text-primary">{{ $cart->product->display_sale_price }}</span>
                                            <span
                                                class="text-xs text-gray-300 line-through ml-1">{{ $cart->product->display_price }}</span>
                                        @else
                                            <span
                                                class="text-base font-black text-primary">{{ $cart->product->display_price }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Qty, Hapus & Subtotal --}}
                            <div
                                class="flex sm:flex-col items-center sm:items-end justify-between sm:justify-between gap-3 sm:pl-2 sm:border-l sm:border-gray-50">
                                <div class="flex items-center gap-2">
                                    <form action="{{ route('user.cart.update', $cart) }}" method="POST"
                                        class="flex items-center gap-1 bg-gray-50 rounded-full p-1">
                                        @csrf @method('PATCH')
                                        <button type="button" onclick="decQty(this)"
                                            class="w-7 h-7 rounded-full bg-white border border-gray-200 flex items-center justify-center hover:border-primary hover:text-primary transition font-bold text-sm leading-none">−</button>
                                        <input type="number" name="quantity" value="{{ $cart->quantity }}" min="1"
                                            max="{{ $cart->productSize->stock }}" onchange="this.form.submit()"
                                            class="w-9 text-center text-sm font-bold bg-transparent focus:outline-none">
                                        <button type="button" onclick="incQty(this)"
                                            class="w-7 h-7 rounded-full bg-white border border-gray-200 flex items-center justify-center hover:border-primary hover:text-primary transition font-bold text-sm leading-none">+</button>
                                    </form>

                                    <form action="{{ route('user.cart.remove', $cart) }}" method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus produk ini dari keranjang?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" title="Hapus dari keranjang"
                                            class="w-9 h-9 text-gray-300 rounded-full flex items-center justify-center hover:bg-red-50 hover:text-red-500 transition">
                                            <i class="fa-solid fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>

                                <div class="text-right">
                                    <p class="text-[11px] text-gray-400">Subtotal</p>
                                    <p class="font-black text-gray-800 text-sm">{{ $cart->formatted_subtotal }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Ringkasan --}}
                <div class="space-y-4">
                    <div class="bg-white rounded-2xl p-6 border border-gray-100 sticky top-24">
                        <h3 class="font-black text-gray-800 mb-5">Ringkasan Belanja</h3>

                        <div class="space-y-3 mb-5 pb-5 border-b border-gray-100">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Total harga ({{ $carts->sum('quantity') }} barang)</span>
                                <span class="font-bold text-gray-700">Rp{{ number_format($subtotal, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Ongkos kirim</span>
                                <span class="text-gray-400 italic">Dihitung saat checkout</span>
                            </div>
                        </div>

                        <div class="flex justify-between items-center mb-6">
                            <span class="font-black text-gray-800">Total</span>
                            <span
                                class="font-black text-primary text-lg">Rp{{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>

                        <a href="{{ route('user.checkout') }}"
                            class="block w-full bg-primary text-white font-black py-4 rounded-2xl text-center hover:bg-primary-dark transition shadow-lg shadow-purple-200 text-sm">
                            Checkout Sekarang <i class="fa-solid fa-arrow-right ml-1"></i>
                        </a>
                        <a href="{{ route('user.shop') }}"
                            class="block text-center text-sm text-gray-400 hover:text-gray-600 mt-3 transition">
                            ← Lanjut Belanja
                        </a>

                        <p class="flex items-center justify-center gap-1.5 text-[11px] text-gray-300 mt-5">
                            <i class="fa-solid fa-lock"></i> Pembayaran aman & terpercaya
                        </p>
                    </div>
                </div>
            </div>
        @endif

    </div>

@endsection

@push('scripts')
    <script>
        function decQty(btn) {
            const input = btn.nextElementSibling;
            if (parseInt(input.value) > 1) {
                input.value--;
                input.dispatchEvent(new Event('change'));
            }
        }

        function incQty(btn) {
            const input = btn.previousElementSibling;
            if (parseInt(input.value) < parseInt(input.max)) {
                input.value++;
                input.dispatchEvent(new Event('change'));
            }
        }
    </script>
@endpush
