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
          <span class="font-bold">{{ $reportsCount }}</span>
        </x-card-info>
      </div>
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 flex justify-between">
          <h1 class="text-3xl font-extrabold">Tabel Laporan Pemupukan</h1>
          <div class="flex space-x-3">
            <a href="{{ route('fertilization-report.export') }}" class="bg-fertimads-2 text-white py-1.5 px-5 rounded-md"><i class="fa-regular fa-file-excel"></i> Excel</a>
            <a href="{{ route('fertilization-report.export-pdf') }}" target="_blank" class="bg-red-500 text-white py-1.5 px-5 rounded-md"><i class="fa-regular fa-file-pdf"></i> PDF</a>
          </div>
        </div>
        <table class="w-full border-slate-400 table mb-0 text-left">
          <thead>
            <tr>
              <th>Tanggal</th>
              <th>Nama Lahan</th>
              <th>Nama Kebun</th>
              <th>Jenis Pupuk Dasar</th>
              <th>Jumlah Pupuk Dasar</th>
              <th>Waktu</th>
            </tr>
          </thead>
          <tbody class="table-border-bottom-0">
            @forelse ($reports as $report)
            <tr>
              <td>{{ $report->created_at->format('d M Y H:i:s') }}</td>
              <td>{{ $report->deviceSelenoid->garden->land->name }}</td>
              <td>{{ $report->deviceSelenoid->garden->name }}</td>
              <td>{{ $report->pemupukan_type }}</td>
              <td>{{ number_format($report->total_volume, 2) }} Ltr</td>
              <td>{{ $report->time_in_hours }} Jam</td>
            </tr>
            @empty
            <tr>
              <td colspan="8" class="text-center">Tidak ada data</td>
            </tr>
            @endforelse
          </tbody>
        </table>
        @if ($reports->hasPages())
        <div class="p-6">
          {{ $reports->links() }}
        </div>
        @endif
      </div>
    </div>
  </div>
</x-app-layout>
