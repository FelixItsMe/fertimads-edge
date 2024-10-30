<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Tipe Perangkat IoT') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="sm:max-w-7x xl:max-w-full mx-auto sm:px-6 lg:px-8 flex flex-col gap-4">
            @if (session()->has('device-type-success'))
                <x-alert-info>
                    {{ session()->get('device-type-success') }}
                </x-alert-info>
            @endif
            <div class="flex flex-col sm:flex-row gap-4">
                <x-card-info class="w-full md:w-1/2 lg:w-1/4">
                    <h5 class="text-xs text-slate-400">Total Tipe Perangkat IoT</h5>
                    <span class="font-bold">{{ $deviceTypes->total() }}</span>
                </x-card-info>
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between">
                      <div>
                          <h1 class="text-3xl font-extrabold">Daftar Tipe Perangkat IoT</h1>
                      </div>
                      <a href="{{ route('device-type.create') }}" class="bg-fertimads-2 text-white py-1.5 px-5 rounded-md">Tambah Tipe Perangkat IoT</a>
                    </div>
                    <div class="mt-4">
                      <form action="" method="get" onchange="this.submit()">
                        <div class="grid grid-cols-1 md:grid-cols-3">
                            <x-select-input id="type" class="block mt-1 w-full rounded-xl" name="type">
                                <option value="">Semua</option>
                                @foreach (\App\Enums\DeviceTypeEnums::cases() as $deviceTypeEnum)
                                    <option value="{{ $deviceTypeEnum->value }}" @selected($deviceTypeEnum->value == request()->query('type'))>{{ $deviceTypeEnum->getLabelText() }}</option>
                                @endforeach
                            </x-select-input>
                        </div>
                      </form>
                    </div>
                </div>
            </div>
            <div class="grid grid-flow-row grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                @forelse ($deviceTypes as $deviceType)
                    <div class="flex flex-col gap-y-2 bg-white rounded-md overflow-hidden p-4">
                        <a href="{{ route('device-type.show', $deviceType->id) }}" title="Klik untuk detail tipe perangkat">
                          <img src="{{ asset($deviceType->image ?? 'images/default/default-image.jpg') }}" alt="Commodity Img"
                              class="w-full aspect-square object-cover rounded-md">
                        </a>
                        <div class="h-full flex flex-col justify-between">
                            <div>
                                <span class="text-base mb-2">{{ $deviceType->name }}</span>
                            </div>
                            <div class="mt-2 flex flex-row justify-between items-center">
                                <span>{{ $deviceType->type->getLabelText() }} v{{ $deviceType->version }}</span>
                                <div class="flex flex-row-reverse gap-2">
                                    <a href="javascript:void(0);" onclick="deleteData({{ $deviceType->id }})" title="{{ __('Hapus Tipe Perangkat IoT') }}" class="text-xs text-danger">
                                        <i class="fa-solid fa-trash-can pointer-events-none"></i>
                                    </a>
                                    <a href="{{ route('device-type.edit', $deviceType->id) }}" title="{{ __('Edit Tipe Perangkat IoT') }}" class="text-xs text-warning">
                                        <i class="fa-solid fa-pen pointer-events-none"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-2 sm:col-span-3 md:col-span-4 lg:col-span-5 flex flex-col gap-y-2 bg-white rounded-md overflow-hidden p-4">
                        <div>
                            <p class="text-xs">{{ __('Data tidak ada') }}</p>
                        </div>
                    </div>
                @endforelse
            </div>

            @if ($deviceTypes->hasPages())
                <div class="bg-white rounded-md overflow-hidden p-6">
                    {{ $deviceTypes->links() }}
                </div>
            @endif
        </div>
    </div>
    @push('scripts')
        <script src="{{ asset('js/api.js') }}"></script>
        <script>
            const deleteData = async (id) => {
                const isDelete = confirm(`Apakah anda yakin ingin menghapus tipe perangkat ${name}?`)

                if (!isDelete) {
                  return false
                }

                showLoading()

                const data = await fetchData(
                    "{{ route('device-type.destroy', 'ID') }}".replace('ID', id),
                    {
                        method: "DELETE",
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').attributes.content.nodeValue,
                            'Accept': 'application/json',
                        },
                    }
                );

                if (!data) {
                    hideLoading()

                    alert('Error')

                    return false
                }

                location.reload()

                return true
            }

            const disabledClipTest = e => {
                e.classList.toggle('line-clamp-2')
            }

            window.onload = () => {
                console.log('Hello World');
            }
        </script>
    @endpush
</x-app-layout>
