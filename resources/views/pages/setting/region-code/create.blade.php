<x-app-layout>
    <x-slot name="header">
        <h2 class="leading-tight">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('commodity.index') }}">Kode Wilayah</a>
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
                <form action="{{ route('region-code.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="p-6 flex flex-col gap-2">
                        <div class="w-full">
                            <x-input-label for="csv">{{ __('File Kode Wilayah') }}</x-input-label>
                            <input id="csv"
                                class="block w-full px-3 py-2 text-base font-normal text-gray-700 bg-white border border-solid border-gray-300 rounded-md cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                type="file" accept=".csv" name="csv_file" required
                                aria-describedby="csvHelp" />
                            <div id="csvHelp" class="text-xs text-slate-400 mt-1">Format file CSV.</div>
                            <x-input-error :messages="$errors->get('csv_file')" class="mt-2" />
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
        <script>
            document.addEventListener("DOMContentLoaded", () => {
                console.log("Hello World!");

            })
        </script>
    @endpush
</x-app-layout>
