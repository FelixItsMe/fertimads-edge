<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
      {{ __('Manajemen Objek Peta') }}
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="sm:max-w-7x xl:max-w-full mx-auto sm:px-6 lg:px-8 flex flex-col gap-4">
      @if (session()->has('map-object-success'))
      <div class="bg-sky-500 text-white w-full p-6 sm:rounded-lg flex items-center">
        <i class="fa-solid fa-circle-info text-3xl mr-3"></i>&nbsp;{{ session()->get('map-object-success') }}
      </div>
      @endif
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex flex-row justify-between">
          <div>
            <h5 class="text-xs text-slate-400">Total Objek</h5>
            <span class="font-bold">{{ $mapObjects->total() }}</span>
          </div>
          <div class="flex items-center">
            <i class="fa-solid fa-marker p-3 bg-primary text-white rounded-lg"></i>
          </div>
        </div>
      </div>
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
            <a href="{{ route('map-object.create') }}"
              class="bg-fertimads-2 text-white py-2 px-4 rounded-md">Tambah Objek</a>
          </div>
        </div>
        <div class="overflow-x-scroll">
          <table class="w-full align-middle border-slate-400 table mb-0">
            <thead>
              <tr>
                <th>Nama Objek</th>
                <th>Tipe Objek</th>
                <th>Koordinat Objek</th>
                <th class="text-left">Aksi</th>
              </tr>
            </thead>
            <tbody class="table-border-bottom-0">
              @forelse ($mapObjects as $object)
              <tr>
                <td class="text-center">{{ $object->name }}</td>
                <td class="text-center">{{ $object->type }}</td>
                <td class="text-center">{{ $object->lat }},&nbsp;{{ $object->lng }}</td>
                <td class="text-center">
                  <div class="flex flex-row space-x-2">
                    <a href="{{ route('map-object.edit', $object->id) }}" title="Edit Kebun"
                      class="text-sm text-warning">
                      <i class="fa-solid fa-pen"></i>
                    </a>
                    <a href="#"
                      onclick="deleteData({{ $object->id }}, '{{ $object->name }}')"
                      title="Hapus Kebun" class="text-sm text-danger">
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
          @if ($mapObjects->hasPages())
          <div class="p-6">
            {{ $mapObjects->links() }}
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
        "{{ route('map-object.destroy', 'ID') }}".replace('ID', id), {
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
