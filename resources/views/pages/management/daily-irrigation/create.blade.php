<x-app-layout>
  <x-slot name="header">
      <h2 class="leading-tight">
          <ol class="breadcrumb">
              <li class="breadcrumb-item">
                  <a href="{{ route('daily-irrigation.index') }}">Irigasi Harian</a>
              </li>
              <li class="breadcrumb-item breadcrumb-active">{{ __('Import Excel') }}</li>
          </ol>
      </h2>
  </x-slot>

  <div class="py-12">
      <div class="sm:max-w-7x xl:max-w-full mx-auto sm:px-6 lg:px-8">
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
              <form action="{{ route('daily-irrigation.store') }}" method="POST" enctype="multipart/form-data">
                  @csrf
                  <div class="p-6 flex flex-col gap-2">
                      <div class="grid grid-flow-col gap-2">
                          <div class="w-full col-span-2">
                              <x-input-label for="image">{{ __('File Excel') }}</x-input-label>
                              <input id="image" class="block w-full px-3 py-2 text-base font-normal text-gray-700 bg-white border border-solid border-gray-300 rounded-md cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                  type="file" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                                  name="import_excel" required
                                  aria-describedby="pictureHelp" />
                              <div id="pictureHelp" class="text-xs text-slate-400 mt-1">Format xlsc. Maks.
                                  2MB</div>
                              <x-input-error :messages="$errors->get('import_excel')" class="mt-2" />
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
