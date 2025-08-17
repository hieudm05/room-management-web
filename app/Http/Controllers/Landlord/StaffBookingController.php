<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class StaffBookingController extends Controller
{
    public function index()
    {
        $staffId = Auth::id();

        $bookings = Booking::with(['user', 'post'])
            ->whereHas('post', function ($query) use ($staffId) {
                $query->where('staff_id', $staffId);
            })
            ->orderByDesc('created_at')
            ->get();

        return view('landlord.Staff.staff_bookings.index', compact('bookings'));
    }
    public function wait($id)
{
    $booking = Booking::with(['post', 'user'])->findOrFail($id);

    // ✅ Chỉ staff quản lý post mới được set waiting
    if ($booking->post->staff_id != auth()->id()) {
        return response()->json(['success' => false, 'message' => 'Bạn không có quyền đổi trạng thái này.']);
    }

    $booking->status = 'waiting';
    $booking->save();

    // ✅ Lấy thông tin staff
    $staff = auth()->user();
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
        \Mail::send('landlord.bookings.emails.bookingss', [
            'customer_name'   => $booking->user->name ?? $booking->guest_name,
            'appointment_time'=> $booking->check_in, // dùng check_in
            'landlord_name'   => $staff->name,
            'landlord_phone'  => $staff->phone_number ?? 'Không có',
            'landlord_address'=> $address ?? 'Không có',
        ], function ($message) use ($customerEmail) {
            $message->to($customerEmail);
            $message->subject('📅 Thông báo hẹn gặp để xem phòng');
        });
    }

    // ✅ Tạo notification cho user (nếu có tài khoản)
    if ($booking->user) {
        $notification = \App\Models\Notification::create([
            'title'      => 'Thông báo lịch hẹn xem phòng',
            'message'    => 'Nhân viên ' . $staff->name . ' đã hẹn bạn xem phòng ' . ($booking->room->room_number ?? ''),
            'type'       => 'system',
            'link'       => route('user.bookings.index'),
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
