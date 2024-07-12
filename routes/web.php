<?php

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

    // Land Route
    Route::resource('land', \App\Http\Controllers\v1\LandController::class);
    Route::get('get-land/{land}', [\App\Http\Controllers\v1\LandController::class, 'getLand'])->name('land.get-land');
    Route::get('get-land-polygon-with-garden/{land}', [\App\Http\Controllers\v1\LandController::class, 'getLandPolygonWithGardens'])->name('land.get-land-polygon');

    Route::resource('garden', \App\Http\Controllers\v1\GardenController::class);

    Route::resource('commodity', \App\Http\Controllers\v1\CommodityController::class);

    Route::resource('device-type', \App\Http\Controllers\v1\DeviceTypeController::class);

    Route::resource('device', \App\Http\Controllers\v1\DeviceController::class);

    Route::resource('commodity.phase', \App\Http\Controllers\v1\CommodityPhaseController::class)->only(['create', 'store']);
    Route::get('/commodity/{commodity}/phase/edit', [\App\Http\Controllers\v1\CommodityPhaseController::class, 'edit'])->name('commodity.phase.edit');
    Route::put('/commodity/{commodity}/phase/update', [\App\Http\Controllers\v1\CommodityPhaseController::class, 'update'])->name('commodity.phase.update');
});

require __DIR__.'/auth.php';
