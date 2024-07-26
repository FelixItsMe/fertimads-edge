<li class="menu-item">
  <a href="{{ route('fertilization-report.index') }}" class="menu-link">
    <i @class([ 'menu-icon' , 'active-icon'=> request()->routeIs('fertilization-report.*'),
      'fa-solid',
      'fa-mountain',
      ]) ></i>
    <div class="text-slate-400">Laporan Pupuk</div>
  </a>
</li>
<li class="menu-item">
  <a href="{{ route('harvest-report.index') }}" class="menu-link">
    <i @class([ 'menu-icon' , 'active-icon'=> request()->routeIs('garden.*'),
      'fa-solid',
      'fa-table-cells',
      ]) ></i>
    <div class="text-slate-400">Laporan Panen</div>
  </a>
</li>
<li class="menu-item">
  <a href="{{ route('pest.index') }}" class="menu-link">
    <i @class([ 'menu-icon' , 'active-icon'=> request()->routeIs('pest.*'),
      'fa-solid',
      'fa-apple-whole',
      ]) ></i>
    <div class="text-slate-400">Laporan Hama</div>
  </a>
</li>
<li class="menu-item">
  <a href="{{ route('disease.index') }}" class="menu-link">
    <i @class([ 'menu-icon' , 'active-icon'=> request()->routeIs('disease.*'),
      'fa-solid',
      'fa-apple-whole',
      ]) ></i>
    <div class="text-slate-400">Laporan Penyakit</div>
  </a>
</li>
<li class="menu-item">
  <a href="{{ route('rsc.index') }}" class="menu-link">
    <i @class([ 'menu-icon' , 'active-icon'=> request()->routeIs('rsc.*'),
      'fa-solid',
      'fa-map',
      ]) ></i>
    <div class="text-slate-400">RSC Data</div>
  </a>
</li>
