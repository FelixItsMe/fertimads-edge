<x-app-layout>
    <x-slot name="header">
        <h2 class="leading-tight">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('commodity.index') }}">Manajemen Komoditi</a>
                </li>
                <li class="breadcrumb-item breadcrumb-active">{{ __('Detail Komoditi') }}</li>
            </ol>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="sm:max-w-7x xl:max-w-full mx-auto sm:px-6 lg:px-8">
            @if (session()->has('commodity-success'))
                <x-alert-info class="mb-3">
                    {{ session()->get('commodity-success') }}
                </x-alert-info>
            @endif
            <div class="grid grid-flow-row grid-cols-1 md:grid-cols-3 max-md:gap-y-4 md:gap-4">
                <div class="w-full">
                    <img src="{{ asset($commodity->image ?? 'images/default/default-image.jpg') }}"
                        alt="Commodity image"
                        class="object-cover w-full aspect-square border-2 border-slate-500 sm:rounded-lg">
                </div>
                <div class="col-span-2 w-full flex flex-col gap-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="flex flex-col gap-y-4">
                            <div class="w-full">
                                <x-input-label class="text-slate-400"
                                    for="name">{{ __('Nama Komoditi') }}</x-input-label>
                                <span>{{ $commodity->name }}</span>
                            </div>
                            <div class="w-full">
                                <x-input-label class="text-slate-400"
                                    for="name">{{ __('Deskripsi Komoditi') }}</x-input-label>
                                <span>{{ $commodity->description }}</span>
                            </div>
                        </div>
                    </div>
                    @if (count($commodity->commodityPhases) == 0)
                        <a href="{{ route('commodity.phase.create', $commodity->id) }}"
                            class="py-2 px-4 bg-primary text-white rounded-lg hover:outline hover:outline-green-600">
                            Buat Fase Tumbuh
                        </a>
                    @endif
                    @if (count($commodity->commodityPhases) > 0)
                        <a href="{{ route('commodity.phase.edit', $commodity->id) }}"
                            class="py-2 px-4 bg-primary text-white rounded-lg hover:outline hover:outline-green-600">
                            Edit Fase Tumbuh
                        </a>
                    @endif
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="overflow-x-scroll">
                          <table class="w-full align-middle border-slate-400 table mb-0">
                              <thead>
                                  <tr>
                                      <th>Fase tumbuh</th>
                                      <th>Umur (Hari)</th>
                                      <th>per fase pertumbuhan (Hari)</th>
                                      <th>kc</th>
                                  </tr>
                              </thead>
                              <tbody class="table-border-bottom-0">
                                  @forelse ($commodity->commodityPhases as $commodityPhase)
                                      <tr>
                                          <td>Fase {{ $commodityPhase->phase->value }}: {{ $commodityPhase->phase->getLabelText() }}</td>
                                          <td class="text-center">{{ $commodityPhase->age }}</td>
                                          <td class="text-center">{{ $commodityPhase->growth_phase }}</td>
                                          <td class="text-center">{{ $commodityPhase->kc }}</td>
                                      </tr>
                                  @empty
                                      <tr>
                                          <td colspan="4" class="text-center">Tidak ada data</td>
                                      </tr>
                                  @endforelse
                              </tbody>
                          </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            window.onload = () => {
                console.log('Hello World');
            }
        </script>
    @endpush
</x-app-layout>
