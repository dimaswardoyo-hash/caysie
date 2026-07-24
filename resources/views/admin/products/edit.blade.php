@extends('layouts.admin')
@section('title', 'Edit Produk')

@section('content')

    <div class="mb-6">
        <a href="{{ route('admin.products.index') }}" class="text-sm text-gray-400 hover:text-primary transition">
            ← Kembali ke daftar produk
        </a>
    </div>

    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <div class="lg:col-span-2 space-y-6">

                {{-- Info Dasar --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-800 mb-5 flex items-center gap-2">
                        <span
                            class="w-7 h-7 bg-purple-100 text-primary rounded-lg flex items-center justify-center text-xs"><i
                                class="fa-solid fa-tag"></i></span>
                        Informasi Produk
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-1.5">Nama Produk <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-primary transition @error('name') border-red-400 @enderror">
                            @error('name')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-1.5">Deskripsi</label>
                            <textarea name="description" rows="4"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-primary transition resize-none">{{ old('description', $product->description) }}</textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 mb-1.5">Kategori <span
                                        class="text-red-500">*</span></label>
                                <select name="category" id="category-select" required
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-primary transition"
                                    onchange="toggleSizeOptionsByCategory(this.value)">
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat }}"
                                            {{ old('category', $product->category) === $cat ? 'selected' : '' }}>
                                            {{ ucfirst($cat) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 mb-1.5">Berat (gram) <span
                                        class="text-red-500">*</span></label>
                                <input type="number" name="weight" value="{{ old('weight', $product->weight) }}"
                                    min="150" max="250" required
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-primary transition">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Harga --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-800 mb-5 flex items-center gap-2">
                        <span
                            class="w-7 h-7 bg-green-100 text-green-600 rounded-lg flex items-center justify-center text-xs"><i
                                class="fa-solid fa-money-bill"></i></span>
                        Harga Produk
                    </h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-1.5">Harga Normal <span
                                    class="text-red-500">*</span></label>
                            <div class="relative">
                                <span
                                    class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-semibold text-gray-400">Rp</span>
                                <input type="number" name="price" value="{{ old('price', (int) $product->price) }}"
                                    required min="0"
                                    class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-primary transition">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-1.5">Harga Diskon</label>
                            <div class="relative">
                                <span
                                    class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-semibold text-gray-400">Rp</span>
                                <input type="number" name="price_sale"
                                    value="{{ old('price_sale', (int) $product->price_sale) }}" min="0"
                                    class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-primary transition">
                            </div>
                            @error('price_sale')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Ukuran & Stok --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-800 mb-5 flex items-center gap-2">
                        <span
                            class="w-7 h-7 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center text-xs"><i
                                class="fa-solid fa-ruler"></i></span>
                        Ukuran & Stok
                    </h3>
                    <div class="space-y-3">
                        @php $existingSizes = $product->sizes->keyBy('size'); @endphp
                        @foreach ($sizes as $i => $size)
                            @php $existing = $existingSizes->get($size); @endphp
                            <div class="size-item flex items-center gap-4 p-4 bg-gray-50 rounded-xl border border-gray-100"
                                data-size="{{ $size }}">
                                <label class="flex items-center gap-3 cursor-pointer flex-1">
                                    <input type="checkbox" name="sizes[{{ $i }}][size]"
                                        value="{{ $size }}" class="w-5 h-5 rounded accent-primary"
                                        {{ $existing ? 'checked' : '' }} onchange="toggleStockInput(this)">
                                    <span
                                        class="font-bold text-gray-700 text-sm w-20 whitespace-nowrap">{{ $size }}</span>
                                </label>
                                <div class="flex items-center gap-2 flex-1">
                                    <label class="text-xs text-gray-400 w-10">Stok:</label>
                                    <input type="number" name="sizes[{{ $i }}][stock]"
                                        value="{{ $existing ? $existing->stock : 0 }}" min="0"
                                        class="stock-input w-24 px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-primary transition disabled:bg-gray-100 disabled:text-gray-300"
                                        {{ !$existing ? 'disabled' : '' }}>
                                </div>
                                <span class="text-xs text-gray-400">pcs</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Kolom Kanan --}}
            <div class="space-y-6">

                {{-- Foto --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-800 mb-5 flex items-center gap-2">
                        <span
                            class="w-7 h-7 bg-orange-100 text-orange-500 rounded-lg flex items-center justify-center text-xs"><i
                                class="fa-solid fa-image"></i></span>
                        Foto Produk
                    </h3>
                    <div id="image-preview"
                        class="w-full h-48 bg-gray-50 border-2 border-dashed border-gray-200 rounded-2xl overflow-hidden cursor-pointer hover:border-primary transition mb-4"
                        onclick="document.getElementById('image-input').click()">
                        @if ($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex flex-col items-center justify-center">
                                <i class="fa-solid fa-cloud-arrow-up text-3xl text-gray-300 mb-2"></i>
                                <p class="text-xs text-gray-400">Klik untuk ganti foto</p>
                            </div>
                        @endif
                    </div>
                    <input type="file" id="image-input" name="image" accept="image/*" class="hidden"
                        onchange="previewImage(this)">
                    @if ($product->image)
                        <p class="text-xs text-gray-400 text-center mb-4">Klik gambar untuk mengganti foto utama</p>
                    @endif

                    {{-- Foto tambahan yang ada --}}
                    @if ($product->images && count($product->images) > 0)
                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <p class="text-xs font-semibold text-gray-500 mb-3">Foto Tambahan Saat Ini</p>
                            <div class="grid grid-cols-3 gap-2">
                                @foreach ($product->images as $idx => $img)
                                    <div class="relative group">
                                        <img src="{{ asset('storage/' . $img) }}"
                                            class="w-full h-20 object-cover rounded-lg">
                                        <form action="{{ route('admin.products.delete-image', $product) }}"
                                            method="POST" onsubmit="return confirm('Hapus foto ini?')">
                                            @csrf @method('DELETE')
                                            <input type="hidden" name="index" value="{{ $idx }}">
                                            <button type="submit"
                                                class="absolute top-1 right-1 w-6 h-6 bg-red-500 text-white rounded-full text-xs opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                                                ×
                                            </button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <label class="block text-xs font-semibold text-gray-500 mb-2">Tambah Foto Baru</label>
                        <input type="file" name="images[]" accept="image/*" multiple
                            class="w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-purple-50 file:text-primary hover:file:bg-purple-100 transition">
                    </div>
                </div>

                {{-- Pengaturan --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-800 mb-5 flex items-center gap-2">
                        <span
                            class="w-7 h-7 bg-gray-100 text-gray-600 rounded-lg flex items-center justify-center text-xs"><i
                                class="fa-solid fa-gear"></i></span>
                        Pengaturan
                    </h3>
                    <div class="space-y-4">
                        <label class="flex items-center justify-between cursor-pointer p-3 bg-gray-50 rounded-xl">
                            <div>
                                <p class="text-sm font-semibold text-gray-700">Aktifkan Produk</p>
                                <p class="text-xs text-gray-400">Tampil di toko</p>
                            </div>
                            <div class="relative">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" value="1"
                                    {{ $product->is_active ? 'checked' : '' }} class="sr-only peer">
                                <div
                                    class="w-11 h-6 bg-gray-200 peer-checked:bg-primary rounded-full peer after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-5">
                                </div>
                            </div>
                        </label>
                        <label class="flex items-center justify-between cursor-pointer p-3 bg-gray-50 rounded-xl">
                            <div>
                                <p class="text-sm font-semibold text-gray-700">Produk Unggulan</p>
                                <p class="text-xs text-gray-400">Tampil di halaman utama</p>
                            </div>
                            <div class="relative">
                                <input type="hidden" name="is_featured" value="0">
                                <input type="checkbox" name="is_featured" value="1"
                                    {{ $product->is_featured ? 'checked' : '' }} class="sr-only peer">
                                <div
                                    class="w-11 h-6 bg-gray-200 peer-checked:bg-yellow-400 rounded-full peer after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-5">
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <button type="submit"
                        class="w-full bg-primary text-white font-bold py-3.5 rounded-xl hover:bg-primary-dark transition shadow-lg shadow-purple-200 text-sm flex items-center justify-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                    </button>
                    <a href="{{ route('admin.products.index') }}"
                        class="block text-center mt-3 text-sm text-gray-400 hover:text-gray-600 transition">Batal</a>
                </div>
            </div>
        </div>
    </form>

@endsection

@push('scripts')
    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = e => {
                    document.getElementById('image-preview').innerHTML =
                        `<img src="${e.target.result}" class="w-full h-full object-cover">`;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function toggleStockInput(checkbox) {
            const container = checkbox.closest('.flex');
            const stockInput = container.querySelector('.stock-input');
            stockInput.disabled = !checkbox.checked;
            if (!checkbox.checked) stockInput.value = 0;
            else stockInput.focus();
        }

        // 'One Size' hanya relevan untuk kategori Aksesoris — sama seperti di
        // form tambah produk. Item ukuran yang tidak relevan disembunyikan
        // dan checkbox-nya di-uncheck supaya tidak ikut ter-submit diam-diam.
        function toggleSizeOptionsByCategory(category) {
            document.querySelectorAll('.size-item').forEach(item => {
                const isOneSizeItem = item.dataset.size === 'One Size';
                const shouldShow = category === 'aksesoris' ? isOneSizeItem : !isOneSizeItem;

                item.classList.toggle('hidden', !shouldShow);

                if (!shouldShow) {
                    const checkbox = item.querySelector('input[type="checkbox"]');
                    const stockInput = item.querySelector('.stock-input');
                    checkbox.checked = false;
                    stockInput.disabled = true;
                    stockInput.value = 0;
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            const currentCategory = document.getElementById('category-select').value;
            if (currentCategory) {
                toggleSizeOptionsByCategory(currentCategory);
            }
        });
    </script>
@endpush
