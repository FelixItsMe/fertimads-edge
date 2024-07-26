<x-app-layout>
  @push('styles')
  <style>
    .gemini-header {
      color: #333;
      border-bottom: 2px solid #ddd;
      padding-bottom: 5px;
    }

    .gemini-subhead {
      color: #666;
      margin-top: 15px;
    }

    p {
      margin: 10px 0;
    }

    .gemini-list {
      list-style: disc;
      margin-left: 20px;
    }

    .gemini-list-item {
      margin: 5px 0;
    }
  </style>
  @endpush

  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
      {{ __('Detail Gulma') }}
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="flex space-x-5">
        <div class="w-8/12">
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 w-full">
            <img src="/{{ $weeds->foto }}" class="w-full aspect-square rounded-lg object-cover" alt="">
            <div class="mt-5">
              <table class="w-full">
                <tr>
                  <td class="font-bold">Nama Gulma</td>
                  <td>:</td>
                  <td>{{ $weeds->nama_gulma }}</td>
                </tr>
                <tr>
                  <td class="font-bold">Jenis Pestisida</td>
                  <td>:</td>
                  <td>{{ $weeds->jenis_pestisida }}</td>
                </tr>
                <tr>
                  <td class="font-bold">Bahan Aktif</td>
                  <td>:</td>
                  <td>{{ $weeds->bahan_aktif }}</td>
                </tr>
                <tr>
                  <td class="font-bold">Nama Obat</td>
                  <td>:</td>
                  <td>{{ $weeds->nama_obat }}</td>
                </tr>
              </table>
            </div>
          </div>
        </div>
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 w-full">
          <h2 class="gemini-header">Deskripsi</h2>
          <p>{{ $weeds->deskripsi }}</p>

          <h2 class="gemini-header">Pengendalian</h2>
          <p>{{ $weeds->pengendalian }}</p>

          <h2 class="gemini-header">Klasifikasi Berdasarkan Cara Kerja</h2>
          <p>{{ $weeds->klasifikasi_berdasarkan_cara_kerja }}</p>

          <h2 class="gemini-subhead">Golongan Senyawa Kimia</h2>
          <p>{{ $weeds->golongan_senyawa_kimia }}</p>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
