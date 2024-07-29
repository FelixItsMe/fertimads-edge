<x-app-layout>
  @push('styles')
  <link rel="stylesheet" href="{{ asset('leaflet/leaflet.css') }}">
  <link rel="stylesheet" href="{{ asset('css/extend.css') }}">
  <style>
    #map {
      height: 90vh;
      font-family: 'Figtree', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
    }

    .modal-header {
      font-size: 18px;
      font-weight: bold;
      margin-bottom: 10px;
    }

    .modal-body {
      margin-bottom: 10px;
    }

    .modal-footer {
      text-align: right;
    }

    .modal-footer button {
      margin-left: 5px;
    }
  </style>
  @endpush

  <x-slot name="header">
    <h2 class="leading-tight">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <a href="#">Pages</a>
        </li>
        <li class="breadcrumb-item breadcrumb-active">{{ __('RSC Data') }}</li>
      </ol>
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="mx-auto sm:px-6 lg:px-8">
      <div class="grid grid-flow-row grid-cols-1 gap-2">
        <div>
          <div id="map" class="rounded-md"></div>
        </div>
      </div>
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-5">
        <div class="p-6 flex justify-between">
          <h1 class="text-3xl font-extrabold">Tabel Data RSC</h1>
          <a href="{{ route('pest.create') }}" class="bg-fertimads-2 text-white py-1.5 px-5 rounded-md">Tambah Data</a>
        </div>
        <table class="w-full border-slate-400 table mb-0 text-left">
          <thead>
            <tr>
              <th>Waktu</th>
              <th>Kebun</th>
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
            @forelse ([] as $pest)
            <tr>
            </tr>
            @empty
            <tr>
              <td colspan="12" class="text-center">Tidak ada data</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  @push('scripts')
  <script src="{{ asset('js/jquery.js') }}"></script>
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

    const map = L.map('map', {
        preferCanvas: true,
        layers: [googleStreetsSecond],
        zoomControl: false
      })
      .setView([-6.869080223722067, 107.72491693496704], 12);

    L.control.zoom({
      position: 'topright'
    }).addTo(map);

    const getPopupContent = function(layer) {
      if (layer instanceof L.Marker || layer instanceof L.CircleMarker) {
        return layer.getLatLng().lat + " " + layer.getLatLng().lng;
      } else if (layer instanceof L.Circle) {
        const center = layer.getLatLng(),
          radius = layer.getRadius();
        return "Center: " + strLatLng(center) + "<br />" +
          "Radius: " + _round(radius, 2) + " m";
      } else if (layer instanceof L.Polygon) {
        const ll = layer._defaultShape ? layer._defaultShape() : layer.getLatLngs(),
          area = L.GeometryUtil.geodesicArea(ll);
        luasPolygon = area.toLocaleString();
        document.querySelector('#area').value = area.toFixed(2)
        return "Area: " + L.GeometryUtil.readableArea(area, true);
      } else if (layer instanceof L.Polyline) {
        const ll = layer._defaultShape ? layer._defaultShape() : layer.getLatLngs(),
          distance = 0;
        if (ll.length < 2) {
          return "Distance: N/A";
        } else {
          for (const i = 0; i < ll.length - 1; i++) {
            distance += ll[i].distanceTo(ll[i + 1]);
          }
          return "Distance: " + _round(distance, 2) + " m";
        }
      }
      return null;
    };

    const getLands = async () => {
      const data = await fetchData(
        "{{ route('extra.land.polygon.garden') }}", {
          method: "GET",
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').attributes.content.nodeValue,
            'Accept': 'application/json',
          },
        }
      );

      return data?.lands
    }

    const getLatestTelemetry = async (gardenId) => {
      const data = await fetchData(
        "{{ route('extra.garden.latest-telemetry', 'ID') }}".replace('ID', gardenId), {
          method: "GET",
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').attributes.content.nodeValue,
            'Accept': 'application/json',
          }
        })

      return data;
    }

    const getGardenSchedule = async (gardenId, currentMonth = new Date().getMonth(), currentYear = new Date().getFullYear()) => {
      const data = await fetchData(
        "{{ route('extra.activity-schedule.schedule-in-month', ['month' => 'MONTH', 'year' => 'YEAR', 'garden_id' => 'ID']) }}"
        .replace('MONTH', currentMonth)
        .replace('YEAR', currentYear)
        .replace('ID', gardenId), {
          method: "GET",
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').attributes.content.nodeValue,
            'Accept': 'application/json',
          }
        })

      return data;
    }

    const getGardenScheduleDetail = async (gardenId, currentDate) => {
      const data = await fetchData(
        "{{ route('extra.activity-schedule.date', ['date' => 'DATE', 'garden_id' => 'ID']) }}"
        .replace('DATE', currentDate)
        .replace('ID', gardenId), {
          method: "GET",
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').attributes.content.nodeValue,
            'Accept': 'application/json',
          }
        })

      return data;
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
                        <h4 class="text-lg font-medium leading-6 text-gray-900 mb-2">Informasi Kebun ${garden.name}</h4>
                        <table class="w-full">
                          <tbody>
                            <tr class="py-3">
                              <td class="pb-1">
                                <p class="text-gray-500 font-bold">Luas Kebun</p>
                              </td>
                              <td class="pb-1">:</td>
                              <td class="pb-1"><span class="text-gray-500 font-normal" id="luasKebun">${garden.area} m²</span></td>
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
                              <td class="py-1"><span class="text-gray-500 font-normal" id="komoditi">${garden.commodity.name}</span></td>
                            </tr>
                            <tr class="py-3">
                              <td class="py-1">
                                <p class="text-gray-500 font-bold">Total Blok</p>
                              </td>
                              <td class="py-1">:</td>
                              <td class="py-1"><span class="text-gray-500 font-normal" id="totalBlok">${garden.count_block} Blok</span></td>
                            </tr>
                            <tr class="py-3">
                              <td class="py-1">
                                <p class="text-gray-500 font-bold">Populasi</p>
                              </td>
                              <td class="py-1">:</td>
                              <td class="py-1"><span class="text-gray-500 font-normal" id="populasi">${garden.populations || '-'} Tanaman</span></td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                      <div>
                        <h4 class="text-lg font-medium leading-6 text-gray-900 mb-2">Unsur Hara Terbaru</h4>
                        <div class="flex w-full flex-wrap gap-5">
                          <div>
                            <div class="font-bold text-gray-500">Nitrogen</div>
                            <div class="text-gray-500 font-normal">${latestTelemetry.telemetry.soil_sensor.N} mg/kg</div>
                          </div>
                          <div>
                            <div class="font-bold text-gray-500">Fosfor</div>
                            <div class="text-gray-500 font-normal">${latestTelemetry.telemetry.soil_sensor.P} mg/kg</div>
                          </div>
                          <div>
                            <div class="font-bold text-gray-500">Kalium</div>
                            <div class="text-gray-500 font-normal">${latestTelemetry.telemetry.soil_sensor.K} mg/kg</div>
                          </div>
                          <div>
                            <div class="font-bold text-gray-500">EC</div>
                            <div class="text-gray-500 font-normal">${latestTelemetry.telemetry.soil_sensor.EC} ppm</div>
                          </div>
                          <div>
                            <div class="font-bold text-gray-500">pH Tanah</div>
                            <div class="text-gray-500 font-normal">${latestTelemetry.telemetry.soil_sensor.pH}</div>
                          </div>
                          <div>
                            <div class="font-bold text-gray-500">Suhu Tanah</div>
                            <div class="text-gray-500 font-normal">${latestTelemetry.telemetry.soil_sensor.T}C<sup>o</sup></div>
                          </div>
                          <div>
                            <div class="font-bold text-gray-500">Kelembapan tanah</div>
                            <div class="text-gray-500 font-normal">${latestTelemetry.telemetry.soil_sensor.H}%</div>
                          </div>
                          <div>
                            <div class="font-bold text-gray-500">Suhu Lingkungan</div>
                            <div class="text-gray-500 font-normal">${latestTelemetry.telemetry.dht1.T.toFixed(2)}C<sup>o</sup></div>
                          </div>
                          <div>
                            <div class="font-bold text-gray-500">Kelembapan Lingkungan</div>
                            <div class="text-gray-500 font-normal">${latestTelemetry.telemetry.dht1.H.toFixed(2)}%</div>
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
        L.DomEvent.disableScrollPropagation(div)
        return div;
      };
      map.modalControl.addTo(map);
    };

    const initLandPolygon = async (id, map) => {
      const lands = await getLands()

      if (currentLand.polygonLayer) {
        currentLand.polygonLayer.remove()
      }

      if (!lands) {
        return false
      }

      currentLand.polygonLayer = initPolygon(map, lands[0].polygon, {
        dashArray: '10, 10',
        dashOffset: '20',
        color: '#bdbdbd',
      })

      map.fitBounds(currentLand.polygonLayer.getBounds());

      currentGroupGarden.clearLayers()

      lands.forEach(land => {
        const landLayer = L.polygon(land.polygon, {
          color: '#' + land.color
        });
        currentGroupGarden.addLayer(landLayer);

        // Tambahkan event listener untuk membuka modal
        landLayer.on('click', () => {
          openModal(map, 'Detail', 'Informasi detail tentang polygon ini.');
        });

        land.gardens.forEach(garden => {
          const gardenLayer = L.polygon(garden.polygon, {
            dashArray: '10, 10',
            dashOffset: '20',
            color: '#' + garden.color + "55",
          });
          currentGroupGarden.addLayer(gardenLayer);

          // Tambahkan event listener untuk membuka modal
          gardenLayer.on('click', async () => {
            await openModal(map, garden).then(async () => {
              let today = new Date();
              let currentDate = today.getDate();
              let currentMonth = today.getMonth();
              let currentYear = today.getFullYear();
              let currentFullDate = `${currentYear}-${(currentMonth + 1).toString().padStart(2, "0")}-${currentDate.toString().padStart(2, "0")}`;
              const calendarEl = $(document).find('#calendar')
              let pickedDate = currentFullDate

              const generateCalendar = (month, year, availableSchedules) => {
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
                    cell.classList.add('py-4', 'border', 'text-center', 'relative', 'cursor-pointer', 'hover:bg-primary',
                      'hover:text-white');
                    if (currentDay <= 0 || currentDay > daysInMonth) {
                      cell.classList.add('text-gray-400'); // Grey out days from previous/next month
                    } else {
                      const info = document.createElement('div');
                      const wBar = document.createElement('div');
                      const fBar = document.createElement('div');
                      const formatDate = `${year}-${(month + 1).toString().padStart(2, "0")}-${currentDay.toString().padStart(2, "0")}`
                      cell.textContent = currentDay;
                      cell.setAttribute('data-date', formatDate);

                      if (formatDate === pickedDate) {
                        selectDate(cell)
                      }

                      cell.onclick = (e) => {
                        selectDate(e.target)
                      }

                      if (currentDay == new Date().getDate()) {
                        cell.classList.add('text-blue-500')
                      }

                      const schedule = availableSchedules.find(row => {
                        console.log(row.date)
                        return row.date == formatDate
                      })


                      if (schedule?.schedule?.includes(1)) {
                        wBar.classList.add('bg-primary', 'w-full', 'h-1');
                      }
                      if (schedule?.schedule?.includes(2)) {
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

                calendarEl.empty()
                calendarEl.append(table)
              }

              let schedulesResponse

              const selectDate = async e => {
                console.dir(e.dataset);
                const classes = ['bg-primary', 'text-white', 'active']

                document.querySelector('#calendar td.active')?.classList.remove(...classes)

                e.classList.add(...classes)

                await getGardenScheduleDetail(garden.id, e.dataset.date).then(data => {
                  let jenisKegiatan = data.types.join(" | ")
                  $(document).find('#jenisKegiatan').text(jenisKegiatan)
                })
              }

              $(document).on('click', '#addMonthButton', async function(e) {
                currentMonth++
                if (currentMonth > 11) {
                  currentMonth = 0
                  currentYear++
                }

                await getGardenSchedule(garden.id, currentMonth + 1, currentYear).then(data => {
                  schedulesResponse = data
                })

                generateCalendar(currentMonth, currentYear, schedulesResponse.schedules);
              })

              $(document).on('click', '#subMonthButton', async function(e) {
                currentMonth--
                if (currentMonth < 0) {
                  currentMonth = 11
                  currentYear--
                }

                await getGardenSchedule(garden.id, currentMonth + 1, currentYear).then(data => {
                  schedulesResponse = data
                })

                generateCalendar(currentMonth, currentYear, schedulesResponse.schedules);
              })

              await getGardenSchedule(garden.id, currentMonth + 1).then(data => {
                schedulesResponse = data
              })

              generateCalendar(currentMonth, currentYear, schedulesResponse.schedules)
            });
          });
        });
      })

      currentGroupGarden.addTo(map)

      return true
    }
    window.onload = () => {
      console.log('Hello world');
      initLandPolygon(1, map)
    }
  </script>
  @endpush

</x-app-layout>
