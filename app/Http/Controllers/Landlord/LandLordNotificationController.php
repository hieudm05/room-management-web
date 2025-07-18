<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Notification;
class LandLordNotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()
            ->customNotifications()
            ->withPivot('is_read', 'received_at')
            ->orderByDesc('notification_user.received_at')
            ->paginate(10);

        return view('landlord.notifications.index', compact('notifications'));
    }

    public function markAsRead($id)
    {
        $notification = Auth::user()->customNotifications()->where('notifications.id', $id)->firstOrFail();

        $notification->pivot->is_read = true;
        $notification->pivot->read_at = now();
        $notification->pivot->save();

        return redirect($notification->link ?? route('landlord.notifications.index'));
    }
 public function destroy($id)
{
    $user = auth()->user();

    $notification = $user->customNotifications()->where('notifications.id', $id)->first();

    if (!$notification) {
        return back()->with('error', 'Không tìm thấy thông báo.');
    }

    $user->customNotifications()->detach($id);

    return back()->with('success', 'Đã xoá thông báo.');
}
   


public function bulkDelete(Request $request)
{
    $ids = $request->input('ids', []);

    if (empty($ids)) {
        return back()->with('error', 'Vui lòng chọn ít nhất một thông báo để xoá.');
    }

    Auth::user()->customNotifications()->detach($ids);

    return back()->with('success', 'Đã xoá các thông báo đã chọn.');
}
}
