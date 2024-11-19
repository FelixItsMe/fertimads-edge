<x-app-layout>
    <x-slot name="header">
        <h2 class="leading-tight">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('region-code.index') }}">Kode Wilayah</a>
                </li>
                <li class="breadcrumb-item breadcrumb-active">{{ __('Import Kode Wilayah') }}</li>
            </ol>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="sm:max-w-7x xl:max-w-full mx-auto sm:px-6 lg:px-8">
            @if (session()->has('setting-weather-success'))
                <div class="bg-sky-500 text-white w-full p-6 sm:rounded-lg flex items-center mb-4">
                    <i
                        class="fa-solid fa-circle-info text-3xl mr-3"></i>&nbsp;{{ session()->get('setting-weather-success') }}
                </div>
            @endif
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <form action="{{ route('region-code.store') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                    @csrf
                    <div class="p-6 flex flex-col gap-2">
                        <div class="w-full">
                            <x-input-label for="csv">{{ __('File Kode Wilayah') }}</x-input-label>
                            <input id="csv"
                                class="block w-full px-3 py-2 text-base font-normal text-gray-700 bg-white border border-solid border-gray-300 rounded-md cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                type="file" accept=".csv" name="csv_file" required aria-describedby="csvHelp" />
                            <div id="csvHelp" class="text-xs text-slate-400 mt-1">Format file CSV.</div>
                            <x-input-error :messages="$errors->get('csv_file')" class="mt-2" />
                        </div>
                        <div class="flex flex-col">
                            <div class="w-full flex justify-between">
                                <div>
                                  <span id="text-loading">
                                    @switch($importStatus)
                                        @case(1)
                                            Loading...
                                            @break
                                        @case(2)
                                            Selesai Import Data
                                            @break
                                        @default

                                    @endswitch
                                  </span>
                                </div>
                                <div>
                                  @if ($importStatus != 1)
                                  <x-primary-button id="btn-submit">
                                      {{ __('Simpan') }}
                                  </x-primary-button>
                                  @else
                                  <x-primary-button disabled class="cursor-not-allowed" id="btn-submit">
                                      {{ __('Simpan') }}
                                  </x-primary-button>
                                  @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            const uploadForm = document.getElementById('uploadForm');

            uploadForm.addEventListener('submit', async (event) => {
                event.preventDefault(); // Prevent form from refreshing the page

                const fileInput = document.getElementById('csv');
                const file = fileInput.files[0];

                if (!file) {
                    alert('Please select a file!');
                    return;
                }

                const formData = new FormData();
                formData.append('csv_file', file);

                try {
                    const response = await fetch("{{ route('region-code.store') }}", {
                        method: 'POST',
                        headers: {
                          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').attributes.content
                            .nodeValue,
                          'Accept': 'application/json',
                        },
                        body: formData,
                    });

                    if (response.ok) {
                        const result = await response.json();
                        alert('File uploaded successfully: ' + result.message);
                        fileInput.value = ''
                        document.getElementById('text-loading').textContent = "Loading"
                        document.getElementById('btn-submit').disabled = true
                        document.getElementById('btn-submit').classList.add('cursor-not-allowed')
                    } else {
                        alert('File upload failed: ' + response.statusText);
                    }
                } catch (error) {
                    alert('Error: ' + error.message);
                }
            });

            document.addEventListener("DOMContentLoaded", () => {
                console.log("Hello World!");

                window.Echo.private('import.{{ auth()->user()->id }}')
                  .listen('ImportFinishEvent', (event) => {
                      // document.querySelector('#export-link').innerHTML =
                      console.log('message');

                      document.getElementById('text-loading').textContent = "Selesai Import Data"
                      document.getElementById('btn-submit').disabled = false
                      document.getElementById('btn-submit').classList.remove('cursor-not-allowed')
                  })
            })
        </script>
    @endpush
</x-app-layout>
