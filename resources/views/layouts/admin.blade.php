<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
    </style>
</head>

<body class="bg-gray-100 font-sans">

    {{-- Flash Messages (popup, auto-dismiss, konsisten untuk success/error/warning/status) --}}
    @include('components.flash-toast')

    <div class="flex h-screen overflow-hidden">

        {{-- SIDEBAR --}}
        <aside class="w-64 bg-gray-900 text-white flex flex-col flex-shrink-0">
            {{-- Logo --}}
            <div class="p-6 border-b border-gray-700">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-primary rounded-xl flex items-center justify-center">
                        <i class="fa-solid fa-shirt text-white text-lg"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold tracking-wide">CAYSIE</h1>
                        <p class="text-xs text-gray-400">Admin Panel</p>
                    </div>
                </div>
            </div>

            {{-- Nav --}}
            <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
                <p class="text-xs text-gray-500 uppercase tracking-wider mb-3 px-3">Menu Utama</p>

                <a href="{{ route('admin.dashboard') }}"
                    class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}
              flex items-center gap-3 px-4 py-3 rounded-lg text-gray-300 hover:text-white transition">
                    <i class="fa-solid fa-gauge-high w-5"></i><span>Dashboard</span>
                </a>

                <a href="{{ route('admin.products.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}
              flex items-center gap-3 px-4 py-3 rounded-lg text-gray-300 hover:text-white transition">
                    <i class="fa-solid fa-box-open w-5"></i><span>Produk</span>
                </a>

                <a href="{{ route('admin.orders.index') }}"
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

                <a href="{{ route('admin.revenue.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.revenue.*') ? 'active' : '' }}
              flex items-center gap-3 px-4 py-3 rounded-lg text-gray-300 hover:text-white transition">
                    <i class="fa-solid fa-chart-line w-5"></i><span>Pemasukan</span>
                </a>

                <a href="{{ route('admin.users.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}
              flex items-center gap-3 px-4 py-3 rounded-lg text-gray-300 hover:text-white transition">
                    <i class="fa-solid fa-users w-5"></i><span>Kelola User</span>
                </a>

                <a href="{{ route('admin.testimonials.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.testimonials.*') ? 'active' : '' }}
              flex items-center gap-3 px-4 py-3 rounded-lg text-gray-300 hover:text-white transition">
                    <i class="fa-solid fa-comment-dots w-5"></i><span>Testimoni</span>
                </a>
            </nav>

            {{-- User Info --}}
            <div class="p-4 border-t border-gray-700">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-9 h-9 bg-primary rounded-full flex items-center justify-center text-sm font-bold">
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
        <div class="flex-1 flex flex-col overflow-hidden">
            {{-- Topbar --}}
            <header class="bg-white shadow-sm px-8 py-4 flex items-center justify-between flex-shrink-0">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">@yield('title', 'Dashboard')</h2>
                    <p class="text-sm text-gray-500">{{ now()->isoFormat('dddd, D MMMM Y') }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="bg-purple-100 text-purple-700 text-xs font-semibold px-3 py-1 rounded-full">
                        <i class="fa-solid fa-shield-halved mr-1"></i>Admin
                    </span>
                </div>
            </header>

            {{-- Page Content --}}
            <main class="flex-1 overflow-y-auto p-8">
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>

</html>
