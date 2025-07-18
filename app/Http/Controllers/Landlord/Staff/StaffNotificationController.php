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
            ->customNotifications()
            ->withPivot('is_read', 'received_at')
            ->orderByDesc('notification_user.received_at')
            ->paginate(10);

        return view('landlord.staff.notifications.index', compact('notifications'));
    }

    public function markAsRead($id)
    {
        $notification = Auth::user()
            ->customNotifications() // ✅ Sửa ở đây
            ->where('notifications.id', $id)
            ->firstOrFail();

        $notification->pivot->is_read = true;
        $notification->pivot->read_at = now();
        $notification->pivot->save();

        return redirect($notification->link ?? route('landlord.staff.notifications.index'));
    }

    public function destroy($id)
    {
        $notification = Auth::user()
            ->customNotifications() // ✅ Sửa ở đây
            ->where('notifications.id', $id)
            ->first();

        if (!$notification) {
            return back()->with('error', 'Không tìm thấy thông báo.');
        }

        Auth::user()->customNotifications()->detach($id); // ✅ Sửa ở đây

        return back()->with('success', 'Đã xoá thông báo.');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'uuid',
        ]);

        Auth::user()->customNotifications()->detach($request->ids); // ✅ Sửa ở đây

        return redirect()->route('landlord.staff.notifications.index')
            ->with('success', 'Đã xóa thông báo được chọn.');
    }

    public function markAllAsRead()
    {
        $user = auth()->user();

        $user->customNotifications()
            ->wherePivot('is_read', false)
            ->updateExistingPivot(
                $user->customNotifications()->pluck('notifications.id')->toArray(),
                ['is_read' => true, 'read_at' => now()]
            );

        return redirect()->back()->with('success', 'Đã đánh dấu tất cả thông báo là đã đọc.');
    }
}
