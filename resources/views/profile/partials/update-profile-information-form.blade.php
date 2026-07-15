<div class="flex items-center gap-3 mb-5">
    <span class="w-9 h-9 bg-purple-100 text-primary rounded-xl flex items-center justify-center">
        <i class="fa-solid fa-id-card text-sm"></i>
    </span>
    <div>
        <h2 class="text-base font-black text-dark">Informasi Profil</h2>
        <p class="text-xs text-gray-400">Perbarui nama, email, dan nomor HP akunmu.</p>
    </div>
</div>

<form id="send-verification" method="post" action="{{ route('verification.send') }}">
    @csrf
</form>

<form method="post" action="{{ route('profile.update') }}" class="space-y-4">
    @csrf
    @method('patch')

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        {{-- Name --}}
        <div>
            <label for="name" class="block text-xs font-bold text-gray-500 mb-1.5">Nama Lengkap</label>
            <div class="relative">
                <i class="fa-solid fa-user absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required
                    autofocus autocomplete="name"
                    class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-800 focus:outline-none focus:border-primary focus:bg-white transition @error('name') border-red-300 @enderror">
            </div>
            @error('name')
                <p class="text-xs text-red-500 font-semibold mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        {{-- Phone --}}
        <div>
            <label for="phone" class="block text-xs font-bold text-gray-500 mb-1.5">Nomor HP</label>
            <div class="relative">
                <i class="fa-solid fa-phone absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input id="phone" type="tel" name="phone" value="{{ old('phone', $user->phone) }}"
                    autocomplete="tel" placeholder="08xxxxxxxxxx"
                    class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-800 focus:outline-none focus:border-primary focus:bg-white transition @error('phone') border-red-300 @enderror">
            </div>
            @error('phone')
                <p class="text-xs text-red-500 font-semibold mt-1.5">{{ $message }}</p>
            @enderror
        </div>
    </div>

    {{-- Email --}}
    <div>
        <label for="email" class="block text-xs font-bold text-gray-500 mb-1.5">Email</label>
        <div class="relative">
            <i class="fa-solid fa-envelope absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required
                autocomplete="username"
                class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-800 focus:outline-none focus:border-primary focus:bg-white transition @error('email') border-red-300 @enderror">
        </div>
        @error('email')
            <p class="text-xs text-red-500 font-semibold mt-1.5">{{ $message }}</p>
        @enderror

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
            <div
                class="flex items-center gap-2 bg-yellow-50 border border-yellow-200 text-yellow-700 text-xs font-semibold px-3.5 py-2.5 rounded-xl mt-2.5">
                <i class="fa-solid fa-triangle-exclamation text-yellow-500"></i>
                <span>
                    Email kamu belum terverifikasi.
                    <button form="send-verification" class="underline font-bold hover:text-yellow-800">
                        Kirim ulang email verifikasi
                    </button>
                </span>
            </div>
        @endif
    </div>

    <div class="pt-1">
        <button type="submit"
            class="inline-flex items-center gap-2 bg-gradient-to-r from-primary to-primary-dark text-white text-sm font-black px-6 py-2.5 rounded-xl hover:opacity-90 transition shadow-lg shadow-primary/25">
            <i class="fa-solid fa-floppy-disk"></i>
            Simpan Perubahan
        </button>
    </div>
</form>
