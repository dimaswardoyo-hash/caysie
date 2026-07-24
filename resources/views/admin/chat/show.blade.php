@extends('layouts.admin')
@section('title', 'Chat — ' . $user->name)

@section('content')
    <a href="{{ route('admin.chat.index') }}"
        class="text-sm text-gray-500 hover:text-primary mb-4 inline-flex items-center gap-1">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Inbox
    </a>

    @if (session('success'))
        <div class="bg-green-50 text-green-700 text-sm font-bold px-4 py-3 rounded-xl mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden flex flex-col" style="height: 65vh;">
        {{-- Header --}}
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <p class="font-bold text-gray-800">{{ $user->name }}</p>
                    <p class="text-xs text-gray-400">{{ $user->email }}</p>
                </div>
            </div>

            <form action="{{ route('admin.chat.destroy', $user) }}" method="POST"
                onsubmit="return confirm('Hapus semua riwayat chat dengan {{ $user->name }}? Tindakan ini tidak bisa dibatalkan.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-xs text-gray-400 hover:text-red-500 flex items-center gap-1 font-bold">
                    <i class="fa-solid fa-trash-can"></i> Hapus Riwayat
                </button>
            </form>
        </div>

        {{-- Pesan --}}
        <div id="admin-chat-messages" class="flex-1 overflow-y-auto px-5 py-4 space-y-3 bg-gray-50">
            @foreach ($messages as $m)
                <div class="flex {{ $m->sender === 'user' ? 'justify-start' : 'justify-end' }}"
                    data-msg-id="{{ $m->id }}">
                    <div
                        class="max-w-[70%] px-4 py-2 rounded-2xl text-sm {{ $m->sender === 'user' ? 'bg-white border border-gray-200 text-gray-800' : 'bg-primary text-white' }}">
                        <p class="whitespace-pre-wrap break-words">{{ $m->message }}</p>
                        <p class="text-[10px] mt-1 opacity-60">{{ $m->created_at->format('H:i') }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Input balas --}}
        <form id="admin-chat-form" class="border-t border-gray-100 p-3 flex items-center gap-2 shrink-0">
            <input id="admin-chat-input" type="text" maxlength="1000" autocomplete="off" placeholder="Balas pesan..."
                class="flex-1 text-sm border border-gray-200 rounded-full px-4 py-2 focus:outline-none focus:border-primary">
            <button type="submit"
                class="w-10 h-10 rounded-full bg-primary text-white flex items-center justify-center hover:bg-primary-dark shrink-0">
                <i class="fa-solid fa-paper-plane text-xs"></i>
            </button>
        </form>
    </div>

    <script>
        (function() {
            const userId = {{ $user->id }};
            const box = document.getElementById('admin-chat-messages');
            let lastId = {{ $messages->last()->id ?? 0 }};

            box.scrollTop = box.scrollHeight;

            function escapeHtml(str) {
                const div = document.createElement('div');
                div.textContent = str;
                return div.innerHTML;
            }

            function renderMessage(m) {
                const wrap = document.createElement('div');
                const isUser = m.sender === 'user';
                wrap.className = `flex ${isUser ? 'justify-start' : 'justify-end'}`;
                wrap.innerHTML = `
                    <div class="max-w-[70%] px-4 py-2 rounded-2xl text-sm ${isUser ? 'bg-white border border-gray-200 text-gray-800' : 'bg-primary text-white'}">
                        <p class="whitespace-pre-wrap break-words">${escapeHtml(m.message)}</p>
                        <p class="text-[10px] mt-1 opacity-60">${m.time}</p>
                    </div>`;
                box.appendChild(wrap);
            }

            async function poll() {
                try {
                    const res = await fetch(`{{ url('admin/chat') }}/${userId}/messages?after_id=${lastId}`);
                    const data = await res.json();
                    if (data.messages && data.messages.length) {
                        data.messages.forEach(renderMessage);
                        lastId = data.messages[data.messages.length - 1].id;
                        box.scrollTop = box.scrollHeight;
                    }
                } catch (e) {
                    /* diam, coba lagi di interval berikutnya */ }
            }

            setInterval(poll, 4000);

            document.getElementById('admin-chat-form').addEventListener('submit', async (e) => {
                e.preventDefault();
                const input = document.getElementById('admin-chat-input');
                const text = input.value.trim();
                if (!text) return;
                input.value = '';

                const res = await fetch(`{{ url('admin/chat') }}/${userId}/send`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        message: text
                    }),
                });
                const data = await res.json();
                renderMessage(data.message);
                lastId = data.message.id;
                box.scrollTop = box.scrollHeight;
            });
        })();
    </script>
@endsection
