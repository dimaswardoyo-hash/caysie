<nav class="bg-white shadow-sm sticky top-0 z-50 border-b border-gray-100" x-data="{ open: false }">
    <div class="max-w-7xl mx-auto px-6 py-0">
        <div class="flex items-center justify-between h-16">

            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <div class="w-9 h-9 bg-primary rounded-xl flex items-center justify-center shadow-md">
                    <i class="fa-solid fa-shirt text-white text-sm"></i>
                </div>
                <span class="text-xl font-black tracking-wide text-dark">CAYSIE</span>
            </a>

            {{-- Menu Tengah --}}
            <div class="hidden md:flex items-center gap-8">
                <a href="{{ route('home') }}"
                    class="nav-link text-sm font-semibold {{ request()->routeIs('home') ? 'text-primary' : 'text-gray-600 hover:text-primary' }} transition">
                    Beranda
                </a>
                <a href="#produk" class="nav-link text-sm font-semibold text-gray-600 hover:text-primary transition">
                    Produk
                </a>
                <a href="#tentang" class="nav-link text-sm font-semibold text-gray-600 hover:text-primary transition">
                    Tentang
                </a>
                <a href="#kontak" class="nav-link text-sm font-semibold text-gray-600 hover:text-primary transition">
                    Kontak
                </a>
            </div>

            {{-- Auth Buttons --}}
            <div class="hidden md:flex items-center gap-5">
                @auth
                    {{-- Sudah login → ke dashboard --}}
                    {{-- Keranjang --}}
                    <a href="{{ route('user.cart') }}" class="relative">
                        <div class="w-10 h-10 flex items-center justify-center rounded-xl hover:bg-gray-100 transition">
                            <i class="fa-solid fa-cart-shopping text-gray-600 text-lg"></i>
                        </div>
                        @if (isset($cartCount) && $cartCount > 0)
                            <span
                                class="absolute -top-1 -right-1 w-5 h-5 bg-primary text-white text-xs font-black rounded-full flex items-center justify-center">
                                {{ $cartCount > 9 ? '9+' : $cartCount }}
                            </span>
                        @endif
                    </a>
                    {{-- Avatar --}}
                    <div class="flex items-center gap-2">
                        <div
                            class="w-9 h-9 bg-primary rounded-full flex items-center justify-center text-white text-sm font-black">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <span class="hidden md:block text-sm font-semibold text-gray-700 max-w-28 truncate">
                            {{ auth()->user()->name }}
                        </span>
                    </div>
                    {{-- Logout --}}
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button
                            class="text-gray-400 hover:text-red-500 transition w-8 h-8 flex items-center justify-center rounded-lg hover:bg-red-50">
                            <i class="fa-solid fa-right-from-bracket text-sm"></i>
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}"
                        class="text-sm font-semibold text-gray-600 hover:text-primary border border-gray-200 hover:border-primary px-4 py-2 rounded-xl transition">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}"
                        class="text-sm font-semibold text-white bg-primary hover:bg-primary-dark px-5 py-2 rounded-xl transition shadow-md shadow-purple-200">
                        Daftar Gratis
                    </a>
                @endauth
            </div>

            {{-- Mobile Hamburger --}}
            <button class="md:hidden p-2 rounded-lg hover:bg-gray-100"
                onclick="this.nextElementSibling.classList.toggle('hidden')">
                <i class="fa-solid fa-bars text-gray-600"></i>
            </button>
        </div>

        {{-- Mobile Menu --}}
        <div class="hidden md:hidden pb-4 border-t border-gray-100 pt-4">
            <div class="flex flex-col gap-3">
                <a href="{{ route('home') }}" class="text-sm font-semibold text-gray-600 py-2">Beranda</a>
                <a href="#produk" class="text-sm font-semibold text-gray-600 py-2">Produk</a>
                <a href="#tentang" class="text-sm font-semibold text-gray-600 py-2">Tentang</a>
                <a href="#kontak" class="text-sm font-semibold text-gray-600 py-2">Kontak</a>
                <hr>
                @guest
                    <a href="{{ route('login') }}"
                        class="text-sm font-semibold text-center text-primary border border-primary py-2 rounded-xl">Masuk</a>
                    <a href="{{ route('register') }}"
                        class="text-sm font-semibold text-center text-white bg-primary py-2 rounded-xl">Daftar
                        Gratis</a>
                @endguest
            </div>
        </div>
    </div>
</nav>
