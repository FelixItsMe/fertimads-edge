<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('leaflet/leaflet.css') }}">
        <link rel="stylesheet" href="{{ asset('css/extend.css') }}">
        <style>
            #map {
                height: 70vh;
                z-index: 50;
            }
        </style>
    @endpush

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Data Telemetri RSC') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="sm:max-w-7x xl:max-w-full mx-auto sm:px-6 lg:px-8 flex flex-col gap-4">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div id="map"></div>
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 flex justify-between">
                    <h1 class="text-3xl font-extrabold">Tabel Data RSC</h1>
                    <div>
                        <input type="text" placeholder="Search" aria-placeholder="Search" class="rounded-full">

                        <x-primary-button x-data=""
                            x-on:click.prevent="$dispatch('open-modal', 'export-data')">{{ __('Export Data') }}</x-primary-button>
                    </div>
                </div>
                <div class="overflow-x-scroll">
                    <table class="w-full align-middle border-slate-400 table mb-0">
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>Selenoid</th>
                                <th>Nitrogen</th>
                                <th>Fosfor</th>
                                <th>Kalium</th>
                                <th>EC</th>
                                <th>pH Tanah</th>
                                <th>Suhu Tanah</th>
                                <th>Kelembapan Tanah</th>
                                <th>Suhu Lingkungan</th>
                                <th>Kelembapan Lingkungan</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            {{-- <tr>
                        <td>12/11/2023 13:00</td>
                        <td>2(a)</td>
                        <td>7 mg/kg</td>
                        <td>3 mg/kg</td>
                        <td>5 mg/kg</td>
                        <td>151 ppm</td>
                        <td>5</td>
                        <td>30.30<sup>o</sup>C</td>
                        <td>64.20%</td>
                        <td>30.30<sup>o</sup>C</td>
                        <td>21.20%</td>
                      </tr> --}}
                            @forelse ($deviceTelemetries as $deviceTelemetry)
                                @php
                                    $telemetry = (array) $deviceTelemetry->telemetry;
                                @endphp
                                @for ($i = 1; $i <= 4; $i++)
                                    <tr>
                                        <td>{{ $deviceTelemetry->created_at }}</td>
                                        <td>Selenoid {{ $i }}</td>
                                        <td>{{ $telemetry['SS' . $i]->N }}&nbsp;mg/kg</td>
                                        <td>{{ $telemetry['SS' . $i]->P }}&nbsp;mg/kg</td>
                                        <td>{{ $telemetry['SS' . $i]->K }}&nbsp;mg/kg</td>
                                        <td>{{ $telemetry['SS' . $i]->EC }}&nbsp;uS/cm</td>
                                        <td>{{ $telemetry['SS' . $i]->pH }}</td>
                                        <td>{{ $telemetry['SS' . $i]->T }}<sup>o</sup>C</td>
                                        <td>{{ $telemetry['SS' . $i]->H }}%</td>
                                        <td>{{ number_format($telemetry['DHT1']->T, 2) }}<sup>o</sup>C</td>
                                        <td>{{ number_format($telemetry['DHT1']->H, 2) }}%</td>
                                    </tr>
                                @endfor
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($deviceTelemetries->hasPages())
                    <div class="p-6">
                        {{ $deviceTelemetries->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- Export Modal --}}
    <x-modal name="export-data" :show="$errors->isNotEmpty()" style="z-index: 9999;" focusable>
        <form method="get" action="{{ route('telemetry-rsc.export-excel') }}" class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Pilih range tanggal yang akan diexport') }}
            </h2>

            <x-input-error :messages="$errors->get('deviceTelemetries')" class="mt-2" />

            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-6">
              <div>
                <x-input-label for="from" value="{{ __('Tanggal Awal') }}" />

                <x-text-input id="from" name="from" type="date" class="mt-1 block w-full"
                    placeholder="From" value="{{ request()->query('from', now()->format('Y-m-d')) }}" />

                <x-input-error :messages="$errors->get('from')" class="mt-2" />
              </div>
              <div>
                <x-input-label for="to" value="{{ __('Tanggal Akhir') }}" />

                <x-text-input id="to" name="to" type="date" class="mt-1 block w-full"
                    placeholder="To" value="{{ request()->query('to', now()->format('Y-m-d')) }}" />

                <x-input-error :messages="$errors->get('to')" class="mt-2" />
              </div>
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Batalkan') }}
                </x-secondary-button>

                <x-primary-button class="ms-3">
                    {{ __('Export Excel') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>

    @push('scripts')
        <script src="{{ asset('leaflet/leaflet.js') }}"></script>
        <script src="{{ asset('js/extend.js') }}"></script>
        <script src="{{ asset('js/map.js') }}"></script>
        <script src="{{ asset('js/api.js') }}"></script>
        <script src="{{ asset('js/weather.js') }}"></script>
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
            let googleStreets = L.tileLayer('http://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
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
                    layers: [googleStreets],
                    zoomControl: true
                })
                .setView([-6.869080223722067, 107.72491693496704], 12);

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
                            color: '#' + garden.color,
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
                }

                bmkgWether(weatherElements)

                setInterval(() => {
                  bmkgWether(weatherElements)
                }, 1000 * 10);
            }
        </script>
    @endpush
</x-app-layout>
