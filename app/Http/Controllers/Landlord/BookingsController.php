<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingsController extends Controller
{

    public function index()
    {
        $bookings = Booking::with(['user', 'post'])
            ->orderByDesc('created_at')
            ->get();
            // dd($bookings);

        return view('landlord.bookings.index', compact('bookings'));
    }

    // app/Http/Controllers/Landlord/BookingController.php

    public function approve($id)
    {
        $booking = Booking::with('post')->findOrFail($id); // ✅ Load luôn post

        if (!$booking->post || !$booking->post->staff_id) {
            return response()->json(['success' => false, 'message' => 'Không xác định được nhân viên đã đăng bài.']);
        }

        $booking->status = 'approved';
        $booking->confirmed_by = $booking->post->staff_id;
        $booking->save();

        return response()->json(['success' => true]);
    }



    public function reject($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->status = 'rejected';
        $booking->save();

        return response()->json(['success' => true]);
    }
}
