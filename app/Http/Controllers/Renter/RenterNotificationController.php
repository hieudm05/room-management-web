<?php

namespace App\Http\Controllers\Renter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Notification;
use App\Models\NotificationUser;

class RenterNotificationController extends Controller
{
    // Danh sách thông báo của user hiện tại
    public function index(Request $request)
    {
        $user = $request->user();

        $notifications = $user->notifications()
            ->orderByDesc('notification_user.received_at')
            ->paginate(10);

        return view('home.notifications.index', compact('notifications'));
    }

    // Đánh dấu đã đọc
    public function markAsRead(Request $request, $id)
    {
        $user = $request->user();

        $user->notifications()
            ->updateExistingPivot($id, [
                'is_read' => true,
                'read_at' => now(),
            ]);

        return back();
    }
    public function destroy($id)
    {
        $user = auth()->user();

        // Nếu bạn dùng bảng trung gian như notification_user
        $user->notifications()->detach($id);

        return redirect()->back()->with('success', 'Đã xoá thông báo.');
    }
    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);

        if (!empty($ids)) {
            auth()->user()->notifications()->detach($ids);
            return back()->with('success', 'Đã xoá các thông báo đã chọn.');
        }

        return back()->with('error', 'Bạn chưa chọn thông báo nào để xoá.');
    }
}
