<?php

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use App\Models\Landlord\Room;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Landlord\ContractRenewal;
use App\Models\Landlord\RentalAgreement;
use App\Models\Landlord\Staff\Rooms\RoomBill;
use App\Models\RoomLeaveLog;

class MyRoomController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today();

        // Lấy room_id từ info
        $roomId = $user->info?->room_id;

        if (!$roomId) {
            return redirect()->back()->with('error', 'Bạn chưa được gán vào phòng nào.');
        }

        // Kiểm tra đã rời phòng chưa
        $hasLeftRoom = RoomLeaveLog::where('user_id', $user->id)
            ->where('room_id', $roomId)
            ->where('status', 'Approved')
            ->whereDate('leave_date', '<=', $today)
            ->exists();

        // Nếu đã rời phòng thì không lấy thông tin chi tiết nữa
       $room = $hasLeftRoom ? null : Room::with(['property', 'currentUserInfos'])->find($roomId);

        // Nếu không tìm thấy phòng và cũng không phải người đã rời phòng → lỗi
        if (!$room && !$hasLeftRoom) {
            return redirect()->back()->with('error', 'Không tìm thấy thông tin phòng.');
        }

        // Lấy danh sách hóa đơn
        $bills = RoomBill::where('room_id', $room->room_id)->orderByDesc('month')->get();

        // Kiểm tra hợp đồng hết hạn
       $contract = RentalAgreement::where('room_id', $room->room_id)
    ->where('status', 'active')
    ->latest('end_date')
    ->first();

$alert = null;
$monthsRemaining = null;
$endDateFormatted = null;
$showRenewButtons = false;
$alertType = null;

if ($contract) {
    $today = Carbon::today();
    $endDate = Carbon::parse($contract->end_date);
    $monthsRemaining = floor($today->floatDiffInMonths($endDate));
    $endDateFormatted = $endDate->format('d/m/Y');

    if ($monthsRemaining == 2) {
        $alert = "⚠️ Hợp đồng phòng sắp hết hạn! Còn 2 tháng nữa ($endDateFormatted).";
        $alertType = 'warning';
    } elseif ($monthsRemaining == 1) {
        $alert = "⚠️ Hợp đồng phòng sắp hết hạn! Còn 1 tháng nữa ($endDateFormatted).";
        $alertType = 'danger';
        $showRenewButtons = true;
    }
}
$hasRenewalPending = $room->contractRenewals()
    ->where('user_id', auth()->id())
    ->where('status', 'pending')
    ->exists();

    // cảnh báo chưa đóng tiền khi đã có hóa đơn từ mùng 1-5 của tháng
    $showBillReminder = false;
$billReminderType = null;
$today = Carbon::today();
// $today = Carbon::create(2025,8,5);
$day = $today->day;

// Kiểm tra nếu đã có hóa đơn tháng này nhưng chưa thanh toán
$unpaidBill = RoomBill::where('room_id', $room->room_id)
    ->whereMonth('month', $today->month)
    ->whereYear('month', $today->year)
    ->where('status', '!=', 'paid')
    ->exists();

if ($unpaidBill && $day >= 4) {
    $showBillReminder = true;

    if ($day == 4) {
        $billReminderType = 'warning'; // Màu vàng
    } elseif ($day >= 5) {
        $billReminderType = 'danger'; // Màu đỏ + icon tức giận
    }
}



        return view('home.my-room', compact('room', 'bills', 'alert', 'showRenewButtons', 'contract','alertType','hasRenewalPending','showBillReminder','billReminderType'));

    }

        $hasRenewalPending = $room ? $room->contractRenewals()
            ->where('user_id', auth()->id())
            ->where('status', 'pending')
            ->exists() : false;

        // Kiểm tra cảnh báo hóa đơn chưa thanh toán
        $showBillReminder = false;
        $billReminderType = null;

        $day = $today->day;

        $unpaidBill = $room ? RoomBill::where('room_id', $room->room_id)
            ->whereMonth('month', $today->month)
            ->whereYear('month', $today->year)
            ->where('status', '!=', 'paid')
            ->exists() : false;

        if ($unpaidBill && $day >= 4) {
            $showBillReminder = true;
            $billReminderType = $day == 4 ? 'warning' : 'danger';
        }

        return view('home.my-room', compact(
            'room',
            'bills',
            'alert',
            'alertType',
            'showRenewButtons',
            'contract',
            'hasRenewalPending',
            'showBillReminder',
            'billReminderType',
            'hasLeftRoom'
        ));
    }

    public function renew(Request $request, $roomId)
    {
        $room = Room::findOrFail($roomId);

        if ($request->input('action') === 'accept') {
            $exists = ContractRenewal::where('room_id', $room->room_id)
                ->where('user_id', auth()->id())
                ->where('status', 'pending')
                ->exists();

            if ($exists) {
                return back()->with('error', 'Bạn đã gửi yêu cầu tái ký rồi.');
            }

            ContractRenewal::create([
                'room_id' => $room->room_id,
                'user_id' => auth()->id(),
                'status' => 'pending',
            ]);

            return back()->with('success', 'Gửi yêu cầu tái ký thành công.');
        }

        return back();
    }
}
