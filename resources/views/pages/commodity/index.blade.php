<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Komoditi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="sm:max-w-7x xl:max-w-full mx-auto sm:px-6 lg:px-8 flex flex-col gap-4">
            @if (session()->has('commodity-success'))
                <div class="bg-sky-500 text-white w-full p-6 sm:rounded-lg flex items-center">
                    <i class="fa-solid fa-circle-info text-3xl mr-3"></i>&nbsp;{{ session()->get('commodity-success') }}
                </div>
            @endif
            <div class="flex flex-col sm:flex-row gap-4">
                <x-card-info class="w-full md:w-1/2 lg:w-1/4">
                    <h5 class="text-xs text-slate-400">Total Komoditi</h5>
                    <span class="font-bold">{{ $commodities->total() }}</span>
                </x-card-info>
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-3">
                <div class="flex max-md:flex-col max-md:space-y-4 md:justify-between">
                    <div class="max-md:w-full">
                        <form action="" method="get" id="form-filter">
                            <div class="relative w-fit">
                                <input type="text" name="search"
                                    class="w-full px-4 py-2 border-slate-300 rounded-xl shadow-sm focus:outline-none focus:ring focus:border-blue-300"
                                    placeholder="Cari" value="{{ request()->query('search') }}">
                                <button type="submit"
                                    class="absolute inset-y-0 right-0 px-4 py-2 text-sm text-gray-600 focus:outline-none">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                </button>
                            </div>
                            <div class="mt-3">
                                <h3>Urutan</h3>
                            </div>
                            <div class="flex flex-row space-x-2">
                                @foreach ($orderBys as $orderBy)
                                    <div>
                                        <input type="radio" id="{{ $orderBy->id }}" name="order_by"
                                            value="{{ $orderBy->value }}" class="hidden peer/garden"
                                            onchange="filterAction()" @checked(request()->query('order_by') == $orderBy->value) />
                                        <label for="{{ $orderBy->id }}"
                                            class="inline-flex w-full px-4 py-2 bg-white rounded-md shadow-md text-xs cursor-pointer peer-checked/garden:bg-primary peer-checked/garden:text-white hover:text-gray-600">
                                            {{ $orderBy->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </form>
                    </div>
                    <div>
                      <div class="max-md:w-full flex flex-row space-x-2">
                        <a href="{{ route('commodity.create') }}"
                            class="bg-fertimads-2 text-white py-2 px-4 rounded-md box-border">Tambah Komoditi</a>
                        <a href="{{ route('commodity.export-excel', ['order_by' => request()->query('order_by')]) }}"
                            target="_blank" class="bg-green-500 text-white py-2 px-4 rounded-md text-center">Export
                            Excel</a>
                      </div>
                    </div>
                </div>
            </div>
            <div class="grid grid-flow-row grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                @forelse ($commodities as $commodity)
                    <div class="flex flex-col gap-y-2 bg-white rounded-md overflow-hidden p-4">
                        <a href="{{ route('commodity.show', $commodity->id) }}">
                            <img src="{{ asset($commodity->image ?? 'images/default/default-image.jpg') }}"
                                alt="Commodity Img" class="w-full aspect-square object-cover rounded-md">
                        </a>
                        <div class="h-full flex flex-col justify-between">
                            <div>
                                <span class="text-base mb-2">{{ $commodity->name }}</span>
                                <div class="text-xs text-slate-400">Deskripsi</div>
                                <p class="text-xs line-clamp-2 cursor-pointer" title="Klik untuk lihat deskripsi"
                                    onclick="disabledClipTest(this)">{{ $commodity->description }}</p>
                            </div>
                            <div class="mt-2 flex flex-row justify-between items-center">
                                <span class="text-xs">{{ $commodity->gardens_count }} Kebun</span>
                                <div class="flex flex-row-reverse gap-2">
                                    <a href="javascript:void(0);"
                                        onclick="deleteData({{ $commodity->id }}, '{{ $commodity->name }}')"
                                        title="{{ __('Hapus Komoditi') }}" class="text-xs text-danger">
                                        <i class="fa-solid fa-trash-can pointer-events-none"></i>
                                    </a>
                                    <a href="{{ route('commodity.edit', $commodity->id) }}"
                                        title="{{ __('Edit Komoditi') }}" class="text-xs text-warning">
                                        <i class="fa-solid fa-pen pointer-events-none"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col gap-y-2 bg-white rounded-md overflow-hidden p-4">
                        <div>
                            <p class="text-xs">{{ __('Data tidak ada') }}</p>
                        </div>
                    </div>
                @endforelse
            </div>

            @if ($commodities->hasPages())
                <div class="bg-white rounded-md overflow-hidden p-6">
                    {{ $commodities->links() }}
                </div>
            @endif
        </div>
    </div>
    @push('scripts')
        <script src="{{ asset('js/api.js') }}"></script>
        <script>
            const filterAction = () => {
                document.querySelector('#form-filter').submit()
            }
            const deleteData = async (id, name) => {
                const isDelete = confirm(`Apakah anda yakin ingin menghapus komoditas ${name}?`)

                if (!isDelete) {
                    return false
                }

                const data = await fetchData(
                    "{{ route('commodity.destroy', 'ID') }}".replace('ID', id), {
                        method: "DELETE",
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').attributes.content
                                .nodeValue,
                            'Accept': 'application/json',
                        },
                    }
                );

                console.log(data);

                if (!data) {
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
