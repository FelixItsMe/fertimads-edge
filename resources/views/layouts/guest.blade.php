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

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="container mx-auto px-4 py-16 flex items-center justify-center min-h-screen">
            <div class="flex-1 flex justify-center">
                <div class="w-full sm:max-w-md mt-6 px-6 py-4">
                    {{ $slot }}
                </div>
            </div>

            <div class="flex-1 flex justify-center">
                <a href="/">
                    <x-application-logo class="w-full object-cover" />
                </a>
            </div>
        </div>
        <div class="absolute bottom-0 left-1/2 -translate-x-1/2">
            Copyright © 2024 IPB UNIVERSITY. All Right Reserved.
        </div>
    </body>
</html>
