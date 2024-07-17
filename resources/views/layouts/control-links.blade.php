
<li class="menu-item">
    <a href="{{ route('head-unit.manual.index') }}" class="menu-link">
        <i @class([
            'menu-icon',
            'active-icon' => request()->routeIs('head-unit.*'),
            'fa-solid',
            'fa-diamond',
            ]) ></i>
        <div class="text-slate-400">Kontrol Head Unit</div>
    </a>
</li>
