@extends('layouts.app')
@section('title', 'Caysie — Fashion Anak Muda Gunungkidul')

@section('content')

    {{-- ========== HERO ========== --}}
    @guest
        <section class="bg-gradient-to-br from-[#6C63FF] via-[#5a52e0] to-[#3730a3] text-white overflow-hidden">
            <div class="max-w-7xl mx-auto px-6 py-24 relative">
                {{-- Dekor lingkaran --}}
                <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full -translate-y-32 translate-x-32"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 bg-white/5 rounded-full translate-y-16 -translate-x-16"></div>

                <div class="relative z-10 max-w-2xl">
                    <span
                        class="inline-block bg-white/20 backdrop-blur text-white text-xs font-bold px-4 py-2 rounded-full mb-6 tracking-wider">
                        ✦ FASHION ANAK MUDA GUNUNGKIDUL
                    </span>
                    <h1 class="text-5xl md:text-6xl font-black leading-tight mb-6">
                        Tampil Keren,<br>
                        <span class="text-purple-200">Harga Bersahabat</span>
                    </h1>
                    <p class="text-lg text-purple-100 mb-10 leading-relaxed max-w-lg">
                        Koleksi kaos & celana terkini untuk anak muda. Kualitas premium, style yang selalu up-to-date langsung
                        dari Gunungkidul.
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <a href="#produk"
                            class="inline-flex items-center gap-2 bg-white text-primary font-bold px-8 py-4 rounded-2xl hover:bg-purple-50 transition shadow-xl shadow-purple-900/30 text-sm">
                            Belanja Sekarang <i class="fa-solid fa-arrow-right"></i>
                        </a>
                        <a href="{{ route('register') }}"
                            class="inline-flex items-center gap-2 bg-white/15 backdrop-blur text-white font-bold px-8 py-4 rounded-2xl border border-white/30 hover:bg-white/25 transition text-sm">
                            Daftar Gratis <i class="fa-solid fa-user-plus"></i>
                        </a>
                    </div>

                    {{-- Stats --}}
                    <div class="flex flex-wrap gap-8 mt-14">
                        <div>
                            <p class="text-3xl font-black">500+</p>
                            <p class="text-purple-200 text-sm mt-1">Produk Terjual</p>
                        </div>
                        <div class="w-px bg-white/20"></div>
                        <div>
                            <p class="text-3xl font-black">200+</p>
                            <p class="text-purple-200 text-sm mt-1">Pelanggan Puas</p>
                        </div>
                        <div class="w-px bg-white/20"></div>
                        <div>
                            <p class="text-3xl font-black">4.9★</p>
                            <p class="text-purple-200 text-sm mt-1">Rating Toko</p>
                        </div>
                        <div class="w-px bg-white/20"></div>
                        <div>
                            <p class="text-3xl font-black">2 Thn</p>
                            <p class="text-purple-200 text-sm mt-1">Berpengalaman</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endguest

    @auth
        {{-- Welcome Banner --}}
        <section class="py-4">
            <div class="max-w-7xl mx-auto px-6">
                <div
                    class="bg-gradient-to-br from-primary to-indigo-700 rounded-3xl p-8 mb-8 text-white relative overflow-hidden">
                    <div class="absolute -right-12 -top-12 w-56 h-56 bg-white/10 rounded-full"></div>
                    <div class="absolute right-10 -bottom-10 w-36 h-36 bg-white/5 rounded-full"></div>
                    <div class="relative z-10 flex items-center justify-between flex-wrap gap-6">
                        <div>
                            <p class="text-purple-200 text-sm mb-1">👋 Selamat datang,</p>
                            <h1 class="text-3xl font-black mb-2">{{ auth()->user()->name }}</h1>
                            <p class="text-purple-200 text-sm">Temukan koleksi fashion terbaru Caysie buat kamu!</p>

                            <div class="flex gap-3 mt-5">
                                <a href="{{ route('user.shop') }}"
                                    class="bg-white text-primary font-black px-6 py-2.5 rounded-xl text-sm hover:bg-purple-50 transition shadow-lg">
                                    Belanja Sekarang
                                </a>
                                <a href="{{ route('user.orders') }}"
                                    class="bg-white/20 text-white font-bold px-6 py-2.5 rounded-xl text-sm border border-white/30 hover:bg-white/30 transition">
                                    Pesanan Saya
                                </a>
                            </div>
                        </div>
                        <div class="text-8xl opacity-80">🛍️</div>
                    </div>
                </div>
            </div>
        </section>
    @endauth

    {{-- Kategori --}}
    <section class="py-6 md:py-0">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Header --}}
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl md:text-2xl font-black text-gray-800">
                    Kategori
                </h2>

                <a href="{{ route('user.shop') }}"
                    class="flex items-center gap-1 text-sm font-bold text-primary 
                       hover:gap-2 transition-all group">
                    Lihat semua
                    <i class="fa-solid fa-arrow-right text-xs group-hover:translate-x-1 transition"></i>
                </a>
            </div>

            {{-- Grid --}}
            @php
                $catIcons = [
                    'kaos' => 'fa-shirt',
                    'celana' => 'fa-socks',
                    'jaket' => 'fa-vest',
                    'aksesoris' => 'fa-glasses',
                ];
            @endphp
            <div class="max-w-5xl mx-auto grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-6">

                @foreach (['kaos', 'celana', 'jaket', 'aksesoris'] as $cat)
                    @php $c = ['kaos' => 'purple', 'celana' => 'orange', 'jaket' => 'emerald', 'aksesoris' => 'yellow'][$cat]; @endphp
                    <a href="{{ route('user.shop', ['category' => $cat]) }}"
                        class="group bg-white rounded-2xl py-6 px-4 text-center 
                           border border-gray-100 shadow-sm
                           hover:shadow-lg hover:border-{{ $c }}-200
                           hover:-translate-y-1 
                           transition-all duration-300 ease-out">

                        <div
                            class="w-14 h-14 mx-auto mb-3 rounded-2xl bg-{{ $c }}-50 flex items-center justify-center
                               group-hover:scale-110 group-hover:bg-{{ $c }}-100 transition-all duration-300">
                            <i class="fa-solid {{ $catIcons[$cat] }} text-{{ $c }}-600 text-xl"></i>
                        </div>

                        <p
                            class="font-bold text-gray-700 text-sm capitalize 
                              group-hover:text-{{ $c }}-600 transition">
                            {{ $cat }}
                        </p>

                    </a>
                @endforeach

            </div>

        </div>
    </section>

    {{-- Produk Unggulan --}}
    @if ($featured->count())
        <section class="py-6 md:py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                {{-- Header --}}
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl md:text-2xl font-black text-gray-800">
                        ⭐ Produk Unggulan
                    </h2>

                    <a href="{{ route('user.shop') }}"
                        class="flex items-center gap-1 text-sm font-bold text-primary 
                           hover:gap-2 transition-all group">
                        Lihat semua
                        <i class="fa-solid fa-arrow-right text-xs group-hover:translate-x-1 transition"></i>
                    </a>
                </div>

                {{-- Grid --}}
                <div class="max-w-7xl mx-auto grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-6">
                    @foreach ($featured as $product)
                        @include('user.partials.product-card', ['product' => $product])
                    @endforeach
                </div>

            </div>
        </section>
    @endif


    {{-- Produk Terbaru --}}
    <section class="py-6 md:py-8 id="produk">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Header --}}
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl md:text-2xl font-black text-gray-800">
                    🔥 Produk Terbaru
                </h2>

                <a href="{{ route('user.shop') }}"
                    class="flex items-center gap-1 text-sm font-bold text-primary 
                       hover:gap-2 transition-all group">
                    Lihat semua
                    <i class="fa-solid fa-arrow-right text-xs group-hover:translate-x-1 transition"></i>
                </a>
            </div>

            {{-- Grid --}}
            <div class="max-w-7xl mx-auto grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-6">
                @foreach ($newArrivals as $product)
                    @include('user.partials.product-card', ['product' => $product])
                @endforeach
            </div>

        </div>
    </section>

    {{-- ========== BANNER PROMO ========== --}}
    <section class="py-4 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <div
                class="bg-gradient-to-r from-dark via-[#2d2b5e] to-[#1a1a4e] rounded-3xl p-10 flex flex-col md:flex-row items-center justify-between gap-8 overflow-hidden relative">
                <div class="absolute top-0 right-20 w-64 h-64 bg-primary/10 rounded-full -translate-y-32"></div>
                <div class="relative z-10">
                    <span class="text-xs font-bold text-purple-300 tracking-widest">PROMO SPESIAL</span>
                    <h3 class="text-3xl font-black text-white mt-2 mb-3">
                        Gratis Ongkir<br>se-Gunungkidul! 🎉
                    </h3>
                    <p class="text-gray-400 mb-6">Berlaku untuk semua produk, min. pembelian Rp100.000</p>
                    <a href="{{ route('register') }}"
                        class="inline-flex items-center gap-2 bg-primary text-white font-bold px-7 py-3 rounded-xl hover:bg-primary-dark transition shadow-xl shadow-purple-900/40 text-sm">
                        Klaim Sekarang <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
                <div class="text-9xl opacity-70 relative z-10">🚚</div>
            </div>
        </div>
    </section>

    {{-- ========== KEUNGGULAN ========== --}}
    <section id="tentang" class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-12">
                <p class="text-primary font-bold text-sm tracking-widest mb-2">KENAPA CAYSIE?</p>
                <h2 class="text-3xl font-black text-dark">Keunggulan Kami</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @php
                    $features = [
                        [
                            'icon' => 'fa-award',
                            'color' => 'text-purple-600',
                            'bg' => 'bg-purple-100',
                            'title' => 'Kualitas Terjamin',
                            'desc' =>
                                'Setiap produk dipilih dengan teliti. Bahan berkualitas, jahitan rapi, dan nyaman dipakai seharian.',
                        ],
                        [
                            'icon' => 'fa-truck-fast',
                            'color' => 'text-blue-600',
                            'bg' => 'bg-blue-100',
                            'title' => 'Pengiriman Cepat',
                            'desc' =>
                                'Pesanan diproses dalam 1x24 jam. Tersedia berbagai pilihan kurir dengan estimasi pengiriman yang akurat.',
                        ],
                        [
                            'icon' => 'fa-rotate-left',
                            'color' => 'text-green-600',
                            'bg' => 'bg-green-100',
                            'title' => 'Garansi Produk',
                            'desc' =>
                                'Tidak puas? Kami terima retur dalam 7 hari. Kepuasan pelanggan adalah prioritas utama kami.',
                        ],
                    ];
                @endphp
                @foreach ($features as $f)
                    <div class="bg-white rounded-2xl p-8 border border-gray-100 card-hover">
                        <div class="w-14 h-14 {{ $f['bg'] }} rounded-2xl flex items-center justify-center mb-6">
                            <i class="fa-solid {{ $f['icon'] }} {{ $f['color'] }} text-xl"></i>
                        </div>
                        <h3 class="font-bold text-gray-800 text-lg mb-3">{{ $f['title'] }}</h3>
                        <p class="text-gray-500 text-sm leading-relaxed">{{ $f['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ========== TESTIMONI ========== --}}
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-12">
                <p class="text-primary font-bold text-sm tracking-widest mb-2">TESTIMONI</p>
                <h2 class="text-3xl font-black text-dark">Kata Mereka 💬</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @php
                    $testimonials = [
                        [
                            'name' => 'Andi R.',
                            'loc' => 'Wonosari',
                            'rating' => 5,
                            'color' => 'bg-purple-500',
                            'text' =>
                                'Kualitasnya bagus banget, bahan adem dan jahitannya rapi. Sudah beli 3x dan selalu puas. Recommended banget!',
                        ],
                        [
                            'name' => 'Sari D.',
                            'loc' => 'Semanu',
                            'rating' => 5,
                            'color' => 'bg-orange-400',
                            'text' =>
                                'Pengiriman cepat, packing aman. Kaosnya sesuai gambar, warnanya juga bagus. Pasti bakal order lagi!',
                        ],
                        [
                            'name' => 'Rizky P.',
                            'loc' => 'Playen',
                            'rating' => 4,
                            'color' => 'bg-emerald-500',
                            'text' =>
                                'Harga terjangkau tapi kualitas gak kalah sama brand mahal. Celana joggernya enak banget dipake nongki.',
                        ],
                    ];
                @endphp
                @foreach ($testimonials as $t)
                    <div class="bg-gray-50 rounded-2xl p-7 border border-gray-100">
                        <div class="flex gap-1 text-yellow-400 mb-4">
                            @for ($i = 0; $i < $t['rating']; $i++)
                                <i class="fa-solid fa-star text-sm"></i>
                            @endfor
                            @for ($i = $t['rating']; $i < 5; $i++)
                                <i class="fa-regular fa-star text-sm text-gray-300"></i>
                            @endfor
                        </div>
                        <p class="text-gray-600 text-sm leading-relaxed mb-6 italic">"{{ $t['text'] }}"</p>
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 {{ $t['color'] }} rounded-full flex items-center justify-center text-white font-bold text-sm">
                                {{ strtoupper(substr($t['name'], 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-bold text-gray-800 text-sm">{{ $t['name'] }}</p>
                                <p class="text-gray-400 text-xs">{{ $t['loc'] }}, Gunungkidul</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ========== CTA AKHIR ========== --}}
    @guest
        <section class="py-16 bg-gradient-to-br from-primary to-[#3730a3] text-white">
            <div class="max-w-2xl mx-auto px-6 text-center">
                <h2 class="text-3xl font-black mb-4">Siap Tampil Keren? 🔥</h2>
                <p class="text-purple-200 mb-8 leading-relaxed">Daftar sekarang dan dapatkan akses ke semua koleksi terbaru
                    Caysie. Gratis selamanya!</p>
                <a href="{{ route('register') }}"
                    class="inline-flex items-center gap-3 bg-white text-primary font-black px-10 py-4 rounded-2xl hover:bg-purple-50 transition shadow-2xl shadow-purple-900/40 text-base">
                    <i class="fa-solid fa-user-plus"></i> Daftar Gratis Sekarang
                </a>
                <p class="text-purple-300 text-sm mt-4">Sudah punya akun? <a href="{{ route('login') }}"
                        class="text-white underline font-semibold">Masuk di sini</a></p>
            </div>
        </section>
    @endguest

@endsection
