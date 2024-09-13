<div
  class="flex-grow flex-col bg-white w-64 lg:fixed lg:top-0 lg:bottom-0 lg:left-0 lg:ml-0 lg:mr-0 max-sm:hidden overflow-y-scroll styled-scrollbars">
  <div id="app-brand" class="w-full h-16 mt-3 px-8">
    <a href="#" class="flex items-center">
      @switch(Auth::user()->role)
      @case('management')
      <img src="{{ asset('assets/logos/logo-kasamak.png') }}" alt="" srcset="" class="object-cover">
      @break

      @case('control')
      <img src="{{ asset('assets/logos/logo-kasamak.png') }}" alt="" srcset="" class="object-cover">
      @break

      @default
      <img src="{{ asset('assets/logos/logo-kasamak.png') }}" alt="" srcset=""
        class="object-cover">
      @endswitch
    </a>
  </div>
  <ul id="menu-inner"
    class="flex flex-col flex-auto items-center justify-start m-0 p-0 pt-6 relative overflow-hidden touch-auto pb-6">
    @if (Auth::user()->role != \App\Enums\UserRoleEnums::CARE->value)
    <li class="menu-item">
      <a href="{{ route('dashboard.index') }}" class="menu-link">
        <i @class([ 'menu-icon' , 'active-icon'=> request()->routeIs('dashboard.index'),
          'fa-solid',
          'fa-house',
          ]) ></i>
        <div class="text-slate-400">Dashboard</div>
      </a>
    </li>
    @endif
    @includeWhen(Auth::user()->role == \App\Enums\UserRoleEnums::MANAGEMENT->value || Auth::user()->role == 'su',
    'layouts.management-links')
    @includeWhen(Auth::user()->role == \App\Enums\UserRoleEnums::CONTROL->value || Auth::user()->role == 'su',
    'layouts.control-links')
    @includeWhen(Auth::user()->role == \App\Enums\UserRoleEnums::CARE->value || Auth::user()->role == 'su',
    'layouts.care-links')
  </ul>
  <div class="px-8 font-bold">
    Pengaturan Aplikasi
  </div>
  <ul id="menu-inner"
    class="flex flex-col flex-auto items-center justify-start m-0 p-0 pt-6 relative overflow-hidden touch-auto pb-6">
    <li class="menu-item">
      <a href="{{ route('weather.index') }}" class="menu-link">
        <i @class([ 'menu-icon' , 'active-icon'=> request()->routeIs('weather.*'),
          'fa-solid',
          'fa-gear',
          ])></i>
        <div class="text-slate-400">Cuaca</div>
      </a>
    </li>
  </ul>
  <div class="px-8 font-bold">
    Pengaturan Akun
  </div>
  <ul id="menu-inner"
    class="flex flex-col flex-auto items-center justify-start m-0 p-0 pt-6 relative overflow-hidden touch-auto pb-6">
    <li class="menu-item">
      <a href="{{ route('profile.edit') }}" class="menu-link">
        <i @class([ 'menu-icon' , 'active-icon'=> request()->routeIs('profile.edit'),
          'fa-solid',
          'fa-user',
          ])></i>
        <div class="text-slate-400">Profile</div>
      </a>
    </li>
    <li class="menu-item">
      <!-- Authentication -->
      <a href="#" class="menu-link" x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'sign-out')">
        <i @class(['menu-icon', 'text-red-500' , 'fa-solid' , 'fa-file' ])></i>
        <div class="text-slate-400">{{ __('Sign Out') }}</div>
      </a>
    </li>
  </ul>
</div>
