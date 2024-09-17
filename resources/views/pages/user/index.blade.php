<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Anggota') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="sm:max-w-7x xl:max-w-full mx-auto sm:px-6 lg:px-8 flex flex-col gap-4">
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
                <div class="p-6 flex max-md:flex-col max-md:space-y-2 justify-between">
                    <h1 class="text-3xl font-extrabold max-md:text-center">Daftar Anggota</h1>
                    <div>
                      <div class="flex flex-row space-x-2 justify-end">
                        <a href="{{ route('user.create') }}" class="bg-fertimads-2 text-white py-2 px-4 rounded-md">Tambah Anggota</a>
                        <a href="{{ route('user.export-excel') }}" target="_blank"
                          class="bg-green-500 text-white py-2 px-4 rounded-md text-center">Export Excel</a>
                      </div>
                    </div>
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
                                    <div class="flex flex-row space-x-2 justify-center">
                                        <a href="{{ route('user.edit', $user->id) }}" title="Edit Anggota" class="text-sm text-warning p-1">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        <a href="#" onclick="deleteData({{ $user->id }})" title="Hapus Anggota" class="text-sm text-danger p-1">
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
                const isDelete = confirm(`Apakah anda yakin ingin menghapus anggota ${name}?`)

                if (!isDelete) {
                  return false
                }

                showLoading()

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

                if (!data) {
                    hideLoading()

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
