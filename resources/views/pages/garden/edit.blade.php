<x-app-layout>
  @push('styles')
  <link rel="stylesheet" href="{{ asset('leaflet/leaflet.css') }}">
  <link rel="stylesheet" href="{{ asset('css/extend.css') }}">
  <style>
    #map {
      height: 90vh;
      z-index: 50;
    }
  </style>
  @endpush
  <x-slot name="header">
    <h2 class="leading-tight">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <a href="{{ route('garden.index') }}">Manajemen Kebun</a>
        </li>
        <li class="breadcrumb-item breadcrumb-active">{{ __('Edit Kebun') }}</li>
      </ol>
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="sm:max-w-7x xl:max-w-full mx-auto sm:px-6 lg:px-8">
      <form action="{{ route('garden.update', $garden->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 gap-2">
          <div class="w-full">
            <div id="map" class="rounded-md"></div>
            <input type="hidden" name="polygon" id="polygon" value="{{ json_encode($garden->polygon) }}">
          </div>
          <div class="w-full">
            <div class="p-6 bg-white overflow-hidden shadow-sm sm:rounded-lg flex flex-col gap-y-4">
              <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                <div class="w-full">
                  <x-input-label for="latitude">{{ __('Latitude') }}</x-input-label>
                  <x-text-input id="latitude" class="block mt-1 w-full rounded-xl" type="text" name="latitude" :value="$garden->latitude" required autofocus autocomplete="latitude" />
                  <x-input-error :messages="$errors->get('latitude')" class="mt-2" />
                </div>
                <div class="w-full">
                  <x-input-label for="longitude">{{ __('Longitude') }}</x-input-label>
                  <x-text-input id="longitude" class="block mt-1 w-full rounded-xl" type="text" name="longitude" :value="$garden->longitude" required autofocus autocomplete="longitude" />
                  <x-input-error :messages="$errors->get('longitude')" class="mt-2" />
                </div>
                <div class="w-full">
                  <x-input-label for="altitude">{{ __('Altitude') }}&nbsp;(mdpl)</x-input-label>
                  <x-text-input id="altitude" class="block mt-1 w-full rounded-xl" type="number" step=".01" name="altitude" :value="$garden->altitude" required autofocus autocomplete="altitude" />
                  <x-input-error :messages="$errors->get('altitude')" class="mt-2" />
                </div>
              </div>
              <div class="w-full">
                <x-input-label for="land_id">{{ __('Pilih Lahan') }}</x-input-label>
                <x-select-input id="land_id" class="block mt-1 w-full rounded-xl" name="land_id">
                  <option value="">Pilih Lahan</option>
                  @foreach ($lands as $id => $land)
                  <option value="{{ $id }}" @selected($id==$garden->land_id)>{{ $land }}</option>
                  @endforeach
                </x-select-input>
                <x-input-error :messages="$errors->get('land_id')" class="mt-2" />
              </div>
              <div class="w-full">
                <x-input-label for="name">{{ __('Nama Kebun') }}</x-input-label>
                <x-text-input id="name" class="block mt-1 w-full rounded-xl" type="text" name="name" :value="$garden->name" required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
              </div>
              <div class="w-full">
                <x-input-label for="area">{{ __('Luas Kebun') }}&nbsp;(m²)</x-input-label>
                <x-text-input id="area" class="block mt-1 w-full rounded-xl" type="number" min="0" step=".01" name="area" :value="$garden->area" required autofocus autocomplete="area" />
                <x-input-error :messages="$errors->get('area')" class="mt-2" />
              </div>
              <div class="w-full">
                <x-input-label for="color">{{ __('Warna') }}</x-input-label>
                <x-text-input id="color" class="block mt-1 w-full p-1" type="color" name="color" :value="'#' . $garden->color" required autofocus autocomplete="color" />
                <x-input-error :messages="$errors->get('altitude')" class="mt-2" />
              </div>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                <div class="w-full">
                  <x-input-label for="count_block">{{ __('Blok') }}</x-input-label>
                  <x-text-input id="count_block" class="count_block mt-1 w-full rounded-xl" type="number" step=".01" name="count_block" :value="$garden->count_block" required autofocus autocomplete="count_block" />
                  <x-input-error :messages="$errors->get('count_block')" class="mt-2" />
                </div>
                <div class="w-full">
                  <x-input-label for="population">{{ __('Populasi') }}</x-input-label>
                  <x-text-input id="population" class="population mt-1 w-full rounded-xl" type="number" step=".01" name="population" :value="$garden->population" required autofocus autocomplete="population" />
                  <x-input-error :messages="$errors->get('population')" class="mt-2" />
                </div>
              </div>
              <div class="flex flex-col">
                <div class="w-full flex justify-end">
                  <x-primary-button>
                    {{ __('Simpan') }}
                  </x-primary-button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>

  @push('scripts')
  <script src="{{ asset('leaflet/leaflet.js') }}"></script>
  <script src="{{ asset('js/extend.js') }}"></script>
  <script src="{{ asset('js/map.js') }}"></script>
  <script src="{{ asset('js/api.js') }}"></script>
  <script>
    let stateData = {
      id: '{{ $garden->id }}',
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

    L.control.zoom({
      position: 'topleft'
    }).addTo(map);

    L.control.layers(baseMapOptions, null, {
      position: 'bottomright'
    }).addTo(map)

    const editableLayers = new L.FeatureGroup();
    map.addLayer(editableLayers);

    const optionDraw = {
      position: 'topright',
      draw: {
        polyline: false,
        polygon: {
          allowIntersection: false, // Restricts shapes to simple polygons
          drawError: {
            message: '<strong>Oh snap!<strong> you can\'t draw that!' // Message that will show when intersect
          },
        },
        circle: false, // Turns off this drawing tool
        circlemarker: false, // Turns off this drawing tool
        rectangle: false,
        marker: true
      },
      edit: {
        featureGroup: editableLayers, //REQUIRED!!
        remove: true
      },
    };

    const drawControl = new L.Control.Draw(optionDraw);
    map.addControl(drawControl);

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

    map.on(L.Draw.Event.CREATED, function(e) {
      let type = e.layerType,
        layer = e.layer;

      switch (type) {
        case 'polygon':
          if (currentPolygonLayer) {
            editableLayers.removeLayer(currentPolygonLayer);
          }

          editableLayers.removeLayer(layer);
          stateData.polygon = layer.getLatLngs()[0];

          layer.setStyle({
            color: document.querySelector('#color').value
          })

          fillPolygon(JSON.stringify(stateData.polygon.map((val, _) => [val.lat, val.lng])), 'polygon')
          stateData.layerPolygon = layer
          currentPolygonLayer = layer
          editableLayers.addLayer(currentPolygonLayer);
          break;
        case 'marker':
          if (currentMarkerLayer) {
            editableLayers.removeLayer(currentMarkerLayer);
          }
          stateData.latitude = layer.getLatLng().lat
          stateData.longitude = layer.getLatLng().lng
          fillPosition(layer.getLatLng().lat, layer.getLatLng().lng, 'latitude', 'longitude')
          currentMarkerLayer = layer
          editableLayers.addLayer(currentMarkerLayer);
          break

        default:
          break;
      }

      // if (editableLayers && editableLayers.getLayers().length !== 0) {
      //     editableLayers.clearLayers();
      // }

      let content = getPopupContent(layer);
      if (content !== null) {
        layer.bindPopup(content);
      }

    });

    map.on('draw:edited', function(e) {
      let layers = e.layers;
      layers.eachLayer(function(layer) {
        console.log(layer instanceof L.Marker);
        console.log(layer instanceof L.Polygon);
        if (layer instanceof L.Marker) {
          stateData.latitude = layer.getLatLng().lat
          stateData.longitude = layer.getLatLng().lng
          fillPosition(layer.getLatLng().lat, layer.getLatLng().lng, 'latitude', 'longitude')
        }
        if (layer instanceof L.Polygon) {
          stateData.polygon = layer.getLatLngs()[0];

          fillPolygon(JSON.stringify(stateData.polygon.map((val, _) => [val.lat, val.lng])),
            'polygon')
          stateData.layerPolygon = layer
        }

        let content = getPopupContent(layer);
        if (content !== null) {
          layer.setPopupContent(content);
        }
      });
    });

    map.on('draw:deleted', function(e) {
      stateData.polygon = null
      stateData.layerPolygon = null
    });

    const getLand = async id => {
      const data = await fetchData(
        "{{ route('extra.land.get-land-polygon', 'ID') }}".replace('ID', id), {
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
      const land = await getLand(id)

      if (currentLand.polygonLayer) {
        currentLand.polygonLayer.remove()
      }

      if (!land) {
        return false
      }

      currentLand.polygonLayer = initPolygon(map, land.polygon, {
        dashArray: '10, 10',
        dashOffset: '20',
        color: '#bdbdbd',
      })

      map.fitBounds(currentLand.polygonLayer.getBounds());

      currentGroupGarden.clearLayers()

      land.gardens.forEach(garden => {
        if (garden.id != stateData.id) {
          currentGroupGarden.addLayer(
            L.polygon(garden.polygon, {
              dashArray: '10, 10',
              dashOffset: '20',
              color: '#' + garden.color + "55",
            }).bindPopup(garden.name)
          )
        }
      })

      currentGroupGarden.addTo(map)

      return true
    }

    window.onload = async () => {
      console.log('Hello world');

      stateData.polygon = JSON.parse(document.querySelector('#polygon').value)
      stateData.layerPolygon = initPolygon(map, stateData.polygon)
      stateData.latitude = parseFloat(document.querySelector('#latitude').value)
      stateData.longitude = parseFloat(document.querySelector('#longitude').value)
      stateData.layerPolygon.setStyle({
        color: document.querySelector('#color').value
      })

      map.fitBounds(stateData.layerPolygon.getBounds());
      currentPolygonLayer = stateData.layerPolygon
      currentMarkerLayer = initMarker(map, stateData.latitude, stateData.longitude)
      editableLayers.addLayer(currentPolygonLayer);
      editableLayers.addLayer(currentMarkerLayer);

      initLandPolygon(document.querySelector('#land_id').value, map)

      document.querySelector('#land_id').addEventListener('change', async e => {
        initLandPolygon(e.target.value, map)
      })

      document.querySelector('#color').addEventListener('change', e => {
        currentPolygonLayer.setStyle({
          color: e.target.value
        })
      })
    }
  </script>
  @endpush
</x-app-layout>
