<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    {{-- Famicon --}}
    <link rel="icon" href="{{ asset('images/default/famicon.svg') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:ital,wght@0,300..900;1,300..900&display=swap"
        rel="stylesheet">

    <!-- Icons. Uncomment required icon fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Page CSS -->
    @stack('styles')
    <link href='https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/leaflet.fullscreen.css'
        rel='stylesheet' />
    <link href="https://cdn.jsdelivr.net/gh/aazuspan/leaflet-feature-legend/src/feature-legend.css" rel="stylesheet" />

    <style>
        .legend-spacing {
            margin-bottom: .5em;
        }

        .legend-spacing i {
            margin-right: 1em;
        }

        .legend {
            background-color: white;
            padding: 15px;
            border-radius: 10px;
        }
    </style>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    <div id="loading-spinner" class="fixed top-0 left-0 w-full h-full flex justify-center items-center bg-primary z-50"
        style="z-index: 9999;">
        <div class="loader border-t-4 border-b-4 border-slate-50 rounded-full w-16 h-16 animate-spin"></div>
    </div>

    <div class="fixed top-4 right-4 bg-red-500 text-white p-4 rounded-md hidden" id="error-body" style="z-index: 999">
        <i class="fa-solid fa-circle-exclamation"></i>&nbsp;<span id="error-message"></span>
    </div>
    <div id="layout-wrapper" class="w-full flex flex-auto items-stretch">
        <div x-data="{ sideopen: true }" class="min-h-screen w-full flex flex-auto bg-gray-100 items-stretch">
            @include('layouts.sidebar')
            <div :class="{ 'lg:pl-64': sideopen }" class="pt-0 basis-full flex-col w-0 min-w-0 max-w-full">
                @include('layouts.navigation')

                <!-- Page Heading -->
                @isset($header)
                    <header class="bg-white shadow">
                        <div class="sm:sm:max-w-7x xl:max-w-full mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <!-- Page Content -->
                <main class="min-h-screen">
                    {{ $slot }}
                </main>
                <footer class="px-6 py-1 font-normal text-base text-slate-400">
                    Copyright © {{ now()->year }} IPB UNIVERSITY. All Right Reserved.
                </footer>
            </div>
        </div>
    </div>

    {{-- Export Modal --}}
    <x-modal name="sign-out" style="z-index: 999999999;" focusable>
        <form method="post" action="{{ route('logout') }}" class="p-6">
            @csrf
            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Apakah anda yakin ingin keluar?') }}
            </h2>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Batalkan') }}
                </x-secondary-button>

                <x-danger-button class="ms-3">
                    {{ __('Keluar') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/js/all.min.js"
        integrity="sha512-u3fPA7V8qQmhBPNT5quvaXVa1mnnLSXUep5PS1qo5NRzHwG19aHmNJnj1Q8hpA/nBWZtZD4r4AX6YOt5ynLN2g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    @stack('scripts')
    <script src="https://cdn.jsdelivr.net/gh/aazuspan/leaflet-feature-legend/src/feature-legend.js"></script>
    <script src='https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/Leaflet.fullscreen.min.js'></script>
    <script src="{{ asset('js/api.js') }}"></script>
    <script>
        const showLoading = () => {
            document.getElementById('loading-spinner').classList.remove('hidden');
            document.getElementById('layout-wrapper').classList.add('hidden');
        }
        const hideLoading = () => {
            document.getElementById('loading-spinner').classList.add('hidden');
            document.getElementById('layout-wrapper').classList.remove('hidden');
        }

        const initMapPlugin = () => {
            if (map) {
                map.addControl(new L.Control.Fullscreen({
                    title: {
                        'false': 'View Fullscreen',
                        'true': 'Exit Fullscreen'
                    },
                    position: "topleft"
                }));
            }
        }

        const initMap = () => {
            const getObjects = async () => {
                const data = await fetchData(
                    "{{ route('extra.map-object.geojson') }}", {
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').attributes
                                .content.nodeValue,
                            'Accept': 'application/json',
                        }
                    }
                )

                return data
            }

            const initMapObjects = async (map) => {
                const objects = await getObjects()
                let mapObjectsGroup = L.layerGroup()
                let waterPipelinesGroup = L.layerGroup()
                let mapObjectsLegends = {}

                if (!objects) {
                    return false
                }

                mapObjectsGroup.clearLayers()
                waterPipelinesGroup.clearLayers()

                let customIcon = L.icon({
                    iconUrl: "{{ asset('assets/leaflet/water-pipeline.svg') }}",
                    iconSize: [25, 25],
                    iconAnchor: [12, 12],
                })

                for (const waterPipeline of objects.waterPipelines) {

                    const popupContent = `
                      <h6 class="font-bold text-lg mb-3 font-sans">Informasi Marker</h6>
                      <div class="font-sans">
                          <div class="mb-2">
                            <div class="font-bold">Nama</div>
                            <div class="col-sm-8">${waterPipeline.name}</div>
                          </div>
                          <div class="mb-2">
                            <div class="font-bold">Tipe</div>
                            <div class="col-sm-8">Jalur Pipa Air</div>
                          </div>
                      </div>
                    `
                    waterPipelinesGroup.addLayer(
                        L.polyline(waterPipeline.polyline).bringToBack().bindPopup(popupContent)
                    )
                }
                for (const feature of objects.features) {
                    const [lng, lat] = feature.geometry.coordinates

                    const popupContent = `
                      <h6 class="font-bold text-lg mb-3 font-sans">Informasi Marker</h6>
                      <div class="font-sans">
                          <div class="mb-2">
                            <div class="font-bold">Nama</div>
                            <div class="col-sm-8">${feature.properties.name}</div>
                          </div>
                          <div class="mb-2">
                            <div class="font-bold">Tipe</div>
                            <div class="col-sm-8">${feature.properties.type}</div>
                          </div>
                          <div class="mb-2">
                            <div class="font-bold">Deskripsi</div>
                            <div class="col-sm-8">${feature.properties.description}</div>
                          </div>
                      </div>
                    `

                    mapObjectsGroup.addLayer(
                        L.marker([lat, lng], {
                            icon: L.icon({
                                iconUrl: feature.properties.icon,
                                iconSize: [50, 50],
                                iconAnchor: [12, 12],
                            })
                        })
                        .bindPopup(popupContent)
                    )
                }

                mapObjectsGroup.addTo(map);

                waterPipelinesGroup.addTo(map)

                // const onEachFeature = (feature, layer) => {
                //     if (feature.properties && feature.properties.name) {
                //         const popupContent = `
            //           <h6 class="font-bold text-lg mb-3 font-sans">Informasi Marker</h6>
            //           <div class="font-sans">
            //               <div class="mb-2">
            //                 <div class="font-bold">Nama</div>
            //                 <div class="col-sm-8">${feature.properties.name}</div>
            //               </div>
            //               <div class="mb-2">
            //                 <div class="font-bold">Tipe</div>
            //                 <div class="col-sm-8">${feature.properties.type}</div>
            //               </div>
            //               <div class="mb-2">
            //                 <div class="font-bold">Deskripsi</div>
            //                 <div class="col-sm-8">${feature.properties.description}</div>
            //               </div>
            //           </div>
            //         `

                //         layer.bindPopup(popupContent);
                //     }

                //     if (feature.properties && feature.properties.icon) {
                //         let icon = L.icon({
                //             iconUrl: feature.properties.icon,
                //             iconSize: [50, 50],
                //             iconAnchor: [12, 12],
                //         })

                //         layer.setIcon(icon)
                //     }

                //     if (feature.properties && feature.properties.type) {
                //         mapObjectsLegends[feature.properties.type] = layer
                //     }
                // }

                // mapObjectsGroup.addLayer(L.geoJSON(objects, {
                //     onEachFeature: onEachFeature
                // }))
                // mapObjectsGroup.addTo(map)

                // let customIcon = L.icon({
                //     iconUrl: "{{ asset('assets/leaflet/water-pipeline.svg') }}",
                //     iconSize: [25, 25],
                //     iconAnchor: [12, 12],
                // })
                // const iconMarker = L.marker([51.505, -0.115], {
                //     icon: customIcon
                // })

                // for (const waterPipeline of objects.waterPipelines) {
                //     waterPipelinesGroup.addLayer(L.polyline(waterPipeline.polyline).bringToBack())
                // }

                // waterPipelinesGroup.addTo(map)

                // mapObjectsLegends['Jalur Pipa Air'] = iconMarker

                // L.control.featureLegend(mapObjectsLegends, {
                //     position: "bottomleft",
                //     title: "Legenda",
                //     symbolContainerSize: 50,
                //     symbolScaling: "clamped",
                //     maxSymbolSize: 50,
                //     minSymbolSize: 2,
                //     collapsed: true,
                //     drawShadows: true,
                // }).addTo(map);

                // const legendTitle = document.querySelector('.leaflet-control-feature-legend-title')
                // const legendContents = document.querySelectorAll('.leaflet-control-feature-legend-contents div')
                // legendTitle.classList.add("font-bold", "mb-3")
                // legendContents.forEach((item) => {
                //     item.classList.add("legend-spacing")
                // })

                let legend = L.control({
                    position: 'bottomleft'
                });

                legend.onAdd = (map) => {
                    const div = L.DomUtil.create('div', 'info legend');

                    let listLegend = ``

                    objects.features.forEach(feature => {
                        listLegend +=
                            `<div class="flex flex-row items-center"><img class="w-12 h-12" src="${feature.properties.icon}" /> <span>${feature.properties.name}</span></div>`
                    });

                    listLegend +=
                        `<div class="flex flex-row items-center"><img class="w-12 h-12" src="{{ asset('assets/leaflet/water-pipeline.svg') }}" /> <span>Jalur Pipa Air</span></div>`

                    div.innerHTML = `<div class="flex flex-row justify-between space-x-4">
                      <h4 id="map-legend" class="cursor-pointer" title="Klik untuk melihat legenda">Legenda</h4>
                    </div>
                    <div id="map-legend-body" class="flex flex-row flex-wrap space-x-2 hidden">${listLegend}</div>`;

                    return div;
                };

                legend.addTo(map);

                const mapLegend = document.getElementById('map-legend')

                if (mapLegend) {
                    mapLegend.addEventListener('click', e => {
                        document.getElementById('map-legend-body').classList.toggle(
                            'hidden')
                    })
                }
            }

            if (map) {
                initMapObjects(map)
            }
        }

        // Show the loading spinner when the page starts loading
        showLoading()

        function scrollToActiveLink() {
            const activeLink = document.querySelector('#sidebar .active-icon');

            if (activeLink) {
                setTimeout(() => {
                    activeLink.scrollIntoView({
                        behavior: 'instant',
                        block: 'center'
                    });
                }, 0);
            }
        }

        // Hide the loading spinner when the page has fully loaded
        window.addEventListener('load', function() {
            scrollToActiveLink()
            hideLoading()
            initMapPlugin()
            initMap()
        });
    </script>
</body>

</html>
