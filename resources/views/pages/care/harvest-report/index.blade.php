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
        <li class="breadcrumb-item breadcrumb-active">{{ __('Laporan Panen') }}</li>
      </ol>
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="mx-auto sm:px-6 lg:px-8">
      <h1 class="text-2xl font-bold">List Rangkuman</h1>

      <div class="mt-5">
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
          <div class="bg-white p-6">
            <div class="flex gap-x-5">
              <div class="flex-1">
                <div class="flex">
                  <div class="w-1/2">
                    <div class="text-sky-600 text-5xl font-bold">87%</div>
                    <div class="text-zinc-500">Hasil Panen baik</div>
                  </div>
                  <div class="">
                    <div class="text-zinc-500">Tanggal Panen</div>
                    <div class="font-bold">{{ now()->format('d M Y') }}</div>
                  </div>
                </div>
                <div class="mt-5 flex justify-between">
                  <div class="w-1/2 flex flex-col space-y-2">
                    <div class="text-teal-400 text-2xl font-bold">Lahan Jonggol</div>
                    <div class="text-zinc-500">Kebun (3A)</div>
                    <div class="text-zinc-500">Singkong</div>
                    <div class="text-zinc-500">50 Populasi</div>
                  </div>
                  <div class="w-1/2 flex flex-col space-y-2 ">
                    <div class="text-teal-400 text-2xl font-bold">Pupuk</div>
                    <div class="text-zinc-500">Pupuk Urea</div>
                    <div class="text-zinc-500">Pupuk Organik</div>
                  </div>
                </div>
                <div class="mt-5 flex justify-between">
                  <div class="w-1/2 flex flex-col space-y-2">
                    <div class="text-teal-400 text-2xl font-bold">Hasil Panen</div>
                    <div class="text-zinc-500">80 Ton</div>
                  </div>
                  <div class="w-1/2 flex flex-col space-y-2 ">
                    <div class="text-teal-400 text-2xl font-bold">Hama</div>
                    <div class="text-zinc-500">Tikus</div>
                  </div>
                </div>
                <div class="mt-5 font-bold text-xl">Laporan Keuangan</div>
                <div class="mt-5 flex justify-between">
                  <div class="w-1/2 flex flex-col space-y-2">
                    <div class="text-teal-400 font-bold">Operasional</div>
                    <div class="text-teal-400 font-bold">Est Harga Jual</div>
                    <div class="text-teal-400 font-bold">Laba Bersih</div>
                  </div>
                  <div class="w-1/2 flex flex-col space-y-2 ">
                    <div class="">Rp. 1,000,000</div>
                    <div class="">Rp. 1,000,000</div>
                    <div class="font-bold">Rp. 1,000,000</div>
                  </div>
                </div>
              </div>
              <div class="flex-1">
                <div class="bg-gray-200 p-6">
                  <div class="text-teal-400 text-2xl font-bold">Rangkuman Unsur Tanah</div>

                  <div class="mt-5">
                    <div class="w-full flex">
                      <div class="w-[225px] text-zinc-500">Kalium</div>
                      <div class="font-bold">7 mg/kg</div>
                    </div>
                    <div class="w-full flex">
                      <div class="w-[225px] text-zinc-500">Nitrogen</div>
                      <div class="font-bold">7 mg/kg</div>
                    </div>
                    <div class="mt-3 w-full flex">
                      <div class="w-[225px] text-zinc-500">Fosfor</div>
                      <div class="font-bold">7 mg/kg</div>
                    </div>
                    <div class="mt-3 w-full flex">
                      <div class="w-[225px] text-zinc-500">pH tanah</div>
                      <div class="font-bold">7</div>
                    </div>
                    <div class="mt-3 w-full flex">
                      <div class="w-[225px] text-zinc-500">Suhu Tanah</div>
                      <div class="font-bold">28<sup>o</sup>C</div>
                    </div>
                    <div class="mt-3 w-full flex">
                      <div class="w-[225px] text-zinc-500">Kelembapan Tanah</div>
                      <div class="font-bold">65%</div>
                    </div>
                    <div class="mt-3 w-full flex">
                      <div class="w-[225px] text-zinc-500">Suhu Lingkungan</div>
                      <div class="font-bold">27<sup>o</sup>C</div>
                    </div>
                    <div class="mt-3 w-full flex">
                      <div class="w-[225px] text-zinc-500">Kelembapan Lingkungan</div>
                      <div class="font-bold">67%</div>
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

    const getPopupContent = function(layer) {
      // Marker - add lat/long
      if (layer instanceof L.Marker || layer instanceof L.CircleMarker) {
        return layer.getLatLng().lat + " " + layer.getLatLng().lng;
        // Circle - lat/long, radius
      } else if (layer instanceof L.Circle) {
        const center = layer.getLatLng(),
          radius = layer.getRadius();
        return "Center: " + strLatLng(center) + "<br />" +
          "Radius: " + _round(radius, 2) + " m";
        // Rectangle/Polygon - area
      } else if (layer instanceof L.Polygon) {
        const ll = layer._defaultShape ? layer._defaultShape() : layer.getLatLngs(),
          area = L.GeometryUtil.geodesicArea(ll);
        luasPolygon = area.toLocaleString();
        document.querySelector('#area').value = area.toFixed(2)
        return "Area: " + L.GeometryUtil.readableArea(area, true);
        // Polyline - distance
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

    const initLandPolygon = async (id, map) => {
      const lands = await getLands()

      if (currentLand.polygonLayer) {
        currentLand.polygonLayer.remove()
      }

      if (!lands) {
        return false
      }

      currentLand.polygonLayer = initPolygon(map, lands[1].polygon, {
        dashArray: '10, 10',
        dashOffset: '20',
        color: '#bdbdbd',
      })

      map.fitBounds(currentLand.polygonLayer.getBounds());

      currentGroupGarden.clearLayers()

      lands.forEach(land => {
        currentGroupGarden.addLayer(L.polygon(land.polygon, {
          color: '#' + land.color
        }))

        land.gardens.forEach(garden => {
          currentGroupGarden.addLayer(L.polygon(garden.polygon, {
            dashArray: '10, 10',
            dashOffset: '20',
            color: '#' + garden.color + "55",
          }))
        })
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
