<x-app-layout>
  <x-slot name="header">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          {{ __('Manajemen Peralatan') }}
      </h2>
  </x-slot>

  <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 flex flex-col gap-4">
          @if (session()->has('tool-success'))
              <div class="bg-sky-500 text-white w-full p-6 sm:rounded-lg flex items-center">
                  <i class="fa-solid fa-circle-info text-3xl mr-3"></i>&nbsp;{{ session()->get('tool-success') }}
              </div>
          @endif
          <div class="flex flex-col sm:flex-row gap-4">
              <x-card-info class="w-full md:w-1/2 lg:w-1/4">
                  <h5 class="text-xs text-slate-400">Total Alat</h5>
                  <span class="font-bold">{{ $tools->total() }}</span>
              </x-card-info>
          </div>
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-6 flex justify-between">
                  <h1 class="text-3xl font-extrabold">List Peralatan</h1>
                  <a href="{{ route('tool.create') }}" class="bg-fertimads-2 text-white py-1.5 px-5 rounded-md">Tambah Alat</a>
              </div>
          </div>
          <div class="grid grid-flow-row grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
              @forelse ($tools as $tool)
                  <div class="flex flex-col gap-y-2 bg-white rounded-md overflow-hidden p-4">
                      <img src="{{ asset($tool->image ?? 'images/default/default-image.jpg') }}" alt="Commodity Img"
                          class="w-full aspect-square object-cover rounded-md">
                      <div class="h-full flex flex-col justify-between">
                          <div>
                              <span class="text-base mb-2">{{ $tool->name }}</span>
                              <div class="text-xs text-slate-400">Deskripsi</div>
                              <p class="text-xs line-clamp-2 cursor-pointer" title="Klik untuk lihat deskripsi"
                                  onclick="disabledClipTest(this)">{{ $tool->description }}</p>
                          </div>
                          <div class="mt-2 flex flex-row justify-between items-center">
                              <span class="text-xs text-primary">Qty:&nbsp;{{ $tool->quantity }}</span>
                              <div class="flex flex-row-reverse gap-2">
                                  <a href="javascript:void(0);" onclick="deleteData({{ $tool->id }})" title="{{ __('Hapus Alat') }}" class="text-xs text-danger">
                                      <i class="fa-solid fa-trash-can pointer-events-none"></i>
                                  </a>
                                  <a href="{{ route('tool.edit', $tool->id) }}" title="{{ __('Edit Alat') }}" class="text-xs text-warning">
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

          @if ($tools->hasPages())
              <div class="bg-white rounded-md overflow-hidden p-6">
                  {{ $tools->links() }}
              </div>
          @endif
      </div>
  </div>
  @push('scripts')
      <script src="{{ asset('js/api.js') }}"></script>
      <script>
          const deleteData = async (id) => {
              const data = await fetchData(
                  "{{ route('tool.destroy', 'ID') }}".replace('ID', id),
                  {
                      method: "DELETE",
                      headers: {
                          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').attributes.content.nodeValue,
                          'Accept': 'application/json',
                      },
                  }
              );

              console.log(data);

              if (!data) {
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
