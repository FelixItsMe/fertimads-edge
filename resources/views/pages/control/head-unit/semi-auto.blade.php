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
                <div class="grid grid-flow-row grid-cols-1 md:grid-cols-5 gap-2 max-md:px-4">
                    <div class="flex flex-col gap-2 pr-12">
                        <div class="font-bold py-2">Opsi Kendali</div>
                        @include('pages.control.head-unit.links')
                    </div>
                    <div class="col-span-4 flex flex-col gap-2">
                        <div class="py-2"><span class="font-bold">Kendali Perangkat</span></div>
                        <div class="grid grid-flow-row grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="flex flex-col gap-2">
                                <div class="grid grid-flow-row grid-cols-1 md:grid-cols-4">
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
                                <div class="grid grid-flow-row grid-cols-1 md:grid-cols-4">
                                    <div>Pilih Kebun</div>
                                    <div class="col-span-3">
                                        <div class="grid grid-flow-row grid-cols-2 gap-2" id="list-gardens">
                                            <button type="button"
                                                class="bg-white rounded-md px-4 py-2 text-xs text-left" disabled>Pilih
                                                Lahan</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="grid grid-flow-row grid-cols-1 md:grid-cols-4">
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
                                <div class="grid grid-flow-row grid-cols-1 md:grid-cols-4">
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
                                            class="bg-red-500 text-white font-bold rounded-md px-4 py-2">Matikan
                                            Pompa</button>
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
        <script src="{{ asset('js/weather.js') }}"></script>
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
                                      <td class="pb-1"><span class="text-gray-500 font-normal" id="luasKebun"> m²</span></td>
                                    </tr>
                                    <tr class="py-3">
                                      <td class="py-1">
                                      <p class="text-gray-500 font-bold">Komoditi</p>
                                      </td>
                                      <td class="py-1">:</td>
                                      <td class="py-1"><span class="text-gray-500 font-normal" id="komoditi"></span></td>
                                    </tr>
                                    <tr class="py-3">
                                      <td class="py-1">
                                        <p class="text-gray-500 font-bold">Total Blok</p>
                                      </td>
                                      <td class="py-1">:</td>
                                      <td class="py-1"><span class="text-gray-500 font-normal" id="totalBlok"> Blok</span></td>
                                    </tr>
                                    <tr class="py-3">
                                      <td class="py-1">
                                        <p class="text-gray-500 font-bold">Populasi</p>
                                      </td>
                                      <td class="py-1">:</td>
                                      <td class="py-1"><span class="text-gray-500 font-normal" id="populasi"> Tanaman</span></td>
                                    </tr>
                                  </tbody>
                                </table>
                              </div>
                              <div>
                                <h4 class="text-xs md:text-lg font-medium leading-6 text-gray-900 mb-2">Unsur Hara Terbaru</h4>
                                <div class="flex w-full flex-wrap gap-5">
                                  <div>
                                    <div class="font-bold text-gray-500">Nitrogen</div>
                                    <div class="text-gray-500 font-normal" id="telemetry-n"> mg/kg</div>
                                  </div>
                                  <div>
                                    <div class="font-bold text-gray-500">Fosfor</div>
                                    <div class="text-gray-500 font-normal" id="telemetry-f"> mg/kg</div>
                                  </div>
                                  <div>
                                    <div class="font-bold text-gray-500">Kalium</div>
                                    <div class="text-gray-500 font-normal" id="telemetry-k"> mg/kg</div>
                                  </div>
                                  <div>
                                    <div class="font-bold text-gray-500">EC</div>
                                    <div class="text-gray-500 font-normal" id="telemetry-ec"> uS/cm</div>
                                  </div>
                                  <div>
                                    <div class="font-bold text-gray-500">pH Tanah</div>
                                    <div class="text-gray-500 font-normal" id="telemetry-ph"></div>
                                  </div>
                                  <div>
                                    <div class="font-bold text-gray-500">Suhu Tanah</div>
                                    <div class="text-gray-500 font-normal" id="telemetry-t-tanah"><sup>o</sup>C</div>
                                  </div>
                                  <div>
                                    <div class="font-bold text-gray-500">Kelembapan Tanah</div>
                                    <div class="text-gray-500 font-normal" id="telemetry-h-tanah">%</div>
                                  </div>
                                  <div>
                                    <div class="font-bold text-gray-500">Suhu Lingkungan</div>
                                    <div class="text-gray-500 font-normal" id="telemetry-t-dht"><sup>o</sup>C</div>
                                  </div>
                                  <div>
                                    <div class="font-bold text-gray-500">Kelembapan Lingkungan</div>
                                    <div class="text-gray-500 font-normal" id="telemetry-h-dht">%</div>
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
                            const modalData = await gardenModalData(garden.id)
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

            const pickLand = landId => {
                initLandPolygon(landId, map)
            }

            // model garden
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
            // end modal garden

            window.onload = () => {
                console.log('Hello world');

                document.querySelector('#list-gardens').addEventListener('change', async e => {
                    const modalData = await gardenModalData(
                        document.querySelector(
                            '#list-gardens input:checked').value
                    )
                })

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
