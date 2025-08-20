<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

class UserBookingController extends Controller
{
    // Danh sách lịch hẹn của user
    public function index()
    {
        $bookings = Booking::with(['room', 'post'])
            ->where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->get();

        return view('home.bookingss', compact('bookings'));
    }

    // Xem chi tiết lịch hẹn

}
