<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('leaflet/leaflet.css') }}">
        <link rel="stylesheet" href="{{ asset('css/extend.css') }}">
        <style>
            #map {
                height: 800px;
                font-family: 'Figtree', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            }

            .modal-header {
                font-size: 18px;
                font-weight: bold;
                margin-bottom: 10px;
            }

            .modal-body {
                margin-bottom: 10px;
            }

            .modal-footer {
                text-align: right;
            }

            .modal-footer button {
                margin-left: 5px;
            }

            @media (max-height: 450px) {
                #garden-detail-modal {
                    width: 500px;
                }
            }
        </style>
    @endpush
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Data Telemetri Fix Station') }}
        </h2>
    </x-slot>

    @if (session()->has('success'))
        <div class="bg-sky-500 text-white w-full p-6 sm:rounded-lg flex items-center mb-4">
            <i class="fa-solid fa-circle-info text-3xl mr-3"></i>&nbsp;{{ session()->get('success') }}
        </div>
    @endif

    @if (session()->has('failed'))
        <div class="bg-red-400 text-white w-full p-6 sm:rounded-lg flex items-center mb-4">
            <i class="fa-solid fa-circle-info text-3xl mr-3"></i>&nbsp;{{ session()->get('failed') }}
        </div>
    @endif

    <div class="py-12">
        <div class="sm:max-w-7x xl:max-w-full mx-auto sm:px-6 lg:px-8 flex flex-col gap-4">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex flex-col space-y-2">
                        <div class=" flex justify-between">
                            <h1 class="text-3xl font-extrabold">Tabel Data Telemetri Fix Station</h1>
                            <div class="flex flex-row space-x-2 items-center">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="overflow-x-scroll">
                    <table class="w-full align-middle border-slate-400 table mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Timestamp</th>
                                <th>Status</th>
                                <th>Message</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0 h-[500px]" id="fix-station-tbody">
                            @forelse ($cloudExportLogs as $cloudExportLog)
                                <tr>
                                    <td>
                                        {{ ($cloudExportLogs->currentPage() - 1) * $cloudExportLogs->perPage() + $loop->iteration }}
                                    </td>
                                    <td>{{ $cloudExportLog->created_at }}</td>
                                    <td>{{ $cloudExportLog->status->getLabelText() }}</td>
                                    <td>{{ Str::limit($cloudExportLog->message, 200, '...') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($cloudExportLogs->hasPages())
                    <div class="p-6">
                        {{ $cloudExportLogs->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            window.onload = () => {
                console.log('Hello world');
            }
        </script>
    @endpush
</x-app-layout>
