@extends('layouts.admin')
@section('title', 'Detail Pesanan')

@section('content')

    <div class="mb-6">
        <a href="{{ route('admin.orders.index') }}" class="text-sm text-gray-400 hover:text-primary transition">
            ← Kembali ke daftar pesanan
        </a>
    </div>

    @php
        $statusColors = [
            'pending' => 'yellow',
            'waiting_confirmation' => 'orange',
            'confirmed' => 'blue',
            'processing' => 'purple',
            'shipped' => 'indigo',
            'delivered' => 'green',
            'cancelled' => 'red',
        ];
        $statusLabels = [
            'pending' => 'Menunggu Bayar',
            'waiting_confirmation' => 'Menunggu Konfirmasi',
            'confirmed' => 'Sudah Dibayar',
            'processing' => 'Diproses',
            'shipped' => 'Dikirim',
            'delivered' => 'Selesai',
            'cancelled' => 'Dibatalkan',
        ];
        $c = $statusColors[$order->status] ?? 'gray';
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- KIRI: Detail Pesanan --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Header Status --}}
            <div class="bg-white rounded-2xl p-6 border border-gray-100">
                <div class="flex items-start justify-between flex-wrap gap-4 mb-6">
                    <div>
                        <p class="text-xs text-gray-400 mb-1">Kode Pesanan</p>
                        <h2 class="text-2xl font-black text-gray-800">{{ $order->order_code }}</h2>
                        <p class="text-xs text-gray-400 mt-1">{{ $order->created_at->isoFormat('dddd, D MMMM Y · HH:mm') }}
                            WIB</p>
                    </div>
                    <span
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-bold
                    bg-{{ $c }}-100 text-{{ $c }}-700">
                        <span class="w-2 h-2 rounded-full bg-{{ $c }}-500"></span>
                        {{ $statusLabels[$order->status] ?? $order->status }}
                    </span>
                </div>

                {{-- Progress Bar --}}
                @php
                    $steps = ['pending' => 0, 'confirmed' => 1, 'processing' => 2, 'shipped' => 3, 'delivered' => 4];
                    $current = $steps[$order->status] ?? 0;
                    $stepLabels = ['Menunggu Bayar', 'Dibayar', 'Diproses', 'Dikirim', 'Selesai'];
                    $stepIcons = ['fa-clock', 'fa-money-bill', 'fa-gear', 'fa-truck', 'fa-circle-check'];
                @endphp
                @if ($order->status !== 'cancelled')
                    <div class="relative flex items-start justify-between mt-2">
                        {{-- Garis penghubung --}}
                        <div class="absolute top-5 left-5 right-5 h-0.5 bg-gray-100"></div>
                        <div class="absolute top-5 left-5 h-0.5 bg-primary transition-all duration-500"
                            style="width: calc({{ $current }} / 4 * (100% - 2.5rem))"></div>

                        @foreach ($stepLabels as $i => $lbl)
                            <div class="relative flex flex-col items-center" style="width:20%">
                                <div
                                    class="w-10 h-10 rounded-full flex items-center justify-center text-xs font-bold z-10 border-2
                        {{ $i <= $current ? 'bg-primary border-primary text-white' : 'bg-white border-gray-200 text-gray-400' }}">
                                    <i class="fa-solid {{ $stepIcons[$i] }}"></i>
                                </div>
                                <p
                                    class="text-xs text-center mt-2 leading-tight {{ $i <= $current ? 'text-primary font-bold' : 'text-gray-400' }}">
                                    {{ $lbl }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-red-50 rounded-xl px-4 py-3 flex items-center gap-3 mt-2">
                        <i class="fa-solid fa-circle-xmark text-red-500"></i>
                        <p class="text-sm font-semibold text-red-700">Pesanan ini telah dibatalkan.</p>
                    </div>
                @endif
            </div>

            {{-- Produk --}}
            <div class="bg-white rounded-2xl p-6 border border-gray-100">
                <h3 class="font-black text-gray-800 mb-5">Produk Dipesan</h3>
                <div class="space-y-4">
                    @foreach ($order->items as $item)
                        <div class="flex gap-4 items-center pb-4 border-b border-gray-50 last:border-0 last:pb-0">
                            <div class="w-16 h-16 bg-gray-100 rounded-xl overflow-hidden flex-shrink-0">
                                @if ($item->product_image)
                                    <img src="{{ asset('storage/' . $item->product_image) }}"
                                        class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-2xl">👕</div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <p class="font-bold text-gray-800 text-sm">{{ $item->product_name }}</p>
                                <div class="flex items-center gap-3 mt-1 text-xs text-gray-400">
                                    <span
                                        class="bg-gray-100 px-2 py-0.5 rounded font-semibold text-gray-600">{{ $item->product_size }}</span>
                                    <span>{{ $item->quantity }} pcs</span>
                                    <span>@ Rp{{ number_format($item->price, 0, ',', '.') }}</span>
                                </div>
                            </div>
                            <p class="font-black text-gray-800 text-sm flex-shrink-0">
                                Rp{{ number_format($item->subtotal, 0, ',', '.') }}
                            </p>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 pt-4 border-t border-gray-100 flex justify-between">
                    <span class="text-sm text-gray-500">Subtotal produk</span>
                    <span class="font-black text-gray-800">{{ $order->formatted_subtotal }}</span>
                </div>
                <div class="flex justify-between mt-2">
                    <span class="text-sm text-gray-500">Ongkos kirim ({{ $order->courier_name }})</span>
                    <span class="font-bold text-gray-700">Rp{{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between mt-3 pt-3 border-t border-gray-100">
                    <span class="font-black text-gray-800">Total Bayar</span>
                    <span class="font-black text-primary text-xl">{{ $order->formatted_total }}</span>
                </div>
            </div>

            {{-- Bukti Pembayaran --}}
            @if ($order->payment_proof)
                <div class="bg-white rounded-2xl p-6 border border-gray-100">
                    <h3 class="font-black text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-image text-blue-500"></i> Bukti Pembayaran
                    </h3>
                    <div class="flex gap-6 flex-wrap items-start">
                        <img src="{{ asset('storage/' . $order->payment_proof) }}"
                            class="max-w-xs w-full rounded-2xl border border-gray-200 cursor-pointer hover:opacity-90 transition"
                            onclick="window.open(this.src,'_blank')">
                        <div>
                            @if ($order->paid_at)
                                <p class="text-xs text-gray-400 mb-1">Dikirim pada</p>
                                <p class="font-bold text-gray-800 text-sm">
                                    {{ $order->paid_at->isoFormat('D MMM Y, HH:mm') }} WIB</p>
                            @endif
                            @if (in_array($order->status, ['pending', 'waiting_confirmation']))
                                <form action="{{ route('admin.orders.confirm', $order) }}" method="POST" class="mt-4">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                        class="inline-flex items-center gap-2 bg-green-500 text-white font-bold px-5 py-2.5 rounded-xl hover:bg-green-600 transition text-sm shadow-lg shadow-green-200">
                                        <i class="fa-solid fa-check-circle"></i> Konfirmasi Pembayaran
                                    </button>
                                </form>
                            @endif
                            <p class="text-xs text-gray-400 mt-3">Klik gambar untuk memperbesar</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- KANAN: Update Status + Info --}}
        <div class="space-y-5">

            {{-- Generate Resi Otomatis (Biteship) --}}
            @if (!$order->tracking_number && in_array($order->status, ['confirmed', 'processing']))
                <div class="bg-white rounded-2xl p-6 border border-gray-100">
                    <h3 class="font-black text-gray-800 mb-1 flex items-center gap-2">
                        <span
                            class="w-7 h-7 bg-orange-100 text-orange-600 rounded-lg flex items-center justify-center text-xs">
                            <i class="fa-solid fa-truck-fast"></i>
                        </span>
                        Buat Resi Otomatis
                    </h3>
                    <p class="text-xs text-gray-400 mb-4">Generate AWB via Biteship — nomor resi terisi otomatis,
                        tanpa ketik manual.</p>
                    <button type="button" onclick="openShipmentModal()"
                        class="w-full bg-orange-500 text-white font-bold py-3 rounded-xl hover:bg-orange-600 transition text-sm shadow-lg shadow-orange-200">
                        <i class="fa-solid fa-bolt mr-1"></i> Buat Pengiriman
                    </button>
                </div>
            @endif

            {{-- Update Status --}}
            <div class="bg-white rounded-2xl p-6 border border-gray-100">
                <h3 class="font-black text-gray-800 mb-4 flex items-center gap-2">
                    <span class="w-7 h-7 bg-purple-100 text-primary rounded-lg flex items-center justify-center text-xs">
                        <i class="fa-solid fa-rotate"></i>
                    </span>
                    Update Status
                </h3>
                <form action="{{ route('admin.orders.status', $order) }}" method="POST">
                    @csrf @method('PATCH')
                    <div class="space-y-2 mb-4">
                        @foreach ($statuses as $status)
                            @php $sc = $statusColors[$status] ?? 'gray'; @endphp
                            <label
                                class="flex items-center gap-3 p-3 rounded-xl cursor-pointer border-2 transition
                                  {{ $order->status === $status
                                      ? 'border-' . $sc . '-400 bg-' . $sc . '-50'
                                      : 'border-gray-100 hover:border-gray-200' }}">
                                <input type="radio" name="status" value="{{ $status }}" class="sr-only"
                                    onchange="toggleTrackingField(this)" {{ $order->status === $status ? 'checked' : '' }}>
                                <span class="w-3 h-3 rounded-full bg-{{ $sc }}-400 flex-shrink-0"></span>
                                <span
                                    class="text-sm font-semibold {{ $order->status === $status ? 'text-' . $sc . '-800' : 'text-gray-600' }}">
                                    {{ $statusLabels[$status] ?? $status }}
                                </span>
                                @if ($order->status === $status)
                                    <i class="fa-solid fa-circle-check text-{{ $sc }}-500 ml-auto text-xs"></i>
                                @endif
                            </label>
                        @endforeach
                    </div>

                    {{-- No. Resi — wajib diisi saat status "Dikirim" --}}
                    <div id="tracking-field" class="mb-4 {{ $order->status === 'shipped' ? '' : 'hidden' }}">
                        <label class="block text-xs font-bold text-gray-500 mb-1.5">
                            Nomor Resi <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="tracking_number"
                            value="{{ old('tracking_number', $order->tracking_number) }}"
                            placeholder="Contoh: JX1234567890"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm font-mono focus:outline-none focus:border-primary transition">
                        <p class="text-[11px] text-gray-400 mt-1">
                            Kurir: {{ $order->courier_name }} {{ $order->courier_service }}
                        </p>
                        @error('tracking_number')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit"
                        class="w-full bg-primary text-white font-bold py-3 rounded-xl hover:bg-primary-dark transition text-sm shadow-lg shadow-purple-200">
                        <i class="fa-solid fa-save mr-1"></i> Simpan Status
                    </button>
                </form>
            </div>

            <script>
                function toggleTrackingField(radio) {
                    const field = document.getElementById('tracking-field');
                    if (radio.value === 'shipped') {
                        field.classList.remove('hidden');
                    } else {
                        field.classList.add('hidden');
                    }
                }
            </script>

            {{-- Info Pelanggan --}}
            <div class="bg-white rounded-2xl p-6 border border-gray-100">
                <h3 class="font-black text-gray-800 mb-4 flex items-center gap-2">
                    <span class="w-7 h-7 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center text-xs">
                        <i class="fa-solid fa-user"></i>
                    </span>
                    Info Pelanggan
                </h3>
                <div class="flex items-center gap-3 mb-4 pb-4 border-b border-gray-100">
                    <div
                        class="w-10 h-10 bg-primary rounded-full flex items-center justify-center text-white font-black text-sm">
                        {{ strtoupper(substr($order->user->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-bold text-gray-800 text-sm">{{ $order->user->name }}</p>
                        <p class="text-xs text-gray-400">{{ $order->user->email }}</p>
                    </div>
                </div>
                <div class="space-y-2 text-sm">
                    <div>
                        <p class="text-xs text-gray-400">Penerima</p>
                        <p class="font-bold text-gray-800">{{ $order->receiver_name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">No. HP</p>
                        <p class="font-bold text-gray-800">{{ $order->receiver_phone }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Alamat</p>
                        <p class="font-semibold text-gray-700 text-xs leading-relaxed">
                            {{ $order->receiver_address }},<br>
                            {{ $order->receiver_city }}, {{ $order->receiver_province }}
                            {{ $order->receiver_postal_code }}
                        </p>
                    </div>
                    <div class="pt-2 border-t border-gray-100">
                        <p class="text-xs text-gray-400">Kurir</p>
                        <p class="font-bold text-gray-800">{{ $order->courier_name }} — {{ $order->courier_service }}</p>
                        <p class="text-xs text-gray-400">Est. {{ $order->shipping_estimate }} hari kerja</p>
                    </div>
                    @if ($order->notes)
                        <div class="pt-2 border-t border-gray-100">
                            <p class="text-xs text-gray-400">Catatan</p>
                            <p class="text-sm text-gray-600 italic">"{{ $order->notes }}"</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ── Modal Generate Resi Otomatis (Biteship) ─────────────────── --}}
    @if (!$order->tracking_number && in_array($order->status, ['confirmed', 'processing']))
        <div id="shipment-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full max-h-[85vh] overflow-y-auto p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-black text-gray-800">Buat Pengiriman Otomatis</h3>
                    <button type="button" onclick="closeShipmentModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div id="shipment-step-area">
                    <p class="text-sm text-gray-500 mb-3">Mencari area tujuan berdasarkan alamat pesanan ini
                        (<strong>{{ $order->receiver_city }}, {{ $order->receiver_district }}</strong>)…</p>
                    <div id="area-loading" class="text-center text-gray-400 text-sm py-6">
                        <i class="fa-solid fa-spinner fa-spin mr-1"></i> Mencari area di Biteship…
                    </div>
                    <div id="area-list" class="space-y-2"></div>
                    <p id="area-error" class="hidden text-sm text-red-500 mt-2"></p>
                </div>

                <div id="shipment-step-rates" class="hidden">
                    <p class="text-sm text-gray-500 mb-3">Pilih kurir untuk area terpilih:</p>
                    <div id="rates-loading" class="hidden text-center text-gray-400 text-sm py-6">
                        <i class="fa-solid fa-spinner fa-spin mr-1"></i> Mengambil daftar kurir…
                    </div>
                    <div id="rates-list" class="space-y-2"></div>
                    <p id="rates-error" class="hidden text-sm text-red-500 mt-2"></p>
                    <button type="button" onclick="backToAreaStep()"
                        class="text-xs text-gray-400 hover:text-primary mt-3">
                        ← Ganti area tujuan
                    </button>
                </div>

                <form id="generate-shipment-form" action="{{ route('admin.orders.shipping.generate', $order) }}"
                    method="POST" class="hidden mt-4">
                    @csrf
                    <input type="hidden" name="area_id" id="input-area-id">
                    <input type="hidden" name="courier_code" id="input-courier-code">
                    <input type="hidden" name="courier_service" id="input-courier-service">
                    <div id="confirm-summary" class="bg-gray-50 rounded-xl p-4 text-sm mb-4"></div>
                    <button type="submit"
                        class="w-full bg-orange-500 text-white font-bold py-3 rounded-xl hover:bg-orange-600 transition text-sm">
                        <i class="fa-solid fa-check mr-1"></i> Konfirmasi & Buat Resi
                    </button>
                </form>
            </div>
        </div>

        <script>
            const CSRF = document.querySelector('meta[name="csrf-token"]').content;
            const RESOLVE_AREA_URL = @json(route('admin.orders.shipping.resolve-area', $order));
            const RATES_URL = @json(route('admin.orders.shipping.rates', $order));
            const KNOWN_AREA_ID = @json($order->biteship_area_id);
            const KNOWN_AREA_NAME = @json(trim(($order->receiver_district ? $order->receiver_district . ', ' : '') . $order->receiver_city));

            function openShipmentModal() {
                document.getElementById('shipment-modal').classList.remove('hidden');
                document.getElementById('generate-shipment-form').classList.add('hidden');

                // Order dari checkout baru sudah tahu area_id-nya sendiri (dipilih customer
                // langsung dari search Biteship) — jadi langsung lompat ke pilih kurir,
                // tidak perlu tebak-tebak area lagi seperti order lama.
                if (KNOWN_AREA_ID) {
                    document.getElementById('shipment-step-area').classList.add('hidden');
                    document.getElementById('shipment-step-rates').classList.remove('hidden');
                    selectArea(KNOWN_AREA_ID, KNOWN_AREA_NAME);
                } else {
                    document.getElementById('shipment-step-area').classList.remove('hidden');
                    document.getElementById('shipment-step-rates').classList.add('hidden');
                    fetchAreas();
                }
            }

            function closeShipmentModal() {
                document.getElementById('shipment-modal').classList.add('hidden');
            }

            function backToAreaStep() {
                document.getElementById('shipment-step-rates').classList.add('hidden');
                document.getElementById('generate-shipment-form').classList.add('hidden');
                document.getElementById('shipment-step-area').classList.remove('hidden');
                fetchAreas();
            }

            async function fetchAreas() {
                const loading = document.getElementById('area-loading');
                const list = document.getElementById('area-list');
                const errorEl = document.getElementById('area-error');
                loading.classList.remove('hidden');
                list.innerHTML = '';
                errorEl.classList.add('hidden');

                try {
                    const res = await fetch(RESOLVE_AREA_URL, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': CSRF,
                            'Accept': 'application/json'
                        },
                    });
                    const data = await res.json();
                    loading.classList.add('hidden');

                    if (!data.success || !data.areas || data.areas.length === 0) {
                        errorEl.textContent = data.message || 'Area tidak ditemukan.';
                        errorEl.classList.remove('hidden');
                        return;
                    }

                    // Kalau cuma 1 hasil yang cocok, langsung lanjut ke pilih kurir
                    if (data.areas.length === 1) {
                        selectArea(data.areas[0].id, data.areas[0].name);
                        return;
                    }

                    data.areas.forEach(area => {
                        const btn = document.createElement('button');
                        btn.type = 'button';
                        btn.className =
                            'w-full text-left px-4 py-3 rounded-xl border border-gray-200 hover:border-primary text-sm transition';
                        btn.innerHTML = `<span class="font-semibold text-gray-800">${area.name}</span>`;
                        btn.onclick = () => selectArea(area.id, area.name);
                        list.appendChild(btn);
                    });
                } catch (e) {
                    loading.classList.add('hidden');
                    errorEl.textContent = 'Gagal menghubungi server: ' + e.message;
                    errorEl.classList.remove('hidden');
                }
            }

            let selectedAreaName = '';

            async function selectArea(areaId, areaName) {
                selectedAreaName = areaName;
                document.getElementById('shipment-step-area').classList.add('hidden');
                document.getElementById('shipment-step-rates').classList.remove('hidden');

                const loading = document.getElementById('rates-loading');
                const list = document.getElementById('rates-list');
                const errorEl = document.getElementById('rates-error');
                loading.classList.remove('hidden');
                list.innerHTML = '';
                errorEl.classList.add('hidden');

                try {
                    const res = await fetch(RATES_URL, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': CSRF,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            area_id: areaId
                        }),
                    });
                    const data = await res.json();
                    loading.classList.add('hidden');

                    if (!data.success || !data.rates || data.rates.length === 0) {
                        errorEl.textContent = data.message || 'Tidak ada kurir tersedia untuk area ini.';
                        errorEl.classList.remove('hidden');
                        return;
                    }

                    data.rates.forEach(rate => {
                        const btn = document.createElement('button');
                        btn.type = 'button';
                        btn.className =
                            'w-full flex items-center justify-between px-4 py-3 rounded-xl border border-gray-200 hover:border-primary text-sm transition text-left';
                        btn.innerHTML = `
                            <span>
                                <span class="font-bold text-gray-800">${rate.courier_name} — ${rate.service}</span><br>
                                <span class="text-xs text-gray-400">Estimasi ${rate.estimate} hari</span>
                            </span>
                            <span class="font-black text-primary">Rp${Number(rate.cost).toLocaleString('id-ID')}</span>
                        `;
                        btn.onclick = () => confirmShipment(areaId, rate);
                        list.appendChild(btn);
                    });
                } catch (e) {
                    loading.classList.add('hidden');
                    errorEl.textContent = 'Gagal menghubungi server: ' + e.message;
                    errorEl.classList.remove('hidden');
                }
            }

            function confirmShipment(areaId, rate) {
                document.getElementById('shipment-step-rates').classList.add('hidden');
                document.getElementById('input-area-id').value = areaId;
                document.getElementById('input-courier-code').value = rate.courier_code;
                document.getElementById('input-courier-service').value = rate.service;
                document.getElementById('confirm-summary').innerHTML = `
                    <p><span class="text-gray-400">Tujuan:</span> <strong>${selectedAreaName}</strong></p>
                    <p><span class="text-gray-400">Kurir:</span> <strong>${rate.courier_name} — ${rate.service}</strong></p>
                    <p><span class="text-gray-400">Ongkir:</span> <strong>Rp${Number(rate.cost).toLocaleString('id-ID')}</strong></p>
                `;
                document.getElementById('generate-shipment-form').classList.remove('hidden');
            }
        </script>
    @endif

@endsection
