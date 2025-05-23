<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Landlord\PropertyController;
use App\Http\Controllers\Landlord\RoomController;

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

    // Nhóm routes liên quan đến rooms
Route::prefix('rooms')->name('rooms.')->group(function () {
    Route::get('/', [RoomController::class, 'index'])->name('index');
    Route::get('/create', [RoomController::class, 'create'])->name('create');
    Route::post('/store', [RoomController::class, 'store'])->name('store');
    Route::get('/{room}/edit', [RoomController::class, 'edit'])->name('edit');
    Route::put('/{room}', [RoomController::class, 'update'])->name('update');
    Route::put('/{room}/hide', [RoomController::class, 'hide'])->name('hide');
    Route::delete('/{room}', [RoomController::class, 'destroy'])->name('destroy');
    Route::get('/{room}/show', [RoomController::class, 'show'])->name('show');

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