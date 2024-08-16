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
                <div class="grid grid-flow-row grid-cols-1 lg:grid-cols-5 gap-2">
                    <div class="flex flex-col md:gap-2 md:pr-12 max-md:mx-2 max-md:space-y-2">
                        <div class="font-bold py-2">Opsi Kendali</div>
                        @include('pages.control.head-unit.links')
                    </div>
                    <div class="lg:col-span-4 flex flex-col gap-2 max-md:mx-2">
                        <div class="py-2"><span class="font-bold">Kendali Perangkat</span></div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="flex flex-col md:gap-2 max-md:space-y-2">
                                <div class="grid grid-flow-row grid-cols-4">
                                    <div class="max-md:col-span-4">Pilih Output</div>
                                    <div class="col-span-4 md:col-span-3">
                                        <div class="grid grid-flow-row grid-cols-2 gap-2">
                                            <a href="{{ route('head-unit.schedule-water.index') }}"
                                                @class([
                                                    'bg-white' => !request()->routeIs('head-unit.schedule-water.index'),
                                                    'bg-primary' => request()->routeIs('head-unit.schedule-water.index'),
                                                    'text-white' => request()->routeIs('head-unit.schedule-water.index'),
                                                    'rounded-md',
                                                    'px-4',
                                                    'py-2',
                                                    'text-xs',
                                                ])>Penyiraman</a>
                                            <a href="#" @class([
                                                'bg-white' => !request()->routeIs('head-unit.schedule-fertilizer.index'),
                                                'bg-primary' => request()->routeIs('head-unit.schedule-fertilizer.index'),
                                                'text-white' => request()->routeIs('head-unit.schedule-fertilizer.index'),
                                                'rounded-md',
                                                'px-4',
                                                'py-2',
                                                'text-xs',
                                            ])>Pemupukan</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="grid grid-flow-row grid-cols-4">
                                    <div class="max-md:col-span-4">Pilih Pupuk</div>
                                    <div class="col-span-4 md:col-span-3">
                                        <div class="grid grid-flow-row grid-cols-2 gap-2">
                                            @foreach (\App\Enums\FertilizerScheduleTypeEnums::cases() as $fertilizerScheduleTypeEnum)
                                                <div>
                                                    <input type="radio"
                                                        id="type-{{ $fertilizerScheduleTypeEnum->value }}"
                                                        name="type" value="{{ $fertilizerScheduleTypeEnum->value }}"
                                                        class="hidden output-type peer/type" />
                                                    <label for="type-{{ $fertilizerScheduleTypeEnum->value }}"
                                                        class="inline-flex w-full px-4 py-2 bg-white rounded-md text-xs cursor-pointer dark:hover:text-gray-300 dark:border-gray-700 dark:peer-checked/type:text-blue-500 peer-checked/type:bg-primary peer-checked/type:text-white hover:text-gray-600 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700">
                                                        {{ $fertilizerScheduleTypeEnum->getLabelText() }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="grid grid-flow-row grid-cols-4">
                                    <div class="max-md:col-span-4">Pilih Lahan</div>
                                    <div class="col-span-4 md:col-span-3">
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
                                    <div class="max-md:col-span-4">Pilih Kebun</div>
                                    <div class="col-span-4 md:col-span-3">
                                        <div class="grid grid-flow-row grid-cols-2 gap-2" id="list-gardens">
                                            <button type="button"
                                                class="bg-white rounded-md px-4 py-2 text-xs text-left" disabled>Pilih
                                                Lahan</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="grid grid-flow-row grid-cols-4">
                                    <div class="max-md:col-span-4">Volume (Liter)</div>
                                    <div class="col-span-4 md:col-span-3">
                                        <div class="grid grid-flow-row grid-cols-2 gap-2">
                                            <input type="number" min="0" step=".01" name="volume"
                                                class="bg-white rounded-md text-xs py-2 px-4 border-none">
                                        </div>
                                    </div>
                                </div>
                                <div class="grid grid-flow-row grid-cols-4">
                                    <div class="max-md:col-span-4">Waktu Pelaksanaan</div>
                                    <div class="col-span-4 md:col-span-3">
                                        <div class="grid grid-flow-row grid-cols-2 gap-2">
                                            <input type="time" name="execute_time"
                                                class="bg-white rounded-md text-xs py-2 px-4 border-none">
                                        </div>
                                    </div>
                                </div>
                                <div class="grid grid-flow-row grid-cols-4">
                                    <div class="max-md:col-span-4">Tanggal Pelaksanaan</div>
                                    <div class="col-span-4 md:col-span-3">
                                        <input type="hidden" name="execute_date">
                                        <div class="py-2 px-4 bg-primary text-white flex flex-row justify-between">
                                            <button type="button" onclick="subMonth()"><i
                                                    class="fa-solid fa-chevron-left"></i></button>
                                            <div id="month-year-text"></div>
                                            <button type="button" onclick="addMonth()"><i
                                                    class="fa-solid fa-chevron-right"></i></button>
                                        </div>
                                        <div id="calendar" class="p-2 bg-white"></div>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div class="flex flex-col gap-2">
                                    <div>
                                        <button type="button" onclick="storeScheduleFertilize()"
                                            class="bg-primary text-white font-bold rounded-md px-4 py-2 max-md:w-full">Kirim</button>
                                    </div>
                                    <div class="bg-sky-500 text-white w-full p-6 sm:rounded-lg flex items-center hidden"
                                        id="info-body">
                                        <i class="fa-solid fa-circle-info text-3xl mr-3"></i>&nbsp;<span
                                            id="info-text"></span>
                                    </div>
                                    <div>
                                        <h3>Jadwal Pemupukan</h3>
                                        <div class="grid grid-cols-1 gap-2" id="list-schedule">
                                          <div class="bg-white py-2 px-4 rounded-md grid grid-cols-1 md:grid-cols-5 md:gap-2 max-md:space-y-2 justify-between items-center">
                                            <div class="col-span-5">Pilih tanggal di kalendar</div>
                                          </div>
                                        </div>
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
            const inputExecuteDate = document.querySelector('[name="execute_date"]')
            const eInfoBody = document.querySelector('#info-body')
            const eInfoText = document.querySelector('#info-text')
            const eListSchedule = document.querySelector('#list-schedule')

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

            const calendarEl = document.getElementById('calendar');

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
                            cell.setAttribute('data-date', `${year}-${(month + 1).toString().padStart(2, "0")}-${currentDay.toString().padStart(2, "0")}`);

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

                calendarEl.innerHTML = ""; // Clear previous calendar
                calendarEl.appendChild(table);
            }

            const storeScheduleFertilize = async () => {
                const gardenId = document.querySelector('input[name="garden_id"]:checked')?.value
                const type = document.querySelector('input[name="type"]:checked')?.value
                const volume = document.querySelector('input[name="volume"]')?.value
                const executeTime = document.querySelector('input[name="execute_time"]')?.value
                const executeDate = document.querySelector('input[name="execute_date"]')?.value

                showLoading()

                const data = await fetchData(
                    "{{ route('head-unit.schedule-fertilizer.store') }}", {
                        method: "POST",
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').attributes.content
                                .nodeValue,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            'garden_id': gardenId,
                            'type': type,
                            'volume': volume,
                            'execute_time': executeTime,
                            'execute_date': executeDate,
                        })
                    }
                );

                hideLoading()

                eInfo(
                  true,
                  data
                    ? 'Jadwal Pemupukan Berhasil disimpan!'
                    : 'Gagal disimpan!'
                );

                showLoadingListSchedule()

                const schedules = await fetchActiveSchedule(inputExecuteDate.value)

                initListSchedule(schedules)
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

            const selectDate = async (e) => {
                const classes = ['bg-primary', 'text-white', 'active']

                document.querySelector('#calendar td.active')?.classList.remove(...classes)

                e.classList.add(...classes)

                inputExecuteDate.value = e.dataset.date

                showLoadingListSchedule()

                const schedules = await fetchActiveSchedule(e.dataset.date)

                initListSchedule(schedules)
            }

            const fetchActiveSchedule = async date => {
                const url = new URL("{{ route('extra.schedule.fertilizer.active') }}")
                url.searchParams.append('date', date)
                const data = await fetchData(
                    url,
                    {
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

            const showLoadingListSchedule = () => {
              eListSchedule.innerHTML = `
                  <div class="bg-white py-2 px-4 rounded-md">
                      Loading...
                  </div>
                `
            }

            const initListSchedule = schedules => {
              if (schedules.length == 0) {
                eListSchedule.innerHTML = `
                  <div class="bg-white py-2 px-4 rounded-md grid grid-cols-1 md:grid-cols-5 md:gap-2 max-md:space-y-2 justify-between items-center">
                    <div class="col-span-5 text-center">Tidak Ada</div>
                  </div>`

                return false
              }

              let eSchedules = ``

              schedules.forEach(schedule => {
                eSchedules += `
                  <div class="bg-white py-2 px-4 rounded-md grid grid-cols-1 md:grid-cols-5 md:gap-2 max-md:space-y-2 justify-between items-center">
                    <span class="col-span-4 break-words">${schedule.execute_start.split(' ')[1]}-${schedule.execute_end.split(' ')[1]}|${schedule.device_selenoid.device.series}|${schedule.garden.name}|${fertilizeType(schedule.type)} ${schedule.total_volume} Ltr</span>
                    <button class="bg-red-500 text-white font-semibold py-2 px-4 rounded-md w-full" onclick="destroySchedules(${schedule.id})">Hapus</button>
                  </div>
                `
              });

              eListSchedule.innerHTML = eSchedules
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

            const fertilizeType = type => {
              switch (type) {
                case 1:
                  return 'N'
                  break;
                case 2:
                  return 'P'
                  break;
                case 3:
                  return 'K'
                  break;

                default:
                  return ''
                  break;
              }
            }

            const destroySchedules = async id => {
              showLoadingListSchedule()

              const data = await fetchData(
                    "{{ route('head-unit.schedule-fertilizer.destroy', 'ID') }}".replace('ID', id),
                    {
                        method: "DELETE",
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').attributes.content
                                .nodeValue,
                            'Accept': 'application/json',
                        },
                    }
                );

                const schedules = await fetchActiveSchedule(inputExecuteDate.value)

                eInfo(true, 'Berhasil dihapus');

                initListSchedule(schedules)
            }

            const eInfo = (show = false, message = '') => {
                if (!show) {
                  eInfoBody.classList.add('hidden')
                  return false
                }

                eInfoText.textContent = message

                eInfoBody.classList.remove('hidden')

                setTimeout(() => {
                  eInfoBody.classList.add('hidden')
                }, 3000);
            }

            window.onload = () => {
                console.log('Hello world');

                // Generate and display the calendar
                generateCalendar(currentMonth, currentYear);
            }
        </script>
    @endpush
</x-app-layout>
