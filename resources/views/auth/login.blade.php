<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-12">
          <img src="{{ asset('assets/logos/Logo_IPB_University_Horizontal.png') }}" alt="logo_ipb" srcset="" class="w-full object-cover">
        </div>

        <div class="mb-12">
            <h2 class="text-3xl font-bold mb-3" style="color: #740101;">Sign In</h2>
            <span class="text-slate-400">Enter your email and password to sign in!</span>
        </div>

        <!-- Email Address -->
        <div>
            <x-input-label for="email">{{ __('Email') }}<span class="text-fertimads">*</span></x-input-label>
            <x-text-input id="email" class="block mt-1 px-5 w-full rounded-xl" type="email" name="email"
                :value="old('email')" required autofocus autocomplete="username" placeholder="mail@simmmple.com"
                aria-placeholder="mail@simmmple.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password">Password<span class="text-fertimads">*</span></x-input-label>

            <div class="relative mt-1">
              <input id="password" type="password" name="password" class="w-full px-5 py-2 border-slate-300 rounded-xl shadow-sm focus:outline-none focus:ring focus:border-blue-300" placeholder="Enter your password">
              <button type="button" onclick="togglePassword(this)" class="absolute inset-y-0 right-0 px-5 py-2 text-sm text-gray-600 focus:outline-none">
                <i class="fa-regular fa-eye" id="eye-icon"></i>
              </button>
            </div>
            {{-- <x-text-input id="password" class="block mt-1 px-5 w-full rounded-xl" type="password" name="password"
                placeholder="Min. 8 characters" aria-placeholder="Min. 8 characters" required
                autocomplete="current-password" /> --}}

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-fertimads shadow-sm"
                    name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Keep me logged in') }}</span>
            </label>
            @if (false)
                <a class="underline text-sm text-fertimads hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    href="{{ route('password.request') }}">
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
    @push('scripts')
      <script>
        const togglePassword = e => {
          const inputPassword = document.querySelector('input[name="password"]')
          const toggleIcon = document.querySelector('#eye-icon')
          console.log(inputPassword.type);

          switch (inputPassword.type) {
            case 'password':
              inputPassword.type = 'text'
              toggleIcon.classList.replace('fa-eye', 'fa-eye-slash')
              break;

            case 'text':
              inputPassword.type = 'password'
              toggleIcon.classList.replace('fa-eye-slash', 'fa-eye')
              break;

            default:
              inputPassword.type = 'password'
              break;
          }
        }
      </script>
    @endpush
</x-guest-layout>
