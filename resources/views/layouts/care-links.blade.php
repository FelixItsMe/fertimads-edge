<li class="menu-item">
  <a href="#" class="menu-link">
    <i class="menu-icon fa-solid fa-house"></i>
    <div class="text-slate-400">Dashboard</div>
  </a>
</li>
<li class="menu-item">
  <a href="{{ route('land.index') }}" class="menu-link">
    <i @class([ 'menu-icon' , 'active-icon'=> request()->routeIs('land.*'),
      'fa-solid',
      'fa-mountain',
      ]) ></i>
    <div class="text-slate-400">Laporan Pupuk</div>
  </a>
</li>
<li class="menu-item">
  <a href="{{ route('garden.index') }}" class="menu-link">
    <i @class([ 'menu-icon' , 'active-icon'=> request()->routeIs('garden.*'),
      'fa-solid',
      'fa-table-cells',
      ]) ></i>
    <div class="text-slate-400">Laporan Panen</div>
  </a>
</li>
<li class="menu-item">
  <a href="{{ route('pest.index') }}" class="menu-link">
    <i @class([ 'menu-icon' , 'active-icon'=> request()->routeIs('commodity.*'),
      'fa-solid',
      'fa-apple-whole',
      ]) ></i>
    <div class="text-slate-400">Laporan Hama</div>
  </a>
</li>
