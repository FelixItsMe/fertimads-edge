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
                    <a href="{{ route('garden.index') }}">Kebun</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('garden.show', $smsGarden->garden_id) }}">Kebun {{ $smsGarden->garden->name }}</a>
                </li>
                <li class="breadcrumb-item breadcrumb-active">{{ __('Detail SMS') }} {{ $smsGarden->created_at }}</li>
            </ol>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="sm:max-w-7x xl:max-w-full mx-auto sm:px-6 lg:px-8 flex flex-col space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                @foreach ($avg as $key => $avgValue)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex flex-row justify-between">
                        <div>
                            <h5 class="text-xs text-slate-400">Rata rata
                                {{ \App\Enums\SampleEnums::from($key)->getLabelTextWithUnit() }}</h5>
                            <span class="font-bold">{{ number_format($avgValue, 2) }}</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fa-solid fa-globe p-3 bg-primary text-white rounded-lg"></i>
                        </div>
                    </div>
                @endforeach
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex flex-row justify-between">
                    <div>
                        <h5 class="text-xs text-slate-400">Total</h5>
                        <span class="font-bold">{{ $smsTelemetries->total() }}</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fa-solid fa-globe p-3 bg-primary text-white rounded-lg"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 flex justify-between">
                    <div>

                    </div>
                    <div>
                        <a href="{{ route('sms-garden.export-excel', $smsGarden->id) }}"
                            class="bg-green-500 text-white py-2 px-4 rounded-md text-center"
                            title="Export Excel">Excel</a>
                    </div>
                </div>
                <div class="overflow-x-scroll">
                    <table class="w-full align-middle border-slate-400 table mb-0">
                        <thead>
                            <tr>
                                <th style="width: 5%">#</th>
                                <th>N</th>
                                <th>P</th>
                                <th>K</th>
                                <th>EC</th>
                                <th>pH</th>
                                <th>Suhu</th>
                                <th>Kelembapan</th>
                                <th>Suhu Tanah</th>
                                <th>Kelembapan Tanah</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse ($smsTelemetries as $smsTelemetry)
                                <tr>
                                    <td class="text-center">
                                        {{ ($smsTelemetries->currentPage() - 1) * $smsTelemetries->perPage() + $loop->iteration }}
                                    </td>
                                    <td class="text-center">{{ $smsTelemetry->samples->n }} mg/kg</td>
                                    <td class="text-center">{{ $smsTelemetry->samples->p }} mg/kg</td>
                                    <td class="text-center">{{ $smsTelemetry->samples->k }} mg/kg</td>
                                    <td class="text-center">{{ $smsTelemetry->samples->ec ?? '-' }} uS/cm</td>
                                    <td class="text-center">{{ $smsTelemetry->samples?->ph ?? '-' }}</td>
                                    <td class="text-center">{{ $smsTelemetry->samples->ambient_temperature ?? '-' }}°C
                                    </td>
                                    <td class="text-center">{{ $smsTelemetry->samples->ambient_humidity ?? '-' }}%</td>
                                    <td class="text-center">{{ $smsTelemetry->samples->soil_temperature ?? '-' }}°C
                                    </td>
                                    <td class="text-center">{{ $smsTelemetry->samples->soil_moisture ?? '-' }}%</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">Tidak ada data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    @if ($smsTelemetries->hasPages())
                        <div class="p-6">
                            {{ $smsTelemetries->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('leaflet/leaflet.js') }}"></script>
        <script src="{{ asset('js/extend.js') }}"></script>
        <script src="{{ asset('js/map.js') }}"></script>
        <script>
            window.onload = () => {
                console.log('Hello World');
            }
        </script>
    @endpush
</x-app-layout>
