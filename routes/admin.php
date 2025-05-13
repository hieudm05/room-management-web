<?php

use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\Route;

// Route::middleware(['auth', 'is_admin'])->prefix('admin')->name('admin.')->group(function () {
//     Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
// });

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
});