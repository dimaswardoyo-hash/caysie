@extends('layouts.app')
@section('title', 'Toko')

@push('styles')
    <style>
        /* ── Pagination restyle agar konsisten dengan tema ungu ── */
        .shop-pagination nav {
            display: flex;
            justify-content: center;
        }

        .shop-pagination nav>div:first-child {
            display: none;
        }

        /* sembunyikan teks "Showing x to y" bawaan */
        .shop-pagination .flex.justify-between.flex-1.sm\:hidden {
            display: none;
        }

        .shop-pagination span[aria-current="page"] span,
        .shop-pagination span[aria-current="page"] {
            background: linear-gradient(to right, #6C63FF, #5a52e0) !important;
            color: #fff !important;
            border-color: transparent !important;
            border-radius: 12px !important;
            box-shadow: 0 8px 16px rgba(108, 99, 255, .25);
        }

        .shop-pagination a,
        .shop-pagination span:not([aria-current="page"]) {
            border-radius: 12px !important;
            border-color: #f0f0f0 !important;
            color: #6b7280 !important;
            margin: 0 2px;
            transition: all .15s ease;
        }

        .shop-pagination a:hover {
            background: #f5f4ff !important;
            color: #6C63FF !important;
            border-color: #e4e1ff !important;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .product-fade {
            animation: fadeUp .35s ease both;
        }
    </style>
@endpush

@section('content')

    <div class="max-w-7xl mx-auto px-6 py-8">

        {{-- ===================== HERO STRIP ===================== --}}
        <div
            class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-primary to-primary-dark px-7 py-9 md:px-10 md:py-12 mb-8">
            <div class="absolute -right-10 -top-10 w-56 h-56 bg-white/10 rounded-full blur-2xl"></div>
            <div class="absolute right-16 bottom-0 w-32 h-32 bg-white/10 rounded-full blur-xl"></div>
            <div class="relative flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <span
                        class="inline-flex items-center gap-1.5 bg-white/15 text-white text-xs font-bold px-3 py-1 rounded-full mb-3">
                        <i class="fa-solid fa-shirt"></i> Koleksi Caysie
                    </span>
                    <h1 class="text-3xl md:text-4xl font-black text-white tracking-tight">Semua Produk</h1>
                    <p class="text-white/70 text-sm mt-1.5 max-w-md">Kaos, celana, jaket, dan aksesoris pilihan — kualitas
                        premium, harga bersahabat.</p>
                </div>
                <div class="flex items-center gap-2 bg-white/15 backdrop-blur-sm px-4 py-2.5 rounded-2xl w-fit">
                    <i class="fa-solid fa-box text-white/80"></i>
                    <p class="text-sm text-white font-bold">{{ $products->total() }} produk ditemukan</p>
                </div>
            </div>
        </div>

        {{-- ===================== FILTER CARD ===================== --}}
        <div class="bg-white border border-gray-100 rounded-2xl p-5 mb-6 shadow-sm shadow-gray-100">
            <form method="GET" class="flex flex-wrap gap-3">
                <div class="relative flex-1 min-w-48">
                    <i
                        class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari produk..."
                        class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-primary focus:bg-white transition">
                </div>
                <select name="category"
                    class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium text-gray-600 focus:outline-none focus:border-primary transition">
                    <option value="">Semua Kategori</option>
                    @foreach (['kaos', 'celana', 'jaket', 'aksesoris'] as $cat)
                        <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>
                            {{ ucfirst($cat) }}</option>
                    @endforeach
                </select>
                <select name="sort"
                    class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium text-gray-600 focus:outline-none focus:border-primary transition">
                    <option value="">Terbaru</option>
                    <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Harga Terendah</option>
                    <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Harga Tertinggi
                    </option>
                </select>
                <button type="submit"
                    class="px-5 py-2.5 bg-gradient-to-r from-primary to-primary-dark text-white text-sm font-bold rounded-xl hover:opacity-90 transition shadow-lg shadow-primary/25">
                    <i class="fa-solid fa-filter mr-1"></i> Filter
                </button>
                @if (request()->hasAny(['search', 'category', 'sort']))
                    <a href="{{ route('user.shop') }}"
                        class="px-5 py-2.5 bg-gray-50 border border-gray-200 text-gray-500 text-sm font-bold rounded-xl hover:bg-gray-100 transition">
                        <i class="fa-solid fa-rotate-left mr-1"></i> Reset
                    </a>
                @endif
            </form>

            {{-- Quick category chips --}}
            <div class="flex flex-wrap gap-2 mt-4 pt-4 border-t border-gray-100">
                <a href="{{ route('user.shop', request()->except(['category', 'page'])) }}"
                    class="px-3.5 py-1.5 rounded-full text-xs font-bold transition
                      {{ !request('category') ? 'bg-primary text-white shadow-md shadow-primary/25' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}">
                    Semua
                </a>
                @foreach (['kaos', 'celana', 'jaket', 'aksesoris'] as $cat)
                    <a href="{{ route('user.shop', array_merge(request()->except(['category', 'page']), ['category' => $cat])) }}"
                        class="px-3.5 py-1.5 rounded-full text-xs font-bold transition
                      {{ request('category') === $cat ? 'bg-primary text-white shadow-md shadow-primary/25' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}">
                        {{ ucfirst($cat) }}
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Active filter tags (search) --}}
        @if (request('search'))
            <div class="flex items-center gap-2 mb-6 -mt-2">
                <span class="text-xs text-gray-400 font-semibold">Menampilkan hasil untuk:</span>
                <span
                    class="inline-flex items-center gap-2 bg-primary/10 text-primary text-xs font-bold px-3 py-1.5 rounded-full">
                    "{{ request('search') }}"
                    <a href="{{ route('user.shop', request()->except(['search', 'page'])) }}"
                        class="hover:text-primary-dark">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                </span>
            </div>
        @endif

        {{-- ===================== PRODUCT GRID ===================== --}}
        @if ($products->isEmpty())
            <div class="bg-white rounded-2xl py-20 text-center text-gray-400 border border-gray-100">
                <div class="w-20 h-20 bg-gray-50 rounded-3xl flex items-center justify-center mx-auto mb-5">
                    <i class="fa-solid fa-box-open text-3xl opacity-40"></i>
                </div>
                <p class="font-bold text-gray-600">Produk tidak ditemukan</p>
                <p class="text-sm text-gray-400 mt-1">Coba ubah kata kunci atau kategori pencarianmu</p>
                <a href="{{ route('user.shop') }}"
                    class="inline-flex items-center gap-2 mt-5 px-5 py-2.5 bg-gradient-to-r from-primary to-primary-dark text-white text-sm font-bold rounded-xl hover:opacity-90 transition shadow-lg shadow-primary/25">
                    <i class="fa-solid fa-rotate-left"></i> Reset Filter
                </a>
            </div>
        @else
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5 mb-8">
                @foreach ($products as $i => $product)
                    <div class="product-fade" style="animation-delay: {{ min($i, 8) * 40 }}ms">
                        @include('user.partials.product-card', ['product' => $product])
                    </div>
                @endforeach
            </div>
            <div class="shop-pagination">
                {{ $products->withQueryString()->links() }}
            </div>
        @endif

    </div>

@endsection
