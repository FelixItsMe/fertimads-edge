@if (Auth::user()->role == \App\Enums\UserRoleEnums::CARE->value)
<li class="menu-item">
  <a href="{{ route('care.index') }}" class="menu-link">
    <i @class([ 'menu-icon' , 'active-icon'=> request()->routeIs('care.index*'),
      'fa-solid',
      'fa-house',
      ]) ></i>
    <div class="text-slate-400">Dashboard</div>
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
        <div class="text-slate-400">Data Telemetri Soil Monitoring System (SMS)</div>
    </a>
</li>
@endif
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
    <i @class([ 'menu-icon' , 'active-icon'=> request()->routeIs('harvest-report.*'),
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
  <a href="{{ route('weeds.index') }}" class="menu-link">
    <i @class([ 'menu-icon' , 'active-icon'=> request()->routeIs('weeds.*'),
      'fa-solid',
      'fa-apple-whole',
      ]) ></i>
    <div class="text-slate-400">Laporan Gulma</div>
  </a>
</li>
