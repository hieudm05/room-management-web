<?php

namespace App\Http\Controllers\Renter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserInfo;
use App\Models\RentalAgreement;
use App\Models\Landlord\Room;
use App\Models\Landlord\RoomLeaveRequest;
use App\Models\Landlord\Property;
use Illuminate\Support\Facades\Auth;

class RoomLeaveController extends Controller
{
    /**
     * Trang chính hiển thị danh sách thành viên phòng và các yêu cầu rời phòng
     */
  public function index()
{
    $userId = Auth::id();

    $userInfo = UserInfo::where('user_id', $userId)->first();

    if (!$userInfo || !$userInfo->room_id) {
        return redirect()->back()->withErrors('❌ Bạn chưa thuộc phòng nào.');
    }

    $room = Room::with(['userInfos.user', 'rentalAgreement.renter'])->find($userInfo->room_id);

    $isContractOwner = $room->rentalAgreement && $room->rentalAgreement->renter_id == $userId;

    if ($isContractOwner) {
    // Lấy tất cả yêu cầu rời phòng (mọi trạng thái)
    $leaveRequests = RoomLeaveRequest::where('room_id', $room->room_id)
        ->orderByDesc('created_at')
        ->get()
        ->keyBy('user_id'); // dễ lấy theo user trong view
} else {
    // Lấy yêu cầu của user hiện tại (mọi trạng thái)
    $leaveRequests = RoomLeaveRequest::where('room_id', $room->room_id)
        ->where('user_id', $userId)
        ->orderByDesc('created_at')
        ->get()
        ->keyBy('user_id');
}

    return view('home.roomleave.stopRentForm', [
        'room' => $room,
        'currentUserId' => $userId,
        'isContractOwner' => $isContractOwner,
        'leaveRequests' => $leaveRequests, // 👈 Quan trọng
    ]);
}
  public function sendLeaveRequest(Request $request)
{
    $request->validate([
        'room_id'       => 'required|exists:rooms,room_id',
        'user_id'       => 'required|exists:users,id',
        'leave_date'    => 'required|date|after_or_equal:today',
        'reason'        => 'nullable|string|max:255',
        'action_type'   => 'nullable|in:terminate,transfer',
        'new_renter_id' => 'nullable|exists:users,id',
    ]);

    $userId = Auth::id();

    $existing = RoomLeaveRequest::where('user_id', $userId)
        ->where('status', 'Pending')
        ->first();

    if ($existing) {
        return back()->withErrors('⚠️ Bạn đã gửi yêu cầu rời phòng và đang chờ xử lý.');
    }

    $userInfo = UserInfo::where('user_id', $userId)->first();
    if (!$userInfo) {
        return back()->withErrors('Không tìm thấy thông tin người dùng.');
    }

    $room = Room::with('staffs')->findOrFail($request->room_id);

    $firstStaff = $room->staffs->first(); // Lấy staff đầu tiên từ bảng trung gian room_staff

    $leaveRequest = new RoomLeaveRequest([
        'user_id'      => $request->user_id,
        'room_id'      => $request->room_id,
        'leave_date'   => $request->leave_date,
        'reason'       => $request->reason,
        'status'       => 'Pending',
        'landlord_id'  => $room->landlord_id ?? null,
        'staff_id'     => $firstStaff?->id, // lấy từ room_staff
    ]);

    if ($request->action_type === 'transfer') {
        $leaveRequest->action_type = 'transfer';
        $leaveRequest->new_renter_id = $request->new_renter_id;
    } elseif ($request->action_type === 'terminate') {
        $leaveRequest->action_type = 'terminate';
    }

    $leaveRequest->save();

    return redirect()->route('home.roomleave.stopRentForm')->with('success', '✅ Yêu cầu đã được gửi thành công.');
}


    /**
     * Xem chi tiết yêu cầu rời phòng
     */
    public function viewRequest($id)
    {
        $userId = Auth::id();

        $request = RoomLeaveRequest::with(['room.property', 'newRenter'])
            ->where('id', $id)
            ->where('user_id', $userId)
            ->first();

        if (!$request) {
            return redirect()->back()->withErrors('Không tìm thấy yêu cầu.');
        }

        return view('home.roomleave.viewRequest', [
            'request' => $request
        ]);
    }

    /**
     * Hủy yêu cầu rời phòng
     */
    public function cancelRequest($id)
    {
        $userId = Auth::id();

        $request = RoomLeaveRequest::where('id', $id)
            ->where('user_id', $userId)
            ->where('status', 'Pending')
            ->first();

        if (!$request) {
            return redirect()->back()->withErrors('Không thể hủy yêu cầu này.');
        }

        $request->delete();

        return redirect()->route('home.roomleave.stopRentForm')->with('success', '🗑️ Yêu cầu đã được hủy.');
    }
}
