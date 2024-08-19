<x-app-layout>
  <x-slot name="header">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          {{ __('Manajemen Perangkat AWS') }}
      </h2>
  </x-slot>

  <div class="py-12">
      <div class="sm:max-w-7x xl:max-w-full mx-auto sm:px-6 lg:px-8 flex flex-col gap-4">
          @if (session()->has('device-aws-success'))
              <x-alert-info>
                  {{ session()->get('device-aws-success') }}
              </x-alert-info>
          @endif
          <div class="flex flex-col sm:flex-row gap-4">
              <x-card-info class="w-full md:w-1/2 lg:w-1/4">
                  <h5 class="text-xs text-slate-400">Total Perangkat AWS</h5>
                  <span class="font-bold">1</span>
              </x-card-info>
          </div>
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-6 flex justify-between">
                  <h1 class="text-3xl font-extrabold">Daftar Perangkat AWS</h1>
                  <a href="#" class="bg-fertimads-2 text-white py-1.5 px-5 rounded-md">Tambah Perangkat AWS Baru</a>
              </div>
          </div>
          <div class="grid grid-flow-row grid-cols-2 sm:grid-cols-3 md:grid-cols-3 gap-4">
              <div class="grid grid-cols-2 gap-2 bg-white rounded-md overflow-hidden p-4">
                  <img src="{{ asset('images/default/default-image.jpg') }}" alt="Device Img"
                      class="w-full aspect-square object-cover rounded-md">
                  <div class="h-full flex flex-col justify-between">
                      <div class="flex flex-col text-sm font-light">
                          <div class="grid grid-cols-3">
                            <div class="col-span-2">Suhu</div>
                            <div>25°</div>
                          </div>
                          <div class="grid grid-cols-3">
                            <div class="col-span-2">Kelembapan</div>
                            <div>25%</div>
                          </div>
                          <div class="grid grid-cols-3">
                            <div class="col-span-2">Kecepatan Angin</div>
                            <div>25 km/h North</div>
                          </div>
                          <div class="grid grid-cols-3">
                            <div class="col-span-2">Curah Hujan</div>
                            <div>25%</div>
                          </div>
                          <div class="grid grid-cols-3">
                            <div class="col-span-2">Suhu Tertinggi</div>
                            <div>25°</div>
                          </div>
                          <div class="grid grid-cols-3">
                            <div class="col-span-2">Suhu Terendah</div>
                            <div>25°</div>
                          </div>
                          <div class="mt-3">104,0183419, 6,810204</div>
                      </div>
                      <div class="mt-2 flex flex-row justify-between items-center">
                          <span class="font-light">AWS 001</span>
                          <div class="flex flex-row-reverse gap-2">
                              <a href="javascript:void(0);" onclick="deleteData(1)" title="{{ __('Hapus Perangkat AWS') }}" class="text-xs text-danger">
                                  <i class="fa-solid fa-trash-can pointer-events-none"></i>
                              </a>
                              <a href="#" title="{{ __('Edit Perangkat AWS') }}" class="text-xs text-warning">
                                  <i class="fa-solid fa-pen pointer-events-none"></i>
                              </a>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </div>
  @push('scripts')
      <script src="{{ asset('js/api.js') }}"></script>
      <script>
          const deleteData = async (id) => {
              const isDelete = confirm(`Apakah anda yakin ingin menghapus perangkat IoT ${name}?`)

              if (!isDelete) {
                return false
              }

              showLoading()

              const data = await fetchData(
                  "{{ route('device.destroy', 'ID') }}".replace('ID', id),
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
