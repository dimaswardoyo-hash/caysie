<nav class="bg-white shadow-sm sticky top-0 z-50 border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between gap-4">

        {{-- LOGO --}}
        <a href="{{ route('user.dashboard') }}" class="flex items-center gap-2.5 flex-shrink-0">
            <div class="w-9 h-9 bg-primary rounded-xl flex items-center justify-center shadow-md shadow-purple-200">
                <i class="fa-solid fa-shirt text-white text-sm"></i>
            </div>
            <span class="text-xl font-black text-gray-800 tracking-wide">CAYSIE</span>
        </a>

        {{-- MENU TENGAH --}}
        <div class="hidden md:flex items-center gap-7 flex-1 justify-center">
            <a href="{{ route('user.dashboard') }}"
                class="text-sm font-semibold transition hover:text-primary
                      {{ request()->routeIs('user.dashboard') ? 'text-primary' : 'text-gray-500' }}">
                Beranda
            </a>
            <a href="{{ route('user.shop') }}"
                class="text-sm font-semibold transition hover:text-primary
                      {{ request()->routeIs('user.shop*', 'user.product.*') ? 'text-primary' : 'text-gray-500' }}">
                Produk
            </a>
        </div>

        {{-- ICON KANAN --}}
        <div class="flex items-center gap-2">
            @auth
                {{-- ── IKON RIWAYAT PESANAN + DROPDOWN ── --}}
                <div class="nav-dropdown" id="order-nav-dropdown">
                    <button onclick="toggleDropdown('order-nav-dropdown')"
                        class="relative w-10 h-10 rounded-xl flex items-center justify-center transition
                           {{ request()->routeIs('user.orders*') ? 'bg-purple-100' : 'hover:bg-gray-100' }}"
                        title="Riwayat Pesanan">
                        <i
                            class="fa-solid fa-clipboard-list text-lg
                              {{ request()->routeIs('user.orders*') ? 'text-primary' : 'text-gray-500' }}"></i>

                        {{-- Badge jumlah pesanan aktif --}}
                        @if (isset($activeOrderCount) && $activeOrderCount > 0)
                            <span
                                class="absolute -top-1 -right-1 min-w-[18px] h-[18px] bg-yellow-500 text-white
                                 text-[9px] font-black rounded-full flex items-center justify-center px-1
                                 border-2 border-white shadow-sm">
                                {{ $activeOrderCount > 9 ? '9+' : $activeOrderCount }}
                            </span>
                        @endif
                    </button>

                    {{-- DROPDOWN RIWAYAT --}}
                    <div class="nav-dropdown-menu">

                        {{-- Header --}}
                        <div class="px-4 py-3.5 border-b border-gray-100 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <h3 class="font-black text-gray-800 text-sm">Riwayat Pembelian</h3>
                                @if (isset($activeOrderCount) && $activeOrderCount > 0)
                                    <span
                                        class="bg-yellow-100 text-yellow-700 text-[10px] font-bold px-2 py-0.5 rounded-full">
                                        {{ $activeOrderCount }} aktif
                                    </span>
                                @endif
                            </div>
                            <a href="{{ route('user.orders') }}" class="text-xs text-primary font-bold hover:underline">
                                Lihat semua →
                            </a>
                        </div>

                        {{-- Statistik mini --}}
                        @if (isset($orderStats))
                            <div class="grid grid-cols-3 gap-0 border-b border-gray-100">
                                <div class="py-3 text-center border-r border-gray-100">
                                    <p class="text-lg font-black text-primary">{{ $orderStats['active'] }}</p>
                                    <p class="text-[10px] text-gray-400 font-semibold mt-0.5">Aktif</p>
                                </div>
                                <div class="py-3 text-center border-r border-gray-100">
                                    <p class="text-lg font-black text-green-500">{{ $orderStats['delivered'] }}</p>
                                    <p class="text-[10px] text-gray-400 font-semibold mt-0.5">Selesai</p>
                                </div>
                                <div class="py-3 text-center">
                                    <p class="text-lg font-black text-yellow-500">{{ $orderStats['pending'] }}</p>
                                    <p class="text-[10px] text-gray-400 font-semibold mt-0.5">Perlu Bayar</p>
                                </div>
                            </div>
                        @endif

                        {{-- Daftar 3 pesanan terbaru --}}
                        <div class="p-2 max-h-80 overflow-y-auto">
                            @if (isset($recentOrders) && $recentOrders->count())
                                @foreach ($recentOrders as $ord)
                                    @php
                                        $sc = [
                                            'pending' => [
                                                'dot' => 'bg-yellow-400',
                                                'bg' => 'bg-yellow-50',
                                                'border' => 'border-yellow-200',
                                                'label' => 'Menunggu Bayar',
                                                'text' => 'text-yellow-800',
                                            ],
                                            'paid' => [
                                                'dot' => 'bg-blue-400',
                                                'bg' => 'bg-blue-50',
                                                'border' => 'border-blue-200',
                                                'label' => 'Sudah Dibayar',
                                                'text' => 'text-blue-800',
                                            ],
                                            'processing' => [
                                                'dot' => 'bg-purple-400',
                                                'bg' => 'bg-purple-50',
                                                'border' => 'border-purple-200',
                                                'label' => 'Diproses',
                                                'text' => 'text-purple-800',
                                            ],
                                            'shipped' => [
                                                'dot' => 'bg-blue-500',
                                                'bg' => 'bg-blue-50',
                                                'border' => 'border-blue-200',
                                                'label' => 'Dikirim',
                                                'text' => 'text-blue-900',
                                            ],
                                            'delivered' => [
                                                'dot' => 'bg-green-400',
                                                'bg' => 'bg-white',
                                                'border' => 'border-gray-200',
                                                'label' => 'Selesai',
                                                'text' => 'text-green-800',
                                            ],
                                            'cancelled' => [
                                                'dot' => 'bg-red-400',
                                                'bg' => 'bg-white',
                                                'border' => 'border-gray-100',
                                                'label' => 'Dibatalkan',
                                                'text' => 'text-red-700',
                                            ],
                                        ][$ord->status] ?? [
                                            'dot' => 'bg-gray-400',
                                            'bg' => 'bg-white',
                                            'border' => 'border-gray-100',
                                            'label' => $ord->status,
                                            'text' => 'text-gray-700',
                                        ];
                                    @endphp

                                    <a href="{{ route('user.orders.show', $ord) }}"
                                        class="block p-3 rounded-xl mb-1.5 border {{ $sc['border'] }} {{ $sc['bg'] }}
                                  hover:opacity-90 transition group">

                                        <div class="flex items-center justify-between mb-1.5">
                                            <div class="flex items-center gap-1.5">
                                                <span class="w-2 h-2 rounded-full {{ $sc['dot'] }}"></span>
                                                <span
                                                    class="text-[10px] font-bold {{ $sc['text'] }}">{{ $sc['label'] }}</span>
                                            </div>
                                            <span class="text-[10px] text-gray-400">
                                                {{ $ord->created_at->diffForHumans() }}
                                            </span>
                                        </div>

                                        <div class="flex items-center justify-between">
                                            <div class="min-w-0 flex-1">
                                                <p class="text-xs font-black text-gray-800">{{ $ord->order_code }}</p>
                                                <p class="text-[10px] text-gray-400 truncate mt-0.5">
                                                    @if ($ord->items->count())
                                                        {{ $ord->items->first()->product_name }}
                                                        @if ($ord->items->count() > 1)
                                                            <span class="text-gray-400">+{{ $ord->items->count() - 1 }}
                                                                lainnya</span>
                                                        @endif
                                                    @endif
                                                </p>
                                            </div>
                                            <div class="text-right flex-shrink-0 ml-3">
                                                <p class="text-xs font-black text-primary">{{ $ord->formatted_total }}</p>
                                            </div>
                                        </div>

                                        {{-- Countdown jika pending & belum expired --}}
                                        @if ($ord->status === 'pending' && $ord->payment_deadline && !$ord->isPaymentExpired() && !$ord->payment_proof)
                                            <div
                                                class="mt-2 flex items-center justify-between bg-white rounded-lg px-2.5 py-1.5
                                        border border-yellow-200">
                                                <span class="text-[10px] text-yellow-600 font-semibold">
                                                    <i class="fa-solid fa-clock mr-1"></i>Bayar sebelum habis
                                                </span>
                                                <span
                                                    class="text-[10px] font-black text-yellow-700 font-mono tracking-wider
                                             navbar-countdown"
                                                    data-seconds="{{ $ord->payment_seconds_left }}">
                                                    {{ gmdate('H:i:s', $ord->payment_seconds_left) }}
                                                </span>
                                            </div>
                                            <div class="mt-2 flex gap-1.5" onclick="event.preventDefault()">
                                                <a href="{{ route('user.orders.show', $ord) }}"
                                                    class="flex-1 text-center text-[10px] font-bold bg-primary text-white
                                          py-1.5 rounded-lg hover:bg-primary-dark transition">
                                                    Bayar Sekarang
                                                </a>
                                            </div>
                                        @endif

                                        {{-- Tombol beli lagi jika delivered --}}
                                        @if ($ord->status === 'delivered')
                                            <div class="mt-2 flex gap-1.5" onclick="event.preventDefault()">
                                                <form action="{{ route('user.orders.reorder', $ord) }}" method="POST"
                                                    class="flex-1">
                                                    @csrf
                                                    <button type="submit"
                                                        class="w-full text-[10px] font-bold bg-purple-50 text-purple-700
                                               border border-purple-200 py-1.5 rounded-lg hover:bg-purple-100 transition">
                                                        <i class="fa-solid fa-rotate-right mr-1"></i> Beli Lagi
                                                    </button>
                                                </form>
                                            </div>
                                        @endif

                                    </a>
                                @endforeach
                            @else
                                <div class="py-10 text-center text-gray-400">
                                    <i class="fa-solid fa-clipboard-list text-3xl mb-2 opacity-30"></i>
                                    <p class="text-xs font-semibold">Belum ada pesanan</p>
                                </div>
                            @endif
                        </div>

                        {{-- Footer dropdown --}}
                        <div class="px-3 py-2.5 border-t border-gray-100 bg-gray-50">
                            <a href="{{ route('user.orders') }}"
                                class="flex items-center justify-center gap-2 w-full py-2.5 bg-primary
                                  text-white rounded-xl text-xs font-bold hover:bg-primary-dark transition
                                  shadow-md shadow-purple-200">
                                <i class="fa-solid fa-list text-xs"></i>
                                Lihat Semua Riwayat Pesanan
                            </a>
                        </div>

                    </div>
                </div>
                {{-- ── END DROPDOWN RIWAYAT ── --}}

                {{-- KERANJANG --}}
                <a href="{{ route('user.cart') }}"
                    class="relative w-10 h-10 rounded-xl flex items-center justify-center transition
                      {{ request()->routeIs('user.cart') ? 'bg-purple-100' : 'hover:bg-gray-100' }}"
                    title="Keranjang">
                    <i
                        class="fa-solid fa-cart-shopping text-lg
                          {{ request()->routeIs('user.cart') ? 'text-primary' : 'text-gray-500' }}"></i>
                    @if (isset($cartCount) && $cartCount > 0)
                        <span
                            class="absolute -top-1 -right-1 min-w-[18px] h-[18px] bg-primary text-white
                             text-[9px] font-black rounded-full flex items-center justify-center px-1
                             border-2 border-white shadow-sm">
                            {{ $cartCount > 9 ? '9+' : $cartCount }}
                        </span>
                    @endif
                </a>

                {{-- PROFIL DROPDOWN --}}
                <div class="profile-dropdown hidden md:block" id="profile-nav-dropdown">
                    <button onclick="toggleDropdown('profile-nav-dropdown')"
                        class="flex items-center gap-2 pl-1 pr-3 py-1 rounded-xl hover:bg-gray-100 transition group">
                        <div
                            class="w-8 h-8 bg-primary rounded-full flex items-center justify-center
                                text-white font-black text-sm shadow-md shadow-purple-200">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <span class="text-sm font-semibold text-gray-700 max-w-24 truncate hidden lg:block">
                            {{ auth()->user()->name }}
                        </span>
                        <i class="fa-solid fa-chevron-down text-xs text-gray-400 group-hover:text-gray-600 transition"></i>
                    </button>

                    <div class="profile-dropdown-menu">
                        {{-- Info user --}}
                        <div class="px-4 py-4 border-b border-gray-100">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 bg-primary rounded-full flex items-center justify-center
                                        text-white font-black text-base shadow-md shadow-purple-200">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="font-black text-gray-800 text-sm truncate">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-gray-400 truncate">{{ auth()->user()->email }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Menu profil --}}
                        <div class="p-2">
                            <a href="{{ route('user.dashboard') }}"
                                class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-gray-50 transition group">
                                <span
                                    class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center
                                         group-hover:bg-purple-100 transition">
                                    <i
                                        class="fa-solid fa-house text-gray-500 group-hover:text-primary text-xs transition"></i>
                                </span>
                                <span class="text-sm font-semibold text-gray-700">Beranda</span>
                            </a>
                            <a href="{{ route('user.orders') }}"
                                class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-gray-50 transition group">
                                <span
                                    class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center
                                         group-hover:bg-purple-100 transition">
                                    <i
                                        class="fa-solid fa-receipt text-gray-500 group-hover:text-primary text-xs transition"></i>
                                </span>
                                <div class="flex-1 flex items-center justify-between">
                                    <span class="text-sm font-semibold text-gray-700">Pesanan Saya</span>
                                    @if (isset($activeOrderCount) && $activeOrderCount > 0)
                                        <span
                                            class="bg-yellow-100 text-yellow-700 text-[10px] font-bold px-2 py-0.5 rounded-full">
                                            {{ $activeOrderCount }}
                                        </span>
                                    @endif
                                </div>
                            </a>
                            <a href="{{ route('user.cart') }}"
                                class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-gray-50 transition group">
                                <span
                                    class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center
                                         group-hover:bg-purple-100 transition">
                                    <i
                                        class="fa-solid fa-cart-shopping text-gray-500 group-hover:text-primary text-xs transition"></i>
                                </span>
                                <div class="flex-1 flex items-center justify-between">
                                    <span class="text-sm font-semibold text-gray-700">Keranjang</span>
                                    @if (isset($cartCount) && $cartCount > 0)
                                        <span
                                            class="bg-purple-100 text-purple-700 text-[10px] font-bold px-2 py-0.5 rounded-full">
                                            {{ $cartCount }}
                                        </span>
                                    @endif
                                </div>
                            </a>
                        </div>

                        {{-- Logout --}}
                        <div class="p-2 pt-0 border-t border-gray-100 mt-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl
                                       hover:bg-red-50 transition group text-left">
                                    <span
                                        class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center
                                             group-hover:bg-red-100 transition">
                                        <i
                                            class="fa-solid fa-right-from-bracket text-gray-500 group-hover:text-red-500 text-xs transition"></i>
                                    </span>
                                    <span class="text-sm font-semibold text-gray-700 group-hover:text-red-600 transition">
                                        Keluar
                                    </span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endauth
            @guest
                <a href="{{ route('login') }}"
                    class="text-sm font-semibold text-gray-600 hover:text-primary border border-gray-200 hover:border-primary px-4 py-2 rounded-xl transition">
                    Masuk
                </a>

                <a href="{{ route('register') }}"
                    class="text-sm font-semibold text-white bg-primary hover:bg-primary-dark px-5 py-2 rounded-xl transition shadow-md shadow-purple-200">
                    Daftar Gratis
                </a>
            @endguest
            {{-- Mobile: burger --}}
            <button
                class="md:hidden w-10 h-10 rounded-xl flex items-center justify-center hover:bg-gray-100 transition"
                onclick="toggleMobileMenu()">
                <i class="fa-solid fa-bars text-gray-600" id="mobile-menu-icon"></i>
            </button>
        </div>
    </div>

    {{-- MOBILE MENU --}}
    <div id="mobile-menu" class="hidden md:hidden border-t border-gray-100 bg-white">
        <div class="px-4 py-3 space-y-1">
            <a href="{{ route('user.dashboard') }}"
                class="flex items-center gap-3 px-3 py-3 rounded-xl hover:bg-gray-50 transition
                      {{ request()->routeIs('user.dashboard') ? 'bg-purple-50 text-primary' : 'text-gray-600' }}">
                <i class="fa-solid fa-house text-sm w-4"></i>
                <span class="text-sm font-semibold">Beranda</span>
            </a>
            <a href="{{ route('user.shop') }}"
                class="flex items-center gap-3 px-3 py-3 rounded-xl hover:bg-gray-50 transition
                      {{ request()->routeIs('user.shop*') ? 'bg-purple-50 text-primary' : 'text-gray-600' }}">
                <i class="fa-solid fa-bag-shopping text-sm w-4"></i>
                <span class="text-sm font-semibold">Produk</span>
            </a>
            <a href="{{ route('user.orders') }}"
                class="flex items-center gap-3 px-3 py-3 rounded-xl hover:bg-gray-50 transition
                      {{ request()->routeIs('user.orders*') ? 'bg-purple-50 text-primary' : 'text-gray-600' }}">
                <i class="fa-solid fa-clipboard-list text-sm w-4"></i>
                <div class="flex-1 flex items-center justify-between">
                    <span class="text-sm font-semibold">Riwayat Pesanan</span>
                    @if (isset($activeOrderCount) && $activeOrderCount > 0)
                        <span class="bg-yellow-100 text-yellow-700 text-xs font-bold px-2 py-0.5 rounded-full">
                            {{ $activeOrderCount }} aktif
                        </span>
                    @endif
                </div>
            </a>
            <a href="{{ route('user.cart') }}"
                class="flex items-center gap-3 px-3 py-3 rounded-xl hover:bg-gray-50 transition
                      {{ request()->routeIs('user.cart') ? 'bg-purple-50 text-primary' : 'text-gray-600' }}">
                <i class="fa-solid fa-cart-shopping text-sm w-4"></i>
                <div class="flex-1 flex items-center justify-between">
                    <span class="text-sm font-semibold">Keranjang</span>
                    @if (isset($cartCount) && $cartCount > 0)
                        <span class="bg-purple-100 text-purple-700 text-xs font-bold px-2 py-0.5 rounded-full">
                            {{ $cartCount }}
                        </span>
                    @endif
                </div>
            </a>
            <div class="pt-2 border-t border-gray-100">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center gap-3 px-3 py-3 rounded-xl hover:bg-red-50 text-left transition">
                        <i class="fa-solid fa-right-from-bracket text-red-400 text-sm w-4"></i>
                        <span class="text-sm font-semibold text-red-500">Keluar</span>
                    </button>
                </form>
            </div>

            @guest
                <a href="{{ route('login') }}"
                    class="text-sm font-semibold text-center text-primary border border-primary py-2 rounded-xl">
                    Masuk
                </a>

                <a href="{{ route('register') }}"
                    class="text-sm font-semibold text-center text-white bg-primary py-2 rounded-xl">
                    Daftar Gratis
                </a>
            @endguest
        </div>
    </div>
</nav>
