<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-12">
            <h2 class="text-3xl font-bold mb-3" style="color: #740101;">Sign In</h2>
            <span class="text-slate-400">Enter your email and password to sign in!</span>
        </div>

        <!-- Email Address -->
        <div>
            <x-input-label for="email">{{ __('Email') }}<span class="text-fertimads">*</span></x-input-label>
            <x-text-input id="email" class="block mt-1 px-5 w-full rounded-xl"
                type="email" name="email" :value="old('email')" required
                autofocus autocomplete="username"
                placeholder="mail@simmmple.com"
                aria-placeholder="mail@simmmple.com"/>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password">{{ __('Password') }}<span class="text-fertimads">*</span></x-input-label>

            <x-text-input id="password" class="block mt-1 px-5 w-full rounded-xl"
                            type="password"
                            name="password"
                            placeholder="Min. 8 characters"
                            aria-placeholder="Min. 8 characters"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-fertimads shadow-sm" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Keep me logged in') }}</span>
            </label>
            @if (Route::has('password.request'))
                <a class="underline text-sm text-fertimads hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('Forgot password?') }}
                </a>
            @endif
        </div>

        <div class="flex flex-col gap-1 mt-4">
            <x-primary-button class="w-full justify-center">
                {{ __('Sign in') }}
            </x-primary-button>
            <div class="w-full text-center text-fertimads">
                Policy & Privacy
            </div>
        </div>
    </form>
</x-guest-layout>
