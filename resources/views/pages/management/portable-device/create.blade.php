<x-app-layout>
    <x-slot name="header">
        <h2 class="leading-tight">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('portable-device.index') }}">Manajemen Perangkat Portable SMS</a>
                </li>
                <li class="breadcrumb-item breadcrumb-active">{{ __('Tambah Perangkat Portable SMS Baru') }}</li>
            </ol>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="sm:max-w-7x xl:max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <form action="{{ route('portable-device.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="p-6 flex flex-col gap-2">
                        <div class="grid grid-flow-col sm:grid-flow-row sm:grid-cols-5 gap-2">
                            <div class="w-full">
                                <x-input-label for="name">{{ __('Gambar') }}</x-input-label>
                                <img src="{{ asset('images/default/default-image.jpg') }}" alt="Preview Image"
                                    class="aspect-square object-cover w-full" id="preview-img">
                            </div>
                            <div class="w-full col-span-4">
                                <x-input-label for="image">{{ __('File Gambar') }}</x-input-label>
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
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-2">
                            <div class="w-full">
                                <x-input-label for="series">{{ __('Series') }}</x-input-label>
                                <x-text-input id="series" class="block mt-1 w-full rounded-xl" type="text"
                                    name="series" :value="old('series')" required autofocus autocomplete="series" />
                                <x-input-error :messages="$errors->get('series')" class="mt-2" />
                            </div>
                            <div class="w-full">
                                <x-input-label for="version">{{ __('Version') }}</x-input-label>
                                <x-text-input id="version" class="block mt-1 w-full rounded-xl" type="text"
                                    name="version" :value="old('version')" required autofocus autocomplete="version"
                                    placeholder="1.0" aria-placeholder="1.0" />
                                <x-input-error :messages="$errors->get('version')" class="mt-2" />
                            </div>
                        </div>
                        <div class="w-full">
                            <x-input-label for="production-date">{{ __('Tanggal Produksi') }}</x-input-label>
                            <x-text-input id="production-date" class="block mt-1 w-full rounded-xl" type="date"
                                name="production_date" :value="old('production_date')" required autofocus
                                autocomplete="production_date" />
                            <x-input-error :messages="$errors->get('production_date')" class="mt-2" />
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
        <script>
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

                document.getElementById("version").addEventListener("keypress", function(event) {
                    const key = event.keyCode;
                    // Only allow numbers (key codes 48 to 57)
                    if (key < 46 || key == 47 || key > 57) {
                        event.preventDefault();
                    }
                });
            })
        </script>
    @endpush
</x-app-layout>
