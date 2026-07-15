@extends('layouts.app')
@section('title', 'Daftar — Caysie')

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
                <h1 class="text-2xl font-black text-dark tracking-tight">Buat Akun Baru</h1>
                <p class="text-gray-500 text-sm mt-1">Gabung dan mulai belanja di Caysie</p>
            </div>

            {{-- Card --}}
            <div class="bg-white border border-gray-100 rounded-3xl shadow-sm shadow-gray-100 p-7 md:p-8">

                <form method="POST" action="{{ route('register') }}" class="space-y-4">
                    @csrf

                    {{-- Name --}}
                    <div>
                        <label for="name" class="block text-xs font-bold text-gray-500 mb-1.5">Nama Lengkap</label>
                        <div class="relative">
                            <i
                                class="fa-solid fa-user absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                            <input id="name" type="text" name="name" value="{{ old('name') }}" required
                                autofocus autocomplete="name" placeholder="Nama kamu"
                                class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-800 focus:outline-none focus:border-primary focus:bg-white transition @error('name') border-red-300 @enderror">
                        </div>
                        @error('name')
                            <p class="text-xs text-red-500 font-semibold mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-xs font-bold text-gray-500 mb-1.5">Email</label>
                        <div class="relative">
                            <i
                                class="fa-solid fa-envelope absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                                autocomplete="username" placeholder="nama@email.com"
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
                            <input id="password" type="password" name="password" required autocomplete="new-password"
                                placeholder="••••••••"
                                class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-800 focus:outline-none focus:border-primary focus:bg-white transition @error('password') border-red-300 @enderror">
                        </div>
                        @error('password')
                            <p class="text-xs text-red-500 font-semibold mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Confirm Password --}}
                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold text-gray-500 mb-1.5">Konfirmasi
                            Kata Sandi</label>
                        <div class="relative">
                            <i
                                class="fa-solid fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                            <input id="password_confirmation" type="password" name="password_confirmation" required
                                autocomplete="new-password" placeholder="••••••••"
                                class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-800 focus:outline-none focus:border-primary focus:bg-white transition @error('password_confirmation') border-red-300 @enderror">
                        </div>
                        @error('password_confirmation')
                            <p class="text-xs text-red-500 font-semibold mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Submit --}}
                    <button type="submit"
                        class="w-full flex items-center justify-center gap-2 bg-gradient-to-r from-primary to-primary-dark text-white text-sm font-black py-3 rounded-xl hover:opacity-90 transition shadow-lg shadow-primary/25 mt-2">
                        <i class="fa-solid fa-user-plus"></i>
                        Daftar
                    </button>
                </form>
            </div>

            {{-- Switch to login --}}
            <p class="text-center text-sm text-gray-500 mt-6">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="font-bold text-primary hover:text-primary-dark transition">
                    Masuk di sini
                </a>
            </p>
        </div>
    </div>
@endsection
