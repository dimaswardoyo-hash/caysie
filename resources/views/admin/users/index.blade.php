@extends('layouts.admin')
@section('title', 'Kelola User')

@section('content')

    <div class="flex items-center justify-between mb-6">
        <p class="text-sm text-gray-400">Total {{ $users->total() }} user terdaftar</p>
    </div>

    <form method="GET" class="flex gap-3 mb-6">
        <div class="relative flex-1 max-w-sm">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email..."
                class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-primary transition">
        </div>
        <button type="submit"
            class="px-5 py-2.5 bg-primary text-white text-sm font-bold rounded-xl hover:bg-primary-dark transition">
            Cari
        </button>
        @if (request('search'))
            <a href="{{ route('admin.users.index') }}"
                class="px-5 py-2.5 bg-gray-100 text-gray-600 text-sm font-bold rounded-xl">Reset</a>
        @endif
    </form>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        @if ($users->isEmpty())
            <div class="py-20 text-center text-gray-400">
                <i class="fa-solid fa-users text-5xl mb-4 opacity-30"></i>
                <p class="font-semibold">Belum ada user terdaftar</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100 text-xs text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-4 text-left">User</th>
                            <th class="px-4 py-4 text-left">Email</th>
                            <th class="px-4 py-4 text-center">Total Pesanan</th>
                            <th class="px-4 py-4 text-left">Bergabung</th>
                            <th class="px-4 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($users as $user)
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 bg-primary rounded-full flex items-center justify-center text-white font-black text-sm flex-shrink-0">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-800">{{ $user->name }}</p>
                                            @if ($user->phone)
                                                <p class="text-xs text-gray-400">{{ $user->phone }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-gray-600 text-xs">{{ $user->email }}</td>
                                <td class="px-4 py-4 text-center">
                                    <span
                                        class="inline-block bg-purple-100 text-purple-700 text-xs font-black px-3 py-1 rounded-full">
                                        {{ $user->orders_count }} pesanan
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-gray-400 text-xs">{{ $user->created_at->isoFormat('D MMM Y') }}
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <a href="{{ route('admin.users.show', $user) }}"
                                        class="w-8 h-8 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center hover:bg-blue-100 transition mx-auto"
                                        title="Detail">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if ($users->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">{{ $users->links() }}</div>
            @endif
        @endif
    </div>

@endsection
