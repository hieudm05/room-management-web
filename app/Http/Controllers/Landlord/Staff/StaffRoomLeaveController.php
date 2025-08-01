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
        ->whereNotIn('staff_status', ['Finalized']) // đã xử lý xong thì ẩn
        ->get();

    return view('landlord.staff.roomleave.index', compact('requests'));
}
   public function finalize($id)
{
    $request = RoomLeaveRequest::where('id', $id)
        ->where('staff_id', Auth::id())
        ->where('staff_status', 'Approved') // chỉ khi đã duyệt
        ->where('status', 'approved')       // chủ trọ đã duyệt
        ->firstOrFail();

    $request->staff_status = 'Finalized';
    $request->save();

    return redirect()->route('landlord.staff.roomleave.index')
        ->with('success', '✅ Đã xác nhận hoàn tất yêu cầu.');
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
   public function processed()
    {
        $staffId = Auth::id();

        // Lấy các yêu cầu trả phòng đã được xử lý bởi nhân viên này
       $processedLeaves = RoomLeaveRequest::where('handled_by', $staffId)
    ->with('room', 'user')
    ->latest('updated_at') // hoặc latest('id')
    ->paginate(10);

        return view('landlord.staff.roomleave.processed', compact('processedLeaves'));
    }
}

