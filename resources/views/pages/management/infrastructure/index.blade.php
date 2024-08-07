<x-app-layout>
  <x-slot name="header">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          {{ __('Manajemen Infrastruktur') }}
      </h2>
  </x-slot>

  <div class="py-12">
      <div class="sm:max-w-7x xl:max-w-full mx-auto sm:px-6 lg:px-8 flex flex-col gap-4">
          @if (session()->has('infrastructure-success'))
              <div class="bg-sky-500 text-white w-full p-6 sm:rounded-lg flex items-center">
                  <i class="fa-solid fa-circle-info text-3xl mr-3"></i>&nbsp;{{ session()->get('infrastructure-success') }}
              </div>
          @endif
          <div class="flex flex-col sm:flex-row gap-4">
              <x-card-info class="w-full md:w-1/2 lg:w-1/4">
                  <h5 class="text-xs text-slate-400">Total Infrastruktur</h5>
                  <span class="font-bold">{{ $infrastructures->total() }}</span>
              </x-card-info>
          </div>
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-6 flex justify-between">
                  <h1 class="text-3xl font-extrabold">List Infrastruktur</h1>
                  <a href="{{ route('infrastructure.create') }}" class="bg-fertimads-2 text-white py-1.5 px-5 rounded-md">Tambah Infrastruktur</a>
              </div>
          </div>
          <div class="grid grid-flow-row grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
              @forelse ($infrastructures as $infrastructure)
                  <div class="flex flex-col gap-y-2 bg-white rounded-md overflow-hidden p-4">
                      <img src="{{ asset($infrastructure->image ?? 'images/default/default-image.jpg') }}" alt="Commodity Img"
                          class="w-full aspect-square object-cover rounded-md">
                      <div class="h-full flex flex-col justify-between">
                          <div>
                              <span class="text-base mb-2">{{ $infrastructure->name }}</span>
                              <div class="text-xs text-slate-400">Deskripsi</div>
                              <p class="text-xs line-clamp-2 cursor-pointer" title="Klik untuk lihat deskripsi"
                                  onclick="disabledClipTest(this)">{{ $infrastructure->description }}</p>
                          </div>
                          <div class="mt-2 flex flex-row justify-between items-center">
                              <span class="text-xs text-primary">Qty:&nbsp;{{ $infrastructure->quantity }}</span>
                              <div class="flex flex-row-reverse gap-2">
                                  <a href="javascript:void(0);" onclick="deleteData({{ $infrastructure->id }})" title="{{ __('Hapus Infrastruktur') }}" class="text-xs text-danger">
                                      <i class="fa-solid fa-trash-can pointer-events-none"></i>
                                  </a>
                                  <a href="{{ route('infrastructure.edit', $infrastructure->id) }}" title="{{ __('Edit Infrastruktur') }}" class="text-xs text-warning">
                                      <i class="fa-solid fa-pen pointer-events-none"></i>
                                  </a>
                              </div>
                          </div>
                      </div>
                  </div>
              @empty
                  <div class="col-span-full text-center bg-white rounded-md overflow-hidden p-4">
                      <div>
                          <p class="text-lg">{{ __('Data tidak ada') }}</p>
                      </div>
                  </div>
              @endforelse
          </div>

          @if ($infrastructures->hasPages())
              <div class="bg-white rounded-md overflow-hidden p-6">
                  {{ $infrastructures->links() }}
              </div>
          @endif
      </div>
  </div>
  @push('scripts')
      <script src="{{ asset('js/api.js') }}"></script>
      <script>
          const deleteData = async (id) => {
              const isDelete = confirm(`Apakah anda yakin ingin menghapus infrastruktur ${name}?`)

              if (!isDelete) {
                return false
              }

              showLoading()

              const data = await fetchData(
                  "{{ route('infrastructure.destroy', 'ID') }}".replace('ID', id),
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
