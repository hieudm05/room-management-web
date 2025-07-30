<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Landlord\RoomLeaveRequest;
use App\Models\RentalAgreement;
use App\Models\UserInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LandlordRoomLeaveController extends Controller
{
    // Hiển thị danh sách yêu cầu đã được staff duyệt
  public function index()
{
    $requests = RoomLeaveRequest::with(['user', 'room'])
        ->where('status', 'staff_approved')
        ->where('landlord_id', Auth::id()) // ✅ Chỉ lấy request của chủ trọ hiện tại
        ->get();

    return view('landlord.roomleave.index', compact('requests'));
}

    // Xem chi tiết một yêu cầu
    public function show($id)
    {
        $request = RoomLeaveRequest::with(['user', 'room'])->findOrFail($id);
        return view('landlord.roomleave.show', compact('request'));
    }

    // Duyệt yêu cầu
    public function approve($id)
    {
        DB::transaction(function () use ($id) {
            $request = RoomLeaveRequest::findOrFail($id);

            // Cập nhật trạng thái
            $request->status = 'approved';
            $request->handled_by = Auth::id();
            $request->handled_at = now();
            $request->save();

            // Nếu là nhượng quyền
            if ($request->type === 'transfer' && $request->new_renter_id) {
                $agreement = RentalAgreement::where('room_id', $request->room_id)->first();
                if ($agreement) {
                    $agreement->renter_id = $request->new_renter_id;
                    $agreement->save();
                }
            }

            // Nếu là rời phòng
            if ($request->type === 'leave') {
                UserInfo::where('room_id', $request->room_id)
                    ->where('user_id', $request->user_id)
                    ->delete();
            }
        });

        return redirect()->route('landlord.roomleave.index')->with('success', '✅ Đã duyệt yêu cầu thành công.');
    }

    // Hiện form nhập lý do từ chối
    public function rejectForm($id)
    {
        $request = RoomLeaveRequest::findOrFail($id);
        return view('landlord.roomleave.reject', compact('request'));
    }

    // Xử lý từ chối
    public function reject(Request $request, $id)
    {
        $request->validate([
            'reject_reason' => 'required|string|max:255',
        ]);

        $leaveRequest = RoomLeaveRequest::findOrFail($id);
        $leaveRequest->status = 'rejected';
        $leaveRequest->reject_reason = $request->reject_reason;
        $leaveRequest->handled_by = Auth::id();
        $leaveRequest->handled_at = now();
        $leaveRequest->save();

        // Có thể gửi thông báo cho người thuê ở đây nếu muốn

        return redirect()->route('landlord.roomleave.index')->with('info', '❌ Đã từ chối yêu cầu và gửi lý do cho người thuê.');
    }
}
