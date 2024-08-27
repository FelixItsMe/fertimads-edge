<li class="menu-item">
    <a href="{{ route('activity-schedule.index') }}" class="menu-link">
        <i @class([
            'menu-icon',
            'active-icon' => request()->routeIs('activity-schedule.*'),
            'fa-solid',
            'fa-calendar-days',
            ]) ></i>
        <div class="text-slate-400">Jadwal Kegiatan</div>
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
    <a href="{{ route('tool.index') }}" class="menu-link">
        <i @class([
            'menu-icon',
            'active-icon' => request()->routeIs('tool.*'),
            'fa-solid',
            'fa-hammer',
            ]) ></i>
        <div class="text-slate-400">Manajemen Peralatan</div>
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
    <a href="{{ route('aws-device.index') }}" class="menu-link">
        <i @class([
            'menu-icon',
            'active-icon' => request()->routeIs('aws-device.*'),
            'fa-solid',
            'fa-cloud-moon',
            ]) ></i>
        <div class="text-slate-400">Manajemen Perangkat AWS</div>
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
<li class="menu-item">
    <a href="{{ route('infrastructure.index') }}" class="menu-link">
        <i @class([
            'menu-icon',
            'active-icon' => request()->routeIs('infrastructure.*'),
            'fa-solid',
            'fa-city',
            ]) ></i>
        <div class="text-slate-400">Manajemen Infrastruktur</div>
    </a>
</li>
<li class="menu-item">
    <a href="{{ route('activity-log.index') }}" class="menu-link">
        <i @class([
            'menu-icon',
            'active-icon' => request()->routeIs('activity-log.*'),
            'fa-solid',
            'fa-clock-rotate-left',
            ]) ></i>
        <div class="text-slate-400">Log Aktivitas</div>
    </a>
</li>
<li class="menu-item">
    <a href="{{ route('daily-irrigation.index') }}" class="menu-link">
        <i @class([
            'menu-icon',
            'active-icon' => request()->routeIs('daily-irrigation.*'),
            'fa-solid',
            'fa-cloud-sun-rain',
            ]) ></i>
        <div class="text-slate-400">Irigasi Harian</div>
    </a>
</li>
