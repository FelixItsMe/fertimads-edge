<?php

use App\Models\RegionCode;
use App\Models\WetherWidget;
use Illuminate\Support\Facades\Cache;


if (! function_exists('getWeatherWidgetMode')) {
    /**
     * Get weather widget mode from database
     *
     */
    function getWeatherWidgetMode()
    {
        return Cache::rememberForever('weather_widget', function () {
            $wetherWidget = WetherWidget::first();
            $regionCode = RegionCode::query()
                ->where('full_code', $wetherWidget->region_code)
                ->first();

            $region = [
                'name' => $regionCode?->region_name
            ];

            return (object) [
                ...$wetherWidget->toArray(),
                ...$region,
            ];
        });
    }
}
