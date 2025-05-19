<?php

use App\Http\Controllers\Landlord\PropertyController;
use Illuminate\Support\Facades\Route;



// Landlord
   Route::prefix('landlords')->name('landlord.')->group(function () {
    // Home dashboard
    Route::get('/', function () {
        return view('landlord.dashboard');
    })->name('dashboard');
    // Quản lý khu trọ/bất động sản
    Route::get('/list', [PropertyController::class, 'listLandlord'])->name("list");
    
});
// end Landlord