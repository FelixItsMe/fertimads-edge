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
                                    <div>Pilih Lahan</div>
                                    <div class="col-span-3">
                                        <div class="grid grid-flow-row grid-cols-2 gap-2">
                                            @foreach ($lands as $id => $landName)
                                                <div>
                                                    <input type="radio" id="land-{{ $id }}" name="land_id"
                                                        onchange="pickLand({{ $id }})"
                                                        value="{{ $id }}" class="hidden output-type peer/penyiraman" />
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
                                            <button type="button"
                                                class="bg-white rounded-md px-4 py-2 text-xs text-left"
                                                disabled
                                                >Pilih Lahan</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="grid grid-flow-row grid-cols-4">
                                    <div>Humidity</div>
                                    <div class="col-span-3">
                                        <div class="grid grid-flow-row grid-cols-2 gap-2">
                                            <div class="col-span-2">
                                                <input type="checkbox" name="hummidity[enable]" id="hummidity-enable"
                                                    class="rounded-md border-none" value="1">
                                                <label for="hummidity-enable"
                                                    class="text-xs">Enable</label>
                                            </div>
                                            <input type="number" min="0" step=".01" name="hummidity[upper_limit]"
                                                class="bg-white rounded-md text-xs py-2 px-4 border-none" id="hummidity-upper-limit"
                                                placeholder="Upper Limit" aria-placeholder="Upper Limit">
                                            <input type="number" min="0" step=".01" name="hummidity[lower_limit]"
                                                class="bg-white rounded-md text-xs py-2 px-4 border-none" id="hummidity-lower-limit"
                                                placeholder="Lower Limit" aria-placeholder="Lower Limit">
                                        </div>
                                    </div>
                                </div>
                                <div class="grid grid-flow-row grid-cols-4">
                                    <div>Nitrogen</div>
                                    <div class="col-span-3">
                                        <div class="grid grid-flow-row grid-cols-2 gap-2">
                                            <div class="col-span-2">
                                                <input type="checkbox" name="nitrogen[enable]" id="nitrogen-enable"
                                                    class="rounded-md border-none" value="1">
                                                <label for="nitrogen-enable"
                                                    class="text-xs">Enable</label>
                                            </div>
                                            <input type="number" min="0" step=".01" name="nitrogen[upper_limit]"
                                                class="bg-white rounded-md text-xs py-2 px-4 border-none" id="nitrogen-upper-limit"
                                                placeholder="Upper Limit" aria-placeholder="Upper Limit">
                                            <input type="number" min="0" step=".01" name="nitrogen[lower_limit]"
                                                class="bg-white rounded-md text-xs py-2 px-4 border-none" id="nitrogen-lower-limit"
                                                placeholder="Lower Limit" aria-placeholder="Lower Limit">
                                        </div>
                                    </div>
                                </div>
                                <div class="grid grid-flow-row grid-cols-4">
                                    <div>Phosphorus</div>
                                    <div class="col-span-3">
                                        <div class="grid grid-flow-row grid-cols-2 gap-2">
                                            <div class="col-span-2">
                                                <input type="checkbox" name="phosphorus[enable]" id="phosphorus-enable"
                                                    class="rounded-md border-none" value="1">
                                                <label for="phosphorus-enable"
                                                    class="text-xs">Enable</label>
                                            </div>
                                            <input type="number" min="0" step=".01" name="phosphorus[upper_limit]"
                                                class="bg-white rounded-md text-xs py-2 px-4 border-none" id="phosphorus-upper-limit"
                                                placeholder="Upper Limit" aria-placeholder="Upper Limit">
                                            <input type="number" min="0" step=".01" name="phosphorus[lower_limit]"
                                                class="bg-white rounded-md text-xs py-2 px-4 border-none" id="phosphorus-lower-limit"
                                                placeholder="Lower Limit" aria-placeholder="Lower Limit">
                                        </div>
                                    </div>
                                </div>
                                <div class="grid grid-flow-row grid-cols-4">
                                    <div>Kalium</div>
                                    <div class="col-span-3">
                                        <div class="grid grid-flow-row grid-cols-2 gap-2">
                                            <div class="col-span-2">
                                                <input type="checkbox" name="kalium[enable]" id="kalium-enable"
                                                    class="rounded-md border-none" value="1">
                                                <label for="kalium-enable"
                                                    class="text-xs">Enable</label>
                                            </div>
                                            <input type="number" min="0" step=".01" name="kalium[upper_limit]"
                                                class="bg-white rounded-md text-xs py-2 px-4 border-none" id="kalium-upper-limit"
                                                placeholder="Upper Limit" aria-placeholder="Upper Limit">
                                            <input type="number" min="0" step=".01" name="kalium[lower_limit]"
                                                class="bg-white rounded-md text-xs py-2 px-4 border-none" id="kalium-lower-limit"
                                                placeholder="Lower Limit" aria-placeholder="Lower Limit">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div class="flex flex-col gap-2">
                                    <div>
                                        <button type="button" onclick="storeSemiAuto()"
                                            class="bg-primary text-white font-bold rounded-md px-4 py-2">Kirim</button>
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

            const storeSemiAuto = async () => {
                const gardenId = document.querySelector('input[name="garden_id"]:checked')?.value
                const hummidity = {
                    'enable': document.querySelector('#hummidity-enable:checked') ? true : false,
                    'upper_limit': document.querySelector('#hummidity-upper-limit')?.value
                        ? document.querySelector('#hummidity-upper-limit')?.value
                        : null,
                    'lower_limit': document.querySelector('#hummidity-lower-limit')?.value
                        ? document.querySelector('#hummidity-lower-limit')?.value
                        : null,
                }
                const nitrogen = {
                    'enable': document.querySelector('#nitrogen-enable:checked') ? true : false,
                    'upper_limit': document.querySelector('#nitrogen-upper-limit')?.value
                        ? document.querySelector('#nitrogen-upper-limit')?.value
                        : null,
                    'lower_limit': document.querySelector('#nitrogen-lower-limit')?.value
                        ? document.querySelector('#nitrogen-lower-limit')?.value
                        : null,
                }
                const phosphorus = {
                    'enable': document.querySelector('#phosphorus-enable:checked') ? true : false,
                    'upper_limit': document.querySelector('#phosphorus-upper-limit')?.value
                        ? document.querySelector('#phosphorus-upper-limit')?.value
                        : null,
                    'lower_limit': document.querySelector('#phosphorus-lower-limit')?.value
                        ? document.querySelector('#phosphorus-lower-limit')?.value
                        : null,
                }
                const kalium = {
                    'enable': document.querySelector('#kalium-enable:checked') ? true : false,
                    'upper_limit': document.querySelector('#kalium-upper-limit')?.value
                        ? document.querySelector('#kalium-upper-limit')?.value
                        : null,
                    'lower_limit': document.querySelector('#kalium-lower-limit')?.value
                        ? document.querySelector('#kalium-lower-limit')?.value
                        : null,
                }

                const data = await fetchData(
                    "{{ route('head-unit.sensor.store') }}",
                    {
                        method: "POST",
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').attributes.content.nodeValue,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            'garden_id': gardenId,
                            'humidity': hummidity,
                            'nitrogen': nitrogen,
                            'phosphorus': phosphorus,
                            'kalium': kalium,
                        })
                    }
                );

                if (!data) {
                    document.querySelector('#info-text').textContent = 'Gagal mengirim perintah'
                } else {
                    document.querySelector('#info-text').textContent = 'Berhasil mengirim perintah, check perangkat anda!'
                }

                document.querySelector('#info-body').classList.remove('hidden')
            }

            const getSensor = async selenoidId => {
                if(!selenoidId) return false

                const data = await fetchData(
                    "{{ route('extra.device-selenoid.sensor', 'ID') }}".replace('ID', selenoidId),
                    {
                        method: "GET",
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').attributes.content.nodeValue,
                            'Accept': 'application/json',
                        },
                    }
                );


                if (!data) {
                    document.querySelector('#info-body').classList.remove('hidden')
                    document.querySelector('#info-text').textContent = 'Gagal mendapatkan data sensor kebun'
                    return false
                }

                const sensor = data.sensor

                document.querySelector('#hummidity-enable').checked = sensor.humidity.enable
                document.querySelector('#hummidity-upper-limit').value = sensor.humidity.upper_limit
                document.querySelector('#hummidity-lower-limit').value = sensor.humidity.lower_limit

                document.querySelector('#nitrogen-enable').checked = sensor.nitrogen.enable
                document.querySelector('#nitrogen-upper-limit').value = sensor.nitrogen.upper_limit
                document.querySelector('#nitrogen-lower-limit').value = sensor.nitrogen.lower_limit

                document.querySelector('#phosphorus-enable').checked = sensor.phosphorus.enable
                document.querySelector('#phosphorus-upper-limit').value = sensor.phosphorus.upper_limit
                document.querySelector('#phosphorus-lower-limit').value = sensor.phosphorus.lower_limit

                document.querySelector('#kalium-enable').checked = sensor.kalium.enable
                document.querySelector('#kalium-upper-limit').value = sensor.kalium.upper_limit
                document.querySelector('#kalium-lower-limit').value = sensor.kalium.lower_limit

                return true
            }

            const getLand = async id => {
                const data = await fetchData(
                    "{{ route('extra.land.get-land-polygon', 'ID') }}".replace('ID', id),
                    {
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
                            <input type="radio" id="garden-${garden.id}" name="garden_id" value="${garden.id}"
                                onchange="getSensor('${garden.device_selenoid?.selenoid}')" class="hidden peer/garden" />
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
