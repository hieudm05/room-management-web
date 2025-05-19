<?php

use App\Http\Controllers\Landlord\LandlordController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Landlord
   Route::prefix('landlord')->name('landlord.')->group(function () {
    Route::get('/', [LandlordController::class, 'index'])->name("dashboard");
});
// end Landlord

