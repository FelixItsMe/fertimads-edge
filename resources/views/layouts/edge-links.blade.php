<li class="menu-item">
  <a href="{{ route('fix-station.index') }}" class="menu-link">
      <i @class([
          'menu-icon',
          'active-icon' => request()->routeIs('fix-station.*'),
          'fa-solid',
          'fa-cubes',
          ]) ></i>
      <div class="text-slate-400">Fix Station</div>
  </a>
</li>
<li class="menu-item">
  <a href="{{ route('cloud.index') }}" class="menu-link">
      <i @class([
          'menu-icon',
          'active-icon' => request()->routeIs('cloud.*'),
          'fa-solid',
          'fa-cubes',
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
          'fa-cubes',
          ]) ></i>
      <div class="text-slate-400">Cloud Export Logs</div>
  </a>
</li>
