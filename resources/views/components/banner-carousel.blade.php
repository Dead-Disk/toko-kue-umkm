@props([
    'banners' => collect(),
    'setting' => null,
    'previewId' => 'bannerCarousel',
])

@php
    $storeSetting = $setting ?? \App\Models\StoreSetting::current();
    $bannerList = collect($banners)->values();
    $aspectClass = $storeSetting->bannerAspectRatioClass();
    $globalScale = $storeSetting->banner_scale ?? 'cover';
    $duration = max(1, (int) ($storeSetting->banner_slide_duration ?? 5));
    $autoSlide = (bool) ($storeSetting->banner_auto_slide ?? true);
@endphp

<div id="{{ $previewId }}" class="w-full relative select-none group" data-interval="{{ $duration * 1000 }}" data-auto-slide="{{ $autoSlide ? 'true' : 'false' }}">
    @if ($bannerList->isNotEmpty())
        {{-- Carousel Viewport --}}
        <div class="relative overflow-hidden rounded-2xl lg:rounded-3xl border border-[#F3E1DC] bg-white shadow-2xs">
            <div id="{{ $previewId }}Track" class="relative w-full {{ $aspectClass }} overflow-hidden">
                @foreach ($bannerList as $index => $banner)
                    @php
                        $bTitle = is_array($banner) ? ($banner['judul'] ?? $banner['title'] ?? null) : ($banner->judul ?? $banner->title ?? null);
                        $bSubtitle = is_array($banner) ? ($banner['subjudul'] ?? $banner['subtitle'] ?? $banner['description'] ?? null) : ($banner->subjudul ?? $banner->subtitle ?? $banner->description ?? null);
                        $bImage = is_array($banner) ? ($banner['gambar_url'] ?? $banner['gambar'] ?? $banner['image'] ?? null) : ($banner->gambar_url ?? $banner->gambar ?? $banner->image ?? null);
                        $bLink = is_array($banner) ? ($banner['link'] ?? $banner['url'] ?? null) : ($banner->link ?? $banner->url ?? null);
                        $bBadge = is_array($banner) ? ($banner['badge'] ?? null) : ($banner->badge ?? null);
                        $bScale = is_array($banner) ? ($banner['scale'] ?? null) : ($banner->scale ?? null);
                        $scaleClass = $storeSetting->bannerScaleClass($bScale);
                    @endphp

                    <div class="{{ $previewId }}-slide absolute inset-0 w-full h-full transition-all duration-700 ease-in-out {{ $index === 0 ? 'opacity-100 z-10 scale-100' : 'opacity-0 z-0 pointer-events-none scale-105' }}"
                         data-slide-index="{{ $index }}">
                        @if ($bLink)
                            <a href="{{ $bLink }}" class="block w-full h-full relative group/item overflow-hidden" title="{{ $bTitle ?? 'Lihat Promo' }}">
                        @else
                            <div class="block w-full h-full relative overflow-hidden">
                        @endif

                        {{-- Banner Image (Murni Tanpa Judul / Subjudul) --}}
                        <img src="{{ $bImage }}"
                             alt="Banner Toko"
                             class="w-full h-full {{ $scaleClass }} bg-[#FAF2EE] transition-transform duration-700 group-hover/item:scale-102">

                        @if ($bLink)
                            </a>
                        @else
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            @if ($bannerList->count() > 1)
                {{-- Navigation Arrows (HANYA muncul saat di-hover, benar-benar hilang saat tidak di-hover) --}}
                <button type="button"
                        id="{{ $previewId }}Prev"
                        class="absolute left-2.5 sm:left-4 top-1/2 -translate-y-1/2 z-20 w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-white/90 hover:bg-white text-stone-800 shadow-md flex items-center justify-center opacity-0 group-hover:opacity-100 pointer-events-none group-hover:pointer-events-auto transition-all duration-300 -translate-x-2 group-hover:translate-x-0 cursor-pointer"
                        aria-label="Banner Sebelumnya">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <button type="button"
                        id="{{ $previewId }}Next"
                        class="absolute right-2.5 sm:right-4 top-1/2 -translate-y-1/2 z-20 w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-white/90 hover:bg-white text-stone-800 shadow-md flex items-center justify-center opacity-0 group-hover:opacity-100 pointer-events-none group-hover:pointer-events-auto transition-all duration-300 translate-x-2 group-hover:translate-x-0 cursor-pointer"
                        aria-label="Banner Selanjutnya">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </button>
            @endif
        </div>

        @if ($bannerList->count() > 1)
            {{-- Pagination Dots (Di Luar dan Di Bawah Banner Toko) --}}
            <div class="flex items-center justify-center gap-1.5 sm:gap-2 pt-2.5 pb-0.5">
                @foreach ($bannerList as $idx => $b)
                    <button type="button"
                            class="{{ $previewId }}-dot transition-all duration-300 {{ $idx === 0 ? 'w-5 sm:w-6 h-1.5 sm:h-2 bg-[#6B0F1A] rounded-full' : 'w-1.5 sm:w-2 h-1.5 sm:h-2 bg-stone-300 hover:bg-stone-400 rounded-full' }}"
                            data-slide-target="{{ $idx }}"
                            aria-label="Lompat ke banner ke-{{ $idx + 1 }}">
                    </button>
                @endforeach
            </div>
        @endif
    @else
        {{-- Empty State: Ketika Tidak Ada Banner --}}
        <div class="w-full rounded-2xl lg:rounded-3xl border-2 border-dashed border-[#DE9DA7]/60 bg-white/70 backdrop-blur-xs p-6 sm:p-8 lg:p-10 flex flex-col items-center justify-center text-center transition hover:border-[#DE9DA7]">
            <div class="w-11 h-11 sm:w-13 sm:h-13 lg:w-14 lg:h-14 rounded-full bg-[#FAF0EB] border border-[#F4DDD5] flex items-center justify-center text-[#B87A85] mb-2 sm:mb-2.5 shadow-2xs">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 lg:w-7 lg:h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v18M3 4.5h15.5a1 1 0 0 1 .8 1.6L16.7 9l2.6 2.9a1 1 0 0 1-.8 1.6H3" />
                </svg>
            </div>
            <p class="text-xs sm:text-sm font-semibold text-[#6B0F1A] tracking-wide">
                tidak ada banner
            </p>
            <p class="text-[10px] sm:text-xs text-stone-400 mt-0.5">
                Saat ini belum ada banner yang ditampilkan.
            </p>
        </div>
    @endif
</div>

@if ($bannerList->count() > 1)
<script>
    (function() {
        const root = document.getElementById('{{ $previewId }}');
        if (!root) return;

        const slides = root.querySelectorAll('.{{ $previewId }}-slide');
        const dots = root.querySelectorAll('.{{ $previewId }}-dot');
        const prevBtn = document.getElementById('{{ $previewId }}Prev');
        const nextBtn = document.getElementById('{{ $previewId }}Next');
        const intervalTime = parseInt(root.dataset.interval, 10) || 5000;
        const autoSlideEnabled = root.dataset.autoSlide === 'true';

        let currentIndex = 0;
        let timer = null;

        function showSlide(index) {
            if (index < 0) {
                index = slides.length - 1;
            } else if (index >= slides.length) {
                index = 0;
            }

            slides.forEach((slide, i) => {
                if (i === index) {
                    slide.classList.remove('opacity-0', 'z-0', 'pointer-events-none', 'scale-105');
                    slide.classList.add('opacity-100', 'z-10', 'scale-100');
                } else {
                    slide.classList.remove('opacity-100', 'z-10', 'scale-100');
                    slide.classList.add('opacity-0', 'z-0', 'pointer-events-none', 'scale-105');
                }
            });

            dots.forEach((dot, i) => {
                if (i === index) {
                    dot.className = '{{ $previewId }}-dot transition-all duration-300 w-5 sm:w-6 h-1.5 sm:h-2 bg-[#6B0F1A] rounded-full';
                } else {
                    dot.className = '{{ $previewId }}-dot transition-all duration-300 w-1.5 sm:w-2 h-1.5 sm:h-2 bg-stone-300 hover:bg-stone-400 rounded-full';
                }
            });

            currentIndex = index;
        }

        function next() {
            showSlide(currentIndex + 1);
        }

        function prev() {
            showSlide(currentIndex - 1);
        }

        function startTimer() {
            if (!autoSlideEnabled || slides.length <= 1) return;
            stopTimer();
            timer = setInterval(next, intervalTime);
        }

        function stopTimer() {
            if (timer) {
                clearInterval(timer);
                timer = null;
            }
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                next();
                startTimer();
            });
        }

        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                prev();
                startTimer();
            });
        }

        dots.forEach(dot => {
            dot.addEventListener('click', () => {
                const target = parseInt(dot.dataset.slideTarget, 10);
                showSlide(target);
                startTimer();
            });
        });

        root.addEventListener('mouseenter', stopTimer);
        root.addEventListener('mouseleave', startTimer);
        root.addEventListener('touchstart', stopTimer, { passive: true });
        root.addEventListener('touchend', startTimer, { passive: true });

        startTimer();
    })();
</script>
@endif
