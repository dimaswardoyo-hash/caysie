<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
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

        .nav-link {
            position: relative;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 0;
            width: 0;
            height: 2px;
            background: #6C63FF;
            transition: .3s;
        }

        .nav-link:hover::after {
            width: 100%;
        }

        html {
            scroll-behavior: smooth;
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
    @yield('content')

    {{-- ===================== FOOTER ===================== --}}
    @include('components.footer')

    @stack('scripts')
</body>

</html>
