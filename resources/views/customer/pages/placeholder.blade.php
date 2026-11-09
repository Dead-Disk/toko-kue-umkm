<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Belum Tersedia - {{ \App\Models\StoreSetting::current()?->store_name ?? 'Nak Mbarep' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#FAF2EE] text-gray-800 min-h-screen antialiased flex flex-col font-sans">
    {{-- Top Navigation Bar --}}
    @include('customer.partials.navbar')

    <div class="max-w-md md:max-w-2xl mx-auto px-4 py-12 w-full flex-1 text-center">
        <h1 class="text-xl sm:text-2xl font-serif font-bold text-[#6B0F1A] mb-4">Maaf, halaman ini belum tersedia.</h1>

        <form action="{{ route('customer.index') }}" method="GET">
            <button type="submit" class="bg-[#6B0F1A] hover:bg-[#520912] text-white text-xs font-semibold px-4 py-2.5 rounded-xl transition cursor-pointer shadow-xs">
                Kembali ke Beranda
            </button>
        </form>
    </div>
</body>
</html>
