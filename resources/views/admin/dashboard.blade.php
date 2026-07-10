@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-3 sm:gap-6 mb-6 sm:mb-8">

        <div class="card-hover bg-white rounded-xl sm:rounded-2xl p-3 sm:p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-2 sm:mb-4">
                <div
                    class="w-9 h-9 sm:w-12 sm:h-12 bg-purple-100 rounded-lg sm:rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-users text-purple-600 text-sm sm:text-xl"></i>
                </div>
                <span
                    class="text-[10px] sm:text-xs text-green-600 bg-green-100 px-1.5 sm:px-2 py-0.5 sm:py-1 rounded-full font-semibold whitespace-nowrap">+12%</span>
            </div>
            <p class="text-xl sm:text-3xl font-bold text-gray-800">{{ $stats['total_users'] }}</p>
            <p class="text-xs sm:text-sm text-gray-500 mt-1 truncate">Total User</p>
        </div>

        <div class="card-hover bg-white rounded-xl sm:rounded-2xl p-3 sm:p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-2 sm:mb-4">
                <div
                    class="w-9 h-9 sm:w-12 sm:h-12 bg-blue-100 rounded-lg sm:rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-box-open text-blue-600 text-sm sm:text-xl"></i>
                </div>
                <span
                    class="text-[10px] sm:text-xs text-green-600 bg-green-100 px-1.5 sm:px-2 py-0.5 sm:py-1 rounded-full font-semibold whitespace-nowrap">Aktif</span>
            </div>
            <p class="text-xl sm:text-3xl font-bold text-gray-800">{{ $stats['total_products'] }}</p>
            <p class="text-xs sm:text-sm text-gray-500 mt-1 truncate">Total Produk</p>
        </div>

        <div class="card-hover bg-white rounded-xl sm:rounded-2xl p-3 sm:p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-2 sm:mb-4">
                <div
                    class="w-9 h-9 sm:w-12 sm:h-12 bg-yellow-100 rounded-lg sm:rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-receipt text-yellow-600 text-sm sm:text-xl"></i>
                </div>
                <span
                    class="text-[10px] sm:text-xs text-yellow-600 bg-yellow-100 px-1.5 sm:px-2 py-0.5 sm:py-1 rounded-full font-semibold whitespace-nowrap">Pending</span>
            </div>
            <p class="text-xl sm:text-3xl font-bold text-gray-800">{{ $stats['total_orders'] }}</p>
            <p class="text-xs sm:text-sm text-gray-500 mt-1 truncate">Total Pesanan</p>
        </div>

        <div class="card-hover bg-white rounded-xl sm:rounded-2xl p-3 sm:p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-2 sm:mb-4">
                <div
                    class="w-9 h-9 sm:w-12 sm:h-12 bg-green-100 rounded-lg sm:rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-money-bill-wave text-green-600 text-sm sm:text-xl"></i>
                </div>
                <span
                    class="text-[10px] sm:text-xs text-green-600 bg-green-100 px-1.5 sm:px-2 py-0.5 sm:py-1 rounded-full font-semibold whitespace-nowrap">Bulan
                    ini</span>
            </div>
            <p class="text-xl sm:text-3xl font-bold text-gray-800 truncate">
                Rp{{ number_format($stats['revenue_this_month'], 0, ',', '.') }}</p>
            <p class="text-xs sm:text-sm text-gray-500 mt-1 truncate">Pemasukan Bulan Ini</p>
        </div>

    </div>

    {{-- Quick Actions --}}
    <div class="bg-white rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-sm border border-gray-100 mb-6 sm:mb-8">
        <h3 class="text-base sm:text-lg font-bold text-gray-800 mb-4 sm:mb-5">Aksi Cepat</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
            <a href="{{ route('admin.products.create') }}"
                class="flex flex-col items-center gap-2 sm:gap-3 p-3 sm:p-5 bg-purple-50 hover:bg-purple-100 active:bg-purple-100 rounded-xl transition group">
                <div
                    class="w-10 h-10 sm:w-12 sm:h-12 bg-purple-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform flex-shrink-0">
                    <i class="fa-solid fa-plus text-white text-base sm:text-lg"></i>
                </div>
                <span class="text-xs sm:text-sm font-semibold text-purple-700 text-center">Tambah Produk</span>
            </a>
            <a href="{{ route('admin.orders.index') }}"
                class="flex flex-col items-center gap-2 sm:gap-3 p-3 sm:p-5 bg-blue-50 hover:bg-blue-100 active:bg-blue-100 rounded-xl transition group">
                <div
                    class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform flex-shrink-0">
                    <i class="fa-solid fa-list text-white text-base sm:text-lg"></i>
                </div>
                <span class="text-xs sm:text-sm font-semibold text-blue-700 text-center">Lihat Pesanan</span>
            </a>
            <a href="{{ route('admin.revenue.index') }}"
                class="flex flex-col items-center gap-2 sm:gap-3 p-3 sm:p-5 bg-green-50 hover:bg-green-100 active:bg-green-100 rounded-xl transition group">
                <div
                    class="w-10 h-10 sm:w-12 sm:h-12 bg-green-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform flex-shrink-0">
                    <i class="fa-solid fa-chart-bar text-white text-base sm:text-lg"></i>
                </div>
                <span class="text-xs sm:text-sm font-semibold text-green-700 text-center">Laporan</span>
            </a>
            <a href="{{ route('admin.users.index') }}"
                class="flex flex-col items-center gap-2 sm:gap-3 p-3 sm:p-5 bg-orange-50 hover:bg-orange-100 active:bg-orange-100 rounded-xl transition group">
                <div
                    class="w-10 h-10 sm:w-12 sm:h-12 bg-orange-500 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform flex-shrink-0">
                    <i class="fa-solid fa-user-gear text-white text-base sm:text-lg"></i>
                </div>
                <span class="text-xs sm:text-sm font-semibold text-orange-700 text-center">Kelola User</span>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        {{-- Recent Orders --}}
        <div class="xl:col-span-2 bg-white rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-4 sm:mb-5">
                <h3 class="text-base sm:text-lg font-bold text-gray-800">Pesanan Terbaru</h3>
                <a href="{{ route('admin.orders.index') }}"
                    class="text-xs sm:text-sm font-semibold text-primary hover:underline whitespace-nowrap">Lihat
                    semua</a>
            </div>

            @if ($recentOrders->isEmpty())
                <div class="flex flex-col items-center justify-center py-8 sm:py-12 text-gray-400 px-2 text-center">
                    <i class="fa-solid fa-inbox text-4xl sm:text-5xl mb-3 sm:mb-4 opacity-30"></i>
                    <p class="text-sm">Belum ada pesanan masuk</p>
                    <p class="text-xs mt-1">Pesanan akan muncul di sini setelah user melakukan pembelian</p>
                </div>
            @else
                {{-- Mobile: stacked cards --}}
                <div class="flex flex-col gap-3 sm:hidden">
                    @foreach ($recentOrders as $order)
                        <a href="{{ route('admin.orders.show', $order) }}"
                            class="block p-3 rounded-xl border border-gray-100 hover:border-primary/40 hover:bg-purple-50/40 transition">
                            <div class="flex items-center justify-between gap-2 mb-1.5">
                                <p class="font-bold text-gray-800 text-xs truncate">{{ $order->order_code }}</p>
                                <span
                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold whitespace-nowrap
                                bg-{{ $order->status_color }}-100 text-{{ $order->status_color }}-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-{{ $order->status_color }}-500"></span>
                                    {{ $order->status_label }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-xs text-gray-500 truncate">{{ $order->user->name ?? '-' }}</p>
                                <p class="font-black text-gray-800 text-sm whitespace-nowrap">
                                    {{ $order->formatted_total }}</p>
                            </div>
                            <p class="text-[11px] text-gray-400 mt-1">{{ $order->created_at->diffForHumans() }}</p>
                        </a>
                    @endforeach
                </div>

                {{-- Desktop / tablet: table --}}
                <div class="hidden sm:block overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-xs text-gray-500 uppercase tracking-wider border-b border-gray-100">
                                <th class="text-left py-2 pr-3">Pesanan</th>
                                <th class="text-left py-2 pr-3">Pelanggan</th>
                                <th class="text-left py-2 pr-3">Total</th>
                                <th class="text-center py-2">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach ($recentOrders as $order)
                                <tr class="hover:bg-gray-50/50 transition cursor-pointer"
                                    onclick="window.location='{{ route('admin.orders.show', $order) }}'">
                                    <td class="py-3 pr-3">
                                        <p class="font-bold text-gray-800 text-xs">{{ $order->order_code }}</p>
                                        <p class="text-xs text-gray-400 mt-0.5">
                                            {{ $order->created_at->diffForHumans() }}</p>
                                    </td>
                                    <td class="py-3 pr-3 text-gray-700 text-xs">{{ $order->user->name ?? '-' }}</td>
                                    <td class="py-3 pr-3 font-black text-gray-800 text-xs whitespace-nowrap">
                                        {{ $order->formatted_total }}</td>
                                    <td class="py-3 text-center">
                                        <span
                                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold whitespace-nowrap
                                        bg-{{ $order->status_color }}-100 text-{{ $order->status_color }}-700">
                                            <span
                                                class="w-1.5 h-1.5 rounded-full bg-{{ $order->status_color }}-500"></span>
                                            {{ $order->status_label }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- Low Stock --}}
        <div class="bg-white rounded-xl sm:rounded-2xl p-4 sm:p-6 shadow-sm border border-gray-100">
            <h3 class="text-base sm:text-lg font-bold text-gray-800 mb-4 sm:mb-5">Stok Menipis</h3>

            @if ($lowStock->isEmpty())
                <div class="flex flex-col items-center justify-center py-8 text-gray-400 px-2 text-center">
                    <i class="fa-solid fa-boxes-stacked text-4xl mb-3 opacity-30"></i>
                    <p class="text-sm">Stok produk aman</p>
                </div>
            @else
                <div class="flex flex-col gap-3">
                    @foreach ($lowStock as $product)
                        <a href="{{ route('admin.products.edit', $product) }}"
                            class="flex items-center gap-3 p-2 -mx-2 rounded-lg hover:bg-gray-50 transition">
                            <div
                                class="w-10 h-10 sm:w-11 sm:h-11 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-triangle-exclamation text-orange-500 text-sm"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs sm:text-sm font-semibold text-gray-800 truncate">{{ $product->name }}
                                </p>
                                <p class="text-[11px] sm:text-xs text-gray-400">Sisa {{ $product->total_stock }} pcs
                                </p>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

@endsection
