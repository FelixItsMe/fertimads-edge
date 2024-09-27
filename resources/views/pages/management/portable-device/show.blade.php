<x-app-layout>
  <x-slot name="header">
      <h2 class="leading-tight">
          <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('portable-device.index') }}">Manajemen Perangkat Portable SMS</a>
            </li>
              <li class="breadcrumb-item breadcrumb-active">{{ __('Detail Perangkat Portable SMS') }}</li>
          </ol>
      </h2>
  </x-slot>

  <div class="py-12">
      <div class="sm:max-w-7x xl:max-w-full mx-auto sm:px-6 lg:px-8">
          @if (session()->has('portable-device-success'))
              <x-alert-info class="mb-3">
                  {{ session()->get('portable-device-success') }}
              </x-alert-info>
          @endif
          <div class="grid grid-flow-row grid-cols-1 md:grid-cols-3 max-md:gap-y-4 md:gap-4">
              <div class="w-full">
                  <img src="{{ asset($portableDevice->image ?? 'images/default/default-image.jpg') }}"
                      alt="Portable Device image"
                      class="object-cover w-full aspect-square border-2 border-slate-500 sm:rounded-lg">
              </div>
              <div class="col-span-2 w-full flex flex-col gap-2">
                  <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                      <div class="flex flex-col gap-y-4">
                        <div class="w-full">
                            <x-input-label class="text-slate-400"
                                for="name">{{ __('Series') }}</x-input-label>
                            <span>{{ $portableDevice->series }}</span>
                        </div>
                          <div class="w-full">
                              <x-input-label class="text-slate-400"
                                  for="name">{{ __('Version') }}</x-input-label>
                              <span>{{ $portableDevice->version }}</span>
                          </div>
                          <div class="w-full">
                              <x-input-label class="text-slate-400"
                                  for="name">{{ __('Tanggal Produksi') }}</x-input-label>
                              <span>{{ $portableDevice->production_date }} Liter/menit</span>
                          </div>
                          <div class="w-full">
                              <x-input-label class="text-slate-400"
                                  for="name">{{ __('Note') }}</x-input-label>
                              <span>{{ $portableDevice->note ?? '-' }}</span>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </div>

  @push('scripts')
      <script>
          window.onload = () => {
              console.log('Hello World');
          }
      </script>
  @endpush
</x-app-layout>
