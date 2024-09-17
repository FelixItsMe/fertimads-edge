<x-responsive-nav-link :href="route('head-unit.semi-auto.index')" :active="request()->routeIs('head-unit.*')">
  {{ __('Kontrol Head Unit') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('telemetry-rsc.index')" :active="request()->routeIs('telemetry-rsc.*')">
  {{ __('Data Telemetri Soil Monitoring System (SMS)') }}
</x-responsive-nav-link>
