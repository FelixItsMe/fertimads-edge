<x-app-layout>
  @push('styles')
  <style>
    #holder.hover {
      border: 10px dashed #0c0 !important;
    }
  </style>
  @endpush

  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
      {{ __('Manajemen Hama') }}
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 flex flex-col gap-4">
      @if (session()->has('user-success'))
      <div class="bg-sky-500 text-white w-full p-6 sm:rounded-lg flex items-center">
        <i class="fa-solid fa-circle-info text-3xl mr-3"></i>&nbsp;{{ session()->get('user-success') }}
      </div>
      @endif
      <form action="{{ route('pest.store') }}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="flex space-x-5">
          <div class="w-1/3">
            <div class="relative h-[400px] w-[400px]">
              <div id="holder" class="z-0 absolute grid place-items-center text-gray-800 bg-slate-400 top-0 bottom-0 right-0 left-0 border-2 border-dashed">
                Upload File disini
              </div>
              <img id='preview_img' class="z-10 h-[400px] w-[400px] object-cover rounded-lg absolute top-0 bottom-0 right-0 left-0" src="" />
              <label class="z-50 block mt-5 absolute top-1/2 left-1/2 -translate-y-1/2 -translate-x-1/2 w-full h-full opacity-0">
                <span class="sr-only">Choose profile photo</span>
                <input type="file" onchange="loadFile(event)" name="file" class="block w-full text-sm text-slate-500
                    file:mr-4 file:py-2 file:px-4
                        file:rounded-full file:border-0
                        file:text-sm file:font-semibold
                        file:bg-violet-50 file:text-violet-700
                        hover:file:bg-violet-100
                        " />
              </label>
            </div>
          </div>
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 w-full">
            <div class="flex space-x-5">
              <div class="w-full">
                <label for="" class="block">Kebun</label>
                <x-select-input class="w-full" name="garden_id" id="">
                  @foreach ($gardens as $garden)
                  <option value="{{ $garden->id }}">{{ $garden->name }}</option>
                  @endforeach
                </x-select-input>
                <x-input-error :messages="$errors->get('garden_Id')" class="mt-2" />
              </div>
              <div class="w-full">
                <label for="" class="block">Komoditi</label>
                <x-select-input class="w-full" name="commodity_id" id="">
                  @foreach ($commodities as $commodity)
                  <option value="{{ $commodity->id }}">{{ $commodity->name }}</option>
                  @endforeach
                </x-select-input>
                <x-input-error :messages="$errors->get('commodity_id')" class="mt-2" />
              </div>
              <div class="w-full">
                <label for="" class="block">Jumlah Terinfeksi</label>
                <x-text-input class="w-full" name="infected_count" id=""></x-text-input>
                <x-input-error :messages="$errors->get('infected_count')" class="mt-2" />
              </div>
            </div>
            <div class="w-full mt-5">
              <label for="" class="block">Deskripsikan Gejala</label>
              <x-textarea class="w-full h-full block" name="gemini_prompt"></x-textarea>
              <x-input-error :messages="$errors->get('gemini_prompt')" class="mt-2" />
            </div>
            <div class="block mt-5">
              <x-primary-button>Kirim</x-primary-button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
  </div>

  @push('scripts')
  <script>
    var output = document.getElementById('preview_img');

    var holder = document.getElementById('holder');

    holder.ondragover = function() {
      this.className = 'hover';
      return false;
    };
    holder.ondragend = function() {
      this.className = '';
      return false;
    };
    holder.ondrop = function(e) {
      this.className = '';
      e.preventDefault();
      output.src = URL.createObjectURL(event.dataTransfer.files[0]);
      output.onload = function() {
        URL.revokeObjectURL(output.src) // free memory
        output.style.opacity = 1;
      }
    }

    window.onload = function() {
      output.style.opacity = 0;
    }

    var loadFile = function(event) {

      var input = event.target;
      var file = input.files[0];
      var type = file.type;

      output.src = URL.createObjectURL(event.target.files[0]);
      output.onload = function() {
        URL.revokeObjectURL(output.src) // free memory
        output.style.opacity = 1;
      }
    };
  </script>
  @endpush
</x-app-layout>