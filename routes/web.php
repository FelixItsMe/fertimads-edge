<?php

use App\Enums\UserRoleEnums;
use App\Http\Controllers\ProfileController;
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
        ->group(function(){
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
    });

    Route::middleware(['roleAccess:' . UserRoleEnums::CONTROL->value])
        ->prefix('control')
        ->group(function(){
        Route::get('head-unit/manual', [\App\Http\Controllers\v1\Control\ControlHeadUnitController::class, 'indexControlManual'])->name('head-unit.manual.index');
        Route::post('head-unit/manual', [\App\Http\Controllers\v1\Control\ControlHeadUnitController::class, 'storeControlManual'])->name('head-unit.manual.store');
        Route::get('head-unit/semi-auto', [\App\Http\Controllers\v1\Control\ControlHeadUnitController::class, 'indexControlSemiAuto'])->name('head-unit.semi-auto.index');
        Route::post('head-unit/semi-auto', [\App\Http\Controllers\v1\Control\ControlHeadUnitController::class, 'storeControlSemiAuto'])->name('head-unit.semi-auto.store');
        Route::get('head-unit/sensor', [\App\Http\Controllers\v1\Control\ControlHeadUnitController::class, 'indexControlSensor'])->name('head-unit.sensor.index');
        Route::post('head-unit/sensor', [\App\Http\Controllers\v1\Control\ControlHeadUnitController::class, 'storeControlSensor'])->name('head-unit.sensor.store');
        Route::get('head-unit/schedule-water', [\App\Http\Controllers\v1\Control\ControlHeadUnitController::class, 'indexControlScheduleWater'])->name('head-unit.schedule-water.index');
        Route::post('head-unit/schedule-water', [\App\Http\Controllers\v1\Control\ControlHeadUnitController::class, 'storeControlScheduleWater'])->name('head-unit.schedule-water.store');
    });

    // extra to get data
    Route::prefix('extra')->name('extra.')->group(function(){
        // garden
        Route::get('garden/list', [\App\Http\Controllers\v1\GardenController::class, 'listGardensName'])->name('garden.list');

        // device
        Route::get('device-selenoid/{deviceSelenoid}/sensor', [\App\Http\Controllers\v1\DeviceSelenoidController::class, 'selenoidSensor'])->name('device-selenoid.sensor');

        Route::get('get-land/{land}', [\App\Http\Controllers\v1\LandController::class, 'getLand'])->name('land.get-land');
        Route::get('get-land-polygon-with-garden/{land}', [\App\Http\Controllers\v1\LandController::class, 'getLandPolygonWithGardens'])->name('land.get-land-polygon');
    });
});

require __DIR__.'/auth.php';
