@extends('layouts.app')
@section('title', 'Toko')

@section('content')

<div class="flex flex-col md:flex-row gap-3 items-start md:items-center justify-between mb-6">
    <h1 class="text-2xl font-black text-gray-800">Semua Produk</h1>
    <p class="text-sm text-gray-400">{{ $products->total() }} produk ditemukan</p>
</div>

{{-- Filter --}}
<form method="GET" class="flex flex-wrap gap-3 mb-8">
    <div class="relative flex-1 min-w-48">
        <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Cari produk..."
               class="w-full pl-9 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-primary transition">
    </div>
    <select name="category" class="px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-primary transition">
        <option value="">Semua Kategori</option>
        @foreach(['kaos','celana','jaket','aksesoris'] as $cat)
        <option value="{{ $cat }}" {{ request('category')===$cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
        @endforeach
    </select>
    <select name="sort" class="px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-primary transition">
        <option value="">Terbaru</option>
        <option value="price_asc"  {{ request('sort')==='price_asc'  ? 'selected' : '' }}>Harga Terendah</option>
        <option value="price_desc" {{ request('sort')==='price_desc' ? 'selected' : '' }}>Harga Tertinggi</option>
    </select>
    <button type="submit" class="px-5 py-2.5 bg-primary text-white text-sm font-bold rounded-xl hover:bg-primary-dark transition">
        <i class="fa-solid fa-filter mr-1"></i> Filter
    </button>
    @if(request()->hasAny(['search','category','sort']))
    <a href="{{ route('user.shop') }}" class="px-5 py-2.5 bg-gray-100 text-gray-600 text-sm font-bold rounded-xl hover:bg-gray-200 transition">Reset</a>
    @endif
</form>

@if($products->isEmpty())
<div class="bg-white rounded-2xl py-20 text-center text-gray-400 border border-gray-100">
    <i class="fa-solid fa-box-open text-5xl mb-4 opacity-30"></i>
    <p class="font-semibold">Produk tidak ditemukan</p>
    <a href="{{ route('user.shop') }}" class="text-sm text-primary mt-2 inline-block hover:underline">Reset filter</a>
</div>
@else
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5 mb-8">
    @foreach($products as $product)
        @include('user.partials.product-card', ['product' => $product])
    @endforeach
</div>
{{ $products->withQueryString()->links() }}
@endif

@endsection