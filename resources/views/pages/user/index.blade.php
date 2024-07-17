<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Anggota') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 flex flex-col gap-4">
            @if (session()->has('user-success'))
                <div class="bg-sky-500 text-white w-full p-6 sm:rounded-lg flex items-center">
                    <i class="fa-solid fa-circle-info text-3xl mr-3"></i>&nbsp;{{ session()->get('user-success') }}
                </div>
            @endif
            <div class="grid grid-flow-row grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                <x-card-info>
                    <h5 class="text-xs text-slate-400">Total Anggota</h5>
                    <span class="font-bold">{{ $users->total() }}</span>
                </x-card-info>
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 flex justify-between">
                    <h1 class="text-3xl font-extrabold">Tabel Anggota</h1>
                    <a href="{{ route('user.create') }}" class="bg-fertimads-2 text-white py-1.5 px-5 rounded-md">Tambah Anggota</a>
                </div>
                <table class="w-full align-middle border-slate-400 table mb-0">
                    <thead>
                        <tr>
                            <th>Nama Anggota</th>
                            <th>Posisi/Role</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse ($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ ucfirst($user->role) }}</td>
                                <td>
                                    <div class="flex flex-row space-x-2">
                                        <a href="{{ route('user.edit', $user->id) }}" title="Edit Lahan" class="text-sm text-warning">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        <a href="#" onclick="deleteData({{ $user->id }})" title="Hapus Lahan" class="text-sm text-danger">
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
                @if ($users->hasPages())
                    <div class="p-6">
                        {{ $users->links() }}
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
                    "{{ route('user.destroy', 'ID') }}".replace('ID', id),
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
