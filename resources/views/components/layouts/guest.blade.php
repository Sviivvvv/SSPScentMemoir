@props(['title' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">


<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ? $title . ' | ' : '' }}{{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Garamond', serif;
        }
    </style>
    @livewireStyles
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="antialiased bg-[#122C4F] text-[#FBF9E4] min-h-screen">
    {{ $slot }}
    @livewireScripts
</body>

</html>