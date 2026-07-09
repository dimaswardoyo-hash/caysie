@extends('layouts.app')
@section('title', 'Checkout')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">

        <h1 class="text-2xl font-black text-gray-800 mb-6 flex items-center gap-2">
            <i class="fa-solid fa-bag-shopping text-primary"></i> Checkout
        </h1>

        <form action="{{ route('user.checkout.store') }}" method="POST" id="form-checkout">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- ══════════════ KIRI ══════════════ --}}
                <div class="lg:col-span-2 space-y-5">

                    {{-- IDENTITAS --}}
                    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                        <h3 class="font-black text-gray-800 mb-4 flex items-center gap-2">
                            <span class="w-7 h-7 bg-purple-100 text-primary rounded-lg flex items-center justify-center">
                                <i class="fa-solid fa-user text-xs"></i>
                            </span>
                            Identitas Penerima
                        </h3>
                        <div class="space-y-3">

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 mb-1.5">
                                        Nama Penerima <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="receiver_name"
                                        value="{{ old('receiver_name', auth()->user()->name) }}" required
                                        placeholder="Nama lengkap penerima"
                                        class="input-field @error('receiver_name') border-red-400 @enderror">
                                    @error('receiver_name')
                                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 mb-1.5">
                                        Nomor HP <span class="text-red-500">*</span>
                                    </label>
                                    <input type="tel" name="receiver_phone"
                                        value="{{ old('receiver_phone', auth()->user()->phone ?? '') }}" required
                                        placeholder="08xxxxxxxxxx"
                                        class="input-field @error('receiver_phone') border-red-400 @enderror">
                                    @error('receiver_phone')
                                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Provinsi + Kota --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 mb-1.5">
                                        Provinsi <span class="text-red-500">*</span>
                                    </label>
                                    <div class="select-wrap">
                                        <select id="sel-province" name="receiver_province" required
                                            class="input-field appearance-none pr-10 @error('receiver_province') border-red-400 @enderror">
                                            <option value="">Memuat provinsi...</option>
                                        </select>
                                        <span id="spin-province" class="select-spinner hidden"></span>
                                        <i class="fa-solid fa-chevron-down select-arrow"></i>
                                    </div>
                                    <input type="hidden" id="hid-province-id" name="receiver_province_id">
                                    @error('receiver_province')
                                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-600 mb-1.5">
                                        Kota / Kabupaten <span class="text-red-500">*</span>
                                    </label>
                                    <div class="select-wrap">
                                        <select id="sel-city" name="receiver_city" required disabled
                                            class="input-field appearance-none pr-10 disabled:bg-gray-50 disabled:text-gray-400 @error('receiver_city') border-red-400 @enderror">
                                            <option value="">-- Pilih provinsi dulu --</option>
                                        </select>
                                        <span id="spin-city" class="select-spinner hidden"></span>
                                        <i class="fa-solid fa-chevron-down select-arrow"></i>
                                    </div>
                                    {{-- ID kota dikirim ke OngkirController untuk query BinderByte --}}
                                    <input type="hidden" id="hid-city-id" name="receiver_city_id">
                                    @error('receiver_city')
                                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Kecamatan + Kelurahan --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 mb-1.5">
                                        Kecamatan <span class="text-gray-400 font-normal">(opsional)</span>
                                    </label>
                                    <div class="select-wrap">
                                        <select id="sel-district" name="receiver_district" disabled
                                            class="input-field appearance-none pr-10 disabled:bg-gray-50 disabled:text-gray-400">
                                            <option value="">-- Pilih kota dulu --</option>
                                        </select>
                                        <span id="spin-district" class="select-spinner hidden"></span>
                                        <i class="fa-solid fa-chevron-down select-arrow"></i>
                                    </div>
                                    <input type="hidden" id="hid-district-id" name="receiver_district_id">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-600 mb-1.5">
                                        Kelurahan / Desa <span class="text-gray-400 font-normal">(opsional)</span>
                                    </label>
                                    <div class="select-wrap">
                                        <select id="sel-village" name="receiver_village" disabled
                                            class="input-field appearance-none pr-10 disabled:bg-gray-50 disabled:text-gray-400">
                                            <option value="">-- Pilih kecamatan dulu --</option>
                                        </select>
                                        <span id="spin-village" class="select-spinner hidden"></span>
                                        <i class="fa-solid fa-chevron-down select-arrow"></i>
                                    </div>
                                </div>
                            </div>

                            {{-- Alamat + Kode Pos --}}
                            <div class="flex flex-col sm:flex-row gap-4">
                                <div class="flex-1">
                                    <label class="block text-xs font-bold text-gray-600 mb-1.5">
                                        Alamat Lengkap <span class="text-red-500">*</span>
                                    </label>
                                    <textarea name="receiver_address" rows="3" required placeholder="Nama jalan, nomor rumah, RT/RW, patokan..."
                                        class="input-field textarea-clean resize-none @error('receiver_address') border-red-400 @enderror">{{ old('receiver_address') }}</textarea>
                                    @error('receiver_address')
                                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="sm:w-36 flex-shrink-0">
                                    <label class="block text-xs font-bold text-gray-600 mb-1.5 whitespace-nowrap">
                                        Kode Pos <span class="text-gray-400 font-normal">(opsional)</span>
                                    </label>
                                    <input type="text" name="receiver_postal_code"
                                        value="{{ old('receiver_postal_code') }}" placeholder="55801" maxlength="10"
                                        class="input-field">
                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- ONGKIR --}}
                    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm" id="section-ongkir">
                        <h3 class="font-black text-gray-800 mb-1 flex items-center gap-2">
                            <span class="w-7 h-7 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center">
                                <i class="fa-solid fa-truck text-xs"></i>
                            </span>
                            Pilih Jasa Pengiriman
                        </h3>
                        <p class="text-xs text-gray-500 mb-4 ml-9">
                            <i class="fa-solid fa-location-dot text-primary mr-1"></i>
                            Dikirim dari: <strong>Gunungkidul, DI Yogyakarta</strong>
                        </p>

                        {{-- Panduan --}}
                        <div id="box-guide"
                            class="bg-blue-50 border border-blue-100 rounded-xl p-4 mb-4 flex items-start gap-3">
                            <i class="fa-solid fa-circle-info text-blue-400 mt-0.5 flex-shrink-0"></i>
                            <p class="text-xs text-blue-700 leading-relaxed">
                                Pilih <strong>provinsi</strong> dan <strong>kota/kabupaten</strong> tujuan,
                                kemudian klik tombol <strong>"Cek Ongkos Kirim"</strong>.
                            </p>
                        </div>

                        {{-- Tombol Cek --}}
                        <div id="box-trigger" class="hidden mb-4">
                            <button type="button" onclick="cekOngkir()"
                                class="w-full flex items-center justify-center gap-2 py-4
                               bg-blue-50 border-2 border-dashed border-blue-300 rounded-xl
                               text-blue-700 font-bold text-sm
                               hover:bg-blue-100 hover:border-blue-400 transition">
                                <i class="fa-solid fa-calculator"></i>
                                Cek Ongkos Kirim ke Kota Ini
                            </button>
                        </div>

                        {{-- Loading --}}
                        <div id="box-loading" class="hidden py-12 flex-col items-center gap-3">
                            <div class="w-10 h-10 border-4 border-primary border-t-transparent rounded-full animate-spin">
                            </div>
                            <p class="text-sm text-gray-500 font-semibold">Menghitung ongkos kirim...</p>
                        </div>

                        {{-- Error --}}
                        <div id="box-error" class="hidden bg-red-50 border border-red-200 rounded-xl p-4 mb-4">
                            <p class="text-sm text-red-700 font-semibold flex items-center gap-2">
                                <i class="fa-solid fa-circle-exclamation text-red-500"></i>
                                <span id="txt-error">Gagal memuat ongkir.</span>
                            </p>
                            <button type="button" onclick="cekOngkir()"
                                class="text-xs text-red-600 underline font-semibold mt-1.5 ml-6">
                                Coba lagi →
                            </button>
                        </div>

                        {{-- Hasil: ringkasan info + dropdown pilihan kurir --}}
                        <div id="box-results" class="hidden">
                            <div id="ongkir-info"
                                class="flex items-center justify-between flex-wrap gap-2
                                        bg-gray-50 rounded-xl px-4 py-3 mb-3 text-xs text-gray-600">
                            </div>

                            <div id="ongkir-dropdown"
                                class="border-2 border-gray-100 rounded-2xl overflow-hidden transition-colors">
                                {{-- Tombol utama: menampilkan pilihan terpilih, atau ajakan memilih --}}
                                <button type="button" id="ongkir-toggle" onclick="toggleOngkirList()"
                                    class="w-full flex items-center gap-3 p-3.5 bg-white hover:bg-gray-50
                                           transition text-left">
                                    <div id="ongkir-toggle-icon"
                                        class="w-11 h-11 bg-gray-50 border border-gray-200 rounded-xl flex items-center
                                               justify-center text-xl flex-shrink-0 transition-colors">
                                        <i class="fa-solid fa-truck-fast text-gray-300 text-base"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p id="ongkir-toggle-title" class="font-black text-gray-400 text-sm truncate">
                                            Pilih jasa pengiriman
                                        </p>
                                        <p id="ongkir-toggle-sub" class="text-xs text-gray-400 truncate">
                                            Tap untuk melihat pilihan yang tersedia
                                        </p>
                                    </div>
                                    <div id="ongkir-toggle-price" class="text-right flex-shrink-0"></div>
                                    <i id="ongkir-toggle-chevron"
                                        class="fa-solid fa-chevron-down text-gray-300 text-xs flex-shrink-0 transition-transform duration-200"></i>
                                </button>

                                {{-- Panel daftar pilihan kurir (dropdown) --}}
                                <div id="ongkir-list-panel"
                                    class="hidden border-t border-gray-100 bg-gray-50/60 p-2.5 space-y-2 max-h-80 overflow-y-auto">
                                </div>
                            </div>
                        </div>

                        {{-- Hidden inputs — dikirim ke CheckoutController --}}
                        <input type="hidden" name="courier_code" id="val-code">
                        <input type="hidden" name="courier_name" id="val-name">
                        <input type="hidden" name="courier_service" id="val-service">
                        <input type="hidden" name="shipping_cost" id="val-cost">
                        <input type="hidden" name="shipping_estimate" id="val-estimate">

                        @error('courier_name')
                            <p class="text-xs text-red-500 mt-2">{{ $message }}</p>
                        @enderror
                        @error('shipping_cost')
                            <p class="text-xs text-red-500 mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- CATATAN --}}
                    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                        <h3 class="font-black text-gray-800 mb-1 flex items-center gap-2">
                            <span
                                class="w-7 h-7 bg-yellow-100 text-yellow-600 rounded-lg flex items-center justify-center">
                                <i class="fa-solid fa-note-sticky text-xs"></i>
                            </span>
                            Catatan Pesanan <span class="font-normal text-gray-400 text-sm ml-1">(opsional)</span>
                        </h3>
                        <p class="text-xs text-gray-400 mb-3 ml-9">Contoh: titip tetangga, warna pintu, patokan rumah.
                        </p>
                        <textarea name="notes" id="notes-field" rows="4" maxlength="250"
                            placeholder="Tulis catatan untuk kurir atau penjual di sini..." class="input-field textarea-clean resize-none">{{ old('notes') }}</textarea>
                        <p class="text-[11px] text-gray-300 text-right mt-1">
                            <span id="notes-count">0</span>/250
                        </p>
                    </div>

                </div>

                {{-- ══════════════ KANAN: Ringkasan ══════════════ --}}
                <div>
                    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm sticky top-24">
                        <h3 class="font-black text-gray-800 mb-4">Ringkasan Pesanan</h3>

                        <div class="space-y-3 mb-4 pb-4 border-b border-gray-100 max-h-56 overflow-y-auto">
                            @foreach ($carts as $cart)
                                <div class="flex gap-3 items-center">
                                    <div class="w-12 h-12 rounded-xl overflow-hidden bg-gray-100 flex-shrink-0">
                                        @if ($cart->product->image)
                                            <img src="{{ asset('storage/' . $cart->product->image) }}"
                                                class="w-full h-full object-cover" alt="">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-xl">👕</div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-bold text-gray-800 line-clamp-2 leading-tight">
                                            {{ $cart->product->name }}</p>
                                        <p class="text-xs text-gray-400 mt-0.5">
                                            {{ $cart->productSize->size ?? '-' }} × {{ $cart->quantity }}
                                            · {{ $cart->product->weight ?? 0 }}g
                                        </p>
                                    </div>
                                    <p class="text-xs font-black text-gray-700 flex-shrink-0">
                                        {{ $cart->formatted_subtotal }}</p>
                                </div>
                            @endforeach
                        </div>

                        <div class="space-y-2.5 mb-4">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Subtotal produk</span>
                                <span class="font-bold">Rp{{ number_format($subtotal, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Ongkos kirim</span>
                                <span class="font-bold text-gray-700" id="disp-shipping">— Pilih kurir</span>
                            </div>
                            <div id="row-estimate" class="hidden justify-between text-sm">
                                <span class="text-gray-500">Estimasi tiba</span>
                                <span class="font-semibold text-blue-600" id="disp-estimate">-</span>
                            </div>
                        </div>

                        <div class="flex justify-between items-center py-4 border-t border-b border-gray-100 mb-5">
                            <span class="font-black text-gray-800">Total Bayar</span>
                            <span class="font-black text-primary text-2xl" id="disp-total">
                                Rp{{ number_format($subtotal, 0, ',', '.') }}
                            </span>
                        </div>

                        <div class="bg-purple-50 border border-purple-100 rounded-xl p-4 mb-5 flex gap-2.5">
                            <i class="fa-solid fa-shield-halved text-primary mt-0.5 flex-shrink-0"></i>
                            <div>
                                <p class="text-xs font-bold text-purple-800 mb-0.5">Pembayaran Aman via Transfer</p>
                                <p class="text-xs text-purple-600">Transfer Bank / Upload Bukti Pembayaran</p>
                            </div>
                        </div>

                        <button type="submit" id="btn-submit"
                            class="w-full bg-primary text-white font-black py-4 rounded-2xl
                           hover:bg-primary-dark transition shadow-lg shadow-purple-200
                           text-sm flex items-center justify-center gap-2">
                            <i class="fa-solid fa-lock text-xs"></i>
                            Lanjut ke Pembayaran
                        </button>
                        <a href="{{ route('user.cart') }}"
                            class="block text-center text-sm text-gray-400 hover:text-gray-600 mt-3 transition">
                            ← Kembali ke keranjang
                        </a>
                    </div>
                </div>

            </div>
        </form>
    </div>
@endsection

@push('styles')
    <style>
        .input-field {
            @apply w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:border-primary transition;
            --tw-ring-color: rgb(108 99 255 / 0.2);
        }

        /* Textarea dengan scrollbar tipis & konsisten (tanpa tombol panah bawaan browser) */
        .textarea-clean {
            line-height: 1.5;
            scrollbar-width: thin;
            scrollbar-color: #d1d5db transparent;
        }

        .textarea-clean::-webkit-scrollbar {
            width: 6px;
        }

        .textarea-clean::-webkit-scrollbar-track {
            background: transparent;
        }

        .textarea-clean::-webkit-scrollbar-thumb {
            background-color: #d1d5db;
            border-radius: 999px;
        }

        .textarea-clean::-webkit-scrollbar-button {
            display: none;
            height: 0;
            width: 0;
        }

        .select-wrap {
            position: relative;
        }

        .select-arrow {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 11px;
            color: #9ca3af;
            pointer-events: none;
        }

        .select-spinner {
            position: absolute;
            right: 32px;
            top: 50%;
            transform: translateY(-50%);
            width: 14px;
            height: 14px;
            border: 2px solid #6C63FF;
            border-top-color: transparent;
            border-radius: 50%;
            animation: spin .6s linear infinite;
        }

        @keyframes spin {
            to {
                transform: translateY(-50%) rotate(360deg);
            }
        }

        #box-loading {
            display: none;
        }

        #box-loading.flex {
            display: flex;
        }

        #ongkir-list-panel {
            animation: dropdown-in .18s ease;
        }

        @keyframes dropdown-in {
            from {
                opacity: 0;
                transform: translateY(-4px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        // ═══════════════════════════════════════════════════════
        // KONSTANTA
        // ═══════════════════════════════════════════════════════
        const SUB = {{ $subtotal }};
        const CSRF = document.querySelector('meta[name="csrf-token"]').content;
        const F = new Intl.NumberFormat('id-ID');

        let _province = '',
            _city = '',
            _cityId = '';

        // ═══════════════════════════════════════════════════════
        // FETCH HELPER
        // ═══════════════════════════════════════════════════════
        async function req(url, opts = {}) {
            const res = await fetch(url, {
                ...opts,
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': CSRF,
                    ...(opts.headers || {}),
                },
            });

            if (!res.headers.get('content-type')?.includes('application/json')) {
                throw new Error('Sesi habis atau terjadi error server. Silakan refresh halaman.');
            }

            const json = await res.json();

            if (!res.ok) {
                // Laravel validation errors
                const msg = json.message || json.errors ?
                    Object.values(json.errors || {}).flat().join(' ') :
                    `HTTP ${res.status}`;
                throw new Error(msg);
            }

            return json;
        }

        // ═══════════════════════════════════════════════════════
        // HELPERS SELECT
        // ═══════════════════════════════════════════════════════
        const $ = id => document.getElementById(id);
        const qs = s => document.querySelector(s);

        function selLoad(id, spinId, msg) {
            const s = $(id);
            s.disabled = true;
            s.innerHTML = `<option>${msg}</option>`;
            $(spinId)?.classList.remove('hidden');
        }

        function selDone(id, spinId) {
            $(id).disabled = false;
            $(spinId)?.classList.add('hidden');
        }

        function selReset(id, spinId, msg) {
            const s = $(id);
            s.disabled = true;
            s.innerHTML = `<option value="">${msg}</option>`;
            $(spinId)?.classList.add('hidden');
        }

        function fillSel(id, items, valKey, textKey, idKey = null) {
            const s = $(id);
            (items || []).forEach(i => {
                const o = document.createElement('option');
                o.value = i[valKey];
                if (idKey && i[idKey] !== undefined) o.dataset.id = i[idKey];
                o.textContent = i[textKey];
                s.appendChild(o);
            });
        }

        function show(id, visible) {
            const el = $(id);
            if (!el) return;
            el.classList.toggle('hidden', !visible);
            if (id === 'box-loading') {
                el.classList.toggle('flex', visible);
            }
        }

        // ═══════════════════════════════════════════════════════
        // LOAD PROVINSI
        // ═══════════════════════════════════════════════════════
        async function initProvinces() {
            selLoad('sel-province', 'spin-province', 'Memuat provinsi...');
            try {
                const d = await req('/api/wilayah/provinces');
                $('sel-province').innerHTML = '<option value="">-- Pilih Provinsi --</option>';
                // BinderByte mengembalikan { id, name } atau { id_provinsi, nama }
                // — normalise di sini
                (d.data || []).forEach(p => {
                    const o = document.createElement('option');
                    o.value = p.name || p.nama || '';
                    o.dataset.id = p.id || p.id_provinsi || '';
                    o.textContent = p.name || p.nama || '';
                    $('sel-province').appendChild(o);
                });
            } catch (e) {
                $('sel-province').innerHTML = '<option value="">⚠ Gagal memuat — refresh halaman</option>';
                console.error('Provinsi:', e.message);
            } finally {
                selDone('sel-province', 'spin-province');
            }
        }

        // ═══════════════════════════════════════════════════════
        // PROVINSI → KOTA
        // ═══════════════════════════════════════════════════════
        $('sel-province').addEventListener('change', async function() {
            const o = this.options[this.selectedIndex];
            const id = o.dataset.id || '';
            $('hid-province-id').value = id;
            _province = o.value;
            _city = '';
            _cityId = '';

            selReset('sel-city', 'spin-city', '-- Pilih Kota/Kabupaten --');
            selReset('sel-district', 'spin-district', '-- Pilih kota dulu --');
            selReset('sel-village', 'spin-village', '-- Pilih kecamatan dulu --');
            resetOngkir();

            if (!id) return;

            selLoad('sel-city', 'spin-city', 'Memuat kota...');
            try {
                const d = await req(`/api/wilayah/cities?province_id=${id}`);
                $('sel-city').innerHTML = '<option value="">-- Pilih Kota/Kabupaten --</option>';
                (d.data || []).forEach(c => {
                    const o = document.createElement('option');
                    o.value = c.name || c.nama || '';
                    o.dataset.id = c.id || c.id_kabupaten || '';
                    o.textContent = c.name || c.nama || '';
                    $('sel-city').appendChild(o);
                });
            } catch (e) {
                $('sel-city').innerHTML = '<option value="">⚠ Gagal memuat kota</option>';
            } finally {
                selDone('sel-city', 'spin-city');
            }
        });

        // ═══════════════════════════════════════════════════════
        // KOTA → KECAMATAN
        // ═══════════════════════════════════════════════════════
        $('sel-city').addEventListener('change', async function() {
            const o = this.options[this.selectedIndex];
            const id = o.dataset.id || '';
            $('hid-city-id').value = id;
            _city = o.value;
            _cityId = id;

            selReset('sel-district', 'spin-district', '-- Pilih kota dulu --');
            selReset('sel-village', 'spin-village', '-- Pilih kecamatan dulu --');
            resetOngkir();

            if (!id) {
                _city = '';
                _cityId = '';
                return;
            }

            // Tampilkan tombol cek ongkir
            show('box-guide', false);
            show('box-trigger', true);

            selLoad('sel-district', 'spin-district', 'Memuat kecamatan...');
            try {
                const d = await req(`/api/wilayah/districts?city_id=${id}`);
                $('sel-district').innerHTML = '<option value="">-- Pilih Kecamatan (opsional) --</option>';
                (d.data || []).forEach(kec => {
                    const o = document.createElement('option');
                    o.value = kec.name || kec.nama || '';
                    o.dataset.id = kec.id || kec.id_kecamatan || '';
                    o.textContent = kec.name || kec.nama || '';
                    $('sel-district').appendChild(o);
                });
            } catch (e) {
                $('sel-district').innerHTML = '<option value="">Tidak tersedia</option>';
            } finally {
                selDone('sel-district', 'spin-district');
            }
        });

        // ═══════════════════════════════════════════════════════
        // KECAMATAN → KELURAHAN
        // ═══════════════════════════════════════════════════════
        $('sel-district').addEventListener('change', async function() {
            const o = this.options[this.selectedIndex];
            const id = o.dataset.id || '';
            $('hid-district-id').value = id;

            selReset('sel-village', 'spin-village', '-- Pilih kelurahan/desa --');
            if (!id) return;

            selLoad('sel-village', 'spin-village', 'Memuat kelurahan/desa...');
            try {
                const d = await req(`/api/wilayah/villages?district_id=${id}`);
                $('sel-village').innerHTML = '<option value="">-- Pilih Kelurahan/Desa (opsional) --</option>';
                (d.data || []).forEach(kel => {
                    const o = document.createElement('option');
                    o.value = kel.name || kel.nama || '';
                    o.textContent = kel.name || kel.nama || '';
                    $('sel-village').appendChild(o);
                });
            } catch (e) {
                $('sel-village').innerHTML = '<option value="">Tidak tersedia</option>';
            } finally {
                selDone('sel-village', 'spin-village');
            }
        });

        // ═══════════════════════════════════════════════════════
        // CEK ONGKIR
        // ═══════════════════════════════════════════════════════
        async function cekOngkir() {
            if (!_province || !_city || !_cityId) {
                toast('Pilih provinsi dan kota tujuan terlebih dahulu!', 'error');
                return;
            }

            show('box-trigger', false);
            show('box-loading', true);
            show('box-error', false);
            show('box-results', false);
            $('ongkir-info').innerHTML = '';
            $('ongkir-list-panel').innerHTML = '';
            resetKurir();

            try {
                const d = await req('/api/ongkir/check', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        destination_province: _province,
                        destination_city: _city,
                        destination_city_id: _cityId, // ← kirim ID untuk BinderByte
                    }),
                });

                show('box-loading', false);

                if (!d.success || !d.data?.length) {
                    $('txt-error').textContent = d.message || 'Tidak ada layanan tersedia untuk tujuan ini.';
                    show('box-error', true);
                    show('box-trigger', true);
                    return;
                }

                renderKurir(d.data, d.total_weight, d.destination, d.source);

            } catch (e) {
                show('box-loading', false);
                show('box-trigger', true);
                $('txt-error').textContent = e.message;
                show('box-error', true);
                console.error('[Ongkir]', e.message);
            }
        }

        // ═══════════════════════════════════════════════════════
        // RENDER KURIR
        // Kompatibel dengan respons BinderByte DAN ShippingService lokal
        // ═══════════════════════════════════════════════════════
        function renderKurir(list, weight, dest, source) {
            const info = $('ongkir-info');
            const panel = $('ongkir-list-panel');
            panel.innerHTML = '';

            const sourceLabel = source === 'binderbyte' ?
                '<span class="text-green-600 font-bold">via BinderByte API</span>' :
                '<span class="text-orange-500 font-bold">Estimasi lokal (API tidak tersedia)</span>';

            info.innerHTML = `
            <span>
                <i class="fa-solid fa-truck text-primary mr-1"></i>
                <strong>${list.length} layanan</strong> ke <strong>${dest}</strong>
                · Berat: <strong>${weight}g</strong>
                · ${sourceLabel}
            </span>
            <button type="button" onclick="cekOngkir()"
                class="text-primary font-bold hover:underline flex items-center gap-1">
                <i class="fa-solid fa-rotate-right text-xs"></i> Refresh
            </button>`;

            list.forEach((item, i) => {
                const cost = item.cost || 0;

                // Normalise field — BinderByte & ShippingService lokal pakai nama berbeda
                const courierName = item.courier_name || item.name || '—';
                const courierCode = item.courier_code || item.code || '';
                const courierIcon = item.courier_icon || courierEmoji(courierCode);
                const service = item.service || item.code || '—';
                const serviceName = item.service_name || item.description || service;
                const estimate = item.estimate || item.etd || '-';

                const label = document.createElement('label');
                label.className = 'kurir-card flex items-center gap-3 p-3 bg-white border-2 border-gray-100 ' +
                    'rounded-xl cursor-pointer hover:border-primary hover:bg-purple-50 ' +
                    'transition-all duration-200 group select-none';

                label.dataset.cost = cost;
                label.dataset.name = courierName;
                label.dataset.service = service;
                label.dataset.code = courierCode;
                label.dataset.estimate = estimate;
                label.dataset.icon = courierIcon;

                label.innerHTML = `
            <input type="radio" class="sr-only" name="_kr" value="${i}" onchange="pilihKurir(this)">
            <div class="w-10 h-10 bg-gray-50 border border-gray-200 rounded-lg flex items-center
                        justify-center text-lg flex-shrink-0 group-hover:border-purple-200 transition">
                ${courierIcon}
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-0.5 flex-wrap">
                    <span class="font-black text-gray-800 text-sm">${courierName}</span>
                    <span class="text-[10px] bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full font-bold">
                        ${service}
                    </span>
                </div>
                <p class="text-xs text-gray-400 mb-0.5 truncate">${serviceName}</p>
                <p class="text-[11px] text-blue-600 font-semibold flex items-center gap-1">
                    <i class="fa-regular fa-clock"></i>
                    Estimasi ${estimate} hari kerja
                </p>
            </div>
            <div class="text-right flex-shrink-0">
                <p class="font-black text-primary text-sm">Rp${F.format(cost)}</p>
            </div>
            <div class="kurir-dot w-5 h-5 rounded-full border-2 border-gray-300 flex-shrink-0
                        flex items-center justify-center transition-all"></div>`;

                panel.appendChild(label);
            });

            show('box-results', true);
            // Belum ada pilihan → buka dropdown otomatis agar user langsung memilih
            openOngkirList();
        }

        // ═══════════════════════════════════════════════════════
        // DROPDOWN ONGKIR — buka / tutup daftar pilihan kurir
        // ═══════════════════════════════════════════════════════
        function openOngkirList() {
            $('ongkir-list-panel').classList.remove('hidden');
            $('ongkir-toggle-chevron').style.transform = 'rotate(180deg)';
        }

        function closeOngkirList() {
            $('ongkir-list-panel').classList.add('hidden');
            $('ongkir-toggle-chevron').style.transform = 'rotate(0deg)';
        }

        function toggleOngkirList() {
            const panel = $('ongkir-list-panel');
            panel.classList.contains('hidden') ? openOngkirList() : closeOngkirList();
        }

        // Emoji fallback saat courier_icon tidak ada (respons BinderByte)
        function courierEmoji(code) {
            const map = {
                jne: '🟡',
                jnt: '🔴',
                sicepat: '🟢',
                anteraja: '🟠',
                pos: '🔵',
                tiki: '🟣',
                ninja: '⚫',
                lion: '🦁',
                ide: '🟤',
                sap: '📦',
            };
            return map[String(code).toLowerCase()] || '📦';
        }

        // ═══════════════════════════════════════════════════════
        // PILIH KURIR
        // ═══════════════════════════════════════════════════════
        function pilihKurir(radio) {
            const card = radio.closest('label');

            document.querySelectorAll('.kurir-card').forEach(c => {
                c.classList.remove('border-primary', 'bg-purple-50', 'shadow-md');
                c.classList.add('border-gray-100');
                const dot = c.querySelector('.kurir-dot');
                if (dot) {
                    dot.classList.remove('bg-primary', 'border-primary');
                    dot.innerHTML = '';
                }
            });

            card.classList.add('border-primary', 'bg-purple-50', 'shadow-md');
            card.classList.remove('border-gray-100');
            const dot = card.querySelector('.kurir-dot');
            if (dot) {
                dot.classList.add('bg-primary', 'border-primary');
                dot.innerHTML = '<i class="fa-solid fa-check text-white text-[8px]"></i>';
            }

            const cost = parseInt(card.dataset.cost) || 0;
            const est = card.dataset.estimate || '-';
            const name = card.dataset.name;
            const service = card.dataset.service;
            const icon = card.dataset.icon;

            $('val-code').value = card.dataset.code;
            $('val-name').value = name;
            $('val-service').value = service;
            $('val-cost').value = cost;
            $('val-estimate').value = est;

            $('disp-shipping').textContent = 'Rp' + F.format(cost);
            $('disp-total').textContent = 'Rp' + F.format(SUB + cost);
            $('disp-estimate').textContent = `Est. ${est} hari`;
            $('row-estimate').classList.remove('hidden');
            $('row-estimate').style.display = 'flex';

            // Perbarui tombol dropdown agar menampilkan 1 pilihan yang dipilih
            $('ongkir-toggle-icon').innerHTML = icon;
            $('ongkir-toggle-icon').classList.add('border-purple-200', 'bg-purple-50');
            $('ongkir-toggle-title').innerHTML =
                `<span class="text-gray-800">${name}</span>
             <span class="text-[10px] bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded-full font-bold ml-1">${service}</span>`;
            $('ongkir-toggle-title').classList.remove('text-gray-400');
            $('ongkir-toggle-sub').textContent = `Estimasi ${est} hari kerja · Tap untuk ganti`;
            $('ongkir-toggle-price').innerHTML =
                `<p class="font-black text-primary text-sm">Rp${F.format(cost)}</p>`;

            // Tutup dropdown setelah satu pilihan ditentukan
            closeOngkirList();
        }

        // ═══════════════════════════════════════════════════════
        // RESET
        // ═══════════════════════════════════════════════════════
        function resetOngkir() {
            show('box-guide', true);
            show('box-trigger', false);
            show('box-loading', false);
            show('box-error', false);
            show('box-results', false);
            $('ongkir-info').innerHTML = '';
            $('ongkir-list-panel').innerHTML = '';
            closeOngkirList();
            resetKurir();
        }

        function resetKurir() {
            ['val-code', 'val-name', 'val-service', 'val-cost', 'val-estimate']
            .forEach(id => {
                const e = $(id);
                if (e) e.value = '';
            });
            $('disp-shipping').textContent = '— Pilih kurir';
            $('disp-total').textContent = 'Rp' + F.format(SUB);
            $('row-estimate').classList.add('hidden');

            // Kembalikan tombol dropdown ke kondisi belum memilih
            const icon = $('ongkir-toggle-icon');
            if (icon) {
                icon.innerHTML = '<i class="fa-solid fa-truck-fast text-gray-300 text-base"></i>';
                icon.classList.remove('border-purple-200', 'bg-purple-50');
            }
            const title = $('ongkir-toggle-title');
            if (title) {
                title.textContent = 'Pilih jasa pengiriman';
                title.classList.add('text-gray-400');
            }
            const sub = $('ongkir-toggle-sub');
            if (sub) sub.textContent = 'Tap untuk melihat pilihan yang tersedia';
            const price = $('ongkir-toggle-price');
            if (price) price.innerHTML = '';
        }

        // ═══════════════════════════════════════════════════════
        // VALIDASI FORM
        // ═══════════════════════════════════════════════════════
        $('form-checkout').addEventListener('submit', function(e) {
            const checks = [
                [!qs('[name="receiver_name"]').value?.trim(), 'Nama penerima wajib diisi!',
                    '[name="receiver_name"]'
                ],
                [!qs('[name="receiver_phone"]').value?.trim(), 'Nomor HP wajib diisi!',
                    '[name="receiver_phone"]'
                ],
                [!_province, 'Pilih provinsi tujuan!', '#sel-province'],
                [!_city, 'Pilih kota/kabupaten tujuan!', '#sel-city'],
                [!qs('[name="receiver_address"]').value?.trim(), 'Alamat lengkap wajib diisi!',
                    '[name="receiver_address"]'
                ],
                [!$('val-code').value, 'Pilih jasa pengiriman terlebih dahulu!', '#section-ongkir'],
            ];

            for (const [cond, msg, focusSel] of checks) {
                if (cond) {
                    e.preventDefault();
                    toast(msg, 'error');
                    const el = document.querySelector(focusSel);
                    el?.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                    setTimeout(() => el?.focus(), 400);
                    return;
                }
            }

            const btn = $('btn-submit');
            btn.disabled = true;
            btn.innerHTML =
                `<div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div> Memproses...`;
        });

        // ═══════════════════════════════════════════════════════
        // TOAST
        // ═══════════════════════════════════════════════════════
        function toast(msg, type = 'info') {
            const colors = {
                error: 'bg-red-50 border-red-300 text-red-800',
                success: 'bg-green-50 border-green-300 text-green-800',
                info: 'bg-blue-50 border-blue-300 text-blue-800'
            };
            const icons = {
                error: 'fa-circle-exclamation text-red-500',
                success: 'fa-circle-check text-green-500',
                info: 'fa-circle-info text-blue-500'
            };
            const t = document.createElement('div');
            t.className =
                `fixed top-5 right-5 z-[9999] flex items-center gap-3 px-5 py-3.5 border rounded-2xl shadow-xl text-sm font-semibold ${colors[type]} transition-all duration-300`;
            t.innerHTML = `<i class="fa-solid ${icons[type]} flex-shrink-0"></i><span>${msg}</span>
            <button onclick="this.parentElement.remove()" class="ml-2 opacity-60 hover:opacity-100 transition">
                <i class="fa-solid fa-xmark text-xs"></i></button>`;
            document.body.appendChild(t);
            setTimeout(() => {
                t.style.opacity = '0';
                setTimeout(() => t.remove(), 300);
            }, 4000);
        }

        // ═══════════════════════════════════════════════════════
        // CATATAN — hitung karakter
        // ═══════════════════════════════════════════════════════
        function initNotesCounter() {
            const field = $('notes-field');
            const count = $('notes-count');
            if (!field || !count) return;
            const update = () => count.textContent = field.value.length;
            field.addEventListener('input', update);
            update();
        }

        // ═══════════════════════════════════════════════════════
        // INIT
        // ═══════════════════════════════════════════════════════
        document.addEventListener('DOMContentLoaded', () => {
            initProvinces();
            initNotesCounter();
        });
    </script>
@endpush
