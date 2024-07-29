<?php

use App\Enums\UserRoleEnums;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\v1\Care\DiseaseController;
use App\Http\Controllers\v1\Care\FeritilizerReportController;
use App\Http\Controllers\v1\Care\HarvestReportController;
use App\Http\Controllers\v1\Care\PestController;
use App\Http\Controllers\v1\Care\RSCDataController;
use App\Http\Controllers\v1\Care\WeedsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware(['roleAccess:' . UserRoleEnums::MANAGEMENT->value])
        ->prefix('management')
        ->group(function () {
            Route::group([
                'prefix' => 'dashboard',
            ], function () {
                Route::get('/', [\App\Http\Controllers\v1\Management\DashboardController::class, 'index'])->name('dashboard.index');
            });

            // Land Route
            Route::resource('land', \App\Http\Controllers\v1\LandController::class);

            Route::resource('garden', \App\Http\Controllers\v1\GardenController::class);

            Route::resource('commodity', \App\Http\Controllers\v1\CommodityController::class);

            Route::resource('device-type', \App\Http\Controllers\v1\DeviceTypeController::class);

            Route::resource('device', \App\Http\Controllers\v1\DeviceController::class);

            Route::resource('commodity.phase', \App\Http\Controllers\v1\CommodityPhaseController::class)->only(['create', 'store']);
            Route::get('/commodity/{commodity}/phase/edit', [\App\Http\Controllers\v1\CommodityPhaseController::class, 'edit'])->name('commodity.phase.edit');
            Route::put('/commodity/{commodity}/phase/update', [\App\Http\Controllers\v1\CommodityPhaseController::class, 'update'])->name('commodity.phase.update');

            Route::resource('user', \App\Http\Controllers\v1\UserController::class);

            Route::resource('tool', \App\Http\Controllers\v1\Management\ToolController::class);
            Route::resource('infrastructure', \App\Http\Controllers\v1\Management\InfrastructureController::class);

            Route::get('activity-schedule', [\App\Http\Controllers\v1\Management\ActivityScheduleController::class, 'index'])->name('activity-schedule.index');
            Route::get('activity-schedule/year/{year}/month/{month}', [\App\Http\Controllers\v1\Management\ActivityScheduleController::class, 'scheduleInMonth'])->name('activity-schedule.schedule-in-month');
            Route::get('activity-schedule/date/{date}', [\App\Http\Controllers\v1\Management\ActivityScheduleController::class, 'detailScheduleDay'])->name('activity-schedule.date');

            Route::get('activity-log', [\App\Http\Controllers\v1\Management\ActivityLogController::class, 'index'])->name('activity-log.index');

            Route::get('test/import/excel', [\App\Http\Controllers\v1\Management\ActivityLogController::class, 'indexImport'])->name('test.import.excel.index');
            Route::post('test/import/excel', [\App\Http\Controllers\v1\Management\ActivityLogController::class, 'storeImport'])->name('test.import.excel.store');

            Route::resource('daily-irrigation', \App\Http\Controllers\v1\Management\DailyIrrigationController::class)->only(['index', 'create', 'store']);
        });

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
            Route::get('head-unit/schedule-fertilizer', [\App\Http\Controllers\v1\Control\ControlHeadUnitController::class, 'indexControlScheduleFertilizer'])->name('head-unit.schedule-fertilizer.index');
            Route::post('head-unit/schedule-fertilizer', [\App\Http\Controllers\v1\Control\ControlHeadUnitController::class, 'storeControlScheduleFertilizer'])->name('head-unit.schedule-fertilizer.store');
        });

    Route::middleware(['roleAccess:' . UserRoleEnums::CARE->value])->prefix('care')->group(function () {
        Route::resource('pest', PestController::class);
        Route::get('fertilization-report', [FeritilizerReportController::class, 'index'])->name('fertilization-report.index');
        Route::get('fertilization-report/export', [FeritilizerReportController::class, 'export'])->name('fertilization-report.export');
        Route::get('fertilization-report/export-pdf', [FeritilizerReportController::class, 'pdf'])->name('fertilization-report.export-pdf');

        Route::get('harvest-report', [HarvestReportController::class, 'index'])->name('harvest-report.index');

        Route::resource('rsc', RSCDataController::class);
        Route::resource('disease', DiseaseController::class);
        Route::resource('weeds', WeedsController::class);
    });

    // extra to get data
    Route::prefix('extra')->name('extra.')->group(function () {
        // garden
        Route::get('garden/list', [\App\Http\Controllers\v1\GardenController::class, 'listGardensName'])->name('garden.list');

        // device
        Route::get('device-selenoid/{deviceSelenoid}/sensor', [\App\Http\Controllers\v1\DeviceSelenoidController::class, 'selenoidSensor'])->name('device-selenoid.sensor');

        Route::get('get-land/{land}', [\App\Http\Controllers\v1\LandController::class, 'getLand'])->name('land.get-land');
        Route::get('get-land-polygon-with-garden/{land}', [\App\Http\Controllers\v1\LandController::class, 'getLandPolygonWithGardens'])->name('land.get-land-polygon');
        Route::get('land/polygon/garden', [\App\Http\Controllers\v1\LandController::class, 'landsPolyWithGardensPoly'])->name('land.polygon.garden');
        Route::get('garden/{garden}/latest-telemetry', [App\Http\Controllers\Api\v1\GardenController::class, 'gardenLatestTelemetry'])->name('garden.latest-telemetry');
        Route::get('activity-schedule', [\App\Http\Controllers\v1\Management\ActivityScheduleController::class, 'index'])->name('activity-schedule.index');
        Route::get('activity-schedule/year/{year}/month/{month}', [\App\Http\Controllers\v1\Management\ActivityScheduleController::class, 'scheduleInMonth'])->name('activity-schedule.schedule-in-month');
        Route::get('activity-schedule/date/{date}', [\App\Http\Controllers\v1\Management\ActivityScheduleController::class, 'detailScheduleDay'])->name('activity-schedule.date');
    });
});

require __DIR__ . '/auth.php';
