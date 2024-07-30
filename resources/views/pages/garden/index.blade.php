<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Kebun') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 flex flex-col gap-4">
            @if (session()->has('garden-success'))
                <div class="bg-sky-500 text-white w-full p-6 sm:rounded-lg flex items-center">
                    <i class="fa-solid fa-circle-info text-3xl mr-3"></i>&nbsp;{{ session()->get('garden-success') }}
                </div>
            @endif
            <div class="flex flex-row gap-4">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex flex-row justify-between w-1/4">
                    <div>
                        <h5 class="text-xs text-slate-400">Total Lahan</h5>
                        <span class="font-bold">{{ $gardens->total() }}</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fa-solid fa-globe p-3 bg-primary text-white rounded-lg"></i>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex flex-row justify-between w-1/4">
                    <div>
                        <h5 class="text-xs text-slate-400">Luas Lahan (m²)</h5>
                        <span class="font-bold">{{ $sumArea }}</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fa-solid fa-globe p-3 bg-primary text-white rounded-lg"></i>
                    </div>
                </div>
                <x-card-info class="w-1/4">
                    <h5 class="text-xs text-slate-400">Total Blok</h5>
                    <span class="font-bold">0&nbsp;Blok</span>
                </x-card-info>
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 flex justify-between">
                    <h1 class="text-3xl font-extrabold">Tabel Kebun</h1>
                    <a href="{{ route('garden.create') }}" class="bg-fertimads-2 text-white py-1.5 px-5 rounded-md">Tambah Kebun</a>
                </div>
                <table class="w-full align-middle border-slate-400 table mb-0">
                    <thead>
                        <tr>
                            <th>Nama Kebun</th>
                            <th>Luas Kebun</th>
                            <th>Nama Komoditi</th>
                            <th>Koordinat Kebun</th>
                            <th>Total Blok</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse ($gardens as $garden)
                            <tr>
                                <td>{{ $garden->name }}</td>
                                <td>{{ $garden->area }}&nbsp;Hektar</td>
                                <td>{{ $garden->commodity->name }}</td>
                                <td>{{ $garden->latitude }},&nbsp;{{ $garden->longitude }},&nbsp;{{ $garden->altitude }}</td>
                                <td>{{ $garden->count_block }} Blok</td>
                                <td>
                                    <div class="flex flex-row space-x-2">
                                        <a href="{{ route('garden.show', $garden->id) }}" title="Edit Lahan" class="text-sm text-info">
                                            <i class="fa-solid fa-circle-info"></i>
                                        </a>
                                        <a href="{{ route('garden.edit', $garden->id) }}" title="Edit Lahan" class="text-sm text-warning">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        <a href="#" onclick="deleteData({{ $garden->id }})" title="Hapus Lahan" class="text-sm text-danger">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                @if ($gardens->hasPages())
                    <div class="p-6">
                        {{ $gardens->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
    @push('scripts')
        <script src="{{ asset('js/api.js') }}"></script>
        <script>
            const deleteData = async (id) => {
                const data = await fetchData(
                    "{{ route('garden.destroy', 'ID') }}".replace('ID', id),
                    {
                        method: "DELETE",
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').attributes.content.nodeValue,
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

            window.onload = () => {
                console.log('Hello World');


            }
        </script>
    @endpush
</x-app-layout>
