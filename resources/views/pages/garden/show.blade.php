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
                    <a href="{{ route('garden.index') }}">Kebun</a>
                </li>
                <li class="breadcrumb-item breadcrumb-active">{{ __('Detail Kebun') }}</li>
            </ol>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row max-sm:gap-2 sm:space-x-2">
                <div class="w-full sm:w-3/4">
                    <div id="map" class="rounded-md border-2 border-slate-600"></div>
                    <input type="hidden" name="land_polygon" id="land-polygon" value="{{ json_encode($garden->land->polygon) }}">
                    <input type="hidden" name="garden_polygon" id="garden-polygon" value="{{ json_encode($garden->polygon) }}">
                </div>
                <div class="w-full sm:w-1/4">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="flex flex-col gap-y-4">
                            <div class="w-full">
                                <x-input-label class="text-slate-400" for="name">{{ __('Nama Lahan') }}</x-input-label>
                                <span>{{ $garden->name }}</span>
                            </div>
                            <div class="w-full">
                                <x-input-label class="text-slate-400" for="name">{{ __('Luas Lahan') }}</x-input-label>
                                <span>{{ $garden->area }} m2</span>
                            </div>
                            <div class="w-full">
                                <x-input-label class="text-slate-400" for="name">{{ __('Nama Komoditi') }}</x-input-label>
                                <span>{{ $garden->commodity->name }}</span>
                            </div>
                            <div class="w-full">
                                <x-input-label class="text-slate-400" for="name">{{ __('Koordinat Lahan') }}</x-input-label>
                                <span>{{ $garden->latitude }}, {{ $garden->longitude }}, {{ $garden->altitude }}</span>
                            </div>
                            <div class="w-full">
                                <x-input-label class="text-slate-400" for="name">{{ __('Nama Lahan') }}</x-input-label>
                                <span>{{ $garden->land->name }}</span>
                            </div>
                            <div class="w-full">
                                <x-input-label class="text-slate-400" for="name">{{ __('Perangkat IoT') }}</x-input-label>
                                <span>{{ $garden->device?->series ?? '-' }}</span>
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
                land: {
                    polygon: null,
                    layerPolygon: null,
                },
                garden: {
                    polygon: null,
                    layerPolygon: null,
                },
                latitude: parseFloat("{{ $garden->latitude }}"),
                longitude: parseFloat("{{ $garden->longitude }}"),
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

                stateData.land.polygon = JSON.parse(document.querySelector('#land-polygon').value)
                stateData.land.layerPolygon = initPolygon(map, stateData.land.polygon, {
                    dashArray: '10, 10',
                    dashOffset: '20',
                    color: '#bdbdbdf1',
                })
                stateData.garden.polygon = JSON.parse(document.querySelector('#garden-polygon').value)
                stateData.garden.layerPolygon = initPolygon(map, stateData.garden.polygon, {
                    color: '#{{ $garden->color }}',
                })
                currentMarkerLayer = initMarker(map, stateData.latitude, stateData.longitude)

                map.fitBounds(stateData.land.layerPolygon.getBounds());
            }
        </script>
    @endpush
</x-app-layout>