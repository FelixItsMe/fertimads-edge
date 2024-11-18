<x-app-layout>
  @push('styles')
  <link rel="stylesheet" href="{{ asset('leaflet/leaflet.css') }}">
  <link rel="stylesheet" href="{{ asset('css/extend.css') }}">
  <link href="https://cdn.jsdelivr.net/gh/aazuspan/leaflet-feature-legend/src/feature-legend.css" rel="stylesheet" />
  <style>
    #map {
      height: 80vh;
      z-index: 50;
    }

    .legend-spacing {
      margin-bottom: .5em;
    }

    .legend-spacing i {
      margin-right: 1em;
    }
  </style>
  @endpush

  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
      {{ __('Dashboard') }}
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="sm:max-w-7x xl:max-w-full mx-auto sm:px-6 lg:px-8 flex flex-col gap-4">
      <div class="grid grid-flow-row grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
        <x-card-info class="w-full">
          <h5 class="text-xs text-slate-400">Luas Lahan</h5>
          <span class="font-bold">{{ $sumArea }} m&#178;</span>
        </x-card-info>
        <x-card-info class="w-full">
          <h5 class="text-xs text-slate-400">Total Kebun</h5>
          <span class="font-bold">{{ $countGarden }}</span>
        </x-card-info>
        <x-card-info class="w-full">
          <h5 class="text-xs text-slate-400">Total Varietas</h5>
          <span class="font-bold">{{ $countCommodity }}</span>
        </x-card-info>
        <x-card-info class="w-full">
          <h5 class="text-xs text-slate-400">Total Peralatan</h5>
          <span class="font-bold">{{ $countTool }}</span>
        </x-card-info>
        <x-card-info class="w-full">
          <h5 class="text-xs text-slate-400">Total Anggota</h5>
          <span class="font-bold">{{ $countUser }}</span>
        </x-card-info>
      </div>
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div id="map"></div>
      </div>
      <div class="grid grid-flow-row grid-cols-1 md:grid-cols-3 max-sm:gap-y-4 md:gap-4">
        <div class="col-span-2">
          <div class="flex flex-col gap-2">
            <ul class="flex flex-row space-x-4 font-semibold text-slate-400">
              <li @class(['text-slate-600'=> !request()->query('category', null)])><a href="?category">Semua Jadwal</a></li>
              <li @class([ 'text-slate-600'=> request()->query('category', null) == 'water',
                ])><a href="?category=water">Penyiraman</a></li>
              <li @class([ 'text-slate-600'=> request()->query('category', null) == 'fertilizer',
                ])><a href="?category=fertilizer">Pemupukan</a></li>
            </ul>
            <div class="grid grid-flow-row grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
              @foreach ($fertilizeSchedules as $fertilizeSchedule)
              <div class="bg-white p-4 rounded-lg flex flex-col gap-2">
                <div class="text-sm font-semibold">Pemupukan
                  {{ $fertilizeSchedule->garden->name }}
                </div>
                <div class="flex flex-row justify-between">
                  <div>
                  </div>
                  <div class="text-xs font-light align-middle">
                    {{ $fertilizeSchedule->execute_start }}
                  </div>
                </div>
              </div>
              @endforeach
              @foreach ($waterSchedules as $waterScheduleRun)
              <div class="bg-white p-4 rounded-lg flex flex-col gap-2">
                <div class="text-sm font-semibold">Penyiraman
                  {{ $waterScheduleRun->deviceSchedule->deviceSelenoid?->garden?->name ?? '-' }}
                </div>
                <div class="flex flex-row justify-between">
                  <div>
                  </div>
                  <div class="text-xs font-light align-middle">
                    {{ $waterScheduleRun->start_time }}
                  </div>
                </div>
              </div>
              @endforeach
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-6 flex justify-between">
                <h1 class="text-3xl font-extrabold">Kebun</h1>
              </div>
              <table class="w-full align-middle border-slate-400 table mb-0">
                <thead>
                  <tr>
                    <th>Nama Kebun</th>
                    <th>Luas Kebun</th>
                    <th>Jenis Komoditi</th>
                  </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                  @foreach ($gardens as $garden)
                  <tr>
                    <td>{{ $garden->name }}</td>
                    <td>{{ $garden->area }} m&#178;</td>
                    <td>{{ $garden->commodity->name }}</td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div>
          <div class="bg-white rounded-lg p-10">
            <ol
              class="relative text-gray-500 border-s-2 border-gray-200 dark:border-gray-700 dark:text-gray-400 pb-2">
              @foreach ($activityLog as $activity)
              <li class="mb-10 ms-6">
                <span
                  class="absolute flex items-center justify-center w-8 h-8 bg-white rounded-full -start-4 ring-4 ring-white dark:ring-gray-900 dark:bg-green-900">
                  <i @class([ 'fa-solid' , 'fa-user'=> $activity->event == 'login',
                    'fa-star' => $activity->event == 'logout',
                    'fa-plus' => $activity->event == 'create',
                    'fa-pen' => $activity->event == 'edit',
                    'fa-trash-can' => $activity->event == 'delete',
                    'w-3.5',
                    'h-3.5',
                    'text-green-500' => $activity->event == 'login',
                    'text-red-500' => in_array($activity->event, ['logout', 'delete']),
                    'text-blue-500' => $activity->event == 'create',
                    'text-yellow-500' => $activity->event == 'edit',
                    'dark:text-green-400',
                    ])></i>
                </span>
                <h3 class="font-medium leading-tight">{{ ucwords($activity->description) }}</h3>
                <p class="text-sm">{{ $activity->created_at }}</p>
              </li>
              @endforeach
            </ol>

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
  <script src="{{ asset('js/weather.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/gh/aazuspan/leaflet-feature-legend/src/feature-legend.js"></script>
  <script>
    // Get current date
    let today = new Date();
    let currentDate = today.getDate();
    let currentMonth = today.getMonth();
    let currentYear = today.getFullYear();
    let currentFullDate =
      `${currentYear}-${(currentMonth + 1).toString().padStart(2, "0")}-${currentDate.toString().padStart(2, "0")}`;
    let controller
    let controllerDetailGardenSchedules
    let pickedDate = currentFullDate
    const weatherWidgetMode = "{{ getWeatherWidgetMode()->aws_device_id }}"
    const weatherWidgetRegionCode = "{{ getWeatherWidgetMode()->region_code }}"
    const weatherWidgetRegionName = "{{ getWeatherWidgetMode()->name }}"

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
    let mapObjectsGroup = L.layerGroup()
    let mapObjectsLegends = {}
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

    L.control.layers(baseMapOptions, null, {
      position: 'bottomright'
    }).addTo(map)

    L.control.zoom({
      position: 'topleft'
    }).addTo(map);

    map.modalWether = L.control({
      position: 'topright'
    });
    map.modalWether.onAdd = function(map) {
      const div = L.DomUtil.create('div', 'leaflet-control');

      div.innerHTML = weatherHtml()

      L.DomEvent.disableClickPropagation(div)
      L.DomEvent.disableScrollPropagation(div)
      return div;
    };
    map.modalWether.addTo(map);

    map.modalControl = L.control({
      position: 'topright'
    });

    map.modalControl.onAdd = function(map) {
      const div = L.DomUtil.create('div', 'leaflet-control');

      let garden = null
      let latestTelemetry = null

      div.innerHTML = `
        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:align-middle sm:max-w-2xl sm:w-full hidden" id="garden-detail-modal" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
          <div class="px-2 pt-2 pb-2 bg-white sm:p-3 sm:pb-4 max-h-48 md:max-h-80 overflow-y-scroll">
            <div class="sm:flex sm:items-start">
              <div class="mt-3 text-center sm:mt-0 sm:text-left">
                <div class="mt-2">
                  <div class="grid grid-cols-1 md:grid-cols-2 md:gap-4">
                    <div>
                      <h4 class="text-sm md:text-lg font-medium leading-6 text-gray-900 mb-2">Informasi Kebun <span id="nama-kebun"></span></h4>
                      <table class="w-full">
                        <tbody>
                          <tr class="py-3">
                            <td class="pb-1">
                              <p class="text-gray-500 font-bold">Luas Kebun</p>
                            </td>
                            <td class="pb-1">:</td>
                            <td class="pb-1"><span class="text-gray-500 font-normal" id="luasKebun">${garden?.area ?? '-'} m²</span></td>
                          </tr>
                          <tr class="py-3">
                            <td class="py-1">
                            <p class="text-gray-500 font-bold">Komoditi</p>
                            </td>
                            <td class="py-1">:</td>
                            <td class="py-1"><span class="text-gray-500 font-normal" id="komoditi">${garden?.commodity?.name ?? ''}</span></td>
                          </tr>
                          <tr class="py-3">
                            <td class="py-1">
                              <p class="text-gray-500 font-bold">Total Blok</p>
                            </td>
                            <td class="py-1">:</td>
                            <td class="py-1"><span class="text-gray-500 font-normal" id="totalBlok">${garden?.count_block ?? '-'} Blok</span></td>
                          </tr>
                          <tr class="py-3">
                            <td class="py-1">
                              <p class="text-gray-500 font-bold">Populasi</p>
                            </td>
                            <td class="py-1">:</td>
                            <td class="py-1"><span class="text-gray-500 font-normal" id="populasi">${garden?.populations || '-'} Tanaman</span></td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                    <div>
                      <h4 class="text-xs md:text-lg font-medium leading-6 text-gray-900 mb-2">Unsur Hara Terbaru</h4>
                      <div class="flex w-full flex-wrap gap-5">
                        <div>
                          <div class="font-bold text-gray-500">Nitrogen</div>
                          <div class="text-gray-500 font-normal" id="telemetry-n">${(latestTelemetry?.telemetry?.soil_sensor.N ?? 0)} mg/kg</div>
                        </div>
                        <div>
                          <div class="font-bold text-gray-500">Fosfor</div>
                          <div class="text-gray-500 font-normal" id="telemetry-f">${(latestTelemetry?.telemetry?.soil_sensor.P ?? 0)} mg/kg</div>
                        </div>
                        <div>
                          <div class="font-bold text-gray-500">Kalium</div>
                          <div class="text-gray-500 font-normal" id="telemetry-k">${(latestTelemetry?.telemetry?.soil_sensor.K ?? 0)} mg/kg</div>
                        </div>
                        <div>
                          <div class="font-bold text-gray-500">EC</div>
                          <div class="text-gray-500 font-normal" id="telemetry-ec">${(latestTelemetry?.telemetry?.soil_sensor.EC ?? 0)} uS/cm</div>
                        </div>
                        <div>
                          <div class="font-bold text-gray-500">pH Tanah</div>
                          <div class="text-gray-500 font-normal" id="telemetry-ph">${(latestTelemetry?.telemetry?.soil_sensor.pH ?? 0)}</div>
                        </div>
                        <div>
                          <div class="font-bold text-gray-500">Suhu Tanah</div>
                          <div class="text-gray-500 font-normal" id="telemetry-t-tanah">${(latestTelemetry?.telemetry?.soil_sensor.T ?? 0)}<sup>o</sup>C</div>
                        </div>
                        <div>
                          <div class="font-bold text-gray-500">Kelembapan Tanah</div>
                          <div class="text-gray-500 font-normal" id="telemetry-h-tanah">${(latestTelemetry?.telemetry?.soil_sensor.H ?? 0)}%</div>
                        </div>
                        <div>
                          <div class="font-bold text-gray-500">Suhu Lingkungan</div>
                          <div class="text-gray-500 font-normal" id="telemetry-t-dht">${(latestTelemetry?.telemetry?.dht1.T ?? 0).toFixed(2)}<sup>o</sup>C</div>
                        </div>
                        <div>
                          <div class="font-bold text-gray-500">Kelembapan Lingkungan</div>
                          <div class="text-gray-500 font-normal" id="telemetry-h-dht">${(latestTelemetry?.telemetry?.dht1.H ?? 0).toFixed(2)}%</div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="grid grid-cols-1 md:grid-cols-2 md:gap-4 mt-3">
                    <div>
                      <h4 class="text-xs md:text-lg font-medium leading-6 text-gray-900 mb-2">Jadwal Fertigasi</h4>
                      <div class="overflow-y-hidden">
                        <input type="hidden" name="execute_date">
                        <div class="py-2 px-4 bg-primary text-white flex flex-row justify-between">
                          <div id="month-year-text" class="font-extrabold"></div>
                          <div class="flex gap-2">
                            <button type="button" class="hover:text-slate-400"
                              onclick="subMonth(this)" id="calendar-add-month" data-garden-id=""><i class="fa-solid fa-chevron-left"></i></button>
                            <button type="button" class="hover:text-slate-400"
                              onclick="addMonth(this)" id="calendar-sub-month" data-garden-id=""><i class="fa-solid fa-chevron-right"></i></button>
                          </div>
                        </div>
                        <div id="calendar"></div>
                      </div>

                      <div class="flex items-center space-x-4 mt-3">
                        <div class="flex items-center space-x-2">
                          <span class="w-4 rounded-full aspect-square bg-yellow-300"></span>
                          <span class="text-xs md:text-base">Pemupukan</span>
                        </div>

                        <div class="flex items-center space-x-2">
                          <span class="w-4 rounded-full aspect-square bg-primary"></span>
                          <span class="text-xs md:text-base">Penyiraman</span>
                        </div>
                      </div>
                    </div>
                    <div class="">
                      <h4 class="text-xs md:text-lg font-medium leading-6 text-gray-900 mb-2">Detail Informasi Jadwal</h4>

                      <div id="list-detail-schedule" class="flex flex-col space-y-2">
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
            <button type="button" class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-teal-700 hover:text-white sm:ml-3 sm:w-auto sm:text-sm" onclick="document.querySelector('#garden-detail-modal').classList.add('hidden')">Tutup</button>
          </div>
        </div>
      `;

      L.DomEvent.disableClickPropagation(div)
      L.DomEvent.disableScrollPropagation(div)
      return div;
    };
    map.modalControl.addTo(map);

    const getObjects = async () => {
      const data = await fetchData(
        "{{ route('extra.map-object.geojson') }}", {
          method: 'GET',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').attributes.content.nodeValue,
            'Accept': 'application/json',
          }
        }
      )

      return data
    }

    const getLands = async () => {
      const data = await fetchData(
        "{{ route('extra.land.polygon.garden') }}", {
          method: "GET",
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').attributes.content
              .nodeValue,
            'Accept': 'application/json',
          },
        }
      );

      return data?.lands
    }

    const initMapObjects = async (map) => {
      const objects = await getObjects()

      if (!objects) {
        return false
      }

      mapObjectsGroup.clearLayers()

      const onEachFeature = (feature, layer) => {
        if (feature.properties && feature.properties.name) {
          layer.bindPopup(feature.properties.name);
        }

        if (feature.properties && feature.properties.icon) {
          let icon = L.icon({
            iconUrl: feature.properties.icon,
            iconSize: [25, 25],
            iconAnchor: [12, 12],
          })

          layer.setIcon(icon)
        }

        if (feature.properties && feature.properties.icon) {
          let icon = L.icon({
            iconUrl: feature.properties.icon,
            iconSize: [25, 25],
            iconAnchor: [12, 12],
          })

          layer.setIcon(icon)
        }

        if (feature.properties && feature.properties.type) {
          mapObjectsLegends[feature.properties.type] = layer
        }
      }

      mapObjectsGroup.addLayer(L.geoJSON(objects, {
        onEachFeature: onEachFeature
      }))
      mapObjectsGroup.addTo(map)
      L.control.featureLegend(mapObjectsLegends, {
        position: "bottomleft",
        title: "Legenda",
        symbolContainerSize: 24,
        symbolScaling: "clamped",
        maxSymbolSize: 24,
        minSymbolSize: 2,
        collapsed: false,
        drawShadows: true,
      }).addTo(map);

      const legendTitle = document.querySelector('.leaflet-control-feature-legend-title')
      const legendContents = document.querySelectorAll('.leaflet-control-feature-legend-contents div')
      legendTitle.classList.add("font-bold", "mb-3")
      legendContents.forEach((item) => {
        item.classList.add("legend-spacing")
      })
    }

    const initLandPolygon = async (id, map) => {
      const lands = await getLands()

      if (currentLand.polygonLayer) {
        currentLand.polygonLayer.remove()
      }

      if (!lands) {
        return false
      }

      currentGroupGarden.clearLayers()

      let firstLand = null

      lands.forEach(land => {
        if (land.gardens) {
          firstLand = L.polygon(land.polygon, {
            dashArray: '10, 10',
            dashOffset: '20',
            color: '#fff',
          })
        }
        currentGroupGarden.addLayer(
          L.polygon(land.polygon, {
            dashArray: '10, 10',
            dashOffset: '20',
            color: '#fff',
          })
        )

        land.gardens.forEach(garden => {
          currentGroupGarden.addLayer(
            L.polygon(garden.polygon, {
              color: '#' + garden.color,
            }).on('click', async e => {
              const modalData = await gardenModalData(garden.id)
            })
          )
        })

      })

      currentGroupGarden.addTo(map)

      return true
    }

    const openModal = async (map, garden) => {
      if (map.modalControl) {
        map.removeControl(map.modalControl);
      }

      let latestTelemetry
      await getLatestTelemetry(garden.id).then(data => {
        latestTelemetry = data
      })

      map.modalControl = L.control({
        position: 'topleft'
      });

      map.modalControl.onAdd = function(map) {
        const div = L.DomUtil.create('div', 'leaflet-control');

        div.innerHTML = `
          <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:align-middle sm:max-w-2xl sm:w-full" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
            <div class="px-2 pt-5 pb-4 bg-white sm:p-3 sm:pb-4">
              <div class="sm:flex sm:items-start">
                <div class="mt-3 text-center sm:mt-0 sm:text-left">
                  <div class="mt-2">
                    <div class="grid grid-cols-2 gap-4">
                      <div>
                        <h4 class="text-lg font-medium leading-6 text-gray-900 mb-2">Informasi Kebun ${garden?.name ?? ''}</h4>
                        <table class="w-full">
                          <tbody>
                            <tr class="py-3">
                              <td class="pb-1">
                                <p class="text-gray-500 font-bold">Luas Kebun</p>
                              </td>
                              <td class="pb-1">:</td>
                              <td class="pb-1"><span class="text-gray-500 font-normal" id="luasKebun">${garden?.area ?? '-'} m²</span></td>
                            </tr>
                            <tr class="py-3">
                              <td class="py-1">
                                <p class="text-gray-500 font-bold">Tahap</p>
                              </td>
                              <td class="py-1">:</td>
                              <td class="py-1"><span class="text-gray-500 font-normal" id="tahap">Penanaman</span></td>
                            </tr>
                            <tr class="py-3">
                              <td class="py-1">
                                <p class="text-gray-500 font-bold">Mandor</p>
                              </td>
                              <td class="py-1">:</td>
                              <td class="py-1"><span class="text-gray-500 font-normal" id="mandor">Jhon Doe</span></td>
                            </tr>
                            <tr class="py-3">
                              <td class="py-1">
                              <p class="text-gray-500 font-bold">Komoditi</p>
                              </td>
                              <td class="py-1">:</td>
                              <td class="py-1"><span class="text-gray-500 font-normal" id="komoditi">${garden?.commodity?.name ?? ''}</span></td>
                            </tr>
                            <tr class="py-3">
                              <td class="py-1">
                                <p class="text-gray-500 font-bold">Total Blok</p>
                              </td>
                              <td class="py-1">:</td>
                              <td class="py-1"><span class="text-gray-500 font-normal" id="totalBlok">${garden?.count_block ?? '-'} Blok</span></td>
                            </tr>
                            <tr class="py-3">
                              <td class="py-1">
                                <p class="text-gray-500 font-bold">Populasi</p>
                              </td>
                              <td class="py-1">:</td>
                              <td class="py-1"><span class="text-gray-500 font-normal" id="populasi">${garden?.populations || '-'} Tanaman</span></td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                      <div>
                        <h4 class="text-lg font-medium leading-6 text-gray-900 mb-2">Unsur Hara Terbaru</h4>
                        <div class="flex w-full flex-wrap gap-5">
                          <div>
                            <div class="font-bold text-gray-500">Nitrogen</div>
                            <div class="text-gray-500 font-normal">${(latestTelemetry?.telemetry?.soil_sensor.N ?? 0)} mg/kg</div>
                          </div>
                          <div>
                            <div class="font-bold text-gray-500">Fosfor</div>
                            <div class="text-gray-500 font-normal">${(latestTelemetry?.telemetry?.soil_sensor.P ?? 0)} mg/kg</div>
                          </div>
                          <div>
                            <div class="font-bold text-gray-500">Kalium</div>
                            <div class="text-gray-500 font-normal">${(latestTelemetry?.telemetry?.soil_sensor.K ?? 0)} mg/kg</div>
                          </div>
                          <div>
                            <div class="font-bold text-gray-500">EC</div>
                            <div class="text-gray-500 font-normal">${(latestTelemetry?.telemetry?.soil_sensor.EC ?? 0)} uS/cm</div>
                          </div>
                          <div>
                            <div class="font-bold text-gray-500">pH Tanah</div>
                            <div class="text-gray-500 font-normal">${(latestTelemetry?.telemetry?.soil_sensor.pH ?? 0)}</div>
                          </div>
                          <div>
                            <div class="font-bold text-gray-500">Suhu Tanah</div>
                            <div class="text-gray-500 font-normal">${(latestTelemetry?.telemetry?.soil_sensor.T ?? 0)}<sup>o</sup>C</div>
                          </div>
                          <div>
                            <div class="font-bold text-gray-500">Kelembapan Tanah</div>
                            <div class="text-gray-500 font-normal">${(latestTelemetry?.telemetry?.soil_sensor.H ?? 0)}%</div>
                          </div>
                          <div>
                            <div class="font-bold text-gray-500">Suhu Lingkungan</div>
                            <div class="text-gray-500 font-normal">${(latestTelemetry?.telemetry?.dht1.T ?? 0).toFixed(2)}<sup>o</sup>C</div>
                          </div>
                          <div>
                            <div class="font-bold text-gray-500">Kelembapan Lingkungan</div>
                            <div class="text-gray-500 font-normal">${(latestTelemetry?.telemetry?.dht1.H ?? 0).toFixed(2)}%</div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mt-3">
                      <div>
                        <h4 class="text-lg font-medium leading-6 text-gray-900 mb-2">Jadwal Fertigasi</h4>
                        <div class="">
                          <input type="hidden" name="execute_date">
                          <div class="py-2 px-4 bg-primary text-white flex flex-row justify-between">
                            <div id="month-year-text" class="font-extrabold"></div>
                            <div class="flex gap-2">
                              <button type="button" class="hover:text-slate-400"
                              id="subMonthButton"><i class="fa-solid fa-chevron-left"></i></button>
                              <button type="button" class="hover:text-slate-400"
                              id="addMonthButton"><i class="fa-solid fa-chevron-right"></i></button>
                            </div>
                          </div>
                          <div id="calendar"></div>
                        </div>
                      </div>
                      <div>
                        <h4 class="text-lg font-medium leading-6 text-gray-900 mb-2">Detail Informasi Jadwal</h4>
                        <table class="w-full">
                          <tbody>
                            <tr class="py-3">
                              <td class="pb-1" style="vertical-align: top">
                                <p class="text-gray-500 font-bold">Kegiatan</p>
                              </td>
                              <td class="pb-1" style="vertical-align: top">:</td>
                              <td class="pb-1"><span class="text-gray-500 font-normal" id="jenisKegiatan">-</span></td>
                            </tr>
                            <tr class="py-3">
                              <td class="py-1" style="vertical-align: top">
                                <p class="text-gray-500 font-bold">Estimasi Volume</p>
                              </td>
                              <td class="py-1" style="vertical-align: top">:</td>
                              <td class="py-1"><span class="text-gray-500 font-normal" id="volume">-</span></td>
                            </tr>
                            <tr class="py-3">
                              <td class="py-1" style="vertical-align: top">
                                <p class="text-gray-500 font-bold">Estimasi Durasi</p>
                              </td>
                              <td class="py-1" style="vertical-align: top">:</td>
                              <td class="py-1"><span class="text-gray-500 font-normal" id="waktuKegiatan">-</span></td>
                            </tr>
                            <tr class="py-3">
                              <td class="py-1" style="vertical-align: top">
                              <p class="text-gray-500 font-bold">Waktu Penjadwalan</p>
                              </td>
                              <td class="py-1" style="vertical-align: top">:</td>
                              <td class="py-1">
                                <div class="text-gray-500 font-normal" id="waktuPenjadwalan">-</div>
                              </td>
                            </tr>
                            <tr class="py-3">
                              <td class="py-1" style="vertical-align: top">
                                <p class="text-gray-500 font-bold">Aktual Volume</p>
                              </td>
                              <td class="py-1" style="vertical-align: top">:</td>
                              <td class="py-1"><span class="text-gray-500 font-normal" id="aktualVolume">-</span></td>
                            </tr>
                            <tr class="py-3">
                              <td class="py-1" style="vertical-align: top">
                                <p class="text-gray-500 font-bold">Aktual Durasi</p>
                              </td>
                              <td class="py-1" style="vertical-align: top">:</td>
                              <td class="py-1"><span class="text-gray-500 font-normal" id="aktualWaktuKegiatan">-</span></td>
                            </tr>
                            <tr class="py-3">
                              <td class="py-1">
                                <p class="text-gray-500 font-bold">Flow Rate Progress</p>
                              </td>
                            </tr>
                            <tr class="py-3">
                              <td class="py-1">
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                  <div class="bg-yellow-300 h-2.5 rounded-full" style="width: ${latestTelemetry?.telemetry?.flow_meter.V ?? '0'}%"></div>
                                </div>
                              </td>
                              <td class="py-1"></td>
                              <td class="py-1 px-3">
                              ${(latestTelemetry?.telemetry?.flow_meter.V ?? 0).toFixed(2)}%
                              </td>
                            </tr>
                          </tbody>
                        </table>

                        <div class="flex items-center space-x-4 mt-3">
                          <div class="flex items-center space-x-2">
                            <span class="w-4 rounded-full aspect-square bg-yellow-300"></span>
                            <span class="">Pemupukan</span>
                          </div>

                          <div class="flex items-center space-x-2">
                            <span class="w-4 rounded-full aspect-square bg-blue-300"></span>
                            <span class="">Penyiraman</span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
              <button type="button" class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-teal-700 hover:text-white sm:ml-3 sm:w-auto sm:text-sm" onclick="map.removeControl(map.modalControl)">Tutup</button>
            </div>
          </div>
        `;

        L.DomEvent.disableClickPropagation(div)
        L.DomEvent.disableScrollPropagation(div)
        return div;
      };
      map.modalControl.addTo(map);
    };

    const gardenModalData = async id => {
      const data = await fetchData(
        "{{ route('extra.garden.modal', 'ID') }}".replace('ID', id), {
          method: "GET",
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').attributes.content
              .nodeValue,
            'Accept': 'application/json',
          },
        }
      );

      if (!data) return false

      document.querySelector('#nama-kebun').textContent = data.garden.name
      document.querySelector('#luasKebun').textContent = data.garden.area + " m²"
      document.querySelector('#komoditi').textContent = data.garden.commodity.name
      document.querySelector('#totalBlok').textContent = data.garden.count_block
      document.querySelector('#populasi').textContent = data.garden.population

      document.querySelector('#telemetry-n').textContent = parseFloat(data.telemetry.soil_sensor.N).toFixed(
        2) + " mg/kg"
      document.querySelector('#telemetry-f').textContent = parseFloat(data.telemetry.soil_sensor.P).toFixed(
        2) + " mg/kg"
      document.querySelector('#telemetry-k').textContent = parseFloat(data.telemetry.soil_sensor.K).toFixed(
        2) + " mg/kg"
      document.querySelector('#telemetry-ec').textContent = parseFloat(data.telemetry.soil_sensor.EC).toFixed(
        2) + " uS/cm"
      document.querySelector('#telemetry-ph').textContent = parseFloat(data.telemetry.soil_sensor.pH).toFixed(
        2)
      document.querySelector('#telemetry-t-tanah').textContent = parseFloat(data.telemetry.soil_sensor.T)
        .toFixed(2) + "°C"
      document.querySelector('#telemetry-h-tanah').textContent = parseFloat(data.telemetry.soil_sensor.H)
        .toFixed(2) + "%"
      document.querySelector('#telemetry-t-dht').textContent = parseFloat(data.telemetry.dht1.T).toFixed(2) +
        "°C"
      document.querySelector('#telemetry-h-dht').textContent = parseFloat(data.telemetry.dht1.H).toFixed(2) +
        "%"

      document.querySelector('#calendar-add-month').dataset.gardenId = data.garden.id
      document.querySelector('#calendar-sub-month').dataset.gardenId = data.garden.id

      const calendarSchedules = await getSchedules(data.garden.id, currentYear, currentMonth + 1)

      // Generate and display the calendar
      generateCalendar(currentMonth, currentYear, data.garden.id, calendarSchedules.schedules);

      document.querySelector('#garden-detail-modal').classList.remove('hidden')
    }

    const generateCalendar = (month, year, gardenId, availableSchedules) => {
      const calendarEl = document.getElementById('calendar');

      // Get the first day of the month
      const firstDay = new Date(year, month, 1);

      // Get the number of days in the month
      const daysInMonth = new Date(year, month + 1, 0).getDate();

      // Get the day of the week for the first day
      const dayOfWeek = firstDay.getDay();

      // Get month name
      const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
      ];
      const currentMonthName = monthNames[month];

      document.querySelector('#month-year-text').textContent = `${currentMonthName} ${year}`

      // Create the table element
      const table = document.createElement('table');
      table.classList.add('table-auto', 'w-full');

      // Create the table header row
      const headerRow = document.createElement('tr');
      ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jum\'at', 'Sabtu'].forEach(day => {
        const headerCell = document.createElement('th');
        headerCell.classList.add('text-center', 'text-xs', 'py-2', 'bg-gray-200', 'w-1/7');
        headerCell.textContent = day;
        headerRow.appendChild(headerCell);
      });
      table.appendChild(headerRow);

      // Create the calendar grid
      let currentDay = 1 - dayOfWeek; // Adjust for starting day
      while (currentDay <= daysInMonth) {
        const row = document.createElement('tr');
        for (let i = 0; i < 7; i++) {
          const cell = document.createElement('td');
          cell.classList.add('py-4', 'border', 'text-center', 'relative', 'cursor-pointer',
            'hover:bg-primary',
            'hover:text-white');
          if (currentDay <= 0 || currentDay > daysInMonth) {
            cell.classList.add('text-gray-400'); // Grey out days from previous/next month
          } else {
            const info = document.createElement('div');
            const wBar = document.createElement('div');
            const fBar = document.createElement('div');
            const formatDate =
              `${year}-${(month + 1).toString().padStart(2, "0")}-${currentDay.toString().padStart(2, "0")}`
            cell.textContent = currentDay;
            cell.setAttribute('data-date', formatDate);

            if (formatDate == pickedDate) {
              selectDate(cell, gardenId)
            }

            cell.onclick = (e) => {
              selectDate(e.target, gardenId)
            }

            if (currentDay == new Date().getDate()) {
              cell.classList.add('text-blue-500')
            }

            const schedule = availableSchedules.find(schedule => {
              return schedule.date == formatDate
            })

            if (schedule?.schedule.includes(1)) {
              wBar.classList.add('bg-primary', 'w-full', 'h-1');
            }
            if (schedule?.schedule.includes(2)) {
              fBar.classList.add('bg-yellow-300', 'w-full', 'h-1');
            }
            info.classList.add(
              'absolute',
              'bottom-1',
              'left-0',
              'h-3',
              'right-0',
              'flex',
              'flex-col',
              'px-2'
            );

            info.appendChild(wBar)
            info.appendChild(fBar)
            cell.appendChild(info)
          }
          row.appendChild(cell);
          currentDay++;
        }
        table.appendChild(row);
      }

      calendarEl.innerHTML = ""; // Clear previous calendar
      calendarEl.appendChild(table);
    }

    const selectDate = async (e, gardenId) => {
      if (!e.dataset.date) {
        return false
      }
      const classes = ['bg-primary', 'text-white', 'active']

      document.querySelector('#calendar td.active')?.classList.remove(...classes)

      e.classList.add(...classes)

      pickedDate = e.dataset.date

      await scheduleGardenDetail(pickedDate, gardenId)
    }

    const scheduleGardenDetail = async (date, gardenId) => {
      const eListDetailSchedules = document.querySelector('#list-detail-schedule')

      eListDetailSchedules.innerHTML = `<div class="bg-white p-4 rounded-md shadow-md flex justify-center">
                            <x-loading />
                          </div>`

      if (controllerDetailGardenSchedules) {
        controllerDetailGardenSchedules.abort()
      }

      controllerDetailGardenSchedules = new AbortController();
      const signal = controllerDetailGardenSchedules.signal;
      const data = await fetchData(
        "{{ route('activity-schedule.detail', ['date' => 'DATE', 'garden' => 'GARDEN']) }}"
        .replace('DATE', date)
        .replace('GARDEN', gardenId), {
          signal,
          method: "GET",
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').attributes.content
              .nodeValue,
            'Accept': 'application/json',
          },
        }
      );

      eListDetailSchedules.innerHTML = ``

      if (!data) {
        return false
      }

      let eDetail = ``

      data.waterSchedules.forEach(waterSchedule => {
        eDetail += initWaterSchedules(waterSchedule)
      });

      data.fertilizerSchedules.forEach(fertilizerSchedule => {
        eDetail += initFertilizerSchedules(fertilizerSchedule)
      });

      eListDetailSchedules.innerHTML = eDetail
    }

    const initWaterSchedules = waterSchedule => {
      const date1 = parseDatetime(waterSchedule.start_time);
      const date2 = parseDatetime(waterSchedule.end_time);

      const diffInMiliseconds = date2 - date1

      const diffInMinutes = diffInMiliseconds / (1000 * 60)
      let actualDiffInMinutes = null

      const diffDay = calculateDayDifference(
        new Date(waterSchedule.device_schedule.start_date),
        date1,
      )

      const newCommAge = diffDay + waterSchedule.device_schedule.commodity_age

      let eActual = ``

      if (waterSchedule.device_schedule_execute) {
        const actualDate1 = parseDatetime(waterSchedule.device_schedule_execute.start_time);
        const actualDate2 = parseDatetime(waterSchedule.device_schedule_execute.end_time);

        actualDiffInMinutes = (actualDate2 - actualDate1) / (1000 * 60)

        eActual = `<div class="grid grid-flow-row grid-cols-5 align-text-bottom">
                                <div class="col-span-2">
                                    <span class="text-sm font-bold text-slate-400">Aktual Volume</span>
                                </div>
                                <div class="col-span-3">
                                    <span class="col-span-3 text-sm text-slate-400" id="text-water-times">${waterSchedule.device_schedule_execute?.total_volume.toFixed(2)} Liter</span>
                                </div>
                            </div>
                            <div class="grid grid-flow-row grid-cols-5 align-text-bottom">
                                <div class="col-span-2">
                                    <span class="text-sm font-bold text-slate-400">Aktual Durasi</span>
                                </div>
                                <div class="col-span-3">
                                    <span class="col-span-3 text-sm text-slate-400" id="text-water-times">${actualDiffInMinutes.toFixed(2)} Menit</span>
                                </div>
                            </div>`
      }

      return `<div class="bg-white p-4 rounded-md shadow-md">
                            <h3 class="text-xs md:text-base font-bold mb-4">Detail Informasi Jadwal Penyiraman</h3>
                            <div class="grid grid-flow-row grid-cols-5 align-text-bottom">
                                <div class="col-span-2">
                                    <span class="text-xs md:text-sm font-bold text-slate-400">Komoditi</span>
                                </div>
                                <div class="col-span-3">
                                    <span class="col-span-3 text-xs md:text-sm text-slate-400" id="text-water-times">${waterSchedule.device_schedule.commodity.name}</span>
                                </div>
                            </div>
                            <div class="grid grid-flow-row grid-cols-5 align-text-bottom">
                                <div class="col-span-2">
                                    <span class="text-xs md:text-sm font-bold text-slate-400">Umur</span>
                                </div>
                                <div class="col-span-3">
                                    <span class="col-span-3 text-xs md:text-sm text-slate-400" id="text-water-times">${newCommAge} Hari</span>
                                </div>
                            </div>
                            <div class="grid grid-flow-row grid-cols-5 align-text-bottom">
                                <div class="col-span-2">
                                    <span class="text-xs md:text-sm font-bold text-slate-400">Volume</span>
                                </div>
                                <div class="col-span-3">
                                    <span class="col-span-3 text-xs md:text-sm text-slate-400" id="text-water-times">${waterSchedule.total_volume.toFixed(2)} Liter</span>
                                </div>
                            </div>
                            <div class="grid grid-flow-row grid-cols-5 align-text-bottom">
                                <div class="col-span-2">
                                    <span class="text-xs md:text-sm font-bold text-slate-400">Durasi</span>
                                </div>
                                <div class="col-span-3">
                                    <span class="col-span-3 text-xs md:text-sm text-slate-400" id="text-water-times">${diffInMinutes.toFixed(2)} Menit</span>
                                </div>
                            </div>
                            <div class="grid grid-flow-row grid-cols-5 align-text-bottom">
                                <div class="col-span-2">
                                    <span class="text-xs md:text-sm font-bold text-slate-400">Waktu</span>
                                </div>
                                <div class="col-span-3">
                                    <span class="col-span-3 text-xs md:text-sm text-slate-400" id="text-water-times">${waterSchedule.start_time}</span>
                                </div>
                            </div>
                            ${eActual}
                          </div>`
    }

    const initFertilizerSchedules = fertilizerSchedules => {
      const date1 = parseDatetime(fertilizerSchedules.execute_start);
      const date2 = parseDatetime(fertilizerSchedules.execute_end);

      const diffInMiliseconds = date2 - date1

      const diffInMinutes = diffInMiliseconds / (1000 * 60)
      let actualDiffInMinutes = null

      let eActual = ``

      if (fertilizerSchedules.schedule_execute) {
        const actualDate1 = parseDatetime(fertilizerSchedules.schedule_execute.execute_start);
        const actualDate2 = parseDatetime(fertilizerSchedules.schedule_execute.execute_end);

        actualDiffInMinutes = (actualDate2 - actualDate1) / (1000 * 60)

        eActual = `<div class="grid grid-flow-row grid-cols-5 align-text-bottom">
                                <div class="col-span-2">
                                    <span class="text-xs md:text-sm font-bold text-slate-400">Aktual Volume</span>
                                </div>
                                <div class="col-span-3">
                                    <span class="col-span-3 text-xs md:text-sm text-slate-400" id="text-water-times">${fertilizerSchedules.schedule_execute?.total_volume.toFixed(2)} Liter</span>
                                </div>
                            </div>
                            <div class="grid grid-flow-row grid-cols-5 align-text-bottom">
                                <div class="col-span-2">
                                    <span class="text-xs md:text-sm font-bold text-slate-400">Aktual Durasi</span>
                                </div>
                                <div class="col-span-3">
                                    <span class="col-span-3 text-xs md:text-sm text-slate-400" id="text-water-times">${actualDiffInMinutes.toFixed(2)} Menit</span>
                                </div>
                            </div>`
      }

      return `<div class="bg-white p-4 rounded-md shadow-md">
                            <h3 class="text-xs md:text-base font-bold mb-4">Detail Informasi Jadwal Pemupukan</h3>
                            <div class="grid grid-flow-row grid-cols-5 align-text-bottom">
                                <div class="col-span-2">
                                    <span class="text-xs md:text-sm font-bold text-slate-400">Volume</span>
                                </div>
                                <div class="col-span-3">
                                    <span class="col-span-3 text-xs md:text-sm text-slate-400" id="text-water-times">${fertilizerSchedules.total_volume.toFixed(2)} Liter</span>
                                </div>
                            </div>
                            <div class="grid grid-flow-row grid-cols-5 align-text-bottom">
                                <div class="col-span-2">
                                    <span class="text-xs md:text-sm font-bold text-slate-400">Durasi</span>
                                </div>
                                <div class="col-span-3">
                                    <span class="col-span-3 text-xs md:text-sm text-slate-400" id="text-water-times">${diffInMinutes.toFixed(2)} Menit</span>
                                </div>
                            </div>
                            <div class="grid grid-flow-row grid-cols-5 align-text-bottom">
                                <div class="col-span-2">
                                    <span class="text-xs md:text-sm font-bold text-slate-400">Waktu</span>
                                </div>
                                <div class="col-span-3">
                                    <span class="col-span-3 text-xs md:text-sm text-slate-400" id="text-water-times">${fertilizerSchedules.execute_start}</span>
                                </div>
                            </div>
                            ${eActual}
                          </div>`
    }

    function parseDatetime(datetimeStr) {
      // Split the datetime string into date and time components
      let [date, time] = datetimeStr.split(' ');

      // Split the date component into year, month, and day
      let [year, month, day] = date.split('-');

      // Split the time component into hours, minutes, and seconds
      let [hours, minutes, seconds] = time.split(':');

      // Create a new Date object (Note: months are 0-indexed in JavaScript Date)
      return new Date(year, month - 1, day, hours, minutes, seconds);
    }

    const pickLand = landId => {
      initLandPolygon(landId, map)
    }

    const getSchedules = async (gardenId, year, month) => {
      if (controller) {
        controller.abort()
      }

      controller = new AbortController();
      const signal = controller.signal;
      const calendarUrl = new URL(
        "{{ route('activity-schedule.schedule-in-month', ['month' => 'MONTH', 'year' => 'YEAR']) }}"
        .replace('MONTH', month)
        .replace('YEAR', year)
      )

      calendarUrl.searchParams.append('garden_id', gardenId)

      const data = await fetchData(
        calendarUrl, {
          signal,
          method: "GET",
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').attributes.content
              .nodeValue,
            'Accept': 'application/json',
          },
        }
      );

      return data
    }

    const addMonth = async e => {
      currentMonth++
      if (currentMonth > 11) {
        currentMonth = 0
        currentYear++
      }

      const data = await getSchedules(e.dataset.gardenId, currentYear, currentMonth + 1)

      generateCalendar(currentMonth, currentYear, e.dataset.gardenId, data.schedules);
    }

    const subMonth = async e => {
      currentMonth--
      if (currentMonth < 0) {
        currentMonth = 11
        currentYear--
      }

      const data = await getSchedules(e.dataset.gardenId, currentYear, currentMonth + 1)

      generateCalendar(currentMonth, currentYear, e.dataset.gardenId, data.schedules);
    }

    const calculateDayDifference = (d1, d2) => {

      // Calculate the time difference in milliseconds
      const timeDiff = Math.abs(d2 - d1);

      // Convert milliseconds to days
      const dayDiff = Math.ceil(timeDiff / (1000 * 60 * 60 * 24));

      return dayDiff - 1;
    }

    window.onload = () => {
      console.log('Hello world');
      initLandPolygon(1, map)
      initMapObjects(map)

      const weatherElements = {
        eTemp: document.querySelector('#bmkg-temp'),
        eHumid: document.querySelector('#bmkg-humid'),
        eMaxT: document.querySelector('#bmkg-max-t'),
        eMinT: document.querySelector('#bmkg-min-t'),
        eWindSpeed: document.querySelector('#bmkg-ws'),
        eWeatherName: document.querySelector('#bmkg-weather-name'),
        eWeatherIcon: document.querySelector('#bmkg-weather-icon'),
        eTime: document.querySelector('#bmkg-times'),
        eDay: document.querySelector('#bmkg-day'),
        eRegionName: document.querySelector('#bmkg-region-name'),
        regionCode: weatherWidgetRegionCode,
        regionName: weatherWidgetRegionName
      }

      if (!weatherWidgetMode) {
        bmkgWether(weatherElements)

        setInterval(() => {
          bmkgWether(weatherElements)
        }, 1000 * 10);
      } else if (weatherWidgetMode != null) {
        awsWether(weatherWidgetMode, weatherElements)
      }
    }
  </script>
  @endpush
</x-app-layout>
