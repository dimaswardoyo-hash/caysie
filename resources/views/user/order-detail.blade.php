@extends('layouts.app')
@section('title', 'Detail Pesanan')

@section('content')

    <div class="flex flex-wrap gap-3 mb-6">
        <a href="{{ route('user.orders') }}" class="text-sm text-gray-400 hover:text-primary transition">
            ← Kembali ke pesanan saya
        </a>
    </div>

    {{-- Aksi di halaman detail --}}
    <div class="flex flex-wrap gap-3 mb-6">
        @if ($order->can_pay && !$order->payment_proof)
            <button onclick="document.getElementById('section-upload').scrollIntoView({behavior:'smooth'})"
                class="inline-flex items-center gap-2 bg-primary text-white font-bold px-5 py-2.5 rounded-xl hover:bg-primary-dark transition text-sm shadow-lg shadow-purple-200">
                <i class="fa-solid fa-upload"></i> Upload Bukti Bayar
            </button>
        @endif

        @if ($order->can_cancel)
            <button onclick="openCancelModal('{{ $order->id }}', '{{ $order->order_code }}')"
                class="inline-flex items-center gap-2 bg-red-50 border border-red-200 text-red-600 font-bold px-5 py-2.5 rounded-xl hover:bg-red-100 transition text-sm">
                <i class="fa-solid fa-xmark"></i> Batalkan Pesanan
            </button>
        @endif

        @if (in_array($order->status, ['delivered', 'cancelled']))
            <form action="{{ route('user.orders.reorder', $order) }}" method="POST">
                @csrf
                <button type="submit"
                    class="inline-flex items-center gap-2 bg-purple-50 border border-purple-200 text-purple-700 font-bold px-5 py-2.5 rounded-xl hover:bg-purple-100 transition text-sm">
                    <i class="fa-solid fa-rotate-right"></i> Beli Lagi
                </button>
            </form>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Kiri --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Status Header --}}
            <div class="bg-white rounded-2xl p-6 border border-gray-100">
                <div class="flex items-start justify-between flex-wrap gap-4">
                    <div>
                        <p class="text-xs text-gray-400 mb-1">Kode Pesanan</p>
                        <p class="text-xl font-black text-gray-800">{{ $order->order_code }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ $order->created_at->isoFormat('dddd, D MMMM Y · HH:mm') }}
                            WIB</p>
                    </div>
                    <span
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-bold
                    bg-{{ $order->status_color }}-100 text-{{ $order->status_color }}-700">
                        <span class="w-2 h-2 rounded-full bg-{{ $order->status_color }}-500"></span>
                        {{ $order->status_label }}
                    </span>
                </div>

                {{-- Progress --}}
                @php
                    $steps = ['pending' => 0, 'confirmed' => 1, 'processing' => 2, 'shipped' => 3, 'delivered' => 4];
                    $current = $steps[$order->status] ?? 0;
                    $labels = ['Menunggu Bayar', 'Dibayar', 'Diproses', 'Dikirim', 'Selesai'];
                    $icons = ['fa-clock', 'fa-money-bill', 'fa-gear', 'fa-truck', 'fa-circle-check'];
                @endphp
                @if ($order->status !== 'cancelled')
                    <div class="mt-6">
                        <div class="flex items-center justify-between">
                            @foreach ($labels as $i => $label)
                                <div
                                    class="flex flex-col items-center flex-1 {{ $i < count($labels) - 1 ? 'relative' : '' }}">
                                    <div
                                        class="w-9 h-9 rounded-full flex items-center justify-center text-xs font-bold z-10
                            {{ $i <= $current ? 'bg-primary text-white' : 'bg-gray-100 text-gray-400' }}">
                                        <i class="fa-solid {{ $icons[$i] }}"></i>
                                    </div>
                                    @if ($i < count($labels) - 1)
                                        <div
                                            class="absolute top-4 left-1/2 w-full h-0.5 {{ $i < $current ? 'bg-primary' : 'bg-gray-100' }} -z-0">
                                        </div>
                                    @endif
                                    <p
                                        class="text-xs text-center mt-2 {{ $i <= $current ? 'text-primary font-bold' : 'text-gray-400' }} hidden sm:block">
                                        {{ $label }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Produk --}}
            <div class="bg-white rounded-2xl p-6 border border-gray-100">
                <h3 class="font-black text-gray-800 mb-4">Produk Dipesan</h3>
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
                                <p class="text-xs text-gray-400 mt-0.5">Ukuran {{ $item->product_size }} ·
                                    {{ $item->quantity }} pcs</p>
                                <p class="text-xs text-gray-500 mt-0.5">Rp{{ number_format($item->price, 0, ',', '.') }} /
                                    pcs
                                </p>
                            </div>
                            <p class="font-black text-gray-800 text-sm flex-shrink-0">
                                Rp{{ number_format($item->subtotal, 0, ',', '.') }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Upload Bukti Bayar — di halaman detail --}}
            @if ($order->status === 'pending')
                <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-6" id="section-upload">
                    <h3 class="font-black text-yellow-800 mb-2 flex items-center gap-2">
                        <i class="fa-solid fa-money-bill-wave text-yellow-600"></i>
                        Selesaikan Pembayaran
                    </h3>

                    {{-- Countdown --}}
                    @if ($order->payment_deadline && !$order->isPaymentExpired())
                        <div class="flex items-center gap-3 mb-4">
                            <p class="text-sm text-yellow-700">Bayar sebelum:</p>
                            <span
                                class="bg-white border border-yellow-300 px-3 py-1 rounded-xl font-black text-yellow-700 font-mono tracking-widest"
                                data-countdown="{{ $order->payment_seconds_left }}" id="countdown-{{ $order->id }}">
                                {{ gmdate('H:i:s', $order->payment_seconds_left) }}
                            </span>
                            <span class="text-xs text-yellow-600">{{ $order->payment_deadline_label }}</span>
                        </div>
                    @elseif($order->isPaymentExpired())
                        <div class="bg-red-50 rounded-xl p-3 mb-4 text-xs text-red-700 font-bold">
                            ⏰ Waktu pembayaran sudah habis.
                        </div>
                    @endif

                    <div class="bg-white rounded-xl p-4 text-sm mb-4">
                        <p class="font-bold text-gray-700 mb-2">Informasi Transfer:</p>
                        <table class="text-xs w-full">
                            <tr>
                                <td class="text-gray-400 py-1 w-28">Bank</td>
                                <td class="font-bold">BCA</td>
                            </tr>
                            <tr>
                                <td class="text-gray-400 py-1">No. Rekening</td>
                                <td class="font-bold text-primary">1234567890</td>
                            </tr>
                            <tr>
                                <td class="text-gray-400 py-1">Atas Nama</td>
                                <td class="font-bold">Caysie Store</td>
                            </tr>
                            <tr>
                                <td class="text-gray-400 py-1">Jumlah Transfer</td>
                                <td class="font-black text-primary">Rp{{ number_format($order->total, 0, ',', '.') }}</td>
                            </tr>
                        </table>
                    </div>

                    @if (!$order->payment_proof && !$order->isPaymentExpired())
                        <form action="{{ route('user.orders.proof', $order) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="flex gap-3 flex-wrap">
                                <input type="file" name="payment_proof" accept="image/*" required
                                    class="flex-1 text-xs text-gray-600 file:mr-3 file:py-2.5 file:px-5 file:rounded-xl file:border-0 file:bg-yellow-200 file:text-yellow-800 file:font-bold hover:file:bg-yellow-300 transition">
                                <button type="submit"
                                    class="px-5 py-2.5 bg-yellow-500 text-white font-bold rounded-xl hover:bg-yellow-600 transition text-sm">
                                    <i class="fa-solid fa-paper-plane mr-1"></i> Kirim Bukti
                                </button>
                            </div>
                            @error('payment_proof')
                                <p class="text-xs text-red-500 mt-2">{{ $message }}</p>
                            @enderror
                        </form>
                    @elseif($order->payment_proof)
                        <div class="flex items-center gap-3 text-sm text-green-700 font-semibold">
                            <i class="fa-solid fa-circle-check text-green-500 text-lg"></i>
                            Bukti pembayaran sudah dikirim, menunggu konfirmasi admin.
                        </div>
                    @endif
                </div>
            @endif
        </div>

        {{-- Kanan --}}
        <div class="space-y-4">

            {{-- Ringkasan Harga --}}
            <div class="bg-white rounded-2xl p-6 border border-gray-100">
                <h3 class="font-black text-gray-800 mb-4">Rincian Biaya</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Subtotal produk</span>
                        <span class="font-bold">Rp{{ number_format($order->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Ongkos kirim</span>
                        <span class="font-bold">Rp{{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                    </div>
                </div>
                <div class="flex justify-between mt-4 pt-4 border-t border-gray-100">
                    <span class="font-black text-gray-800">Total Bayar</span>
                    <span class="font-black text-primary text-lg">Rp{{ number_format($order->total, 0, ',', '.') }}</span>
                </div>
            </div>

            {{-- Info Pengiriman --}}
            <div class="bg-white rounded-2xl p-6 border border-gray-100">
                <h3 class="font-black text-gray-800 mb-4">Info Pengiriman</h3>
                <div class="space-y-3 text-sm">
                    <div>
                        <p class="text-gray-400 text-xs mb-1">Kurir</p>
                        <p class="font-bold text-gray-800">{{ $order->courier_name }} — {{ $order->courier_service }}</p>
                        <p class="text-xs text-gray-400">Estimasi {{ $order->shipping_estimate }} hari kerja</p>

                        @if ($order->tracking_number)
                            <div class="flex items-center justify-between gap-3 mt-3 bg-gray-50 rounded-xl px-3.5 py-2.5">
                                <div class="min-w-0">
                                    <p class="text-[11px] text-gray-400">No. Resi</p>
                                    <p class="font-mono font-bold text-gray-700 text-xs truncate">
                                        {{ $order->tracking_number }}</p>
                                </div>
                                <button onclick="openTrackingModal()"
                                    class="flex-shrink-0 inline-flex items-center gap-1.5 bg-primary text-white text-xs font-bold px-3.5 py-2 rounded-lg hover:bg-primary-dark transition">
                                    <i class="fa-solid fa-truck-fast text-xs"></i> Lacak Paket
                                </button>
                            </div>
                        @endif
                    </div>
                    <div class="pt-3 border-t border-gray-100">
                        <p class="text-gray-400 text-xs mb-1">Alamat Penerima</p>
                        <p class="font-bold text-gray-800">{{ $order->receiver_name }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $order->receiver_phone }}</p>
                        <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                            {{ $order->receiver_address }},<br>
                            {{ $order->receiver_city }}, {{ $order->receiver_province }}
                            {{ $order->receiver_postal_code }}
                        </p>
                    </div>
                </div>
            </div>

            @if ($order->notes)
                <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100">
                    <p class="text-xs font-bold text-gray-500 mb-1">Catatan</p>
                    <p class="text-sm text-gray-600">{{ $order->notes }}</p>
                </div>
            @endif
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        {{-- Countdown (sama seperti di orders.blade.php) --}}
        document.querySelectorAll('[data-countdown]').forEach(el => {
            let secs = parseInt(el.dataset.countdown);
            if (secs <= 0) return;
            const interval = setInterval(() => {
                secs--;
                if (secs <= 0) {
                    clearInterval(interval);
                    el.textContent = '00:00:00';
                    return;
                }
                const h = Math.floor(secs / 3600),
                    m = Math.floor((secs % 3600) / 60),
                    s = secs % 60;
                el.textContent =
                    `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
                if (secs < 3600) el.classList.add('text-red-600');
            }, 1000);
        });

        function openCancelModal(orderId, orderCode) {
            document.getElementById('form-cancel').action = `/user/orders/${orderId}/cancel`;
            document.getElementById('cancel-order-code').textContent = orderCode;
            document.getElementById('modal-cancel').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeCancelModal() {
            document.getElementById('modal-cancel').classList.add('hidden');
            document.body.style.overflow = '';
        }

        function toggleOtherReason(radio) {
            const other = document.getElementById('other-reason');
            if (radio.value === 'Lainnya') {
                other.classList.remove('hidden');
                other.required = true;
                other.name = 'cancel_reason';
                radio.name = '_cancel_reason_radio';
            } else {
                other.classList.add('hidden');
                other.required = false;
                other.name = 'cancel_reason_other';
                radio.name = 'cancel_reason';
            }
        }
        document.getElementById('modal-cancel')?.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
                document.body.style.overflow = '';
            }
        });

        // ── Modal Lacak Paket ─────────────────────────────────
        const TRACKING_URL = '{{ $order->tracking_number ? route('api.tracking.order', $order) : '' }}';
        let trackingLoaded = false;

        function openTrackingModal() {
            if (!TRACKING_URL) return;
            document.getElementById('modal-tracking').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            if (!trackingLoaded) fetchTracking();
        }

        function closeTrackingModal() {
            document.getElementById('modal-tracking').classList.add('hidden');
            document.body.style.overflow = '';
        }

        document.getElementById('modal-tracking')?.addEventListener('click', function(e) {
            if (e.target === this) closeTrackingModal();
        });

        async function fetchTracking() {
            const loadingEl = document.getElementById('tracking-loading');
            const errorEl = document.getElementById('tracking-error');
            const contentEl = document.getElementById('tracking-content');

            loadingEl.classList.remove('hidden');
            errorEl.classList.add('hidden');
            contentEl.classList.add('hidden');

            try {
                const res = await fetch(TRACKING_URL, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const json = await res.json();

                loadingEl.classList.add('hidden');

                if (!res.ok || !json.success) {
                    showTrackingError(json?.data?.error || json?.message ||
                        'Belum ada update dari kurir untuk nomor resi ini.');
                    return;
                }

                renderTracking(json.data || {});
                trackingLoaded = true;
                contentEl.classList.remove('hidden');
            } catch (e) {
                loadingEl.classList.add('hidden');
                showTrackingError('Koneksi gagal. Periksa internet kamu lalu coba lagi.');
            }
        }

        function showTrackingError(message) {
            document.getElementById('tracking-error-message').textContent = message;
            document.getElementById('tracking-error').classList.remove('hidden');
        }

        function trackingStatusMeta(status) {
            const s = (status || '').toString().toUpperCase();
            if (s.includes('DELIVER') || s.includes('TERKIRIM') || s.includes('SELESAI')) {
                return {
                    color: 'green',
                    icon: 'fa-circle-check',
                    label: 'Sudah Diterima'
                };
            }
            if (s.includes('RETUR') || s.includes('GAGAL') || s.includes('CANCEL') || s.includes('BATAL')) {
                return {
                    color: 'red',
                    icon: 'fa-circle-xmark',
                    label: 'Ada Masalah Pengiriman'
                };
            }
            if (s.includes('TRANSIT') || s.includes('ANTAR') || s.includes('KIRIM') || s.includes('PROSES')) {
                return {
                    color: 'blue',
                    icon: 'fa-truck-fast',
                    label: 'Dalam Perjalanan'
                };
            }
            return {
                color: 'yellow',
                icon: 'fa-box',
                label: status || 'Diproses'
            };
        }

        function renderTracking(data) {
            const summary = data.summary || {};
            const history = Array.isArray(data.history) ? data.history : [];
            const meta = trackingStatusMeta(summary.status);

            document.getElementById('tracking-summary').innerHTML = `
                <div class="flex items-center gap-3">
                    <span class="w-11 h-11 rounded-xl bg-${meta.color}-100 text-${meta.color}-600 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid ${meta.icon}"></i>
                    </span>
                    <div class="min-w-0">
                        <p class="font-black text-gray-800 text-sm">${meta.label}</p>
                        <p class="text-xs text-gray-400 truncate">${escapeHtml(summary.courier || '')}${summary.courier ? ' · ' : ''}No. Resi ${escapeHtml(summary.awb || '-')}</p>
                    </div>
                </div>
            `;

            const timelineEl = document.getElementById('tracking-timeline');

            if (history.length === 0) {
                timelineEl.innerHTML =
                    '<p class="text-xs text-gray-400 text-center py-6">Belum ada riwayat perjalanan dari kurir.</p>';
                return;
            }

            timelineEl.innerHTML = history.map((h, i) => `
                <div class="flex gap-3">
                    <div class="flex flex-col items-center flex-shrink-0 pt-1">
                        <span class="w-2.5 h-2.5 rounded-full ${i === 0 ? 'bg-primary' : 'bg-gray-300'} flex-shrink-0"></span>
                        ${i < history.length - 1 ? '<span class="w-px flex-1 bg-gray-200 my-1"></span>' : ''}
                    </div>
                    <div class="pb-4 min-w-0 flex-1">
                        <p class="text-xs leading-relaxed ${i === 0 ? 'font-bold text-gray-800' : 'text-gray-500'}">${escapeHtml(h.desc || '-')}</p>
                        <p class="text-[11px] text-gray-400 mt-1">${h.location ? escapeHtml(h.location) + ' · ' : ''}${formatTrackDate(h.date)}</p>
                    </div>
                </div>
            `).join('');
        }

        function escapeHtml(str) {
            const div = document.createElement('div');
            div.textContent = str == null ? '' : String(str);
            return div.innerHTML;
        }

        function formatTrackDate(dateStr) {
            if (!dateStr) return '';
            const d = new Date(String(dateStr).replace(' ', 'T'));
            if (isNaN(d.getTime())) return escapeHtml(dateStr);
            return d.toLocaleString('id-ID', {
                day: 'numeric',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }
    </script>

    {{-- Modal Cancel --}}
    <div id="modal-cancel" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4"
        style="background:rgba(0,0,0,0.5)">
        <div class="bg-white rounded-3xl p-6 w-full max-w-md shadow-2xl">
            <div class="flex items-center justify-between mb-5">
                <h3 class="font-black text-gray-800 text-lg">Batalkan Pesanan</h3>
                <button onclick="closeCancelModal()"
                    class="w-8 h-8 bg-gray-100 rounded-xl flex items-center justify-center hover:bg-gray-200 transition text-gray-500">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <form id="form-cancel" method="POST">
                @csrf

                <div class="bg-red-50 border border-red-100 rounded-2xl p-4 mb-5 flex gap-3">
                    <i class="fa-solid fa-triangle-exclamation text-red-500 text-lg flex-shrink-0 mt-0.5"></i>
                    <div>
                        <p class="font-bold text-red-800 text-sm mb-1">Yakin ingin membatalkan?</p>
                        <p class="text-xs text-red-600 leading-relaxed">
                            Pesanan <strong id="cancel-order-code"></strong> akan dibatalkan dan stok produk dikembalikan.
                            Tindakan ini tidak dapat diurungkan.
                        </p>
                    </div>
                </div>

                <div class="mb-5">
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Alasan Pembatalan <span class="text-red-500">*</span>
                    </label>
                    <div class="space-y-2 mb-3">
                        @foreach (['Saya ingin mengubah produk/ukuran', 'Saya menemukan harga lebih murah', 'Pesanan dibuat tidak sengaja', 'Ingin mengubah alamat pengiriman', 'Lainnya'] as $reason)
                            <label
                                class="flex items-center gap-3 p-3 border border-gray-100 rounded-xl cursor-pointer hover:border-primary hover:bg-purple-50 transition">
                                <input type="radio" name="cancel_reason" value="{{ $reason }}"
                                    class="accent-primary" onchange="toggleOtherReason(this)">
                                <span class="text-sm text-gray-700">{{ $reason }}</span>
                            </label>
                        @endforeach
                    </div>
                    <textarea id="other-reason" name="cancel_reason_other" rows="2" placeholder="Tuliskan alasan pembatalan..."
                        class="hidden w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-primary transition resize-none"></textarea>
                    @error('cancel_reason')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex gap-3">
                    <button type="button" onclick="closeCancelModal()"
                        class="flex-1 py-3 bg-gray-100 text-gray-600 rounded-xl font-bold text-sm hover:bg-gray-200 transition">
                        Tidak, Kembali
                    </button>
                    <button type="submit" id="btn-cancel-submit"
                        class="flex-1 py-3 bg-red-500 text-white rounded-xl font-bold text-sm hover:bg-red-600 transition shadow-lg shadow-red-200 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-xmark"></i> Ya, Batalkan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Lacak Paket --}}
    <div id="modal-tracking" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4"
        style="background:rgba(0,0,0,0.5)">
        <div class="bg-white rounded-3xl w-full max-w-md shadow-2xl max-h-[85vh] flex flex-col overflow-hidden">

            {{-- Header --}}
            <div class="flex items-center justify-between px-6 pt-6 pb-4 flex-shrink-0 border-b border-gray-100">
                <div>
                    <h3 class="font-black text-gray-800 text-lg">Lacak Paket</h3>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $order->order_code }}</p>
                </div>
                <button onclick="closeTrackingModal()"
                    class="w-8 h-8 bg-gray-100 rounded-xl flex items-center justify-center hover:bg-gray-200 transition text-gray-500 flex-shrink-0">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            {{-- Body (scrollable) --}}
            <div class="px-6 py-5 overflow-y-auto flex-1">

                {{-- Loading --}}
                <div id="tracking-loading" class="py-12 text-center">
                    <div class="w-8 h-8 border-3 border-primary border-t-transparent rounded-full animate-spin mx-auto mb-3"
                        style="border-width:3px"></div>
                    <p class="text-xs text-gray-400">Mengambil data pelacakan...</p>
                </div>

                {{-- Error --}}
                <div id="tracking-error" class="hidden py-8 text-center">
                    <div class="w-12 h-12 rounded-full bg-red-50 flex items-center justify-center mx-auto mb-3">
                        <i class="fa-solid fa-triangle-exclamation text-red-400"></i>
                    </div>
                    <p id="tracking-error-message" class="text-sm text-gray-500 px-4 leading-relaxed"></p>
                </div>

                {{-- Content --}}
                <div id="tracking-content" class="hidden">
                    {{-- Ringkasan status --}}
                    <div id="tracking-summary" class="bg-gray-50 rounded-2xl p-4 mb-5"></div>

                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Riwayat Perjalanan</p>
                    <div id="tracking-timeline"></div>
                </div>
            </div>
        </div>
    </div>
@endpush
