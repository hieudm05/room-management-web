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
     $request->validate([
    'post_id' => 'required|exists:staff_posts,post_id',
    'check_in' => 'required|date_format:d/m/Y H:i|after_or_equal:today',
    'note' => 'nullable|string|max:255',
    'guest_name' => 'nullable|string|max:255',
    'phone' => [
        'nullable',
        'regex:/^(0[3-9][0-9]{8}|\+84[3-9][0-9]{8})$/'
    ],
    'email' => 'nullable|email|max:255', // ✅ validate email
]);

Booking::create([
    'post_id' => $request->post_id,
    'user_id' => Auth::check() ? Auth::id() : null,
    'check_in' => \Carbon\Carbon::createFromFormat('d/m/Y H:i', $request->check_in),
    'note' => $request->note,
    'status' => 'pending',
    'guest_name' => $request->guest_name,
    'phone' => $request->phone,
    'email' => $request->email, // ✅ lưu email
    'room_id' => $request->room_id,
    'confirmed_by' => $request->post_by,
]);




        return redirect()->back()->with('success', 'Booking submitted successfully!');
    }
}
