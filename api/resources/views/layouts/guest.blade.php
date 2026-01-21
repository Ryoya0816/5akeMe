<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        {{-- @vite が読み込めないときのフォールバック（guest は Tailwind に依存するため最小限） --}}
        <style>
            body { font-family: Figtree, system-ui, sans-serif; color: #111827; -webkit-font-smoothing: antialiased; }
            .g-fallback { min-height: 100vh; display: flex; flex-direction: column; align-items: center; padding-top: 1.5rem; background: #f3f4f6; }
            @media (min-width: 640px) { .g-fallback { justify-content: center; padding-top: 0; } }
            .g-card { width: 100%; margin-top: 1.5rem; padding: 1rem 1.5rem; background: #fff; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); overflow: hidden; }
            @media (min-width: 640px) { .g-card { max-width: 28rem; border-radius: 0.5rem; } }
        </style>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="g-fallback min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
            <div>
                <a href="/">
                    <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                </a>
            </div>

            <div class="g-card w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
