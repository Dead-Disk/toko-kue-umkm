@props([
    'banners' => collect(),
    'setting' => null,
    'previewId' => 'bannerCarousel',
    'withHero' => false,
])

@php
    $storeSetting = $setting ?? \App\Models\StoreSetting::current();
    $bannerList = collect($banners)->values();
    $aspectClass = $storeSetting->bannerAspectRatioClass();
    $globalScale = $storeSetting->banner_scale ?? 'cover';
    $duration = max(1, (int) ($storeSetting->banner_slide_duration ?? 5));
    $autoSlide = (bool) ($storeSetting->banner_auto_slide ?? true);

    $totalSlides = ($withHero ? 1 : 0) + $bannerList->count();
    $isMultiple = $totalSlides > 1;
@endphp

<div id="{{ $previewId }}" class="w-full relative select-none group" data-interval="{{ $duration * 1000 }}" data-auto-slide="{{ $autoSlide ? 'true' : 'false' }}">
    @if ($totalSlides > 0)
        {{-- Carousel Viewport --}}
        <div class="relative overflow-hidden {{ $withHero ? 'rounded-[24px] lg:rounded-[32px] border border-[#F4DDD5] bg-[#FAF0EB] shadow-xs' : 'rounded-2xl lg:rounded-3xl border border-[#F3E1DC] bg-white shadow-2xs' }}">
            <div id="{{ $previewId }}Track" class="relative w-full {{ $aspectClass }} {{ $withHero ? 'min-h-[190px] sm:min-h-[220px] md:min-h-[280px] lg:min-h-[340px] xl:min-h-[380px]' : '' }} overflow-hidden">
                @if ($withHero)
                    {{-- Slide 0: Hero Section Bawaan --}}
                    <div class="{{ $previewId }}-slide absolute inset-0 w-full h-full transition-all duration-600 ease-out opacity-100 z-10 translate-x-0"
                         data-slide-index="0">
                        <div class="w-full h-full p-3.5 sm:p-5 lg:p-6 xl:p-8 flex items-center">
                            <div class="w-full grid grid-cols-12 gap-2.5 sm:gap-6 lg:gap-8 items-center relative z-10">
                                {{-- Left Text Info --}}
                                <div class="col-span-7 lg:col-span-6 space-y-1.5 sm:space-y-3 lg:space-y-4 xl:space-y-5">
                                    {{-- Mini Brand Tag --}}
                                    <div class="inline-flex items-center gap-1.5 lg:gap-2 text-[#6B0F1A]">
                                        <x-store-logo class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6 shrink-0" />
                                        <span class="font-serif font-bold text-[11px] sm:text-xs lg:text-sm tracking-tight truncate">Nak Mbarep <span class="text-rose-500 font-sans">Kue</span></span>
                                        <span class="hidden sm:inline text-[9px] sm:text-[10px] lg:text-xs text-rose-800/80 font-normal">· Kue Lezat, Momen Lebih Manis ♡</span>
                                    </div>

                                    {{-- Main Headline --}}
                                    <h2 class="text-sm sm:text-xl md:text-2xl lg:text-3xl xl:text-4xl font-serif font-bold text-[#540F18] leading-tight">
                                        Kue Spesial untuk<br>Setiap Momen Berharga
                                    </h2>

                                    {{-- Quote Badge --}}
                                    <div class="inline-block bg-white/70 border border-dashed border-[#DE9DA7] text-[#6B0F1A] text-[9px] sm:text-xs lg:text-sm px-2 sm:px-2.5 py-0.5 lg:px-4 lg:py-1 rounded-full font-medium shadow-2xs max-w-full truncate">
                                        Karena setiap kebahagiaan layak dirayakan dengan kue terbaik ♡
                                    </div>

                                    {{-- Cute Chef Mascot --}}
                                    <div class="pt-0.5 lg:pt-1 flex items-center gap-1.5 lg:gap-2.5">
                                        <img src="{{ asset('images/hero-mascot.png') }}" alt="Maskot Nak Mbarep" class="h-8 sm:h-12 lg:h-16 xl:h-18 w-auto object-contain shrink-0">
                                        <span class="text-[9px] sm:text-xs lg:text-sm text-[#7A2832] italic line-clamp-1">Dari dapur kecil, untuk kebahagiaan besar ♡</span>
                                    </div>
                                </div>

                                {{-- Right Hero Cake Image --}}
                                <div class="col-span-5 lg:col-span-6 relative flex items-center justify-center h-full">
                                    <div class="relative w-full max-w-[150px] sm:max-w-[220px] lg:max-w-[320px] xl:max-w-[380px] flex items-center justify-center">
                                        <img src="{{ asset('images/hero-new-banner.png') }}" alt="Strawberry Cake Spesial Nak Mbarep" class="block w-full h-auto max-h-[140px] sm:max-h-[220px] lg:max-h-[300px] xl:max-h-[360px] object-contain drop-shadow-sm">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Banner Slides --}}
                @foreach ($bannerList as $index => $banner)
                    @php
                        $slideIdx = $withHero ? $index + 1 : $index;
                        $isFirst = $slideIdx === 0;
                        $bTitle = is_array($banner) ? ($banner['judul'] ?? $banner['title'] ?? null) : ($banner->judul ?? $banner->title ?? null);
                        $bSubtitle = is_array($banner) ? ($banner['subjudul'] ?? $banner['subtitle'] ?? $banner['description'] ?? null) : ($banner->subjudul ?? $banner->subtitle ?? $banner->description ?? null);
                        $rawImage = is_array($banner) ? ($banner['gambar_url'] ?? $banner['gambar'] ?? $banner['image'] ?? null) : ($banner->gambar_url ?? $banner->gambar ?? $banner->image ?? null);
                        $bImage = \App\Models\Banner::resolveImageUrl($rawImage);
                        $bLink = is_array($banner) ? ($banner['link'] ?? $banner['url'] ?? null) : ($banner->link ?? $banner->url ?? null);
                        $bBadge = is_array($banner) ? ($banner['badge'] ?? null) : ($banner->badge ?? null);
                        $bScale = is_array($banner) ? ($banner['scale'] ?? null) : ($banner->scale ?? null);
                        $scaleClass = $storeSetting->bannerScaleClass($bScale);
                    @endphp

                    <div class="{{ $previewId }}-slide absolute inset-0 w-full h-full transition-all duration-600 ease-out {{ $isFirst ? 'opacity-100 z-10 translate-x-0' : 'opacity-0 z-0 pointer-events-none translate-x-full' }}"
                         data-slide-index="{{ $slideIdx }}">
                        @if ($bLink)
                            <a href="{{ $bLink }}" class="block w-full h-full relative group/item overflow-hidden" title="{{ $bTitle ?? 'Lihat Promo' }}">
                        @else
                            <div class="block w-full h-full relative overflow-hidden">
                        @endif

                        {{-- Banner Image --}}
                        <img src="{{ $bImage }}"
                             alt="{{ $bTitle ?? 'Banner Toko' }}"
                             class="w-full h-full {{ $scaleClass }} bg-[#FAF2EE] transition-transform duration-700 group-hover/item:scale-102">

                        @if ($bLink)
                            </a>
                        @else
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            @if ($isMultiple)
                {{-- Navigation Arrows (Default tersembunyi/idle, muncul saat hover di desktop atau sentuh di mobile) --}}
                <button type="button"
                        id="{{ $previewId }}Prev"
                        class="absolute left-2 sm:left-4 top-1/2 -translate-y-1/2 z-20 w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-white/90 hover:bg-white text-stone-800 shadow-md flex items-center justify-center opacity-0 pointer-events-none -translate-x-2 group-hover:opacity-100 group-hover:pointer-events-auto group-hover:translate-x-0 group-[.nav-active]:opacity-100 group-[.nav-active]:pointer-events-auto group-[.nav-active]:translate-x-0 transition-all duration-300 cursor-pointer"
                        aria-label="Slide Sebelumnya">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <button type="button"
                        id="{{ $previewId }}Next"
                        class="absolute right-2 sm:right-4 top-1/2 -translate-y-1/2 z-20 w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-white/90 hover:bg-white text-stone-800 shadow-md flex items-center justify-center opacity-0 pointer-events-none translate-x-2 group-hover:opacity-100 group-hover:pointer-events-auto group-hover:translate-x-0 group-[.nav-active]:opacity-100 group-[.nav-active]:pointer-events-auto group-[.nav-active]:translate-x-0 transition-all duration-300 cursor-pointer"
                        aria-label="Slide Selanjutnya">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </button>
            @endif
        </div>

        @if ($isMultiple)
            {{-- Pagination Dots (Di Luar dan Di Bawah Carousel) --}}
            <div class="flex items-center justify-center gap-1.5 sm:gap-2 pt-2.5 pb-0.5">
                @for ($i = 0; $i < $totalSlides; $i++)
                    <button type="button"
                            class="{{ $previewId }}-dot transition-all duration-300 {{ $i === 0 ? 'w-5 sm:w-6 h-1.5 sm:h-2 bg-[#6B0F1A] rounded-full' : 'w-1.5 sm:w-2 h-1.5 sm:h-2 bg-stone-300 hover:bg-stone-400 rounded-full' }}"
                            data-slide-target="{{ $i }}"
                            aria-label="Lompat ke slide ke-{{ $i + 1 }}">
                    </button>
                @endfor
            </div>
        @endif
    @else
        {{-- Empty State: Hanya Ditampilkan Jika Tidak Ada Banner dan Bukan Tampilan Customer Hero (Misal: Preview Admin) --}}
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

@if ($isMultiple)
<script>
    (function() {
        const root = document.getElementById('{{ $previewId }}');
        if (!root) return;

        const slides = Array.from(root.querySelectorAll('.{{ $previewId }}-slide'));
        const dots = Array.from(root.querySelectorAll('.{{ $previewId }}-dot'));
        const prevBtn = document.getElementById('{{ $previewId }}Prev');
        const nextBtn = document.getElementById('{{ $previewId }}Next');
        const intervalTime = parseInt(root.dataset.interval, 10) || 5000;
        const autoSlideEnabled = root.dataset.autoSlide === 'true';

        if (slides.length <= 1) return;

        let currentIndex = 0;
        let isTransitioning = false;
        let timer = null;

        function updateDots(newIndex) {
            dots.forEach((dot, i) => {
                if (i === newIndex) {
                    dot.className = '{{ $previewId }}-dot transition-all duration-300 w-5 sm:w-6 h-1.5 sm:h-2 bg-[#6B0F1A] rounded-full';
                } else {
                    dot.className = '{{ $previewId }}-dot transition-all duration-300 w-1.5 sm:w-2 h-1.5 sm:h-2 bg-stone-300 hover:bg-stone-400 rounded-full';
                }
            });
        }

        function goToSlide(targetIndex, direction = 1) {
            if (targetIndex === currentIndex || isTransitioning) return;
            isTransitioning = true;

            const currentSlide = slides[currentIndex];
            const nextSlide = slides[targetIndex];

            // Posisi awal slide target sebelum transisi dimulai
            nextSlide.classList.remove('transition-all', 'duration-600', 'ease-out');
            if (direction > 0) {
                nextSlide.classList.remove('-translate-x-full', 'translate-x-0');
                nextSlide.classList.add('translate-x-full', 'opacity-0', 'z-10');
            } else {
                nextSlide.classList.remove('translate-x-full', 'translate-x-0');
                nextSlide.classList.add('-translate-x-full', 'opacity-0', 'z-10');
            }

            // Trigger reflow agar browser mendaftarkan posisi awal
            void nextSlide.offsetWidth;

            // Aktifkan kembali kelas transisi
            currentSlide.classList.add('transition-all', 'duration-600', 'ease-out');
            nextSlide.classList.add('transition-all', 'duration-600', 'ease-out');

            // Geser slide saat ini ke luar
            if (direction > 0) {
                currentSlide.classList.remove('translate-x-0', 'opacity-100', 'z-10', 'pointer-events-auto');
                currentSlide.classList.add('-translate-x-full', 'opacity-0', 'z-0', 'pointer-events-none');
            } else {
                currentSlide.classList.remove('translate-x-0', 'opacity-100', 'z-10', 'pointer-events-auto');
                currentSlide.classList.add('translate-x-full', 'opacity-0', 'z-0', 'pointer-events-none');
            }

            // Geser slide baru ke dalam
            nextSlide.classList.remove('translate-x-full', '-translate-x-full', 'opacity-0', 'pointer-events-none');
            nextSlide.classList.add('translate-x-0', 'opacity-100', 'z-10', 'pointer-events-auto');

            updateDots(targetIndex);
            currentIndex = targetIndex;

            setTimeout(() => {
                isTransitioning = false;
            }, 620);
        }

        function next() {
            const nextIndex = (currentIndex + 1) % slides.length;
            goToSlide(nextIndex, 1);
        }

        function prev() {
            const prevIndex = (currentIndex - 1 + slides.length) % slides.length;
            goToSlide(prevIndex, -1);
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

        // Auto-Hide Tombol Navigasi Panah pada Perangkat Sentuh / Mobile (Idle)
        let touchNavTimer = null;

        function showNavOnTouch() {
            root.classList.add('nav-active');
            if (touchNavTimer) {
                clearTimeout(touchNavTimer);
            }
            touchNavTimer = setTimeout(() => {
                root.classList.remove('nav-active');
                touchNavTimer = null;
            }, 2800);
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', (e) => {
                e.preventDefault();
                showNavOnTouch();
                next();
                startTimer();
            });
        }

        if (prevBtn) {
            prevBtn.addEventListener('click', (e) => {
                e.preventDefault();
                showNavOnTouch();
                prev();
                startTimer();
            });
        }

        dots.forEach((dot) => {
            dot.addEventListener('click', (e) => {
                e.preventDefault();
                showNavOnTouch();
                const target = parseInt(dot.dataset.slideTarget, 10);
                if (!isNaN(target)) {
                    goToSlide(target, target > currentIndex ? 1 : -1);
                    startTimer();
                }
            });
        });

        // Pause saat kursor berada di atas carousel (hover)
        root.addEventListener('mouseenter', () => {
            stopTimer();
        });
        root.addEventListener('mouseleave', () => {
            root.classList.remove('nav-active');
            if (touchNavTimer) {
                clearTimeout(touchNavTimer);
                touchNavTimer = null;
            }
            startTimer();
        });

        // Trigger kemunculan tombol saat area banner disentuh / diklik
        root.addEventListener('click', () => {
            showNavOnTouch();
        });

        // Dukungan Sentuhan Geser (Touch Swipe Gesture) di Mobile
        let touchStartX = 0;
        let touchStartY = 0;
        let touchEndX = 0;
        let touchEndY = 0;
        let isTouching = false;

        root.addEventListener('touchstart', (e) => {
            stopTimer();
            showNavOnTouch();
            if (e.touches && e.touches.length === 1) {
                touchStartX = e.touches[0].clientX;
                touchStartY = e.touches[0].clientY;
                touchEndX = touchStartX;
                touchEndY = touchStartY;
                isTouching = true;
            }
        }, { passive: true });

        root.addEventListener('touchmove', (e) => {
            if (!isTouching || !e.touches || e.touches.length !== 1) return;
            touchEndX = e.touches[0].clientX;
            touchEndY = e.touches[0].clientY;
        }, { passive: true });

        root.addEventListener('touchend', () => {
            if (!isTouching) return;
            isTouching = false;
            showNavOnTouch();
            const deltaX = touchEndX - touchStartX;
            const deltaY = touchEndY - touchStartY;

            // Trigger perpindahan jika geseran horizontal > 40px dan lebih dominan dibanding vertikal
            if (Math.abs(deltaX) > 40 && Math.abs(deltaX) > Math.abs(deltaY)) {
                if (deltaX < 0) {
                    next();
                } else {
                    prev();
                }
            }
            startTimer();
        }, { passive: true });

        root.addEventListener('touchcancel', () => {
            isTouching = false;
            startTimer();
        }, { passive: true });

        startTimer();
    })();
</script>
@endif
