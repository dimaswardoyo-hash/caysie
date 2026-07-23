@extends('layouts.app')
@section('title', 'Masuk — Caysie')

@section('content')
    <div
        class="min-h-[calc(100vh-68px)] flex items-center justify-center bg-gradient-to-br from-slate-50 via-purple-50/60 to-slate-50 px-4 py-12">
        <div class="w-full max-w-md">

            {{-- Brand --}}
            <div class="flex flex-col items-center mb-8">
                <div
                    class="w-16 h-16 bg-gradient-to-br from-primary to-primary-dark rounded-2xl flex items-center justify-center shadow-lg shadow-primary/25 mb-4">
                    <i class="fa-solid fa-shirt text-white text-xl"></i>
                </div>
                <h1 class="text-2xl font-black text-dark tracking-tight">Selamat Datang Kembali</h1>
                <p class="text-gray-500 text-sm mt-1">Masuk untuk lanjut belanja di Caysie</p>
            </div>

            {{-- Card --}}
            <div class="bg-white border border-gray-100 rounded-3xl shadow-sm shadow-gray-100 p-7 md:p-8">

                {{-- Google Login --}}
                <a href="{{ route('auth.google.redirect') }}"
                    class="w-full flex items-center justify-center gap-2.5 bg-white border border-gray-200 text-gray-700 text-sm font-bold py-2.5 rounded-xl hover:bg-gray-50 transition shadow-sm">
                    <svg class="w-4.5 h-4.5" style="width:18px;height:18px" viewBox="0 0 24 24">
                        <path fill="#4285F4"
                            d="M23.49 12.27c0-.79-.07-1.54-.2-2.27H12v4.51h6.47c-.29 1.48-1.14 2.73-2.42 3.58v2.98h3.93c2.3-2.12 3.51-5.24 3.51-8.8z" />
                        <path fill="#34A853"
                            d="M12 24c3.24 0 5.95-1.08 7.93-2.92l-3.93-2.98c-1.09.73-2.48 1.16-4 1.16-3.08 0-5.68-2.08-6.6-4.87H1.36v3.06C3.33 21.3 7.36 24 12 24z" />
                        <path fill="#FBBC05"
                            d="M5.4 14.39c-.24-.73-.38-1.5-.38-2.29s.14-1.56.38-2.29V6.75H1.36A11.97 11.97 0 000 12.1c0 1.94.46 3.77 1.36 5.35l4.04-3.06z" />
                        <path fill="#EA4335"
                            d="M12 4.75c1.76 0 3.35.61 4.6 1.79l3.45-3.45C17.94 1.2 15.24 0 12 0 7.36 0 3.33 2.7 1.36 6.75l4.04 3.06c.92-2.79 3.52-4.87 6.6-4.87z" />
                    </svg>
                    Masuk dengan Google
                </a>

                {{-- Divider --}}
                <div class="flex items-center gap-3 my-5">
                    <div class="h-px bg-gray-200 flex-1"></div>
                    <span class="text-xs font-semibold text-gray-400">atau masuk dengan email</span>
                    <div class="h-px bg-gray-200 flex-1"></div>
                </div>

                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-xs font-bold text-gray-500 mb-1.5">Email</label>
                        <div class="relative">
                            <i
                                class="fa-solid fa-envelope absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                                autofocus autocomplete="username" placeholder="nama@email.com"
                                class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-800 focus:outline-none focus:border-primary focus:bg-white transition @error('email') border-red-300 @enderror">
                        </div>
                        @error('email')
                            <p class="text-xs text-red-500 font-semibold mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div>
                        <label for="password" class="block text-xs font-bold text-gray-500 mb-1.5">Kata Sandi</label>
                        <div class="relative">
                            <i
                                class="fa-solid fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                            <input id="password" type="password" name="password" required autocomplete="current-password"
                                placeholder="••••••••"
                                class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-800 focus:outline-none focus:border-primary focus:bg-white transition @error('password') border-red-300 @enderror">
                        </div>
                        @error('password')
                            <p class="text-xs text-red-500 font-semibold mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Remember + Forgot --}}
                    <div class="flex items-center justify-between pt-1">
                        <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer select-none">
                            <input id="remember_me" type="checkbox" name="remember"
                                class="rounded border-gray-300 text-primary focus:ring-primary">
                            <span class="text-xs font-semibold text-gray-500">Ingat saya</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}"
                                class="text-xs font-bold text-primary hover:text-primary-dark transition">
                                Lupa kata sandi?
                            </a>
                        @endif
                    </div>

                    {{-- Submit --}}
                    <button type="submit"
                        class="w-full flex items-center justify-center gap-2 bg-gradient-to-r from-primary to-primary-dark text-white text-sm font-black py-3 rounded-xl hover:opacity-90 transition shadow-lg shadow-primary/25 mt-2">
                        <i class="fa-solid fa-right-to-bracket"></i>
                        Masuk
                    </button>
                </form>
            </div>

            {{-- Switch to register --}}
            <p class="text-center text-sm text-gray-500 mt-6">
                Belum punya akun?
                <a href="{{ route('register') }}" class="font-bold text-primary hover:text-primary-dark transition">
                    Daftar sekarang
                </a>
            </p>
        </div>
    </div>
@endsection
