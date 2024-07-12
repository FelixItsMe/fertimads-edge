<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('leaflet/leaflet.css') }}">
        <link rel="stylesheet" href="{{ asset('css/extend.css') }}">
        <style>
            #map {
                height: 50vh;
            }
        </style>
    @endpush
    <x-slot name="header">
        <h2 class="leading-tight">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('land.index') }}">Lahan</a>
                </li>
                <li class="breadcrumb-item breadcrumb-active">{{ __('Detail Lahan') }}</li>
            </ol>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row max-sm:gap-2 sm:space-x-2">
                <div class="w-full sm:w-3/4">
                    <div id="map" class="rounded-md"></div>
                    <input type="hidden" name="polygon" id="polygon" value="{{ json_encode($land->polygon) }}">
                </div>
                <div class="w-full sm:w-1/4">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="flex flex-col gap-y-4">
                            <div class="w-full">
                                <x-input-label class="text-slate-400" for="name">{{ __('Nama Lahan') }}</x-input-label>
                                <span>{{ $land->name }}</span>
                            </div>
                            <div class="w-full">
                                <x-input-label class="text-slate-400" for="name">{{ __('Luas Lahan') }}</x-input-label>
                                <span>{{ $land->area }} Hektar</span>
                            </div>
                            <div class="w-full">
                                <x-input-label class="text-slate-400" for="name">{{ __('Lokasi Lahan') }}</x-input-label>
                                <span>{{ $land->address }}</span>
                            </div>
                            <div class="w-full">
                                <x-input-label class="text-slate-400" for="name">{{ __('Koordinat Lahan') }}</x-input-label>
                                <span>{{ $land->latitude }}, {{ $land->longitude }}, {{ $land->altitude }}</span>
                            </div>
                            <div class="w-full">
                                <x-input-label class="text-slate-400" for="name">{{ __('Jumlah Lahan') }}</x-input-label>
                                <span>0</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('leaflet/leaflet.js') }}"></script>
        <script src="{{ asset('js/extend.js') }}"></script>
        <script src="{{ asset('js/map.js') }}"></script>
        <script>
            let stateData = {
                polygon: null,
                layerPolygon: null,
                latitude: parseFloat("{{ $land->latitude }}"),
                longitude: parseFloat("{{ $land->longitude }}"),
            }
            let currentMarkerLayer = null
            let currentPolygonLayer = null
            // Layer MAP
            let googleStreets = L.tileLayer('http://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
                maxZoom: 20,
                subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
            });
            let googleStreetsSecond = L.tileLayer('http://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
                maxZoom: 20,
                subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
            });
            let googleStreetsThird = L.tileLayer('http://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
                maxZoom: 20,
                subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
            });

            // Layer MAP
            const map = L.map('map', {
                    preferCanvas: true,
                    layers: [googleStreets],
                    zoomControl: true
                })
                .setView([-6.869080223722067, 107.72491693496704], 12);

            window.onload = () => {
                console.log('Hello World');

                stateData.polygon = JSON.parse(document.querySelector('#polygon').value)
                stateData.layerPolygon = initPolygon(map, stateData.polygon, {
                    dashArray: '10, 10',
                    dashOffset: '20',
                })
                currentMarkerLayer = initMarker(map, stateData.latitude, stateData.longitude)

                map.fitBounds(stateData.layerPolygon.getBounds());
            }
        </script>
    @endpush
</x-app-layout>
