<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StaffBookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['user', 'post'])
            ->orderByDesc('created_at')
            ->get();
        return view('landlord.Staff.staff_bookings.index', compact('bookings'));
    }
    public function wait($id)
    {
        $booking = Booking::find($id);
        if (!$booking) return response()->json(['success' => false]);

        $booking->status = 'waiting';
        $booking->save();

        return response()->json(['success' => true]);
    }

    public function done($id)
    {
        $booking = Booking::find($id);
        if (!$booking) return response()->json(['success' => false]);

        $booking->status = 'done';
        $booking->save();

        return response()->json(['success' => true]);
    }

    public function noShow($id)
    {
        $booking = Booking::find($id);
        if (!$booking) return response()->json(['success' => false]);

        $booking->status = 'no-cancel';
        $booking->save();

        return response()->json(['success' => true]);
    }
    public function doneWithImage(Request $request, $id)
    {
        try {
            $booking = Booking::find($id);

            if (!$booking) {
                Log::warning("Không tìm thấy booking với ID: {$id}");
                return response()->json(['success' => false]);
            }

            Log::info("Yêu cầu xác nhận với ảnh cho booking ID: {$id}");
            Log::info('Dữ liệu gửi lên:', $request->all());

            if ($request->hasFile('proof_image')) {
                $file = $request->file('proof_image');
                Log::info('Đã nhận được file:', [
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                ]);

                $path = $file->store('proofs', 'public');
                $booking->proof_image = $path;
            } else {
                Log::warning('Không nhận được file proof_image trong request!');
                return response()->json(['success' => false, 'message' => 'Không có ảnh được gửi lên']);
            }

            $booking->status = 'completed';
            $booking->save();

            Log::info("Booking ID {$id} đã cập nhật thành công với trạng thái DONE");

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error("Lỗi trong doneWithImage: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Đã xảy ra lỗi server']);
        }
    }
}
