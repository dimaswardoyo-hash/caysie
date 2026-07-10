@extends('layouts.admin')
@section('title', 'Manajemen Produk')

@section('content')

    {{-- Alert --}}
    @if (session('success'))
        <div class="mb-6 flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 px-5 py-4 rounded-2xl">
            <i class="fa-solid fa-circle-check text-green-500 text-lg"></i>
            <span class="font-semibold text-sm">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 sm:mb-8">
        <div>
            <p class="text-sm text-gray-400 mb-1">Total {{ $products->total() }} produk</p>
        </div>
        <a href="{{ route('admin.products.create') }}"
            class="inline-flex items-center justify-center gap-2 bg-primary text-white font-bold px-6 py-3 rounded-xl hover:bg-primary-dark transition shadow-lg shadow-purple-200 text-sm">
            <i class="fa-solid fa-plus"></i> Tambah Produk
        </a>
    </div>

    {{-- Filter & Search --}}
    <form method="GET"
        class="bg-white rounded-2xl p-4 sm:p-5 shadow-sm border border-gray-100 mb-6 flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-full sm:min-w-52">
            <label class="block text-xs font-semibold text-gray-500 mb-1">Cari Produk</label>
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama produk..."
                    class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-primary transition">
            </div>
        </div>
        <div class="flex-1 min-w-[calc(50%-0.375rem)] sm:min-w-36 sm:flex-none">
            <label class="block text-xs font-semibold text-gray-500 mb-1">Kategori</label>
            <select name="category"
                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-primary transition">
                <option value="">Semua</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>
                        {{ ucfirst($cat) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="flex-1 min-w-[calc(50%-0.375rem)] sm:min-w-36 sm:flex-none">
            <label class="block text-xs font-semibold text-gray-500 mb-1">Status</label>
            <select name="status"
                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-primary transition">
                <option value="">Semua</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
            </select>
        </div>
        <button type="submit"
            class="flex-1 sm:flex-none px-5 py-2.5 bg-primary text-white text-sm font-semibold rounded-xl hover:bg-primary-dark transition">
            <i class="fa-solid fa-filter mr-1"></i> Filter
        </button>
        @if (request()->hasAny(['search', 'category', 'status']))
            <a href="{{ route('admin.products.index') }}"
                class="flex-1 sm:flex-none text-center px-5 py-2.5 bg-gray-100 text-gray-600 text-sm font-semibold rounded-xl hover:bg-gray-200 transition">
                Reset
            </a>
        @endif
    </form>

    @if ($products->isEmpty())
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 py-20 text-center text-gray-400">
            <i class="fa-solid fa-box-open text-5xl mb-4 opacity-30"></i>
            <p class="font-semibold">Belum ada produk</p>
            <p class="text-sm mt-1">Klik "Tambah Produk" untuk mulai menambahkan.</p>
        </div>
    @else
        {{-- Mobile Card List --}}
        <div class="md:hidden space-y-3">
            @foreach ($products as $product)
                @php
                    $catColors = [
                        'kaos' => 'purple',
                        'celana' => 'orange',
                        'jaket' => 'emerald',
                        'aksesoris' => 'yellow',
                    ];
                    $c = $catColors[$product->category] ?? 'gray';
                @endphp
                <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100">
                    <div class="flex items-start gap-3">
                        <div class="w-16 h-16 rounded-xl overflow-hidden bg-gray-100 flex-shrink-0">
                            @if ($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" class="w-full h-full object-cover"
                                    alt="{{ $product->name }}">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-2xl">👕</div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-gray-800 truncate">{{ $product->name }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">
                                {{ $product->is_featured ? '⭐ Unggulan · ' : '' }}Stok: {{ $product->total_stock }}
                            </p>
                            <div class="flex items-center gap-2 mt-1.5 flex-wrap">
                                <span
                                    class="inline-block bg-{{ $c }}-100 text-{{ $c }}-700 text-xs font-bold px-2.5 py-0.5 rounded-full">
                                    {{ ucfirst($product->category) }}
                                </span>
                                <span class="font-bold text-gray-800 text-sm">{{ $product->display_price }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-1 mt-3">
                        @foreach ($product->sizes->sortBy('size') as $size)
                            <span
                                class="text-xs px-2 py-0.5 rounded-lg font-semibold
                                {{ $size->stock > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-500' }}">
                                {{ $size->size }}: {{ $size->stock }}
                            </span>
                        @endforeach
                    </div>

                    <div class="flex items-center justify-between mt-4 pt-3 border-t border-gray-50">
                        <form action="{{ route('admin.products.toggle', $product) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold transition
                                {{ $product->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-500' }}">
                                <span
                                    class="w-1.5 h-1.5 rounded-full {{ $product->is_active ? 'bg-green-500' : 'bg-red-400' }}"></span>
                                {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                            </button>
                        </form>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.products.show', $product) }}"
                                class="w-9 h-9 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center">
                                <i class="fa-solid fa-eye text-xs"></i>
                            </a>
                            <a href="{{ route('admin.products.edit', $product) }}"
                                class="w-9 h-9 bg-yellow-50 text-yellow-600 rounded-lg flex items-center justify-center">
                                <i class="fa-solid fa-pen text-xs"></i>
                            </a>
                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                                onsubmit="return confirm('Yakin hapus produk ini?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="w-9 h-9 bg-red-50 text-red-500 rounded-lg flex items-center justify-center">
                                    <i class="fa-solid fa-trash text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach

            @if ($products->hasPages())
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 px-4 py-3">
                    {{ $products->links() }}
                </div>
            @endif
        </div>

        {{-- Desktop Table --}}
        <div class="hidden md:block bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100 text-xs text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-4 text-left">Produk</th>
                            <th class="px-4 py-4 text-left">Kategori</th>
                            <th class="px-4 py-4 text-left">Harga</th>
                            <th class="px-4 py-4 text-left">Ukuran & Stok</th>
                            <th class="px-4 py-4 text-left">Berat</th>
                            <th class="px-4 py-4 text-center">Status</th>
                            <th class="px-4 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($products as $product)
                            <tr class="hover:bg-gray-50/50 transition">
                                {{-- Produk --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-14 h-14 rounded-xl overflow-hidden bg-gray-100 flex-shrink-0">
                                            @if ($product->image)
                                                <img src="{{ asset('storage/' . $product->image) }}"
                                                    class="w-full h-full object-cover" alt="{{ $product->name }}">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-2xl">👕
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-800">{{ $product->name }}</p>
                                            <p class="text-xs text-gray-400 mt-0.5">
                                                {{ $product->is_featured ? '⭐ Unggulan · ' : '' }}
                                                Stok total: {{ $product->total_stock }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                {{-- Kategori --}}
                                <td class="px-4 py-4">
                                    @php
                                        $catColors = [
                                            'kaos' => 'purple',
                                            'celana' => 'orange',
                                            'jaket' => 'emerald',
                                            'aksesoris' => 'yellow',
                                        ];
                                        $c = $catColors[$product->category] ?? 'gray';
                                    @endphp
                                    <span
                                        class="inline-block bg-{{ $c }}-100 text-{{ $c }}-700 text-xs font-bold px-3 py-1 rounded-full">
                                        {{ ucfirst($product->category) }}
                                    </span>
                                </td>
                                {{-- Harga --}}
                                <td class="px-4 py-4">
                                    <p class="font-bold text-gray-800">{{ $product->display_price }}</p>
                                    @if ($product->price_sale)
                                        <p class="text-xs text-red-500 font-semibold">
                                            {{ $product->display_sale_price }}
                                            <span
                                                class="text-gray-400 line-through ml-1">{{ $product->display_price }}</span>
                                        </p>
                                    @endif
                                </td>
                                {{-- Ukuran & Stok --}}
                                <td class="px-4 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach ($product->sizes->sortBy('size') as $size)
                                            <span
                                                class="text-xs px-2 py-0.5 rounded-lg font-semibold
                                {{ $size->stock > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-500' }}">
                                                {{ $size->size }}: {{ $size->stock }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                {{-- Berat --}}
                                <td class="px-4 py-4 text-gray-600 text-sm">{{ $product->weight }}g</td>
                                {{-- Status --}}
                                <td class="px-4 py-4 text-center">
                                    <form action="{{ route('admin.products.toggle', $product) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <button type="submit"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold transition
                                {{ $product->is_active
                                    ? 'bg-green-100 text-green-700 hover:bg-red-100 hover:text-red-600'
                                    : 'bg-red-100 text-red-500 hover:bg-green-100 hover:text-green-700' }}">
                                            <span
                                                class="w-1.5 h-1.5 rounded-full {{ $product->is_active ? 'bg-green-500' : 'bg-red-400' }}"></span>
                                            {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </button>
                                    </form>
                                </td>
                                {{-- Aksi --}}
                                <td class="px-4 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('admin.products.show', $product) }}"
                                            class="w-8 h-8 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center hover:bg-blue-100 transition"
                                            title="Detail">
                                            <i class="fa-solid fa-eye text-xs"></i>
                                        </a>
                                        <a href="{{ route('admin.products.edit', $product) }}"
                                            class="w-8 h-8 bg-yellow-50 text-yellow-600 rounded-lg flex items-center justify-center hover:bg-yellow-100 transition"
                                            title="Edit">
                                            <i class="fa-solid fa-pen text-xs"></i>
                                        </a>
                                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                                            onsubmit="return confirm('Yakin hapus produk ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="w-8 h-8 bg-red-50 text-red-500 rounded-lg flex items-center justify-center hover:bg-red-100 transition"
                                                title="Hapus">
                                                <i class="fa-solid fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($products->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $products->links() }}
                </div>
            @endif
    @endif
    </div>

@endsection
