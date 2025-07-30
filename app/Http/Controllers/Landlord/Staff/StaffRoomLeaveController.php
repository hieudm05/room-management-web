<?php

namespace App\Http\Controllers\Landlord\Staff;

use App\Http\Controllers\Controller;
use App\Models\Landlord\RoomLeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffRoomLeaveController extends Controller
{
    /**
     * Danh sách yêu cầu chờ duyệt sơ bộ bởi nhân viên
     */
    public function index()
    {
        $staffId = Auth::id();

        $requests = RoomLeaveRequest::with(['user', 'room'])
            ->where('staff_id', $staffId)
            ->where('staff_status', 'Pending') // ✅ Dùng staff_status
            ->get();

        return view('landlord.staff.roomleave.index', compact('requests'));
    }

    /**
     * Xem chi tiết yêu cầu rời phòng
     */
    public function show($id)
    {
        $staffId = Auth::id();

        $request = RoomLeaveRequest::with(['user', 'room'])
            ->where('id', $id)
            ->where('staff_id', $staffId)
            ->firstOrFail();

        return view('landlord.staff.roomleave.show', compact('request'));
    }

    /**
     * Nhân viên duyệt yêu cầu - gửi lên chủ trọ
     */
    public function approve($id)
{
    $request = RoomLeaveRequest::with('room.property') // để lấy landlord từ property
        ->where('id', $id)
        ->where('staff_status', 'Pending')
        ->where('staff_id', Auth::id())
        ->firstOrFail();

    $landlordId = optional($request->room->property)->landlord_id;

    $request->staff_status = 'Approved';
    $request->status = 'staff_approved';
    $request->handled_by = Auth::id();
    $request->handled_at = now();
    $request->landlord_id = $landlordId; // ⚠️ đây là dòng quan trọng
    $request->save();

    return redirect()->route('landlord.staff.roomleave.index')
        ->with('success', '✅ Đã duyệt yêu cầu và chuyển đến chủ trọ.');
}
}
