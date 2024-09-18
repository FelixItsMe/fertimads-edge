<x-app-layout>
  @push('styles')
  <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />
  <style>
    #holder.hover {
      border: 10px dashed #0c0 !important;
    }
  </style>
  @endpush

  <x-slot name="header">
    <h2 class="leading-tight">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <a href="{{ route('disease.index') }}">Laporan Penyakit</a>
        </li>
        <li class="breadcrumb-item breadcrumb-active">{{ __('Tambah Penyakit') }}</li>
      </ol>
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="sm:max-w-7x xl:max-w-full mx-auto sm:px-6 lg:px-8 flex flex-col gap-4">
      @if (session()->has('user-success'))
      <div class="bg-sky-500 text-white w-full p-6 sm:rounded-lg flex items-center">
        <i class="fa-solid fa-circle-info text-3xl mr-3"></i>&nbsp;{{ session()->get('user-success') }}
      </div>
      @endif
      <form action="{{ route('disease.store') }}" method="post" enctype="multipart/form-data" id="form">
        @csrf
        <div class="flex space-x-5">
          <div class="w-1/3">
            <div class="relative h-[400px] w-[400px]">
              <div id="holder" class="z-0 absolute grid text-center place-items-center text-gray-800 bg-slate-400 top-0 bottom-0 right-0 left-0 border-2 border-dashed">
                Upload File disini <br>
                (Maksimal Ukuran foto 2mb, dengan dimensi 2048x2048, dan fomat JPEG, JPG, PNG)
              </div>
              <img id='preview_img' class="z-10 h-[400px] w-[400px] object-cover rounded-lg absolute top-0 bottom-0 right-0 left-0" src="" />
              <label class="z-50 block mt-5 absolute top-1/2 left-1/2 -translate-y-1/2 -translate-x-1/2 w-full h-full opacity-0">
                <span class="sr-only">Choose profile photo</span>
                <input type="file" onchange="loadFile(event)" name="image" class="block w-full text-sm text-slate-500
                    file:mr-4 file:py-2 file:px-4
                        file:rounded-full file:border-0
                        file:text-sm file:font-semibold
                        file:bg-violet-50 file:text-violet-700
                        hover:file:bg-violet-100
                        " />
              </label>
              <x-input-error :messages="$errors->get('image')" class="mt-2" />
            </div>
          </div>
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 w-full">
            <div class="flex space-x-5">
              <div class="w-full">
                <label for="name" class="block">Nama Penyakit</label>
                <x-text-input class="w-full" name="name" id="name"></x-text-input>
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
              </div>
              <div class="w-full">
                <label for="category" class="block">Kategori</label>
                <x-text-input class="w-full" name="category" id="category"></x-text-input>
                <x-input-error :messages="$errors->get('category')" class="mt-2" />
              </div>
            </div>
            <div class="w-full mt-5">
              <label for="symptoms" class="block">Gejala</label>
              <x-text-input type="hidden" class="w-full h-full block" name="symptoms" id="symptoms"></x-text-input>
              <div id="symptoms-editor"></div>
              <x-input-error :messages="$errors->get('symptoms')" class="mt-2" />
            </div>
            <div class="w-full mt-5">
              <label for="cause" class="block">Penyebab</label>
              <x-text-input type="hidden" class="w-full h-full block" name="cause" id="cause"></x-text-input>
              <div id="cause-editor"></div>
              <x-input-error :messages="$errors->get('cause')" class="mt-2" />
            </div>
            <div class="w-full mt-5">
              <label for="control" class="block">Pengendalian</label>
              <x-text-input type="hidden" class="w-full h-full block" name="control" id="control"></x-text-input>
              <div id="control-editor"></div>
              <x-input-error :messages="$errors->get('control')" class="mt-2" />
            </div>
            <div class="w-full mt-5">
              <label for="pestisida" class="block">Pestisida</label>
              <x-text-input class="w-full" name="pestisida" id="pestisida"></x-text-input>
              <x-input-error :messages="$errors->get('pestisida')" class="mt-2" />
            </div>
            <div class="w-full mt-5">
              <label for="works_category" class="block">Kategori Kerja</label>
              <x-text-input class="w-full" name="works_category" id="works_category"></x-text-input>
              <x-input-error :messages="$errors->get('works_category')" class="mt-2" />
            </div>
            <div class="w-full mt-5">
              <label for="chemical" class="block">Bahan Kimia</label>
              <x-text-input type="hidden" class="w-full h-full block" name="chemical" id="chemical"></x-text-input>
              <div id="chemical-editor"></div>
              <x-input-error :messages="$errors->get('chemical')" class="mt-2" />
            </div>
            <div class="w-full mt-5">
              <label for="active_materials" class="block">Bahan Aktif</label>
              <x-text-input type="hidden" class="w-full h-full block" name="active_materials" id="active_materials"></x-text-input>
              <div id="active-editor"></div>
              <x-input-error :messages="$errors->get('active_materials')" class="mt-2" />
            </div>
            <div class="block mt-5">
              <x-primary-button>Kirim</x-primary-button>
              <a href="{{ route('disease.index') }}" class='inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150'>Batal</a>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>

  @push('scripts')
  <!-- Script untuk memuat quill.js editor -->
  <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
  <script>
    const causeEditor = new Quill('#cause-editor', {
      theme: 'snow'
    });
    const symptomsEditor = new Quill('#symptoms-editor', {
      theme: 'snow'
    });
    const controlEditor = new Quill('#control-editor', {
      theme: 'snow'
    });
    const chemicalEditor = new Quill('#chemical-editor', {
      theme: 'snow'
    });
    const activeEditor = new Quill('#active-editor', {
      theme: 'snow'
    });

    const form = document.querySelector('#form')

    form.onsubmit = function(e) {
      document.querySelector("#symptoms").value = symptomsEditor.root.innerHTML
      document.querySelector("#cause").value = causeEditor.root.innerHTML
      document.querySelector("#control").value = controlEditor.root.innerHTML
      document.querySelector("#chemical").value = chemicalEditor.root.innerHTML
      document.querySelector("#active_materials").value = activeEditor.root.innerHTML
    }
  </script>

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
