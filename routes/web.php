<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Client\AuthLandlordController;
use App\Http\Controllers\Client\AuthUserController;
use App\Http\Controllers\Client\ForgotPasswordController;
use App\Http\Controllers\Client\HomeController;
use App\Http\Controllers\Client\ResetPasswordController;
use App\Http\Controllers\Landlord\PropertyController;
use App\Http\Controllers\Landlord\RoomController;
use App\Http\Controllers\TenantProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminProfileController;

Route::get('/provinces', [AddressController::class, 'getProvinces']);
Route::get('/districts/{provinceCode}', [AddressController::class, 'getDistricts']);
Route::get('/wards/{districtCode}', [AddressController::class, 'getWards']);
// Landlord
    Route::get('/login', [AuthUserController::class, 'loginForm'])->name('login');
    Route::post('/login', [AuthUserController::class, 'login'])->name('login.post');
// Landlord
Route::prefix('landlords')->name('landlords.')->group(function () {
    // Trang dashboard
    Route::get('/', function () {
        return view('landlord.dashboard');
    })->name('dashboard');

    // Đăng ký làm chủ trọ (chỉ khi user chưa là landlord)
    Route::get('/register', [AuthLandlordController::class, 'showForm'])->name('register.form');
    Route::post('/register', [AuthLandlordController::class, 'submit'])->name('register.submit');

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

//Auth user
Route::get('/login', [AuthUserController::class, 'loginForm'])->name('login');
Route::prefix('auth')->name('auth.')->group(function () {
    // Đăng ký
    Route::get('/register', [AuthUserController::class, 'registerForm'])->name('register');
    Route::post('/register', [AuthUserController::class, 'register'])->name('register.post');
    // Đăng nhập
    Route::get('/login', [AuthUserController::class, 'loginForm'])->name('login');
    Route::post('/login', [AuthUserController::class, 'login'])->name('login.post');
    // Đăng xuất
    Route::get('/logout', [AuthUserController::class, 'logout'])->name('logout');
});
Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

// Trang chủ trang web
Route::get('/', [HomeController::class, 'renter'])->name('renter');
// Route::get('/landlord', [HomeController::class, 'landlordindex'])->name('landlord');


// Tenant Profile Management

Route::middleware(['auth'])->group(function () {
    Route::get('/profile/edit', [TenantProfileController::class, 'edit'])->name('tenant.profile.edit');
    Route::put('/profile/update', [TenantProfileController::class, 'update'])->name('tenant.profile.update');
    Route::put('/profile/avatar', [TenantProfileController::class, 'updateAvatar'])->name('profile.update.avatar');
});


Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/profile/edit', [AdminProfileController::class, 'edit'])->name('admin.profile.edit');
    Route::post('/profile/update', [AdminProfileController::class, 'update'])->name('admin.profile.update');
});
