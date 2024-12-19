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
