{{--
    Reusable testimonial carousel.

    Expects `$testimonialItems` as an array of associative arrays:
    [
        'name'    => 'Andi R.',
        'meta'    => 'Wonosari, Gunungkidul' (or a formatted date),
        'rating'  => 5,
        'message' => 'Teksnya...',
        'color'   => 'bg-purple-500',
    ]

    Optional:
    $testimonialEmptyTitle, $testimonialEmptyDesc — copy for the empty state.
--}}
@php
    $testimonialItems = $testimonialItems ?? [];
    $testimonialCount = count($testimonialItems);
    $testimonialId = 'tst-' . uniqid();
@endphp

@if ($testimonialCount === 0)
    <div class="max-w-md mx-auto text-center py-6">
        <div class="w-16 h-16 bg-gray-50 rounded-3xl flex items-center justify-center mx-auto mb-4">
            <i class="fa-solid fa-comment-dots text-2xl text-gray-300"></i>
        </div>
        <p class="font-bold text-gray-600 text-sm">{{ $testimonialEmptyTitle ?? 'Belum ada testimoni' }}</p>
        <p class="text-gray-400 text-xs mt-1">
            {{ $testimonialEmptyDesc ?? 'Jadilah pelanggan pertama yang berbagi pengalaman!' }}</p>
    </div>
@else
    <div class="tst-carousel relative" id="{{ $testimonialId }}" data-count="{{ $testimonialCount }}">
        {{-- Fade edges --}}
        <div
            class="pointer-events-none absolute inset-y-0 left-0 w-10 md:w-24 bg-gradient-to-r from-white to-transparent z-10">
        </div>
        <div
            class="pointer-events-none absolute inset-y-0 right-0 w-10 md:w-24 bg-gradient-to-l from-white to-transparent z-10">
        </div>

        <div
            class="tst-viewport overflow-hidden select-none {{ $testimonialCount === 1 ? 'flex justify-center' : '' }}">
            <div class="tst-track flex {{ $testimonialCount === 1 ? '' : 'cursor-grab active:cursor-grabbing' }}"
                style="{{ $testimonialCount === 1 ? '' : 'will-change: transform;' }}">
                @foreach ($testimonialItems as $t)
                    <div class="tst-slide shrink-0 px-3 py-2 w-[86vw] max-w-[380px] sm:w-[380px]"
                        data-index="{{ $loop->index }}">
                        <div
                            class="tst-card h-full bg-gray-50 rounded-2xl p-7 border border-gray-100 transition-all duration-500 ease-out">
                            <div class="flex gap-1 text-yellow-400 mb-4">
                                @for ($i = 0; $i < ($t['rating'] ?? 0); $i++)
                                    <i class="fa-solid fa-star text-sm"></i>
                                @endfor
                                @for ($i = $t['rating'] ?? 0; $i < 5; $i++)
                                    <i class="fa-regular fa-star text-sm text-gray-300"></i>
                                @endfor
                            </div>
                            <p class="text-gray-600 text-sm leading-relaxed mb-6 italic">"{{ $t['message'] }}"</p>
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 {{ $t['color'] ?? 'bg-primary' }} rounded-full flex items-center justify-center text-white font-bold text-sm shrink-0">
                                    {{ strtoupper(substr($t['name'], 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-bold text-gray-800 text-sm">{{ $t['name'] }}</p>
                                    <p class="text-gray-400 text-xs">{{ $t['meta'] ?? '' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        @if ($testimonialCount > 1)
            {{-- Prev / Next --}}
            <button type="button"
                class="tst-prev absolute left-0 md:-left-5 top-1/2 -translate-y-1/2 z-20
                    w-10 h-10 rounded-full bg-white border border-gray-100 shadow-lg text-gray-500
                    hover:text-primary hover:border-primary/30 transition flex items-center justify-center">
                <i class="fa-solid fa-chevron-left text-xs"></i>
            </button>
            <button type="button"
                class="tst-next absolute right-0 md:-right-5 top-1/2 -translate-y-1/2 z-20
                    w-10 h-10 rounded-full bg-white border border-gray-100 shadow-lg text-gray-500
                    hover:text-primary hover:border-primary/30 transition flex items-center justify-center">
                <i class="fa-solid fa-chevron-right text-xs"></i>
            </button>

            {{-- Dots --}}
            <div class="tst-dots flex items-center justify-center gap-2 mt-8">
                @foreach ($testimonialItems as $t)
                    <button type="button" data-index="{{ $loop->index }}"
                        class="tst-dot h-2 rounded-full bg-gray-200 transition-all duration-300"
                        aria-label="Testimoni {{ $loop->iteration }}"></button>
                @endforeach
            </div>
        @endif
    </div>

    @push('styles')
        <style>
            .tst-slide {
                opacity: .45;
                transform: scale(.88);
                transition: opacity .5s ease, transform .5s ease;
            }

            .tst-slide.is-active {
                opacity: 1;
                transform: scale(1);
            }

            .tst-slide.is-active .tst-card {
                background: #ffffff;
                border-color: rgba(108, 99, 255, .25);
                box-shadow: 0 20px 45px rgba(108, 99, 255, .18);
                transform: translateY(-4px);
            }

            .tst-dot {
                width: .5rem;
            }

            .tst-dot.is-active {
                width: 1.75rem;
                background: #6C63FF;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            (function() {
                const root = document.getElementById('{{ $testimonialId }}');
                if (!root) return;

                const viewport = root.querySelector('.tst-viewport');
                const track = root.querySelector('.tst-track');
                const slides = Array.from(root.querySelectorAll('.tst-slide'));
                const dots = Array.from(root.querySelectorAll('.tst-dot'));
                const prevBtn = root.querySelector('.tst-prev');
                const nextBtn = root.querySelector('.tst-next');
                const count = slides.length;

                let active = Math.floor((count - 1) / 2); // start near middle
                let autoplayTimer = null;
                let isDragging = false;
                let dragStartX = 0;
                let dragDeltaX = 0;

                function slideGap() {
                    if (count < 2) return 0;
                    const a = slides[0].getBoundingClientRect();
                    const b = slides[1].getBoundingClientRect();
                    return b.left - a.right;
                }

                function render() {
                    if (count <= 1) {
                        slides.forEach(s => s.classList.add('is-active'));
                        return;
                    }

                    const slideWidth = slides[0].getBoundingClientRect().width;
                    const gap = slideGap();
                    const step = slideWidth + gap;
                    const viewportWidth = viewport.getBoundingClientRect().width;
                    const offset = (viewportWidth - slideWidth) / 2 - active * step;

                    track.style.transform = `translateX(${offset}px)`;

                    slides.forEach((s, i) => s.classList.toggle('is-active', i === active));
                    dots.forEach((d, i) => d.classList.toggle('is-active', i === active));
                }

                function goTo(index, restart = true) {
                    active = ((index % count) + count) % count;
                    render();
                    if (restart) resetAutoplay();
                }

                function next() {
                    goTo(active + 1);
                }

                function prev() {
                    goTo(active - 1);
                }

                function resetAutoplay() {
                    if (count <= 1) return;
                    clearInterval(autoplayTimer);
                    autoplayTimer = setInterval(next, 4500);
                }

                if (nextBtn) nextBtn.addEventListener('click', () => next());
                if (prevBtn) prevBtn.addEventListener('click', () => prev());
                dots.forEach(d => d.addEventListener('click', () => goTo(parseInt(d.dataset.index, 10))));

                // Click a non-active slide to bring it to the center
                slides.forEach((s, i) => {
                    s.addEventListener('click', () => {
                        if (i !== active && !isDragging) goTo(i);
                    });
                });

                // Drag / swipe support
                if (count > 1) {
                    track.addEventListener('pointerdown', e => {
                        isDragging = true;
                        dragStartX = e.clientX;
                        dragDeltaX = 0;
                        track.style.transition = 'none';
                        clearInterval(autoplayTimer);
                    });

                    window.addEventListener('pointermove', e => {
                        if (!isDragging) return;
                        dragDeltaX = e.clientX - dragStartX;
                    });

                    window.addEventListener('pointerup', () => {
                        if (!isDragging) return;
                        isDragging = false;
                        track.style.transition = '';
                        const threshold = 50;
                        if (dragDeltaX > threshold) {
                            prev();
                        } else if (dragDeltaX < -threshold) {
                            next();
                        } else {
                            render();
                            resetAutoplay();
                        }
                    });
                }

                root.addEventListener('mouseenter', () => clearInterval(autoplayTimer));
                root.addEventListener('mouseleave', () => resetAutoplay());

                window.addEventListener('resize', render);

                render();
                resetAutoplay();
            })();
        </script>
    @endpush
@endif
