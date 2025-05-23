<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\Landlord\PropertyController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\AuthUserController;
use App\Http\Controllers\Client\ForgotPasswordController;
use App\Http\Controllers\Client\ResetPasswordController;
use App\Http\Controllers\Client\HomeController;
use App\Http\Controllers\Landlord\HomeLandlordController;


//API 60 tỉnh thành
Route::get('/provinces', [AddressController::class, 'getProvinces']);
Route::get('/districts/{provinceCode}', [AddressController::class, 'getDistricts']);
Route::get('/wards/{districtCode}', [AddressController::class, 'getWards']);
// End API 63 tỉnh thành

// Landlord
 // Landlord
Route::prefix('landlords')->name('landlords.')->middleware('auth')->group(function () {
    // Trang dashboard
    Route::get('/', [HomeLandlordController::class, 'index'])->name('dashboard');
    
    // Nhóm routes liên quan đến properties
    Route::prefix('properties')->name('properties.')->group(function () {
        Route::get('/list', [PropertyController::class, 'index'])->name('list');
        Route::get('/create', [PropertyController::class, 'create'])->name('create');
        Route::post('/store', [PropertyController::class, 'store'])->name('store');
    });
});
// end Landlord
//Auth user
Route::prefix('auth')->name('auth.')->group(function () {   
   // Đăng ký
    Route::get('/register', [AuthUserController::class, 'registerForm'])->name('register');
    Route::post('/register', [AuthUserController::class, 'register'])->name('register.post'); 
    // Đăng nhập
    Route::get('/login', [AuthUserController::class, 'loginForm'])->name('login');
    Route::post('/login', [AuthUserController::class, 'login'])->name('login.post');
});
    Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

// Trang chủ trang web
Route::get('/', [HomeController::class, 'renter'])->name('renter');
// Route::get('/landlord', [HomeController::class, 'landlordindex'])->name('landlord');