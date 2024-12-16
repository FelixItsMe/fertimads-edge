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
                    <a href="{{ route('water-pipeline.index') }}">Manajemen Jalur Pipa Air</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('water-pipeline.show', $waterPipeline->id) }}">Detail Jalur Pipa Air
                        {{ $waterPipeline->name }}</a>
                </li>
                <li class="breadcrumb-item breadcrumb-active">{{ __('Edit Jalur Pipa Air') }}</li>
            </ol>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="sm:max-w-7x xl:max-w-full mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('water-pipeline.update', $waterPipeline->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 gap-2">
                    <div class="w-full">
                        <div id="map" class="rounded-md"></div>
                        <input type="hidden" name="polyline" id="polyline"
                            value="{{ json_encode($waterPipeline->polyline) }}">
                        <x-input-error :messages="$errors->get('polyline')" class="mt-2" />
                    </div>
                    <div class="w-full">
                        <div class="p-6 bg-white overflow-hidden shadow-sm sm:rounded-lg flex flex-col gap-y-4">
                            <div class="w-full">
                                <x-input-label for="name">{{ __('Nama Jalur Pipa Air') }}</x-input-label>
                                <x-text-input id="name" class="block mt-1 w-full rounded-xl" type="text"
                                    name="name" :value="old('name') ?? $waterPipeline->name" required autofocus autocomplete="name" />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>
                            <div class="w-full">
                                <x-input-label for="description">{{ __('Deskripsi') }}</x-input-label>
                                <x-textarea id="description" class="block mt-1 w-full rounded-xl" type="number"
                                    min="0" step=".01" name="description" required autofocus
                                    autocomplete="description">{{ old('description') ?? $waterPipeline->description }}</x-textarea>
                                <x-input-error :messages="$errors->get('description')" class="mt-2" />
                            </div>
                            <div class="w-full flex justify-end">
                                <x-primary-button>
                                    {{ __('Simpan') }}
                                </x-primary-button>
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
        <script>
            let stateData = {
                polygon: null,
                polyline: null,
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
                    polyline: true,
                    polygon: false,
                    circle: false, // Turns off this drawing tool
                    circlemarker: false, // Turns off this drawing tool
                    rectangle: false,
                    marker: false
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
                    const ll = layer._defaultShape ? layer._defaultShape() : layer.getLatLngs()
                    let distance = 0
                    if (ll.length < 2) {
                        return "Distance: N/A";
                    } else {
                        for (let i = 0; i < ll.length - 1; i++) {
                            distance += ll[i].distanceTo(ll[i + 1]);
                        }
                        return "Distance: " + distance.toFixed(2) + " m";
                    }
                }
                return null;
            };

            map.on(L.Draw.Event.CREATED, function(e) {
                let type = e.layerType,
                    layer = e.layer;

                if (currentPolygonLayer) {
                    editableLayers.removeLayer(currentPolygonLayer);
                }
                editableLayers.removeLayer(layer);

                layer.setStyle({color: 'red'})

                stateData.polyline = layer.getLatLngs()

                currentPolygonLayer = layer

                editableLayers.addLayer(currentPolygonLayer);

                document.querySelector('#polyline').value = JSON.stringify(stateData.polyline.map((val, _) => [val.lat,
                    val.lng
                ]))

                let content = getPopupContent(layer);
                if (content !== null) {
                    layer.bindPopup(content);
                }

            });

            map.on('draw:edited', function(e) {
                let layers = e.layers;
                layers.eachLayer(function(layer) {
                    console.log(layer instanceof L.Polyline);
                    if (layer instanceof L.Polyline) {
                        stateData.polyline = layer.getLatLngs()

                        document.querySelector('#polyline').value = JSON.stringify(stateData.polyline.map((val,
                            _) => [val.lat, val.lng]))
                    }

                    let content = getPopupContent(layer);
                    if (content !== null) {
                        layer.setPopupContent(content);
                    }
                });
            });

            map.on('draw:deleted', function(e) {
                stateData.polyline = null
                document.querySelector('#polyline').value = ""
            });

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

            window.onload = () => {
                console.log('Hello world');

                stateData.polyline = JSON.parse(document.querySelector('#polyline').value)
                currentPolygonLayer = L.polyline(stateData.polyline, {
                    color: 'red'
                }).addTo(map);
                editableLayers.addLayer(currentPolygonLayer);

                setTimeout(() => {
                  currentPolygonLayer.bringToFront()
                }, 1000);

                map.fitBounds(currentPolygonLayer.getBounds());
            }
        </script>
    @endpush
</x-app-layout>
