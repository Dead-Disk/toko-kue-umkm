@props([
    'category' => null,
    'class' => 'w-full h-full',
])

@php
    $slug = '';
    if ($category instanceof \App\Models\Category) {
        $slug = $category->slug ?? \Illuminate\Support\Str::slug($category->nama_kategori ?? '');
    } elseif ($category instanceof \App\Models\Product) {
        $slug = $category->category?->slug ?? \Illuminate\Support\Str::slug($category->category?->nama_kategori ?? '');
    } elseif (is_string($category)) {
        $slug = \Illuminate\Support\Str::slug($category);
    }

    $svgFile = \App\Models\Category::resolveSvgFilename($slug);
    $svgUrl = asset('assets/kategori/' . $svgFile);
@endphp

<img src="{{ $svgUrl }}"
     alt="{{ $slug ?: 'Kategori' }}"
     loading="lazy"
     {{ $attributes->merge(['class' => $class . ' object-contain select-none pointer-events-none']) }}>
