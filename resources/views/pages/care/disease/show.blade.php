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
      {{ __('Detail Penyakit') }}
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <a href="{{ route('disease.index') }}" class='mb-4 inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150'>Kembali</a>
      <div class="flex space-x-5">
        <div class="w-8/12">
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 w-full">
            <img src="/{{ $disease->image }}" class="w-full aspect-square rounded-lg object-cover" alt="">
            <div class="mt-5">
              <table class="w-full">
                <tr>
                  <td class="font-bold">Nama Penyakit</td>
                  <td>:</td>
                  <td>{{ $disease->name }}</td>
                </tr>
                <tr>
                  <td class="font-bold">Kategori</td>
                  <td>:</td>
                  <td>{{ $disease->category }}</td>
                </tr>
                <tr>
                  <td class="font-bold">Pestisida</td>
                  <td>:</td>
                  <td>{{ $disease->pestisida }}</td>
                </tr>
                <tr>
                  <td class="font-bold">Kategori Kerja</td>
                  <td>:</td>
                  <td>{{ $disease->works_category }}</td>
                </tr>
              </table>
            </div>
          </div>
        </div>
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
          <h2 class="gemini-header">Gejala</h2>
          @if (is_array($disease->symptoms))
          <ul class="gemini-list">
            @foreach ($disease->symptoms as $symptom)
            <li class="gemini-list-item">{{ $symptom }}</li>
            @endforeach
          </ul>
          @else
          <p>{{ $disease->symptoms }}</p>
          @endif
          <h2 class="gemini-subhead">Penyebab</h2>
          @if (is_array($disease->cause))
          <ul class="gemini-list">
            @foreach ($disease->cause as $cause)
            <li class="gemini-list-item">{{ $cause }}</li>
            @endforeach
          </ul>
          @else
          <p>{{ $disease->cause }}</p>
          @endif
          <h2 class="gemini-subhead">Pengendalian</h2>
          @if (is_array($disease->control))
          <ul class="gemini-list">
            @foreach ($disease->control as $control)
            <li class="gemini-list-item">{{ $control }}</li>
            @endforeach
          </ul>
          @else
          <p>{{ $disease->control }}</p>
          @endif
          <h2 class="gemini-subhead">Bahan Kimia</h2>
          @if (is_array($disease->chemical))
          <ul class="gemini-list">
            @foreach ($disease->chemical as $chemical)
            <li class="gemini-list-item">{{ $chemical }}</li>
            @endforeach
          </ul>
          @else
          <p>{{ $disease->chemical }}</p>
          @endif
          <h2 class="gemini-subhead">Bahan Aktif</h2>
          @if (is_array($disease->active_materials))
          <ul class="gemini-list">
            @foreach ($disease->active_materials as $active_material)
            <li class="gemini-list-item">{{ $active_material }}</li>
            @endforeach
          </ul>
          @else
          <p>{{ $disease->active_materials }}</p>
          @endif
          <h2 class="gemini-subhead">Nama Obat</h2>
          <p>{{ $disease->cure_name }}</p>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
