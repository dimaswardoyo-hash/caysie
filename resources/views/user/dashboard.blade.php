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

            @if (empty($testimonials) ||
                    (is_object($testimonials) && method_exists($testimonials, 'isEmpty') && $testimonials->isEmpty()))
                <div class="max-w-md mx-auto text-center py-6">
                    <div class="w-16 h-16 bg-gray-50 rounded-3xl flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-comment-dots text-2xl text-gray-300"></i>
                    </div>
                    <p class="font-bold text-gray-600 text-sm">Belum ada testimoni</p>
                    <p class="text-gray-400 text-xs mt-1">Jadilah pelanggan pertama yang berbagi pengalaman!</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @php $avatarColors = ['bg-purple-500', 'bg-orange-400', 'bg-emerald-500', 'bg-pink-500', 'bg-blue-500', 'bg-amber-500']; @endphp
                    @foreach ($testimonials as $t)
                        <div class="bg-gray-50 rounded-2xl p-7 border border-gray-100">
                            <div class="flex gap-1 text-yellow-400 mb-4">
                                @for ($i = 0; $i < $t->rating; $i++)
                                    <i class="fa-solid fa-star text-sm"></i>
                                @endfor
                                @for ($i = $t->rating; $i < 5; $i++)
                                    <i class="fa-regular fa-star text-sm text-gray-300"></i>
                                @endfor
                            </div>
                            <p class="text-gray-600 text-sm leading-relaxed mb-6 italic">"{{ $t->message }}"</p>
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 {{ $avatarColors[$loop->index % count($avatarColors)] }} rounded-full flex items-center justify-center text-white font-bold text-sm">
                                    {{ strtoupper(substr($t->user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-bold text-gray-800 text-sm">{{ $t->user->name }}</p>
                                    <p class="text-gray-400 text-xs">{{ $t->created_at->translatedFormat('d F Y') }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Form testimoni (khusus user login) --}}
            @auth
                <div class="max-w-xl mx-auto mt-10 bg-gray-50 border border-gray-100 rounded-2xl p-6">
                    <h3 class="font-black text-dark text-sm mb-1">
                        {{ $myTestimonial ? 'Perbarui Testimonimu' : 'Bagikan Pengalamanmu' }}
                    </h3>
                    <p class="text-gray-400 text-xs mb-4">
                        {{ $myTestimonial ? 'Ubah rating atau ulasanmu kapan saja.' : 'Ceritakan pengalaman belanjamu di Caysie.' }}
                    </p>

                    <form method="POST" action="{{ route('user.testimoni.store') }}" class="space-y-4">
                        @csrf

                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-1.5">Rating</label>
                            <div class="flex items-center gap-3 flex-wrap">
                                <div class="flex items-center gap-1" id="testimoni-star-picker"
                                    onmouseleave="resetTestimoniHover()">
                                    @php
                                        $currentRating = (int) old('rating', $myTestimonial->rating ?? 0);
                                        $ratingLabels = [
                                            1 => 'Sangat Kurang',
                                            2 => 'Kurang',
                                            3 => 'Cukup',
                                            4 => 'Bagus',
                                            5 => 'Sangat Bagus',
                                        ];
                                    @endphp
                                    @for ($i = 1; $i <= 5; $i++)
                                        <button type="button" onclick="setTestimoniRating({{ $i }})"
                                            onmouseenter="previewTestimoniRating({{ $i }})"
                                            class="testimoni-star text-2xl leading-none transition-transform hover:scale-110 {{ $i <= $currentRating ? 'text-yellow-400' : 'text-gray-300' }}">
                                            <i class="fa-solid fa-star"></i>
                                        </button>
                                    @endfor
                                </div>
                                <span id="testimoni-rating-label" class="text-xs font-bold text-gray-500">
                                    {{ $currentRating > 0 ? $currentRating . '/5 — ' . $ratingLabels[$currentRating] : 'Ketuk bintang untuk memilih rating' }}
                                </span>
                            </div>
                            <input type="hidden" name="rating" id="testimoni-rating-input" value="{{ $currentRating }}">
                            @error('rating')
                                <p class="text-xs text-red-500 font-semibold mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="testimoni-message"
                                class="block text-xs font-bold text-gray-500 mb-1.5">Ulasanmu</label>
                            <textarea id="testimoni-message" name="message" rows="3" maxlength="500"
                                placeholder="Ceritakan pengalaman belanjamu di Caysie..."
                                class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-800 focus:outline-none focus:border-primary transition @error('message') border-red-300 @enderror">{{ old('message', $myTestimonial->message ?? '') }}</textarea>
                            @error('message')
                                <p class="text-xs text-red-500 font-semibold mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit"
                            class="inline-flex items-center gap-2 bg-gradient-to-r from-primary to-primary-dark text-white text-sm font-black px-6 py-2.5 rounded-xl hover:opacity-90 transition shadow-lg shadow-primary/25">
                            <i class="fa-solid fa-paper-plane"></i>
                            {{ $myTestimonial ? 'Perbarui Testimoni' : 'Kirim Testimoni' }}
                        </button>
                    </form>

                    @if ($myTestimonial)
                        <form method="POST" action="{{ route('user.testimoni.destroy') }}"
                            onsubmit="return confirm('Hapus testimonimu?')" class="mt-2">
                            @csrf
                            @method('delete')
                            <button type="submit" class="text-xs font-bold text-red-500 hover:text-red-600 transition">
                                <i class="fa-solid fa-trash-can mr-1"></i> Hapus testimoni saya
                            </button>
                        </form>
                    @endif
                </div>

                @push('scripts')
                    <script>
                        const testimoniRatingLabels = {
                            1: 'Sangat Kurang',
                            2: 'Kurang',
                            3: 'Cukup',
                            4: 'Bagus',
                            5: 'Sangat Bagus',
                        };

                        function paintTestimoniStars(n) {
                            document.querySelectorAll('.testimoni-star').forEach((el, idx) => {
                                el.classList.toggle('text-yellow-400', idx < n);
                                el.classList.toggle('text-gray-300', idx >= n);
                            });
                        }

                        function updateTestimoniLabel(n) {
                            const label = document.getElementById('testimoni-rating-label');
                            if (!label) return;
                            label.textContent = n > 0 ? `${n}/5 — ${testimoniRatingLabels[n]}` : 'Ketuk bintang untuk memilih rating';
                        }

                        function setTestimoniRating(n) {
                            document.getElementById('testimoni-rating-input').value = n;
                            paintTestimoniStars(n);
                            updateTestimoniLabel(n);
                        }

                        function previewTestimoniRating(n) {
                            paintTestimoniStars(n);
                            updateTestimoniLabel(n);
                        }

                        function resetTestimoniHover() {
                            const current = parseInt(document.getElementById('testimoni-rating-input')?.value || 0);
                            paintTestimoniStars(current);
                            updateTestimoniLabel(current);
                        }
                    </script>
                @endpush
            @else
                <div class="max-w-md mx-auto mt-10 bg-gray-50 border border-gray-100 rounded-2xl p-6 text-center">
                    <i class="fa-solid fa-comment-dots text-primary text-2xl mb-3"></i>
                    <p class="font-bold text-gray-700 text-sm mb-1">Punya pengalaman belanja di Caysie?</p>
                    <p class="text-gray-400 text-xs mb-4">Masuk terlebih dahulu untuk membagikan testimonimu.</p>
                    <a href="{{ route('login') }}"
                        class="inline-flex items-center gap-2 bg-gradient-to-r from-primary to-primary-dark text-white text-sm font-black px-6 py-2.5 rounded-xl hover:opacity-90 transition shadow-lg shadow-primary/25">
                        <i class="fa-solid fa-right-to-bracket"></i> Masuk untuk Memberi Testimoni
                    </a>
                </div>
            @endauth
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
