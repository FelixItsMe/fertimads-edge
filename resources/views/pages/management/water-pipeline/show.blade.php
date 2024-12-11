<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('leaflet/leaflet.css') }}">
        <link rel="stylesheet" href="{{ asset('css/extend.css') }}">
        <style>
            #map {
                height: 80vh;
                z-index: 50;
            }
        </style>
    @endpush
    <x-slot name="header">
        <h2 class="leading-tight">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('water-pipeline.index') }}">Manajemen Jalur Pipa Air</a>
                </li>
                <li class="breadcrumb-item breadcrumb-active">Detail Jalur Pipa Air {{ $waterPipeline->name }}</li>
            </ol>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="sm:max-w-7x xl:max-w-full mx-auto sm:px-6 lg:px-8">

            @if (session()->has('water-pipeline-success'))
                <div class="bg-sky-500 text-white w-full p-6 sm:rounded-lg flex items-center">
                    <i
                        class="fa-solid fa-circle-info text-3xl mr-3"></i>&nbsp;{{ session()->get('water-pipeline-success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-2">
                <div class="w-full lg:col-span-3">
                    <div id="map" class="rounded-md outline-2 outline-slate-500"></div>
                    <input type="hidden" name="polyline" id="polyline"
                        value="{{ json_encode($waterPipeline->polyline) }}">
                </div>
                <div class="w-full">
                    <div class="bg-white overflow-hidden shadow-sm lg:rounded-lg p-6">
                        <div class="flex flex-col gap-y-4">
                            <div class="w-full">
                                <x-input-label class="text-slate-400"
                                    for="name">{{ __('Nama Jalur Pipa Air') }}</x-input-label>
                                <span>{{ $waterPipeline->name }}</span>
                            </div>
                            <div class="w-full">
                                <x-input-label class="text-slate-400"
                                    for="name">{{ __('Deskripsi') }}</x-input-label>
                                <span>{{ $waterPipeline->description }}</span>
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
                polyline: null,
                polylineLayer: null
            }
            let currentMarkerLayer = null
            let currentPolygonLayer = null
            let baseMapOptions = {
                'Open Street Map': L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="http://openstreetmap.org">OpenStreetMap</a> Contributors',
                    maxZoom: 18,
                }),
                'Google Satellite': L.tileLayer('https://www.google.cn/maps/vt?lyrs=s,h&x={x}&y={y}&z={z}', {
                    attribution: '&copy; Google Hybrid',
                    maxZoom: 18,
                }),
                'Google Street': L.tileLayer('https://www.google.cn/maps/vt?lyrs=m&x={x}&y={y}&z={z}', {
                    attribution: '&copy; Google Street',
                    maxZoom: 18,
                })
            };

            const map = L.map('map', {
                    preferCanvas: true,
                    layers: [baseMapOptions['Google Satellite']],
                    zoomControl: false
                })
                .setView([-6.46958, 107.033339], 18);

            L.control.zoom({
                position: 'topleft'
            }).addTo(map);

            L.control.layers(baseMapOptions, null, {
                position: 'bottomright'
            }).addTo(map)

            window.onload = () => {
                console.log('Hello World');

                stateData.polyline = JSON.parse(document.querySelector('#polyline').value)
                stateData.polylineLayer = L.polyline(stateData.polyline, {
                    color: 'blue'
                }).addTo(map);

                map.fitBounds(stateData.polylineLayer.getBounds());
            }
        </script>
    @endpush
</x-app-layout>
