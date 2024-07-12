<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 flex flex-col gap-4">
            @if (session()->has('land-success'))
                <div class="bg-sky-500 text-white w-full p-6 sm:rounded-lg flex items-center">
                    <i class="fa-solid fa-circle-info text-3xl mr-3"></i>&nbsp;{{ session()->get('land-success') }}
                </div>
            @endif
            <div class="flex flex-row gap-4">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex flex-row justify-between w-1/4">
                    <div>
                        <h5 class="text-xs text-slate-400">Total Lahan</h5>
                        <span class="font-bold">{{ $lands->total() }}</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fa-solid fa-globe p-3 bg-primary text-white rounded-lg"></i>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex flex-row justify-between w-1/4">
                    <div>
                        <h5 class="text-xs text-slate-400">Luas Lahan (Hektar)</h5>
                        <span class="font-bold">{{ $sumArea }}</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fa-solid fa-globe p-3 bg-primary text-white rounded-lg"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 flex justify-between">
                    <h1 class="text-3xl font-extrabold">Tabel Lahan</h1>
                    <a href="{{ route('land.create') }}" class="bg-fertimads-2 text-white py-1.5 px-5 rounded-md">Tambah Lahan</a>
                </div>
                <table class="w-full align-middle border-slate-400 table mb-0">
                    <thead>
                        <tr>
                            <th>Nama Lahan</th>
                            <th>Luas Lahan</th>
                            <th>Lokasi Lahan</th>
                            <th>Koordinat Lahan</th>
                            <th>Jumlah Kebun</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse ($lands as $land)
                            <tr>
                                <td>{{ $land->name }}</td>
                                <td>{{ $land->area }}</td>
                                <td>{{ Str::limit($land->address, 25) }}</td>
                                <td>{{ $land->latitude }},&nbsp;{{ $land->longitude }},&nbsp;{{ $land->altitude }}</td>
                                <td>0</td>
                                <td>
                                    <div class="flex flex-row space-x-2">
                                        <a href="{{ route('land.show', $land->id) }}" title="Edit Lahan" class="text-sm text-info">
                                            <i class="fa-solid fa-circle-info"></i>
                                        </a>
                                        <a href="{{ route('land.edit', $land->id) }}" title="Edit Lahan" class="text-sm text-warning">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        <a href="#" onclick="deleteData({{ $land->id }})" title="Hapus Lahan" class="text-sm text-danger">
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
                @if ($lands->hasPages())
                    <div class="p-6">
                        {{ $lands->links() }}
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
                    "{{ route('land.destroy', 'ID') }}".replace('ID', id),
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
