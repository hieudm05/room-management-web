<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\StaffPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function store(Request $request)
    {
        $bookings = Booking::with(['user', 'post'])
            ->orderByDesc('created_at')
            ->get();
            // dd($bookings);
        $request->validate([
            'post_id' => 'required|exists:staff_posts,post_id',
            'check_in' => 'required|date_format:d/m/Y|after_or_equal:today',
            'note' => 'nullable|string|max:255',
            'guest_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $post = StaffPost::where('post_id', $request->post_id)->first();
        $staffId = $post->staff_id; // ✅ Vì cột trong DB là `staff_id`

        Booking::create([
            'post_id' => $request->post_id,
            'user_id' => Auth::check() ? Auth::id() : null,
            'check_in' => \Carbon\Carbon::createFromFormat('d/m/Y', $request->check_in),
            'note' => $request->note,
            'status' => 'pending',
            'guest_name' => $request->guest_name,
            'phone' => $request->phone,
            'room_id' => $request->room_id,
            'confirmed_by' => $staffId,
        ]);


        return redirect()->back()->with('success', 'Booking submitted successfully!');
    }
}
