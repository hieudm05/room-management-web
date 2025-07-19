<?php

namespace App\Http\Controllers\Landlord\Staff;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\ComplaintPhoto;

class StaffComplaintController extends Controller
{
    public function index()
    {
        $staffId = auth()->id();

        $complaints = Complaint::where('staff_id', $staffId)
                               ->where('status', 'in_progress')
                               ->latest()
                               ->get();

        return view('landlord.staff.complaints.index', compact('complaints'));
    }

    public function edit($id)
    {
        $complaint = Complaint::with(['room', 'property', 'photos'])->findOrFail($id);

        $this->authorizeComplaint($complaint);

        return view('landlord.staff.complaints.form', compact('complaint'));
    }

    public function resolve(Request $request, $id)
{
    $request->validate([
        'user_cost' => 'nullable|numeric|min:0',
        'landlord_cost' => 'nullable|numeric|min:0',
        'note' => 'nullable|string|max:1000',
        'photos.*' => 'nullable|image|max:5120',
    ]);

    $complaint = Complaint::findOrFail($id);
    $this->authorizeComplaint($complaint);

    $userCost = $request->filled('user_cost') ? (float) $request->input('user_cost') : 0;
    $landlordCost = $request->filled('landlord_cost') ? (float) $request->input('landlord_cost') : 0;

    $complaint->update([
        'user_cost' => $userCost,
        'landlord_cost' => $landlordCost,
        'note' => $request->note,
        'status' => 'resolved',
        'resolved_at' => now(),
    ]);
    // ✅ Upload ảnh xử lý nếu có
    if ($request->hasFile('photos')) {
        foreach ($request->file('photos') as $file) {
            $path = $file->store('complaints', 'public');

            ComplaintPhoto::create([
                'complaint_id' => $complaint->id,
                'photo_path' => $path,
                'type' => 'resolved', // nếu muốn phân loại ảnh sau xử lý
            ]);
        }
    }

    // ✅ Gửi thông báo cho chủ trọ và người thuê
    $this->notifyUsers($complaint, 'resolved');

    return redirect()->route('landlords.staff.complaints.index')
                     ->with('success', 'Đã xử lý khiếu nại và cập nhật ảnh.');
}


    public function rejectForm($id)
    {
        $complaint = Complaint::findOrFail($id);
        $this->authorizeComplaint($complaint);

        return view('landlord.staff.complaints.reject', compact('complaint'));
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'reject_reason' => 'required|string|max:1000',
        ]);

        $complaint = Complaint::findOrFail($id);
        $this->authorizeComplaint($complaint);

        if ($complaint->status !== 'in_progress') {
            return redirect()->route('landlords.staff.complaints.index')
                             ->with('error', 'Chỉ được từ chối khi đang xử lý.');
        }

        $complaint->update([
            'status' => 'rejected',
            'reject_reason' => $request->reject_reason,
        ]);

        // Gửi thông báo
        $this->notifyUsers($complaint, 'rejected');

        return redirect()->route('landlords.staff.complaints.index')
                         ->with('success', 'Đã từ chối xử lý khiếu nại.');
    }

    /**
     * Kiểm tra quyền xử lý khiếu nại.
     */
    private function authorizeComplaint(Complaint $complaint)
    {
        if ($complaint->staff_id !== auth()->id()) {
            abort(403, 'Bạn không có quyền xử lý khiếu nại này.');
        }
    }

    /**
     * Gửi thông báo cho user và landlord.
     */
    private function notifyUsers(Complaint $complaint, string $type)
    {
        $roomName = optional($complaint->room)->name;
        $link = route('landlord.complaints.index', $complaint->id);

        if ($type === 'resolved') {
            // Gửi cho chủ trọ
            if ($complaint->landlord_id) {
                $this->sendNotificationToUser(
                    $complaint->landlord_id,
                    '✅ Khiếu nại đã xử lý',
                    "Nhân viên đã xử lý khiếu nại tại phòng {$roomName}.",
                    $link
                );
            }

            // Gửi cho người thuê
            if ($complaint->user_id) {
                $this->sendNotificationToUser(
                    $complaint->user_id,
                    '✅ Khiếu nại đã xử lý',
                    "Khiếu nại của bạn tại phòng {$roomName} đã được xử lý.",
                    $link
                );
            }
        }

        if ($type === 'rejected') {
            // Gửi cho chủ trọ
            $landlordId = optional(optional($complaint->room)->property)->landlord_id;
            if ($landlordId) {
                $this->sendNotificationToUser(
                    $landlordId,
                    '❌ Nhân viên từ chối khiếu nại',
                    "Khiếu nại tại phòng {$complaint->room->room_number} đã bị từ chối.",
                    $link
                );
            }

            // Gửi cho người thuê
            if ($complaint->user_id) {
                $this->sendNotificationToUser(
                    $complaint->user_id,
                    '❌ Khiếu nại bị từ chối xử lý',
                    "Nhân viên đã từ chối xử lý khiếu nại của bạn tại phòng {$complaint->room->room_number}.",
                    $link
                );
            }
        }
    }

    /**
     * Gửi thông báo cho một người dùng.
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
