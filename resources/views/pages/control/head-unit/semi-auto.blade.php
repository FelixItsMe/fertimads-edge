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
        <h2 class="leading-tight">
            <ol class="breadcrumb">
                <li class="breadcrumb-item breadcrumb-active">{{ __('Kontrol Head Unit') }}</li>
            </ol>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="sm:max-w-7x xl:max-w-full mx-auto sm:px-6 lg:px-8">
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
                                                        value="{{ $id }}"
                                                        class="hidden output-type peer/penyiraman" />
                                                    <label for="land-{{ $id }}"
                                                        class="inline-flex w-full px-4 py-2 bg-white rounded-md text-xs cursor-pointer dark:hover:text-gray-300 dark:border-gray-700 dark:peer-checked/penyiraman:text-blue-500 peer-checked/penyiraman:bg-primary peer-checked/penyiraman:text-white hover:text-gray-600 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700">
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
                                                class="bg-white rounded-md px-4 py-2 text-xs text-left" disabled>Pilih
                                                Lahan</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="grid grid-flow-row grid-cols-4">
                                    <div>Pilih Output</div>
                                    <div class="col-span-3">
                                        <div class="grid grid-flow-row grid-cols-2 gap-2">
                                            <div>
                                                <input type="radio" id="type-penyiraman" name="type"
                                                    value="penyiraman" class="hidden output-type peer/penyiraman" />
                                                <label for="type-penyiraman"
                                                    class="inline-flex w-full px-4 py-2 bg-white rounded-md text-xs cursor-pointer dark:hover:text-gray-300 dark:border-gray-700 dark:peer-checked/penyiraman:text-blue-500 peer-checked/penyiraman:bg-primary peer-checked/penyiraman:text-white hover:text-gray-600 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700">
                                                    Penyiraman
                                                </label>
                                            </div>
                                            <div>
                                                <input type="radio" id="type-pemupukan-n" name="type"
                                                    value="pemupukanN" class="hidden output-type peer/pemupukann" />
                                                <label for="type-pemupukan-n"
                                                    class="inline-flex w-full px-4 py-2 bg-white rounded-md text-xs cursor-pointer dark:hover:text-gray-300 dark:border-gray-700 dark:peer-checked/pemupukann:text-blue-500 peer-checked/pemupukann:bg-primary peer-checked/pemupukann:text-white hover:text-gray-600 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700">
                                                    Pemupukan N
                                                </label>
                                            </div>
                                            <div>
                                                <input type="radio" id="type-pemupukan-p" name="type"
                                                    value="pemupukanP" class="hidden output-type peer/pemupukanp" />
                                                <label for="type-pemupukan-p"
                                                    class="inline-flex w-full px-4 py-2 bg-white rounded-md text-xs cursor-pointer dark:hover:text-gray-300 dark:border-gray-700 dark:peer-checked/pemupukanp:text-blue-500 peer-checked/pemupukanp:bg-primary peer-checked/pemupukanp:text-white hover:text-gray-600 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700">
                                                    Pemupukan P
                                                </label>
                                            </div>
                                            <div>
                                                <input type="radio" id="type-pemupukan-k" name="type"
                                                    value="pemupukanK" class="hidden output-type peer/pemupukank" />
                                                <label for="type-pemupukan-k"
                                                    class="inline-flex w-full px-4 py-2 bg-white rounded-md text-xs cursor-pointer dark:hover:text-gray-300 dark:border-gray-700 dark:peer-checked/pemupukank:text-blue-500 peer-checked/pemupukank:bg-primary peer-checked/pemupukank:text-white hover:text-gray-600 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700">
                                                    Pemupukan K
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="grid grid-flow-row grid-cols-4">
                                    <div>Atur Volume (Liter)</div>
                                    <div class="col-span-3">
                                        <div class="grid grid-flow-row grid-cols-2 gap-2">
                                            <input type="number" min="0" step=".01" name="volume"
                                                class="bg-white rounded-md text-xs py-2 px-4 border-none">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div class="flex flex-col gap-2">
                                    <div>
                                        <button type="button" onclick="storeSemiAuto()"
                                            class="bg-primary text-white font-bold rounded-md px-4 py-2">Kirim</button>
                                        <button type="button" onclick="storeManual()"
                                            class="bg-red-500 text-white font-bold rounded-md px-4 py-2">Matikan Pompa</button>
                                    </div>
                                    <div class="bg-sky-500 text-white w-full p-6 sm:rounded-lg flex items-center hidden"
                                        id="info-body">
                                        <i class="fa-solid fa-circle-info text-3xl mr-3"></i>&nbsp;<span
                                            id="info-text"></span>
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
            // Get current date
            let today = new Date();
            let currentDate = today.getDate();
            let currentMonth = today.getMonth();
            let currentYear = today.getFullYear();

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

            map.modalWether = L.control({
                position: 'topright'
            });

            map.modalWether.onAdd = function(map) {
                const div = L.DomUtil.create('div', 'leaflet-control');

                div.innerHTML = `
                  <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-gradient-to-br from-blue-600 to-blue-900 rounded-lg shadow-xl sm:align-middle sm:max-w-2xl sm:w-full" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
                    <div class="p-3 grid grid-cols-3 gap-2 text-white">
                      <div>
                        <div>
                          <div class="text-lg font-extrabold lato-regular">Jumat</div>
                          <div class="text-6xl font-extrabold lato-regular relative">26<span class="absolute -top-4">°</span></div>
                        </div>
                        <div class="text-xs font-semibold text-slate-50/50">Last Updated 11:50</div>
                        <div><i class="fa-solid fa-location-dot"></i>&nbsp;<span class="text-xs">AWS 01</span></div>
                      </div>
                      <div class="grid grid-cols-1 content-between">
                        <div>
                          <div><i class="fa-solid fa-wind"></i>&nbsp;28 km/h</div>
                          <div><i class="fa-solid fa-droplet"></i>&nbsp;42%</div>
                        </div>
                        <div>
                          <div>H&nbsp;30%</div>
                          <div>L&nbsp;20%</div>
                        </div>
                      </div>
                      <div class="text-center">
                        <div><i class="fa-solid fa-moon text-8xl"></i></div>
                        <div class="text-lg text-slate-50/50">Clear</div>
                      </div>
                    </div>
                  </div>
                `;

                L.DomEvent.disableClickPropagation(div)
                L.DomEvent.disableScrollPropagation(div)
                return div;
            };

            map.modalWether.addTo(map);

            L.control.zoom({
                position: 'bottomleft'
            }).addTo(map);

            L.control.layers(baseMapOptions, null, {
                position: 'bottomright'
            }).addTo(map)

            const storeManual = async () => {
                showLoading()

                const gardenId = document.querySelector('input[name="garden_id"]:checked')?.value
                const data = await fetchData(
                    "{{ route('head-unit.stop.store') }}", {
                        method: "POST",
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').attributes.content
                                .nodeValue,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            'garden_id': gardenId,
                        })
                    }
                );

                hideLoading()

                if (!data) {
                    document.querySelector('#info-text').textContent = 'Gagal mengirim perintah'
                } else {
                    document.querySelector('#info-text').textContent =
                        'Berhasil mengirim perintah, check perangkat anda!'
                }

                document.querySelector('#info-body').classList.remove('hidden')
            }

            const storeSemiAuto = async () => {
                const type = document.querySelector('input[name="type"]:checked')?.value
                const gardenId = document.querySelector('input[name="garden_id"]:checked')?.value
                const volume = document.querySelector('input[name="volume"]')?.value
                const data = await fetchData(
                    "{{ route('head-unit.semi-auto.store') }}", {
                        method: "POST",
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').attributes.content
                                .nodeValue,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            'test': true,
                            'type': type,
                            'garden_id': gardenId,
                            'volume': volume
                        })
                    }
                );

                if (!data) {
                    document.querySelector('#info-text').textContent = 'Gagal mengirim perintah'
                } else {
                    document.querySelector('#info-text').textContent =
                        'Berhasil mengirim perintah, check perangkat anda!'
                }

                document.querySelector('#info-body').classList.remove('hidden')
            }

            const getLand = async id => {
                const data = await fetchData(
                    "{{ route('extra.land.get-land-polygon', 'ID') }}".replace('ID', id), {
                        method: "GET",
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').attributes.content
                                .nodeValue,
                            'Accept': 'application/json',
                        },
                    }
                );

                return data?.land
            }

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

                return data
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
                    let cPolygon = L.polygon(garden.polygon, {
                            color: '#' + garden.color,
                        })
                        .on('click', async e => {
                            const modalData = await gardenModalData(gardenId)

                            openModal(map, modalData);
                            generateCalendar(currentMonth, currentYear);
                        })
                    currentGroupGarden.addLayer(cPolygon)

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

            const openModal = (map, modalData) => {
                if (map.modalControl) {
                    map.removeControl(map.modalControl);
                }

                map.modalControl = L.control({
                    position: 'topleft',
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
                                    <h4 class="text-lg font-medium leading-6 text-gray-900 mb-2">Informasi Kebun</h4>
                                    <table class="w-full">
                                      <tbody>
                                        <tr class="py-3">
                                          <td class="pb-1">
                                            <p class="text-gray-500 font-bold">Luas Kebun</p>
                                          </td>
                                          <td class="pb-1">:</td>
                                          <td class="pb-1"><span class="text-gray-500 font-normal" id="luasKebun">${modalData.garden.area} m²</span></td>
                                        </tr>
                                        <tr class="py-3">
                                          <td class="py-1">
                                          <p class="text-gray-500 font-bold">Komoditi</p>
                                          </td>
                                          <td class="py-1">:</td>
                                          <td class="py-1"><span class="text-gray-500 font-normal" id="komoditi">${modalData.garden.commodity.name}</span></td>
                                        </tr>
                                        <tr class="py-3">
                                          <td class="py-1">
                                            <p class="text-gray-500 font-bold">Total Blok</p>
                                          </td>
                                          <td class="py-1">:</td>
                                          <td class="py-1"><span class="text-gray-500 font-normal" id="totalBlok">${modalData.garden.count_block} Blok</span></td>
                                        </tr>
                                        <tr class="py-3">
                                          <td class="py-1">
                                            <p class="text-gray-500 font-bold">Populasi</p>
                                          </td>
                                          <td class="py-1">:</td>
                                          <td class="py-1"><span class="text-gray-500 font-normal" id="populasi">400 Tanaman</span></td>
                                        </tr>
                                      </tbody>
                                    </table>
                                  </div>
                                  <div>
                                    <h4 class="text-lg font-medium leading-6 text-gray-900 mb-2">Unsur Hara Terbaru</h4>
                                    <div class="flex w-full flex-wrap gap-5">
                                      <div>
                                        <div class="font-bold text-gray-500">Nitrogen</div>
                                        <div class="text-gray-500 font-normal">${modalData.telemetry.soil_sensor.N.toFixed(2)} mg/kg</div>
                                      </div>
                                      <div>
                                        <div class="font-bold text-gray-500">Phosphor</div>
                                        <div class="text-gray-500 font-normal">${modalData.telemetry.soil_sensor.P.toFixed(2)} mg/kg</div>
                                      </div>
                                      <div>
                                        <div class="font-bold text-gray-500">Kalium</div>
                                        <div class="text-gray-500 font-normal">${modalData.telemetry.soil_sensor.K.toFixed(2)} mg/kg</div>
                                      </div>
                                      <div>
                                        <div class="font-bold text-gray-500">EC</div>
                                        <div class="text-gray-500 font-normal">${modalData.telemetry.soil_sensor.EC.toFixed(2)} ppm</div>
                                      </div>
                                      <div>
                                        <div class="font-bold text-gray-500">pH Tanah</div>
                                        <div class="text-gray-500 font-normal">${modalData.telemetry.soil_sensor.pH.toFixed(2)}</div>
                                      </div>
                                      <div>
                                        <div class="font-bold text-gray-500">Suhu Tanah</div>
                                        <div class="text-gray-500 font-normal">${modalData.telemetry.soil_sensor.T.toFixed(2)}C<sup>o</sup></div>
                                      </div>
                                      <div>
                                        <div class="font-bold text-gray-500">Kelembapan tanah</div>
                                        <div class="text-gray-500 font-normal">${modalData.telemetry.soil_sensor.H.toFixed(2)}%</div>
                                      </div>
                                      <div>
                                        <div class="font-bold text-gray-500">Suhu Lingkungan</div>
                                        <div class="text-gray-500 font-normal">${modalData.telemetry.dht1.T.toFixed(2)}C<sup>o</sup></div>
                                      </div>
                                      <div>
                                        <div class="font-bold text-gray-500">Kelembapan Lingkungan</div>
                                        <div class="text-gray-500 font-normal">${modalData.telemetry.dht1.H.toFixed(2)}%</div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                <div class="grid grid-cols-1 gap-4 mt-3">
                                  <div>
                                    <h4 class="text-lg font-medium leading-6 text-gray-900 mb-2">Detail Kegiatan</h4>
                                    <table class="w-full">
                                      <tbody>
                                        <tr class="py-3">
                                          <td class="pb-1 w-2">
                                            <p class="text-gray-500 font-bold">Kegiatan</p>
                                          </td>
                                          <td class="pb-1">:</td>
                                          <td class="pb-1"><span class="text-gray-500 font-normal" id="text-kegiatan">-</span></td>
                                        </tr>
                                        <tr class="py-3">
                                          <td class="py-1">
                                            <p class="text-gray-500 font-bold">Volume</p>
                                          </td>
                                          <td class="py-1">:</td>
                                          <td class="py-1"><span class="text-gray-500 font-normal" id="text-volume">-</span></td>
                                        </tr>
                                        <tr class="py-3">
                                          <td class="py-1">
                                            <p class="text-gray-500 font-bold">Estimasi Waktu</p>
                                          </td>
                                          <td class="py-1">:</td>
                                          <td class="py-1"><span class="text-gray-500 font-normal" id="text-durasi">-</span></td>
                                        </tr>
                                        <tr class="py-3">
                                          <td class="py-1">
                                          <p class="text-gray-500 font-bold">Waktu Mulai</p>
                                          </td>
                                          <td class="py-1">:</td>
                                          <td class="py-1">
                                            <div class="text-gray-500 font-normal" id="text-start">-</div>
                                          </td>
                                        </tr>
                                        <tr class="py-3">
                                          <td class="py-1">
                                            <p class="text-gray-500 font-bold">Flow Rate Progress</p>
                                          </td>
                                          <td class="py-1">:</td>
                                          <td class="py-1">
                                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                              <div class="bg-yellow-300 h-2.5 rounded-full" style="width: 45%"></div>
                                            </div>
                                          </td>
                                        </tr>
                                      </tbody>
                                    </table>

                                    <div class="flex items-center space-x-4 mt-3">
                                      <span class="w-4 rounded-full aspect-square bg-yellow-300"></span>
                                      <span class="">Pemupukan</span>
                                    </div>

                                    <div class="flex items-center space-x-4 mt-1">
                                      <span class="w-4 rounded-full aspect-square bg-blue-300"></span>
                                      <span class="">Penyiraman</span>
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
                    return div;
                };
                map.modalControl.addTo(map);
            };

            const pickLand = landId => {
                initLandPolygon(landId, map)
            }

            function generateCalendar(month, year) {
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
                        cell.classList.add('py-2', 'border', 'text-center', 'cursor-pointer', 'hover:bg-primary',
                            'hover:text-white');
                        if (currentDay <= 0 || currentDay > daysInMonth) {
                            cell.classList.add('text-gray-400'); // Grey out days from previous/next month
                        } else {
                            cell.textContent = currentDay;
                            cell.setAttribute('data-date', `${year}-${(month + 1).toString().padStart(2, "0")}-${currentDay}`);

                            cell.onclick = (e) => {
                                selectDate(e.target)
                            }

                            if (currentDay == new Date().getDate()) {
                                cell.classList.add('text-blue-500')
                            }
                        }
                        row.appendChild(cell);
                        currentDay++;
                    }
                    table.appendChild(row);
                }

                const calendarEl = document.getElementById('calendar');
                calendarEl.innerHTML = ""; // Clear previous calendar
                calendarEl.appendChild(table);
            }

            const selectDate = (e) => {
                const classes = ['bg-primary', 'text-white', 'active']

                document.querySelector('#calendar td.active')?.classList.remove(...classes)

                e.classList.add(...classes)

                document.querySelector('[name="execute_date"]').value = e.dataset.date
            }

            const addMonth = () => {
                currentMonth++
                if (currentMonth > 11) {
                    currentMonth = 0
                    currentYear++
                }

                generateCalendar(currentMonth, currentYear);
            }

            const subMonth = () => {
                currentMonth--
                if (currentMonth < 0) {
                    currentMonth = 11
                    currentYear--
                }

                generateCalendar(currentMonth, currentYear);
            }

            window.onload = () => {
                console.log('Hello world');

                document.querySelector('#list-gardens').addEventListener('change', async e => {
                    console.log('a');
                    const modalData = await gardenModalData(document.querySelector(
                        '#list-gardens input:checked').value)

                    openModal(map, modalData);
                    generateCalendar(currentMonth, currentYear);
                })
            }
        </script>
    @endpush
</x-app-layout>
