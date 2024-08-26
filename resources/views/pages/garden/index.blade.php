<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Kebun') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="sm:max-w-7x xl:max-w-full mx-auto sm:px-6 lg:px-8 flex flex-col gap-4">
            @if (session()->has('garden-success'))
                <div class="bg-sky-500 text-white w-full p-6 sm:rounded-lg flex items-center">
                    <i class="fa-solid fa-circle-info text-3xl mr-3"></i>&nbsp;{{ session()->get('garden-success') }}
                </div>
            @endif
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex flex-row justify-between">
                    <div>
                        <h5 class="text-xs text-slate-400">Total Kebun</h5>
                        <span class="font-bold">{{ $gardens->total() }}</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fa-solid fa-globe p-3 bg-primary text-white rounded-lg"></i>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex flex-row justify-between">
                    <div>
                        <h5 class="text-xs text-slate-400">Total Luas Kebun (m²)</h5>
                        <span class="font-bold">{{ $sums->total_area }}</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fa-solid fa-globe p-3 bg-primary text-white rounded-lg"></i>
                    </div>
                </div>
                <x-card-info>
                    <h5 class="text-xs text-slate-400">Total Blok</h5>
                    <span class="font-bold">{{ $sums->total_block }}&nbsp;Blok</span>
                </x-card-info>
                <x-card-info>
                    <h5 class="text-xs text-slate-400">Total Populasi</h5>
                    <span class="font-bold">{{ $sums->total_population }}</span>
                </x-card-info>
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-3 flex justify-between items-center">
                    <div>
                      <form action="" method="get">
                        <div class="relative">
                          <input type="text" name="search" class="w-full px-4 py-2 border-slate-300 rounded-xl shadow-sm focus:outline-none focus:ring focus:border-blue-300"
                            placeholder="Cari" value="{{ request()->query('search') }}">
                          <button type="submit" class="absolute inset-y-0 right-0 px-4 py-2 text-sm text-gray-600 focus:outline-none">
                            <i class="fa-solid fa-magnifying-glass"></i>
                          </button>
                        </div>
                      </form>
                    </div>
                    <div>
                      <a href="{{ route('garden.create') }}" class="bg-fertimads-2 text-white py-2 px-4 rounded-md">Tambah Kebun</a>
                      <a href="{{ route('garden.export-excel') }}" target="_blank"
                        class="bg-green-500 text-white py-2 px-4 rounded-md text-center">Export Excel</a>
                    </div>
                </div>
                <div class="overflow-x-scroll">
                  <table class="w-full align-middle border-slate-400 table mb-0">
                      <thead>
                          <tr>
                              <th>Nama Kebun</th>
                              <th>Luas Kebun (m²)</th>
                              <th>Nama Komoditi</th>
                              <th>Koordinat Kebun</th>
                              <th>Altitude (mdpl)</th>
                              <th>Total Blok</th>
                              <th>Aksi</th>
                          </tr>
                      </thead>
                      <tbody class="table-border-bottom-0">
                          @forelse ($gardens as $garden)
                              <tr>
                                  <td>{{ $garden->name }}</td>
                                  <td>{{ $garden->area }}&nbsp;m²</td>
                                  <td>{{ $garden->commodity->name }}</td>
                                  <td>{{ $garden->latitude }},&nbsp;{{ $garden->longitude }}</td>
                                  <td>{{ $garden->altitude }}&nbsp;mdpl</td>
                                  <td>{{ $garden->count_block }} Blok</td>
                                  <td>
                                      <div class="flex flex-row space-x-2">
                                          <a href="{{ route('garden.show', $garden->id) }}" title="Detail Kebun" class="text-sm text-info">
                                              <i class="fa-solid fa-circle-info"></i>
                                          </a>
                                          <a href="{{ route('garden.edit', $garden->id) }}" title="Edit Kebun" class="text-sm text-warning">
                                              <i class="fa-solid fa-pen"></i>
                                          </a>
                                          <a href="#" onclick="deleteData({{ $garden->id }}, '{{ $garden->name }}')" title="Hapus Kebun" class="text-sm text-danger">
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
                  @if ($gardens->hasPages())
                      <div class="p-6">
                          {{ $gardens->links() }}
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
                const isDelete = confirm(`Apakah anda yakin ingin menghapus kebun ${name}?`)

                if (!isDelete) {
                  return false
                }

                const data = await fetchData(
                    "{{ route('garden.destroy', 'ID') }}".replace('ID', id),
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

            window.onload = () => {
                console.log('Hello World');


            }
        </script>
    @endpush
</x-app-layout>
