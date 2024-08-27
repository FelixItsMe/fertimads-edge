<?php

namespace App\Http\Controllers\v1\Setting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\Weather\UpdateWeatherRequest;
use App\Models\AwsDevice;
use App\Models\WetherWidget;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class WeatherController extends Controller
{
    public function index() : View {
        return view('pages.setting.weather.index', [
            'wetherWidget' => getWeatherWidgetMode(),
            'awsDevices' => AwsDevice::pluck('series', 'id')
        ]);
    }

    public function update(UpdateWeatherRequest $request) : RedirectResponse {
        $wetherWidget = WetherWidget::first();

        $wetherWidget->update($request->validated());

        Cache::forget('weather_widget');

        return back()->with('setting-weather-success', 'Berhasil disimpan');
    }
}
