<div class="flex items-center gap-3 mb-5">
    <span class="w-9 h-9 bg-purple-100 text-primary rounded-xl flex items-center justify-center">
        <i class="fa-solid fa-lock text-sm"></i>
    </span>
    <div>
        <h2 class="text-base font-black text-dark">Ubah Kata Sandi</h2>
        <p class="text-xs text-gray-400">Gunakan kata sandi yang panjang dan unik agar akun tetap aman.</p>
    </div>
</div>

<form method="post" action="{{ route('password.update') }}" class="space-y-4">
    @csrf
    @method('put')

    <div>
        <label for="update_password_current_password" class="block text-xs font-bold text-gray-500 mb-1.5">Kata Sandi Saat
            Ini</label>
        <div class="relative">
            <i class="fa-solid fa-key absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input id="update_password_current_password" type="password" name="current_password"
                autocomplete="current-password" placeholder="••••••••"
                class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-800 focus:outline-none focus:border-primary focus:bg-white transition @error('current_password', 'updatePassword') border-red-300 @enderror">
        </div>
        @error('current_password', 'updatePassword')
            <p class="text-xs text-red-500 font-semibold mt-1.5">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label for="update_password_password" class="block text-xs font-bold text-gray-500 mb-1.5">Kata Sandi
                Baru</label>
            <div class="relative">
                <i class="fa-solid fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input id="update_password_password" type="password" name="password" autocomplete="new-password"
                    placeholder="••••••••"
                    class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-800 focus:outline-none focus:border-primary focus:bg-white transition @error('password', 'updatePassword') border-red-300 @enderror">
            </div>
            @error('password', 'updatePassword')
                <p class="text-xs text-red-500 font-semibold mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="update_password_password_confirmation"
                class="block text-xs font-bold text-gray-500 mb-1.5">Konfirmasi Kata Sandi</label>
            <div class="relative">
                <i class="fa-solid fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input id="update_password_password_confirmation" type="password" name="password_confirmation"
                    autocomplete="new-password" placeholder="••••••••"
                    class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-800 focus:outline-none focus:border-primary focus:bg-white transition @error('password_confirmation', 'updatePassword') border-red-300 @enderror">
            </div>
            @error('password_confirmation', 'updatePassword')
                <p class="text-xs text-red-500 font-semibold mt-1.5">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="pt-1">
        <button type="submit"
            class="inline-flex items-center gap-2 bg-gradient-to-r from-primary to-primary-dark text-white text-sm font-black px-6 py-2.5 rounded-xl hover:opacity-90 transition shadow-lg shadow-primary/25">
            <i class="fa-solid fa-shield-halved"></i>
            Perbarui Kata Sandi
        </button>
    </div>
</form>
