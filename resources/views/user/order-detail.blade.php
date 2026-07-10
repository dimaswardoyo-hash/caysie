@extends('layouts.app')
@section('title', 'Detail Pesanan')

@section('content')

    <section class="max-w-7xl mx-auto px-6 py-8">
        <div class="flex justify-between mb-6">
            <a href="{{ route('user.orders') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-full text-sm font-semibold
        bg-white border border-gray-200 text-gray-600
        hover:bg-primary hover:text-white hover:border-primary
        hover:shadow-lg hover:shadow-primary/25 hover:scale-[1.03]
        transition-all duration-300 group">

                <i class="fa-solid fa-arrow-left text-xs transition-transform duration-300 group-hover:-translate-x-1"></i>
                Kembali ke pesanan saya
            </a>
        </div>

        {{-- Aksi di halaman detail --}}
        <div class="flex flex-wrap gap-3 mb-6">
            @if ($order->can_pay && !$order->payment_proof)
                <button onclick="document.getElementById('section-upload').scrollIntoView({behavior:'smooth'})"
                    class="inline-flex items-center gap-2 bg-gradient-to-r from-primary to-primary-dark text-white font-bold px-5 py-2.5 rounded-full hover:opacity-90 transition-all duration-200 text-sm shadow-lg shadow-primary/25">
                    <i class="fa-solid fa-upload"></i> Upload Bukti Bayar
                </button>
            @endif

            @if ($order->can_cancel)
                <button onclick="openCancelModal('{{ $order->id }}', '{{ $order->order_code }}')"
                    class="inline-flex items-center gap-2 bg-red-50 border border-red-200 text-red-600 font-bold px-5 py-2.5 rounded-full hover:bg-red-100 transition-all duration-200 text-sm">
                    <i class="fa-solid fa-xmark"></i> Batalkan Pesanan
                </button>
            @endif

            @if (in_array($order->status, ['delivered', 'cancelled']))
                <form action="{{ route('user.orders.reorder', $order) }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center gap-2 bg-primary/10 border border-primary/20 text-primary font-bold px-5 py-2.5 rounded-full hover:bg-primary/20 transition-all duration-200 text-sm">
                        <i class="fa-solid fa-rotate-right"></i> Beli Lagi
                    </button>
                </form>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Kiri --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Status Header --}}
                <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-primary to-primary-dark"></div>
                    <div class="flex items-start justify-between flex-wrap gap-4">
                        <div>
                            <p class="text-xs text-gray-400 mb-1">Kode Pesanan</p>
                            <div class="flex items-center gap-2">
                                <p class="text-xl font-black text-dark">{{ $order->order_code }}</p>
                                <button type="button" onclick="copyOrderCode(this)" data-code="{{ $order->order_code }}"
                                    class="w-7 h-7 rounded-lg bg-gray-100 hover:bg-primary/10 text-gray-400 hover:text-primary flex items-center justify-center transition-all duration-200"
                                    title="Salin kode pesanan">
                                    <i class="fa-regular fa-copy text-xs"></i>
                                </button>
                            </div>
                            <p class="text-xs text-gray-400 mt-1">
                                {{ $order->created_at->isoFormat('dddd, D MMMM Y · HH:mm') }}
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
                        $steps = [
                            'pending' => 0,
                            'confirmed' => 1,
                            'processing' => 2,
                            'shipped' => 3,
                            'delivered' => 4,
                        ];
                        $current = $steps[$order->status] ?? 0;
                        $labels = ['Menunggu Bayar', 'Dibayar', 'Diproses', 'Dikirim', 'Selesai'];
                        $icons = ['fa-clock', 'fa-money-bill', 'fa-gear', 'fa-truck', 'fa-circle-check'];
                    @endphp
                    @if ($order->status !== 'cancelled')
                        <div class="mt-7">
                            <div class="flex items-center justify-between">
                                @foreach ($labels as $i => $label)
                                    <div
                                        class="flex flex-col items-center flex-1 {{ $i < count($labels) - 1 ? 'relative' : '' }}">
                                        <div
                                            class="w-9 h-9 rounded-full flex items-center justify-center text-xs font-bold z-10 transition-all duration-300
                            {{ $i <= $current ? 'bg-gradient-to-br from-primary to-primary-dark text-white shadow-md shadow-primary/30' : 'bg-gray-100 text-gray-400' }}">
                                            <i class="fa-solid {{ $icons[$i] }}"></i>
                                        </div>
                                        @if ($i < count($labels) - 1)
                                            <div
                                                class="absolute top-4 left-1/2 w-full h-0.5 {{ $i < $current ? 'bg-gradient-to-r from-primary to-primary-dark' : 'bg-gray-100' }} -z-0">
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
                <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm">
                    <h3 class="font-black text-dark mb-4 flex items-center gap-2">
                        <span class="w-8 h-8 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                            <i class="fa-solid fa-bag-shopping text-xs"></i>
                        </span>
                        Produk Dipesan
                    </h3>
                    <div class="space-y-4">
                        @foreach ($order->items as $item)
                            <div class="flex gap-4 items-center pb-4 border-b border-gray-50 last:border-0 last:pb-0">
                                <div
                                    class="w-16 h-16 bg-gradient-to-br from-primary/10 to-indigo-50 rounded-2xl overflow-hidden flex-shrink-0">
                                    @if ($item->product_image)
                                        <img src="{{ asset('storage/' . $item->product_image) }}"
                                            class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-2xl">👕</div>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <p class="font-bold text-dark text-sm">{{ $item->product_name }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">Ukuran {{ $item->product_size }} ·
                                        {{ $item->quantity }} pcs</p>
                                    <p class="text-xs text-gray-500 mt-0.5">
                                        Rp{{ number_format($item->price, 0, ',', '.') }} /
                                        pcs
                                    </p>
                                </div>
                                <p class="font-black text-dark text-sm flex-shrink-0">
                                    Rp{{ number_format($item->subtotal, 0, ',', '.') }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Upload Bukti Bayar — di halaman detail --}}
                @if ($order->status === 'pending')
                    <div class="bg-gradient-to-br from-amber-50 to-orange-50 border border-amber-100 rounded-3xl p-6"
                        id="section-upload">
                        <h3 class="font-black text-amber-800 mb-2 flex items-center gap-2">
                            <span class="w-8 h-8 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center">
                                <i class="fa-solid fa-money-bill-wave text-xs"></i>
                            </span>
                            Selesaikan Pembayaran
                        </h3>

                        {{-- Countdown --}}
                        @if ($order->payment_deadline && !$order->isPaymentExpired())
                            <div class="flex items-center gap-3 mb-4 flex-wrap">
                                <p class="text-sm text-amber-700">Bayar sebelum:</p>
                                <span
                                    class="bg-white border border-amber-200 px-3 py-1 rounded-xl font-black text-amber-700 font-mono tracking-widest shadow-sm"
                                    data-countdown="{{ $order->payment_seconds_left }}"
                                    id="countdown-{{ $order->id }}">
                                    {{ gmdate('H:i:s', $order->payment_seconds_left) }}
                                </span>
                                <span class="text-xs text-amber-600">{{ $order->payment_deadline_label }}</span>
                            </div>
                        @elseif($order->isPaymentExpired())
                            <div class="bg-red-50 rounded-xl p-3 mb-4 text-xs text-red-700 font-bold">
                                ⏰ Waktu pembayaran sudah habis.
                            </div>
                        @endif

                        <div class="bg-white rounded-2xl p-4 text-sm mb-4 shadow-sm">
                            <p class="font-bold text-gray-700 mb-2">Informasi Transfer:</p>
                            <table class="text-xs w-full">
                                <tr>
                                    <td class="text-gray-400 py-1 w-28">Bank</td>
                                    <td class="font-bold">BCA</td>
                                </tr>
                                <tr>
                                    <td class="text-gray-400 py-1">No. Rekening</td>
                                    <td class="font-bold text-primary">
                                        <span class="inline-flex items-center gap-2">
                                            1234567890
                                            <button type="button" onclick="copyText(this, '1234567890')"
                                                class="text-gray-300 hover:text-primary transition-colors duration-200"
                                                title="Salin nomor rekening">
                                                <i class="fa-regular fa-copy"></i>
                                            </button>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-gray-400 py-1">Atas Nama</td>
                                    <td class="font-bold">Caysie Store</td>
                                </tr>
                                <tr>
                                    <td class="text-gray-400 py-1">Jumlah Transfer</td>
                                    <td class="font-black text-primary">Rp{{ number_format($order->total, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </table>
                        </div>

                        @if (!$order->payment_proof && !$order->isPaymentExpired())
                            <form action="{{ route('user.orders.proof', $order) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="flex gap-3 flex-wrap">
                                    <input type="file" name="payment_proof" accept="image/*" required
                                        class="flex-1 text-xs text-gray-600 file:mr-3 file:py-2.5 file:px-5 file:rounded-xl file:border-0 file:bg-amber-200 file:text-amber-800 file:font-bold hover:file:bg-amber-300 transition">
                                    <button type="submit"
                                        class="px-5 py-2.5 bg-amber-500 text-white font-bold rounded-xl hover:bg-amber-600 transition text-sm shadow-md shadow-amber-200">
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
                <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm">
                    <h3 class="font-black text-dark mb-4 flex items-center gap-2">
                        <span class="w-8 h-8 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                            <i class="fa-solid fa-receipt text-xs"></i>
                        </span>
                        Rincian Biaya
                    </h3>
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
                    <div
                        class="flex justify-between items-center mt-4 pt-4 border-t border-gray-100 bg-primary/5 -mx-6 -mb-6 px-6 py-4 rounded-b-3xl">
                        <span class="font-black text-dark">Total Bayar</span>
                        <span
                            class="font-black text-primary text-lg">Rp{{ number_format($order->total, 0, ',', '.') }}</span>
                    </div>
                </div>

                {{-- Info Pengiriman --}}
                <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm">
                    <h3 class="font-black text-dark mb-4 flex items-center gap-2">
                        <span class="w-8 h-8 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                            <i class="fa-solid fa-truck text-xs"></i>
                        </span>
                        Info Pengiriman
                    </h3>
                    <div class="space-y-3 text-sm">
                        <div>
                            <p class="text-gray-400 text-xs mb-1">Kurir</p>
                            <p class="font-bold text-dark">{{ $order->courier_name }} — {{ $order->courier_service }}
                            </p>
                            <p class="text-xs text-gray-400">Estimasi {{ $order->shipping_estimate }} hari kerja</p>

                            @if ($order->tracking_number)
                                <div
                                    class="flex items-center justify-between gap-3 mt-3 bg-gray-50 rounded-2xl px-3.5 py-2.5">
                                    <div class="min-w-0">
                                        <p class="text-[11px] text-gray-400">No. Resi</p>
                                        <p class="font-mono font-bold text-gray-700 text-xs truncate">
                                            {{ $order->tracking_number }}</p>
                                    </div>
                                    <button onclick="openTrackingModal()"
                                        class="flex-shrink-0 inline-flex items-center gap-1.5 bg-gradient-to-r from-primary to-primary-dark text-white text-xs font-bold px-3.5 py-2 rounded-lg hover:opacity-90 transition-all duration-200 shadow-sm shadow-primary/25">
                                        <i class="fa-solid fa-truck-fast text-xs"></i> Lacak Paket
                                    </button>
                                </div>
                            @endif
                        </div>
                        <div class="pt-3 border-t border-gray-100">
                            <p class="text-gray-400 text-xs mb-1">Alamat Penerima</p>
                            <p class="font-bold text-dark">{{ $order->receiver_name }}</p>
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
                    <div class="bg-gray-50 rounded-3xl p-4 border border-gray-100">
                        <p class="text-xs font-bold text-gray-500 mb-1 flex items-center gap-1.5">
                            <i class="fa-solid fa-note-sticky text-gray-400"></i> Catatan
                        </p>
                        <p class="text-sm text-gray-600">{{ $order->notes }}</p>
                    </div>
                @endif
            </div>
        </div>

    </section>

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

        // ── Salin teks (kode pesanan / no. rekening) ─────────────
        function copyText(btnEl, text) {
            navigator.clipboard.writeText(text).then(() => {
                const icon = btnEl.querySelector('i');
                const original = icon.className;
                icon.className = 'fa-solid fa-check text-green-500';
                setTimeout(() => icon.className = original, 1500);
            }).catch(() => {});
        }

        function copyOrderCode(btnEl) {
            copyText(btnEl, btnEl.dataset.code);
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
                            Pesanan <strong id="cancel-order-code"></strong> akan dibatalkan dan stok produk
                            dikembalikan.
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
                                class="flex items-center gap-3 p-3 border border-gray-100 rounded-2xl cursor-pointer hover:border-primary hover:bg-primary/5 transition-all duration-200">
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
            <div
                class="flex items-center justify-between px-6 pt-6 pb-4 flex-shrink-0 border-b border-gray-100 bg-gradient-to-r from-primary/5 to-transparent">
                <div>
                    <h3 class="font-black text-dark text-lg">Lacak Paket</h3>
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
