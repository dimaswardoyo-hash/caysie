@extends('layouts.admin')
@section('title', 'Chat')

@section('content')
    <h1 class="text-2xl font-black text-gray-800 mb-6">Chat dengan Pelanggan</h1>

    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        @forelse ($threads as $user)
            @php $last = $lastMessages->get($user->id); @endphp
            <a href="{{ route('admin.chat.show', $user) }}"
                class="flex items-center gap-4 px-5 py-4 border-b border-gray-50 last:border-0 hover:bg-gray-50 transition">
                <div
                    class="w-11 h-11 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold shrink-0">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-2">
                        <p class="font-bold text-gray-800 truncate">{{ $user->name }}</p>
                        @if ($last)
                            <span class="text-xs text-gray-400 shrink-0">{{ $last->created_at->diffForHumans() }}</span>
                        @endif
                    </div>
                    @if ($last)
                        <p class="text-sm text-gray-500 truncate">
                            @if ($last->sender === 'admin')
                                <span class="text-gray-400">Anda: </span>
                            @endif
                            {{ $last->message }}
                        </p>
                    @endif
                </div>
                @if ($user->unread_count > 0)
                    <span
                        class="bg-red-500 text-white text-xs font-black w-5 h-5 rounded-full flex items-center justify-center shrink-0">
                        {{ $user->unread_count > 9 ? '9+' : $user->unread_count }}
                    </span>
                @endif
            </a>
        @empty
            <p class="text-center text-gray-400 py-16">Belum ada percakapan masuk.</p>
        @endforelse
    </div>

    @if ($threads->hasPages())
        <div class="mt-6">{{ $threads->links() }}</div>
    @endif
@endsection
