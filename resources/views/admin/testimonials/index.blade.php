@extends('layouts.admin')
@section('title', 'Kelola Testimoni')

@section('content')

    {{-- Alert --}}
    @if (session('success'))
        <div class="mb-6 flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 px-5 py-4 rounded-2xl">
            <i class="fa-solid fa-circle-check text-green-500 text-lg"></i>
            <span class="font-semibold text-sm">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6 mb-6 sm:mb-8">
        <div class="bg-white rounded-xl sm:rounded-2xl p-3 sm:p-6 shadow-sm border border-gray-100">
            <div
                class="w-9 h-9 sm:w-12 sm:h-12 bg-purple-100 rounded-lg sm:rounded-xl flex items-center justify-center mb-2 sm:mb-4">
                <i class="fa-solid fa-comment-dots text-purple-600 text-sm sm:text-xl"></i>
            </div>
            <p class="text-xl sm:text-3xl font-bold text-gray-800">{{ $stats['total'] }}</p>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Total Testimoni</p>
        </div>

        <div class="bg-white rounded-xl sm:rounded-2xl p-3 sm:p-6 shadow-sm border border-gray-100">
            <div
                class="w-9 h-9 sm:w-12 sm:h-12 bg-green-100 rounded-lg sm:rounded-xl flex items-center justify-center mb-2 sm:mb-4">
                <i class="fa-solid fa-eye text-green-600 text-sm sm:text-xl"></i>
            </div>
            <p class="text-xl sm:text-3xl font-bold text-gray-800">{{ $stats['approved'] }}</p>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Ditampilkan</p>
        </div>

        <div class="bg-white rounded-xl sm:rounded-2xl p-3 sm:p-6 shadow-sm border border-gray-100">
            <div
                class="w-9 h-9 sm:w-12 sm:h-12 bg-gray-100 rounded-lg sm:rounded-xl flex items-center justify-center mb-2 sm:mb-4">
                <i class="fa-solid fa-eye-slash text-gray-500 text-sm sm:text-xl"></i>
            </div>
            <p class="text-xl sm:text-3xl font-bold text-gray-800">{{ $stats['hidden'] }}</p>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Disembunyikan</p>
        </div>

        <div class="bg-white rounded-xl sm:rounded-2xl p-3 sm:p-6 shadow-sm border border-gray-100">
            <div
                class="w-9 h-9 sm:w-12 sm:h-12 bg-yellow-100 rounded-lg sm:rounded-xl flex items-center justify-center mb-2 sm:mb-4">
                <i class="fa-solid fa-star text-yellow-500 text-sm sm:text-xl"></i>
            </div>
            <p class="text-xl sm:text-3xl font-bold text-gray-800">{{ $stats['avg_rating'] }}</p>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Rata-rata Rating</p>
        </div>
    </div>

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <p class="text-sm text-gray-400">Total {{ is_object($testimonials) && method_exists($testimonials, 'total') ? $testimonials->total() : count($testimonials) }} testimoni</p>
    </div>

    {{-- Filters --}}
    <form method="GET" class="flex flex-wrap gap-3 mb-6">
        <div class="relative flex-1 min-w-[200px] max-w-sm">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Cari nama, email, atau isi testimoni..."
                class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-primary transition">
        </div>

        <select name="status"
            class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-primary transition">
            <option value="">Semua Status</option>
            <option value="approved" @selected(request('status') === 'approved')>Ditampilkan</option>
            <option value="hidden" @selected(request('status') === 'hidden')>Disembunyikan</option>
        </select>

        <select name="rating"
            class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-primary transition">
            <option value="">Semua Rating</option>
            @for ($i = 5; $i >= 1; $i--)
                <option value="{{ $i }}" @selected((string) request('rating') === (string) $i)>{{ $i }} Bintang</option>
            @endfor
        </select>

        <button type="submit"
            class="px-5 py-2.5 bg-primary text-white text-sm font-bold rounded-xl hover:bg-primary-dark transition">
            Filter
        </button>
        @if (request()->anyFilled(['search', 'status', 'rating']))
            <a href="{{ route('admin.testimonials.index') }}"
                class="px-5 py-2.5 bg-gray-100 text-gray-600 text-sm font-bold rounded-xl">Reset</a>
        @endif
    </form>

    {{-- Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        @if ($testimonials->isEmpty())
            <div class="py-20 text-center text-gray-400">
                <i class="fa-solid fa-comment-dots text-5xl mb-4 opacity-30"></i>
                <p class="font-semibold">Belum ada testimoni yang cocok dengan filter ini</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100 text-xs text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-4 text-left">User</th>
                            <th class="px-4 py-4 text-left">Rating</th>
                            <th class="px-4 py-4 text-left">Testimoni</th>
                            <th class="px-4 py-4 text-left">Tanggal</th>
                            <th class="px-4 py-4 text-center">Status</th>
                            <th class="px-4 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($testimonials as $t)
                            <tr class="hover:bg-gray-50/50 transition align-top">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 bg-primary rounded-full flex items-center justify-center text-white font-black text-sm flex-shrink-0">
                                            {{ strtoupper(substr($t->user->name, 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-bold text-gray-800 truncate">{{ $t->user->name }}</p>
                                            <p class="text-xs text-gray-400 truncate">{{ $t->user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="flex text-yellow-400 text-xs">
                                        @for ($i = 0; $i < $t->rating; $i++)
                                            <i class="fa-solid fa-star"></i>
                                        @endfor
                                        @for ($i = $t->rating; $i < 5; $i++)
                                            <i class="fa-regular fa-star text-gray-300"></i>
                                        @endfor
                                    </div>
                                </td>
                                <td class="px-4 py-4 max-w-xs">
                                    <p class="text-gray-600 text-xs leading-relaxed line-clamp-3">{{ $t->message }}</p>
                                </td>
                                <td class="px-4 py-4 text-gray-400 text-xs whitespace-nowrap">
                                    {{ $t->created_at->isoFormat('D MMM Y') }}
                                </td>
                                <td class="px-4 py-4 text-center">
                                    @if ($t->is_approved)
                                        <span
                                            class="inline-block bg-green-100 text-green-700 text-xs font-black px-3 py-1 rounded-full">
                                            Tampil
                                        </span>
                                    @else
                                        <span
                                            class="inline-block bg-gray-100 text-gray-500 text-xs font-black px-3 py-1 rounded-full">
                                            Disembunyikan
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center justify-center gap-1.5">
                                        {{-- Toggle tampil/sembunyikan --}}
                                        <form method="POST" action="{{ route('admin.testimonials.toggle', $t) }}">
                                            @csrf
                                            @method('patch')
                                            <button type="submit"
                                                title="{{ $t->is_approved ? 'Sembunyikan' : 'Tampilkan' }}"
                                                class="w-8 h-8 rounded-lg flex items-center justify-center transition
                                                {{ $t->is_approved ? 'bg-gray-100 text-gray-500 hover:bg-gray-200' : 'bg-green-50 text-green-600 hover:bg-green-100' }}">
                                                <i
                                                    class="fa-solid {{ $t->is_approved ? 'fa-eye-slash' : 'fa-eye' }} text-xs"></i>
                                            </button>
                                        </form>

                                        {{-- Edit --}}
                                        <button type="button"
                                            class="js-edit-testimoni w-8 h-8 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center hover:bg-blue-100 transition"
                                            title="Edit" data-id="{{ $t->id }}" data-rating="{{ $t->rating }}"
                                            data-message="{{ $t->message }}" data-name="{{ $t->user->name }}"
                                            onclick="openEditTestimoniModal(this)">
                                            <i class="fa-solid fa-pen text-xs"></i>
                                        </button>

                                        {{-- Delete --}}
                                        <form method="POST" action="{{ route('admin.testimonials.destroy', $t) }}"
                                            onsubmit="return confirm('Hapus testimoni ini secara permanen?')">
                                            @csrf
                                            @method('delete')
                                            <button type="submit" title="Hapus"
                                                class="w-8 h-8 bg-red-50 text-red-500 rounded-lg flex items-center justify-center hover:bg-red-100 transition">
                                                <i class="fa-solid fa-trash-can text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if ($testimonials->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">{{ $testimonials->links() }}</div>
            @endif
        @endif
    </div>

    {{-- ===================== MODAL EDIT (vanilla JS) ===================== --}}
    <div id="edit-testimoni-modal" class="hidden fixed inset-0 z-[9999] flex items-center justify-center px-4">
        <div class="absolute inset-0 bg-dark/60 backdrop-blur-sm" onclick="closeEditTestimoniModal()"></div>

        <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-md p-7">
            <div class="flex items-center gap-3 mb-5">
                <span class="w-9 h-9 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-pen text-sm"></i>
                </span>
                <div>
                    <h3 class="text-base font-black text-dark">Edit Testimoni</h3>
                    <p class="text-xs text-gray-400" id="edit-testimoni-name"></p>
                </div>
            </div>

            <form id="edit-testimoni-form" method="POST" action="">
                @csrf
                @method('put')

                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">Rating</label>
                    <div class="flex items-center gap-3 flex-wrap">
                        <div class="flex items-center gap-1" id="edit-testimoni-star-picker"
                            onmouseleave="resetEditTestimoniHover()">
                            @for ($i = 1; $i <= 5; $i++)
                                <button type="button" onclick="setEditTestimoniRating({{ $i }})"
                                    onmouseenter="previewEditTestimoniRating({{ $i }})"
                                    class="edit-testimoni-star text-2xl leading-none transition-transform hover:scale-110 text-gray-300">
                                    <i class="fa-solid fa-star"></i>
                                </button>
                            @endfor
                        </div>
                        <span id="edit-testimoni-rating-label" class="text-xs font-bold text-gray-500"></span>
                    </div>
                    <input type="hidden" name="rating" id="edit-testimoni-rating-input" value="5">
                </div>

                <div class="mb-5">
                    <label for="edit-testimoni-message" class="block text-xs font-bold text-gray-500 mb-1.5">Isi
                        Testimoni</label>
                    <textarea id="edit-testimoni-message" name="message" rows="4" maxlength="500"
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-800 focus:outline-none focus:border-primary focus:bg-white transition"></textarea>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeEditTestimoniModal()"
                        class="px-5 py-2.5 bg-gray-100 text-gray-600 text-sm font-bold rounded-xl hover:bg-gray-200 transition">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-5 py-2.5 bg-gradient-to-r from-primary to-primary-dark text-white text-sm font-black rounded-xl hover:opacity-90 transition shadow-lg shadow-primary/25">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            const editTestimoniRatingLabels = {
                1: 'Sangat Kurang',
                2: 'Kurang',
                3: 'Cukup',
                4: 'Bagus',
                5: 'Sangat Bagus',
            };

            function paintEditTestimoniStars(n) {
                document.querySelectorAll('.edit-testimoni-star').forEach((el, idx) => {
                    el.classList.toggle('text-yellow-400', idx < n);
                    el.classList.toggle('text-gray-300', idx >= n);
                });
            }

            function updateEditTestimoniLabel(n) {
                const label = document.getElementById('edit-testimoni-rating-label');
                if (label) label.textContent = n > 0 ? `${n}/5 — ${editTestimoniRatingLabels[n]}` : '';
            }

            function setEditTestimoniRating(n) {
                document.getElementById('edit-testimoni-rating-input').value = n;
                paintEditTestimoniStars(n);
                updateEditTestimoniLabel(n);
            }

            function previewEditTestimoniRating(n) {
                paintEditTestimoniStars(n);
                updateEditTestimoniLabel(n);
            }

            function resetEditTestimoniHover() {
                const current = parseInt(document.getElementById('edit-testimoni-rating-input')?.value || 0);
                paintEditTestimoniStars(current);
                updateEditTestimoniLabel(current);
            }

            function openEditTestimoniModal(btn) {
                const {
                    id,
                    rating,
                    message,
                    name
                } = btn.dataset;
                document.getElementById('edit-testimoni-form').action = `/admin/testimonials/${id}`;
                document.getElementById('edit-testimoni-name').textContent = name;
                document.getElementById('edit-testimoni-message').value = message;
                setEditTestimoniRating(parseInt(rating));
                document.getElementById('edit-testimoni-modal').classList.remove('hidden');
                document.body.classList.add('overflow-y-hidden');
            }

            function closeEditTestimoniModal() {
                document.getElementById('edit-testimoni-modal').classList.add('hidden');
                document.body.classList.remove('overflow-y-hidden');
            }

            document.addEventListener('keydown', e => {
                if (e.key === 'Escape') closeEditTestimoniModal();
            });
        </script>
    @endpush

@endsection
