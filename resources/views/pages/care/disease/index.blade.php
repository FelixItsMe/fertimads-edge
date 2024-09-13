<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
      {{ __('Laporan Penyakit') }}
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="sm:max-w-7x xl:max-w-full mx-auto sm:px-6 lg:px-8 flex flex-col gap-4">
      @if (session()->has('success'))
      <div class="bg-sky-500 text-white w-full p-6 sm:rounded-lg flex items-center">
        <i class="fa-solid fa-circle-info text-3xl mr-3"></i>&nbsp;{{ session()->get('success') }}
      </div>
      @endif
      <div class="grid grid-flow-row grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
        <x-card-info>
          <h5 class="text-xs text-slate-400">Total Penyakit Terdata</h5>
          <span class="font-bold">{{ $diseases->total() }}</span>
        </x-card-info>
      </div>
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 flex justify-between">
          <h1 class="text-3xl font-extrabold">Tabel Laporan Penyakit</h1>
          <div class="flex space-x-3">
            <a href="{{ route('disease.create') }}" class="bg-indigo-500 text-white py-1.5 px-5 rounded-md">Tambah Data</a>
            @if ($diseases->total() > 0)
            <a href="{{ route('disease-report.export') }}" class="bg-fertimads-2 text-white py-1.5 px-5 rounded-md"><i class="fa-regular fa-file-excel"></i> Excel</a>
            <a href="{{ route('disease-report.export-pdf') }}" target="_blank" class="bg-red-500 text-white py-1.5 px-5 rounded-md"><i class="fa-regular fa-file-pdf"></i> PDF</a>
            @endif
          </div>
        </div>
        <div class="overflow-x-scroll">
          <table class="w-full border-slate-400 table mb-0 text-left">
            <thead>
              <tr>
                <th>Waktu</th>
                <th>Nama Penyakit</th>
                <th>Kelompok Penyakit</th>
                <th>Jenis Pestisida</th>
                <th>Cara Kerja</th>
                <th>Golongan Senyawa Kimia</th>
                <th>Bahan Aktif</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody class="table-border-bottom-0">
              @forelse ($diseases as $disease)
              <tr>
                <td>{{ $disease->created_at->format('d M Y H:i:s') }}</td>
                <td>{{ $disease->name }}</td>
                <td>{{ $disease->category }}</td>
                <td>{{ $disease->pestisida }}</td>
                <td>{{ $disease->works_category }}</td>
                <td>{!! $disease->chemical !!}</td>
                <td>{!! $disease->active_materials !!}</td>
                <td>
                  <div class="flex flex-row space-x-2">
                    <a href="{{ route('disease.show', $disease->id) }}" title="Lihat Penyakit" class="text-sm text-warning">
                      <i class="fa-solid fa-circle-info"></i>
                    </a>
                    <a href="#" onclick="deleteData({{ $disease->id }})" title="Hapus Penyakit" class="text-sm text-danger">
                      <i class="fa-solid fa-trash-can"></i>
                    </a>
                  </div>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="12" class="text-center">Tidak ada data</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        @if ($diseases->hasPages())
        <div class="p-6">
          {{ $diseases->links() }}
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
          "{{ route('disease.destroy', 'ID') }}".replace('ID', id), {
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
          return false;
        }

        location.reload();

        return true;
      }
    }

    window.onload = () => {
      console.log('Hello World');
    }
  </script>
  @endpush
</x-app-layout>
