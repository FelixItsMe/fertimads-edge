<x-app-layout>
  <x-slot name="header">
      <h2 class="leading-tight">
          <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('device-type.index') }}">Manajemen Tipe Perangkat IoT</a>
            </li>
              <li class="breadcrumb-item breadcrumb-active">{{ __('Detail Tipe Perangkat IoT') }}</li>
          </ol>
      </h2>
  </x-slot>

  <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          @if (session()->has('device-type-success'))
              <x-alert-info class="mb-3">
                  {{ session()->get('device-type-success') }}
              </x-alert-info>
          @endif
          <div class="grid grid-flow-row grid-cols-1 md:grid-cols-3 max-md:gap-y-4 md:gap-4">
              <div class="w-full">
                  <img src="{{ asset($deviceType->image ?? 'images/default/default-image.jpg') }}"
                      alt="Commodity image"
                      class="object-cover w-full aspect-square border-2 border-slate-500 sm:rounded-lg">
              </div>
              <div class="col-span-2 w-full flex flex-col gap-2">
                  <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 relative">
                      <div class="absolute top-5 right-5">
                        <a href="{{ route('device-type.edit', $deviceType->id) }}" title="{{ __('Edit Tipe Perangkat IoT') }}" class="text-base text-warning">
                            <i class="fa-solid fa-pen pointer-events-none"></i>
                        </a>
                      </div>
                      <div class="flex flex-col gap-y-4">
                          <div class="w-full">
                              <x-input-label class="text-slate-400"
                                  for="name">{{ __('Nama') }}</x-input-label>
                              <span>{{ $deviceType->name }}</span>
                          </div>
                          <div class="w-full">
                              <x-input-label class="text-slate-400"
                                  for="name">{{ __('Versi') }}</x-input-label>
                              <span>v{{ $deviceType->version }}</span>
                          </div>
                          <div class="w-full">
                              <x-input-label class="text-slate-400"
                                  for="name">{{ __('Deskripsi') }}</x-input-label>
                              <div class="prose">{!! $deviceType->description !!}</div>
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
