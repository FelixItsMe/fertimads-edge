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
                  <div>Pilih Lahan</div>
                  <div class="col-span-3 grid grid-flow-row grid-cols-2 gap-2">
                    @foreach ($lands as $id => $landName)
                    <div>
                      <input type="radio" id="land-{{ $id }}" name="land_id" onchange="pickLand({{ $id }})" value="{{ $id }}" class="hidden output-type peer/penyiraman" />
                      <label for="land-{{ $id }}" class="inline-flex w-full px-4 py-2 bg-white rounded-md text-xs cursor-pointer dark:hover:text-gray-300 dark:border-gray-700 dark:peer-checked/penyiraman:text-blue-500 peer-checked/penyiraman:bg-primary peer-checked/penyiraman:text-white hover:text-gray-600 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700">
                        {{ $landName }}
                      </label>
                    </div>
                    @endforeach
                  </div>
                </div>
                <div class="grid grid-flow-row grid-cols-4">
                  <div>Pilih Kebun</div>
                  <div class="col-span-3 grid grid-flow-row grid-cols-2 gap-2" id="list-gardens">
                    <button type="button" class="bg-white rounded-md px-4 py-2 text-xs text-left" disabled>Pilih Lahan</button>
                  </div>
                </div>
                <div class="grid grid-flow-row grid-cols-4">
                  <div>Pilih Output</div>
                  <div class="col-span-3 grid grid-flow-row grid-cols-2 gap-2">
                    <div>
                      <input type="radio" id="type-penyiraman" name="type" value="penyiraman" class="hidden output-type peer/penyiraman" />
                      <label for="type-penyiraman" class="inline-flex w-full px-4 py-2 bg-white rounded-md text-xs cursor-pointer dark:hover:text-gray-300 dark:border-gray-700 dark:peer-checked/penyiraman:text-blue-500 peer-checked/penyiraman:bg-primary peer-checked/penyiraman:text-white hover:text-gray-600 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700">
                        Penyiraman
                      </label>
                    </div>
                    <div>
                      <input type="radio" id="type-pemupukan-n" name="type" value="pemupukanN" class="hidden output-type peer/pemupukann" />
                      <label for="type-pemupukan-n" class="inline-flex w-full px-4 py-2 bg-white rounded-md text-xs cursor-pointer dark:hover:text-gray-300 dark:border-gray-700 dark:peer-checked/pemupukann:text-blue-500 peer-checked/pemupukann:bg-primary peer-checked/pemupukann:text-white hover:text-gray-600 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700">
                        Pemupukan N
                      </label>
                    </div>
                    <div>
                      <input type="radio" id="type-pemupukan-p" name="type" value="pemupukanP" class="hidden output-type peer/pemupukanp" />
                      <label for="type-pemupukan-p" class="inline-flex w-full px-4 py-2 bg-white rounded-md text-xs cursor-pointer dark:hover:text-gray-300 dark:border-gray-700 dark:peer-checked/pemupukanp:text-blue-500 peer-checked/pemupukanp:bg-primary peer-checked/pemupukanp:text-white hover:text-gray-600 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700">
                        Pemupukan P
                      </label>
                    </div>
                    <div>
                      <input type="radio" id="type-pemupukan-k" name="type" value="pemupukanK" class="hidden output-type peer/pemupukank" />
                      <label for="type-pemupukan-k" class="inline-flex w-full px-4 py-2 bg-white rounded-md text-xs cursor-pointer dark:hover:text-gray-300 dark:border-gray-700 dark:peer-checked/pemupukank:text-blue-500 peer-checked/pemupukank:bg-primary peer-checked/pemupukank:text-white hover:text-gray-600 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700">
                        Pemupukan K
                      </label>
                    </div>
                  </div>
                </div>
              </div>
              <div>
                <div class="flex flex-row space-x-2">
                  <button type="button" onclick="storeManual('on')" class="bg-primary text-white font-bold rounded-md px-4 py-2">ON</button>
                  <button type="button" onclick="storeManual('off')" class="bg-white text-primary font-bold rounded-md px-4 py-2">OFF</button>
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
      position: 'bottomleft'
    }).addTo(map);

    L.control.layers(baseMapOptions, null, { position: 'bottomright' }).addTo(map)
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

    const storeManual = async (status) => {
      console.dir(document.querySelector('input[name="type"]:checked'))
      const type = document.querySelector('input[name="type"]:checked')?.value
      const gardenId = document.querySelector('input[name="garden_id"]:checked')?.value
      const data = await fetchData(
        "{{ route('head-unit.manual.store') }}", {
          method: "POST",
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').attributes.content.nodeValue,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            'test': true,
            'type': type,
            'garden_id': gardenId,
            'status': status
          })
        }
      );

      return data
    }

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

    window.onload = () => {
      console.log('Hello world');
    }
  </script>
  @endpush
</x-app-layout>
