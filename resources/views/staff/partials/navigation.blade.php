@props(['active' => 'dashboard', 'mobile' => false])

@php
    $currentRoute = request()->route()?->getName() ?? '';

    $linkClasses = 'flex items-center rounded-xl text-sm font-semibold transition-all '
        .($mobile ? 'gap-3 px-4 py-2.5' : 'gap-3.5 px-4 py-3');

    $activeClasses = 'bg-white text-[#6B0F1A] shadow-md shadow-black/5';
    $inactiveClasses = 'text-stone-300 hover:text-white hover:bg-white/10';

    $navigationItems = [
        [
            'key' => 'dashboard',
            'label' => 'Beranda',
            'route' => 'staff.dashboard',
            'patterns' => ['staff.dashboard'],
            'paths' => [
                'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
            ],
        ],
        [
            'key' => 'pesanan',
            'label' => 'Pesanan',
            'route' => 'staff.pesanan.index',
            'patterns' => ['staff.pesanan', 'staff.orders'],
            'paths' => [
                'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z',
            ],
        ],
        [
            'key' => 'produk',
            'label' => 'Produk',
            'route' => 'staff.produk.index',
            'patterns' => ['staff.produk'],
            'paths' => [
                'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
            ],
        ],
        [
            'key' => 'kategori',
            'label' => 'Kategori',
            'route' => 'staff.kategori.index',
            'patterns' => ['staff.kategori'],
            'paths' => [
                'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z',
            ],
        ],
        [
            'key' => 'kasir',
            'label' => 'Kasir / POS',
            'route' => 'staff.kasir.index',
            'patterns' => ['staff.kasir'],
            'paths' => [
                'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z',
            ],
        ],
    ];
@endphp

@foreach ($navigationItems as $item)
    @php
        $isActive = $active === $item['key'];

        foreach ($item['patterns'] as $pattern) {
            if (str_starts_with($currentRoute, $pattern)) {
                $isActive = true;
                break;
            }
        }
    @endphp

    <a href="{{ route($item['route']) }}"
       @if ($isActive) aria-current="page" @endif
       class="{{ $linkClasses }} {{ $isActive ? $activeClasses : $inactiveClasses }}">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            @foreach ($item['paths'] as $path)
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $path }}"/>
            @endforeach
        </svg>
        <span>{{ $item['label'] }}</span>
    </a>
@endforeach
