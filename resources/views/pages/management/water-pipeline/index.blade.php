<x-app-layout>
  <x-slot name="header">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          {{ __('Manajemen Jalur Pipa Air') }}
      </h2>
  </x-slot>

  <div class="py-12">
      <div class="sm:max-w-7x xl:max-w-full mx-auto sm:px-6 lg:px-8 flex flex-col gap-4">
          @if (session()->has('water-pipeline-success'))
              <div class="bg-sky-500 text-white w-full p-6 sm:rounded-lg flex items-center">
                  <i class="fa-solid fa-circle-info text-3xl mr-3"></i>&nbsp;{{ session()->get('water-pipeline-success') }}
              </div>
          @endif
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-3 flex max-md:flex-col max-md:space-y-2 justify-between items-center">
                  <div>
                      <form action="" method="get">
                          <div class="relative">
                              <input type="text" name="search"
                                  class="w-full px-4 py-2 border-slate-300 rounded-xl shadow-sm focus:outline-none focus:ring focus:border-blue-300"
                                  placeholder="Cari" value="{{ request()->query('search') }}">
                              <button type="submit"
                                  class="absolute inset-y-0 right-0 px-4 py-2 text-sm text-gray-600 focus:outline-none">
                                  <i class="fa-solid fa-magnifying-glass"></i>
                              </button>
                          </div>
                      </form>
                  </div>
                  <div class="flex flex-row space-x-2">
                      <a href="{{ route('water-pipeline.create') }}"
                          class="bg-fertimads-2 text-white py-2 px-4 rounded-md">Tambah Jalur Pipa</a>
                  </div>
              </div>
              <div class="overflow-x-scroll">
                  <table class="w-full align-middle border-slate-400 table mb-0">
                      <thead>
                          <tr>
                              <th>#</th>
                              <th>Nama</th>
                              <th>Aksi</th>
                          </tr>
                      </thead>
                      <tbody class="table-border-bottom-0">
                          @forelse ($waterPipelines as $waterPipeline)
                              <tr>
                                  <td class="text-center">{{ ($waterPipelines->currentPage() - 1) * $waterPipelines->perPage() + $loop->iteration }}</td>
                                  <td class="text-center">{{ $waterPipeline->name }}</td>
                                  <td>
                                      <div class="flex flex-row justify-center space-x-2">
                                          <a href="{{ route('water-pipeline.show', $waterPipeline->id) }}" title="Detail Jalur Pipa Air"
                                              class="text-sm text-info">
                                              <i class="fa-solid fa-circle-info"></i>
                                          </a>
                                          <a href="{{ route('water-pipeline.edit', $waterPipeline->id) }}" title="Edit Jalur Pipa Air"
                                              class="text-sm text-warning">
                                              <i class="fa-solid fa-pen"></i>
                                          </a>
                                          <a href="#"
                                              onclick="deleteData({{ $waterPipeline->id }}, '{{ $waterPipeline->name }}')"
                                              title="Hapus Jalur Pipa Air" class="text-sm text-danger">
                                              <i class="fa-solid fa-trash-can"></i>
                                          </a>
                                      </div>
                                  </td>
                              </tr>
                          @empty
                              <tr>
                                  <td colspan="6" class="text-center">Tidak ada data</td>
                              </tr>
                          @endforelse
                      </tbody>
                  </table>
                  @if ($waterPipelines->hasPages())
                      <div class="p-6">
                          {{ $waterPipelines->links() }}
                      </div>
                  @endif
              </div>
          </div>
      </div>
  </div>
  @push('scripts')
      <script src="{{ asset('js/api.js') }}"></script>
      <script>
          const deleteData = async (id, name) => {
              const isDelete = confirm(`Apakah anda yakin ingin menghapus jalur pipa air ${name}?`)

              if (!isDelete) {
                  return false
              }

              const data = await fetchData(
                  "{{ route('water-pipeline.destroy', 'ID') }}".replace('ID', id), {
                      method: "DELETE",
                      headers: {
                          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').attributes.content
                              .nodeValue,
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

          window.onload = () => {
              console.log('Hello World');


          }
      </script>
  @endpush
</x-app-layout>
