<x-app-layout>
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
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <form action="{{ route('device.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="p-6 flex flex-col gap-2">
                        <div class="grid grid-flow-col sm:grid-flow-row sm:grid-cols-3 gap-2">
                            <div class="w-full">
                                <x-input-label for="name">{{ __('Gambar') }}</x-input-label>
                                <img src="{{ asset('images/default/default-image.jpg') }}" alt="Preview Image"
                                    class="aspect-square object-cover w-full" id="preview-img">
                            </div>
                            <div class="w-full col-span-2">
                                <x-input-label for="image">{{ __('File Gambar') }} <span class="text-danger">*Tidak wajib</span></x-input-label>
                                <input id="image" class="block w-full px-3 py-2 text-base font-normal text-gray-700 bg-white border border-solid border-gray-300 rounded-md cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    type="file"
                                    accept="image/png, image/jpg, image/jpeg"
                                    name="image"
                                    aria-describedby="pictureHelp" />
                                <div id="pictureHelp" class="text-xs text-slate-400 mt-1">Format gambar JPG, JPEG, PNG. Maks.
                                    2MB</div>
                                <x-input-error :messages="$errors->get('image')" class="mt-2" />
                            </div>
                        </div>
                        <div class="w-full">
                            <x-input-label for="device_type_id">{{ __('Pilih Tipe') }}</x-input-label>
                            <x-select-input id="device_type_id" class="block mt-1 w-full rounded-xl" name="device_type_id">
                                <option value="">Pilih Tipe</option>
                                @foreach ($deviceTypes as $id => $deviceType)
                                    <option value="{{ $id }}">{{ $deviceType }}</option>
                                @endforeach
                            </x-select-input>
                            <x-input-error :messages="$errors->get('device_type_id')" class="mt-2" />
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
                                    min="0" step=".01"
                                    name="debit" :value="old('debit')" required autofocus autocomplete="debit" />
                                <x-input-error :messages="$errors->get('debit')" class="mt-2" />
                            </div>
                        </div>
                        <div class="flex flex-col">
                            <div class="w-full">
                                <x-input-label for="note">{{ __('Note') }}</x-input-label>
                                <x-textarea id="note" class="block mt-1 w-full rounded-xl" name="note">{{ old('note') }}</x-textarea>
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

            })
        </script>
    @endpush
</x-app-layout>
