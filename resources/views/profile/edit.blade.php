@extends('layouts.app')
@section('title', 'Profil Saya — Caysie')

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 py-8">

        {{-- ===================== HERO PROFIL ===================== --}}
        <div
            class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-primary to-primary-dark px-7 py-9 md:px-10 md:py-10 mb-8">
            <div class="absolute -right-10 -top-10 w-56 h-56 bg-white/10 rounded-full blur-2xl"></div>
            <div class="absolute right-16 bottom-0 w-32 h-32 bg-white/10 rounded-full blur-xl"></div>
            <div class="relative flex flex-col sm:flex-row items-center sm:items-start gap-5 text-center sm:text-left">
                <div
                    class="w-20 h-20 flex-shrink-0 bg-white/15 backdrop-blur-sm rounded-3xl flex items-center justify-center text-white text-3xl font-black shadow-lg">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <span
                        class="inline-flex items-center gap-1.5 bg-white/15 text-white text-xs font-bold px-3 py-1 rounded-full mb-2">
                        <i class="fa-solid fa-user"></i> Akun Saya
                    </span>
                    <h1 class="text-2xl md:text-3xl font-black text-white tracking-tight truncate">{{ $user->name }}</h1>
                    <p class="text-white/70 text-sm mt-1 truncate">{{ $user->email }}</p>
                    <p class="text-white/60 text-xs mt-2">
                        <i class="fa-regular fa-calendar mr-1"></i> Bergabung sejak
                        {{ $user->created_at->translatedFormat('F Y') }}
                    </p>
                </div>
            </div>
        </div>

        {{-- ===================== FLASH STATUS ===================== --}}
        @php
            $statusMessages = [
                'profile-updated' => ['Profil kamu berhasil diperbarui.', 'green'],
                'password-updated' => ['Kata sandi kamu berhasil diperbarui.', 'green'],
                'verification-link-sent' => ['Link verifikasi baru telah dikirim ke email kamu.', 'blue'],
            ];
            $statusInfo = $statusMessages[session('status')] ?? null;
        @endphp
        @if ($statusInfo)
            <div
                class="flex items-center gap-3 bg-{{ $statusInfo[1] }}-50 border border-{{ $statusInfo[1] }}-200 text-{{ $statusInfo[1] }}-700 px-5 py-3.5 rounded-2xl mb-6">
                <i class="fa-solid fa-circle-check text-{{ $statusInfo[1] }}-500"></i>
                <span class="text-sm font-semibold">{{ $statusInfo[0] }}</span>
            </div>
        @endif

        <div class="space-y-6">
            {{-- INFORMASI PROFIL --}}
            <div class="bg-white border border-gray-100 rounded-2xl p-5 md:p-6 shadow-sm shadow-gray-100">
                @include('profile.partials.update-profile-information-form')
            </div>

            {{-- UBAH KATA SANDI --}}
            <div class="bg-white border border-gray-100 rounded-2xl p-5 md:p-6 shadow-sm shadow-gray-100">
                @include('profile.partials.update-password-form')
            </div>

            {{-- HAPUS AKUN --}}
            <div class="bg-white border border-red-100 rounded-2xl p-5 md:p-6 shadow-sm shadow-gray-100">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
@endsection
