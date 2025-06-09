<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeLandlordController extends Controller
{
    public function index()
    {
        // Trả về view dashboard dành cho landlord
        return view('landlord.dashboard');
    }
}
