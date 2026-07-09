@extends('layouts.app')
@section('title', 'Riwayat Pesanan')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
            <div class="flex items-center gap-3">
                <span class="w-10 h-10 bg-purple-100 text-primary rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-receipt text-sm"></i>
                </span>
                <div>
                    <h1 class="text-2xl font-black text-gray-800 leading-tight">Riwayat Pesanan</h1>
                    <p class="text-xs text-gray-400">
                        @if ($orders->total())
                            Kamu punya {{ $orders->total() }} pesanan tercatat
                        @else
                            Semua pesananmu akan muncul di sini
                        @endif
                    </p>
                </div>
            </div>
            <a href="{{ route('user.shop') }}"
                class="inline-flex items-center gap-2 bg-primary text-white font-bold px-5 py-2.5 rounded-xl hover:bg-primary-dark transition text-sm shadow-lg shadow-purple-200">
                <i class="fa-solid fa-bag-shopping"></i> Lanjut Belanja
            </a>
        </div>

        {{-- Filter Tab Status --}}
        @php
            $tabList = [
                '' => 'Semua',
                'pending' => 'Menunggu Bayar',
                'confirmed' => 'Sudah Dibayar',
                'processing' => 'Diproses',
                'shipped' => 'Dikirim',
                'delivered' => 'Selesai',
                'cancelled' => 'Dibatalkan',
            ];
            $tabColors = [
                'pending' => 'yellow',
                'confirmed' => 'blue',
                'processing' => 'purple',
                'shipped' => 'indigo',
                'delivered' => 'green',
                'cancelled' => 'red',
            ];
        @endphp

        <div class="flex gap-2 flex-wrap mb-6 overflow-x-auto pb-1">
            @foreach ($tabList as $key => $label)
                <a href="{{ route('user.orders', $key ? ['status' => $key] : []) }}"
                    class="flex-shrink-0 px-4 py-2 rounded-full text-xs font-bold transition whitespace-nowrap
              {{ request('status') === $key || (!request('status') && $key === '')
                  ? 'bg-primary text-white shadow-md shadow-purple-200'
                  : 'bg-white border border-gray-200 text-gray-500 hover:border-primary hover:text-primary' }}">
                    {{ $label }}
                    @if ($key && isset($statusCounts[$key]) && $statusCounts[$key] > 0)
                        <span class="ml-1 opacity-80">({{ $statusCounts[$key] }})</span>
                    @endif
                </a>
            @endforeach
        </div>

        {{-- Daftar Pesanan --}}
        @if ($orders->isEmpty())
            <div class="bg-white rounded-3xl py-20 px-6 text-center border border-gray-100">
                <div class="w-20 h-20 mx-auto mb-5 bg-purple-50 rounded-full flex items-center justify-center">
                    <i class="fa-solid fa-receipt text-3xl text-primary/40"></i>
                </div>
                <h3 class="text-lg font-black text-gray-700 mb-1.5">Belum ada pesanan</h3>
                <p class="text-gray-400 text-sm mb-6">Semua pesanan yang kamu buat akan tampil di halaman ini. Yuk mulai
                    belanja koleksi terbaru Caysie!</p>
                <a href="{{ route('user.shop') }}"
                    class="inline-flex items-center gap-2 bg-primary text-white font-bold px-8 py-3 rounded-2xl hover:bg-primary-dark transition shadow-lg shadow-purple-200">
                    <i class="fa-solid fa-bag-shopping text-xs"></i> Mulai Belanja
                </a>
            </div>
        @else
            <div class="space-y-3">
                @foreach ($orders as $order)
                    @php
                        $sc = $tabColors[$order->status] ?? 'gray';
                        $isExpired = $order->isPaymentExpired();
                    @endphp

                    <div
                        class="bg-white rounded-2xl overflow-hidden border
        {{ $order->status === 'pending' && !$isExpired ? 'border-yellow-300' : ($order->status === 'delivered' ? 'border-green-200' : ($order->status === 'cancelled' ? 'border-red-100' : 'border-gray-100')) }}
        transition hover:shadow-md">

                        {{-- Header Kartu --}}
                        <div
                            class="px-4 py-2.5 flex items-center justify-between flex-wrap gap-2 bg-gray-50/70 border-b border-gray-100">
                            <div class="flex items-center gap-3 flex-wrap">
                                <span class="font-black text-gray-800 text-sm">{{ $order->order_code }}</span>
                                <span class="text-xs text-gray-400">
                                    <i class="fa-regular fa-calendar text-xs mr-1"></i>
                                    {{ $order->created_at->isoFormat('D MMM Y, HH:mm') }}
                                </span>
                            </div>
                            <div class="flex items-center gap-2">
                                {{-- Badge status --}}
                                <span
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold
                    bg-{{ $sc }}-100 text-{{ $sc }}-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-{{ $sc }}-500"></span>
                                    @if ($order->status === 'pending' && $isExpired)
                                        Waktu Habis
                                    @else
                                        {{ $order->status_label }}
                                    @endif
                                </span>
                            </div>
                        </div>

                        <div class="p-4">

                            {{-- Produk & Total (ringkas jadi 1 baris, berapa pun jumlah itemnya) --}}
                            <div class="flex items-center gap-3 mb-3.5">
                                {{-- Thumbnail bertumpuk --}}
                                <div class="flex -space-x-3 flex-shrink-0">
                                    @foreach ($order->items->take(3) as $item)
                                        <div
                                            class="w-11 h-11 rounded-xl overflow-hidden bg-gray-100 border-2 border-white flex items-center justify-center flex-shrink-0">
                                            @if ($item->product_image)
                                                <img src="{{ asset('storage/' . $item->product_image) }}"
                                                    class="w-full h-full object-cover {{ $order->status === 'cancelled' ? 'grayscale opacity-50' : '' }}">
                                            @else
                                                <span
                                                    class="text-base {{ $order->status === 'cancelled' ? 'opacity-40' : '' }}">👕</span>
                                            @endif
                                        </div>
                                    @endforeach
                                    @if ($order->items->count() > 3)
                                        <div
                                            class="w-11 h-11 rounded-xl bg-gray-100 border-2 border-white flex items-center justify-center flex-shrink-0">
                                            <span
                                                class="text-[11px] font-bold text-gray-500">+{{ $order->items->count() - 3 }}</span>
                                        </div>
                                    @endif
                                </div>

                                {{-- Nama produk pertama + sisanya --}}
                                <div class="flex-1 min-w-0">
                                    <p
                                        class="font-bold text-sm {{ $order->status === 'cancelled' ? 'text-gray-400' : 'text-gray-800' }} line-clamp-1">
                                        {{ $order->items->first()->product_name }}
                                        @if ($order->items->count() > 1)
                                            <span class="text-gray-400 font-normal">+{{ $order->items->count() - 1 }}
                                                produk
                                                lain</span>
                                        @endif
                                    </p>
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        {{ $order->items->sum('quantity') }} barang
                                    </p>
                                </div>

                                {{-- Total bayar --}}
                                <div class="text-right flex-shrink-0">
                                    <p class="text-[11px] text-gray-400">Total Bayar</p>
                                    <p
                                        class="font-black text-sm {{ $order->status === 'cancelled' ? 'text-gray-300' : 'text-primary' }}">
                                        Rp{{ number_format($order->total, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>

                            {{-- Info Pengiriman jika shipped --}}
                            @if ($order->status === 'shipped')
                                <div
                                    class="bg-blue-50 border border-blue-100 rounded-xl px-4 py-2.5 mb-3.5 flex items-center gap-3">
                                    <i class="fa-solid fa-truck text-blue-500 text-sm"></i>
                                    <p class="text-xs text-blue-700">
                                        <span class="font-bold">Paket sedang dalam perjalanan</span> · estimasi tiba
                                        {{ $order->shipping_estimate }} hari kerja
                                    </p>
                                </div>
                            @endif

                            {{-- Countdown + Info Bayar jika PENDING --}}
                            @if ($order->status === 'pending' && !$isExpired)
                                <div class="bg-yellow-50 border border-yellow-200 rounded-xl mb-3.5 overflow-hidden">
                                    <div class="flex items-center justify-between gap-3 px-4 py-2.5">
                                        <p class="text-xs font-bold text-yellow-800 flex items-center gap-1.5 min-w-0">
                                            <i class="fa-solid fa-clock text-yellow-600 flex-shrink-0"></i>
                                            <span class="line-clamp-1">Bayar sebelum
                                                {{ $order->payment_deadline_label }}</span>
                                        </p>
                                        <span
                                            class="font-mono font-black text-sm text-yellow-700 tracking-wider flex-shrink-0"
                                            data-countdown="{{ $order->payment_seconds_left }}"
                                            id="countdown-{{ $order->id }}">
                                            {{ gmdate('H:i:s', $order->payment_seconds_left) }}
                                        </span>
                                    </div>
                                    <details class="border-t border-yellow-200/70">
                                        <summary
                                            class="px-4 py-2 text-xs font-semibold text-yellow-700 cursor-pointer select-none hover:text-yellow-800">
                                            Lihat cara pembayaran
                                        </summary>
                                        <div class="bg-white px-4 py-3 text-xs text-gray-600 space-y-1">
                                            <p><span class="text-gray-400 w-20 inline-block">Bank</span>
                                                <strong>BCA</strong>
                                            </p>
                                            <p><span class="text-gray-400 w-20 inline-block">No. Rek</span> <strong
                                                    class="text-primary">1234567890</strong>
                                                <button onclick="copyRek()"
                                                    class="ml-2 text-primary hover:underline font-bold"
                                                    id="copy-rek">Salin</button>
                                            </p>
                                            <p><span class="text-gray-400 w-20 inline-block">Atas Nama</span> <strong>Caysie
                                                    Store</strong></p>
                                            <p><span class="text-gray-400 w-20 inline-block">Jumlah</span>
                                                <strong
                                                    class="text-primary">Rp{{ number_format($order->total, 0, ',', '.') }}</strong>
                                                <span class="text-gray-400 ml-1">(sesuai nominal ini)</span>
                                            </p>
                                        </div>
                                    </details>
                                </div>
                            @elseif($order->status === 'pending' && $isExpired)
                                <div
                                    class="bg-red-50 border border-red-200 rounded-xl px-4 py-2.5 mb-3.5 flex items-center gap-3">
                                    <i class="fa-solid fa-clock text-red-400 text-sm"></i>
                                    <p class="text-xs font-bold text-red-700">Waktu pembayaran telah habis. Pesanan otomatis
                                        dibatalkan.</p>
                                </div>
                            @endif

                            {{-- Alasan batal --}}
                            @if ($order->status === 'cancelled' && $order->cancel_reason)
                                <div class="bg-red-50 border border-red-100 rounded-xl px-4 py-2.5 mb-3.5">
                                    <p class="text-xs font-bold text-red-700 mb-1">
                                        <i class="fa-solid fa-circle-xmark mr-1"></i>
                                        Alasan Pembatalan
                                        @if ($order->cancelled_by === 'system')
                                            <span class="font-normal text-red-400">(sistem)</span>
                                        @elseif($order->cancelled_by === 'user')
                                            <span class="font-normal text-red-400">(kamu)</span>
                                        @endif
                                    </p>
                                    <p class="text-xs text-red-600 italic line-clamp-1">"{{ $order->cancel_reason }}"</p>
                                </div>
                            @endif

                            {{-- Bukti bayar sudah diupload --}}
                            @if ($order->payment_proof && $order->status !== 'pending')
                                <div
                                    class="flex items-center gap-2 text-xs text-green-700 bg-green-50 rounded-xl px-4 py-2 mb-3.5 border border-green-100">
                                    <i class="fa-solid fa-circle-check text-green-500"></i>
                                    <span class="font-semibold">Bukti pembayaran sudah dikirim</span>
                                    <span class="text-green-500">·
                                        {{ $order->paid_at?->isoFormat('D MMM Y, HH:mm') }}</span>
                                </div>
                            @endif

                            {{-- ===== TOMBOL AKSI ===== --}}
                            <div class="flex flex-wrap gap-2 pt-3 border-t border-gray-100">

                                {{-- Detail selalu tampil --}}
                                <a href="{{ route('user.orders.show', $order) }}"
                                    class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-gray-50 border border-gray-200 text-gray-600 rounded-xl text-xs font-bold hover:border-primary hover:text-primary transition">
                                    <i class="fa-solid fa-eye text-xs"></i> Detail Pesanan
                                </a>

                                {{-- Upload bukti bayar --}}
                                @if ($order->can_pay && !$order->payment_proof)
                                    <button onclick="openUploadModal('{{ $order->id }}', '{{ $order->order_code }}')"
                                        class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-primary text-white rounded-xl text-xs font-bold hover:bg-primary-dark transition shadow-md shadow-purple-200">
                                        <i class="fa-solid fa-upload text-xs"></i> Upload Bukti Bayar
                                    </button>
                                @endif

                                {{-- Batalkan pesanan --}}
                                @if ($order->can_cancel && $order->status !== 'cancelled')
                                    <button onclick="openCancelModal('{{ $order->id }}', '{{ $order->order_code }}')"
                                        class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-red-50 border border-red-200 text-red-600 rounded-xl text-xs font-bold hover:bg-red-100 transition">
                                        <i class="fa-solid fa-xmark text-xs"></i> Batalkan Pesanan
                                    </button>
                                @endif

                                {{-- Beli lagi jika delivered atau cancelled --}}
                                @if (in_array($order->status, ['delivered', 'cancelled']))
                                    <form action="{{ route('user.orders.reorder', $order) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-purple-50 border border-purple-200 text-purple-700 rounded-xl text-xs font-bold hover:bg-purple-100 transition">
                                            <i class="fa-solid fa-rotate-right text-xs"></i> Beli Lagi
                                        </button>
                                    </form>
                                @endif

                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if ($orders->hasPages())
                <div class="mt-6">{{ $orders->withQueryString()->links() }}</div>
            @endif
        @endif

    </div>

    {{-- ===== MODAL UPLOAD BUKTI BAYAR ===== --}}
    <div id="modal-upload" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4"
        style="background:rgba(0,0,0,0.5)">
        <div class="bg-white rounded-3xl p-6 w-full max-w-md shadow-2xl">
            <div class="flex items-center justify-between mb-5">
                <h3 class="font-black text-gray-800 text-lg">Upload Bukti Pembayaran</h3>
                <button onclick="closeUploadModal()"
                    class="w-8 h-8 bg-gray-100 rounded-xl flex items-center justify-center hover:bg-gray-200 transition text-gray-500">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <form id="form-upload" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <p class="text-xs font-bold text-gray-500 mb-1">Pesanan</p>
                    <p class="font-black text-primary" id="upload-order-code"></p>
                </div>

                {{-- Drop zone --}}
                <div id="drop-zone"
                    class="border-2 border-dashed border-gray-200 rounded-2xl p-8 text-center mb-4 cursor-pointer hover:border-primary transition"
                    onclick="document.getElementById('proof-file').click()">
                    <div id="dz-placeholder">
                        <i class="fa-solid fa-cloud-arrow-up text-4xl text-gray-300 mb-3"></i>
                        <p class="text-sm font-bold text-gray-500">Klik atau drag foto bukti transfer</p>
                        <p class="text-xs text-gray-400 mt-1">JPG, PNG · Maks. 2MB</p>
                    </div>
                    <div id="dz-preview" class="hidden">
                        <img id="proof-preview" class="max-h-40 mx-auto rounded-xl" src="" alt="">
                        <p class="text-xs text-green-600 font-bold mt-2">
                            <i class="fa-solid fa-circle-check mr-1"></i> Foto siap dikirim
                        </p>
                    </div>
                </div>

                <input type="file" id="proof-file" name="payment_proof" accept="image/*" class="hidden"
                    onchange="previewProof(this)">

                @error('payment_proof')
                    <p class="text-xs text-red-500 mb-3">{{ $message }}</p>
                @enderror

                <div class="bg-blue-50 rounded-xl p-3 mb-4 text-xs text-blue-700 flex gap-2">
                    <i class="fa-solid fa-circle-info text-blue-500 mt-0.5 flex-shrink-0"></i>
                    <span>Pastikan foto bukti transfer terlihat jelas. Admin akan memverifikasi dalam 1×24 jam.</span>
                </div>

                <div class="flex gap-3">
                    <button type="button" onclick="closeUploadModal()"
                        class="flex-1 py-3 bg-gray-100 text-gray-600 rounded-xl font-bold text-sm hover:bg-gray-200 transition">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-1 py-3 bg-primary text-white rounded-xl font-bold text-sm hover:bg-primary-dark transition shadow-lg shadow-purple-200 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-paper-plane"></i> Kirim Bukti
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ===== MODAL BATALKAN PESANAN ===== --}}
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

@endsection

@push('scripts')
    <script>
        // ── Countdown Timer ───────────────────────────────────
        document.querySelectorAll('[data-countdown]').forEach(el => {
            let secs = parseInt(el.dataset.countdown);
            if (secs <= 0) return;

            const interval = setInterval(() => {
                secs--;
                if (secs <= 0) {
                    clearInterval(interval);
                    el.textContent = '00:00:00';
                    el.classList.add('text-red-600');
                    // Reload halaman agar status diupdate
                    setTimeout(() => location.reload(), 1500);
                    return;
                }
                const h = Math.floor(secs / 3600);
                const m = Math.floor((secs % 3600) / 60);
                const s = secs % 60;
                el.textContent =
                    `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;

                // Warna merah jika < 1 jam
                if (secs < 3600) el.classList.add('text-red-600');
            }, 1000);
        });

        // ── Salin Nomor Rekening ─────────────────────────────
        function copyRek() {
            navigator.clipboard.writeText('1234567890').then(() => {
                const btn = document.getElementById('copy-rek');
                if (btn) {
                    btn.textContent = 'Tersalin!';
                    btn.classList.add('text-green-600');
                }
                setTimeout(() => {
                    if (btn) {
                        btn.textContent = 'Salin';
                        btn.classList.remove('text-green-600');
                    }
                }, 2000);
            });
        }

        // ── Modal Upload Bukti ───────────────────────────────
        function openUploadModal(orderId, orderCode) {
            document.getElementById('form-upload').action = `/user/orders/${orderId}/proof`;
            document.getElementById('upload-order-code').textContent = orderCode;
            document.getElementById('modal-upload').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeUploadModal() {
            document.getElementById('modal-upload').classList.add('hidden');
            document.body.style.overflow = '';
        }

        function previewProof(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = e => {
                    document.getElementById('proof-preview').src = e.target.result;
                    document.getElementById('dz-placeholder').classList.add('hidden');
                    document.getElementById('dz-preview').classList.remove('hidden');
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Drag & Drop
        const dz = document.getElementById('drop-zone');
        if (dz) {
            dz.addEventListener('dragover', e => {
                e.preventDefault();
                dz.classList.add('border-primary', 'bg-purple-50');
            });
            dz.addEventListener('dragleave', () => dz.classList.remove('border-primary', 'bg-purple-50'));
            dz.addEventListener('drop', e => {
                e.preventDefault();
                dz.classList.remove('border-primary', 'bg-purple-50');
                const file = e.dataTransfer.files[0];
                if (file) {
                    document.getElementById('proof-file').files = e.dataTransfer.files;
                    previewProof(document.getElementById('proof-file'));
                }
            });
        }

        // ── Modal Batalkan ───────────────────────────────────
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
                // Set name agar yang tersubmit adalah textarea
                other.name = 'cancel_reason';
                radio.name = '_cancel_reason_radio';
            } else {
                other.classList.add('hidden');
                other.required = false;
                other.name = 'cancel_reason_other';
                radio.name = 'cancel_reason';
            }
        }

        // Tutup modal saat klik background
        ['modal-upload', 'modal-cancel'].forEach(id => {
            document.getElementById(id)?.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.add('hidden');
                    document.body.style.overflow = '';
                }
            });
        });
    </script>
@endpush
