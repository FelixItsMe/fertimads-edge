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
                                    <div>Pilih Output</div>
                                    <div class="col-span-3">
                                        <div class="grid grid-flow-row grid-cols-2 gap-2">
                                            <a href="#" @class([
                                                'bg-white' => !request()->routeIs('head-unit.schedule-water.index'),
                                                'bg-primary' => request()->routeIs('head-unit.schedule-water.index'),
                                                'text-white' => request()->routeIs('head-unit.schedule-water.index'),
                                                'rounded-md',
                                                'px-4',
                                                'py-2',
                                                'text-xs',
                                            ])>Penyiraman</a>
                                            <a href="{{ route('head-unit.schedule-fertilizer.index') }}"
                                                @class([
                                                    'bg-white' => !request()->routeIs('head-unit.manual.index'),
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
                                    <div>Tanggal Mulai</div>
                                    <div class="col-span-3">
                                        <div class="grid grid-flow-row grid-cols-2 gap-2">
                                            <input type="date" name="start_date"
                                                class="bg-white rounded-md text-xs py-2 px-4 border-none">
                                        </div>
                                    </div>
                                </div>
                                <div class="grid grid-flow-row grid-cols-4">
                                    <div>Umur Komoditi (Hari)</div>
                                    <div class="col-span-3">
                                        <div class="grid grid-flow-row grid-cols-2 gap-2">
                                            <input type="number" min="0" name="commodity_age"
                                                class="bg-white rounded-md text-xs py-2 px-4 border-none">
                                        </div>
                                    </div>
                                </div>
                                <div class="grid grid-flow-row grid-cols-4">
                                    <div>Waktu Pelaksanaan</div>
                                    <div class="col-span-3">
                                        <div class="grid grid-flow-row grid-cols-2 gap-2">
                                            <input type="time" name="execute_time"
                                                class="bg-white rounded-md text-xs py-2 px-4 border-none">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div class="flex flex-col gap-2">
                                    <div>
                                        <button type="button" onclick="storeScheduleWater()"
                                            class="bg-primary text-white font-bold rounded-md px-4 py-2">Kirim</button>
                                    </div>
                                    <div class="bg-sky-500 text-white w-full p-6 sm:rounded-lg flex items-center hidden"
                                        id="info-body">
                                        <i class="fa-solid fa-circle-info text-3xl mr-3"></i>&nbsp;<span
                                            id="info-text"></span>
                                    </div>
                                    <div id="list-active-schedule">
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
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').attributes.content
                                .nodeValue,
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

                const modalData = await gardenModalData(document.querySelector(
                    '#list-gardens input:checked').value)
                openModal(map, modalData);
                generateCalendar(currentMonth, currentYear);

                const data2 = await getActiveWaterSchedule(document.querySelector(
                    '#list-gardens input:checked').value)

                eListWaterSchedules(data2.activeWaterSchedules)
            }

            const stopWaterSchedule = async id => {
                const data = await fetchData(
                    "{{ route('head-unit.schedule-water.stop', 'ID') }}".replace('ID', id), {
                        method: "PUT",
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').attributes.content
                                .nodeValue,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                    }
                );

                if (!data) {
                    document.querySelector('#info-text').textContent = 'Gagal'
                } else {
                    document.querySelector('#info-text').textContent = 'Berhasil menghentikan jadwal yang dipilih!'
                }

                document.querySelector('#info-body').classList.remove('hidden')

                const data2 = await getActiveWaterSchedule(document.querySelector(
                    '#list-gardens input:checked').value)

                eListWaterSchedules(data2.activeWaterSchedules)
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
                                ${garden.name} <br/>(Selenoid ${garden.device_selenoid.selenoid})
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


            const openModal = (map, modalData) => {
                console.log(modalData);

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
                                          <td class="pb-1">
                                            <p class="text-gray-500 font-bold">Kegiatan</p>
                                          </td>
                                          <td class="pb-1">:</td>
                                          <td class="pb-1"><span class="text-gray-500 font-normal" id="jenisKegiatan">Penyiraman</span></td>
                                        </tr>
                                        <tr class="py-3">
                                          <td class="py-1">
                                            <p class="text-gray-500 font-bold">Volume</p>
                                          </td>
                                          <td class="py-1">:</td>
                                          <td class="py-1"><span class="text-gray-500 font-normal" id="volume">50 Liter Air</span></td>
                                        </tr>
                                        <tr class="py-3">
                                          <td class="py-1">
                                            <p class="text-gray-500 font-bold">Total Waktu</p>
                                          </td>
                                          <td class="py-1">:</td>
                                          <td class="py-1"><span class="text-gray-500 font-normal" id="waktuKegiatan">30 Menit Penyiraman</span></td>
                                        </tr>
                                        <tr class="py-3">
                                          <td class="py-1">
                                          <p class="text-gray-500 font-bold">Waktu Penjadwalan</p>
                                          </td>
                                          <td class="py-1">:</td>
                                          <td class="py-1">
                                            <div class="text-gray-500 font-normal" id="waktuPenjadwalan">Senin, 18 Mei 2024</div>
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

            const getActiveWaterSchedule = async id => {
                const data = await fetchData(
                    "{{ route('extra.garden.active-water-schedule', 'ID') }}".replace('ID', id), {
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

            const eListWaterSchedules = listWaterSchedule => {
                const pListSchedule = document.querySelector('#list-active-schedule')
                pListSchedule.innerHTML = ``

                listWaterSchedule.forEach(waterSchedule => {
                    const divSchedule = document.createElement('div')
                    divSchedule.classList.add('bg-white', 'py-2', 'px-4', 'rounded-lg', 'flex', 'justify-between',
                        'items-center')
                    const textSchedule = document.createElement('div')
                    textSchedule.textContent =
                        `${waterSchedule.commodity.name} | ${waterSchedule.start_date} - ${waterSchedule.end_date}`

                    const btnDelete = document.createElement('button')
                    btnDelete.setAttribute('type', 'button')
                    btnDelete.classList.add('bg-red-500', 'py-2', 'px-4', 'text-white', 'rounded-md')
                    btnDelete.textContent = 'Hapus'
                    btnDelete.onclick = e => {
                        stopWaterSchedule(waterSchedule.id)
                    }
                    divSchedule.appendChild(textSchedule)
                    divSchedule.appendChild(btnDelete)

                    pListSchedule.appendChild(divSchedule)
                });
            }

            window.onload = () => {
                console.log('Hello world');

                document.querySelector('#list-gardens').addEventListener('change', async e => {
                    const modalData = await gardenModalData(document.querySelector(
                        '#list-gardens input:checked').value)
                    const data = await getActiveWaterSchedule(document.querySelector(
                        '#list-gardens input:checked').value)

                    eListWaterSchedules(data.activeWaterSchedules)
                    openModal(map, modalData);
                    generateCalendar(currentMonth, currentYear);
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

                bmkgWether(weatherElements)

                setInterval(() => {
                    bmkgWether(weatherElements)
                }, 1000 * 10);
            }
        </script>
    @endpush
</x-app-layout>
