@extends('layouts.app')
@section('title', $product->name)

@section('content')

    <section class="py-6 md:py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Back --}}
            <div class="flex justify-between mb-6">
                <a href="{{ route('user.shop') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold
        bg-white border border-gray-200 text-gray-600
        hover:bg-primary hover:text-white hover:border-primary
        hover:shadow-md hover:scale-[1.03]
        transition-all duration-300 group">

                    <i
                        class="fa-solid fa-arrow-left text-xs transition-transform duration-300 group-hover:-translate-x-1"></i>
                    Kembali ke toko
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 mb-12">

                {{-- FOTO --}}
                <div>
                    <div
                        class="w-full h-[420px] bg-gradient-to-br from-purple-100 to-indigo-100 rounded-3xl overflow-hidden mb-4 shadow-sm">
                        @if ($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" id="main-img"
                                class="w-full h-full object-cover transition-all duration-300" alt="{{ $product->name }}">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-8xl">👕</div>
                        @endif
                    </div>

                    @if ($product->images && count($product->images))
                        <div class="flex gap-3">
                            @if ($product->image)
                                <button onclick="switchImg('{{ asset('storage/' . $product->image) }}')"
                                    class="w-16 h-16 rounded-xl overflow-hidden border-2 border-primary">
                                    <img src="{{ asset('storage/' . $product->image) }}" class="w-full h-full object-cover">
                                </button>
                            @endif

                            @foreach ($product->images as $img)
                                <button onclick="switchImg('{{ asset('storage/' . $img) }}')"
                                    class="w-16 h-16 rounded-xl overflow-hidden border-2 border-transparent hover:border-primary transition">
                                    <img src="{{ asset('storage/' . $img) }}" class="w-full h-full object-cover">
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- INFO --}}
                <div class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-gray-100">

                    {{-- Badge --}}
                    <div class="flex items-center gap-2 mb-3 flex-wrap">
                        <span class="bg-purple-100 text-purple-700 text-xs font-bold px-3 py-1 rounded-full">
                            {{ ucfirst($product->category) }}
                        </span>

                        @if ($product->is_featured)
                            <span class="bg-yellow-100 text-yellow-700 text-xs font-bold px-3 py-1 rounded-full">
                                ⭐ Unggulan
                            </span>
                        @endif
                    </div>

                    {{-- Title --}}
                    <h1 class="text-2xl md:text-3xl font-black text-gray-800 mb-4">
                        {{ $product->name }}
                    </h1>

                    {{-- Price --}}
                    <div class="mb-5">
                        @if ($product->price_sale)
                            <div class="flex items-center gap-3 flex-wrap">
                                <span class="text-3xl font-black text-primary">
                                    {{ $product->display_sale_price }}
                                </span>
                                <span class="text-lg text-gray-300 line-through">
                                    {{ $product->display_price }}
                                </span>
                                <span class="bg-red-100 text-red-600 text-xs font-black px-2 py-1 rounded-lg">
                                    -{{ $product->discount_percent }}%
                                </span>
                            </div>
                        @else
                            <span class="text-3xl font-black text-primary">
                                {{ $product->display_price }}
                            </span>
                        @endif
                    </div>

                    {{-- Deskripsi --}}
                    <p class="text-sm text-gray-500 leading-relaxed mb-6">
                        {{ $product->description ?? 'Tidak ada deskripsi.' }}
                    </p>

                    {{-- Info --}}
                    <div class="flex items-center gap-6 text-sm text-gray-500 mb-6 pb-6 border-b border-gray-100">
                        <span class="flex items-center gap-2">
                            <i class="fa-solid fa-weight-hanging text-xs"></i>
                            {{ $product->weight }}g
                        </span>
                        <span class="flex items-center gap-2">
                            <i class="fa-solid fa-boxes-stacked text-xs"></i>
                            Stok: {{ $product->total_stock }}
                        </span>
                    </div>

                    {{-- FORM --}}
                    <form action="{{ route('user.cart.add') }}" method="POST">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">

                        {{-- SIZE --}}
                        <div class="mb-5">
                            <p class="text-sm font-bold text-gray-700 mb-3">Pilih Ukuran</p>
                            <div class="flex gap-2 flex-wrap">
                                @foreach ($product->sizes as $size)
                                    <label class="cursor-pointer">
                                        <input type="radio" name="product_size_id" value="{{ $size->id }}"
                                            class="sr-only peer" {{ $size->stock == 0 ? 'disabled' : '' }} required>

                                        <span
                                            class="px-4 py-2.5 rounded-xl text-sm font-bold border-2 transition
                                    {{ $size->stock == 0
                                        ? 'border-gray-100 text-gray-300 bg-gray-50 line-through'
                                        : 'border-gray-200 text-gray-600 hover:border-primary hover:text-primary peer-checked:border-primary peer-checked:bg-purple-50 peer-checked:text-primary' }}">
                                            {{ $size->size }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- QTY --}}
                        <div class="mb-6">
                            <p class="text-sm font-bold text-gray-700 mb-3">Jumlah</p>
                            <div class="flex items-center gap-3">
                                <button type="button" onclick="changeQty(-1)"
                                    class="w-10 h-10 rounded-xl border border-gray-200 hover:border-primary hover:text-primary transition">−</button>

                                <input type="number" name="quantity" id="qty" value="1" min="1"
                                    max="10"
                                    class="w-16 text-center font-bold border border-gray-200 rounded-xl py-2">

                                <button type="button" onclick="changeQty(1)"
                                    class="w-10 h-10 rounded-xl border border-gray-200 hover:border-primary hover:text-primary transition">+</button>
                            </div>
                        </div>

                        {{-- BUTTON --}}
                        <div class="flex gap-3">
                            <button type="submit"
                                class="flex-1 bg-primary text-white font-black py-3.5 rounded-2xl 
                                   hover:bg-primary-dark transition shadow-lg shadow-purple-200 flex items-center justify-center gap-2">
                                <i class="fa-solid fa-cart-plus"></i> Tambah ke Keranjang
                            </button>

                            <a href="{{ route('user.cart') }}"
                                class="px-5 py-3.5 bg-gray-100 text-gray-600 rounded-2xl hover:bg-gray-200 transition flex items-center">
                                <i class="fa-solid fa-cart-shopping"></i>
                            </a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </section>

    {{-- RELATED --}}
    @if ($related->count())
        <section class="py-6 md:py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl md:text-2xl font-black text-gray-800">
                        Produk Terkait
                    </h2>
                </div>

                <div class="max-w-5xl mx-auto grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-6">
                    @foreach ($related as $product)
                        @include('user.partials.product-card', ['product' => $product])
                    @endforeach
                </div>

            </div>
        </section>
    @endif

@endsection
