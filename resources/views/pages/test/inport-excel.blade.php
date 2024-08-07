<x-app-layout>
  <x-slot name="header">
      <h2 class="leading-tight">
          <ol class="breadcrumb">
              <li class="breadcrumb-item">
                  <a href="{{ route('commodity.index') }}">Manajemen Komoditi</a>
              </li>
              <li class="breadcrumb-item breadcrumb-active">{{ __('Tambah Komoditi Baru') }}</li>
          </ol>
      </h2>
  </x-slot>

  <div class="py-12">
      <div class="sm:max-w-7x xl:max-w-full mx-auto sm:px-6 lg:px-8">
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
              <form action="{{ route('test.import.excel.store') }}" method="POST" enctype="multipart/form-data">
                  @csrf
                  <div class="p-6 flex flex-col gap-2">
                      <div class="grid grid-flow-col sm:grid-flow-row sm:grid-cols-3 gap-2">
                          <div class="w-full">
                          </div>
                          <div class="w-full col-span-2">
                              <x-input-label for="image">{{ __('File Gambar') }}</x-input-label>
                              <input id="image" class="block w-full px-3 py-2 text-base font-normal text-gray-700 bg-white border border-solid border-gray-300 rounded-md cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                  type="file"
                                  name="test_irigasi" required
                                  aria-describedby="pictureHelp" />
                              <div id="pictureHelp" class="text-xs text-slate-400 mt-1">Format gambar JPG, JPEG, PNG. Maks.
                                  2MB</div>
                              <x-input-error :messages="$errors->get('image')" class="mt-2" />
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

          document.addEventListener("DOMContentLoaded", () => {
              console.log("Hello World!");

          })
      </script>
  @endpush
</x-app-layout>
