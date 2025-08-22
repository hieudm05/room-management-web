<?php

namespace App\Http\Controllers\Landlord;

use App\Models\Booking;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;

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
    $booking = Booking::with(['post', 'user'])->findOrFail($id);

    // Chỉ landlord mới được set waiting
    if ($booking->post->post_by != auth()->id()) {
        return response()->json(['success' => false, 'message' => 'Bạn không có quyền đổi trạng thái này.']);
    }

    $booking->status = 'waiting';
    $booking->save();

    // ✅ Lấy thông tin landlord
    $landlord = auth()->user();
    $customerEmail = $booking->user->email ?? $booking->email;

    // ✅ Lấy địa chỉ từ post
    $address = null;
    if ($booking->post) {
        $addressParts = array_filter([
            $booking->post->address,
            $booking->post->ward,
            $booking->post->district,
            $booking->post->city,
        ]);
        $address = implode(', ', $addressParts);
    }

    // ✅ Gửi mail cho khách
    if ($customerEmail) {
        Mail::send('landlord.bookings.emails.bookingss', [
            'customer_name'   => $booking->user->name ?? $booking->guest_name,
            'appointment_time'=> $booking->check_in, // dùng check_in
            'landlord_name'   => $landlord->name,
            'landlord_phone'  => $landlord->phone_number ?? 'Không có',
            'landlord_address'=> $address ?? 'Không có',
        ], function ($message) use ($customerEmail) {
            $message->to($customerEmail);
            $message->subject('📅 Thông báo hẹn gặp để xem phòng');
        });
    }

    // ✅ Tạo notification cho user (nếu có tài khoản)
    if ($booking->user) {
$notification = Notification::create([
            'title'      => 'Thông báo lịch hẹn xem phòng',
            'message'    => 'Chủ trọ ' . $landlord->name . ' đã hẹn bạn xem phòng ' . ($booking->room->room_number ?? ''),
            'type'       => 'system',
           'link' => route('user.bookings.index'),
            'expired_at' => now()->addDays(7),
            'is_global'  => false,
        ]);

        $notification->users()->attach($booking->user->id, [
            'is_read'     => false,
            'received_at' => now(),
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
    }

    return response()->json(['success' => true, 'message' => 'Đã gửi email và thông báo cho khách.']);
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
