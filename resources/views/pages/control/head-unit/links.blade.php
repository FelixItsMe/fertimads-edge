
<a href="{{ route('head-unit.manual.index') }}"
    @class([
        'bg-white' => !request()->routeIs('head-unit.manual.index'),
        'bg-primary' => request()->routeIs('head-unit.manual.index'),
        'text-white' => request()->routeIs('head-unit.manual.index'),
        'rounded-md',
        'px-4',
        'py-2',
        'text-xs',
    ])>Manual</a>
<a href="{{ route('head-unit.semi-auto.index') }}"
    @class([
        'bg-white' => !request()->routeIs('head-unit.semi-auto.index'),
        'bg-primary' => request()->routeIs('head-unit.semi-auto.index'),
        'text-white' => request()->routeIs('head-unit.semi-auto.index'),
        'rounded-md',
        'px-4',
        'py-2',
        'text-xs',
    ])>Semi-Manual</a>
<a href="{{ route('head-unit.sensor.index') }}"
    @class([
        'bg-white' => !request()->routeIs('head-unit.sensor.index'),
        'bg-primary' => request()->routeIs('head-unit.sensor.index'),
        'text-white' => request()->routeIs('head-unit.sensor.index'),
        'rounded-md',
        'px-4',
        'py-2',
        'text-xs',
    ])>Auto/Sensor</a>
<a href="{{ route('head-unit.schedule-water.index') }}"
    @class([
        'bg-white' => !request()->routeIs('head-unit.schedule-water.index') && !request()->routeIs('head-unit.schedule-fertilizer.index'),
        'bg-primary' => request()->routeIs('head-unit.schedule-water.index') || request()->routeIs('head-unit.schedule-fertilizer.index'),
        'text-white' => request()->routeIs('head-unit.schedule-water.index') || request()->routeIs('head-unit.schedule-fertilizer.index'),
        'rounded-md',
        'px-4',
        'py-2',
        'text-xs',
    ])>Schedule</a>
