<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
      {{ __('Laporan Pupuk') }}
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
          <h5 class="text-xs text-slate-400">Total Laporan Pemupukan</h5>
          <span class="font-bold">0</span>
        </x-card-info>
      </div>
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 flex justify-between">
          <h1 class="text-3xl font-extrabold">Tabel Laporan Pemupukan</h1>
          <a href="{{ route('pest.create') }}" class="bg-fertimads-2 text-white py-1.5 px-5 rounded-md">Export Data</a>
        </div>
        <table class="w-full border-slate-400 table mb-0 text-left">
          <thead>
            <tr>
              <th>Waktu</th>
              <th>Nama Lahan</th>
              <th>Nama Kebun</th>
              <th>Jenis Pupuk Dasar</th>
              <th>Jumlah Pupuk Dasar</th>
              <th>Jenis Pupuk Susulan</th>
              <th>Jumlah Pupuk Susulan</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody class="table-border-bottom-0">
            @forelse ([] as $pest)
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
              <td colspan="8" class="text-center">Tidak ada data</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</x-app-layout>
