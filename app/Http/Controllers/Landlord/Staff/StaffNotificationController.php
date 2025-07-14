<?php

namespace App\Http\Controllers\Landlord\Staff;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class StaffNotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()
            ->notifications()
            ->withPivot('is_read', 'received_at')
            ->orderByDesc('notification_user.received_at')
            ->paginate(10);

        return view('landlord.staff.notifications.index', compact('notifications'));
    }

    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->where('notifications.id', $id)->firstOrFail();

        $notification->pivot->is_read = true;
        $notification->pivot->read_at = now();
        $notification->pivot->save();

        return redirect($notification->link ?? route('landlord.staff.notifications.index'));
    }
    public function destroy($id)
{
    $notification = Auth::user()->notifications()->where('notifications.id', $id)->first();

    if (!$notification) {
        return back()->with('error', 'Không tìm thấy thông báo.');
    }

    Auth::user()->notifications()->detach($id);

    return back()->with('success', 'Đã xoá thông báo.');
}
    public function bulkDelete(Request $request)
{
    $request->validate([
        'ids' => 'required|array',
        'ids.*' => 'uuid', // assuming notification IDs are UUID
    ]);

    $user = Auth::user();

    // Xóa khỏi bảng trung gian notification_user
    $user->notifications()->detach($request->ids);

    return redirect()->route('landlord.staff.notifications.index')->with('success', 'Đã xóa thông báo được chọn.');
}
}