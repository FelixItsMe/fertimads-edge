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
        <li class="breadcrumb-item">
          <a href="{{ route('map-object.index') }}">Manajemen Objek Peta</a>
        </li>
        <li class="breadcrumb-item breadcrumb-active">{{ __('Edit Objek Peta') }}</li>
      </ol>
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="sm:max-w-7x xl:max-w-full mx-auto sm:px-6 lg:px-8">
      @if ($errors->has('lat') || $errors->has('lng'))
      <div class="bg-red-500 text-white w-full p-6 sm:rounded-lg flex items-center">
        <i class="fa-solid fa-circle-warning text-3xl mr-3"></i> Posisi marker harus diisi!
      </div>
      @endif
      <form action="{{ route('map-object.update', $mapObject->id) }}" method="POST">
        @csrf
        @method('patch')
        <div class="w-full mb-3">
          <div class="p-6 bg-white overflow-hidden shadow-sm sm:rounded-lg grid grid-cols-1 gap-2 w-full">
            <div>
              <x-input-label for="type">{{ __('Pilih Tipe') }}</x-input-label>
              <x-select-input id="type" class="block mt-1 w-full rounded-xl" name="type">
                <option value="">Pilih Tipe</option>
                @foreach ($objectTypes as $id => $type)
                <option value="{{ $id }}" @selected($type === $mapObject->type)>{{ $type }}</option>
                @endforeach
              </x-select-input>
              <x-input-error :messages="$errors->get('type')" class="mt-2" />
            </div>
            <div>
              <x-input-label for="name">{{ __('Nama Objek') }}</x-input-label>
              <x-text-input id="name" class="block mt-1 w-full rounded-xl" type="text" name="name" :value="old('name') ?? $mapObject->name" required autofocus autocomplete="name" />
              <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>
            <div>
              <x-input-label for="description">{{ __('Deskripsi') }}</x-input-label>
              <x-text-input id="description" class="block mt-1 w-full rounded-xl" type="text" name="description" :value="old('description') ?? $mapObject->description" required autofocus autocomplete="description" />
              <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>
            <div class="flex justify-end pb-1">
              <x-primary-button>
                {{ __('Simpan') }}
              </x-primary-button>
            </div>
          </div>
        </div>
        <div class="w-full sm:h-3/4">
          <div id="map" class="rounded-md"></div>
          <input type="hidden" name="polygon" id="polygon">
          <x-text-input id="lat" class="block mt-1 w-full rounded-xl" type="hidden" name="lat" :value="$mapObject->lat" required autofocus autocomplete="lat" />
          <x-text-input id="lng" class="block mt-1 w-full rounded-xl" type="hidden" name="lng" :value="$mapObject->lng" required autofocus autocomplete="lng" />
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
        polygon: false,
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
        case 'marker':
          if (currentMarkerLayer) {
            editableLayers.removeLayer(currentMarkerLayer);
          }
          stateData.latitude = layer.getLatLng().lat
          stateData.longitude = layer.getLatLng().lng
          fillPosition(layer.getLatLng().lat, layer.getLatLng().lng, 'lat', 'lng')
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
          fillPosition(layer.getLatLng().lat, layer.getLatLng().lng, 'lat', 'lng')
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
        currentGroupGarden.addLayer(
          L.polygon(garden.polygon, {
            dashArray: '10, 10',
            dashOffset: '20',
            color: '#' + garden.color + "55",
          }).bindPopup(garden.name)
        )
      })

      currentGroupGarden.addTo(map)

      return true
    }

    window.onload = async () => {
      console.log('Hello world');

      stateData.latitude = parseFloat(document.querySelector('#lat').value)
      stateData.longitude = parseFloat(document.querySelector('#lng').value)

      currentMarkerLayer = initMarker(map, stateData.latitude, stateData.longitude)
      editableLayers.addLayer(currentMarkerLayer);
    }
  </script>
  @endpush
</x-app-layout>
