@php
    $waNumber = config('services.whatsapp.admin_number');
    $waText = rawurlencode('Halo Caysie, saya mau tanya-tanya soal produk 🙂');
    $waLink = $waNumber ? "https://wa.me/{$waNumber}?text={$waText}" : null;
    $isUser = auth()->check() && auth()->user()->role === 'user';
@endphp

<div id="chat-widget" class="fixed bottom-5 right-5 z-[9999]">

    {{-- Tombol mengambang --}}
    <button id="chat-toggle-btn" onclick="ChatWidget.toggle()"
        class="relative w-14 h-14 rounded-full bg-primary hover:bg-primary-dark text-white shadow-xl flex items-center justify-center transition-transform hover:scale-105">
        <i id="chat-toggle-icon" class="fa-solid fa-comment-dots text-xl"></i>
        @if ($isUser)
            <span id="chat-unread-badge"
                class="hidden absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold w-5 h-5 rounded-full items-center justify-center">0</span>
        @endif
    </button>

    {{-- Popup panel --}}
    <div id="chat-panel"
        class="hidden absolute bottom-[70px] right-0 w-[340px] max-w-[90vw] bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden flex flex-col"
        style="height: 460px;">

        {{-- Header --}}
        <div class="bg-primary text-white px-4 py-3 flex items-center justify-between shrink-0">
            <div>
                <p class="font-bold text-sm">Chat dengan Admin Caysie</p>
                <p class="text-xs text-white/80">Biasanya balas dalam beberapa menit</p>
            </div>
            <button onclick="ChatWidget.toggle()" class="text-white/80 hover:text-white">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        @if ($isUser)
            <div class="flex justify-end px-3 pt-2 bg-gray-50">
                <button onclick="ChatWidget.deleteHistory()"
                    class="text-[11px] text-gray-400 hover:text-red-500 flex items-center gap-1">
                    <i class="fa-solid fa-trash-can text-[10px]"></i> Hapus riwayat
                </button>
            </div>

            {{-- Area pesan --}}
            <div id="chat-messages" class="flex-1 min-h-0 overflow-y-auto px-3 py-3 space-y-2 bg-gray-50">
                <p class="text-center text-xs text-gray-400 py-6">Memuat percakapan...</p>
            </div>

            {{-- Input --}}
            <form id="chat-form" onsubmit="ChatWidget.send(event)"
                class="border-t border-gray-100 p-2 flex items-center gap-2 shrink-0">
                <input id="chat-input" type="text" maxlength="1000" autocomplete="off" placeholder="Tulis pesan..."
                    class="flex-1 text-sm border border-gray-200 rounded-full px-4 py-2 focus:outline-none focus:border-primary">
                <button type="submit"
                    class="w-9 h-9 rounded-full bg-primary text-white flex items-center justify-center hover:bg-primary-dark shrink-0">
                    <i class="fa-solid fa-paper-plane text-xs"></i>
                </button>
            </form>
        @else
            <div class="flex-1 min-h-0 flex items-center justify-center px-6 text-center">
                <p class="text-sm text-gray-500">
                    <a href="{{ route('login') }}" class="text-primary font-bold">Masuk</a> dulu untuk chat langsung
                    dengan admin.
                </p>
            </div>
        @endif

        {{-- Lanjutkan via WhatsApp --}}
        @if ($waLink)
            <a href="{{ $waLink }}" target="_blank" rel="noopener"
                class="shrink-0 flex items-center justify-center gap-2 bg-green-50 hover:bg-green-100 text-green-700 text-xs font-bold py-2.5 border-t border-gray-100">
                <i class="fa-brands fa-whatsapp text-sm"></i>
                Lanjutkan chat lewat WhatsApp
            </a>
        @endif
    </div>
</div>

@if ($isUser)
    <script>
        const ChatWidget = {
            panelOpen: false,
            lastId: 0,
            pollTimer: null,
            badgePollTimer: null,

            toggle() {
                const panel = document.getElementById('chat-panel');
                this.panelOpen = !this.panelOpen;
                panel.classList.toggle('hidden', !this.panelOpen);

                if (this.panelOpen) {
                    this.hideBadge();
                    // Selalu muat ulang transkrip lengkap saat dibuka — lastId bisa
                    // saja sudah bergeser maju gara-gara polling badge di background
                    // saat popup tertutup, jadi jangan lanjut dari situ.
                    this.lastId = 0;
                    this.fetchMessages(true);
                    this.pollTimer = setInterval(() => this.fetchMessages(false), 4000);
                } else {
                    clearInterval(this.pollTimer);
                }
            },

            hideBadge() {
                const badge = document.getElementById('chat-unread-badge');
                if (badge) {
                    badge.classList.add('hidden');
                    badge.textContent = '0';
                }
            },

            async fetchMessages(isInitial) {
                try {
                    const res = await fetch(`{{ route('user.chat.messages') }}?after_id=${this.lastId}`);
                    const data = await res.json();
                    if (!data.messages || data.messages.length === 0) return;

                    const box = document.getElementById('chat-messages');
                    if (isInitial) box.innerHTML = '';

                    data.messages.forEach(m => this.renderMessage(box, m));
                    this.lastId = data.messages[data.messages.length - 1].id;
                    this.badgeAfterId = this.lastId; // sinkron: pesan ini sudah "dilihat" lewat popup
                    box.scrollTop = box.scrollHeight;
                } catch (e) {
                    console.error('Gagal memuat pesan chat', e);
                }
            },

            renderMessage(box, m) {
                const bubble = document.createElement('div');
                const isAdmin = m.sender === 'admin';
                bubble.className = `flex ${isAdmin ? 'justify-start' : 'justify-end'}`;
                bubble.innerHTML = `
                    <div class="max-w-[75%] px-3 py-2 rounded-2xl text-sm ${
                        isAdmin ? 'bg-white border border-gray-200 text-gray-800' : 'bg-primary text-white'
                    }">
                        <p class="whitespace-pre-wrap break-words">${this.escapeHtml(m.message)}</p>
                        <p class="text-[10px] mt-1 opacity-60">${m.time}</p>
                    </div>`;
                box.appendChild(bubble);
            },

            escapeHtml(str) {
                const div = document.createElement('div');
                div.textContent = str;
                return div.innerHTML;
            },

            async send(e) {
                e.preventDefault();
                const input = document.getElementById('chat-input');
                const text = input.value.trim();
                if (!text) return;
                input.value = '';

                try {
                    const res = await fetch(`{{ route('user.chat.send') }}`, {
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
                    const box = document.getElementById('chat-messages');
                    this.renderMessage(box, data.message);
                    this.lastId = data.message.id;
                    this.badgeAfterId = this.lastId;
                    box.scrollTop = box.scrollHeight;
                } catch (e) {
                    console.error('Gagal mengirim pesan', e);
                }
            },

            async deleteHistory() {
                if (!confirm('Hapus semua riwayat chat dengan admin? Tindakan ini tidak bisa dibatalkan.')) return;

                try {
                    await fetch(`{{ route('user.chat.destroy') }}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                    });
                    const box = document.getElementById('chat-messages');
                    box.innerHTML = '<p class="text-center text-xs text-gray-400 py-6">Belum ada percakapan.</p>';
                    this.lastId = 0;
                    this.badgeAfterId = 0;
                } catch (e) {
                    console.error('Gagal menghapus riwayat chat', e);
                }
            },

            // Poll ringan setiap 20 detik walau panel tertutup, buat munculin badge
            // notifikasi "ada balasan baru dari admin" tanpa harus buka popup dulu.
            // Pakai pelacak SENDIRI (badgeAfterId), bukan `lastId` — supaya begitu
            // popup dibuka lagi, riwayat lengkap tetap termuat normal.
            badgeAfterId: 0,

            async pollBadgeIfClosed() {
                if (this.panelOpen) return;
                try {
                    const res = await fetch(
                        `{{ route('user.chat.messages') }}?after_id=${this.badgeAfterId}&peek=1`);
                    const data = await res.json();
                    if (!data.messages || data.messages.length === 0) return;

                    this.badgeAfterId = data.messages[data.messages.length - 1].id;

                    const newAdminMsgs = data.messages.filter(m => m.sender === 'admin');
                    if (newAdminMsgs.length > 0) {
                        const badge = document.getElementById('chat-unread-badge');
                        if (badge) {
                            const current = parseInt(badge.textContent || '0', 10) || 0;
                            badge.textContent = current + newAdminMsgs.length;
                            badge.classList.remove('hidden');
                            badge.classList.add('flex');
                        }
                    }
                } catch (e) {
                    /* diam saja, ini cuma polling latar belakang */ }
            },
        };

        ChatWidget.badgePollTimer = setInterval(() => ChatWidget.pollBadgeIfClosed(), 20000);
    </script>
@endif
