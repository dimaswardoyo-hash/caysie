<footer id="kontak" class="bg-dark text-white pt-16 pb-8">
    <div class="max-w-7xl mx-auto px-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-10 mb-12">

            {{-- Brand --}}
            <div class="md:col-span-2">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-primary rounded-xl flex items-center justify-center">
                        <i class="fa-solid fa-shirt text-white"></i>
                    </div>
                    <span class="text-2xl font-black tracking-wide">CAYSIE</span>
                </div>
                <p class="text-gray-400 text-sm leading-relaxed mb-6 max-w-xs">
                    Toko fashion anak muda dari Gunungkidul. Koleksi kaos & celana terkini dengan kualitas premium
                    dan harga yang bersahabat.
                </p>
                <div class="flex gap-3">
                    <a href="#"
                        class="w-10 h-10 bg-gray-800 hover:bg-primary rounded-xl flex items-center justify-center transition">
                        <i class="fa-brands fa-tiktok text-white text-sm"></i>
                    </a>
                    <a href="#"
                        class="w-10 h-10 bg-gray-800 hover:bg-orange-500 rounded-xl flex items-center justify-center transition">
                        <i class="fa-solid fa-bag-shopping text-white text-sm"></i>
                    </a>
                    <a href="#"
                        class="w-10 h-10 bg-gray-800 hover:bg-green-600 rounded-xl flex items-center justify-center transition">
                        <i class="fa-brands fa-whatsapp text-white text-sm"></i>
                    </a>
                </div>
            </div>

            {{-- Menu --}}
            <div>
                <h4 class="font-bold text-white mb-4">Menu</h4>
                <ul class="space-y-3 text-sm text-gray-400">
                    <li><a href="{{ route('home') }}" class="hover:text-white transition">Beranda</a></li>
                    <li><a href="#produk" class="hover:text-white transition">Produk</a></li>
                    <li><a href="{{ route('login') }}" class="hover:text-white transition">Masuk</a></li>
                    <li><a href="{{ route('register') }}" class="hover:text-white transition">Daftar</a></li>
                </ul>
            </div>

            {{-- Kontak --}}
            <div>
                <h4 class="font-bold text-white mb-4">Kontak</h4>
                <ul class="space-y-3 text-sm text-gray-400">
                    <li class="flex items-start gap-2"><i class="fa-solid fa-location-dot mt-1 text-primary"></i>
                        Gunungkidul, Yogyakarta</li>
                    <li class="flex items-start gap-2"><i class="fa-brands fa-whatsapp mt-1 text-green-400"></i>
                        08xx-xxxx-xxxx</li>
                    <li class="flex items-start gap-2"><i class="fa-regular fa-clock mt-1 text-yellow-400"></i> Buka
                        08.00 – 21.00 WIB</li>
                    <li class="flex items-start gap-2"><i class="fa-regular fa-envelope mt-1 text-blue-400"></i>
                        caysie@email.com</li>
                </ul>
            </div>
        </div>

        <div class="border-t border-gray-800 pt-8 flex flex-col md:flex-row items-center justify-between gap-4">
            <p class="text-sm text-gray-500">© {{ date('Y') }} Caysie. All rights reserved. Made with ❤️ in
                Gunungkidul</p>
            <div class="flex gap-4 text-sm text-gray-500">
                <a href="#" class="hover:text-white transition">Kebijakan Privasi</a>
                <a href="#" class="hover:text-white transition">Syarat & Ketentuan</a>
            </div>
        </div>
    </div>
</footer>
