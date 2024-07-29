<x-app-layout>
  @push('styles')
  <link rel="stylesheet" href="{{ asset('leaflet/leaflet.css') }}">
  <link rel="stylesheet" href="{{ asset('css/extend.css') }}">
  <style>
    #map {
      height: 70vh;
    }
  </style>
  @endpush
  <x-slot name="header">
    <h2 class="leading-tight">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <a href="#">Pages</a>
        </li>
        <li class="breadcrumb-item breadcrumb-active">{{ __('Kendali Head Unit') }}</li>
      </ol>
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="grid grid-flow-row grid-cols-1 gap-2">
        <div>
          <div id="map" class="rounded-md"></div>
        </div>
        <div class="grid grid-flow-row grid-cols-1 md:grid-cols-5 gap-2">
          <div class="flex flex-col gap-2 pr-12">
            <div class="font-bold py-2">Opsi Kendali</div>
            @include('pages.control.head-unit.links')
          </div>
          <div class="col-span-4 flex flex-col gap-2">
            <div class="py-2"><span class="font-bold">Kendali Perangkat</span></div>
            <div class="grid grid-flow-row grid-cols-2 gap-8">
              <div class="flex flex-col gap-2">
                <div class="grid grid-flow-row grid-cols-4">
                  <div>Pilih Output</div>
                  <div class="col-span-3">
                    <div class="grid grid-flow-row grid-cols-2 gap-2">
                      <a href="#" @class([ 'bg-white'=> !request()->routeIs('head-unit.schedule-water.index'),
                        'bg-primary' => request()->routeIs('head-unit.schedule-water.index'),
                        'text-white' => request()->routeIs('head-unit.schedule-water.index'),
                        'rounded-md',
                        'px-4',
                        'py-2',
                        'text-xs',
                        ])>Penyiraman</a>
                      <a href="{{ route('head-unit.schedule-fertilizer.index') }}" @class([ 'bg-white'=> !request()->routeIs('head-unit.manual.index'),
                        'bg-primary' => request()->routeIs('head-unit.manual.index'),
                        'text-white' => request()->routeIs('head-unit.manual.index'),
                        'rounded-md',
                        'px-4',
                        'py-2',
                        'text-xs',
                        ])>Pemupukan</a>
                    </div>
                  </div>
                </div>
                <div class="grid grid-flow-row grid-cols-4">
                  <div>Pilih Lahan</div>
                  <div class="col-span-3">
                    <div class="grid grid-flow-row grid-cols-2 gap-2">
                      @foreach ($lands as $id => $landName)
                      <div>
                        <input type="radio" id="land-{{ $id }}" name="land_id" onchange="pickLand({{ $id }})" value="{{ $id }}" class="hidden output-type peer/penyiraman" />
                        <label for="land-{{ $id }}" class="inline-flex w-full px-4 py-2 bg-white rounded-md text-xs cursor-pointer dark:hover:text-gray-300 dark:border-gray-700 dark:peer-checked/penyiraman:text-blue-500 peer-checked/penyiraman:bg-primary peer-checked/penyiraman:text-white hover:text-gray-600 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700">
                          {{ $landName }}
                        </label>
                      </div>
                      @endforeach
                    </div>
                  </div>
                </div>
                <div class="grid grid-flow-row grid-cols-4">
                  <div>Pilih Kebun</div>
                  <div class="col-span-3">
                    <div class="grid grid-flow-row grid-cols-2 gap-2" id="list-gardens">
                      <button type="button" class="bg-white rounded-md px-4 py-2 text-xs text-left" disabled>Pilih Lahan</button>
                    </div>
                  </div>
                </div>
                <div class="grid grid-flow-row grid-cols-4">
                  <div>Tanggal Mulai</div>
                  <div class="col-span-3">
                    <div class="grid grid-flow-row grid-cols-2 gap-2">
                      <input type="date" name="start_date" class="bg-white rounded-md text-xs py-2 px-4 border-none">
                    </div>
                  </div>
                </div>
                <div class="grid grid-flow-row grid-cols-4">
                  <div>Umur Komoditi</div>
                  <div class="col-span-3">
                    <div class="grid grid-flow-row grid-cols-2 gap-2">
                      <input type="number" min="0" name="commodity_age" class="bg-white rounded-md text-xs py-2 px-4 border-none">
                    </div>
                  </div>
                </div>
                <div class="grid grid-flow-row grid-cols-4">
                  <div>Waktu Pelaksanaan</div>
                  <div class="col-span-3">
                    <div class="grid grid-flow-row grid-cols-2 gap-2">
                      <input type="time" name="execute_time" class="bg-white rounded-md text-xs py-2 px-4 border-none">
                    </div>
                  </div>
                </div>
              </div>
              <div>
                <div class="flex flex-col gap-2">
                  <div>
                    <button type="button" onclick="storeScheduleWater()" class="bg-primary text-white font-bold rounded-md px-4 py-2">Kirim</button>
                  </div>
                  <div class="bg-sky-500 text-white w-full p-6 sm:rounded-lg flex items-center hidden" id="info-body">
                    <i class="fa-solid fa-circle-info text-3xl mr-3"></i>&nbsp;<span id="info-text"></span>
                  </div>
                </div>
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
  <script src="{{ asset('js/api.js') }}"></script>
  <script>
    let stateData = {
      polygon: null,
      layerPolygon: null,
      latitude: null,
      longitude: null,
    }
    let currentMarkerLayer = null
    let currentPolygonLayer = null
    let currentLand = {
      polygonLayer: null,
      markerLayer: null,
    }
    let currentGroupGarden = L.layerGroup()
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
      position: 'bottomleft'
    }).addTo(map);

    L.control.layers(baseMapOptions, null, {
      position: 'bottomright'
    }).addTo(map)

    const storeScheduleWater = async () => {
      const gardenId = document.querySelector('input[name="garden_id"]:checked')?.value
      const startDate = document.querySelector('input[name="start_date"]')?.value
      const commodityAge = document.querySelector('input[name="commodity_age"]')?.value
      const executeTime = document.querySelector('input[name="execute_time"]')?.value
      const data = await fetchData(
        "{{ route('head-unit.schedule-water.store') }}", {
          method: "POST",
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').attributes.content.nodeValue,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            'garden_id': gardenId,
            'start_date': startDate,
            'commodity_age': commodityAge,
            'execute_time': executeTime,
          })
        }
      );

      if (!data) {
        document.querySelector('#info-text').textContent = 'Gagal menyimpan penjadwalan'
      } else {
        document.querySelector('#info-text').textContent = 'Berhasil menyimpan penjadwalan!'
      }

      document.querySelector('#info-body').classList.remove('hidden')
    }

    const getLand = async id => {
      const data = await fetchData(
        "{{ route('extra.land.get-land-polygon', 'ID') }}".replace('ID', id), {
          method: "GET",
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').attributes.content.nodeValue,
            'Accept': 'application/json',
          },
        }
      );

      return data?.land
    }

    const initLandPolygon = async (id, map) => {
      document.querySelector('#list-gardens').innerHTML = `<button type="button"
                    class="bg-white rounded-md px-4 py-2 text-xs text-left"
                    disabled
                    >Loading</button>`

      const land = await getLand(id)

      if (currentLand.polygonLayer) {
        currentLand.polygonLayer.remove()
      }

      if (!land) {
        document.querySelector('#list-gardens').innerHTML = `<button type="button"
                        class="bg-red-500 text-white rounded-md px-4 py-2 text-xs text-left"
                        disabled
                        >Failed</button>`
        return false
      }

      currentLand.polygonLayer = initPolygon(map, land.polygon, {
        dashArray: '10, 10',
        dashOffset: '20',
        color: '#bdbdbd',
      })

      map.fitBounds(currentLand.polygonLayer.getBounds());

      currentGroupGarden.clearLayers()

      let eGardens = ``

      land.gardens.forEach(garden => {
        currentGroupGarden.addLayer(L.polygon(garden.polygon, {
          dashArray: '10, 10',
          dashOffset: '20',
          color: '#' + garden.color + "55",
        }))

        eGardens += `<div>
                            <input type="radio" id="garden-${garden.id}" name="garden_id" value="${garden.id}" class="hidden peer/garden" />
                            <label for="garden-${garden.id}" class="inline-flex w-full px-4 py-2 bg-white rounded-md text-xs cursor-pointer dark:hover:text-gray-300 dark:border-gray-700 dark:peer-checked/garden:text-blue-500 peer-checked/garden:bg-primary peer-checked/garden:text-white hover:text-gray-600 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700">
                                ${garden.name}
                            </label>
                        </div>`
      })

      document.querySelector('#list-gardens').innerHTML = eGardens

      currentGroupGarden.addTo(map)

      return true
    }

    const pickLand = landId => {
      initLandPolygon(landId, map)
    }

    window.onload = () => {
      console.log('Hello world');
    }
  </script>
  @endpush
</x-app-layout>
