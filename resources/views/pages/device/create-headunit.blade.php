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
                    <a href="{{ route('device.index') }}">Manajemen Perangkat IoT</a>
                </li>
                <li class="breadcrumb-item breadcrumb-active">{{ __('Tambah Perangkat IoT Baru') }}</li>
            </ol>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="sm:max-w-7x xl:max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <form action="{{ route('device.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="device_type_id" value="{{ request()->query('type') }}">
                    <div class="p-6 flex flex-col gap-2">
                        <div class="grid grid-flow-col sm:grid-flow-row sm:grid-cols-4 gap-2">
                            <div class="w-full">
                                <x-input-label for="name">{{ __('Gambar') }}</x-input-label>
                                <img src="{{ asset('images/default/default-image.jpg') }}" alt="Preview Image"
                                    class="aspect-square object-cover w-full" id="preview-img">
                            </div>
                            <div class="w-full col-span-3">
                                <x-input-label for="image">{{ __('File Gambar') }} <span class="text-danger">*Tidak
                                        wajib</span></x-input-label>
                                <input id="image"
                                    class="block w-full px-3 py-2 text-base font-normal text-gray-700 bg-white border border-solid border-gray-300 rounded-md cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    type="file" accept="image/png, image/jpg, image/jpeg" name="image"
                                    aria-describedby="pictureHelp" />
                                <div id="pictureHelp" class="text-xs text-slate-400 mt-1">Format gambar JPG, JPEG, PNG.
                                    Maks.
                                    2MB</div>
                                <x-input-error :messages="$errors->get('image')" class="mt-2" />
                            </div>
                        </div>
                        <div class="flex flex-col">
                            <div class="w-full">
                                <x-input-label for="series">{{ __('Series') }}</x-input-label>
                                <x-text-input id="series" class="block mt-1 w-full rounded-xl" type="text"
                                    name="series" :value="old('series')" required autofocus autocomplete="series" />
                                <x-input-error :messages="$errors->get('series')" class="mt-2" />
                            </div>
                        </div>
                        <div class="flex flex-col">
                            <div class="w-full">
                                <x-input-label for="debit">{{ __('Debit (Liter)') }}</x-input-label>
                                <x-text-input id="debit" class="block mt-1 w-full rounded-xl" type="number"
                                    min="0" step=".01" name="debit" :value="old('debit')" required autofocus
                                    autocomplete="debit" />
                                <x-input-error :messages="$errors->get('debit')" class="mt-2" />
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            <div class="w-full col-span-2">
                                <div id="map" class="rounded-md"></div>
                            </div>
                            <div class="w-full">
                                <x-input-label for="latitude">{{ __('Latitude') }}</x-input-label>
                                <x-text-input id="latitude" class="block mt-1 w-full rounded-xl" type="text"
                                    name="latitude" :value="old('latitude')"
                                    autocomplete="latitude" />
                                <x-input-error :messages="$errors->get('latitude')" class="mt-2" />
                            </div>
                            <div class="w-full">
                                <x-input-label for="longitude">{{ __('Longitude') }}</x-input-label>
                                <x-text-input id="longitude" class="block mt-1 w-full rounded-xl" type="text"
                                    name="longitude" :value="old('longitude')"
                                    autocomplete="longitude" />
                                <x-input-error :messages="$errors->get('longitude')" class="mt-2" />
                            </div>
                        </div>
                        <div class="flex flex-col">
                            <div class="w-full">
                                <x-input-label for="note">{{ __('Note') }}</x-input-label>
                                <x-textarea id="note" class="block mt-1 w-full rounded-xl"
                                    name="note">{{ old('note') }}</x-textarea>
                                <x-input-error :messages="$errors->get('note')" class="mt-2" />
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

            function eventFile(input) {
                // Validate
                if (input.files && input.files[0]) {
                    let fileSize = input.files[0].size / 1024 / 1024; //MB Format
                    let fileType = input.files[0].type;

                    // validate size
                    if (fileSize > 10) {
                        alert('Ukuran File tidak boleh lebih dari 2mb !');
                        input.value = '';
                        return false;
                    }

                    // validate type
                    if (["image/jpeg", "image/jpg", "image/png"].indexOf(fileType) < 0) {
                        alert('Format File tidak valid !');
                        input.value = '';
                        return false;
                    }

                    let reader = new FileReader();

                    reader.onload = function(e) {
                        document.querySelector('#preview-img').setAttribute('src', e.target.result)
                    }

                    reader.readAsDataURL(input.files[0]); // convert to base64 string
                }
            }

            document.addEventListener("DOMContentLoaded", () => {
                console.log("Hello World!");

                // Handle File upload
                document.querySelector('#image').addEventListener('change', e => {
                    if (e.target.files.length == 0) {
                        // $('.profile').attr('src', defaultImage);
                    } else {
                        eventFile(e.target);
                    }
                })

            })
        </script>
    @endpush
</x-app-layout>
