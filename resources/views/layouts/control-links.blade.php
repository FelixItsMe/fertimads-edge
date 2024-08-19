
<li class="menu-item">
    <a href="{{ route('head-unit.semi-auto.index') }}" class="menu-link">
        <i @class([
            'menu-icon',
            'active-icon' => request()->routeIs('head-unit.*'),
            'fa-solid',
            'fa-diamond',
            ]) ></i>
        <div class="text-slate-400">Kontrol Head Unit</div>
    </a>
</li>
<li class="menu-item">
    <a href="{{ route('telemetry-rsc.index') }}" class="menu-link">
        <i @class([
            'menu-icon',
            'active-icon' => request()->routeIs('telemetry-rsc.*'),
            'fa-solid',
            'fa-diamond',
            ]) ></i>
        <div class="text-slate-400">Data Telemetri RSC</div>
    </a>
</li>
