<x-responsive-nav-link :href="route('activity-schedule.index')" :active="request()->routeIs('activity-schedule.*')">
  {{ __('Jadwal Kegiatan') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('land.index')" :active="request()->routeIs('land.*')">
  {{ __('Manajemen Lahan') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('garden.index')" :active="request()->routeIs('garden.*')">
  {{ __('Manajemen Kebun') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('commodity.index')" :active="request()->routeIs('commodity.*')">
  {{ __('Manajemen Komoditi') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('tool.index')" :active="request()->routeIs('tool.*')">
  {{ __('Manajemen Peralatan') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('device-type.index')" :active="request()->routeIs('device-type.*')">
  {{ __('Manajemen Tipe Perangkat IoT') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('device.index')" :active="request()->routeIs('device.*')">
  {{ __('Manajemen Perangkat IoT') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('aws-device.index')" :active="request()->routeIs('aws-device.*')">
  {{ __('Manajemen Perangkat AWS') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('user.index')" :active="request()->routeIs('user.*')">
  {{ __('Manajemen Anggota') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('infrastructure.index')" :active="request()->routeIs('infrastructure.*')">
  {{ __('Manajemen Infrastruktur') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('activity-log.index')" :active="request()->routeIs('activity-log.*')">
  {{ __('Log Aktivitas') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('daily-irrigation.index')" :active="request()->routeIs('daily-irrigation.*')">
  {{ __('Irigasi Harian') }}
</x-responsive-nav-link>
