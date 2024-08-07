<x-app-layout>
  <x-slot name="header">
      <h2 class="leading-tight">
          <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('device.index') }}">Manajemen Perangkat IoT</a>
            </li>
              <li class="breadcrumb-item breadcrumb-active">{{ __('Detail Perangkat IoT') }}</li>
          </ol>
      </h2>
  </x-slot>

  <div class="py-12">
      <div class="sm:max-w-7x xl:max-w-full mx-auto sm:px-6 lg:px-8">
          @if (session()->has('device-success'))
              <x-alert-info class="mb-3">
                  {{ session()->get('device-success') }}
              </x-alert-info>
          @endif
          <div class="grid grid-flow-row grid-cols-1 md:grid-cols-3 max-md:gap-y-4 md:gap-4">
              <div class="w-full">
                  <img src="{{ asset($device->image ?? $device->deviceType->image ?? 'images/default/default-image.jpg') }}"
                      alt="Commodity image"
                      class="object-cover w-full aspect-square border-2 border-slate-500 sm:rounded-lg">
              </div>
              <div class="col-span-2 w-full flex flex-col gap-2">
                  <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                      <div class="flex flex-col gap-y-4">
                          <div class="w-full">
                              <x-input-label class="text-slate-400"
                                  for="name">{{ __('Series') }}</x-input-label>
                              <span>{{ $device->series }}</span>
                          </div>
                          <div class="w-full">
                              <x-input-label class="text-slate-400"
                                  for="name">{{ __('Debit') }}</x-input-label>
                              <span>{{ $device->debit }} Liter/menit</span>
                          </div>
                          <div class="w-full">
                              <x-input-label class="text-slate-400"
                                  for="name">{{ __('Note') }}</x-input-label>
                              <span>{{ $device->note ?? '-' }}</span>
                          </div>
                      </div>
                  </div>
                  <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                      <table class="w-full align-middle border-slate-400 table mb-0">
                          <tbody class="table-border-bottom-0">
                              @forelse ($device->deviceSelenoids as $deviceSelenoid)
                                  <tr>
                                      <td>Selenoid {{ $deviceSelenoid->selenoid }}</td>
                                      <td>
                                        <div @class([
                                          'w-3.5',
                                          'h-3.5',
                                          'rounded-full',
                                          'bg-slate-400' => $deviceSelenoid->status->value == 0,
                                          'bg-green-500' => $deviceSelenoid->status->value == 1,
                                        ])></div>
                                      </td>
                                      <td>{{ $deviceSelenoid->current_mode->getLabelText() }}</td>
                                      <td>{{ $deviceSelenoid->garden?->name ?? '-' }}</td>
                                  </tr>
                              @empty
                                  <tr>
                                      <td colspan="4" class="text-center">Tidak ada data</td>
                                  </tr>
                              @endforelse
                          </tbody>
                      </table>
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
