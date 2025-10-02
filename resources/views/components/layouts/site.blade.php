<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name', 'Scent Memoir') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Garamond', serif;
        }

        div::-webkit-scrollbar {
            display: none;
        }
    </style>

    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @livewireStyles
</head>

<body class="bg-[#122C4F] text-[#FBF9E4] min-h-screen overflow-x-hidden">
    
    {{-- header --}}
    <x-site.header />

    {{-- page content --}}
    <main class="min-h-[60vh]">
        {{ $slot }}
    </main>

    {{-- footer --}}
    <x-site.footer />

    @livewireScripts(['navigate' => true])
</body>

</html>