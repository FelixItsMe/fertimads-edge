<div class="flex-grow flex-col bg-white w-64 lg:fixed lg:top-0 lg:bottom-0 lg:left-0 lg:ml-0 lg:mr-0 max-sm:hidden">
    <div id="app-brand" class="w-full h-16 mt-3 px-8">
        <a href="#" class="flex items-center">
            <img src="{{ asset('assets/logos/logo-management.png') }}" alt="" srcset="" class="object-cover">
        </a>
    </div>
    <ul id="menu-inner" class="flex flex-col flex-auto items-center justify-start m-0 p-0 pt-6 h-full relative overflow-hidden touch-auto py-1">
        <li class="menu-item">
            <a href="#" class="menu-link">
                <i class="menu-icon fa-solid fa-house"></i>
                <div class="text-slate-400">Dashboard</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="{{ route('land.index') }}" class="menu-link">
                <i @class([
                    'menu-icon',
                    'active-icon' => request()->routeIs('land.*'),
                    'fa-solid',
                    'fa-mountain',
                    ]) ></i>
                <div class="text-slate-400">Manajemen Lahan</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="{{ route('garden.index') }}" class="menu-link">
                <i @class([
                    'menu-icon',
                    'active-icon' => request()->routeIs('garden.*'),
                    'fa-solid',
                    'fa-table-cells',
                    ]) ></i>
                <div class="text-slate-400">Manajemen Kebun</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="{{ route('commodity.index') }}" class="menu-link">
                <i @class([
                    'menu-icon',
                    'active-icon' => request()->routeIs('commodity.*'),
                    'fa-solid',
                    'fa-apple-whole',
                    ]) ></i>
                <div class="text-slate-400">Manajemen Komoditi</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="{{ route('device-type.index') }}" class="menu-link">
                <i @class([
                    'menu-icon',
                    'active-icon' => request()->routeIs('device-type.*'),
                    'fa-solid',
                    'fa-microchip',
                    ]) ></i>
                <div class="text-slate-400">Manajemen Tipe Perangkat IoT</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="{{ route('device.index') }}" class="menu-link">
                <i @class([
                    'menu-icon',
                    'active-icon' => request()->routeIs('device.*'),
                    'fa-solid',
                    'fa-cubes',
                    ]) ></i>
                <div class="text-slate-400">Manajemen Perangkat IoT</div>
            </a>
        </li>
    </ul>
</div>
