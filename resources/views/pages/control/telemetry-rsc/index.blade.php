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
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
              <div id="map"></div>
          </div>
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-6 flex justify-between">
                  <h1 class="text-3xl font-extrabold">Tabel Data RSC</h1>
                  <div>
                    <input type="text" placeholder="Search" aria-placeholder="Search"
                      class="rounded-full">
                    <button type="button" class="bg-primary text-white px-4 py-2 rounded-lg font-bold">Export Data</button>
                  </div>
              </div>
              <table class="w-full align-middle border-slate-400 table mb-0">
                  <thead>
                      <tr>
                          <th>Waktu</th>
                          <th>Kebun</th>
                          <th>Nitrogen</th>
                          <th>Phospor</th>
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
                    <tr>
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
                    </tr>
                      {{-- @forelse ($deviceTelemetries as $deviceTelemetry)
                          <tr>
                              <td>{{ number_format($deviceTelemetry->telemetry->, 2) }}</td>
                              <td>0</td>
                          </tr>
                      @empty
                          <tr>
                              <td colspan="6" class="text-center">Tidak ada data</td>
                          </tr>
                      @endforelse --}}
                  </tbody>
              </table>
              {{-- @if ($deviceTelemetries->hasPages())
                  <div class="p-6">
                      {{ $deviceTelemetries->links() }}
                  </div>
              @endif --}}
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
          }
      </script>
  @endpush
</x-app-layout>
