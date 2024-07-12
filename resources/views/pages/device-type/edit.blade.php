<x-app-layout>
    @push('styles')
        <style>
            .ck-content h1              { font-size: 2em; margin: .67em 0 }
            .ck-content h2              { font-size: 1.5em; margin: .75em 0 }
            .ck-content h3              { font-size: 1.17em; margin: .83em 0 }
            .ck-content h5              { font-size: .83em; margin: 1.5em 0 }
            .ck-content h6              { font-size: .75em; margin: 1.67em 0 }
            .ck-content ul {
                list-style: inside !important;
            }
            .ck-content ol {
                list-style-position: inside !important;
            }
        </style>
    @endpush
    <x-slot name="header">
        <h2 class="leading-tight">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('device-type.index') }}">Manajemen Tipe Perangkat IoT</a>
                </li>
                <li class="breadcrumb-item breadcrumb-active">{{ __('Edit Tipe Perangkat IoT') }}</li>
            </ol>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <form action="{{ route('device-type.update', $deviceType->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="p-6 flex flex-col gap-2">
                        <div class="grid grid-flow-col sm:grid-flow-row sm:grid-cols-3 gap-2">
                            <div class="w-full">
                                <x-input-label for="name">{{ __('Gambar') }}</x-input-label>
                                <img src="{{ asset($deviceType->image ?? 'images/default/default-image.jpg') }}" alt="Preview Image"
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
                        <div class="grid grid-flow-col grid-cols-1 sm:grid-flow-row sm:grid-cols-2 gap-2">
                            <div class="w-full">
                                <x-input-label for="name">{{ __('Nama Perangkat') }}</x-input-label>
                                <x-text-input id="name" class="block mt-1 w-full rounded-xl" type="text"
                                    name="name" :value="$deviceType->name" required autofocus autocomplete="name" />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>
                            <div class="w-full">
                                <x-input-label for="version">{{ __('Versi Perangkat') }}</x-input-label>
                                <x-text-input id="version" class="block mt-1 w-full rounded-xl" type="text"
                                    name="version" :value="$deviceType->version" required autofocus autocomplete="version" />
                                <x-input-error :messages="$errors->get('version')" class="mt-2" />
                            </div>
                        </div>
                        <div class="flex flex-col">
                            <div class="w-full">
                                <x-input-label for="description">{{ __('Deskripsi') }}</x-input-label>
                                <x-textarea id="description" class="block mt-1 w-full rounded-xl prose" name="description" required autofocus
                                    autocomplete="description">{!! $deviceType->description !!}</x-textarea>
                                <x-input-error :messages="$errors->get('description')" class="mt-2" />
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
        <script src="{{ asset('assets/ckeditor5-38.1.0/build/ckeditor.js') }}"></script>
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


                ClassicEditor
                    .create(document.querySelector('#description'))
                    .catch(error => {
                        console.error(error);
                    });

                document.getElementById("version").addEventListener("keypress", function(event) {
                    const key = event.keyCode;
                    // Only allow numbers (key codes 48 to 57)
                    if (key < 46 || key == 47 || key > 57) {
                        event.preventDefault();
                    }
                });

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
