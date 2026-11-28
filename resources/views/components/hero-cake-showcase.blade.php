@props([
    'storeName' => 'Nak Mbarep',
])

<div class="hero-cake-stage relative w-full max-w-[280px] h-[195px] sm:max-w-[340px] sm:h-[265px] md:max-w-[390px] md:h-[300px] lg:max-w-[460px] lg:h-[350px] xl:max-w-[520px] xl:h-[380px] mx-auto flex items-center justify-center overflow-visible select-none"
     data-hero-cake-stage>

    {{-- Warm Subtle Ground Shadow under Center Cake --}}
    <div aria-hidden="true"
         class="absolute left-1/2 bottom-3 sm:bottom-5 lg:bottom-6 -translate-x-1/2 w-[124px] sm:w-[175px] lg:w-[230px] h-4 sm:h-6 rounded-full bg-[#2C1810]/14 blur-md z-10 pointer-events-none">
    </div>

    {{-- 1. Center Main Cake (Emerges softly from center scale(0.3) -> scale(1), then floats) --}}
    <div class="hero-cake-center-pop relative z-20 flex items-center justify-center pointer-events-none">
        <img src="{{ asset('images/cake-no-background/' . rawurlencode('Decorated Chocolate Drip Cake Cutout.png')) }}"
             alt="Kue Spesial {{ $storeName }}"
             class="hero-cake-float w-[120px] sm:w-[175px] md:w-[200px] lg:w-[235px] xl:w-[260px] h-auto object-contain drop-shadow-[0_16px_24px_rgba(44,24,16,0.2)]"
             style="--float-rot: 0deg; --float-y: -5px; --float-tilt: 0.8deg; --float-duration: 5.6s; --float-delay: 1.0s;">
    </div>

    {{-- 2. Top-Left Supporting Cake (2-Layer Polar Spiral: Arm sweeps 540deg, Node expands 0 -> --r-tl) --}}
    <div class="hero-cake-spiral-arm z-[15] pointer-events-none"
         style="--arm-end: -148deg; --spiral-delay: 0.15s; --spiral-duration: 1.6s;">
        <div class="hero-cake-spiral-node"
             style="--orbit-r: var(--r-tl); --arm-end: -148deg; --spiral-delay: 0.15s; --spiral-duration: 1.6s;">
            <img src="{{ asset('images/cake-no-background/cake.png') }}"
                 alt="Mini Cake Cookies & Cream"
                 class="hero-cake-float w-[42px] sm:w-[62px] md:w-[70px] lg:w-[80px] xl:w-[88px] max-w-none h-auto object-contain drop-shadow-[0_10px_16px_rgba(44,24,16,0.16)]"
                 style="--float-rot: -18deg; --float-y: -8px; --float-tilt: 3.2deg; --float-duration: 4.3s; --float-delay: 1.8s;">
        </div>
    </div>

    {{-- 3. Top-Right Supporting Cake (2-Layer Polar Spiral: Arm sweeps 540deg, Node expands 0 -> --r-tr) --}}
    <div class="hero-cake-spiral-arm z-30 pointer-events-none"
         style="--arm-end: -32deg; --spiral-delay: 0.30s; --spiral-duration: 1.65s;">
        <div class="hero-cake-spiral-node"
             style="--orbit-r: var(--r-tr); --arm-end: -32deg; --spiral-delay: 0.30s; --spiral-duration: 1.65s;">
            <img src="{{ asset('images/cake-no-background/' . rawurlencode('Chocolate Drip Cake with Candy Toppings.png')) }}"
                 alt="Chocolate Drip Cake"
                 class="hero-cake-float w-[40px] sm:w-[58px] md:w-[66px] lg:w-[76px] xl:w-[84px] max-w-none h-auto object-contain drop-shadow-[0_12px_18px_rgba(44,24,16,0.18)]"
                 style="--float-rot: 15deg; --float-y: -9px; --float-tilt: -2.8deg; --float-duration: 4.9s; --float-delay: 2.0s;">
        </div>
    </div>

    {{-- 4. Bottom-Left Supporting Cake (2-Layer Polar Spiral: Arm sweeps 540deg, Node expands 0 -> --r-bl) --}}
    <div class="hero-cake-spiral-arm z-[15] pointer-events-none"
         style="--arm-end: 148deg; --spiral-delay: 0.45s; --spiral-duration: 1.72s;">
        <div class="hero-cake-spiral-node"
             style="--orbit-r: var(--r-bl); --arm-end: 148deg; --spiral-delay: 0.45s; --spiral-duration: 1.72s;">
            <img src="{{ asset('images/cake-no-background/hotwheel.png') }}"
                 alt="Custom Cake Tema Balap"
                 class="hero-cake-float w-[50px] sm:w-[74px] md:w-[84px] lg:w-[96px] xl:w-[106px] max-w-none h-auto object-contain drop-shadow-[0_12px_18px_rgba(44,24,16,0.18)]"
                 style="--float-rot: -22deg; --float-y: -7px; --float-tilt: 2.6deg; --float-duration: 5.2s; --float-delay: 2.2s;">
        </div>
    </div>

    {{-- 5. Bottom-Right Supporting Cake (2-Layer Polar Spiral: Arm sweeps 540deg, Node expands 0 -> --r-br) --}}
    <div class="hero-cake-spiral-arm z-30 pointer-events-none"
         style="--arm-end: 32deg; --spiral-delay: 0.60s; --spiral-duration: 1.8s;">
        <div class="hero-cake-spiral-node"
             style="--orbit-r: var(--r-br); --arm-end: 32deg; --spiral-delay: 0.60s; --spiral-duration: 1.8s;">
            <img src="{{ asset('images/cake-no-background/kuromi.png') }}"
                 alt="Custom Cake Karakter Kuromi"
                 class="hero-cake-float w-[44px] sm:w-[64px] md:w-[72px] lg:w-[82px] xl:w-[90px] max-w-none h-auto object-contain drop-shadow-[0_12px_18px_rgba(44,24,16,0.18)]"
                 style="--float-rot: 16deg; --float-y: -8px; --float-tilt: -2.4deg; --float-duration: 4.5s; --float-delay: 2.45s;">
        </div>
    </div>
</div>

<script>
    (function () {
        const stages = document.querySelectorAll('[data-hero-cake-stage]:not([data-burst-bound])');
        if (!stages.length) return;

        const reveal = (el) => {
            requestAnimationFrame(() => {
                el.classList.add('is-in-view');
            });
        };

        if (!('IntersectionObserver' in window)) {
            stages.forEach((stage) => {
                stage.dataset.burstBound = '1';
                reveal(stage);
            });
            return;
        }

        const observer = new IntersectionObserver((entries, obs) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    reveal(entry.target);
                    obs.unobserve(entry.target);
                }
            });
        }, { threshold: 0.15 });

        stages.forEach((stage) => {
            stage.dataset.burstBound = '1';
            observer.observe(stage);
        });
    })();
</script>
