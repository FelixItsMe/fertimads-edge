<?php

use App\Http\Controllers\Api\v1\Care\PestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('mobile/v1')->group(function(){
    Route::post('login', [App\Http\Controllers\Api\v1\AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function(){
        Route::post('logout', [App\Http\Controllers\Api\v1\AuthController::class, 'logout']);

        Route::get('device', [App\Http\Controllers\Api\v1\DeviceController::class, 'getDevices']);
        Route::get('device/{device}', [App\Http\Controllers\Api\v1\DeviceController::class, 'detailDevice']);

        Route::get('device/{device}/control', [App\Http\Controllers\Api\v1\DeviceControlController::class, 'index']);

        Route::post('device/{device}/control/auto/store', [App\Http\Controllers\Api\v1\DeviceControlController::class, 'storeDeviceSensor']);
        Route::post('device/{device}/control/semi-auto/store', [App\Http\Controllers\Api\v1\DeviceControlController::class, 'storeDeviceSemiAuto']);
        Route::post('device/{device}/control/manual/store', [App\Http\Controllers\Api\v1\DeviceControlController::class, 'storeDeviceManual']);
        Route::post('device/{device}/control/schedule/store', [App\Http\Controllers\Api\v1\DeviceControlController::class, 'storeDeviceSchedule']);
        Route::get('test-schedule', [App\Http\Controllers\Api\v1\DeviceControlController::class, 'testSchedule']);

        Route::get('get-lands-polygon', [App\Http\Controllers\Api\v1\LandController::class, 'getLandsPolygon']);
        Route::get('get-lands-list-device/{device}', [App\Http\Controllers\Api\v1\LandController::class, 'getListLandsFromDevice']);

        Route::get('get-gardens-list-land/{land}', [App\Http\Controllers\Api\v1\GardenController::class, 'getListGardensFromLands']);
        Route::get('garden/{garden}/detail', [App\Http\Controllers\Api\v1\GardenController::class, 'detailGarden']);
        Route::get('garden/{garden}/list-telemetry', [App\Http\Controllers\Api\v1\GardenController::class, 'gardenListTelemetries']);
        Route::get('garden/{garden}/latest-telemetry', [App\Http\Controllers\Api\v1\GardenController::class, 'gardenLatestTelemetry']);
        Route::get('garden/{garden}/calendar-schedule', [App\Http\Controllers\Api\v1\GardenController::class, 'calendarSchedules']);

        Route::resource('pest', PestController::class);
    });

    Route::get('/user', function (Request $request) {
        return $request->user();
    })->middleware('auth:sanctum');
});
