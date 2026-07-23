@extends('layouts.app')
@section('title', 'Pembayaran — ' . $order->order_number)

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-purple-50 to-blue-50 py-10 px-4">
        <div class="max-w-2xl mx-auto">

            {{-- Header --}}
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-primary/10 mb-4">
                    <i class="fa-solid fa-credit-card text-primary text-2xl"></i>
                </div>
                <h1 class="text-2xl font-black text-gray-800">Selesaikan Pembayaran</h1>
                <p class="text-gray-500 text-sm mt-1">Pesanan <strong>{{ $order->order_number }}</strong></p>
            </div>

            {{-- Status Card --}}
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden mb-6">

                {{-- Status Banner --}}
                <div id="status-banner"
                    class="px-6 py-4 flex items-center gap-3
             @if ($order->status === 'pending') bg-yellow-50 border-b border-yellow-100
             @elseif($order->status === 'confirmed') bg-green-50 border-b border-green-100
             @else bg-gray-50 border-b border-gray-100 @endif">
                    <div id="status-icon">
                        @if ($order->status === 'pending')
                            <div class="w-8 h-8 border-3 border-yellow-400 border-t-transparent rounded-full animate-spin"
                                style="border-width:3px"></div>
                        @elseif($order->status === 'confirmed')
                            <i class="fa-solid fa-circle-check text-green-500 text-2xl"></i>
                        @else
                            <i class="fa-solid fa-circle-xmark text-red-400 text-2xl"></i>
                        @endif
                    </div>
                    <div>
                        <p id="status-label"
                            class="font-black text-sm
                    @if ($order->status === 'pending') text-yellow-800
                    @elseif($order->status === 'confirmed') text-green-800
                    @else text-gray-700 @endif">
                            @if ($order->status === 'pending')
                                Menunggu Pembayaran
                            @elseif($order->status === 'confirmed')
                                Pembayaran Dikonfirmasi ✓
                            @else
                                {{ $order->status_label }}
                            @endif
                        </p>
                        <p class="text-xs text-gray-500" id="status-sub">
                            @if ($order->status === 'pending')
                                Halaman ini otomatis update saat pembayaran diterima
                            @elseif($order->status === 'confirmed')
                                Dibayar pada {{ $order->paid_at?->format('d M Y H:i') }}
                            @endif
                        </p>
                    </div>
                </div>

                {{-- Ringkasan --}}
                <div class="px-6 py-5 space-y-3">

                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">No. Pesanan</span>
                        <span class="font-bold font-mono text-gray-800">{{ $order->order_number }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Penerima</span>
                        <span class="font-semibold">{{ $order->receiver_name }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Tujuan</span>
                        <span class="font-semibold text-right max-w-[55%]">{{ $order->receiver_city }},
                            {{ $order->receiver_province }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Kurir</span>
                        <span class="font-semibold">{{ $order->courier_name }} {{ $order->courier_service }}</span>
                    </div>

                    <div class="border-t border-gray-100 pt-3 mt-3 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Subtotal Produk</span>
                            <span class="font-semibold">Rp{{ number_format($order->subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Ongkos Kirim</span>
                            <span class="font-semibold">Rp{{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center pt-2 border-t border-gray-100">
                            <span class="font-black text-gray-800">Total Bayar</span>
                            <span
                                class="font-black text-primary text-2xl">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                {{-- CTA Bayar --}}
                @if ($order->status === 'pending')
                    <div class="px-6 pb-6 space-y-3">
                        @if ($order->xendit_invoice_url)
                            <a href="{{ $order->xendit_invoice_url }}" target="_blank"
                                class="block w-full bg-primary text-white font-black text-center py-4 rounded-2xl
                      hover:bg-primary-dark transition shadow-lg shadow-purple-200 text-sm">
                                <i class="fa-solid fa-arrow-up-right-from-square mr-2"></i>
                                Bayar Sekarang via Xendit
                            </a>
                            <p class="text-center text-xs text-gray-400">
                                Mendukung: Virtual Account, QRIS, OVO, DANA, ShopeePay, Kartu Kredit
                            </p>
                        @else
                            <form method="POST" action="{{ route('user.payment.retry', $order) }}">
                                @csrf
                                <button type="submit"
                                    class="w-full bg-primary text-white font-black py-4 rounded-2xl
                               hover:bg-primary-dark transition shadow-lg shadow-purple-200 text-sm">
                                    <i class="fa-solid fa-rotate-right mr-2"></i>
                                    Buat Link Pembayaran
                                </button>
                            </form>
                        @endif

                        {{-- Countdown kedaluwarsa --}}
                        @if ($order->xendit_expires_at)
                            <div
                                class="bg-orange-50 border border-orange-100 rounded-xl px-4 py-3 flex items-center gap-2.5">
                                <i class="fa-regular fa-clock text-orange-500 flex-shrink-0"></i>
                                <div>
                                    <p class="text-xs text-orange-700 font-bold">Link kedaluwarsa dalam:</p>
                                    <p class="text-lg font-black text-orange-600 font-mono" id="countdown">—</p>
                                </div>
                            </div>
                        @endif
                    </div>
                @elseif($order->status === 'confirmed')
                    <div class="px-6 pb-6">
                        <a href="{{ route('user.orders.show', $order) }}"
                            class="block w-full bg-green-600 text-white font-black text-center py-4 rounded-2xl
                      hover:bg-green-700 transition shadow-lg shadow-green-200 text-sm">
                            <i class="fa-solid fa-box mr-2"></i>
                            Lihat Detail Pesanan
                        </a>
                    </div>
                @endif
            </div>

            {{-- Metode pembayaran yang didukung --}}
            @if ($order->status === 'pending')
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-6">
                    <p class="text-xs font-bold text-gray-500 mb-4 uppercase tracking-wider">Metode Pembayaran Tersedia</p>
                    <div class="grid grid-cols-4 gap-3 text-center">
                        @foreach ([['icon' => 'fa-building-columns', 'label' => 'Virtual Account', 'color' => 'text-blue-600 bg-blue-50'], ['icon' => 'fa-qrcode', 'label' => 'QRIS', 'color' => 'text-green-600 bg-green-50'], ['icon' => 'fa-wallet', 'label' => 'E-Wallet', 'color' => 'text-purple-600 bg-purple-50'], ['icon' => 'fa-credit-card', 'label' => 'Kartu Kredit', 'color' => 'text-orange-600 bg-orange-50']] as $pm)
                            <div class="rounded-xl p-3 {{ explode(' ', $pm['color'])[1] }}">
                                <i
                                    class="fa-solid {{ $pm['icon'] }} {{ explode(' ', $pm['color'])[0] }} text-xl mb-1.5 block"></i>
                                <p class="text-[10px] font-bold {{ explode(' ', $pm['color'])[0] }}">{{ $pm['label'] }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Back link --}}
            <div class="text-center">
                <a href="{{ route('user.orders') }}" class="text-sm text-gray-400 hover:text-gray-600 transition">
                    ← Lihat semua pesanan
                </a>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // ── Polling status pembayaran (setiap 5 detik) ────────────
        @if ($order->status === 'pending')
            const POLL_URL = '{{ route('user.payment.status', $order) }}';
            const ORDERS_URL = '{{ route('user.orders.show', $order) }}';

            const poll = setInterval(async () => {
                try {
                    const r = await fetch(POLL_URL, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const d = await r.json();

                    if (d.order_status === 'confirmed') {
                        clearInterval(poll);
                        // Update UI tanpa reload
                        document.getElementById('status-banner').className =
                            'px-6 py-4 flex items-center gap-3 bg-green-50 border-b border-green-100';
                        document.getElementById('status-icon').innerHTML =
                            '<i class="fa-solid fa-circle-check text-green-500 text-2xl"></i>';
                        document.getElementById('status-label').className = 'font-black text-sm text-green-800';
                        document.getElementById('status-label').textContent = 'Pembayaran Dikonfirmasi ✓';
                        document.getElementById('status-sub').textContent = 'Dibayar pada ' + (d.paid_at || '');

                        // Redirect ke detail order setelah 2 detik
                        setTimeout(() => {
                            window.location.href = ORDERS_URL;
                        }, 2000);
                    }

                    if (d.order_status === 'cancelled') {
                        clearInterval(poll);
                        document.getElementById('status-label').textContent = 'Pembayaran Kedaluwarsa';
                    }

                } catch (e) {
                    /* silent */
                }
            }, 5000);
        @endif

        // ── Countdown ─────────────────────────────────────────────
        @if ($order->xendit_expires_at)
            const expiry = new Date('{{ \Carbon\Carbon::parse($order->xendit_expires_at)->toIso8601String() }}');
            const el = document.getElementById('countdown');
            const tick = setInterval(() => {
                const diff = Math.max(0, Math.floor((expiry - Date.now()) / 1000));
                if (diff === 0) {
                    clearInterval(tick);
                    if (el) el.textContent = 'Kedaluwarsa';
                    return;
                }
                const h = Math.floor(diff / 3600);
                const m = Math.floor((diff % 3600) / 60);
                const s = diff % 60;
                if (el) el.textContent =
                    `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
            }, 1000);
        @endif
    </script>
@endpush
