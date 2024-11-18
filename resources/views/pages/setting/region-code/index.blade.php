<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kode Wilayah') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="sm:max-w-7x xl:max-w-full mx-auto sm:px-6 lg:px-8 flex flex-col gap-4">
            @if (session()->has('activity-log-success'))
                <div class="bg-sky-500 text-white w-full p-6 sm:rounded-lg flex items-center">
                    <i
                        class="fa-solid fa-circle-info text-3xl mr-3"></i>&nbsp;{{ session()->get('activity-log-success') }}
                </div>
            @endif
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 flex justify-between">
                    <h1 class="text-3xl font-extrabold">List Kode Wilayah</h1>
                    <div>
                        <a href="{{ route('region-code.create') }}"
                            class="bg-fertimads-2 text-white py-1.5 px-5 rounded-md">Import Kode Wilayah</a>
                    </div>
                </div>
                <div class="p-6 pt-0 grid grid-cols-4 gap-2">
                    @foreach ($regionCodes as $regionCode)
                        <div>
                            <a href="{{ route('region-code.show', $regionCode->full_code) }}"><span
                                    class="text-sm text-slate-400">{{ $regionCode->level_1 }}</span>&nbsp;<span
                                    class="text-primary font-bold">{{ $regionCode->region_name }}</span></a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script src="{{ asset('js/api.js') }}"></script>
        <script>
            window.onload = () => {
                console.log('Hello World');


            }
        </script>
    @endpush
</x-app-layout>
