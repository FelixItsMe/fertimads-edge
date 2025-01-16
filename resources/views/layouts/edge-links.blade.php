<li class="menu-item">
  <a href="{{ route('fix-station.index') }}" class="menu-link">
      <i @class([
          'menu-icon',
          'active-icon' => request()->routeIs('fix-station.*'),
          'fa-solid',
          'fa-rectangle-list',
          ]) ></i>
      <div class="text-slate-400">Dashboard</div>
  </a>
</li>
<li class="menu-item">
  <a href="{{ route('cloud.index') }}" class="menu-link">
      <i @class([
          'menu-icon',
          'active-icon' => request()->routeIs('cloud.*'),
          'fa-solid',
          'fa-gears',
          ]) ></i>
      <div class="text-slate-400">Cloud Setting</div>
  </a>
</li>
<li class="menu-item">
  <a href="{{ route('cloud-export-log.index') }}" class="menu-link">
      <i @class([
          'menu-icon',
          'active-icon' => request()->routeIs('cloud-export-log.*'),
          'fa-solid',
          'fa-cloud-arrow-up',
          ]) ></i>
      <div class="text-slate-400">Cloud Export Logs</div>
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
    <a href="{{ route('map-object.index') }}" class="menu-link">
        <i @class([
            'menu-icon',
            'active-icon' => request()->routeIs('map-object.*'),
            'fa-solid',
            'fa-location-dot',
            ]) ></i>
        <div class="text-slate-400">Manajemen Peta</div>
    </a>
</li>
<li class="menu-item">
    <a href="{{ route('water-pipeline.index') }}" class="menu-link">
        <i @class([
            'menu-icon',
            'active-icon' => request()->routeIs('water-pipeline.*'),
            'fa-solid',
            'fa-grip-lines',
            ]) ></i>
        <div class="text-slate-400">Manajemen Jalur Pipa Air</div>
    </a>
</li>
