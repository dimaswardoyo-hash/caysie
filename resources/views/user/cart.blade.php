@extends('layouts.app')
@section('title', 'Keranjang')

@section('content')

<h1 class="text-2xl font-black text-gray-800 mb-6">
    🛒 Keranjang Saya
    @if($carts->count())
    <span class="text-base font-normal text-gray-400 ml-2">({{ $carts->sum('quantity') }} item)</span>
    @endif
</h1>

@if($carts->isEmpty())
<div class="bg-white rounded-3xl py-24 text-center border border-gray-100">
    <div class="text-7xl mb-5">🛒</div>
    <h3 class="text-xl font-black text-gray-700 mb-2">Keranjang masih kosong</h3>
    <p class="text-gray-400 text-sm mb-6">Yuk mulai belanja dan temukan koleksi favoritmu!</p>
    <a href="{{ route('user.shop') }}" class="inline-block bg-primary text-white font-bold px-8 py-3 rounded-2xl hover:bg-primary-dark transition shadow-lg shadow-purple-200">
        Mulai Belanja
    </a>
</div>
@else
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Daftar Item --}}
    <div class="lg:col-span-2 space-y-4">
        @foreach($carts as $cart)
        <div class="bg-white rounded-2xl p-5 border border-gray-100 flex gap-4 items-start">
            {{-- Foto --}}
            <div class="w-20 h-20 rounded-xl overflow-hidden bg-gray-100 flex-shrink-0">
                @if($cart->product->image)
                    <img src="{{ asset('storage/'.$cart->product->image) }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center text-3xl">👕</div>
                @endif
            </div>

            {{-- Info --}}
            <div class="flex-1 min-w-0">
                <a href="{{ route('user.product.show', $cart->product->slug) }}"
                   class="font-bold text-gray-800 hover:text-primary transition text-sm line-clamp-2">
                    {{ $cart->product->name }}
                </a>
                <div class="flex items-center gap-2 mt-1 mb-3">
                    <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded font-semibold">
                        Ukuran {{ $cart->productSize->size }}
                    </span>
                    <span class="text-xs text-gray-400">·</span>
                    <span class="text-xs font-bold {{ $cart->productSize->stock > 0 ? 'text-green-600' : 'text-red-500' }}">
                        Stok: {{ $cart->productSize->stock }} pcs
                    </span>
                </div>
                <div class="flex items-center justify-between flex-wrap gap-3">
                    {{-- Harga --}}
                    <div>
                        @if($cart->product->price_sale)
                        <p class="text-base font-black text-primary">{{ $cart->product->display_sale_price }}</p>
                        <p class="text-xs text-gray-300 line-through">{{ $cart->product->display_price }}</p>
                        @else
                        <p class="text-base font-black text-primary">{{ $cart->product->display_price }}</p>
                        @endif
                    </div>

                    {{-- Qty & Hapus --}}
                    <div class="flex items-center gap-3">
                        <form action="{{ route('user.cart.update', $cart) }}" method="POST" class="flex items-center gap-2">
                            @csrf @method('PATCH')
                            <button type="button" onclick="decQty(this)"
                                class="w-8 h-8 border border-gray-200 rounded-lg flex items-center justify-center hover:border-primary hover:text-primary transition font-bold text-base">−</button>
                            <input type="number" name="quantity" value="{{ $cart->quantity }}" min="1" max="{{ $cart->productSize->stock }}"
                                   onchange="this.form.submit()"
                                   class="w-12 text-center text-sm font-bold border border-gray-200 rounded-lg py-1.5 focus:outline-none focus:border-primary">
                            <button type="button" onclick="incQty(this)"
                                class="w-8 h-8 border border-gray-200 rounded-lg flex items-center justify-center hover:border-primary hover:text-primary transition font-bold text-base">+</button>
                        </form>

                        <form action="{{ route('user.cart.remove', $cart) }}" method="POST"
                              onsubmit="return confirm('Hapus produk ini dari keranjang?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="w-8 h-8 bg-red-50 text-red-400 rounded-lg flex items-center justify-center hover:bg-red-100 transition">
                                <i class="fa-solid fa-trash text-xs"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Subtotal --}}
            <div class="text-right flex-shrink-0">
                <p class="text-xs text-gray-400">Subtotal</p>
                <p class="font-black text-gray-800 text-sm">{{ $cart->formatted_subtotal }}</p>
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
                    <span class="text-gray-500">Total produk ({{ $carts->sum('quantity') }} item)</span>
                    <span class="font-bold">Rp{{ number_format($subtotal,0,',','.') }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Ongkos kirim</span>
                    <span class="text-gray-400 italic">Dihitung saat checkout</span>
                </div>
            </div>

            <div class="flex justify-between mb-6">
                <span class="font-black text-gray-800">Subtotal</span>
                <span class="font-black text-primary text-lg">Rp{{ number_format($subtotal,0,',','.') }}</span>
            </div>

            <a href="{{ route('user.checkout') }}"
               class="block w-full bg-primary text-white font-black py-4 rounded-2xl text-center hover:bg-primary-dark transition shadow-lg shadow-purple-200 text-sm">
                Lanjut ke Checkout <i class="fa-solid fa-arrow-right ml-1"></i>
            </a>
            <a href="{{ route('user.shop') }}" class="block text-center text-sm text-gray-400 hover:text-gray-600 mt-3 transition">
                ← Lanjut Belanja
            </a>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
function decQty(btn) {
    const input = btn.nextElementSibling;
    if (parseInt(input.value) > 1) { input.value--; input.dispatchEvent(new Event('change')); }
}
function incQty(btn) {
    const input = btn.previousElementSibling;
    if (parseInt(input.value) < parseInt(input.max)) { input.value++; input.dispatchEvent(new Event('change')); }
}
</script>
@endpush