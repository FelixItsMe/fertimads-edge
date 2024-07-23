<x-app-layout>
  <x-slot name="header">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          {{ __('Log Aktivitas') }}
      </h2>
  </x-slot>

  <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 flex flex-col gap-4">
          @if (session()->has('activity-log-success'))
              <div class="bg-sky-500 text-white w-full p-6 sm:rounded-lg flex items-center">
                  <i class="fa-solid fa-circle-info text-3xl mr-3"></i>&nbsp;{{ session()->get('activity-log-success') }}
              </div>
          @endif
          <div class="grid grid-flow-row grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
              <x-card-info>
                  <h5 class="text-xs text-slate-400">Total Log</h5>
                  <span class="font-bold">{{ $activityLog->total() }}</span>
              </x-card-info>
          </div>
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-6 flex justify-between">
                  <h1 class="text-3xl font-extrabold">Tabel Log Aktivitas</h1>
                  <div></div>
              </div>
              <table class="w-full align-middle border-slate-400 table mb-0">
                  <thead>
                      <tr>
                          <th>#</th>
                          <th>Waktu</th>
                          <th>Kategori</th>
                          <th>Deskripsi</th>
                      </tr>
                  </thead>
                  <tbody class="table-border-bottom-0">
                      @forelse ($activityLog as $activity)
                          <tr>
                              <td>{{ ($activityLog->currentPage() - 1) * $activityLog->perPage() + $loop->iteration }}</td>
                              <td>{{ $activity->created_at }}</td>
                              <td>{{ ucfirst($activity->event) }}</td>
                              <td>{{ ucfirst($activity->description) }}</td>
                          </tr>
                      @empty
                          <tr>
                              <td colspan="6" class="text-center">Tidak ada data</td>
                          </tr>
                      @endforelse
                  </tbody>
              </table>
              @if ($activityLog->hasPages())
                  <div class="p-6">
                      {{ $activityLog->links() }}
                  </div>
              @endif
          </div>
      </div>
  </div>
  @push('scripts')
      <script src="{{ asset('js/api.js') }}"></script>
      <script>

          window.onload = () => {
              console.log('Hello World');


          }
      </script>
  @endpush
</x-app-layout>
