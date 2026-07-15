<div class="flex items-center gap-3 mb-3">
    <span class="w-9 h-9 bg-red-50 text-red-500 rounded-xl flex items-center justify-center">
        <i class="fa-solid fa-triangle-exclamation text-sm"></i>
    </span>
    <div>
        <h2 class="text-base font-black text-red-600">Hapus Akun</h2>
        <p class="text-xs text-gray-400">Tindakan ini permanen dan tidak dapat dibatalkan.</p>
    </div>
</div>

<p class="text-sm text-gray-500 leading-relaxed mb-4">
    Setelah akun dihapus, seluruh data dan riwayat pesananmu akan hilang secara permanen.
    Unduh data yang ingin kamu simpan sebelum melanjutkan.
</p>

<button type="button" onclick="openDeleteAccountModal()"
    class="inline-flex items-center gap-2 bg-red-50 text-red-600 text-sm font-black px-6 py-2.5 rounded-xl hover:bg-red-100 transition border border-red-100">
    <i class="fa-solid fa-trash-can"></i>
    Hapus Akun Saya
</button>

{{-- ===================== MODAL KONFIRMASI (vanilla JS, tanpa Alpine) ===================== --}}
<div id="delete-account-modal" class="hidden fixed inset-0 z-[9999] flex items-center justify-center px-4">
    <div class="absolute inset-0 bg-dark/60 backdrop-blur-sm" onclick="closeDeleteAccountModal()"></div>

    <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-md p-7">
        <div class="w-14 h-14 bg-red-50 rounded-2xl flex items-center justify-center mb-5">
            <i class="fa-solid fa-triangle-exclamation text-red-500 text-xl"></i>
        </div>

        <h3 class="text-lg font-black text-dark mb-2">Yakin ingin menghapus akun?</h3>
        <p class="text-sm text-gray-500 leading-relaxed mb-5">
            Semua data dan riwayat pesananmu akan dihapus permanen. Masukkan kata sandi untuk konfirmasi.
        </p>

        <form method="post" action="{{ route('profile.destroy') }}">
            @csrf
            @method('delete')

            <div class="relative mb-1.5">
                <i class="fa-solid fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input id="delete_password" type="password" name="password" placeholder="Kata sandi kamu"
                    class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-800 focus:outline-none focus:border-red-400 focus:bg-white transition @error('password', 'userDeletion') border-red-300 @enderror">
            </div>
            @error('password', 'userDeletion')
                <p class="text-xs text-red-500 font-semibold mb-3">{{ $message }}</p>
            @enderror

            <div class="flex justify-end gap-3 mt-5">
                <button type="button" onclick="closeDeleteAccountModal()"
                    class="px-5 py-2.5 bg-gray-100 text-gray-600 text-sm font-bold rounded-xl hover:bg-gray-200 transition">
                    Batal
                </button>
                <button type="submit"
                    class="px-5 py-2.5 bg-red-500 text-white text-sm font-black rounded-xl hover:bg-red-600 transition shadow-lg shadow-red-200">
                    Ya, Hapus Akun
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
    <script>
        function openDeleteAccountModal() {
            document.getElementById('delete-account-modal').classList.remove('hidden');
            document.body.classList.add('overflow-y-hidden');
            setTimeout(() => document.getElementById('delete_password')?.focus(), 50);
        }

        function closeDeleteAccountModal() {
            document.getElementById('delete-account-modal').classList.add('hidden');
            document.body.classList.remove('overflow-y-hidden');
        }

        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') closeDeleteAccountModal();
        });

        @if ($errors->userDeletion->isNotEmpty())
            document.addEventListener('DOMContentLoaded', () => openDeleteAccountModal());
        @endif
    </script>
@endpush
