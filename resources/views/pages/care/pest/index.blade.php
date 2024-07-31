<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
      {{ __('Laporan Hama') }}
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 flex flex-col gap-4">
      @if (session()->has('user-success'))
      <div class="bg-sky-500 text-white w-full p-6 sm:rounded-lg flex items-center">
        <i class="fa-solid fa-circle-info text-3xl mr-3"></i>&nbsp;{{ session()->get('user-success') }}
      </div>
      @endif
      <div class="grid grid-flow-row grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
        <x-card-info>
          <h5 class="text-xs text-slate-400">Total Hama Terdata</h5>
          <span class="font-bold">{{ $pests->total() }}</span>
        </x-card-info>
      </div>
      @if (session()->has('pest-success'))
      <div class="p-4 text-sm text-green-800 rounded-lg bg-green-50" role="alert">
        <span class="font-medium">{{ session('pest-success') }}</span>
      </div>
      @endif
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 flex justify-between">
          <h1 class="text-3xl font-extrabold">Tabel Laporan Hama</h1>
          <a href="{{ route('pest.create') }}" class="bg-fertimads-2 text-white py-1.5 px-5 rounded-md">Tambah Data</a>
        </div>
        <table class="w-full border-slate-400 table mb-0 text-left">
          <thead>
            <tr>
              <th>Waktu</th>
              <th>Nama Penyakit</th>
              <th>Nama Hama</th>
              <th>Kebun</th>
              <th>Komoditi</th>
              <th>Populasi Terinfeksi</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody class="table-border-bottom-0">
            @forelse ($pests as $pest)
            <tr>
              <td>{{ $pest->created_at->format('d M Y H:i:s') }}</td>
              <td>{{ $pest->disease_name }}</td>
              <td>{{ $pest->pest_name }}</td>
              <td>{{ $pest->garden->name }}</td>
              <td>{{ $pest->commodity->name }}</td>
              <td>{{ $pest->infected_count }}</td>
              <td>
                <div class="flex flex-row space-x-2">
                  <a href="{{ route('pest.show', $pest->id) }}" title="Lihat Hama" class="text-sm text-warning">
                    <i class="fa-solid fa-circle-info"></i>
                  </a>
                  <a href="#" onclick="deleteData({{ $pest->id }})" title="Hapus Hama" class="text-sm text-danger">
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
        @if ($pests->hasPages())
        <div class="p-6">
          {{ $pests->links() }}
        </div>
        @endif
      </div>
    </div>
  </div>
  @push('scripts')
  <script src="{{ asset('js/api.js') }}"></script>
  <script>
    const deleteData = async (id) => {
      if (confirm('Apakah anda yakin akan menghapus data ini?')) {
        const data = await fetchData(
          "{{ route('pest.destroy', 'ID') }}".replace('ID', id), {
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
    }

    window.onload = () => {
      console.log('Hello World');


    }
  </script>
  @endpush
</x-app-layout>
