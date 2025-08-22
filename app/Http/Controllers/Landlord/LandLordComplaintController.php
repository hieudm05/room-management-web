<?php

namespace App\Http\Controllers\LandLord;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Complaint;
use App\Models\Notification;
use App\Models\ComplaintPhoto;
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

        if (!$complaint->property || $complaint->property->landlord_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền xem khiếu nại này.');
        }

        return view('landlord.complaints.show', compact('complaint'));
    }

    public function approve($id)
    {
        $complaint = Complaint::with(['room.staffs', 'property'])->findOrFail($id);

        if (!$complaint->property || $complaint->property->landlord_id !== auth()->id()) {
            abort(403, 'Bạn không có quyền duyệt khiếu nại này.');
        }

        $assignedStaff = $complaint->room->staffs->first();

        if ($assignedStaff) {
            $complaint->update([
                'staff_id' => $assignedStaff->id,
                'status' => 'in_progress',
            ]);

            $this->sendNotificationToUser(
                $assignedStaff->id,
                'Bạn được giao xử lý khiếu nại mới',
                'Bạn vừa được giao xử lý một khiếu nại tại phòng ' . $complaint->room->name,
                route('landlord.staff.complaints.index', $complaint->id)
            );
        } else {
            $complaint->update([
                'staff_id' => auth()->id(),
                'status' => 'in_progress',
            ]);

            $this->sendNotificationToUser(
                auth()->id(),
                'Bạn đã nhận xử lý khiếu nại',
                'Bạn đã tự nhận xử lý khiếu nại tại phòng ' . $complaint->room->name,
                route('landlord.complaints.show', $complaint->id)
            );
        }

        if ($complaint->user_id) {
            $this->sendNotificationToUser(
                $complaint->user_id,
                'Khiếu nại đã được tiếp nhận',
                'Chủ trọ đã duyệt và xử lý khiếu nại của bạn.',
                route('home.complaints.index', $complaint->id)
            );
        }

        return redirect()->route('landlord.complaints.index')->with('success', 'Đã duyệt và xử lý khiếu nại.');
    }

    public function assignForm($id)
    {
        $complaint = Complaint::with(['property', 'room.staffs'])->findOrFail($id);

        if (!$complaint->property || $complaint->property->landlord_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền xem khiếu nại này.');
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

        if (!$complaint->property || $complaint->property->landlord_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền xử lý khiếu nại này.');
        }

        $complaint->update([
            'staff_id' => $request->staff_id,
            'status' => 'in_progress',
        ]);

        $this->sendNotificationToUser(
            $request->staff_id,
            'Bạn được giao xử lý khiếu nại',
            'Chủ trọ vừa giao bạn xử lý khiếu nại tại phòng ' . $complaint->room->name,
            route('landlord.staff.complaints.index', $complaint->id)
        );

        if ($complaint->user_id) {
            $this->sendNotificationToUser(
                $complaint->user_id,
                'Khiếu nại đang được xử lý',
                'Chủ trọ đã giao nhân viên xử lý khiếu nại của bạn.',
                route('home.complaints.show', $complaint->id)
            );
        }

        return redirect()->route('landlord.complaints.index')
            ->with('success', 'Đã ủy quyền nhân viên xử lý khiếu nại thành công.');
    }

    public function acceptReject(Request $request, $id)
{
    $complaint = Complaint::findOrFail($id);

    // Xác định action từ form
    $action = $request->input('action');

    if ($action === 'cancel') {
        // Chủ trọ đồng ý từ chối -> đóng khiếu nại
        $complaint->status = 'cancelled';
        $complaint->handled_by = null; // không ai xử lý nữa
        $complaint->save();

        // Thông báo cho người thuê
        Notification::create([
            'title' => 'Khiếu nại đã đóng',
            'message' => "Khiếu nại #{$complaint->id} đã được chủ trọ xác nhận từ chối và đóng lại.",
        ]);

        return back()->with('success', 'Bạn đã đồng ý với lý do từ chối và đóng khiếu nại.');
    } 
    elseif ($action === 'takeover') {
        // Chủ trọ không đồng ý từ chối -> tự xử lý
        $complaint->status = 'in_progress';
        $complaint->handled_by = auth()->id(); // id chủ trọ đang login
        $complaint->save();

        // Thông báo cho người thuê
        Notification::create([
            'title' => 'Chủ trọ tiếp nhận khiếu nại',
            'message' => "Khiếu nại #{$complaint->id} đã được chủ trọ trực tiếp tiếp nhận xử lý.",
        ]);

          return redirect()->route('landlord.complaints.resolve.form', $complaint->id);
    }

    return back()->with('error', 'Hành động không hợp lệ.');
}


    public function showRejection($id)
    {
        $complaint = Complaint::with(['property', 'room', 'staff', 'photos'])->findOrFail($id);

        if ($complaint->status !== 'rejected') {
            return redirect()->route('landlord.complaints.index')->with('error', 'Đã từ chối xử lý khiếu nại.');
        }

        if (!$complaint->property || $complaint->property->landlord_id !== auth()->id()) {
            abort(403, 'Bạn không có quyền xem khiếu nại này.');
        }

        return view('landlord.complaints.reject', compact('complaint'));
    }

    public function rejectAsLandlordForm($id)
    {
        $complaint = Complaint::with('property')->findOrFail($id);

        if (!$complaint->property || $complaint->property->landlord_id !== auth()->id()) {
            abort(403, 'Bạn không có quyền xem khiếu nại này.');
        }

        return view('landlord.complaints.reject', compact('complaint'));
    }

    public function rejectAsLandlord(Request $request, $id)
    {
        $request->validate([
            'reject_reason' => 'required|string|max:1000',
        ]);

        $complaint = Complaint::findOrFail($id);

        if (!$complaint->property || $complaint->property->landlord_id !== auth()->id()) {
            abort(403, 'Bạn không có quyền xử lý khiếu nại này.');
        }

        if (!in_array($complaint->status, ['pending', 'in_progress'])) {
            return redirect()->route('landlord.complaints.index')->with('error', 'Chỉ có thể từ chối khi khiếu nại đang chờ xử lý hoặc đang xử lý.');
        }

        $complaint->update([
            'status' => 'rejected',
            'reject_reason' => $request->reject_reason,
            'handled_by' => auth()->id(),
        ]);

        $this->notifyUsers($complaint, 'rejected');
        if ($request->has('notify_user') && $complaint->user_id) {
    $this->sendNotificationToUser(
        $complaint->user_id,
        'Khiếu nại bị từ chối',
        'Khiếu nại của bạn đã bị từ chối. Lý do: ' . $request->reject_reason,
        route('home.complaints.show', $complaint->id)
    );
}

        return redirect()->route('landlord.complaints.index')->with('success', 'Chủ trọ đã từ chối khiếu nại.');
    }

    public function showResolveForm($id)
    {
        $complaint = Complaint::with(['property', 'room', 'staff', 'photos'])->findOrFail($id);

        if (!$complaint->property || $complaint->property->landlord_id !== auth()->id()) {
            abort(403, 'Bạn không có quyền xem khiếu nại này.');
        }

        if (!in_array($complaint->status, ['pending', 'in_progress'])) {
            return redirect()->route('landlord.complaints.index')->with('error', 'Chỉ có thể xử lý khi khiếu nại đang chờ hoặc đang xử lý.');
        }

        return view('landlord.complaints.resolve', compact('complaint'));
    }

    public function resolveAsLandlord(Request $request, $id)
    {

        $request->validate([
            'user_cost' => 'nullable|numeric|min:0',
            'landlord_cost' => 'nullable|numeric|min:0',
            'note' => 'nullable|string|max:1000',
            'photos.*' => 'nullable|image|max:5120',
        ]);

        $complaint = Complaint::findOrFail($id);

      if (!$complaint->property || $complaint->property->landlord_id !== auth()->id()) {
    abort(403, 'Bạn không có quyền xử lý khiếu nại này.');
}

        $complaint->update([
            'user_cost' => $request->input('user_cost', 0),
            'landlord_cost' => $request->input('landlord_cost', 0),
            'note' => $request->note,
            'status' => 'resolved',
            'resolved_at' => now(),
            'handled_by' => auth()->id(),
        ]);

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $file) {
                $path = $file->store('complaints', 'public');
                ComplaintPhoto::create([
                    'complaint_id' => $complaint->id,
                    'photo_path' => $path,
                    'type' => 'resolved',
                ]);
            }
        }

        $this->notifyUsers($complaint, 'resolved');

        return redirect()->route('landlord.complaints.index')->with('success', 'Chủ trọ đã xử lý khiếu nại thành công.');
    }

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
            'received_at' => now(),
        ]);
    }

    private function notifyUsers($complaint, $status)
    {
        if ($complaint->user_id) {
            $message = match ($status) {
                'resolved' => 'Khiếu nại của bạn đã được chủ trọ xử lý.',
                'rejected' => 'Khiếu nại của bạn đã bị từ chối.',
                default => 'Cập nhật trạng thái khiếu nại.',
            };

            $this->sendNotificationToUser(
                $complaint->user_id,
                'Cập nhật khiếu nại',
                $message,
                route('home.complaints.show', $complaint->id)
            );
        }
    }
}
