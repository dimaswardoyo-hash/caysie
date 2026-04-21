@extends('layouts.app')
@section('title', $product->name)

@section('content')

<div class="mb-6">
    <a href="{{ route('user.shop') }}" class="text-sm text-gray-400 hover:text-primary transition">
        ← Kembali ke toko
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">

    {{-- FOTO --}}
    <div>
        <div class="w-full h-96 bg-gradient-to-br from-purple-100 to-indigo-100 rounded-3xl overflow-hidden mb-4">
            @if($product->image)
                <img src="{{ asset('storage/'.$product->image) }}" id="main-img"
                     class="w-full h-full object-cover" alt="{{ $product->name }}">
            @else
                <div class="w-full h-full flex items-center justify-center text-8xl">👕</div>
            @endif
        </div>
        @if($product->images && count($product->images))
        <div class="flex gap-3">
            @if($product->image)
            <button onclick="switchImg('{{ asset('storage/'.$product->image) }}')"
                class="w-16 h-16 rounded-xl overflow-hidden border-2 border-primary">
                <img src="{{ asset('storage/'.$product->image) }}" class="w-full h-full object-cover">
            </button>
            @endif
            @foreach($product->images as $img)
            <button onclick="switchImg('{{ asset('storage/'.$img) }}')"
                class="w-16 h-16 rounded-xl overflow-hidden border-2 border-transparent hover:border-primary transition">
                <img src="{{ asset('storage/'.$img) }}" class="w-full h-full object-cover">
            </button>
            @endforeach
        </div>
        @endif
    </div>

    {{-- INFO + ADD TO CART --}}
    <div>
        <div class="flex items-center gap-2 mb-2">
            <span class="bg-purple-100 text-purple-700 text-xs font-bold px-3 py-1 rounded-full">{{ ucfirst($product->category) }}</span>
            @if($product->is_featured)
            <span class="bg-yellow-100 text-yellow-700 text-xs font-bold px-3 py-1 rounded-full">⭐ Unggulan</span>
            @endif
        </div>
        <h1 class="text-2xl font-black text-gray-800 mb-3">{{ $product->name }}</h1>

        <div class="mb-4">
            @if($product->price_sale)
            <div class="flex items-baseline gap-3">
                <span class="text-3xl font-black text-primary">{{ $product->display_sale_price }}</span>
                <span class="text-lg text-gray-300 line-through">{{ $product->display_price }}</span>
                <span class="bg-red-100 text-red-600 text-xs font-black px-2 py-1 rounded-lg">Hemat {{ $product->discount_percent }}%</span>
            </div>
            @else
            <span class="text-3xl font-black text-primary">{{ $product->display_price }}</span>
            @endif
        </div>

        <p class="text-sm text-gray-500 leading-relaxed mb-6">{{ $product->description ?? 'Tidak ada deskripsi.' }}</p>

        <div class="flex items-center gap-4 text-sm text-gray-500 mb-6 pb-6 border-b border-gray-100">
            <span class="flex items-center gap-1.5"><i class="fa-solid fa-weight-hanging text-xs"></i> {{ $product->weight }}g</span>
            <span class="flex items-center gap-1.5"><i class="fa-solid fa-boxes-stacked text-xs"></i> Stok: {{ $product->total_stock }} pcs</span>
        </div>

        {{-- Form Tambah ke Keranjang --}}
        <form action="{{ route('user.cart.add') }}" method="POST">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">

            {{-- Pilih Ukuran --}}
            <div class="mb-5">
                <p class="text-sm font-bold text-gray-700 mb-3">Pilih Ukuran</p>
                <div class="flex gap-2 flex-wrap" id="size-buttons">
                    @foreach($product->sizes as $size)
                    <label class="cursor-pointer">
                        <input type="radio" name="product_size_id" value="{{ $size->id }}"
                               class="sr-only peer" {{ $size->stock == 0 ? 'disabled' : '' }}
                               required>
                        <span class="flex flex-col items-center px-4 py-2.5 border-2 rounded-xl text-sm font-bold transition
                            {{ $size->stock == 0
                                ? 'border-gray-100 text-gray-300 bg-gray-50 cursor-not-allowed line-through'
                                : 'border-gray-200 text-gray-600 hover:border-primary hover:text-primary peer-checked:border-primary peer-checked:bg-purple-50 peer-checked:text-primary' }}">
                            {{ $size->size }}
                            <span class="text-xs font-normal mt-0.5 {{ $size->stock == 0 ? 'text-gray-300' : 'text-gray-400' }}">
                                {{ $size->stock > 0 ? $size->stock.' pcs' : 'Habis' }}
                            </span>
                        </span>
                    </label>
                    @endforeach
                </div>
                @error('product_size_id')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Jumlah --}}
            <div class="mb-6">
                <p class="text-sm font-bold text-gray-700 mb-3">Jumlah</p>
                <div class="flex items-center gap-3">
                    <button type="button" onclick="changeQty(-1)"
                        class="w-10 h-10 rounded-xl border border-gray-200 flex items-center justify-center hover:border-primary hover:text-primary transition font-bold text-lg">−</button>
                    <input type="number" name="quantity" id="qty" value="1" min="1" max="10"
                           class="w-16 text-center text-lg font-bold border border-gray-200 rounded-xl py-2 focus:outline-none focus:border-primary">
                    <button type="button" onclick="changeQty(1)"
                        class="w-10 h-10 rounded-xl border border-gray-200 flex items-center justify-center hover:border-primary hover:text-primary transition font-bold text-lg">+</button>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit"
                    class="flex-1 bg-primary text-white font-black py-3.5 rounded-2xl hover:bg-primary-dark transition shadow-lg shadow-purple-200 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-cart-plus"></i> Tambah ke Keranjang
                </button>
                <a href="{{ route('user.cart') }}"
                   class="px-5 py-3.5 bg-gray-100 text-gray-600 rounded-2xl hover:bg-gray-200 transition font-bold flex items-center gap-2">
                    <i class="fa-solid fa-cart-shopping"></i>
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Produk Terkait --}}
@if($related->count())
<div>
    <h2 class="text-xl font-black text-gray-800 mb-5">Produk Terkait</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
        @foreach($related as $product)
            @include('user.partials.product-card', ['product' => $product])
        @endforeach
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
function changeQty(delta) {
    const input = document.getElementById('qty');
    let val = parseInt(input.value) + delta;
    input.value = Math.min(Math.max(val, 1), 10);
}
function switchImg(src) {
    const img = document.getElementById('main-img');
    if (img) { img.style.opacity=0; setTimeout(()=>{img.src=src;img.style.opacity=1;},150); img.style.transition='opacity .15s'; }
}
</script>
@endpush