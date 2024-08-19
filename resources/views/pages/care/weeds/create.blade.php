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
      {{ __('Laporan Gulma') }}
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="sm:max-w-7x xl:max-w-full mx-auto sm:px-6 lg:px-8 flex flex-col gap-4">
      @if (session()->has('user-success'))
      <div class="bg-sky-500 text-white w-full p-6 sm:rounded-lg flex items-center">
        <i class="fa-solid fa-circle-info text-3xl mr-3"></i>&nbsp;{{ session()->get('user-success') }}
      </div>
      @endif
      <form action="{{ route('weeds.store') }}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="flex space-x-5">
          <div class="w-1/3">
            <div class="relative h-[400px] w-[400px]">
              <div id="holder" class="z-0 absolute text-center grid place-items-center text-gray-800 bg-slate-400 top-0 bottom-0 right-0 left-0 border-2 border-dashed">
                Upload File disini <br>
                (Maksimal Ukuran foto 2mb, dengan dimensi 2048x2048, dan fomat JPEG, JPG, PNG)
              </div>
              <img id='preview_img' class="z-10 h-[400px] w-[400px] object-cover rounded-lg absolute top-0 bottom-0 right-0 left-0" src="" />
              <label class="z-50 block mt-5 absolute top-1/2 left-1/2 -translate-y-1/2 -translate-x-1/2 w-full h-full opacity-0">
                <span class="sr-only">Choose profile photo</span>
                <input type="file" onchange="loadFile(event)" name="foto" class="block w-full text-sm text-slate-500
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
                <label for="nama_gulma" class="block">Nama Gulma</label>
                <x-text-input class="w-full" name="nama_gulma" id="nama_gulma"></x-text-input>
                <x-input-error :messages="$errors->get('nama_gulma')" class="mt-2" />
              </div>
            </div>
            <div class="w-full mt-5">
              <label for="deskripsi" class="block">Deskripsi</label>
              <x-textarea class="w-full h-full block" name="deskripsi" id="deskripsi"></x-textarea>
              <x-input-error :messages="$errors->get('deskripsi')" class="mt-2" />
            </div>
            <div class="w-full mt-5">
              <label for="pengendalian" class="block">Pengendalian</label>
              <x-textarea class="w-full h-full block" name="pengendalian" id="pengendalian"></x-textarea>
              <x-input-error :messages="$errors->get('pengendalian')" class="mt-2" />
            </div>
            <div class="w-full mt-5">
              <label for="jenis_pestisida" class="block">Jenis Pestisida</label>
              <x-text-input class="w-full" name="jenis_pestisida" id="jenis_pestisida"></x-text-input>
              <x-input-error :messages="$errors->get('jenis_pestisida')" class="mt-2" />
            </div>
            <div class="w-full mt-5">
              <label for="klasifikasi_berdasarkan_cara_kerja" class="block">Klasifikasi Berdasarkan Cara Kerja</label>
              <x-text-input class="w-full" name="klasifikasi_berdasarkan_cara_kerja" id="klasifikasi_berdasarkan_cara_kerja"></x-text-input>
              <x-input-error :messages="$errors->get('klasifikasi_berdasarkan_cara_kerja')" class="mt-2" />
            </div>
            <div class="w-full mt-5">
              <label for="golongan_senyawa_kimia" class="block">Golongan Senyawa Kimia</label>
              <x-textarea class="w-full h-full block" name="golongan_senyawa_kimia" id="golongan_senyawa_kimia"></x-textarea>
              <x-input-error :messages="$errors->get('golongan_senyawa_kimia')" class="mt-2" />
            </div>
            <div class="w-full mt-5">
              <label for="bahan_aktif" class="block">Bahan Aktif</label>
              <x-textarea class="w-full h-full block" name="bahan_aktif" id="bahan_aktif"></x-textarea>
              <x-input-error :messages="$errors->get('bahan_aktif')" class="mt-2" />
            </div>
            <div class="w-full mt-5">
              <label for="nama_obat" class="block">Nama Obat</label>
              <x-text-input class="w-full" name="nama_obat" id="nama_obat"></x-text-input>
              <x-input-error :messages="$errors->get('nama_obat')" class="mt-2" />
            </div>
            <div class="block mt-5">
              <x-primary-button>Kirim</x-primary-button>
              <a href="{{ route('weeds.index') }}" class='inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150'>Batal</a>
            </div>
          </div>
        </div>
      </form>
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
