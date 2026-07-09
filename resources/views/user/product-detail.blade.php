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
                        class="w-full h-[420px] bg-gradient-to-br from-purple-100 to-indigo-100 rounded-3xl overflow-hidden mb-4 shadow-sm relative">
                        @if ($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" id="main-img"
                                class="w-full h-full object-cover transition-opacity duration-200"
                                alt="{{ $product->name }}">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-8xl">👕</div>
                        @endif

                        @if ($product->price_sale)
                            <span
                                class="absolute top-4 left-4 bg-red-500 text-white text-xs font-black px-3 py-1 rounded-lg shadow-sm">
                                -{{ $product->discount_percent }}%
                            </span>
                        @endif
                    </div>

                    @if ($product->image || ($product->images && count($product->images)))
                        <div class="flex gap-3 flex-wrap">
                            @if ($product->image)
                                <button type="button"
                                    onclick="switchImg(this, '{{ asset('storage/' . $product->image) }}')"
                                    class="thumb-btn w-16 h-16 rounded-xl overflow-hidden border-2 border-primary transition">
                                    <img src="{{ asset('storage/' . $product->image) }}" class="w-full h-full object-cover">
                                </button>
                            @endif

                            @foreach ($product->images as $img)
                                <button type="button" onclick="switchImg(this, '{{ asset('storage/' . $img) }}')"
                                    class="thumb-btn w-16 h-16 rounded-xl overflow-hidden border-2 border-transparent hover:border-primary/50 transition">
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
                            <span
                                class="inline-flex items-center gap-1 bg-yellow-100 text-yellow-700 text-xs font-bold px-3 py-1 rounded-full">
                                <i class="fa-solid fa-star text-[10px]"></i> Unggulan
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
                    <div class="flex items-center gap-3 mb-6 pb-6 border-b border-gray-100">
                        <div class="flex items-center gap-2.5 bg-gray-50 rounded-xl px-3.5 py-2.5">
                            <span class="w-7 h-7 bg-white rounded-lg flex items-center justify-center shadow-sm">
                                <i class="fa-solid fa-weight-hanging text-[11px] text-gray-500"></i>
                            </span>
                            <div class="leading-tight">
                                <p class="text-[10px] text-gray-400 font-semibold">Berat</p>
                                <p class="text-sm font-bold text-gray-700">{{ $product->weight }}g</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2.5 bg-gray-50 rounded-xl px-3.5 py-2.5">
                            <span class="w-7 h-7 bg-white rounded-lg flex items-center justify-center shadow-sm">
                                <i class="fa-solid fa-boxes-stacked text-[11px] text-gray-500"></i>
                            </span>
                            <div class="leading-tight">
                                <p class="text-[10px] text-gray-400 font-semibold">Sisa Stok</p>
                                <p class="text-sm font-bold text-gray-700">{{ $product->total_stock }} pcs</p>
                            </div>
                        </div>
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
                                    class="w-10 h-10 rounded-xl border border-gray-200 text-gray-500 font-bold hover:border-primary hover:text-primary transition">−</button>

                                <input type="number" name="quantity" id="qty" value="1" min="1"
                                    max="10"
                                    class="w-16 text-center font-bold border border-gray-200 rounded-xl py-2 focus:outline-none focus:border-primary">

                                <button type="button" onclick="changeQty(1)"
                                    class="w-10 h-10 rounded-xl border border-gray-200 text-gray-500 font-bold hover:border-primary hover:text-primary transition">+</button>
                            </div>
                        </div>

                        {{-- BUTTON --}}
                        <div class="flex gap-3">
                            <button type="submit"
                                class="flex-1 bg-gradient-to-r from-primary to-primary-dark text-white font-black py-3.5 rounded-2xl 
                                   hover:opacity-90 transition shadow-lg shadow-primary/25 flex items-center justify-center gap-2">
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
                    <a href="{{ route('user.shop') }}" class="text-sm text-primary font-bold hover:underline">
                        Lihat semua →
                    </a>
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

@push('scripts')
    <script>
        // ── Ganti foto utama saat thumbnail diklik ──────────────────
        function switchImg(btn, url) {
            const mainImg = document.getElementById('main-img');
            if (!mainImg) return;

            mainImg.style.opacity = '0';
            setTimeout(() => {
                mainImg.src = url;
                mainImg.style.opacity = '1';
            }, 120);

            // Tandai thumbnail yang sedang aktif
            document.querySelectorAll('.thumb-btn').forEach(el => {
                el.classList.remove('border-primary');
                el.classList.add('border-transparent');
            });
            btn.classList.remove('border-transparent');
            btn.classList.add('border-primary');
        }

        // ── Tambah / kurangi jumlah beli ─────────────────────────────
        function changeQty(delta) {
            const input = document.getElementById('qty');
            if (!input) return;
            let val = parseInt(input.value || '1', 10) + delta;
            const min = parseInt(input.min || '1', 10);
            const max = parseInt(input.max || '10', 10);
            if (val < min) val = min;
            if (val > max) val = max;
            input.value = val;
        }
    </script>
@endpush
