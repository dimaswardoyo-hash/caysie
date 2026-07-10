<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caysie Admin - @yield('title')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#6C63FF',
                        secondary: '#48BB78',
                        dark: '#1A202C',
                    }
                }
            }
        }
    </script>
    <style>
        .sidebar-link:hover {
            background: rgba(108, 99, 255, 0.15);
        }

        .sidebar-link.active {
            background: rgba(108, 99, 255, 0.25);
            border-left: 4px solid #6C63FF;
        }

        .card-hover {
            transition: transform .2s, box-shadow .2s;
        }

        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, .12);
        }

        /* Mobile sidebar off-canvas behaviour */
        @media (max-width: 1023.98px) {
            #admin-sidebar {
                position: fixed;
                inset: 0 auto 0 0;
                z-index: 50;
                transform: translateX(-100%);
                transition: transform .3s ease-in-out;
            }

            #admin-sidebar.sidebar-open {
                transform: translateX(0);
            }
        }
    </style>
</head>

<body class="bg-gray-100 font-sans overflow-x-hidden">

    <div class="flex h-screen overflow-hidden relative">

        {{-- MOBILE OVERLAY --}}
        <div id="sidebar-overlay" onclick="closeSidebar()"
            class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden transition-opacity"></div>

        {{-- SIDEBAR --}}
        <aside id="admin-sidebar"
            class="w-64 sm:w-72 lg:w-64 bg-gray-900 text-white flex flex-col flex-shrink-0 h-full">
            {{-- Logo --}}
            <div class="p-5 sm:p-6 border-b border-gray-700 flex items-center justify-between">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-10 h-10 bg-primary rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-shirt text-white text-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <h1 class="text-xl font-bold tracking-wide truncate">CAYSIE</h1>
                        <p class="text-xs text-gray-400 truncate">Admin Panel</p>
                    </div>
                </div>
                {{-- Close button (mobile only) --}}
                <button onclick="closeSidebar()"
                    class="lg:hidden w-9 h-9 flex-shrink-0 flex items-center justify-center text-gray-400 hover:text-white hover:bg-white/10 rounded-lg transition"
                    aria-label="Tutup menu">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            {{-- Nav --}}
            <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
                <p class="text-xs text-gray-500 uppercase tracking-wider mb-3 px-3">Menu Utama</p>

                <a href="{{ route('admin.dashboard') }}" onclick="closeSidebar()"
                    class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}
              flex items-center gap-3 px-4 py-3 rounded-lg text-gray-300 hover:text-white transition">
                    <i class="fa-solid fa-gauge-high w-5"></i><span>Dashboard</span>
                </a>

                <a href="{{ route('admin.products.index') }}" onclick="closeSidebar()"
                    class="sidebar-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}
              flex items-center gap-3 px-4 py-3 rounded-lg text-gray-300 hover:text-white transition">
                    <i class="fa-solid fa-box-open w-5"></i><span>Produk</span>
                </a>

                <a href="{{ route('admin.orders.index') }}" onclick="closeSidebar()"
                    class="sidebar-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}
              flex items-center gap-3 px-4 py-3 rounded-lg text-gray-300 hover:text-white transition">
                    <i class="fa-solid fa-receipt w-5"></i>
                    <span>Pesanan</span>
                    @php $pending = \App\Models\Order::where('status','pending')->count(); @endphp
                    @if ($pending > 0)
                        <span
                            class="ml-auto bg-red-500 text-white text-xs font-black w-5 h-5 rounded-full flex items-center justify-center">
                            {{ $pending > 9 ? '9+' : $pending }}
                        </span>
                    @endif
                </a>

                <a href="{{ route('admin.revenue.index') }}" onclick="closeSidebar()"
                    class="sidebar-link {{ request()->routeIs('admin.revenue.*') ? 'active' : '' }}
              flex items-center gap-3 px-4 py-3 rounded-lg text-gray-300 hover:text-white transition">
                    <i class="fa-solid fa-chart-line w-5"></i><span>Pemasukan</span>
                </a>

                <a href="{{ route('admin.users.index') }}" onclick="closeSidebar()"
                    class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}
              flex items-center gap-3 px-4 py-3 rounded-lg text-gray-300 hover:text-white transition">
                    <i class="fa-solid fa-users w-5"></i><span>Kelola User</span>
                </a>
            </nav>

            {{-- User Info --}}
            <div class="p-4 border-t border-gray-700">
                <div class="flex items-center gap-3 mb-3">
                    <div
                        class="w-9 h-9 bg-primary rounded-full flex items-center justify-center text-sm font-bold flex-shrink-0">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-400">Administrator</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center gap-2 px-3 py-2 text-sm text-gray-400 hover:text-red-400 hover:bg-red-400/10 rounded-lg transition">
                        <i class="fa-solid fa-right-from-bracket"></i> Logout
                    </button>
                </form>
            </div>
        </aside>

        {{-- MAIN CONTENT --}}
        <div class="flex-1 flex flex-col overflow-hidden min-w-0">
            {{-- Topbar --}}
            <header
                class="bg-white shadow-sm px-4 sm:px-6 lg:px-8 py-3 sm:py-4 flex items-center justify-between gap-3 flex-shrink-0">
                <div class="flex items-center gap-3 min-w-0">
                    {{-- Hamburger (mobile only) --}}
                    <button onclick="openSidebar()"
                        class="lg:hidden w-10 h-10 flex-shrink-0 flex items-center justify-center text-gray-600 hover:text-primary hover:bg-purple-50 rounded-lg transition"
                        aria-label="Buka menu">
                        <i class="fa-solid fa-bars text-lg"></i>
                    </button>
                    <div class="min-w-0">
                        <h2 class="text-base sm:text-xl font-bold text-gray-800 truncate">@yield('title', 'Dashboard')
                        </h2>
                        <p class="hidden sm:block text-sm text-gray-500">{{ now()->isoFormat('dddd, D MMMM Y') }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 flex-shrink-0">
                    <span
                        class="bg-purple-100 text-purple-700 text-xs font-semibold px-2.5 sm:px-3 py-1 rounded-full whitespace-nowrap">
                        <i class="fa-solid fa-shield-halved sm:mr-1"></i><span class="hidden sm:inline">Admin</span>
                    </span>
                </div>
            </header>

            {{-- Page Content --}}
            <main class="flex-1 overflow-y-auto overflow-x-hidden p-4 sm:p-6 lg:p-8">
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        function openSidebar() {
            document.getElementById('admin-sidebar').classList.add('sidebar-open');
            document.getElementById('sidebar-overlay').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeSidebar() {
            document.getElementById('admin-sidebar').classList.remove('sidebar-open');
            document.getElementById('sidebar-overlay').classList.add('hidden');
            document.body.style.overflow = '';
        }

        // Reset sidebar state when resizing back to desktop
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 1024) {
                closeSidebar();
            }
        });

        // Close on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeSidebar();
        });
    </script>

    @stack('scripts')
</body>

</html>
