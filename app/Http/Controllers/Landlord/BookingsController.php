<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BookingsController extends Controller
{
    public function index(Request $request)
    {
        $bookings = Booking::with(['user', 'post', 'room'])->orderByDesc('created_at')->get();
        return view('landlord.bookings.index', compact('bookings'));
    }

    public function approve($id)
    {
        $booking = Booking::with('post')->findOrFail($id);

        if (!$booking->post) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy bài đăng.']);
        }

        // Nhân viên và chủ đều được approve
        if ($booking->post->posted_by_type === 'landlord') {
            $booking->confirmed_by = auth()->id();
        } else { // staff
            $booking->confirmed_by = $booking->post->post_by ?? null;
        }

        $booking->status = 'approved';
        $booking->save();

        return response()->json(['success' => true]);
    }

    public function reject($id)
    {
        $booking = Booking::with('post')->findOrFail($id);
        $booking->status = 'rejected';
        $booking->save();

        return response()->json(['success' => true]);
    }

    public function waiting($id)
    {
        $booking = Booking::with('post')->findOrFail($id);

        // Chỉ landlord (bài đăng của chủ) mới được set waiting
      if ($booking->post->post_by != auth()->id()) {
    return response()->json(['success' => false, 'message' => 'Bạn không có quyền đổi trạng thái này.']);
}


        $booking->status = 'waiting';
        $booking->save();

        return response()->json(['success' => true]);
    }

    public function noCancel($id)
    {
        $booking = Booking::with('post')->findOrFail($id);

        // Chỉ landlord mới được set no-cancel
       if ($booking->post->post_by != auth()->id()) {
    return response()->json(['success' => false, 'message' => 'Bạn không có quyền đổi trạng thái này.']);
}

        $booking->status = 'no-cancel';
        $booking->save();

        return response()->json(['success' => true]);
    }

    public function completed($id)
    {
        $booking = Booking::with('post')->findOrFail($id);

        // Chỉ landlord mới được completed (không kèm ảnh)
       if ($booking->post->post_by != auth()->id()) {
    return response()->json(['success' => false, 'message' => 'Bạn không có quyền đổi trạng thái này.']);
}


        $booking->status = 'completed';
        $booking->save();

        return response()->json(['success' => true]);
    }

    // ✅ Hoàn thành kèm ảnh minh chứng
    public function doneWithImage(Request $request, $id)
    {
        try {
            $booking = Booking::with('post')->findOrFail($id);

            if ($booking->post->posted_by_type !== 'landlord') {
                return response()->json(['success' => false, 'message' => 'Bạn không có quyền đổi trạng thái này.']);
            }

            if (!$request->hasFile('proof_image')) {
                return response()->json(['success' => false, 'message' => 'Không có ảnh được gửi lên.'] );
            }

            $path = $request->file('proof_image')->store('proofs', 'public');
            $booking->proof_image = $path;
            $booking->status = 'completed';
            $booking->save();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error("Lỗi doneWithImage: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Đã xảy ra lỗi server.']);
        }
    }
}
