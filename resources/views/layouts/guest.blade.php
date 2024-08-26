<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="icon" href="{{ asset('images/default/famicon.svg') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Icons. Uncomment required icon fonts -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen flex flex-col font-sans text-gray-900 antialiased">
        <div class="flex-grow container mx-auto px-4 py-16 grid grid-cols-1 md:grid-cols-2 items-center justify-center">
            <div class="flex justify-center">
                <div class="w-full sm:max-w-md mt-6 px-6 py-4">
                    {{ $slot }}
                </div>
            </div>

            <div class="flex justify-center max-md:order-first">
                <a href="/">
                    <x-application-logo class="w-full object-cover" />
                </a>
            </div>
        </div>
        <div class="w-full text-center pb-2">
            Copyright © 2024 IPB UNIVERSITY. All Right Reserved.
        </div>
        @stack('scripts')
    </body>
</html>
