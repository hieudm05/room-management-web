<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\Landlord\PropertyController;
use App\Http\Controllers\Admin\UserController;
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

// admin 
 // Account Management
 Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
});
 // end Account Management