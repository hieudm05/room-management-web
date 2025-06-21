<?php

namespace App\Http\Controllers\Landlord\Staff;

use App\Http\Controllers\Controller;
use App\Models\Landlord\Room;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    //
    public function index(Room $room, Request $request){
        return view('landlord.Staff.Rooms.bills.index', compact('room'));
    }
}
