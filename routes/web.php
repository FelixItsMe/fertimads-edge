<?php

use App\Enums\UserRoleEnums;
use App\Http\Controllers\Edge\CloudSettingController;
use App\Http\Controllers\Edge\FixStationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\v1\Care\ActivityScheduleController;
use App\Http\Controllers\v1\Care\DashboardController;
use App\Http\Controllers\v1\Care\DiseaseController;
use App\Http\Controllers\v1\Care\FeritilizerReportController;
use App\Http\Controllers\v1\Care\HarvestReportController;
use App\Http\Controllers\v1\Care\PestController;
use App\Http\Controllers\v1\Care\RSCDataController;
use App\Http\Controllers\v1\Care\WeedsController;
use App\Http\Controllers\v1\Management\{
    MapObjectController,
    PortableDeviceController,
    SmsGardenController,
    WaterPipelineController,
};
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/test', function () {
    $input = "12.07.28.1007,\"Lubuk Pakam I,II\"";

    // Extract the region name using regular expression
    preg_match('/"(.*?)"/', $input, $matches);

    $region = $matches[1];

    echo "Region: " . $region . "\n";
});

Route::get('/dashboard', function () {
    switch (auth()->user()->role) {
        case UserRoleEnums::MANAGEMENT->value:
            $routeName = 'dashboard.index';
            break;
        case UserRoleEnums::CONTROL->value:
            $routeName = 'head-unit.semi-auto.index';
            break;
        case UserRoleEnums::CARE->value:
            $routeName = 'care.index';
            break;

        default:
            $routeName = 'profile.edit';
            break;
    }
    if (config('edge.status') === true) {
        $routeName = 'fix-station.index';
    }
    return redirect()->route($routeName);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard-care', [DashboardController::class, 'index'])->name('care.index');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('dashboard/', [\App\Http\Controllers\v1\Management\DashboardController::class, 'index'])->name('dashboard.index');
    // Route::group([
    //     'prefix' => 'dashboard',
    // ], function () {
    // });

    Route::middleware(['roleAccess:' . UserRoleEnums::MANAGEMENT->value])
        ->prefix('management')
        ->group(function () {
            Route::get('/fix-station', [FixStationController::class, 'index'])->name('fix-station.index');
            Route::get('/fix-station/get-telemetries', [FixStationController::class, 'getTelemetries'])->name('fix-station.get-telemetries');
            Route::post('/fix-station/export-cloud', [FixStationController::class, 'storeTelemetries'])->name('fix-station.store-cloud');

            // Land Route
            Route::resource('land', \App\Http\Controllers\v1\LandController::class);
            Route::get('land/export/excel', [\App\Http\Controllers\v1\LandController::class, 'exportExcel'])->name('land.export-excel');

            Route::resource('garden', \App\Http\Controllers\v1\GardenController::class);
            Route::resource('map-object', \App\Http\Controllers\v1\Management\MapObjectController::class);
            Route::get('garden/export/excel', [\App\Http\Controllers\v1\GardenController::class, 'exportExcel'])->name('garden.export-excel');

            Route::get('sms-garden/{smsGarden}', [SmsGardenController::class, 'show'])->name('sms-garden.show');
            Route::get('sms-garden/{smsGarden}/export/excel', [SmsGardenController::class, 'exportExcel'])->name('sms-garden.export-excel');

            Route::resource('commodity', \App\Http\Controllers\v1\CommodityController::class);
            Route::get('commodity/export/excel', [\App\Http\Controllers\v1\CommodityController::class, 'exportExcel'])->name('commodity.export-excel');

            Route::resource('device-type', \App\Http\Controllers\v1\DeviceTypeController::class);

            Route::resource('device', \App\Http\Controllers\v1\DeviceController::class);

            Route::resource('commodity.phase', \App\Http\Controllers\v1\CommodityPhaseController::class)->only(['create', 'store']);
            Route::get('/commodity/{commodity}/phase/edit', [\App\Http\Controllers\v1\CommodityPhaseController::class, 'edit'])->name('commodity.phase.edit');
            Route::put('/commodity/{commodity}/phase/update', [\App\Http\Controllers\v1\CommodityPhaseController::class, 'update'])->name('commodity.phase.update');

            Route::resource('user', \App\Http\Controllers\v1\UserController::class);
            Route::get('user/export/excel', [\App\Http\Controllers\v1\UserController::class, 'exportExcel'])->name('user.export-excel');

            Route::resource('tool', \App\Http\Controllers\v1\Management\ToolController::class);
            Route::get('tool/export/excel', [\App\Http\Controllers\v1\Management\ToolController::class, 'exportExcel'])->name('tool.export-excel');

            Route::resource('infrastructure', \App\Http\Controllers\v1\Management\InfrastructureController::class);
            Route::get('infrastructure/export/excel', [\App\Http\Controllers\v1\Management\InfrastructureController::class, 'exportExcel'])->name('infrastructure.export-excel');

            Route::get('activity-schedule', [\App\Http\Controllers\v1\Management\ActivityScheduleController::class, 'index'])->name('activity-schedule.index');
            Route::get('activity-schedule/date/{date}/garden', [\App\Http\Controllers\v1\Management\ActivityScheduleController::class, 'gardensScheduleDay'])->name('activity-schedule.date');

            Route::get('activity-log', [\App\Http\Controllers\v1\Management\ActivityLogController::class, 'index'])->name('activity-log.index');

            Route::get('test/import/excel', [\App\Http\Controllers\v1\Management\ActivityLogController::class, 'indexImport'])->name('test.import.excel.index');
            Route::post('test/import/excel', [\App\Http\Controllers\v1\Management\ActivityLogController::class, 'storeImport'])->name('test.import.excel.store');

            Route::resource('daily-irrigation', \App\Http\Controllers\v1\Management\DailyIrrigationController::class)->only(['index', 'create', 'store']);

            Route::resource('aws-device', \App\Http\Controllers\v1\Management\AwsDeviceController::class)->except('show');

            Route::resource('portable-device', PortableDeviceController::class);

            Route::resource('water-pipeline', WaterPipelineController::class);
        });
    Route::get('activity-schedule/year/{year}/month/{month}', [\App\Http\Controllers\v1\Management\ActivityScheduleController::class, 'scheduleInMonth'])->name('activity-schedule.schedule-in-month');
    Route::get('activity-schedule/date/{date}/garden/{garden}', [\App\Http\Controllers\v1\Management\ActivityScheduleController::class, 'detailGardenScheduleDay'])->name('activity-schedule.detail');

    Route::middleware(['roleAccess:' . UserRoleEnums::CONTROL->value])
        ->prefix('control')
        ->group(function () {
            Route::get('head-unit/manual', [\App\Http\Controllers\v1\Control\ControlHeadUnitController::class, 'indexControlManual'])->name('head-unit.manual.index');
            Route::post('head-unit/manual', [\App\Http\Controllers\v1\Control\ControlHeadUnitController::class, 'storeControlManual'])->name('head-unit.manual.store');
            Route::get('head-unit/semi-auto', [\App\Http\Controllers\v1\Control\ControlHeadUnitController::class, 'indexControlSemiAuto'])->name('head-unit.semi-auto.index');
            Route::post('head-unit/semi-auto', [\App\Http\Controllers\v1\Control\ControlHeadUnitController::class, 'storeControlSemiAuto'])->name('head-unit.semi-auto.store');
            Route::get('head-unit/sensor', [\App\Http\Controllers\v1\Control\ControlHeadUnitController::class, 'indexControlSensor'])->name('head-unit.sensor.index');
            Route::post('head-unit/sensor', [\App\Http\Controllers\v1\Control\ControlHeadUnitController::class, 'storeControlSensor'])->name('head-unit.sensor.store');
            Route::get('head-unit/schedule-water', [\App\Http\Controllers\v1\Control\ControlHeadUnitController::class, 'indexControlScheduleWater'])->name('head-unit.schedule-water.index');
            Route::post('head-unit/schedule-water', [\App\Http\Controllers\v1\Control\ControlHeadUnitController::class, 'storeControlScheduleWater'])->name('head-unit.schedule-water.store');
            Route::put('head-unit/schedule-water/{deviceSchedule}/stop', [\App\Http\Controllers\v1\Control\ControlHeadUnitController::class, 'stopWaterSchedule'])->name('head-unit.schedule-water.stop');
            Route::get('head-unit/schedule-fertilizer', [\App\Http\Controllers\v1\Control\ControlHeadUnitController::class, 'indexControlScheduleFertilizer'])->name('head-unit.schedule-fertilizer.index');
            Route::post('head-unit/schedule-fertilizer', [\App\Http\Controllers\v1\Control\ControlHeadUnitController::class, 'storeControlScheduleFertilizer'])->name('head-unit.schedule-fertilizer.store');
            Route::delete('head-unit/schedule-fertilizer/{deviceFertilizerSchedule}', [\App\Http\Controllers\v1\Control\ControlHeadUnitController::class, 'deleteActiveFertilizerSchedule'])->name('head-unit.schedule-fertilizer.destroy');
            Route::post('head-unit/stop', [\App\Http\Controllers\v1\Control\ControlHeadUnitController::class, 'stopDevice'])->name('head-unit.stop.store');
        });

    Route::middleware(['roleAccess:' . UserRoleEnums::CARE->value . ',' . UserRoleEnums::CONTROL->value])->prefix('control')->group(function () {
        Route::get('telemetry-rsc', [\App\Http\Controllers\v1\Control\TelemetryRscController::class, 'index'])->name('telemetry-rsc.index');
        Route::get('telemetry-rsc/export/excel', [\App\Http\Controllers\v1\Control\TelemetryRscController::class, 'excelExport'])->name('telemetry-rsc.export-excel');
        Route::get('telemetry-rsc/export/excel/download', [\App\Http\Controllers\v1\Control\TelemetryRscController::class, 'downloadCompletedExport'])->name('telemetry-rsc.download-excel');
    });

    Route::middleware(['roleAccess:' . UserRoleEnums::CARE->value])->prefix('care')->group(function () {
        Route::resource('pest', PestController::class);
        Route::get('fertilization-report', [FeritilizerReportController::class, 'index'])->name('fertilization-report.index');
        Route::get('fertilization-report/export', [FeritilizerReportController::class, 'export'])->name('fertilization-report.export');
        Route::get('fertilization-report/export-pdf', [FeritilizerReportController::class, 'pdf'])->name('fertilization-report.export-pdf');
        Route::get('pest-report/export', [PestController::class, 'export'])->name('pest-report.export');
        Route::get('pest-report/export-pdf', [PestController::class, 'pdf'])->name('pest-report.export-pdf');
        Route::get('disease-report/export', [DiseaseController::class, 'export'])->name('disease-report.export');
        Route::get('disease-report/export-pdf', [DiseaseController::class, 'pdf'])->name('disease-report.export-pdf');
        Route::get('weeds-report/export', [WeedsController::class, 'export'])->name('weeds-report.export');
        Route::get('weeds-report/export-pdf', [WeedsController::class, 'pdf'])->name('weeds-report.export-pdf');

        Route::get('harvest-report', [HarvestReportController::class, 'index'])->name('harvest-report.index');

        Route::resource('rsc', RSCDataController::class);
        Route::resource('disease', DiseaseController::class);
        Route::resource('weeds', WeedsController::class);
    });

    Route::prefix('setting')
        ->group(function () {
            Route::get('weather', [\App\Http\Controllers\v1\Setting\WeatherController::class, 'index'])->name('weather.index');
            Route::put('weather', [\App\Http\Controllers\v1\Setting\WeatherController::class, 'update'])->name('weather.update');

            Route::get('region-code', [\App\Http\Controllers\v1\Setting\RegionCodeController::class, 'index'])->name('region-code.index');
            Route::get('region-code/create', [\App\Http\Controllers\v1\Setting\RegionCodeController::class, 'create'])->name('region-code.create');
            // Route::post('region-code', [\App\Http\Controllers\v1\Setting\RegionCodeController::class, 'store'])->name('region-code.store');
            Route::get('region-code/{regionCode}', [\App\Http\Controllers\v1\Setting\RegionCodeController::class, 'show'])->name('region-code.show');

            Route::get('cloud', [CloudSettingController::class, 'index'])->name('cloud.index');
            Route::put('cloud', [CloudSettingController::class, 'update'])->name('cloud.update');
        });

    // extra to get data
    Route::prefix('extra')->name('extra.')->group(function () {
        // garden
        Route::get('garden/list', [\App\Http\Controllers\v1\GardenController::class, 'listGardensName'])->name('garden.list');
        Route::get('garden/{garden}/modal', [\App\Http\Controllers\v1\GardenController::class, 'gardenModal'])->name('garden.modal');
        Route::get('garden/{garden}/active-water-schedule', [\App\Http\Controllers\v1\GardenController::class, 'activeWaterSchedules'])->name('garden.active-water-schedule');

        // device
        Route::get('device-selenoid/{deviceSelenoid}/sensor', [\App\Http\Controllers\v1\DeviceSelenoidController::class, 'selenoidSensor'])->name('device-selenoid.sensor');

        Route::get('schedule/fertilizer/active', [\App\Http\Controllers\v1\Control\ControlHeadUnitController::class, 'activeFertilizerSchedules'])->name('schedule.fertilizer.active');

        Route::get('get-land/{land}', [\App\Http\Controllers\v1\LandController::class, 'getLand'])->name('land.get-land');
        Route::get('get-land-polygon-with-garden/{land}', [\App\Http\Controllers\v1\LandController::class, 'getLandPolygonWithGardens'])->name('land.get-land-polygon');
        Route::get('land/polygon/garden', [\App\Http\Controllers\v1\LandController::class, 'landsPolyWithGardensPoly'])->name('land.polygon.garden');
        Route::get('garden/{garden}/latest-telemetry', [App\Http\Controllers\Api\v1\GardenController::class, 'gardenLatestTelemetry'])->name('garden.latest-telemetry');
        Route::get('activity-schedule', [\App\Http\Controllers\v1\Management\ActivityScheduleController::class, 'index'])->name('activity-schedule.index');
        Route::get('activity-schedule/year/{year}/month/{month}', [ActivityScheduleController::class, 'scheduleInMonth'])->name('activity-schedule.schedule-in-month');
        Route::get('activity-schedule/date/{date}', [ActivityScheduleController::class, 'detailScheduleDay'])->name('activity-schedule.date');
        Route::get('map-object-geojson', [MapObjectController::class, 'geojson'])->name('map-object.geojson');
    });
});

require __DIR__ . '/auth.php';
