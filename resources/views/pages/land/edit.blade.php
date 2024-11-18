<x-app-layout>
  @push('styles')
  <link rel="stylesheet" href="{{ asset('leaflet/leaflet.css') }}">
  <link rel="stylesheet" href="{{ asset('css/extend.css') }}">
  <style>
    #map {
      height: 50vh;
      z-index: 50;
    }
  </style>
  @endpush
  <x-slot name="header">
    <h2 class="leading-tight">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <a href="{{ route('land.index') }}">Manajemen Lahan</a>
        </li>
        <li class="breadcrumb-item breadcrumb-active">{{ __('Edit Lahan Baru') }}</li>
      </ol>
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="sm:max-w-7x xl:max-w-full mx-auto sm:px-6 lg:px-8">
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <form action="{{ route('land.update', $land->id) }}" method="POST">
          @csrf
          @method('PUT')
          <div class="p-6 flex flex-col gap-2">
            <div class="flex flex-col lg:flex-row max-sm:gap-2 lg:space-x-2">
              <div class="w-full lg:w-1/2 flex-shrink">
                <x-input-label for="name">{{ __('Nama Lahan') }}</x-input-label>
                <x-text-input id="name" class="block mt-1 w-full rounded-xl" type="text" name="name" :value="$land->name" required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
              </div>
              <div class="w-full lg:w-1/2 flex-shrink">
                <x-input-label for="area">{{ __('Luas Lahan') }} (m²)</x-input-label>
                <x-text-input id="area" class="block mt-1 w-full rounded-xl" type="number" min="0" step=".01" name="area" :value="$land->area" required autofocus autocomplete="area" />
                <x-input-error :messages="$errors->get('area')" class="mt-2" />
              </div>
            </div>
            <div class="flex flex-col">
              <div class="w-full">
                <x-input-label for="address">{{ __('Alamat') }}</x-input-label>
                <x-textarea id="address" class="block mt-1 w-full rounded-xl" type="number" min="0" step=".01" name="address" required autofocus autocomplete="address">{{ $land->address }}</x-textarea>
                <x-input-error :messages="$errors->get('address')" class="mt-2" />
              </div>
            </div>
            <div class="flex flex-col lg:flex-row max-sm:gap-2 lg:space-x-2">
              <div class="w-full lg:w-1/3 flex-shrink">
                <x-input-label for="latitude">{{ __('Latitude') }}</x-input-label>
                <x-text-input id="latitude" class="block mt-1 w-full rounded-xl" type="text" name="latitude" :value="$land->latitude" required autofocus autocomplete="latitude" />
                <x-input-error :messages="$errors->get('latitude')" class="mt-2" />
              </div>
              <div class="w-full lg:w-1/3 flex-shrink">
                <x-input-label for="longitude">{{ __('Longitude') }}</x-input-label>
                <x-text-input id="longitude" class="block mt-1 w-full rounded-xl" type="text" name="longitude" :value="$land->longitude" required autofocus autocomplete="longitude" />
                <x-input-error :messages="$errors->get('longitude')" class="mt-2" />
              </div>
              <div class="w-full lg:w-1/3 flex-shrink">
                <x-input-label for="altitude">{{ __('Altitude') }} (mdpl)</x-input-label>
                <x-text-input id="altitude" class="block mt-1 w-full rounded-xl" type="number" step=".01" name="altitude" :value="$land->altitude" required autofocus autocomplete="altitude" />
                <x-input-error :messages="$errors->get('altitude')" class="mt-2" />
              </div>
            </div>
            <div class="flex flex-col">
              <div class="w-full">
                <div id="map" class="rounded-md"></div>
                <input type="hidden" name="polygon" id="polygon" value="{{ json_encode($land->polygon) }}">
                <x-input-error :messages="$errors->get('polygon')" class="mt-2" />
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
        </form>
      </div>
    </div>
  </div>

  @push('scripts')
  <script src="{{ asset('leaflet/leaflet.js') }}"></script>
  <script src="{{ asset('js/extend.js') }}"></script>
  <script src="{{ asset('js/map.js') }}"></script>
  <script>
    let stateData = {
      polygon: null,
      layerPolygon: null,
      latitude: null,
      longitude: null,
    }
    let currentMarkerLayer = null
    let currentPolygonLayer = null
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
        if (layer instanceof L.Marker) {
          stateData.latitude = layer.getLatLng().lat
          stateData.longitude = layer.getLatLng().lng
          fillPosition(layer.getLatLng().lat, layer.getLatLng().lng, 'latitude', 'longitude')
        }
        if (layer instanceof L.Polygon) {
          stateData.polygon = layer.getLatLngs()[0];

          fillPolygon(JSON.stringify(stateData.polygon.map((val, _) => [val.lat, val.lng])), 'polygon')
          stateData.layerPolygon = layer
        }

        let content = getPopupContent(layer);
        if (content !== null) {
          layer.setPopupContent(content);
        }
      });
    });

    map.on('draw:deleted', function(e) {
      let layers = e.layers;
      layers.eachLayer(function(layer) {
        if (layer instanceof L.Marker) {
          stateData.latitude = null
          stateData.longitude = null
          fillPosition("", "", 'latitude', 'longitude')
        }
        if (layer instanceof L.Polygon) {
          stateData.polygon = null
          stateData.layerPolygon = null

          fillPolygon("", 'polygon')
        }
      });
    });

    window.onload = () => {
      console.log('Hello World');

      stateData.polygon = JSON.parse(document.querySelector('#polygon').value)
      stateData.layerPolygon = initPolygon(map, stateData.polygon)
      stateData.latitude = parseFloat(document.querySelector('#latitude').value)
      stateData.longitude = parseFloat(document.querySelector('#longitude').value)

      map.fitBounds(stateData.layerPolygon.getBounds());
      currentPolygonLayer = stateData.layerPolygon
      currentMarkerLayer = initMarker(map, stateData.latitude, stateData.longitude)
      editableLayers.addLayer(currentPolygonLayer);
      editableLayers.addLayer(currentMarkerLayer);
    }
  </script>
  @endpush
</x-app-layout>
