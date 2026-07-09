<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Caysie — Fashion Anak Muda Gunungkidul')</title>
    <meta name="description" content="@yield('meta_desc', 'Toko fashion kaos & celana anak muda dari Gunungkidul. Kualitas premium, harga bersahabat.')">

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#6C63FF',
                        'primary-dark': '#5a52e0',
                        dark: '#1A1A2E',
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .card-hover {
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 16px 40px rgba(108, 99, 255, .15);
        }

        /* Dropdown */
        .nav-dropdown {
            position: relative;
        }

        .nav-dropdown-menu {
            display: none;
            position: absolute;
            top: calc(100% + 14px);
            right: 0;
            background: rgba(255, 255, 255, .98);
            backdrop-filter: blur(12px);
            border: 1px solid #f0f0f0;
            border-radius: 22px;
            box-shadow: 0 20px 50px rgba(26, 26, 46, .12);
            width: 360px;
            z-index: 9999;
            overflow: hidden;
        }

        .nav-dropdown.active .nav-dropdown-menu {
            display: block;
        }

        /* Animasi dropdown */
        .nav-dropdown.active .nav-dropdown-menu {
            animation: dropIn .2s ease;
        }

        @keyframes dropIn {
            from {
                opacity: 0;
                transform: translateY(-8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Profile dropdown */
        .profile-dropdown {
            position: relative;
        }

        .profile-dropdown-menu {
            display: none;
            position: absolute;
            top: calc(100% + 14px);
            right: 0;
            background: rgba(255, 255, 255, .98);
            backdrop-filter: blur(12px);
            border: 1px solid #f0f0f0;
            border-radius: 20px;
            box-shadow: 0 20px 50px rgba(26, 26, 46, .12);
            width: 230px;
            z-index: 9999;
            overflow: hidden;
        }

        .profile-dropdown.active .profile-dropdown-menu {
            display: block;
            animation: dropIn .2s ease;
        }
    </style>
    @stack('styles')
</head>

<body class="bg-gray-50 text-gray-800">

    {{-- ===================== NAVBAR ===================== --}}
    @include('components.navbar')
    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="max-w-7xl mx-auto px-6 pt-4">
            <div
                class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 px-5 py-3.5 rounded-2xl">
                <i class="fa-solid fa-circle-check text-green-500"></i>
                <span class="text-sm font-semibold">{{ session('success') }}</span>
            </div>
        </div>
    @endif
    @if (session('error'))
        <div class="max-w-7xl mx-auto px-6 pt-4">
            <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-600 px-5 py-3.5 rounded-2xl">
                <i class="fa-solid fa-circle-exclamation text-red-500"></i>
                <span class="text-sm font-semibold">{{ session('error') }}</span>
            </div>
        </div>
    @endif
    {{-- Content --}}
    @hasSection('content')
        @yield('content')
    @else
        @isset($header)
            <header class="max-w-7xl mx-auto px-6 pt-8">
                {{ $header }}
            </header>
        @endisset
        {{ $slot ?? '' }}
    @endif

    {{-- ===================== FOOTER ===================== --}}
    @include('components.footer')
    <script>
        // ── Toggle Dropdown ──────────────────────────────────────
        function toggleDropdown(id) {
            const el = document.getElementById(id);
            const all = document.querySelectorAll('.nav-dropdown, .profile-dropdown');
            all.forEach(d => {
                if (d.id !== id) d.classList.remove('active');
            });
            el.classList.toggle('active');
        }

        // Tutup semua dropdown saat klik di luar
        document.addEventListener('click', e => {
            const dropdowns = document.querySelectorAll('.nav-dropdown, .profile-dropdown');
            dropdowns.forEach(d => {
                if (!d.contains(e.target)) d.classList.remove('active');
            });
        });

        // ── Mobile Menu ──────────────────────────────────────────
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            const icon = document.getElementById('mobile-menu-icon');
            menu.classList.toggle('hidden');
            icon.className = menu.classList.contains('hidden') ?
                'fa-solid fa-bars text-gray-600' :
                'fa-solid fa-xmark text-gray-600';
        }

        // ── Countdown Timer di Navbar Dropdown ───────────────────
        document.querySelectorAll('.navbar-countdown').forEach(el => {
            let secs = parseInt(el.dataset.seconds ?? 0);
            if (secs <= 0) return;

            const tick = setInterval(() => {
                secs--;
                if (secs <= 0) {
                    clearInterval(tick);
                    el.textContent = '00:00:00';
                    el.classList.add('text-red-600');
                    return;
                }
                const h = Math.floor(secs / 3600);
                const m = Math.floor((secs % 3600) / 60);
                const s = secs % 60;
                el.textContent =
                    `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
                if (secs < 3600) el.classList.add('text-red-600');
            }, 1000);
        });

        // ── Auto-dismiss flash message ────────────────────────────
        setTimeout(() => {
            document.querySelectorAll('[class*="bg-green-50"], [class*="bg-red-50"]').forEach(el => {
                if (el.closest('nav, footer')) return;
                el.style.transition = 'opacity .5s';
                el.style.opacity = '0';
                setTimeout(() => el.remove(), 500);
            });
        }, 4000);
    </script>

    @stack('scripts')
</body>

</html>
