<?php

namespace App\Http\Controllers\LandLord;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Complaint;
use App\Models\Notification;
use Carbon\Carbon;

class LandLordComplaintController extends Controller
{
    public function index()
    {
        $landlordId = Auth::id();

        $complaints = Complaint::whereHas('property', function ($q) use ($landlordId) {
                $q->where('landlord_id', $landlordId);
            })
            ->with(['room.staffs', 'staff', 'property'])
            ->latest()
            ->paginate(5);

        return view('landlord.complaints.index', compact('complaints'));
    }

    public function show($id)
    {
        $complaint = Complaint::with(['room.staffs', 'property', 'staff', 'photos'])->findOrFail($id);

        if ($complaint->property->landlord_id !== Auth::id()) {
            abort(403, 'Không có quyền truy cập khiếu nại này');
        }

        return view('landlord.complaints.show', compact('complaint'));
    }

    public function approve($id)
    {
        $complaint = Complaint::with(['room.staffs', 'property'])->findOrFail($id);

        if ($complaint->property->landlord_id !== auth()->id()) {
            abort(403);
        }

        $assignedStaff = $complaint->room->staffs->first();

        if (!$assignedStaff) {
            return back()->with('error', 'Không tìm thấy nhân viên phụ trách phòng này.');
        }

        $complaint->update([
            'staff_id' => $assignedStaff->id,
            'status' => 'in_progress',
        ]);

        // 🔔 Gửi thông báo cho nhân viên
        $this->sendNotificationToUser(
            $assignedStaff->id,
            'Bạn được giao xử lý khiếu nại mới',
            'Bạn vừa được giao xử lý một khiếu nại tại phòng ' . $complaint->room->name,
            route('landlord.staff.complaints.index', $complaint->id)
        );

        // 🔔 Gửi thông báo cho người thuê
        if ($complaint->user_id) {
            $this->sendNotificationToUser(
                $complaint->user_id,
                'Khiếu nại đã được tiếp nhận',
                'Chủ trọ đã duyệt và giao nhân viên xử lý khiếu nại của bạn.',
                route('home.complaints.index', $complaint->id)
            );
        }

        return redirect()->route('landlord.complaints.index')->with('success', 'Đã duyệt và giao nhân viên xử lý.');
    }

    public function assignForm($id)
    {
        $complaint = Complaint::with(['property', 'room.staffs'])->findOrFail($id);

        if ($complaint->property->landlord_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền truy cập khiếu nại này.');
        }

        $staffList = $complaint->room->staffs;

        return view('landlord.complaints.assign', compact('complaint', 'staffList'));
    }

    public function assign(Request $request, $id)
    {
        $request->validate([
            'staff_id' => 'required|exists:users,id',
        ]);

        $complaint = Complaint::with(['property', 'room'])->findOrFail($id);

        if ($complaint->property->landlord_id !== Auth::id()) {
            abort(403);
        }

        $complaint->update([
            'staff_id' => $request->staff_id,
            'status' => 'in_progress',
        ]);

        // 🔔 Gửi thông báo cho nhân viên
        $this->sendNotificationToUser(
            $request->staff_id,
            'Bạn được giao xử lý khiếu nại',
            'Chủ trọ vừa giao bạn xử lý khiếu nại tại phòng ' . $complaint->room->name,
            route('landlord.staff.complaints.index', $complaint->id)
        );

        // 🔔 Gửi thông báo cho người thuê
        if ($complaint->user_id) {
            $this->sendNotificationToUser(
                $complaint->user_id,
                'Khiếu nại đang được xử lý',
                'Chủ trọ đã giao nhân viên xử lý khiếu nại của bạn tại phòng ' . $complaint->room_id->room_number,
                route('home.complaints.show', $complaint->id)
            );
        }

        return redirect()->route('landlord.complaints.index')
            ->with('success', 'Đã ủy quyền nhân viên xử lý khiếu nại thành công.');
    }

    public function acceptReject($id)
    {
        $complaint = Complaint::with('property')->findOrFail($id);

        if ($complaint->property->landlord_id !== Auth::id()) {
            abort(403);
        }

        if ($complaint->status !== 'rejected') {
            return back()->with('error', 'Khiếu nại này chưa bị từ chối.');
        }

        $complaint->status = 'cancelled';
        $complaint->save();

        // 🔔 Gửi thông báo cho người thuê
        if ($complaint->user_id) {
            $this->sendNotificationToUser(
                $complaint->user_id,
                'Khiếu nại đã bị huỷ xử lý',
                'Chủ trọ đã đánh dấu khiếu nại của bạn là huỷ bỏ.',
                route('landlord.complaint.show', $complaint->id)
            );
        }

        return redirect()->route('landlord.complaints.index')->with('success', 'Đã chấp nhận từ chối xử lý khiếu nại.');
    }

    public function showRejection($id)
    {
        $complaint = Complaint::with(['property', 'room', 'staff', 'photos'])->findOrFail($id);

        if ($complaint->status !== 'rejected') {
            return redirect()->route('landlord.complaints.index')
                ->with('error', 'Đã từ chối xử lý khiếu nại.');
        }

        if ($complaint->property->landlord_id !== auth()->id()) {
            abort(403);
        }

        return view('landlord.complaints.rejection', compact('complaint'));
    }

    /**
     * 🔔 Gửi thông báo cho 1 user cụ thể
     */
    private function sendNotificationToUser($userId, $title, $message, $link = null)
    {
        $notification = Notification::create([
            'title' => $title,
            'message' => $message,
            'type' => 'user',
            'link' => $link,
            'created_at' => now(),
            'expired_at' => now()->addDays(7),
            'is_global' => false,
        ]);

        $notification->users()->attach($userId, [
            'is_read' => false,
            'received_at' => Carbon::now(),
        ]);
    }
}
