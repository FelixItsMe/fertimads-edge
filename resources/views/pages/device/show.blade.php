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
                    <a href="{{ route('device.index') }}">Manajemen Perangkat IoT</a>
                </li>
                <li class="breadcrumb-item breadcrumb-active">{{ __('Detail Perangkat IoT') }}</li>
            </ol>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="sm:max-w-7x xl:max-w-full mx-auto sm:px-6 lg:px-8">
            @if (session()->has('device-success'))
                <x-alert-info class="mb-3">
                    {{ session()->get('device-success') }}
                </x-alert-info>
            @endif
            <div class="grid grid-flow-row grid-cols-1 md:grid-cols-3 max-md:gap-y-4 md:gap-4">
                <div class="w-full">
                    <img src="{{ asset($device->image ?? ($device->deviceType->image ?? 'images/default/default-image.jpg')) }}"
                        alt="Commodity image"
                        class="object-cover w-full aspect-square border-2 border-slate-500 sm:rounded-lg">
                </div>
                <div class="col-span-2 w-full flex flex-col gap-2">
                    <div id="map" @class(['rounded-md', 'hidden' => $device->deviceType->type->value != \App\Enums\DeviceTypeEnums::HEAD_UNIT->value])></div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="flex flex-col gap-y-4">
                            <div class="w-full">
                                <x-input-label class="text-slate-400" for="name">{{ __('Tipe') }}</x-input-label>
                                <span>{{ $device->deviceType->type->getLabelText() }}</span>
                            </div>
                            <div class="w-full">
                                <x-input-label class="text-slate-400" for="name">{{ __('Series') }}</x-input-label>
                                <span>{{ $device->series }}</span>
                            </div>
                            @if ($device->deviceType->type->value == \App\Enums\DeviceTypeEnums::HEAD_UNIT->value)
                                <div class="w-full">
                                    <x-input-label class="text-slate-400"
                                        for="name">{{ __('Debit') }}</x-input-label>
                                    <span>{{ $device->debit }} Liter/menit</span>
                                </div>
                            @endif
                            <div class="w-full">
                                <x-input-label class="text-slate-400"
                                    for="name">{{ __('Note') }}</x-input-label>
                                <span>{{ $device->note ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <table class="w-full align-middle border-slate-400 table mb-0">
                            <tbody class="table-border-bottom-0">
                                @forelse ($device->deviceSelenoids as $deviceSelenoid)
                                    <tr>
                                        <td>Selenoid {{ $deviceSelenoid->selenoid }}</td>
                                        <td>
                                            <div @class([
                                                'w-3.5',
                                                'h-3.5',
                                                'rounded-full',
                                                'bg-slate-400' => $deviceSelenoid->status->value == 0,
                                                'bg-green-500' => $deviceSelenoid->status->value == 1,
                                            ])></div>
                                        </td>
                                        <td>{{ $deviceSelenoid->current_mode->getLabelText() }}</td>
                                        <td>{{ $deviceSelenoid->garden?->name ?? '-' }}</td>
                                    </tr>
                                @empty
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
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
                marker: null,
                latitude: "{{ $device->latitude }}" ? parseFloat("{{ $device->latitude }}") : null,
                longitude: "{{ $device->longitude }}" ? parseFloat("{{ $device->longitude }}") : null,
            }
            let map = null
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
            map = L.map('map', {
                    preferCanvas: true,
                    layers: [baseMapOptions['Google Satellite']],
                    zoomControl: false
                })
                .setView([stateData.latitude ?? -6.46958, stateData.longitude ?? 107.033339], 18);

            L.control.zoom({
                position: 'bottomleft'
            }).addTo(map);

            L.control.layers(baseMapOptions, null, {
                position: 'bottomright'
            }).addTo(map)

            window.onload = () => {
                console.log('Hello World');

                if (stateData.latitude != null) {
                    stateData.marker = initMarker(map, stateData.latitude, stateData.longitude)
                }
            }
        </script>
    @endpush
</x-app-layout>
