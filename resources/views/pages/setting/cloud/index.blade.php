<x-app-layout>
    <x-slot name="header">
        <h2 class="leading-tight">
            <ol class="breadcrumb">
                <li class="breadcrumb-item breadcrumb-active">{{ __('Cuaca') }}</li>
            </ol>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="sm:max-w-7x xl:max-w-full mx-auto sm:px-6 lg:px-8">
            @if (session()->has('cloud-setting-success'))
                <div class="bg-sky-500 text-white w-full p-6 sm:rounded-lg flex items-center mb-4">
                    <i
                        class="fa-solid fa-circle-info text-3xl mr-3"></i>&nbsp;{{ session()->get('cloud-setting-success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <form action="{{ route('cloud.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="p-6 flex flex-col gap-2">
                        <!-- Validation Errors -->
                        <x-validation-errors class="mb-3 text-danger" :errors="$errors" />

                        <div>
                            <x-input-label for="url">{{ __('URL') }}</x-input-label>
                            <x-text-input id="url" class="block mt-1 w-full rounded-xl" type="text"
                                name="url" :value="$cloudSetting->url" />
                            <x-input-error :messages="$errors->get('url')" class="mt-2" />
                        </div>
                        <div class="flex justify-between mt-3">
                            <h4>Headers</h4>
                            <x-primary-button type="button" id="add-new-header">
                                {{ __('Tambah Header') }}
                            </x-primary-button>
                        </div>
                        <div class="flex flex-col gap-2" id="headers-place">
                        </div>
                        <div class="flex flex-col">
                            <div class="w-full flex justify-end">
                                <x-primary-button>
                                    {{ __('Simpan') }}
                                </x-primary-button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            let headers = @json($cloudSetting->headers)

            const inputHeader = (i, key, value) => {
                return `
                  <div class="flex flex-col items-stretch md:flex-row md:space-x-2 headers">
                    <div class="grow grid grid-cols-1 md:grid-cols-2 gap-2">
                      <div>
                          <x-input-label>{{ __('Key') }}</x-input-label>
                          <x-text-input class="block mt-1 w-full rounded-xl" type="text"
                              name="headers[${i}][key]" value="${key}" />
                      </div>
                      <div>
                          <x-input-label>{{ __('Value') }}</x-input-label>
                          <x-text-input class="block mt-1 w-full rounded-xl" type="text"
                              name="headers[${i}][value]" value="${value}" />
                      </div>
                    </div>
                    <div class="flex items-stretch">
                      <x-danger-button type="button" onClick="deleteHeader(this)">
                                Hapus
                            </x-danger-button>
                    </div>
                  </div>
                `
            }

            const deleteHeader = e => {
              console.dir(e.parentNode.parentNode.remove());
            }

            const addNewHeader = (i) => {
                const eHeader = inputHeader(i, '', '')

                document.getElementById('headers-place').insertAdjacentHTML("beforeend", eHeader)

                return i + 1
            }

            const renderHeaders = (headers) => {
                let eHeaders = ``

                let i = 0
                for (const key in headers) {
                    if (Object.prototype.hasOwnProperty.call(headers, key)) {
                        const value = headers[key];
                        eHeaders += inputHeader(i, key, value)
                        i++
                    }
                }

                document.getElementById('headers-place').innerHTML = eHeaders
            }

            document.addEventListener("DOMContentLoaded", () => {
                console.log("Hello World!");

                let globalHeaders = Object.keys(headers).length

                renderHeaders(headers)

                document.getElementById('add-new-header').addEventListener('click', e => {
                  globalHeaders = addNewHeader(globalHeaders)
                })
            })
        </script>
    @endpush
</x-app-layout>
