<x-app-layout>
  @push('styles')
  <style>
    .gemini-header {
      font-weight: bold;
      padding-bottom: 5px;
    }

    .gemini-subhead {
      font-weight: bold;
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
      {{ __('Detail Hama & Penyakit') }}
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="sm:max-w-7x xl:max-w-full mx-auto sm:px-6 lg:px-8">
      <div class="md:flex md:space-x-5">
        <div class="md:w-4/12">
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 w-full">
            <img src="/{{ $pest->file }}" class="w-full aspect-square rounded-lg object-cover" alt="">
            <div class="mt-5">
              <table class="w-full">
                <tr>
                  <td class="font-bold">Komoditi</td>
                  <td>:</td>
                  <td>{{ $pest->commodity->name }}</td>
                </tr>
                <tr>
                  <td class="font-bold">Kebun</td>
                  <td>:</td>
                  <td>{{ $pest->garden->name }}</td>
                </tr>
                <tr>
                  <td class="font-bold">Populitas Terinfeksi</td>
                  <td>:</td>
                  <td>{{ $pest->infected_count }}</td>
                </tr>
              </table>
            </div>
          </div>
        </div>
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
          <h2 class="gemini-header">Nama Penyakit</h2>
          <p>{!! $response->nama_penyakit !!}</p>
          <h2 class="gemini-subhead">Nama Hama</h2>
          <p>{!! $response->nama_hama ?? '-' !!}</p>
          <h2 class="gemini-subhead">Gejala</h2>
          @if (gettype($response->gejala) === 'array')
          <ul class="gemini-list">
            @foreach ($response->gejala as $gejala)
            <li class="gemini-list-item">{!! $gejala !!}</li>
            @endforeach
          </ul>
          @else
          <p>{!! $response->gejala !!}</p>
          @endif
          <h2 class="gemini-subhead">Penyebab</h2>
          @if (gettype($response->penyebab) === 'array')
          <ul class="gemini-list">
            @foreach ($response->penyebab as $penyebab)
            <li class="gemini-list-item">{!! $penyebab !!}</li>
            @endforeach
          </ul>
          @else
          <p>{!! $response->penyebab !!}</p>
          @endif
          <h2 class="gemini-subhead">Pengendalian</h2>
          @if (gettype($response->pengendalian) === 'array')
          <ul class="gemini-list">
            @foreach ($response->pengendalian as $pengendalian)
            <li class="gemini-list-item">{!! $pengendalian !!}</li>
            @endforeach
          </ul>
          @else
          <p>{!! $response->pengendalian !!}</p>
          @endif
          <h2 class="gemini-subhead">Pengobatan</h2>
          @if (gettype($response->pengobatan) === 'array')
          <ul class="gemini-list">
            @foreach ($response->pengobatan as $pengobatan)
            <li class="gemini-list-item">{!! $pengobatan !!}</li>
            @endforeach
          </ul>
          @else
          <p>{!! $response->pengobatan !!}</p>
          @endif
          <h2 class="gemini-subhead">Jenis Pestisida</h2>
          @isset($response->jenis_pestisida)
          @if (gettype($response->jenis_pestisida) === 'array')
          <ul class="gemini-list">
            @foreach ($response->jenis_pestisida as $jenis_pestisida)
            <li class="gemini-list-item">{!! $jenis_pestisida !!}</li>
            @endforeach
          </ul>
          @else
          <p>{!! $response->jenis_pestisida ?? '-' !!}</p>
          @endif
          @endisset
          <h2 class="gemini-subhead">Cara Kerja</h2>
          @isset($response->cara_kerja)
          @if (gettype($response->cara_kerja) === 'array')
          <ul class="gemini-list">
            @foreach ($response->cara_kerja as $cara_kerja)
            <li class="gemini-list-item">{!! $cara_kerja !!}</li>
            @endforeach
          </ul>
          @else
          <p>{!! $response->cara_kerja ?? '-' !!}</p>
          @endif
          @endisset
          @isset ($response->senyawa_kimia)
          <h2 class="gemini-subhead">Golongan Senyawa Kimia</h2>
          @if (gettype($response->senyawa_kimia) === 'array')
          <ul class="gemini-list">
            @foreach ($response->senyawa_kimia as $senyawa_kimia)
            <li class="gemini-list-item">{!! $senyawa_kimia !!}</li>
            @endforeach
          </ul>
          @else
          <p>{!! $response->senyawa_kimia ?? '-' !!}</p>
          @endif
          @endisset
          @isset($response->bahan_aktif)
          <h2 class="gemini-subhead">Bahan Aktif</h2>
          @if (gettype($response->bahan_aktif) === 'array')
          <ul class="gemini-list">
            @foreach ($response->bahan_aktif as $bahan_aktif)
            <li class="gemini-list-item">{!! $bahan_aktif !!}</li>
            @endforeach
          </ul>
          @else
          <p>{!! $response->bahan_aktif === "" ? '-' :($response->bahan_aktif ?? '-') !!}</p>
          @endif
          @endisset
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
