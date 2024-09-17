@if (Auth::user()->role == \App\Enums\UserRoleEnums::CARE->value)
  <x-responsive-nav-link :href="route('care.index')" :active="request()->routeIs('care.*')">
    {{ __('Dashboard') }}
  </x-responsive-nav-link>
  <x-responsive-nav-link :href="route('telemetry-rsc.index')" :active="request()->routeIs('telemetry-rsc.*')">
    {{ __('Data Telemetri Soil Monitoring System (SMS)') }}
  </x-responsive-nav-link>
@endif
<x-responsive-nav-link :href="route('fertilization-report.index')" :active="request()->routeIs('fertilization-report.*')">
  {{ __('Laporan Pupuk') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('harvest-report.index')" :active="request()->routeIs('harvest-report.*')">
  {{ __('Laporan Panen') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('pest.index')" :active="request()->routeIs('pest.*')">
  {{ __('Laporan Hama') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('disease.index')" :active="request()->routeIs('disease.*')">
  {{ __('Laporan Penyakit') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('weeds.index')" :active="request()->routeIs('weeds.*')">
  {{ __('Laporan Gulma') }}
</x-responsive-nav-link>
