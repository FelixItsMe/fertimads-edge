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
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 flex flex-col gap-4">
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
                    <h5 class="text-xs text-slate-400">Total Komoditi</h5>
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
                        <ul class="flex flex-row space-x-4">
                            <li>Semua Jadwal</li>
                            <li>Penyiraman</li>
                            <li>Pemupukan</li>
                        </ul>
                        <div class="grid grid-flow-row grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
                            @foreach ($fertilizeSchedules as $fertilizeSchedule)
                                <div class="bg-white p-4 rounded-lg flex flex-col gap-2">
                                    <div class="text-sm font-semibold">Pemupukan
                                        {{ $fertilizeSchedule->deviceSelenoid->garden->name }}</div>
                                    <div class="flex flex-row justify-between">
                                        <div>
                                        </div>
                                        <div class="text-xs font-light align-middle">{{ $fertilizeSchedule->execute_start }}</div>
                                    </div>
                                </div>
                            @endforeach
                            @foreach ($waterSchedules as $waterScheduleRun)
                                <div class="bg-white p-4 rounded-lg flex flex-col gap-2">
                                    <div class="text-sm font-semibold">Penyiraman
                                        {{ $waterScheduleRun->deviceSchedule->deviceSelenoid->garden->name }}</div>
                                    <div class="flex flex-row justify-between">
                                        <div>
                                        </div>
                                        <div class="text-xs font-light align-middle">{{ $waterScheduleRun->start_time }}</div>
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
                                        <i @class([
                                          'fa-solid',
                                          'fa-user' => $activity->event == 'login',
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
            // Layer MAP
            let googleStreets = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 20,
                subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
            });
            let googleStreetsSecond = L.tileLayer('http://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
                maxZoom: 20,
                subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
            });
            let googleStreetsThird = L.tileLayer('http://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
                subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
            });

            // Layer MAP
            const map = L.map('map', {
                    preferCanvas: true,
                    layers: [googleStreetsSecond],
                    zoomControl: true
                })
                .setView([-6.869080223722067, 107.72491693496704], 12);

            const getLands = async () => {
                const data = await fetchData(
                    "{{ route('extra.land.polygon.garden') }}",
                    {
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
                        currentGroupGarden.addLayer(L.polygon(garden.polygon, {
                            color: '#' + garden.color + "55",
                        }))
                    })

                })

                map.fitBounds(firstLand.getBounds());

                currentGroupGarden.addTo(map)

                return true
            }

            const pickLand = landId => {
                initLandPolygon(landId, map)
            }

            window.onload = () => {
                console.log('Hello world');
                initLandPolygon(1, map)
            }
        </script>
    @endpush
</x-app-layout>
