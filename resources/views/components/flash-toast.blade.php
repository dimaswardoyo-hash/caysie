{{--
    Komponen toast popup global — dipakai di layouts.app & layouts.admin.
    Menangani SEMUA jenis flash message secara konsisten:
    - session('success')
    - session('error')
    - session('warning')
    - session('status')  (dipakai bawaan Laravel Breeze/Fortify, misal di halaman login)

    Sebelumnya tiap halaman punya blok flash message sendiri-sendiri dan
    tidak konsisten (ada yang cuma handle 'success', ada yang cuma 'error',
    dst) — akibatnya beberapa pesan penting (misal error saat generate resi
    di admin, atau warning saat checkout gagal connect Xendit) tidak pernah
    tampil ke user/admin. Komponen ini menggantikan semuanya dari satu tempat.
--}}
@php
    $flashes = [
        'success' => ['bg' => 'bg-green-50', 'border' => 'border-green-200', 'text' => 'text-green-700', 'icon' => 'fa-circle-check', 'iconColor' => 'text-green-500'],
        'error' => ['bg' => 'bg-red-50', 'border' => 'border-red-200', 'text' => 'text-red-600', 'icon' => 'fa-circle-exclamation', 'iconColor' => 'text-red-500'],
        'warning' => ['bg' => 'bg-yellow-50', 'border' => 'border-yellow-200', 'text' => 'text-yellow-800', 'icon' => 'fa-triangle-exclamation', 'iconColor' => 'text-yellow-500'],
        'status' => ['bg' => 'bg-blue-50', 'border' => 'border-blue-200', 'text' => 'text-blue-700', 'icon' => 'fa-circle-info', 'iconColor' => 'text-blue-500'],
    ];

    // Beberapa controller (profile, verifikasi email) mengirim session('status')
    // berupa slug mentah, bukan kalimat siap tampil — dipetakan ke teks Indonesia
    // di sini supaya toast global tetap konsisten dan tidak menampilkan slug mentah.
    $statusSlugMessages = [
        'profile-updated' => 'Profil kamu berhasil diperbarui.',
        'password-updated' => 'Kata sandi kamu berhasil diperbarui.',
        'verification-link-sent' => 'Link verifikasi baru telah dikirim ke email kamu.',
    ];
@endphp

<div id="flash-toast-container"
    class="fixed top-5 right-5 z-[9999] flex flex-col gap-3 w-[92vw] max-w-sm sm:w-96">
    @foreach ($flashes as $key => $style)
        @if (session($key))
            <div data-flash-toast
                class="flash-toast flex items-start gap-3 {{ $style['bg'] }} {{ $style['border'] }} {{ $style['text'] }} border px-5 py-4 rounded-2xl shadow-lg"
                style="animation: flashToastIn .25s ease-out;">
                <i class="fa-solid {{ $style['icon'] }} {{ $style['iconColor'] }} mt-0.5"></i>
                <span class="text-sm font-semibold flex-1">{{ $key === 'status' ? ($statusSlugMessages[session('status')] ?? session('status')) : session($key) }}</span>
                <button type="button" onclick="dismissFlashToast(this.closest('[data-flash-toast]'))"
                    class="text-current/60 hover:text-current transition">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif
    @endforeach
</div>

<style>
    @keyframes flashToastIn {
        from {
            opacity: 0;
            transform: translateX(24px);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes flashToastOut {
        from {
            opacity: 1;
            transform: translateX(0);
        }

        to {
            opacity: 0;
            transform: translateX(24px);
        }
    }
</style>

<script>
    function dismissFlashToast(el) {
        if (!el) return;
        el.style.animation = 'flashToastOut .2s ease-in forwards';
        setTimeout(() => el.remove(), 200);
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('[data-flash-toast]').forEach((el) => {
            setTimeout(() => dismissFlashToast(el), 5000);
        });
    });
</script>
