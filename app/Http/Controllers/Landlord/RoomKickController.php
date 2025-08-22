<?php
namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Landlord\Room;
use Carbon\Carbon;

class RoomKickController extends Controller
{
    public function checkCanKick(Room $room)
    {
        // Lấy tất cả tenants đang active (không cần theo thứ tự mới nhất)
        $tenants = $room->allUserInfos;

        if ($tenants->isEmpty()) {
            return false; // phòng trống
        }

        // Lấy hóa đơn chưa thanh toán gần nhất
        $bill = $room->bills()
                     ->where('status', 'unpaid')
                     ->orderByDesc('month')
                     ->first();

        if (!$bill) {
            return false; // không có hóa đơn chưa thanh toán
        }

        // Kiểm tra quá hạn 5 ngày
        $dueDatePlus5 = Carbon::parse($bill->month)->addDays(5);

        return Carbon::now()->gt($dueDatePlus5);
    }
}
