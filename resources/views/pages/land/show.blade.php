<x-app-layout>
  @push('styles')
  <link rel="stylesheet" href="{{ asset('leaflet/leaflet.css') }}">
  <link rel="stylesheet" href="{{ asset('css/extend.css') }}">
  <style>
    #map {
      height: 50vh;
      z-index: 50;
    }
  </style>
  @endpush
  <x-slot name="header">
    <h2 class="leading-tight">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <a href="{{ route('land.index') }}">Manajemen Lahan</a>
        </li>
        <li class="breadcrumb-item breadcrumb-active">{{ __('Detail Lahan') }}</li>
      </ol>
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="sm:max-w-7x xl:max-w-full mx-auto sm:px-6 lg:px-8">
      <div class="flex flex-col lg:flex-row max-lg:gap-2 lg:space-x-2">
        <div class="w-full lg:w-3/4">
          <div id="map" class="rounded-md"></div>
          <input type="hidden" name="polygon" id="polygon" value="{{ json_encode($land->polygon) }}">
        </div>
        <div class="w-full lg:w-1/4">
          <div class="bg-white overflow-hidden shadow-sm lg:rounded-lg p-6">
            <div class="flex flex-col gap-y-4">
              <div class="w-full">
                <x-input-label class="text-slate-400" for="name">{{ __('Nama Lahan') }}</x-input-label>
                <span>{{ $land->name }}</span>
              </div>
              <div class="w-full">
                <x-input-label class="text-slate-400" for="name">{{ __('Luas Lahan') }}</x-input-label>
                <span>{{ $land->area }}&nbsp;m²</span>
              </div>
              <div class="w-full">
                <x-input-label class="text-slate-400" for="name">{{ __('Lokasi Lahan') }}</x-input-label>
                <span>{{ $land->address }}</span>
              </div>
              <div class="w-full">
                <x-input-label class="text-slate-400" for="name">{{ __('Koordinat Lahan') }}</x-input-label>
                <span>{{ $land->latitude }}, {{ $land->longitude }}</span>
              </div>
              <div class="w-full">
                <x-input-label class="text-slate-400" for="name">{{ __('Altitude') }}</x-input-label>
                <span>{{ $land->altitude }} mdpl</span>
              </div>
              <div class="w-full">
                <x-input-label class="text-slate-400" for="name">{{ __('Jumlah Kebun') }}</x-input-label>
                <span>{{ $land->gardens_count }}</span>
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
