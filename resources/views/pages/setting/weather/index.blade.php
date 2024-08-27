<x-app-layout>
  <x-slot name="header">
      <h2 class="leading-tight">
          <ol class="breadcrumb">
              <li class="breadcrumb-item breadcrumb-active">{{ __('Cuaca') }}</li>
          </ol>
      </h2>
  </x-slot>

  <div class="py-12">
      <div class="sm:max-w-7x xl:max-w-full mx-auto sm:px-6 lg:px-8">
          @if (session()->has('setting-weather-success'))
              <div class="bg-sky-500 text-white w-full p-6 sm:rounded-lg flex items-center mb-4">
                  <i class="fa-solid fa-circle-info text-3xl mr-3"></i>&nbsp;{{ session()->get('setting-weather-success') }}
              </div>
          @endif
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
              <form action="{{ route('weather.update') }}" method="POST">
                  @csrf
                  @method('PUT')
                  <div class="p-6 flex flex-col gap-2">
                      <div class="w-full">
                          <x-input-label for="aws_device_id">{{ __('MODE') }}</x-input-label>
                          <x-select-input id="aws_device_id" class="block mt-1 w-full rounded-xl" name="aws_device_id">
                              <option value="" @selected(!$wetherWidget->aws_device_id)>BMKG</option>
                              @foreach ($awsDevices as $id => $series)
                                  <option value="{{ $id }}" @selected($wetherWidget->aws_device_id == $id)>{{ $series }}</option>
                              @endforeach
                          </x-select-input>
                          <x-input-error :messages="$errors->get('aws_device_id')" class="mt-2" />
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
