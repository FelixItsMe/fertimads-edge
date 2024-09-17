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
    <h2 class="leading-tight">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <a href="{{ route('disease.index') }}">Laporan Penyakit</a>
        </li>
        <li class="breadcrumb-item breadcrumb-active">{{ __('Detail Penyakit') }}</li>
      </ol>
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="sm:max-w-7x xl:max-w-full mx-auto sm:px-6 lg:px-8">
      <a href="{{ route('disease.index') }}" class='mb-4 inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150'>Kembali</a>
      <div class="flex space-x-5">
        <div>
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 w-full">
            <img src="/{{ $disease->image }}" class="w-full aspect-square rounded-lg object-cover" alt="" style="width: 500px">
            <div class="mt-5">
              <table class="w-full">
                <tr>
                  <td class="font-bold">Nama Penyakit</td>
                  <td>:</td>
                  <td>{{ json_decode($disease->name) }}</td>
                </tr>
                <tr>
                  <td class="font-bold">Kategori</td>
                  <td>:</td>
                  <td>{{ json_decode($disease->category) }}</td>
                </tr>
              </table>
            </div>
          </div>
        </div>
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 w-1/2">
          <h2 class="gemini-header">Gejala</h2>
          @if (is_array(json_decode($disease->symptoms)))
          <ul class="gemini-list">
            @forelse (json_decode($disease->symptoms) as $symptom)
            <li class="gemini-list-item">{!! $symptom !!}</li>
            @empty
            <p>-</p>
            @endforelse
          </ul>
          @else
          <p>{!! json_decode($disease->symptoms) !!}</p>
          @endif
          <h2 class="gemini-subhead">Penyebab</h2>
          @if (is_array(json_decode($disease->cause)))
          <ul class="gemini-list">
            @forelse (json_decode($disease->cause) as $cause)
            <li class="gemini-list-item">{!! $cause !!}</li>
            @empty
            <p>-</p>
            @endforelse
          </ul>
          @else
          <p>{!! json_decode($disease->cause) !!}</p>
          @endif
          <h2 class="gemini-subhead">Pengendalian</h2>
          @if (is_array(json_decode($disease->control)))
          <ul class="gemini-list">
            @forelse (json_decode($disease->control) as $control)
            <li class="gemini-list-item">{!! $control !!}</li>
            @empty
            <p>-</p>
            @endforelse
          </ul>
          @else
          <p>{!! json_decode($disease->control) !!}</p>
          @endif
          <h2 class="gemini-subhead">Pestisida</h2>
          @if (is_array(json_decode($disease->pestisida)))
          <ul class="gemini-list">
            @forelse (json_decode($disease->pestisida) as $pestisida)
            <li class="gemini-list-item">{!! $pestisida !!}</li>
            @empty
            <p>-</p>
            @endforelse
          </ul>
          @else
          <p>{!! json_decode($disease->pestisida) !!}</p>
          @endif
          <h2 class="gemini-subhead">Cara Kerja</h2>
          @if (is_array(json_decode($disease->works_category)))
          <ul class="gemini-list">
            @forelse (json_decode($disease->works_category) as $works_category)
            <li class="gemini-list-item">{!! $works_category !!}</li>
            @empty
            <p>-</p>
            @endforelse
          </ul>
          @else
          <p>{!! json_decode($disease->works_category) !!}</p>
          @endif
          <h2 class="gemini-subhead">Golongan Senyawa Kimia</h2>
          @if (is_array(json_decode($disease->chemical)))
          <ul class="gemini-list">
            @forelse (json_decode($disease->chemical) as $chemical)
            <li class="gemini-list-item">{!! $chemical !!}</li>
            @empty
            <p>-</p>
            @endforelse
          </ul>
          @else
          <p>{!! json_decode($disease->chemical) !!}</p>
          @endif
          <h2 class="gemini-subhead">Bahan Aktif</h2>
          @if (is_array(json_decode($disease->active_materials)))
          <ul class="gemini-list">
            @forelse (json_decode($disease->active_materials) as $active_material)
            <li class="gemini-list-item">{!! $active_material !!}</li>
            @empty
            <p>-</p>
            @endforelse
          </ul>
          @else
          <p>{!! json_decode($disease->active_materials) !!}</p>
          @endif
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
