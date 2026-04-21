@extends('layouts.admin')
@section('title', 'Detail Produk')

@section('content')

    {{-- ── HEADER ──────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <a href="{{ route('admin.products.index') }}"
            class="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-primary transition w-fit">
            <i class="fa-solid fa-arrow-left text-xs"></i> Kembali ke daftar produk
        </a>
        <div class="flex items-center gap-3">
            {{-- Toggle Featured --}}
            <form action="{{ route('admin.products.toggle', $product) }}" method="POST">
                @csrf @method('PATCH')
                <button type="submit"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold transition
                {{ $product->is_active
                    ? 'bg-red-50 text-red-600 hover:bg-red-100'
                    : 'bg-green-50 text-green-600 hover:bg-green-100' }}">
                    <i class="fa-solid {{ $product->is_active ? 'fa-eye-slash' : 'fa-eye' }} text-xs"></i>
                    {{ $product->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                </button>
            </form>

            {{-- Edit --}}
            <a href="{{ route('admin.products.edit', $product) }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-yellow-50 text-yellow-700 rounded-xl text-sm font-bold hover:bg-yellow-100 transition">
                <i class="fa-solid fa-pen text-xs"></i> Edit Produk
            </a>

            {{-- Hapus --}}
            <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                onsubmit="return confirm('Yakin ingin menghapus produk ini? Tindakan tidak dapat dibatalkan.')">
                @csrf @method('DELETE')
                <button type="submit"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-red-50 text-red-600 rounded-xl text-sm font-bold hover:bg-red-100 transition">
                    <i class="fa-solid fa-trash text-xs"></i> Hapus
                </button>
            </form>
        </div>
    </div>

    {{-- ── BARIS 1: Foto + Info Utama ──────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

        {{-- FOTO PRODUK --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">

            {{-- Foto Utama --}}
            <div
                class="relative w-full h-72 bg-gradient-to-br from-purple-100 to-indigo-200 rounded-2xl overflow-hidden mb-4 group">
                @if ($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" id="main-photo" class="w-full h-full object-cover"
                        alt="{{ $product->name }}">
                @else
                    <div class="w-full h-full flex flex-col items-center justify-center text-gray-300">
                        <i class="fa-solid fa-image text-6xl mb-3"></i>
                        <p class="text-sm">Belum ada foto</p>
                    </div>
                @endif

                {{-- Badge Status --}}
                <div class="absolute top-3 left-3 flex flex-col gap-2">
                    @if ($product->is_featured)
                        <span
                            class="inline-flex items-center gap-1 bg-yellow-100 text-yellow-800 text-xs font-bold px-3 py-1 rounded-full shadow-sm">
                            <i class="fa-solid fa-star text-yellow-500 text-xs"></i> Unggulan
                        </span>
                    @endif
                    @if ($product->discount_percent)
                        <span
                            class="inline-flex items-center gap-1 bg-red-100 text-red-700 text-xs font-bold px-3 py-1 rounded-full shadow-sm">
                            <i class="fa-solid fa-tag text-xs"></i> -{{ $product->discount_percent }}%
                        </span>
                    @endif
                </div>

                {{-- Badge Aktif --}}
                <div class="absolute top-3 right-3">
                    <span
                        class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-1 rounded-full shadow-sm
                    {{ $product->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                        <span
                            class="w-1.5 h-1.5 rounded-full {{ $product->is_active ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                        {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>
            </div>

            {{-- Thumbnail Foto Tambahan --}}
            @if ($product->images && count($product->images) > 0)
                <div class="flex gap-3 overflow-x-auto pb-1">
                    {{-- Foto utama sebagai thumbnail pertama --}}
                    @if ($product->image)
                        <button onclick="switchPhoto('{{ asset('storage/' . $product->image) }}')"
                            class="flex-shrink-0 w-16 h-16 rounded-xl overflow-hidden border-2 border-primary">
                            <img src="{{ asset('storage/' . $product->image) }}" class="w-full h-full object-cover">
                        </button>
                    @endif
                    @foreach ($product->images as $img)
                        <button onclick="switchPhoto('{{ asset('storage/' . $img) }}')"
                            class="flex-shrink-0 w-16 h-16 rounded-xl overflow-hidden border-2 border-transparent hover:border-primary transition">
                            <img src="{{ asset('storage/' . $img) }}" class="w-full h-full object-cover">
                        </button>
                    @endforeach
                </div>
            @endif

            {{-- Info Upload --}}
            <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between text-xs text-gray-400">
                <span><i class="fa-solid fa-images mr-1"></i>
                    {{ ($product->images ? count($product->images) : 0) + ($product->image ? 1 : 0) }} foto tersedia
                </span>
                <a href="{{ route('admin.products.edit', $product) }}" class="text-primary hover:underline font-semibold">
                    Ganti foto →
                </a>
            </div>
        </div>

        {{-- INFO UTAMA --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col">

            {{-- Nama & Kategori --}}
            <div class="mb-5">
                <div class="flex items-center gap-2 mb-2">
                    @php
                        $catColor =
                            [
                                'kaos' => 'purple',
                                'celana' => 'orange',
                                'jaket' => 'emerald',
                                'aksesoris' => 'yellow',
                            ][$product->category] ?? 'gray';
                    @endphp
                    <span
                        class="inline-block bg-{{ $catColor }}-100 text-{{ $catColor }}-700 text-xs font-bold px-3 py-1 rounded-full">
                        {{ ucfirst($product->category) }}
                    </span>
                    <span class="text-xs text-gray-300">#PRD-{{ str_pad($product->id, 3, '0', STR_PAD_LEFT) }}</span>
                </div>
                <h1 class="text-2xl font-black text-gray-800 leading-tight mb-3">{{ $product->name }}</h1>
                @if ($product->description)
                    <p class="text-sm text-gray-500 leading-relaxed">{{ $product->description }}</p>
                @else
                    <p class="text-sm text-gray-300 italic">Belum ada deskripsi produk.</p>
                @endif
            </div>

            {{-- Blok Harga --}}
            <div class="bg-gradient-to-br from-purple-50 to-indigo-50 rounded-2xl p-5 mb-5 border border-purple-100">
                @if ($product->price_sale)
                    <div class="flex items-baseline gap-3 flex-wrap">
                        <span class="text-3xl font-black text-primary">{{ $product->display_sale_price }}</span>
                        <span class="text-lg text-gray-300 line-through font-medium">{{ $product->display_price }}</span>
                        <span class="bg-red-100 text-red-600 text-xs font-black px-2 py-1 rounded-lg">
                            Hemat {{ $product->discount_percent }}%
                        </span>
                    </div>
                    <p class="text-xs text-purple-400 mt-1 font-medium">Harga diskon aktif</p>
                @else
                    <span class="text-3xl font-black text-primary">{{ $product->display_price }}</span>
                    <p class="text-xs text-gray-400 mt-1">Harga normal</p>
                @endif
            </div>

            {{-- Detail Rows --}}
            <div class="flex-1 space-y-0 divide-y divide-gray-50">
                <div class="flex items-center justify-between py-3">
                    <span class="text-sm text-gray-400 flex items-center gap-2">
                        <i class="fa-solid fa-weight-hanging text-xs w-4 text-gray-300"></i> Berat
                    </span>
                    <span class="text-sm font-bold text-gray-700">{{ $product->weight }} gram</span>
                </div>
                <div class="flex items-center justify-between py-3">
                    <span class="text-sm text-gray-400 flex items-center gap-2">
                        <i class="fa-solid fa-boxes-stacked text-xs w-4 text-gray-300"></i> Total Stok
                    </span>
                    <span class="text-sm font-bold {{ $product->total_stock > 0 ? 'text-green-600' : 'text-red-500' }}">
                        {{ $product->total_stock }} pcs
                        @if ($product->total_stock == 0)
                            <span class="text-xs font-normal text-red-400 ml-1">(habis)</span>
                        @elseif($product->total_stock <= 10)
                            <span class="text-xs font-normal text-orange-400 ml-1">(hampir habis)</span>
                        @endif
                    </span>
                </div>
                <div class="flex items-center justify-between py-3">
                    <span class="text-sm text-gray-400 flex items-center gap-2">
                        <i class="fa-solid fa-star text-xs w-4 text-gray-300"></i> Unggulan
                    </span>
                    <span class="text-sm font-bold {{ $product->is_featured ? 'text-yellow-600' : 'text-gray-400' }}">
                        {{ $product->is_featured ? '⭐ Ya' : 'Tidak' }}
                    </span>
                </div>
                <div class="flex items-center justify-between py-3">
                    <span class="text-sm text-gray-400 flex items-center gap-2">
                        <i class="fa-regular fa-calendar text-xs w-4 text-gray-300"></i> Dibuat
                    </span>
                    <span class="text-sm font-bold text-gray-700">
                        {{ $product->created_at->isoFormat('D MMM Y') }}
                    </span>
                </div>
                <div class="flex items-center justify-between py-3">
                    <span class="text-sm text-gray-400 flex items-center gap-2">
                        <i class="fa-regular fa-clock text-xs w-4 text-gray-300"></i> Terakhir diperbarui
                    </span>
                    <span class="text-sm font-bold text-gray-700">
                        {{ $product->updated_at->diffForHumans() }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- ── BARIS 2: Stok Ukuran + Aksi Cepat ──────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- STOK PER UKURAN --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-5">
                <h3 class="font-bold text-gray-800 flex items-center gap-2">
                    <span class="w-7 h-7 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center">
                        <i class="fa-solid fa-ruler text-xs"></i>
                    </span>
                    Stok per Ukuran
                </h3>
                <span class="text-xs text-gray-400">
                    Total: <strong class="text-primary">{{ $product->total_stock }} pcs</strong>
                </span>
            </div>

            @php
                $maxStock = $product->sizes->max('stock') ?: 1;
                $allSizes = ['S', 'M', 'L', 'XL', 'XXL'];
            @endphp

            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                @foreach ($allSizes as $sizeLabel)
                    @php
                        $sizeObj = $product->sizes->firstWhere('size', $sizeLabel);
                        $stock = $sizeObj ? $sizeObj->stock : null;
                        $pct = $sizeObj && $maxStock > 0 ? round(($stock / $maxStock) * 100) : 0;

                        if ($sizeObj === null) {
                            $cardBg = 'bg-gray-50';
                            $border = 'border-gray-200';
                            $numColor = 'text-gray-300';
                            $lblColor = 'text-gray-300';
                            $barBg = 'bg-gray-200';
                            $barFill = 'bg-gray-300';
                            $display = '–';
                        } elseif ($stock == 0) {
                            $cardBg = 'bg-red-50';
                            $border = 'border-red-200';
                            $numColor = 'text-red-500';
                            $lblColor = 'text-red-400';
                            $barBg = 'bg-red-100';
                            $barFill = 'bg-red-400';
                            $display = '0';
                        } elseif ($stock <= 5) {
                            $cardBg = 'bg-orange-50';
                            $border = 'border-orange-200';
                            $numColor = 'text-orange-600';
                            $lblColor = 'text-orange-500';
                            $barBg = 'bg-orange-100';
                            $barFill = 'bg-orange-400';
                            $display = $stock;
                        } else {
                            $cardBg = 'bg-green-50';
                            $border = 'border-green-200';
                            $numColor = 'text-green-700';
                            $lblColor = 'text-green-600';
                            $barBg = 'bg-green-100';
                            $barFill = 'bg-green-500';
                            $display = $stock;
                        }
                    @endphp
                    <div class="{{ $cardBg }} border {{ $border }} rounded-2xl p-4 text-center">
                        <p class="text-2xl font-black {{ $numColor }} mb-1">{{ $display }}</p>
                        <p class="text-xs font-bold {{ $lblColor }} mb-3">Ukuran {{ $sizeLabel }}</p>
                        {{-- Progress bar --}}
                        <div class="{{ $barBg }} h-1.5 rounded-full overflow-hidden">
                            <div class="{{ $barFill }} h-1.5 rounded-full transition-all duration-500"
                                style="width: {{ $pct }}%"></div>
                        </div>
                        @if ($sizeObj === null)
                            <p class="text-xs text-gray-300 mt-2">Tidak tersedia</p>
                        @elseif($stock == 0)
                            <p class="text-xs text-red-400 mt-2 font-semibold">Habis</p>
                        @elseif($stock <= 5)
                            <p class="text-xs text-orange-500 mt-2 font-semibold">Hampir habis</p>
                        @else
                            <p class="text-xs text-green-500 mt-2 font-semibold">Tersedia</p>
                        @endif
                    </div>
                @endforeach

                {{-- Ringkasan Total --}}
                <div
                    class="bg-purple-50 border-2 border-dashed border-purple-200 rounded-2xl p-4 text-center
                        {{ count($allSizes) % 2 == 1 ? '' : 'sm:col-span-1' }}">
                    <p class="text-2xl font-black text-primary mb-1">{{ $product->total_stock }}</p>
                    <p class="text-xs font-bold text-purple-500 mb-3">Total Stok</p>
                    <div class="bg-purple-100 h-1.5 rounded-full">
                        <div class="bg-primary h-1.5 rounded-full" style="width:100%"></div>
                    </div>
                    <p class="text-xs text-purple-400 mt-2 font-semibold">Semua ukuran</p>
                </div>
            </div>

            {{-- Legenda --}}
            <div class="flex flex-wrap gap-3 mt-5 pt-4 border-t border-gray-100">
                <span class="flex items-center gap-1.5 text-xs text-gray-500">
                    <span class="w-3 h-3 bg-green-400 rounded-full"></span> Tersedia
                </span>
                <span class="flex items-center gap-1.5 text-xs text-gray-500">
                    <span class="w-3 h-3 bg-orange-400 rounded-full"></span> Hampir habis (≤5)
                </span>
                <span class="flex items-center gap-1.5 text-xs text-gray-500">
                    <span class="w-3 h-3 bg-red-400 rounded-full"></span> Habis
                </span>
                <span class="flex items-center gap-1.5 text-xs text-gray-500">
                    <span class="w-3 h-3 bg-gray-300 rounded-full"></span> Tidak dipasang
                </span>
            </div>
        </div>

        {{-- AKSI CEPAT --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <h3 class="font-bold text-gray-800 mb-5 flex items-center gap-2">
                <span class="w-7 h-7 bg-yellow-100 text-yellow-600 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-bolt text-xs"></i>
                </span>
                Aksi Cepat
            </h3>

            <div class="space-y-3">

                {{-- Edit --}}
                <a href="{{ route('admin.products.edit', $product) }}"
                    class="group flex items-center gap-4 p-4 bg-purple-50 hover:bg-purple-100 border border-purple-100 rounded-2xl transition">
                    <div
                        class="w-11 h-11 bg-primary rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-pen text-white text-sm"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-bold text-purple-900">Edit produk ini</p>
                        <p class="text-xs text-purple-500 mt-0.5">Ubah nama, harga, stok, foto & pengaturan</p>
                    </div>
                    <i class="fa-solid fa-chevron-right text-purple-300 text-xs"></i>
                </a>

                {{-- Toggle Featured --}}
                <form action="{{ route('admin.products.toggle', $product) }}" method="POST">
                    @csrf @method('PATCH')
                    <button type="submit"
                        class="group w-full flex items-center gap-4 p-4 rounded-2xl border transition
                    {{ $product->is_featured
                        ? 'bg-yellow-50 hover:bg-yellow-100 border-yellow-100'
                        : 'bg-gray-50 hover:bg-yellow-50 border-gray-100 hover:border-yellow-100' }}">
                        <div
                            class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform
                        {{ $product->is_featured ? 'bg-yellow-400' : 'bg-gray-300' }}">
                            <i class="fa-solid fa-star text-white text-sm"></i>
                        </div>
                        <div class="flex-1 text-left">
                            <p
                                class="text-sm font-bold {{ $product->is_featured ? 'text-yellow-900' : 'text-gray-700' }}">
                                {{ $product->is_featured ? 'Hapus dari unggulan' : 'Jadikan produk unggulan' }}
                            </p>
                            <p class="text-xs mt-0.5 {{ $product->is_featured ? 'text-yellow-600' : 'text-gray-400' }}">
                                {{ $product->is_featured ? 'Produk tidak tampil di halaman utama' : 'Tampilkan di halaman utama toko' }}
                            </p>
                        </div>
                        <i class="fa-solid fa-chevron-right text-gray-300 text-xs"></i>
                    </button>
                </form>

                {{-- Toggle Aktif --}}
                <form action="{{ route('admin.products.toggle', $product) }}" method="POST">
                    @csrf @method('PATCH')
                    <button type="submit"
                        class="group w-full flex items-center gap-4 p-4 rounded-2xl border transition
                    {{ $product->is_active
                        ? 'bg-orange-50 hover:bg-orange-100 border-orange-100'
                        : 'bg-green-50 hover:bg-green-100 border-green-100' }}">
                        <div
                            class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform
                        {{ $product->is_active ? 'bg-orange-400' : 'bg-green-500' }}">
                            <i
                                class="fa-solid {{ $product->is_active ? 'fa-eye-slash' : 'fa-eye' }} text-white text-sm"></i>
                        </div>
                        <div class="flex-1 text-left">
                            <p class="text-sm font-bold {{ $product->is_active ? 'text-orange-900' : 'text-green-900' }}">
                                {{ $product->is_active ? 'Nonaktifkan produk' : 'Aktifkan produk' }}
                            </p>
                            <p class="text-xs mt-0.5 {{ $product->is_active ? 'text-orange-500' : 'text-green-600' }}">
                                {{ $product->is_active ? 'Sembunyikan dari halaman toko' : 'Tampilkan kembali ke toko' }}
                            </p>
                        </div>
                        <i class="fa-solid fa-chevron-right text-gray-300 text-xs"></i>
                    </button>
                </form>

                {{-- Hapus --}}
                <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                    onsubmit="return confirm('Yakin hapus produk \'{{ addslashes($product->name) }}\'?\nSemua foto dan data produk akan dihapus permanen.')">
                    @csrf @method('DELETE')
                    <button type="submit"
                        class="group w-full flex items-center gap-4 p-4 bg-red-50 hover:bg-red-100 border border-red-100 rounded-2xl transition">
                        <div
                            class="w-11 h-11 bg-red-500 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-trash text-white text-sm"></i>
                        </div>
                        <div class="flex-1 text-left">
                            <p class="text-sm font-bold text-red-900">Hapus produk ini</p>
                            <p class="text-xs text-red-400 mt-0.5">Tindakan ini tidak dapat dibatalkan</p>
                        </div>
                        <i class="fa-solid fa-chevron-right text-red-200 text-xs"></i>
                    </button>
                </form>
            </div>

            {{-- Info Produk Singkat --}}
            <div class="mt-5 pt-5 border-t border-gray-100">
                <p class="text-xs font-semibold text-gray-400 mb-3 uppercase tracking-wider">Slug / URL Produk</p>
                <div class="bg-gray-50 rounded-xl px-4 py-3 flex items-center justify-between gap-2">
                    <code class="text-xs text-gray-500 truncate flex-1">{{ $product->slug }}</code>
                    <button onclick="copySlug('{{ $product->slug }}')"
                        class="text-xs text-primary hover:underline font-semibold flex-shrink-0" id="copy-btn">
                        Salin
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        function switchPhoto(src) {
            const main = document.getElementById('main-photo');
            if (main) {
                main.style.opacity = '0';
                setTimeout(() => {
                    main.src = src;
                    main.style.opacity = '1';
                }, 150);
                main.style.transition = 'opacity 0.15s ease';
            }
        }

        function copySlug(text) {
            navigator.clipboard.writeText(text).then(() => {
                const btn = document.getElementById('copy-btn');
                btn.textContent = 'Tersalin!';
                btn.classList.add('text-green-600');
                setTimeout(() => {
                    btn.textContent = 'Salin';
                    btn.classList.remove('text-green-600');
                }, 2000);
            });
        }
    </script>
@endpush
