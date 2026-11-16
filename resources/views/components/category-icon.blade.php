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

    $type = 'default';
    if (str_contains($slug, 'tart') || str_contains($slug, 'cake') || str_contains($slug, 'ulang-tahun') || str_contains($slug, 'birthday') || str_contains($slug, 'black-forest')) {
        $type = 'tart';
    } elseif (str_contains($slug, 'donat') || str_contains($slug, 'donut')) {
        $type = 'donat';
    } elseif (str_contains($slug, 'gulung') || str_contains($slug, 'roll') || str_contains($slug, 'bolu')) {
        $type = 'gulung';
    } elseif (str_contains($slug, 'spiku') || str_contains($slug, 'lapis') || str_contains($slug, 'surabaya')) {
        $type = 'spiku';
    } elseif (str_contains($slug, 'kering') || str_contains($slug, 'cookie') || str_contains($slug, 'nastar') || str_contains($slug, 'kastengel') || str_contains($slug, 'semprit')) {
        $type = 'kue-kering';
    } elseif (str_contains($slug, 'roti') || str_contains($slug, 'bread') || str_contains($slug, 'bakery') || str_contains($slug, 'croissant') || str_contains($slug, 'pastry')) {
        $type = 'roti';
    }
@endphp

<svg {{ $attributes->merge(['class' => $class]) }} viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" role="img" aria-hidden="true">
    @if ($type === 'tart')
        {{-- KUE TART / BIRTHDAY CAKE --}}
        <!-- Piring / Alas Kue -->
        <ellipse cx="50" cy="85" rx="42" ry="7" fill="#E8B8AE" fill-opacity="0.5"/>
        <ellipse cx="50" cy="83" rx="38" ry="6" fill="#F4DDD5"/>
        <!-- Tier Bawah (Base Cake) -->
        <path d="M18 58 C18 58, 22 52, 50 52 C78 52, 82 58, 82 58 V77 C82 82, 70 85, 50 85 C30 85, 18 82, 18 77 Z" fill="#FDE8E1"/>
        <path d="M18 77 C18 82, 30 85, 50 85 C70 85, 82 82, 82 77 V74 C82 79, 70 82, 50 82 C30 82, 18 79, 18 74 Z" fill="#E8B8AE"/>
        <!-- Lelehan Krim Cokelat/Maroon Tier Bawah -->
        <path d="M18 58 C22 63, 26 64, 30 61 C34 58, 38 65, 42 63 C46 60, 50 66, 54 62 C58 59, 62 65, 66 61 C70 58, 74 64, 78 60 C80 59, 82 58, 82 58 C78 52, 50 52, 18 58 Z" fill="#6B0F1A"/>
        <!-- Tier Atas -->
        <path d="M28 36 C28 36, 32 31, 50 31 C68 31, 72 36, 72 36 V54 C72 58, 62 61, 50 61 C38 61, 28 58, 28 54 Z" fill="#FFF5F2"/>
        <path d="M28 54 C28 58, 38 61, 50 61 C62 61, 72 58, 72 54 V52 C72 56, 62 59, 50 59 C38 59, 28 56, 28 52 Z" fill="#F4DDD5"/>
        <!-- Drips Krim Strawberry Tier Atas -->
        <path d="M28 36 C31 41, 35 42, 39 39 C43 37, 46 43, 50 40 C54 37, 57 43, 61 39 C65 37, 69 41, 72 36 C68 31, 50 31, 28 36 Z" fill="#E8B8AE"/>
        <!-- Dekorasi Krim Pipa & Buah Ceri -->
        <circle cx="34" cy="34" r="3.5" fill="#DC2626"/>
        <circle cx="50" cy="31" r="4" fill="#DC2626"/>
        <circle cx="66" cy="34" r="3.5" fill="#DC2626"/>
        <!-- Lilin Ulang Tahun -->
        <rect x="48" y="16" width="4" height="15" rx="1.5" fill="#F43F5E"/>
        <path d="M49 19 L51 17 M49 24 L51 22 M49 29 L51 27" stroke="#FFFFFF" stroke-width="1" stroke-linecap="round"/>
        <!-- Sumbu Lilin -->
        <line x1="50" y1="16" x2="50" y2="13" stroke="#4B5563" stroke-width="1.2" stroke-linecap="round"/>
        <!-- Api Lilin -->
        <path d="M50 7 C50 7, 53 10, 53 12 C53 13.6, 51.6 15, 50 15 C48.4 15, 47 13.6, 47 12 C47 10, 50 7, 50 7 Z" fill="#F59E0B"/>
        <path d="M50 10 C50 10, 51.5 11.5, 51.5 12.5 C51.5 13.3, 50.8 14, 50 14 C49.2 14, 48.5 13.3, 48.5 12.5 C48.5 11.5, 50 10, 50 10 Z" fill="#FEF08A"/>

    @elseif ($type === 'donat')
        {{-- DONAT DENGAN GLAZE & MEISES --}}
        <!-- Bayangan Bawah -->
        <ellipse cx="50" cy="85" rx="36" ry="7" fill="#E8B8AE" fill-opacity="0.4"/>
        <!-- Badan Donat (Golden Dough) -->
        <circle cx="50" cy="49" r="36" fill="#F59E0B"/>
        <circle cx="50" cy="51" r="36" fill="#D97706" fill-opacity="0.25"/>
        <!-- Lubang Donat Bawah (Kedalaman) -->
        <ellipse cx="50" cy="53" rx="14" ry="12" fill="#B45309"/>
        <!-- Glaze Cokelat / Strawberry -->
        <path d="M16 47 C17 31, 31 16, 50 16 C69 16, 83 31, 84 47 C85 55, 81 57, 78 52 C75 48, 72 58, 69 54 C66 50, 63 60, 59 55 C55 51, 52 58, 48 54 C44 50, 41 61, 37 56 C33 52, 30 58, 27 53 C24 49, 21 57, 18 53 C16 50, 15 48, 16 47 Z" fill="#6B0F1A"/>
        <!-- Kilauan Glaze Halus -->
        <path d="M26 27 C33 21, 41 18, 50 18 C55 18, 60 19, 64 21" stroke="#FFFFFF" stroke-width="2.5" stroke-linecap="round" stroke-opacity="0.45"/>
        <!-- Lubang Donat Atas -->
        <ellipse cx="50" cy="49" rx="12" ry="10" fill="#FAF2EE"/>
        <!-- Taburan Meises / Sprinkles Warna-Warni -->
        <!-- Meises Kuning Emas -->
        <rect x="33" y="27" width="6" height="2.5" rx="1.2" fill="#FCD34D" transform="rotate(-25 33 27)"/>
        <rect x="62" y="32" width="6" height="2.5" rx="1.2" fill="#FCD34D" transform="rotate(40 62 32)"/>
        <rect x="42" y="62" width="6" height="2.5" rx="1.2" fill="#FCD34D" transform="rotate(-15 42 62)"/>
        <!-- Meises Putih Krim -->
        <rect x="44" y="22" width="6" height="2.5" rx="1.2" fill="#FFFFFF" transform="rotate(15 44 22)"/>
        <rect x="71" y="44" width="6" height="2.5" rx="1.2" fill="#FFFFFF" transform="rotate(-30 71 44)"/>
        <rect x="25" y="45" width="6" height="2.5" rx="1.2" fill="#FFFFFF" transform="rotate(50 25 45)"/>
        <!-- Meises Pink Rose -->
        <rect x="54" y="24" width="6" height="2.5" rx="1.2" fill="#FDA4AF" transform="rotate(-40 54 24)"/>
        <rect x="33" y="38" width="6" height="2.5" rx="1.2" fill="#FDA4AF" transform="rotate(20 33 38)"/>
        <rect x="58" y="58" width="6" height="2.5" rx="1.2" fill="#FDA4AF" transform="rotate(35 58 58)"/>
        <!-- Meises Hijau Mint -->
        <rect x="65" y="24" width="5.5" height="2.5" rx="1.2" fill="#34D399" transform="rotate(65 65 24)"/>
        <rect x="23" y="35" width="5.5" height="2.5" rx="1.2" fill="#34D399" transform="rotate(-10 23 35)"/>

    @elseif ($type === 'gulung')
        {{-- ROTI GULUNG / SWISS ROLL --}}
        <!-- Bayangan Bawah -->
        <ellipse cx="50" cy="84" rx="40" ry="7" fill="#E8B8AE" fill-opacity="0.4"/>
        <!-- Badan Roti Gulung (Tampak Samping Perspektif) -->
        <path d="M42 22 L76 34 C82 36, 88 44, 88 56 C88 68, 80 76, 72 77 L38 65 Z" fill="#D97706"/>
        <path d="M42 22 L76 34 C79 35, 82 38, 84 43 L48 31 Z" fill="#F59E0B"/>
        <!-- Sisi Potongan Depan (Cross Section Elips) -->
        <ellipse cx="38" cy="49" rx="24" ry="26" fill="#FEF3C7" stroke="#B45309" stroke-width="2.5"/>
        <!-- Lapisan Luar Panggang -->
        <ellipse cx="38" cy="49" rx="22" ry="24" fill="#FDE68A"/>
        <!-- Spiral Krim Cokelat/Selai Cantik -->
        <path d="M38 49
                 M 48 42
                 C 49 33, 38 29, 28 36
                 C 18 43, 20 61, 33 67
                 C 47 72, 56 59, 53 47
                 C 50 37, 36 37, 33 46
                 C 31 52, 40 56, 43 51
                 C 44 48, 41 46, 39 48"
              fill="none" stroke="#6B0F1A" stroke-width="4.5" stroke-linecap="round"/>
        <!-- Krim Putih Vanila di Inti Spiral -->
        <circle cx="39" cy="48" r="2.5" fill="#FFFFFF"/>
        <!-- Hiasan Krim & Cherry di Atas Roti -->
        <ellipse cx="64" cy="27" rx="7" ry="5" fill="#FFF5F2"/>
        <circle cx="64" cy="23" r="4" fill="#DC2626"/>
        <path d="M64 21 Q67 15 72 16" stroke="#15803D" stroke-width="1.5" fill="none" stroke-linecap="round"/>

    @elseif ($type === 'spiku')
        {{-- SPIKU / LAPIS SURABAYA --}}
        <!-- Bayangan Bawah -->
        <ellipse cx="50" cy="85" rx="38" ry="7" fill="#E8B8AE" fill-opacity="0.4"/>
        <!-- Bentuk Potongan Kue Lapis 3 Tingkat (Isometrik Angled) -->
        <!-- Sisi Samping Kanan (Tebal Kue) -->
        <path d="M72 32 L86 42 V70 L72 60 Z" fill="#D97706" fill-opacity="0.7"/>
        <path d="M72 41 L86 51 V61 L72 51 Z" fill="#3E1A0E"/>
        <!-- Sisi Atas (Top Crust) -->
        <path d="M22 40 L50 20 L86 42 L58 62 Z" fill="#D97706"/>
        <path d="M25 40 L50 22 L83 42 L58 60 Z" fill="#F59E0B"/>
        <!-- Pola Cokelat Panggang di Atas Spiku -->
        <path d="M34 34 L62 54 M44 27 L72 47 M54 23 L79 41" stroke="#78350F" stroke-width="2" stroke-linecap="round" stroke-opacity="0.6"/>
        <!-- Layer 1 (Kuning Atas) -->
        <path d="M14 47 L58 62 V68 L14 53 Z" fill="#FCD34D"/>
        <!-- Garis Selai Stroberi / Nanas 1 -->
        <path d="M14 53 L58 68 V70 L14 55 Z" fill="#6B0F1A"/>
        <!-- Layer 2 (Cokelat Tengah) -->
        <path d="M14 55 L58 70 V77 L14 62 Z" fill="#451A03"/>
        <path d="M14 62 L58 77 V78 L14 63 Z" fill="#2E1103"/>
        <!-- Garis Selai Stroberi / Nanas 2 -->
        <path d="M14 63 L58 78 V80 L14 65 Z" fill="#6B0F1A"/>
        <!-- Layer 3 (Kuning Bawah) -->
        <path d="M14 65 L58 80 V86 L14 71 Z" fill="#FCD34D"/>
        <!-- Sisi Kanan Potongan Layer -->
        <path d="M58 62 L72 52 V58 L58 68 Z" fill="#FBBF24"/>
        <path d="M58 68 L72 58 V60 L58 70 Z" fill="#520912"/>
        <path d="M58 70 L72 60 V67 L58 77 Z" fill="#381303"/>
        <path d="M58 77 L72 67 V69 L58 79 Z" fill="#520912"/>
        <path d="M58 79 L72 69 V75 L58 85 Z" fill="#FBBF24"/>

    @elseif ($type === 'kue-kering')
        {{-- KUE KERING / COOKIES / NASTAR --}}
        <!-- Bayangan Bawah -->
        <ellipse cx="50" cy="85" rx="38" ry="7" fill="#E8B8AE" fill-opacity="0.4"/>
        <!-- Cookie Belakang Kiri -->
        <circle cx="34" cy="46" r="21" fill="#D97706"/>
        <circle cx="34" cy="46" r="19" fill="#F59E0B"/>
        <!-- Choco Chips Cookie Belakang -->
        <circle cx="27" cy="38" r="2.8" fill="#451A03"/>
        <circle cx="39" cy="36" r="2.5" fill="#451A03"/>
        <circle cx="25" cy="49" r="2.5" fill="#451A03"/>
        <circle cx="38" cy="51" r="3" fill="#451A03"/>
        <!-- Cookie Utama Depan Kanan -->
        <circle cx="62" cy="56" r="24" fill="#B45309" fill-opacity="0.4"/>
        <circle cx="60" cy="54" r="23" fill="#FBBF24"/>
        <circle cx="60" cy="54" r="21" fill="#FDE68A"/>
        <!-- Choco Chips Cookie Depan -->
        <rect x="52" y="42" width="5.5" height="5" rx="2" fill="#451A03" transform="rotate(15 52 42)"/>
        <rect x="68" y="44" width="6" height="5" rx="2" fill="#451A03" transform="rotate(-25 68 44)"/>
        <rect x="49" y="55" width="6" height="5" rx="2" fill="#451A03" transform="rotate(35 49 55)"/>
        <rect x="66" y="58" width="5.5" height="5" rx="2" fill="#451A03" transform="rotate(-10 66 58)"/>
        <rect x="57" y="66" width="5" height="4.5" rx="2" fill="#451A03" transform="rotate(45 57 66)"/>
        <!-- Nastar Emas Mungil di Depan -->
        <circle cx="28" cy="69" r="13" fill="#F59E0B"/>
        <circle cx="27" cy="68" r="12" fill="#FBBF24"/>
        <!-- Kilau Olesan Telur Nastar -->
        <path d="M21 64 C23 60, 27 58, 31 59" stroke="#FEF08A" stroke-width="2" stroke-linecap="round"/>
        <!-- Cengkeh Nastar -->
        <circle cx="27" cy="64" r="1.8" fill="#451A03"/>
        <line x1="27" y1="64" x2="27" y2="61" stroke="#451A03" stroke-width="1.5" stroke-linecap="round"/>

    @elseif ($type === 'roti')
        {{-- ROTI / CROISSANT / BAKERY --}}
        <!-- Bayangan Bawah -->
        <ellipse cx="50" cy="85" rx="38" ry="7" fill="#E8B8AE" fill-opacity="0.4"/>
        <!-- Badan Croissant Keemasan -->
        <path d="M15 62 C20 44, 34 32, 50 32 C66 32, 80 44, 85 62 C78 68, 70 70, 64 63 C58 57, 54 56, 50 56 C46 56, 42 57, 36 63 C30 70, 22 68, 15 62 Z" fill="#D97706"/>
        <path d="M20 60 C24 46, 36 36, 50 36 C64 36, 76 46, 80 60 C74 65, 68 66, 63 60 C58 54, 54 53, 50 53 C46 53, 42 54, 37 60 C32 66, 26 65, 20 60 Z" fill="#F59E0B"/>
        <!-- Layering Puncak Croissant -->
        <path d="M38 37 C42 34, 46 33, 50 33 C54 33, 58 34, 62 37 C59 52, 41 52, 38 37 Z" fill="#FDE68A"/>
        <path d="M30 43 C33 40, 36 38, 40 37 C42 49, 32 54, 28 47 Z" fill="#FBBF24"/>
        <path d="M70 43 C67 40, 64 38, 60 37 C58 49, 68 54, 72 47 Z" fill="#FBBF24"/>
        <!-- Keratan Roti / Score Lines & Kilauan -->
        <path d="M43 38 Q50 42 57 38" stroke="#B45309" stroke-width="1.8" fill="none" stroke-linecap="round"/>
        <path d="M36 46 Q50 50 64 46" stroke="#B45309" stroke-width="1.8" fill="none" stroke-linecap="round"/>
        <path d="M32 38 C37 34, 43 32, 50 32" stroke="#FFFBEB" stroke-width="2" stroke-linecap="round" stroke-opacity="0.6"/>

    @else
        {{-- DEFAULT: CUPCAKE SPESIAL NAK MBAREP --}}
        <!-- Bayangan Bawah -->
        <ellipse cx="50" cy="86" rx="34" ry="6" fill="#E8B8AE" fill-opacity="0.4"/>
        <!-- Cupcake Paper Wrapper -->
        <path d="M26 56 L32 82 C33 85, 36 87, 40 87 H60 C64 87, 67 85, 68 82 L74 56 Z" fill="#6B0F1A"/>
        <!-- Garis Lipatan Kertas Cup -->
        <line x1="33" y1="58" x2="38" y2="84" stroke="#831843" stroke-width="1.8"/>
        <line x1="41" y1="58" x2="44" y2="85" stroke="#831843" stroke-width="1.8"/>
        <line x1="50" y1="58" x2="50" y2="86" stroke="#831843" stroke-width="1.8"/>
        <line x1="59" y1="58" x2="56" y2="85" stroke="#831843" stroke-width="1.8"/>
        <line x1="67" y1="58" x2="62" y2="84" stroke="#831843" stroke-width="1.8"/>
        <!-- Roti Sponge Cake (Mengembang di atas cup) -->
        <ellipse cx="50" cy="56" rx="27" ry="9" fill="#F59E0B"/>
        <ellipse cx="50" cy="54" rx="25" ry="8" fill="#FDE68A"/>
        <!-- Swirl Krim Frosting Tingkat 1 (Bawah) -->
        <path d="M22 53 C22 45, 33 42, 50 42 C67 42, 78 45, 78 53 C76 57, 68 60, 50 60 C32 60, 24 57, 22 53 Z" fill="#FAF2EE"/>
        <!-- Swirl Krim Frosting Tingkat 2 (Tengah) -->
        <path d="M28 43 C28 36, 37 32, 50 32 C63 32, 72 36, 72 43 C70 47, 62 50, 50 50 C38 50, 30 47, 28 43 Z" fill="#FCE7F3"/>
        <!-- Swirl Krim Frosting Tingkat 3 (Puncak) -->
        <path d="M37 33 C37 26, 43 23, 50 23 C57 23, 63 26, 63 33 C61 37, 56 39, 50 39 C44 39, 39 37, 37 33 Z" fill="#FDA4AF"/>
        <!-- Puncak Putaran Krim -->
        <path d="M50 23 C50 20, 53 18, 52 16 C50 17, 49 19, 49 22 Z" fill="#F43F5E"/>
        <!-- Buah Ceri Merah Merona -->
        <circle cx="53" cy="18" r="5" fill="#DC2626"/>
        <circle cx="51.5" cy="16.5" r="1.5" fill="#FECDD3"/>
        <!-- Tangkai Ceri Melengkung -->
        <path d="M54 14 Q58 6 66 9" stroke="#15803D" stroke-width="1.8" fill="none" stroke-linecap="round"/>
        <!-- Taburan Meises Emas & Maroon -->
        <rect x="36" y="47" width="4.5" height="2" rx="1" fill="#F59E0B" transform="rotate(-20 36 47)"/>
        <rect x="60" y="48" width="4.5" height="2" rx="1" fill="#6B0F1A" transform="rotate(30 60 48)"/>
        <rect x="44" y="38" width="4" height="2" rx="1" fill="#6B0F1A" transform="rotate(15 44 38)"/>
        <rect x="54" y="37" width="4" height="2" rx="1" fill="#F59E0B" transform="rotate(-35 54 37)"/>
    @endif
</svg>
