<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name', 'Scent Memoir') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        body { font-family: 'Garamond', serif; }
        div::-webkit-scrollbar { display: none; }
    </style>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#122C4F] text-[#FBF9E4] min-h-screen overflow-x-hidden max-w-screen">

    {{-- YOUR EXACT HEADER / LOGO + NAV --}}
    <header>
        <div class="w-full px-8 py-4 relative">
            <div class="absolute mt-3 top-4 left-6">
                <p class="text-xl sm:text-3xl md:text-4xl font-bold">Scent Memoir</p>
            </div>
        </div>
    </header>

    {{-- Page content --}}
    <main class="px-6 py-10">
        {{ $slot }}
    </main>

    @livewireScripts(['navigate' => false])
</body>
</html>
