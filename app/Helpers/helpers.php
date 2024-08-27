<?php

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
            return WetherWidget::first();
        });
    }
}
