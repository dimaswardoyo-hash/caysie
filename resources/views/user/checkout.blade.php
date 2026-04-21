@extends('layouts.app')
@section('title', 'Checkout')

@section('content')

<h1 class="text-2xl font-black text-gray-800 mb-6">📦 Checkout</h1>

<form action="{{ route('user.checkout.store') }}" method="POST">
@csrf

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- KIRI: Form --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Identitas Penerima --}}
        <div class="bg-white rounded-2xl p-6 border border-gray-100">
            <h3 class="font-black text-gray-800 mb-5 flex items-center gap-2">
                <span class="w-7 h-7 bg-purple-100 text-primary rounded-lg flex items-center justify-center text-xs">
                    <i class="fa-solid fa-user"></i>
                </span>
                Identitas Penerima
            </h3>
            <div class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1.5">Nama Penerima *</label>
                        <input type="text" name="receiver_name" value="{{ old('receiver_name', auth()->user()->name) }}"
                               required placeholder="Nama lengkap penerima"
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-primary transition @error('receiver_name') border-red-400 @enderror">
                        @error('receiver_name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1.5">Nomor HP *</label>
                        <input type="tel" name="receiver_phone" value="{{ old('receiver_phone', auth()->user()->phone) }}"
                               required placeholder="08xxxxxxxxxx"
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-primary transition @error('receiver_phone') border-red-400 @enderror">
                        @error('receiver_phone')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">Alamat Lengkap *</label>
                    <textarea name="receiver_address" rows="3" required
                              placeholder="Nama jalan, nomor rumah, RT/RW, kelurahan, kecamatan"
                              class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-primary transition resize-none @error('receiver_address') border-red-400 @enderror">{{ old('receiver_address') }}</textarea>
                    @error('receiver_address')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1.5">Provinsi *</label>
                        <input type="text" name="receiver_province" value="{{ old('receiver_province') }}"
                               required placeholder="D.I. Yogyakarta"
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-primary transition">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1.5">Kota/Kabupaten *</label>
                        <input type="text" name="receiver_city" value="{{ old('receiver_city') }}"
                               required placeholder="Gunungkidul"
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-primary transition">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1.5">Kode Pos *</label>
                        <input type="text" name="receiver_postal_code" value="{{ old('receiver_postal_code') }}"
                               required placeholder="55800"
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-primary transition">
                    </div>
                </div>
            </div>
        </div>

        {{-- Pilih Jasa Kirim --}}
        <div class="bg-white rounded-2xl p-6 border border-gray-100">
            <h3 class="font-black text-gray-800 mb-5 flex items-center gap-2">
                <span class="w-7 h-7 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center text-xs">
                    <i class="fa-solid fa-truck"></i>
                </span>
                Pilih Jasa Pengiriman
            </h3>
            @error('courier_index')<p class="text-xs text-red-500 mb-3">{{ $message }}</p>@enderror
            <div class="space-y-3" id="courier-list">
                @foreach($couriers as $idx => $courier)
                <label class="flex items-center gap-4 p-4 border-2 rounded-2xl cursor-pointer transition
                              hover:border-primary peer-checked:border-primary group
                              {{ old('courier_index') == $idx ? 'border-primary bg-purple-50' : 'border-gray-100' }}"
                       id="courier-card-{{ $idx }}">
                    <input type="radio" name="courier_index" value="{{ $idx }}" class="sr-only"
                           {{ old('courier_index') == $idx ? 'checked' : '' }}
                           onchange="selectCourier({{ $idx }}, {{ $courier['cost'] }})">
                    <div class="w-10 h-10 bg-gray-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-truck text-gray-500"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-bold text-gray-800 text-sm">{{ $courier['name'] }} — {{ $courier['service'] }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">Estimasi {{ $courier['estimate'] }} hari kerja</p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="font-black text-primary text-sm">Rp{{ number_format($courier['cost'],0,',','.') }}</p>
                    </div>
                </label>
                @endforeach
            </div>
        </div>

        {{-- Catatan --}}
        <div class="bg-white rounded-2xl p-6 border border-gray-100">
            <h3 class="font-black text-gray-800 mb-4 flex items-center gap-2">
                <span class="w-7 h-7 bg-yellow-100 text-yellow-600 rounded-lg flex items-center justify-center text-xs">
                    <i class="fa-solid fa-note-sticky"></i>
                </span>
                Catatan (Opsional)
            </h3>
            <textarea name="notes" rows="2" placeholder="Misal: titip tetangga, pintu depan warna merah, dll."
                      class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-primary transition resize-none">{{ old('notes') }}</textarea>
        </div>
    </div>

    {{-- KANAN: Ringkasan --}}
    <div>
        <div class="bg-white rounded-2xl p-6 border border-gray-100 sticky top-24">
            <h3 class="font-black text-gray-800 mb-5">Ringkasan Pesanan</h3>

            {{-- Daftar Produk --}}
            <div class="space-y-3 mb-5 pb-5 border-b border-gray-100 max-h-60 overflow-y-auto">
                @foreach($carts as $cart)
                <div class="flex gap-3 items-center">
                    <div class="w-12 h-12 rounded-xl overflow-hidden bg-gray-100 flex-shrink-0">
                        @if($cart->product->image)
                            <img src="{{ asset('storage/'.$cart->product->image) }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-xl">👕</div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-gray-800 line-clamp-1">{{ $cart->product->name }}</p>
                        <p class="text-xs text-gray-400">{{ $cart->productSize->size }} × {{ $cart->quantity }}</p>
                    </div>
                    <p class="text-xs font-black text-gray-700 flex-shrink-0">{{ $cart->formatted_subtotal }}</p>
                </div>
                @endforeach
            </div>

            {{-- Kalkulasi --}}
            <div class="space-y-2 mb-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Subtotal produk</span>
                    <span class="font-bold">Rp{{ number_format($subtotal,0,',','.') }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Ongkos kirim</span>
                    <span class="font-bold" id="shipping-display">— Pilih kurir</span>
                </div>
            </div>

            <div class="flex justify-between py-4 border-t border-b border-gray-100 mb-5">
                <span class="font-black text-gray-800">Total Bayar</span>
                <span class="font-black text-primary text-xl" id="grand-total">Rp{{ number_format($subtotal,0,',','.') }}</span>
            </div>

            <div class="bg-blue-50 rounded-xl p-4 mb-5 flex gap-3">
                <i class="fa-solid fa-circle-info text-blue-500 mt-0.5 flex-shrink-0"></i>
                <div>
                    <p class="text-xs font-bold text-blue-800 mb-1">Pembayaran via Transfer</p>
                    <p class="text-xs text-blue-600 leading-relaxed">Setelah order dibuat, kamu akan mendapat nomor rekening untuk transfer. Upload bukti bayar di halaman pesanan.</p>
                </div>
            </div>

            <button type="submit"
                class="w-full bg-primary text-white font-black py-4 rounded-2xl hover:bg-primary-dark transition shadow-lg shadow-purple-200 text-sm flex items-center justify-center gap-2">
                <i class="fa-solid fa-check-circle"></i> Buat Pesanan
            </button>
            <a href="{{ route('user.cart') }}" class="block text-center text-sm text-gray-400 hover:text-gray-600 mt-3 transition">
                ← Kembali ke keranjang
            </a>
        </div>
    </div>

</div>
</form>

@endsection

@push('scripts')
<script>
const subtotal  = {{ $subtotal }};
const formatter = new Intl.NumberFormat('id-ID');

function selectCourier(idx, cost) {
    document.querySelectorAll('[id^="courier-card-"]').forEach((el, i) => {
        el.classList.toggle('border-primary', i === idx);
        el.classList.toggle('bg-purple-50',   i === idx);
        el.classList.toggle('border-gray-100', i !== idx);
    });
    document.getElementById('shipping-display').textContent = 'Rp' + formatter.format(cost);
    document.getElementById('grand-total').textContent = 'Rp' + formatter.format(subtotal + cost);
}

// Auto-select jika ada old value
@if(old('courier_index') !== null)
selectCourier({{ old('courier_index') }}, {{ $couriers[old('courier_index')]['cost'] }});
@endif
</script>
@endpush