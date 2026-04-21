@extends('layouts.admin')
@section('title', 'Laporan Pemasukan')

@section('content')

    {{-- Filter Tahun --}}
    <div class="flex items-center gap-3 mb-8 flex-wrap">
        <h2 class="text-lg font-black text-gray-700">Laporan Tahun:</h2>
        @foreach ($years as $y)
            <a href="{{ route('admin.revenue.index', ['year' => $y]) }}"
                class="px-4 py-2 rounded-xl text-sm font-bold transition
              {{ $year == $y ? 'bg-primary text-white shadow-md' : 'bg-white border border-gray-200 text-gray-600 hover:border-primary hover:text-primary' }}">
                {{ $y }}
            </a>
        @endforeach
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-money-bill-wave text-green-600 text-xl"></i>
                </div>
                <span class="text-xs font-bold text-green-600 bg-green-50 px-2 py-1 rounded-full">All time</span>
            </div>
            <p class="text-2xl font-black text-gray-800">Rp{{ number_format($totalRevenue, 0, ',', '.') }}</p>
            <p class="text-sm text-gray-400 mt-1">Total Pemasukan</p>
        </div>

        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-calendar-check text-blue-600 text-xl"></i>
                </div>
                <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded-full">Bulan ini</span>
            </div>
            <p class="text-2xl font-black text-gray-800">Rp{{ number_format($monthRevenue, 0, ',', '.') }}</p>
            <p class="text-sm text-gray-400 mt-1">Pemasukan Bulan Ini</p>
        </div>

        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-receipt text-purple-600 text-xl"></i>
                </div>
                <span class="text-xs font-bold text-purple-600 bg-purple-50 px-2 py-1 rounded-full">Total</span>
            </div>
            <p class="text-2xl font-black text-gray-800">{{ $paidOrders }}</p>
            <p class="text-sm text-gray-400 mt-1">Pesanan Berhasil</p>
        </div>

        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-clock text-yellow-600 text-xl"></i>
                </div>
                <span class="text-xs font-bold text-yellow-600 bg-yellow-50 px-2 py-1 rounded-full">Pending</span>
            </div>
            <p class="text-2xl font-black text-gray-800">{{ $pendingOrders }}</p>
            <p class="text-sm text-gray-400 mt-1">Menunggu Pembayaran</p>
        </div>
    </div>

    {{-- Grafik + Produk Terlaris --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

        {{-- Grafik Bulanan --}}
        <div class="lg:col-span-2 bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <h3 class="font-black text-gray-800">Grafik Pemasukan {{ $year }}</h3>
                <span class="text-xs text-gray-400 bg-gray-50 px-3 py-1 rounded-full">Per bulan</span>
            </div>
            <div class="relative" style="height:260px">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        {{-- Produk Terlaris --}}
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
            <h3 class="font-black text-gray-800 mb-5">🏆 Produk Terlaris</h3>
            @if ($topProducts->isEmpty())
                <div class="text-center text-gray-300 py-8">
                    <i class="fa-solid fa-chart-bar text-4xl mb-3"></i>
                    <p class="text-sm">Belum ada data</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach ($topProducts as $i => $prod)
                        @php $maxQty = $topProducts->max('total_qty'); @endphp
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <div class="flex items-center gap-2">
                                    <span
                                        class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-black
                            {{ $i === 0 ? 'bg-yellow-100 text-yellow-700' : ($i === 1 ? 'bg-gray-100 text-gray-600' : ($i === 2 ? 'bg-orange-100 text-orange-600' : 'bg-purple-50 text-purple-500')) }}">
                                        {{ $i + 1 }}
                                    </span>
                                    <span
                                        class="text-sm font-semibold text-gray-700 truncate max-w-32">{{ $prod->product_name }}</span>
                                </div>
                                <span class="text-xs font-black text-primary">{{ $prod->total_qty }} pcs</span>
                            </div>
                            <div class="bg-gray-100 rounded-full h-2">
                                <div class="bg-primary h-2 rounded-full transition-all duration-500"
                                    style="width:{{ $maxQty > 0 ? round(($prod->total_qty / $maxQty) * 100) : 0 }}%"></div>
                            </div>
                            <p class="text-xs text-gray-400 mt-0.5 text-right">
                                Rp{{ number_format($prod->total_revenue, 0, ',', '.') }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Transaksi Terbaru --}}
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between mb-5">
            <h3 class="font-black text-gray-800">Transaksi Terbaru</h3>
            <a href="{{ route('admin.orders.index') }}" class="text-sm text-primary font-semibold hover:underline">
                Lihat semua →
            </a>
        </div>
        @if ($recentOrders->isEmpty())
            <div class="text-center text-gray-300 py-10">
                <i class="fa-solid fa-inbox text-4xl mb-3"></i>
                <p class="text-sm">Belum ada transaksi</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-xs text-gray-400 uppercase tracking-wider border-b border-gray-100">
                            <th class="pb-3 text-left">Kode</th>
                            <th class="pb-3 text-left">Pelanggan</th>
                            <th class="pb-3 text-left">Tanggal</th>
                            <th class="pb-3 text-right">Total</th>
                            <th class="pb-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($recentOrders as $order)
                            @php
                                $sc =
                                    [
                                        'paid' => 'blue',
                                        'processing' => 'purple',
                                        'shipped' => 'indigo',
                                        'delivered' => 'green',
                                    ][$order->status] ?? 'gray';
                                $sl =
                                    [
                                        'paid' => 'Dibayar',
                                        'processing' => 'Diproses',
                                        'shipped' => 'Dikirim',
                                        'delivered' => 'Selesai',
                                    ][$order->status] ?? ucfirst($order->status);
                            @endphp
                            <tr class="hover:bg-gray-50/50">
                                <td class="py-3">
                                    <a href="{{ route('admin.orders.show', $order) }}"
                                        class="font-bold text-primary hover:underline text-xs">{{ $order->order_code }}</a>
                                </td>
                                <td class="py-3 text-gray-600 text-xs">{{ $order->user->name }}</td>
                                <td class="py-3 text-gray-400 text-xs">{{ $order->created_at->isoFormat('D MMM Y') }}</td>
                                <td class="py-3 text-right font-black text-gray-800">{{ $order->formatted_total }}</td>
                                <td class="py-3 text-center">
                                    <span
                                        class="px-2.5 py-1 rounded-full text-xs font-bold bg-{{ $sc }}-100 text-{{ $sc }}-700">
                                        {{ $sl }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <script>
        const ctx = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($chartMonths),
                datasets: [{
                        label: 'Pemasukan (Rp)',
                        data: @json($chartRevenue),
                        backgroundColor: 'rgba(108,99,255,0.15)',
                        borderColor: '#6C63FF',
                        borderWidth: 2,
                        borderRadius: 8,
                        borderSkipped: false,
                        yAxisID: 'y',
                    },
                    {
                        label: 'Jumlah Pesanan',
                        data: @json($chartOrders),
                        type: 'line',
                        borderColor: '#48BB78',
                        backgroundColor: 'rgba(72,187,120,0.1)',
                        borderWidth: 2.5,
                        pointBackgroundColor: '#48BB78',
                        pointRadius: 4,
                        tension: 0.4,
                        fill: true,
                        yAxisID: 'y1',
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: {
                                size: 11,
                                weight: '600'
                            },
                            padding: 16
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: ctx => ctx.datasetIndex === 0 ?
                                ' Rp' + ctx.parsed.y.toLocaleString('id-ID') :
                                ' ' + ctx.parsed.y + ' pesanan'
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 11
                            }
                        }
                    },
                    y: {
                        position: 'left',
                        grid: {
                            color: 'rgba(0,0,0,.05)'
                        },
                        ticks: {
                            font: {
                                size: 10
                            },
                            callback: v => 'Rp' + (v / 1000000).toFixed(1) + 'jt'
                        }
                    },
                    y1: {
                        position: 'right',
                        grid: {
                            drawOnChartArea: false
                        },
                        ticks: {
                            font: {
                                size: 10
                            },
                            stepSize: 1
                        }
                    }
                }
            }
        });
    </script>
@endpush
