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
      {{ __('Data Telemetri SMS') }}
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="sm:max-w-7x xl:max-w-full mx-auto sm:px-6 lg:px-8 flex flex-col gap-4">
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div id="map"></div>
      </div>
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 flex justify-between">
          <h1 class="text-3xl font-extrabold">Tabel Data SMS</h1>
          <div>
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

      <div class="mt-6 flex justify-between">
        <div>
          <div id="export-status">
          </div>
        </div>
        <div>
          <x-secondary-button x-on:click="$dispatch('close')">
            {{ __('Batalkan') }}
          </x-secondary-button>

          <x-primary-button class="ms-3" type="button" x-on:click="exportTelemetry()">
            {{ __('Export Excel') }}
          </x-primary-button>
        </div>
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

    const eExportStatus = document.getElementById('export-status')
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

    map.modalControl = L.control({
      position: 'topright'
    });

    map.modalControl.onAdd = function(map) {
      const div = L.DomUtil.create('div', 'leaflet-control');

      div.innerHTML = `
                  <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:align-middle sm:max-w-2xl sm:w-full hidden" id="garden-detail-modal" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
                    <div class="px-2 pt-2 pb-2 bg-white sm:p-3 sm:pb-4 max-h-48 md:max-h-96 overflow-y-scroll">
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
                                    <tr class="py-3">
                                      <td class="py-1">
                                        <p class="text-gray-500 font-bold">Penyakit</p>
                                      </td>
                                      <td class="py-1">:</td>
                                      <td class="py-1"><span class="text-gray-500 font-normal" id="penyakit"</span></td>
                                    </tr>
                                    <tr class="py-3">
                                      <td class="py-1">
                                        <p class="text-gray-500 font-bold">Hama</p>
                                      </td>
                                      <td class="py-1">:</td>
                                      <td class="py-1"><span class="text-gray-500 font-normal" id="hama"></span></td>
                                    </tr>
                                    <tr class="py-3">
                                      <td class="py-1">
                                        <p class="text-gray-500 font-bold">Jenis Pupuk</p>
                                      </td>
                                      <td class="py-1">:</td>
                                      <td class="py-1"><span class="text-gray-500 font-normal" id="jenis_pupuk"></span></td>
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
          currentGroupGarden.addLayer(
            L.polygon(garden.polygon, {
              color: '#' + garden.color,
            }).on('click', async e => {
              const modalData = await gardenModalData(garden.id)
            })
          )
        })

      })

      map.fitBounds(firstLand.getBounds());

      currentGroupGarden.addTo(map)

      return true
    }

    const pickLand = landId => {
      initLandPolygon(landId, map)
    }

    const exportTelemetry = async () => {
      const eQueryFrom = document.querySelector('input#from'),
        eQueryTo = document.querySelector('input#to'),
        exportUrl = new URL("{{ route('telemetry-rsc.export-excel') }}")

      exportUrl.searchParams.append('from', eQueryFrom.value)
      exportUrl.searchParams.append('to', eQueryTo.value)

      const data = await fetchData(
        exportUrl, {
          method: "GET",
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').attributes.content
              .nodeValue,
            'Accept': 'application/json',
          },
        }
      );

      if (!data) {
        eExportStatus.textContent = ''
        return false
      }

      eExportStatus.textContent = `Export sedang berlangsung!`
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

      if (!data) return false

      document.querySelector('#nama-kebun').textContent = data.garden.name
      document.querySelector('#luasKebun').textContent = data.garden.area + " m²"
      document.querySelector('#komoditi').textContent = data.garden.commodity.name
      document.querySelector('#totalBlok').textContent = data.garden.count_block
      document.querySelector('#populasi').textContent = data.garden.population
      document.querySelector('#penyakit').textContent = data.garden.latest_pest?.disease_name || "-"
      document.querySelector('#hama').textContent = data.garden.latest_pest?.pest_name || "-"
      document.querySelector('#jenis_pupuk').textContent = data.garden.device_selenoid?.device_report?.length > 0 ? data.garden.device_selenoid?.device_report[data.garden.device_selenoid?.device_report?.length - 1].pemupukan_type : '-'

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

            const exportTelemetry = async (btnId) => {
                let eButton = document.getElementById(btnId)

                if (eButton) {
                    // Show loading indication
                    eButton.setAttribute('data-kt-indicator', 'on');

                    // Disable button to avoid multiple click
                    eButton.disabled = true;
                    eButton.classList.replace('bg-primary', 'bg-green-700')
                }


                const eQueryFrom = document.querySelector('input#from'),
                    eQueryTo = document.querySelector('input#to'),
                    exportUrl = new URL("{{ route('telemetry-rsc.export-excel') }}")

                eExportStatus.textContent = `Loading...`

                exportUrl.searchParams.append('from', eQueryFrom.value)
                exportUrl.searchParams.append('to', eQueryTo.value)

                const data = await fetchData(
                    exportUrl, {
                        method: "GET",
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').attributes.content
                                .nodeValue,
                            'Accept': 'application/json',
                        },
                    }
                );

                if (eButton) {
                  eButton.disabled = false
                  eButton.classList.replace('bg-green-700', 'bg-primary')
                }

                if (!data) {
                    eExportStatus.textContent = 'Gagal melakukan export'

                    return false
                }

                eExportStatus.textContent = `Export sedang berlangsung! Harap tunggu...`
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

      return dayDiff;
    }

            window.onload = () => {
                console.log('Hello world');
                window.Echo.private('export-completed.{{ auth()->user()->id }}')
                    .listen('ExportCompletedEvent', (event) => {
                        document.querySelector('#export-link').innerHTML = `Export Selesai... <a href="{{ route('telemetry-rsc.download-excel') }}"
                        target="_blank" class="text-sky-400 hover:text-blue-600 underline">Klik untuk mengunduh!</a>`
        })

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
