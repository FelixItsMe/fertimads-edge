<x-app-layout>
    <x-slot name="header">
        <h2 class="leading-tight">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('commodity.index') }}">Manajemen Komoditi</a>
                </li>
                <li class="breadcrumb-item breadcrumb-active">
                    <a href="{{ route('commodity.show', $commodity->id) }}">Detail Komoditi</a>
                </li>
                <li class="breadcrumb-item breadcrumb-active">{{ __('Tambah Fase') }}</li>
            </ol>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="sm:max-w-7x xl:max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <form action="{{ route('commodity.phase.store', $commodity->id) }}" method="POST">
                    @if ($errors)
                        {{ $errors->first() }}
                    @endif
                    @csrf
                    <div class="p-4 flex flex-col gap-2 gap-y-4">
                        @foreach (\App\Enums\PhaseEnums::cases() as $phaseEnum)
                            <div class="pb-6 flex flex-col gap-3 bg-primary p-4 rounded-lg">
                                <div class="flex flex-col border-b-2 border-white">
                                    <div class="w-full">
                                        <x-input-label for="phase-{{ $loop->iteration }}"
                                            class="text-white text-xl font-bold">
                                            {{ __('Fase') }} {{ $loop->iteration }}: {{ $phaseEnum->getLabelText() }}
                                        </x-input-label>
                                    </div>
                                </div>
                                <div class="grid grid-flow-row grid-cols-1 sm:grid-cols-3 gap-2">
                                    <div class="w-full">
                                        <x-input-label for="age-{{ $loop->iteration }}" class="text-white">{{ __('Umur') }}</x-input-label>
                                        <x-text-input id="age-{{ $loop->iteration }}" data-phase="{{ $loop->iteration }}" class="block mt-1 w-full rounded-xl" type="number"
                                            min="0" onchange="calculateGrowth(this)" onkeyup="calculateGrowth(this)"
                                            name="phase[{{ $phaseEnum->value }}][age]" :value="old('age')" required autocomplete="age" />
                                        <x-input-error :messages="$errors->get('age')" class="mt-2" />
                                    </div>
                                    <div class="w-full">
                                        <x-input-label for="growth-phase-{{ $loop->iteration }}" class="text-white">{{ __('per Fase Pertumbuhan') }}</x-input-label>
                                        <x-text-input id="growth-phase-{{ $loop->iteration }}" data-phase="{{ $loop->iteration }}" class="block mt-1 w-full rounded-xl" type="number"
                                            min="0"
                                            name="phase[{{ $phaseEnum->value }}][growth_phase]" :value="old('growth_phase')" required autocomplete="growth_phase" />
                                        <x-input-error :messages="$errors->get('growth_phase')" class="mt-2" />
                                    </div>
                                    <div class="w-full">
                                        <x-input-label for="kc-{{ $loop->iteration }}" class="text-white">{{ __('Kc') }}</x-input-label>
                                        <x-text-input id="kc-{{ $loop->iteration }}" class="block mt-1 w-full rounded-xl" type="number"
                                            min="0" step=".01"
                                            name="phase[{{ $phaseEnum->value }}][kc]" :value="old('kc')" required autocomplete="kc" />
                                        <x-input-error :messages="$errors->get('kc')" class="mt-2" />
                                    </div>
                                </div>
                            </div>
                        @endforeach
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

            const calculateGrowth = (e) => {
                if (e.dataset.phase == 1) {
                    return 0
                }

                console.dir(e)
                const prevAge = document.querySelector(`#age-${e.dataset.phase - 1}`)?.value ?? 0
                const currentAge = document.querySelector(`#${e.id}`).value

                document.querySelector(`#growth-phase-${e.dataset.phase}`).value = currentAge - prevAge
                console.log(currentAge - prevAge);
            }

            document.addEventListener("DOMContentLoaded", () => {
                console.log("Hello World!");

            })
        </script>
    @endpush
</x-app-layout>
