<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head')
    <style>
        body {
            font-family: 'Garamond', serif;
        }
    </style>
</head>

<body class="min-h-screen antialiased bg-[#122C4F] text-[#FBF9E4]">
    <div class="min-h-svh flex flex-col">
        {{-- Brand (no Laravel logo) --}}
        <div class="absolute mt-3 top-4 left-6">
            <p class="text-xl sm:text-3xl md:text-4xl font-bold">Scent Memoir</p>
        </div>

        {{-- Page content --}}
        <div class="flex flex-1 items-center justify-center p-6 md:p-10">
            <div class="w-full max-w-md"> {{-- use max-w-md (28rem). You can change to max-w-sm if you want it narrower
                --}}
                {{ $slot }}
            </div>
        </div>
    </div>
    @fluxScripts
</body>

</html>