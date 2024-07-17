<x-app-layout>
    <x-slot name="header">
        <h2 class="leading-tight">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('user.index') }}">Manajemen Anggota</a>
                </li>
                <li class="breadcrumb-item breadcrumb-active">{{ __('Edit Anggota') }}</li>
            </ol>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <form action="{{ route('user.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="p-6 flex flex-col gap-2">
                        <div class="flex flex-col">
                            <div class="w-full">
                                <x-input-label for="name">{{ __('Nama') }}</x-input-label>
                                <x-text-input id="name" class="block mt-1 w-full rounded-xl" type="text"
                                    name="name" :value="$user->name" required autofocus autocomplete="name" />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>
                        </div>
                        <div class="flex flex-col">
                            <div class="w-full">
                                <x-input-label for="email">{{ __('Email') }}</x-input-label>
                                <x-text-input id="email" class="block mt-1 w-full rounded-xl" type="text"
                                    name="email" :value="$user->email" required autofocus autocomplete="email" />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>
                        </div>
                        <div class="w-full">
                            <x-input-label for="role">{{ __('Role') }}</x-input-label>
                            <x-select-input id="role" class="block mt-1 w-full rounded-xl" name="role">
                                <option value="">Pilih Role</option>
                                @foreach (\App\Enums\UserRoleEnums::cases() as $userRoleEnum)
                                    <option value="{{ $userRoleEnum->value }}" @selected($user->role == $userRoleEnum->value)>{{ $userRoleEnum->getLabelText() }}</option>
                                @endforeach
                            </x-select-input>
                            <x-input-error :messages="$errors->get('role')" class="mt-2" />
                        </div>
                        <div class="grid grid-flow-col sm:grid-flow-row sm:grid-cols-2 gap-2">
                            <div>
                                <x-input-label for="password">{{ __('Password') }} <span class="text-danger">*Tidak wajib</span></x-input-label>
                                <x-text-input id="password" class="block mt-1 w-full rounded-xl" type="password"
                                    name="password" :value="old('password')" autocomplete="password" />
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="password_confirmation ">{{ __('Password Confirm') }}</x-input-label>
                                <x-text-input id="password_confirmation " class="block mt-1 w-full rounded-xl" type="password "
                                    name="password_confirmation " :value="old('password_confirmation ')" autocomplete="password_confirmation " />
                                <x-input-error :messages="$errors->get('password_confirmation ')" class="mt-2" />
                            </div>
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

            document.addEventListener("DOMContentLoaded", () => {
                console.log("Hello World!");

            })
        </script>
    @endpush
</x-app-layout>
