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

                {{-- Session Status --}}
                @if (session('status'))
                    <div
                        class="flex items-center gap-2 bg-green-50 border border-green-200 text-green-700 text-sm font-semibold px-4 py-3 rounded-xl mb-5">
                        <i class="fa-solid fa-circle-check text-green-500"></i>
                        {{ session('status') }}
                    </div>
                @endif

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
