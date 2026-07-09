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
                <div class="lg:col-span-2 space-y-6">

                    {{-- IDENTITAS --}}
                    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                        <h3 class="font-black text-gray-800 mb-5 flex items-center gap-2">
                            <span class="w-8 h-8 bg-purple-100 text-primary rounded-lg flex items-center justify-center">
                                <i class="fa-solid fa-user text-sm"></i>
                            </span>
                            Identitas Penerima
                        </h3>
                        <div class="space-y-4">

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

                            {{-- Provinsi --}}
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

                            {{-- Kota --}}
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

                            {{-- Kecamatan --}}
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

                            {{-- Kelurahan --}}
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

                            {{-- Alamat --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1.5">
                                    Alamat Lengkap <span class="text-red-500">*</span>
                                </label>
                                <textarea name="receiver_address" rows="3" required placeholder="Nama jalan, nomor rumah, RT/RW, patokan..."
                                    class="input-field resize-none @error('receiver_address') border-red-400 @enderror">{{ old('receiver_address') }}</textarea>
                                @error('receiver_address')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Kode Pos --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1.5">
                                    Kode Pos <span class="text-gray-400 font-normal">(opsional)</span>
                                </label>
                                <input type="text" name="receiver_postal_code" value="{{ old('receiver_postal_code') }}"
                                    placeholder="Contoh: 55801" maxlength="10" class="input-field">
                            </div>

                        </div>
                    </div>

                    {{-- ONGKIR --}}
                    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm" id="section-ongkir">
                        <h3 class="font-black text-gray-800 mb-1 flex items-center gap-2">
                            <span class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center">
                                <i class="fa-solid fa-truck text-sm"></i>
                            </span>
                            Pilih Jasa Pengiriman
                        </h3>
                        <p class="text-xs text-gray-500 mb-3 ml-10">
                            <i class="fa-solid fa-location-dot text-primary mr-1"></i>
                            Dikirim dari: <strong>Gunungkidul, DI Yogyakarta</strong>
                        </p>

                        {{-- Peta rute pengiriman --}}
                        <div id="map-shipping" class="mb-5 border border-gray-100"></div>

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

                        {{-- Hasil --}}
                        <div id="box-results" class="hidden space-y-2"></div>

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

                    {{-- TRACKING RESI (opsional) --}}
                    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                        <h3 class="font-black text-gray-800 mb-4 flex items-center gap-2">
                            <span class="w-8 h-8 bg-green-100 text-green-600 rounded-lg flex items-center justify-center">
                                <i class="fa-solid fa-magnifying-glass text-sm"></i>
                            </span>
                            Cek Resi Pengiriman
                            <span class="font-normal text-gray-400 text-sm ml-1">(pesanan sebelumnya)</span>
                        </h3>
                        <div class="flex gap-2">
                            <select id="trk-courier" class="input-field w-40 flex-shrink-0">
                                <option value="jne">JNE</option>
                                <option value="jnt">J&T</option>
                                <option value="sicepat">SiCepat</option>
                                <option value="anteraja">Anteraja</option>
                                <option value="pos">POS</option>
                                <option value="tiki">TIKI</option>
                                <option value="ninja">Ninja</option>
                                <option value="lion">Lion</option>
                                <option value="ide">ID Express</option>
                                <option value="sap">SAP</option>
                            </select>
                            <input type="text" id="trk-awb" placeholder="Masukkan nomor resi..."
                                class="input-field flex-1">
                            <button type="button" onclick="cekResi()"
                                class="px-5 py-3 bg-green-600 text-white text-sm font-bold rounded-xl
                                       hover:bg-green-700 transition flex-shrink-0">
                                <i class="fa-solid fa-search mr-1"></i> Lacak
                            </button>
                        </div>
                        <div id="trk-loading" class="hidden mt-3 text-sm text-gray-500 flex items-center gap-2">
                            <div class="w-4 h-4 border-2 border-green-500 border-t-transparent rounded-full animate-spin">
                            </div>
                            Melacak paket...
                        </div>
                        <div id="trk-result" class="hidden mt-3"></div>
                    </div>

                    {{-- CATATAN --}}
                    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                        <h3 class="font-black text-gray-800 mb-4 flex items-center gap-2">
                            <span
                                class="w-8 h-8 bg-yellow-100 text-yellow-600 rounded-lg flex items-center justify-center">
                                <i class="fa-solid fa-note-sticky text-sm"></i>
                            </span>
                            Catatan <span class="font-normal text-gray-400 text-sm ml-1">(opsional)</span>
                        </h3>
                        <textarea name="notes" rows="2" placeholder="Titip tetangga, warna pintu, patokan rumah, dll."
                            class="input-field resize-none">{{ old('notes') }}</textarea>
                    </div>

                </div>

                {{-- ══════════════ KANAN: Ringkasan ══════════════ --}}
                <div>
                    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm sticky top-24">
                        <h3 class="font-black text-gray-800 mb-5">Ringkasan Pesanan</h3>

                        <div class="space-y-3 mb-5 pb-4 border-b border-gray-100 max-h-56 overflow-y-auto">
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css" />
    <style>
        #map-shipping {
            height: 220px;
            width: 100%;
            border-radius: 0.75rem;
            z-index: 0;
        }

        .leaflet-popup-content {
            font-family: 'Inter', sans-serif;
            font-size: 12px;
            font-weight: 600;
        }

        .input-field {
            @apply w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:border-primary transition;
            --tw-ring-color: rgb(108 99 255 / 0.2);
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
    </style>
@endpush

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.js"></script>
    <script>
        // ═══════════════════════════════════════════════════════
        // PETA RUTE PENGIRIMAN (Leaflet + OpenStreetMap)
        // ═══════════════════════════════════════════════════════
        // Koordinat toko: Wonosari, Gunungkidul, DI Yogyakarta
        const ORIGIN_COORD = [-7.9666, 110.6081];

        // Koordinat perkiraan per provinsi (ibu kota/centroid) — dipakai untuk
        // menggambar rute karena API RajaOngkir/BinderByte tidak menyediakan lat/long.
        const PROVINCE_COORDS = {
            'ACEH': [5.5483, 95.3238],
            'SUMATERA UTARA': [3.5952, 98.6722],
            'SUMATERA BARAT': [-0.9471, 100.4172],
            'RIAU': [0.5071, 101.4478],
            'KEPULAUAN RIAU': [0.9186, 104.4658],
            'JAMBI': [-1.6101, 103.6131],
            'SUMATERA SELATAN': [-2.9909, 104.7566],
            'BENGKULU': [-3.7928, 102.2608],
            'LAMPUNG': [-5.4292, 105.2610],
            'KEPULAUAN BANGKA BELITUNG': [-2.1316, 106.1169],
            'DKI JAKARTA': [-6.2088, 106.8456],
            'JAWA BARAT': [-6.9175, 107.6191],
            'JAWA TENGAH': [-6.9667, 110.4167],
            'DAERAH ISTIMEWA YOGYAKARTA': [-7.7956, 110.3695],
            'JAWA TIMUR': [-7.2504, 112.7688],
            'BANTEN': [-6.1783, 106.6319],
            'BALI': [-8.6705, 115.2126],
            'NUSA TENGGARA BARAT': [-8.5833, 116.1167],
            'NUSA TENGGARA TIMUR': [-10.1772, 123.6070],
            'KALIMANTAN BARAT': [-0.0263, 109.3425],
            'KALIMANTAN TENGAH': [-1.6815, 113.3823],
            'KALIMANTAN SELATAN': [-3.3194, 114.5908],
            'KALIMANTAN TIMUR': [-0.5022, 117.1536],
            'KALIMANTAN UTARA': [3.0731, 116.0414],
            'SULAWESI UTARA': [1.4748, 124.8421],
            'SULAWESI TENGAH': [-0.8917, 119.8707],
            'SULAWESI SELATAN': [-5.1477, 119.4327],
            'SULAWESI TENGGARA': [-3.9985, 122.5150],
            'GORONTALO': [0.5435, 123.0568],
            'SULAWESI BARAT': [-2.8441, 119.2321],
            'MALUKU': [-3.6954, 128.1814],
            'MALUKU UTARA': [0.7833, 127.3833],
            'PAPUA': [-2.5333, 140.7167],
            'PAPUA BARAT': [-0.8615, 134.0620],
            'PAPUA TENGAH': [-3.3667, 136.1667],
            'PAPUA PEGUNUNGAN': [-4.0833, 138.9667],
            'PAPUA SELATAN': [-8.4667, 140.3833],
            'PAPUA BARAT DAYA': [-0.8667, 131.2500],
        };

        function findProvinceCoord(name) {
            const key = (name || '').toUpperCase().trim();
            if (PROVINCE_COORDS[key]) return PROVINCE_COORDS[key];
            // Partial match — antisipasi variasi nama dari API (mis. "JAWA TIMUR" vs "JATIM")
            for (const k in PROVINCE_COORDS) {
                if (key.includes(k) || k.includes(key)) return PROVINCE_COORDS[k];
            }
            return null;
        }

        let _map, _originMarker, _destMarker, _routeLine;

        function initShippingMap() {
            _map = L.map('map-shipping', {
                zoomControl: true,
                scrollWheelZoom: false,
            }).setView(ORIGIN_COORD, 8);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors',
                maxZoom: 18,
            }).addTo(_map);

            _originMarker = L.marker(ORIGIN_COORD)
                .addTo(_map)
                .bindPopup('<b>Toko Caysie</b><br>Gunungkidul, DI Yogyakarta')
                .openPopup();
        }

        function updateShippingMap(provinceName, cityName) {
            const coord = findProvinceCoord(provinceName);
            if (!_map || !coord) return;

            const label = cityName ? `${cityName}, ${provinceName}` : provinceName;

            if (_destMarker) _map.removeLayer(_destMarker);
            if (_routeLine) _map.removeLayer(_routeLine);

            _destMarker = L.marker(coord)
                .addTo(_map)
                .bindPopup(`<b>Tujuan</b><br>${label}`)
                .openPopup();

            _routeLine = L.polyline([ORIGIN_COORD, coord], {
                color: '#6C63FF',
                weight: 3,
                dashArray: '6, 6',
            }).addTo(_map);

            _map.fitBounds(L.latLngBounds([ORIGIN_COORD, coord]), {
                padding: [30, 30],
            });
        }

        function clearShippingMapDestination() {
            if (_destMarker) {
                _map?.removeLayer(_destMarker);
                _destMarker = null;
            }
            if (_routeLine) {
                _map?.removeLayer(_routeLine);
                _routeLine = null;
            }
            if (_map) _map.setView(ORIGIN_COORD, 8);
        }

        document.addEventListener('DOMContentLoaded', initShippingMap);
    </script>
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

            if (!id) {
                clearShippingMapDestination();
                return;
            }

            updateShippingMap(_province, null);

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
                updateShippingMap(_province, null);
                return;
            }

            updateShippingMap(_province, _city);

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
            $('box-results').innerHTML = '';
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
            const box = $('box-results');

            const sourceLabel = source === 'binderbyte' ?
                '<span class="text-green-600 font-bold">via BinderByte API</span>' :
                '<span class="text-orange-500 font-bold">Estimasi lokal (API tidak tersedia)</span>';

            box.innerHTML = `
        <div class="flex items-center justify-between flex-wrap gap-2 bg-gray-50
                    rounded-xl px-4 py-3 mb-3 text-xs text-gray-600">
            <span>
                <i class="fa-solid fa-truck text-primary mr-1"></i>
                <strong>${list.length} layanan</strong> ke <strong>${dest}</strong>
                · Berat: <strong>${weight}g</strong>
                · ${sourceLabel}
            </span>
            <button type="button" onclick="cekOngkir()"
                class="text-primary font-bold hover:underline flex items-center gap-1">
                <i class="fa-solid fa-rotate-right text-xs"></i> Refresh
            </button>
        </div>`;

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
                label.className = 'kurir-card flex items-center gap-3 p-4 border-2 border-gray-100 ' +
                    'rounded-2xl cursor-pointer hover:border-primary hover:bg-purple-50 ' +
                    'transition-all duration-200 group mb-2 select-none';

                label.dataset.cost = cost;
                label.dataset.name = courierName;
                label.dataset.service = service;
                label.dataset.code = courierCode;
                label.dataset.estimate = estimate;

                label.innerHTML = `
            <input type="radio" class="sr-only" name="_kr" value="${i}" onchange="pilihKurir(this)">
            <div class="w-12 h-12 bg-gray-50 border border-gray-200 rounded-xl flex items-center
                        justify-center text-2xl flex-shrink-0 group-hover:border-purple-200 transition">
                ${courierIcon}
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-0.5 flex-wrap">
                    <span class="font-black text-gray-800 text-sm">${courierName}</span>
                    <span class="text-[10px] bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full font-bold">
                        ${service}
                    </span>
                </div>
                <p class="text-xs text-gray-400 mb-1">${serviceName}</p>
                <p class="text-xs text-blue-600 font-semibold flex items-center gap-1">
                    <i class="fa-regular fa-clock"></i>
                    Estimasi ${estimate} hari kerja
                </p>
            </div>
            <div class="text-right flex-shrink-0">
                <p class="font-black text-primary text-base">Rp${F.format(cost)}</p>
                <p class="text-[10px] text-gray-400 mt-1">Total: Rp${F.format(SUB + cost)}</p>
            </div>
            <div class="kurir-dot w-5 h-5 rounded-full border-2 border-gray-300 flex-shrink-0
                        flex items-center justify-center transition-all"></div>`;

                box.appendChild(label);
            });

            show('box-results', true);
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

            $('val-code').value = card.dataset.code;
            $('val-name').value = card.dataset.name;
            $('val-service').value = card.dataset.service;
            $('val-cost').value = cost;
            $('val-estimate').value = est;

            $('disp-shipping').textContent = 'Rp' + F.format(cost);
            $('disp-total').textContent = 'Rp' + F.format(SUB + cost);
            $('disp-estimate').textContent = `Est. ${est} hari`;
            $('row-estimate').classList.remove('hidden');
            $('row-estimate').style.display = 'flex';
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
            $('box-results').innerHTML = '';
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
        }

        // ═══════════════════════════════════════════════════════
        // CEK RESI
        // ═══════════════════════════════════════════════════════
        async function cekResi() {
            const courier = $('trk-courier').value;
            const awb = $('trk-awb').value.trim();

            if (!awb) {
                toast('Masukkan nomor resi terlebih dahulu!', 'error');
                return;
            }

            $('trk-loading').classList.remove('hidden');
            $('trk-result').classList.add('hidden');
            $('trk-result').innerHTML = '';

            try {
                const d = await req('/api/tracking/track', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        courier,
                        awb
                    }),
                });

                $('trk-loading').classList.add('hidden');

                if (!d.success) {
                    $('trk-result').innerHTML =
                        `<div class="bg-red-50 border border-red-200 rounded-xl p-4 text-sm text-red-700">
                        <i class="fa-solid fa-circle-exclamation mr-2"></i>${d.message || 'Resi tidak ditemukan.'}
                    </div>`;
                    $('trk-result').classList.remove('hidden');
                    return;
                }

                const td = d.data;
                const history = (td.history || td.manifest || []);

                let histHTML = history.length ?
                    history.map(h => `
                    <div class="flex gap-3 text-xs py-2 border-b border-gray-50 last:border-0">
                        <div class="text-gray-400 flex-shrink-0 w-32">${h.date || h.tanggal || ''} ${h.time || h.jam || ''}</div>
                        <div class="text-gray-700">${h.description || h.keterangan || h.desc || ''}</div>
                    </div>`).join('') :
                    '<p class="text-xs text-gray-400">Belum ada riwayat tracking.</p>';

                $('trk-result').innerHTML = `
                <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                    <div class="flex items-center gap-3 mb-3">
                        <i class="fa-solid fa-box text-green-600"></i>
                        <div>
                            <p class="text-xs font-bold text-gray-700">Resi: <span class="text-green-700">${awb}</span></p>
                            <p class="text-xs text-gray-500">Status: <strong>${td.status || td.delivered || '-'}</strong></p>
                        </div>
                    </div>
                    <div class="space-y-1 max-h-48 overflow-y-auto">${histHTML}</div>
                </div>`;
                $('trk-result').classList.remove('hidden');

            } catch (e) {
                $('trk-loading').classList.add('hidden');
                $('trk-result').innerHTML =
                    `<div class="bg-red-50 border border-red-200 rounded-xl p-4 text-sm text-red-700">
                    <i class="fa-solid fa-circle-exclamation mr-2"></i>${e.message}
                </div>`;
                $('trk-result').classList.remove('hidden');
            }
        }

        // Enter key untuk tracking
        $('trk-awb').addEventListener('keydown', e => {
            if (e.key === 'Enter') {
                e.preventDefault();
                cekResi();
            }
        });

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
        // INIT
        // ═══════════════════════════════════════════════════════
        document.addEventListener('DOMContentLoaded', initProvinces);
    </script>
@endpush
