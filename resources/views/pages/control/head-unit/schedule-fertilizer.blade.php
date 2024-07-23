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
                                    <div>Pilih Lahan</div>
                                    <div class="col-span-3">
                                        <div class="grid grid-flow-row grid-cols-2 gap-2">
                                            @foreach (\App\Enums\FertilizerScheduleTypeEnums::cases() as $fertilizerScheduleTypeEnum)
                                                <div>
                                                    <input type="radio" id="type-{{ $fertilizerScheduleTypeEnum->value }}" name="type"
                                                        value="{{ $fertilizerScheduleTypeEnum->value }}"
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
                                    <div>Volume (Liter)</div>
                                    <div class="col-span-3">
                                        <div class="grid grid-flow-row grid-cols-2 gap-2">
                                            <input type="number" min="0" step=".01" name="volume"
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
                                <div class="grid grid-flow-row grid-cols-4">
                                    <div>Tanggal Pelaksanaan</div>
                                    <div class="col-span-3">
                                        <input type="hidden" name="execute_date">
                                        <div class="py-2 px-4 bg-primary text-white flex flex-row justify-between">
                                            <button type="button" onclick="subMonth()"><i class="fa-solid fa-chevron-left"></i></button>
                                            <div id="month-year-text" ></div>
                                            <button type="button" onclick="addMonth()"><i class="fa-solid fa-chevron-right"></i></button>
                                        </div>
                                        <div id="calendar" class="p-2 bg-white"></div>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div class="flex flex-col gap-2">
                                    <div>
                                        <button type="button" onclick="storeScheduleFertilize()"
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
                                    'July', 'August', 'September', 'October', 'November', 'December'];
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
                        cell.classList.add('py-2', 'border', 'text-center', 'cursor-pointer', 'hover:bg-primary', 'hover:text-white');
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

                calendarEl.innerHTML = ""; // Clear previous calendar
                calendarEl.appendChild(table);
            }

            const storeScheduleFertilize = async () => {
                const gardenId = document.querySelector('input[name="garden_id"]:checked')?.value
                const type = document.querySelector('input[name="type"]:checked')?.value
                const volume = document.querySelector('input[name="volume"]')?.value
                const executeTime = document.querySelector('input[name="execute_time"]')?.value
                const executeDate = document.querySelector('input[name="execute_date"]')?.value
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

                // Generate and display the calendar
                generateCalendar(currentMonth, currentYear);
            }
        </script>
    @endpush
</x-app-layout>
