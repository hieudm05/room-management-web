<?php

use App\Http\Controllers\Landlord\PropertyController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;



// Landlord
   Route::prefix('landlords')->name('landlord.')->group(function () {
    // Home dashboard
    Route::get('/', function () {
        return view('landlord.dashboard');
    })->name('dashboard');
    // Quản lý khu trọ/bất động sản
    Route::get('/list', [PropertyController::class, 'index'])->name("list");
    
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