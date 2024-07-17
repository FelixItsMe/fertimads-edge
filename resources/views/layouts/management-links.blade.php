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
<li class="menu-item">
    <a href="{{ route('user.index') }}" class="menu-link">
        <i @class([
            'menu-icon',
            'active-icon' => request()->routeIs('user.*'),
            'fa-solid',
            'fa-users',
            ]) ></i>
        <div class="text-slate-400">Manajemen Anggota</div>
    </a>
</li>
