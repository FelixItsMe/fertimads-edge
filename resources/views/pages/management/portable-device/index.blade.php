<x-app-layout>
  <x-slot name="header">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          {{ __('Manajemen Perangkat Portable SMS') }}
      </h2>
  </x-slot>

  <div class="py-12">
      <div class="sm:max-w-7x xl:max-w-full mx-auto sm:px-6 lg:px-8 flex flex-col gap-4">
          @if (session()->has('portable-device-success'))
              <x-alert-info>
                  {{ session()->get('portable-device-success') }}
              </x-alert-info>
          @endif
          <div class="flex flex-col sm:flex-row gap-4">
              <x-card-info class="w-full md:w-1/2 lg:w-1/4">
                  <h5 class="text-xs text-slate-400">Total Perangkat Portable SMS</h5>
                  <span class="font-bold">{{ $portableDevices->total() }}</span>
              </x-card-info>
          </div>
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-6 flex justify-between">
                  <h1 class="text-3xl font-extrabold">Daftar Perangkat Portable SMS</h1>
                  <a href="{{ route('portable-device.create') }}" class="bg-fertimads-2 text-white py-1.5 px-5 rounded-md">Tambah Perangkat Portable</a>
              </div>
          </div>
          <div class="grid grid-flow-row grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
              @forelse ($portableDevices as $portableDevice)
                  <div class="flex flex-col gap-y-2 bg-white rounded-md overflow-hidden p-4">
                      <a href="{{ route('portable-device.show', $portableDevice->id) }}">
                        <img src="{{ asset($portableDevice->image) }}" alt="Device Img"
                            class="w-full aspect-square object-cover rounded-md"
                            title="Klik untuk melihat detail perangkat">
                      </a>
                      <div class="h-full flex flex-col justify-between">
                          <div>
                              <span class="text-base mb-2">{{ $portableDevice->series }}</span>
                              <div class="text-xs text-slate-400">Note</div>
                              <p class="text-xs line-clamp-2 cursor-pointer" title="Klik untuk lihat deskripsi"
                                  onclick="disabledClipTest(this)">{{ $portableDevice->note }}</p>
                          </div>
                          <div class="mt-2 flex flex-row justify-between items-center">
                              <span>v{{ $portableDevice->version }}</span>
                              <div class="flex flex-row-reverse gap-2">
                                  <a href="javascript:void(0);" onclick="deleteData({{ $portableDevice->id }})" title="{{ __('Hapus Perangkat IoT') }}" class="text-xs text-danger">
                                      <i class="fa-solid fa-trash-can pointer-events-none"></i>
                                  </a>
                                  <a href="{{ route('portable-device.edit', $portableDevice->id) }}" title="{{ __('Edit Perangkat IoT') }}" class="text-xs text-warning">
                                      <i class="fa-solid fa-pen pointer-events-none"></i>
                                  </a>
                              </div>
                          </div>
                      </div>
                  </div>
              @empty
                  <div class="col-span-2 sm:col-span-3 md:col-span-4 lg:col-span-5 flex flex-col gap-y-2 bg-white rounded-md overflow-hidden p-4">
                      <div>
                          <p class="text-xs">{{ __('Data tidak ada') }}</p>
                      </div>
                  </div>
              @endforelse
          </div>

          @if ($portableDevices->hasPages())
              <div class="bg-white rounded-md overflow-hidden p-6">
                  {{ $portableDevices->links() }}
              </div>
          @endif
      </div>
  </div>
  @push('scripts')
      <script src="{{ asset('js/api.js') }}"></script>
      <script>
          const deleteData = async (id) => {
              const isDelete = confirm(`Hapus perangkat Portable ${name}? \n Aksi bersifat permanen.`)

              if (!isDelete) {
                return false
              }

              showLoading()

              const data = await fetchData(
                  "{{ route('portable-device.destroy', 'ID') }}".replace('ID', id),
                  {
                      method: "DELETE",
                      headers: {
                          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').attributes.content.nodeValue,
                          'Accept': 'application/json',
                      },
                  }
              );

              if (!data) {
                  hideLoading()

                  alert('Error')
                  return false
              }

              location.reload()

              return true
          }

          const disabledClipTest = e => {
              e.classList.toggle('line-clamp-2')
          }

          window.onload = () => {
              console.log('Hello World');
          }
      </script>
  @endpush
</x-app-layout>
