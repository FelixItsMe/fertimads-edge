<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    {{-- Famicon --}}
    <link rel="icon" href="{{ asset('images/default/famicon.svg') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:ital,wght@0,300..900;1,300..900&display=swap"
        rel="stylesheet">

    <!-- Icons. Uncomment required icon fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Page CSS -->
    @stack('styles')
    <link href='https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/leaflet.fullscreen.css'
        rel='stylesheet' />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    <div id="loading-spinner" class="fixed top-0 left-0 w-full h-full flex justify-center items-center bg-primary z-50"
        style="z-index: 9999;">
        <div class="loader border-t-4 border-b-4 border-slate-50 rounded-full w-16 h-16 animate-spin"></div>
    </div>

    <div class="fixed top-4 right-4 bg-red-500 text-white p-4 rounded-md hidden" id="error-body" style="z-index: 999">
        <i class="fa-solid fa-circle-exclamation"></i>&nbsp;<span id="error-message"></span>
    </div>
    <div id="layout-wrapper" class="w-full flex flex-auto items-stretch">
        <div x-data="{ sideopen: true }" class="min-h-screen w-full flex flex-auto bg-gray-100 items-stretch">
            @include('layouts.sidebar')
            <div :class="{ 'lg:pl-64': sideopen }" class="pt-0 basis-full flex-col w-0 min-w-0 max-w-full">
                @include('layouts.navigation')

                <!-- Page Heading -->
                @isset($header)
                    <header class="bg-white shadow">
                        <div class="sm:sm:max-w-7x xl:max-w-full mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <!-- Page Content -->
                <main class="min-h-screen">
                    {{ $slot }}
                </main>
                <footer class="px-6 py-1 font-normal text-base text-slate-400">
                    Copyright © {{ now()->year }} IPB UNIVERSITY. All Right Reserved.
                </footer>
            </div>
        </div>
    </div>

    {{-- Export Modal --}}
    <x-modal name="sign-out" style="z-index: 999999999;" focusable>
        <form method="post" action="{{ route('logout') }}" class="p-6">
            @csrf
            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Apakah anda yakin ingin keluar?') }}
            </h2>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Batalkan') }}
                </x-secondary-button>

                <x-danger-button class="ms-3">
                    {{ __('Keluar') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/js/all.min.js"
        integrity="sha512-u3fPA7V8qQmhBPNT5quvaXVa1mnnLSXUep5PS1qo5NRzHwG19aHmNJnj1Q8hpA/nBWZtZD4r4AX6YOt5ynLN2g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    @stack('scripts')
    <script src='https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/Leaflet.fullscreen.min.js'></script>
    <script>
        const showLoading = () => {
            document.getElementById('loading-spinner').classList.remove('hidden');
            document.getElementById('layout-wrapper').classList.add('hidden');
        }
        const hideLoading = () => {
            document.getElementById('loading-spinner').classList.add('hidden');
            document.getElementById('layout-wrapper').classList.remove('hidden');
        }

        const initSidebar = () => {
            const toggleSidebar = document.getElementById("toggle-sidebar")
            const sidebarMain = document.getElementById("sidebar-main")
            const mainContent = document.getElementById("main-content")
            let isSidebarOpen = true

            toggleSidebar.addEventListener("click", function(e) {
                mainContent.classList.toggle("lg:pl-64")
                if (isSidebarOpen) {
                    sidebarMain.classList.add("sidebar-close")
                    sidebarMain.classList.remove("sidebar-open")
                    isSidebarOpen = false
                } else {
                    sidebarMain.classList.add("sidebar-open")
                    sidebarMain.classList.remove("sidebar-close")
                    isSidebarOpen = true
                }

                setTimeout(() => {
                    map.invalidateSize(true)
                }, 500)
            })
        }

        const initMapPlugin = () => {
            if (map) {
                map.addControl(new L.Control.Fullscreen({
                    title: {
                        'false': 'View Fullscreen',
                        'true': 'Exit Fullscreen'
                    },
                    position: "topleft"
                }));
            }
        }

        // Show the loading spinner when the page starts loading
        showLoading()

        function scrollToActiveLink() {
            const activeLink = document.querySelector('#sidebar .active-icon');

            if (activeLink) {
                setTimeout(() => {
                    activeLink.scrollIntoView({ behavior: 'instant', block: 'center' });
                }, 0);
            }
        }
        // Hide the loading spinner when the page has fully loaded
        window.addEventListener('load', function() {
            scrollToActiveLink()
            hideLoading()
            initMapPlugin()
        });
    </script>
</body>

</html>
