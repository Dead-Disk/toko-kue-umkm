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
        <div class="relative {{ $withHero ? 'overflow-visible' : 'overflow-hidden rounded-2xl lg:rounded-3xl border border-[#F3E1DC] bg-white shadow-2xs' }}">
            <div id="{{ $previewId }}Track" class="relative w-full {{ $withHero ? 'min-h-[540px] min-[420px]:min-h-[510px] sm:min-h-[340px] md:min-h-[380px] lg:min-h-[430px] xl:min-h-[460px] overflow-visible' : $aspectClass . ' overflow-hidden' }}">
                @if ($withHero)
                    {{-- Slide 0: Hero Section Bawaan (Modern Artisan Patisserie + Centered Cake Composition) --}}
                    <div class="{{ $previewId }}-slide absolute inset-0 w-full h-full overflow-visible transition-all duration-600 ease-out opacity-100 z-10 translate-x-0"
                         data-slide-index="0">
                        {{-- Card Surface Layer (z-10) --}}
                        <div aria-hidden="true" class="absolute inset-0 rounded-2xl lg:rounded-3xl border border-[#E6D9CC] bg-[#F5EDE4] z-10 pointer-events-none"></div>

                        <div class="relative w-full h-full px-4 py-5 sm:px-8 sm:py-8 lg:px-12 lg:py-10 xl:px-16 flex items-center overflow-visible">
                            <div class="relative w-full grid grid-cols-1 sm:grid-cols-12 gap-4 sm:gap-6 lg:gap-10 items-center overflow-visible">
                                {{-- Right Hero Cake Composition (Top on mobile, Right on sm+) --}}
                                <div class="sm:col-span-5 lg:col-span-5 order-1 sm:order-2 relative flex items-center justify-center overflow-visible">
                                    <x-hero-cake-showcase :store-name="$storeSetting->store_name ?? 'Nak Mbarep'" />
                                </div>

                                {{-- Left Text Info --}}
                                <div class="sm:col-span-7 lg:col-span-7 order-2 sm:order-1 relative z-20 space-y-2.5 sm:space-y-3.5 lg:space-y-5 text-center sm:text-left">
                                    {{-- Eyebrow Brand Tag --}}
                                    <div class="inline-flex items-center justify-center sm:justify-start gap-2.5 text-[#7A4B3E]">
                                        <span class="hidden sm:inline-block w-6 h-px bg-[#C9A89A]"></span>
                                        <span class="text-[10px] sm:text-[11px] font-semibold uppercase tracking-[0.22em]">
                                            {{ $storeSetting->store_name ?? 'Nak Mbarep' }} · Artisan Patisserie
                                        </span>
                                    </div>

                                    {{-- Main Headline --}}
                                    <h2 class="text-[26px] sm:text-3xl md:text-[34px] lg:text-[44px] xl:text-[50px] font-serif font-semibold text-[#2C1810] leading-[1.08] tracking-[-0.015em]">
                                        Kue Spesial untuk<br>
                                        <span class="italic font-medium text-[#6B0F1A]">Setiap Momen Berharga</span>
                                    </h2>

                                    {{-- Editorial Subtitle --}}
                                    <p class="text-[13px] sm:text-sm lg:text-[15px] text-[#5E463B] max-w-[34ch] sm:max-w-md mx-auto sm:mx-0 leading-relaxed">
                                        Karena setiap kebahagiaan layak dirayakan dengan kue terbaik — dibuat segar dari dapur kami untuk momen istimewa Anda.
                                    </p>

                                    {{-- Primary & Secondary CTA --}}
                                    <div class="pt-1.5 flex flex-wrap items-center justify-center sm:justify-start gap-3">
                                        <a href="{{ route('customer.produk.index') }}" class="inline-flex items-center gap-2 bg-[#6B0F1A] hover:bg-[#520912] text-white text-xs sm:text-sm font-medium px-5 py-2.5 lg:px-6 lg:py-3 rounded-full transition">
                                            <span>Jelajahi Katalog</span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                        </a>
                                        <a href="{{ route('customer.custom.create') }}" class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-medium text-[#2C1810] hover:text-[#6B0F1A] px-3 py-2 underline underline-offset-4 decoration-[#C9A89A] hover:decoration-[#6B0F1A] transition">
                                            <span>Pesan Custom Cake</span>
                                        </a>
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

                    <div class="{{ $previewId }}-slide absolute inset-0 w-full h-full {{ $withHero ? 'rounded-2xl lg:rounded-3xl overflow-hidden border border-[#E6D9CC] bg-[#FAF2EE]' : '' }} transition-all duration-600 ease-out {{ $isFirst ? 'opacity-100 z-10 translate-x-0' : 'opacity-0 invisible z-0 pointer-events-none translate-x-full' }}"
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
                        class="absolute left-2 sm:left-4 top-1/2 -translate-y-1/2 z-30 w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-white/90 hover:bg-white text-stone-800 shadow-md flex items-center justify-center opacity-0 pointer-events-none -translate-x-2 group-hover:opacity-100 group-hover:pointer-events-auto group-hover:translate-x-0 group-[.nav-active]:opacity-100 group-[.nav-active]:pointer-events-auto group-[.nav-active]:translate-x-0 transition-all duration-300 cursor-pointer"
                        aria-label="Slide Sebelumnya">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <button type="button"
                        id="{{ $previewId }}Next"
                        class="absolute right-2 sm:right-4 top-1/2 -translate-y-1/2 z-30 w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-white/90 hover:bg-white text-stone-800 shadow-md flex items-center justify-center opacity-0 pointer-events-none translate-x-2 group-hover:opacity-100 group-hover:pointer-events-auto group-hover:translate-x-0 group-[.nav-active]:opacity-100 group-[.nav-active]:pointer-events-auto group-[.nav-active]:translate-x-0 transition-all duration-300 cursor-pointer"
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

        const track = document.getElementById('{{ $previewId }}Track');
        const hasHero = {{ $withHero ? 'true' : 'false' }};
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

            if (hasHero && track) {
                track.classList.remove('overflow-visible');
                track.classList.add('overflow-hidden', 'rounded-2xl', 'lg:rounded-3xl');
            }

            // Posisi awal slide target sebelum transisi dimulai
            nextSlide.classList.remove('transition-all', 'duration-600', 'ease-out', 'invisible');
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
                currentSlide.classList.add('invisible');
                if (hasHero && track && targetIndex === 0) {
                    track.classList.remove('overflow-hidden', 'rounded-2xl', 'lg:rounded-3xl');
                    track.classList.add('overflow-visible');
                }
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
