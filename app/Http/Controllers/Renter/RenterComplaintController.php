<?php

namespace App\Http\Controllers\Renter;

use App\Models\User;
use App\Models\Notification;
use App\Models\NotificationUser;
use App\Models\Landlord\RentalAgreement;
use App\Http\Controllers\Controller;
use App\Models\ComplaintPhoto;
use App\Models\Complaint;
use App\Models\CommonIssue;
use App\Models\Landlord\Property;
use App\Models\Landlord\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class RenterComplaintController extends Controller
{
    // Hiển thị form gửi khiếu nại
    public function create()
    {
        $user = auth()->user();

        $rental = RentalAgreement::with(['room.property'])
            ->where('renter_id', $user->id)
            ->whereIn('status', ['Signed', 'Active'])
            ->latest()
            ->first();

        if (!$rental) {
            return back()->with('error', 'Bạn chưa có hợp đồng thuê đang hoạt động.');
        }

        $room = $rental->room;
        $property = $room->property;
        $commonIssues = CommonIssue::all();

        return view('home.complaints.form', compact('room', 'property', 'commonIssues'));
    }

    // Lưu khiếu nại
    public function store(Request $request)
    {
        $request->validate([
            'property_id' => 'required',
            'room_id' => 'required',
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'common_issue_id' => 'required',
            'detail' => 'nullable|string',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $room = Room::with('staffs')->findOrFail($request->room_id);
        $assignedStaff = $room->staffs->first();

        $complaint = Complaint::create([
            'property_id' => $request->property_id,
            'room_id' => $request->room_id,
            'full_name' => $request->full_name,
            'phone' => $request->phone,
            'common_issue_id' => $request->common_issue_id,
            'title' => $request->title,
            'detail' => $request->detail,
            'staff_id' => optional($assignedStaff)->id,
            'status' => 'pending',
            'user_id' => auth()->id(),

        ]);
            

       if ($request->hasFile('photos')) {
    foreach ($request->file('photos') as $file) {
        $path = $file->store('complaints', 'public');

        ComplaintPhoto::create([
            'complaint_id' => $complaint->id,
            'photo_path' => $path,
            'type' => 'initial', // Gán type 'initial' cho ảnh khi gửi khiếu nại
        ]);
    }
} 

        // Gửi thông báo cho nhân viên nếu có
   
        $landlord = $room->property->landlord ?? null;
if ($landlord) {
    $this->sendNotificationToUser(
        $landlord->id,
        '📬 Bạn vừa nhận được một khiếu lại mới từ phòng ',
        'Phòng ' . $room->room_number  ,
        route('landlord.complaints.show', $complaint->id) // bạn có thể thay route này cho phù hợp
    );
}

        return redirect()->route('home.complaints.index')->with('success', 'Gửi khiếu nại thành công!');
    }

    public function index()
    {
        $complaints = Complaint::where('user_id', auth()->id())
                               ->orderByDesc('created_at')
                               ->paginate(10);

        return view('home.complaints.index', compact('complaints'));
    }

    public function show(Complaint $complaint)
    {
        $this->authorizeView($complaint);
        return view('home.complaints.show', compact('complaint'));
    }

    public function cancel(Complaint $complaint)
    {
        $this->authorizeView($complaint);

        if ($complaint->status !== 'pending') {
            return back()->with('error', 'Chỉ được hủy khiếu nại khi đang chờ xử lý.');
        }

        $complaint->update(['status' => 'cancelled']);

        return back()->with('success', 'Đã hủy khiếu nại.');
    }

    public function edit(Complaint $complaint)
    {
        $this->authorizeView($complaint);

        if ($complaint->status !== 'pending') {
            return redirect()->route('home.complaints.index')->with('error', 'Chỉ được sửa khiếu nại đang chờ xử lý.');
        }

        $commonIssues = CommonIssue::all();
        return view('home.complaints.edit', compact('complaint', 'commonIssues'));
    }

    public function update(Request $request, Complaint $complaint)
    {
        $this->authorizeView($complaint);

        if ($complaint->status !== 'pending') {
            return redirect()->route('home.complaints.index')->with('error', 'Không thể cập nhật khiếu nại đã xử lý.');
        }

        $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'common_issue_id' => 'required|exists:common_issues,id',
            'detail' => 'nullable|string',
            'photos.*' => 'nullable|image|max:5120',
        ]);

        $complaint->update($request->only('full_name', 'phone', 'common_issue_id', 'detail'));

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('complaints', 'public');
                ComplaintPhoto::create([
                    'complaint_id' => $complaint->id,
                    'photo_path' => $path,
                ]);
            }
        }

        return redirect()->route('home.complaints.index')->with('success', 'Cập nhật khiếu nại thành công.');
    }

    public function destroy(Complaint $complaint)
    {
        $this->authorizeView($complaint);

        if ($complaint->status !== 'pending') {
            return redirect()->route('home.complaints.index')->with('error', 'Chỉ được xóa khiếu nại đang chờ xử lý.');
        }

        foreach ($complaint->photos as $photo) {
            Storage::disk('public')->delete($photo->photo_path);
            $photo->delete();
        }

        $complaint->delete();

        return redirect()->route('home.complaints.index')->with('success', 'Đã xóa khiếu nại.');
    }

    // 🔒 Đảm bảo chỉ người gửi mới xem/sửa khiếu nại
    private function authorizeView(Complaint $complaint)
    {
        if ($complaint->user_id !== auth()->id()) {
            abort(403, 'Bạn không có quyền truy cập khiếu nại này.');
        }
    }

    // 🔔 Gửi thông báo
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

        NotificationUser::create([
            'notification_id' => $notification->id,
            'user_id' => $userId,
            'is_read' => false,
            'received_at' => Carbon::now(),
        ]);
    }
}
