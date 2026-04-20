@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">

        <div class="card-hover bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-users text-purple-600 text-xl"></i>
                </div>
                <span class="text-xs text-green-600 bg-green-100 px-2 py-1 rounded-full font-semibold">+12%</span>
            </div>
            <p class="text-3xl font-bold text-gray-800">{{ $stats['total_users'] }}</p>
            <p class="text-sm text-gray-500 mt-1">Total User</p>
        </div>

        <div class="card-hover bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-box-open text-blue-600 text-xl"></i>
                </div>
                <span class="text-xs text-green-600 bg-green-100 px-2 py-1 rounded-full font-semibold">Aktif</span>
            </div>
            <p class="text-3xl font-bold text-gray-800">{{ $stats['total_products'] }}</p>
            <p class="text-sm text-gray-500 mt-1">Total Produk</p>
        </div>

        <div class="card-hover bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-receipt text-yellow-600 text-xl"></i>
                </div>
                <span class="text-xs text-yellow-600 bg-yellow-100 px-2 py-1 rounded-full font-semibold">Pending</span>
            </div>
            <p class="text-3xl font-bold text-gray-800">{{ $stats['total_orders'] }}</p>
            <p class="text-sm text-gray-500 mt-1">Total Pesanan</p>
        </div>

        <div class="card-hover bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-money-bill-wave text-green-600 text-xl"></i>
                </div>
                <span class="text-xs text-green-600 bg-green-100 px-2 py-1 rounded-full font-semibold">Bulan ini</span>
            </div>
            <p class="text-3xl font-bold text-gray-800">Rp 0</p>
            <p class="text-sm text-gray-500 mt-1">Total Pemasukan</p>
        </div>

    </div>

    {{-- Quick Actions --}}
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 mb-8">
        <h3 class="text-lg font-bold text-gray-800 mb-5">Aksi Cepat</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="#"
                class="flex flex-col items-center gap-3 p-5 bg-purple-50 hover:bg-purple-100 rounded-xl transition group">
                <div
                    class="w-12 h-12 bg-purple-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-plus text-white text-lg"></i>
                </div>
                <span class="text-sm font-semibold text-purple-700">Tambah Produk</span>
            </a>
            <a href="#"
                class="flex flex-col items-center gap-3 p-5 bg-blue-50 hover:bg-blue-100 rounded-xl transition group">
                <div
                    class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-list text-white text-lg"></i>
                </div>
                <span class="text-sm font-semibold text-blue-700">Lihat Pesanan</span>
            </a>
            <a href="#"
                class="flex flex-col items-center gap-3 p-5 bg-green-50 hover:bg-green-100 rounded-xl transition group">
                <div
                    class="w-12 h-12 bg-green-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-chart-bar text-white text-lg"></i>
                </div>
                <span class="text-sm font-semibold text-green-700">Laporan</span>
            </a>
            <a href="#"
                class="flex flex-col items-center gap-3 p-5 bg-orange-50 hover:bg-orange-100 rounded-xl transition group">
                <div
                    class="w-12 h-12 bg-orange-500 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-user-gear text-white text-lg"></i>
                </div>
                <span class="text-sm font-semibold text-orange-700">Kelola User</span>
            </a>
        </div>
    </div>

    {{-- Recent Activity placeholder --}}
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <h3 class="text-lg font-bold text-gray-800 mb-5">Pesanan Terbaru</h3>
        <div class="flex flex-col items-center justify-center py-12 text-gray-400">
            <i class="fa-solid fa-inbox text-5xl mb-4 opacity-30"></i>
            <p class="text-sm">Belum ada pesanan masuk</p>
            <p class="text-xs mt-1">Pesanan akan muncul di sini setelah user melakukan pembelian</p>
        </div>
    </div>

@endsection
