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
use App\Http\Controllers\Landlord\Staff\ContractController;
use App\Http\Controllers\Landlord\Staff\DocumentController;
use App\Http\Controllers\Landlord\Staff\ElectricWaterController;
use App\Http\Controllers\Landlord\Staff\PaymentController;
use App\Http\Controllers\Landlord\Staff\ServiceController;
use App\Http\Controllers\Landlord\Staff\StaffRoomController;

Route::get('/provinces', [AddressController::class, 'getProvinces']);
Route::get('/districts/{provinceCode}', [AddressController::class, 'getDistricts']);
Route::get('/wards/{districtCode}', [AddressController::class, 'getWards']);
// Landlord
Route::get('/login', [AuthUserController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthUserController::class, 'login'])->name('login.post');
// Landlord

Route::prefix('landlords')->name('landlords.')->middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return view('landlord.dashboard');
    })->name('dashboard');

    Route::get('/register', [AuthLandlordController::class, 'showForm'])->name('register.form');
    Route::post('/register', [AuthLandlordController::class, 'submit'])->name('register.submit');

    Route::prefix('properties')->name('properties.')->group(function () {
        Route::get('/list', [PropertyController::class, 'index'])->name('list');
        Route::get('/create', [PropertyController::class, 'create'])->name('create');
        Route::post('/store', [PropertyController::class, 'store'])->name('store');
        Route::get('/show/{property_id}', [PropertyController::class, 'show'])->name('show');
        Route::get('/{property_id}/upload-document', [PropertyController::class, 'showUploadDocumentForm'])->name('uploadDocument');
        Route::post('/{property_id}/upload-document', [PropertyController::class, 'uploadDocument'])->name('uploadDocument.post');
    });

    Route::prefix('rooms')->name('rooms.')->group(function () {
        Route::get('/', [RoomController::class, 'index'])->name('index');
        Route::get('/create', [RoomController::class, 'create'])->name('create');
        Route::post('/store', [RoomController::class, 'store'])->name('store');
        Route::get('/{room}/edit', [RoomController::class, 'edit'])->name('edit');
        Route::put('/{room}', [RoomController::class, 'update'])->name('update');
        Route::put('/{room}/hide', [RoomController::class, 'hide'])->name('hide');
        Route::delete('/{room}', [RoomController::class, 'destroy'])->name('destroy');
        Route::get('/{room}', [RoomController::class, 'show'])->name('show');

        //pdf
        Route::get('/{room}/contract-pdf', [RoomController::class, 'streamContract'])->name('contract.pdf');
        Route::get('/{room}/contract-download', [RoomController::class, 'downloadContract'])->name('contract.download');
        // word
        Route::get('/{room}/contract-word', [RoomController::class, 'downloadContractWord'])->name('contract.word');

        //Contract
        Route::get('/{room}/contract-form', [RoomController::class, 'formShowContract'])->name('contract.info');
        Route::post('/{room}/contract-confirm-rentalAgreement', [RoomController::class, 'confirmStatusrentalAgreement'])->name('contract.confirmLG');
        // xác nhận thêm ng dùng vào phòng
        Route::post('/room-users/{id}/suscess', [RoomController::class, 'ConfirmAllUser'])->name('room_users.suscess');
    });

    // Room of staff
    Route::prefix('staff')->name('staff.')->group(function () {
        // Rooms của staff
        Route::get('/', [StaffRoomController::class, 'index'])->name('index');
        Route::get('/{room}/show', [StaffRoomController::class, 'show'])->name('show');
        // Chi tiết con trong phòng (giao diện chi tiết có các nút điều hướng)
        Route::prefix('contract')->name('contract.')->group(function () {
            Route::get('/{room}', [ContractController::class, 'index']);
            // Ninh viết trong này

            //
        });

        Route::prefix('services')->name('services.')->group(function () {
            Route::get('/{room}', [ServiceController::class, 'index']);
        });

        Route::prefix('electric-water')->name('electric_water.')->group(function () {
            Route::get('/{room}', [ElectricWaterController::class, 'index']);
            Route::post('/room-utility/{room}', [ElectricWaterController::class, 'store'])->name('store');
        });

        Route::prefix('documents')->name('documents.')->group(function () {
            Route::get('/{room}', [DocumentController::class, 'index']);
        });

        Route::prefix('payment')->name('payment.')->group(function () {
            Route::get('/{room}', [PaymentController::class, 'index']);
        });


    });
});
Route::prefix('rooms')->group(function () {
    Route::post('/{room}/contracts/preview', [RoomController::class, 'previewContract'])->name('contracts.preview');
    Route::post('/{room}/contracts/confirm', [RoomController::class, 'confirmContract'])->name('contracts.confirm');
    Route::get('/{room}', [RoomController::class, 'show2'])->name('show2');
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
Route::get('/status-agreement', [HomeController::class, 'StausAgreement'])->name('status.agreement');
Route::prefix('room-users')->name('room-users.')->group(function () {
    Route::post('/create-user', [HomeController::class, 'create'])->name('create');
    Route::post('/store-user', [HomeController::class, 'store'])->name('store');
});

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

