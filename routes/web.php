<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\Landlord\PropertyController;
use Illuminate\Support\Facades\Route;


Route::get('/provinces', [AddressController::class, 'getProvinces']);
Route::get('/districts/{provinceCode}', [AddressController::class, 'getDistricts']);
Route::get('/wards/{districtCode}', [AddressController::class, 'getWards']);
// Landlord
 Route::prefix('landlords')->name('landlords.')->group(function () {
    // Trang dashboard
    Route::get('/', function () {
        return view('landlord.dashboard');
    })->name('dashboard');

    // Nhóm routes liên quan đến properties
    Route::prefix('properties')->name('properties.')->group(function () {
        Route::get('/list', [PropertyController::class, 'index'])->name('list');
        Route::get('/create', [PropertyController::class, 'create'])->name('create');
        Route::post('/store', [PropertyController::class, 'store'])->name('store');
    });
});
// end Landlord