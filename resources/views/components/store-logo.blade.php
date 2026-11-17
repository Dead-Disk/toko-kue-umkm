@props([
    'class' => 'h-8 w-8',
])

@php
    $storeSetting = $storeSetting ?? \App\Models\StoreSetting::current();
    $storeName = $storeSetting->store_name ?: 'Nak Mbarep';
    $defaultLogoUrl = asset('images/logo-nav.png');
@endphp

<img src="{{ $storeSetting->logoUrl() }}" alt="Logo {{ $storeName }}" onerror="this.onerror=null;this.src='{{ $defaultLogoUrl }}';" {{ $attributes->except('class')->merge(['class' => $class.' rounded-full object-cover bg-white border border-white/30 shadow-xs shrink-0']) }}>
