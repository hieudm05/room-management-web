<?php

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use App\Models\Landlord\Room;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Landlord\ContractRenewal;
use App\Models\Landlord\RentalAgreement;
use App\Models\Landlord\Room as LandlordRoom;
use App\Models\Landlord\Staff\Rooms\RoomBill;

class MyRoomController extends Controller
{
    public function index()
    {

        $user = Auth::user();
        $roomId = $user->info?->room_id; // dấu ? để tránh lỗi nếu info null

        if (!$roomId) {
            return redirect()->back()->with('error', 'Bạn chưa được gán vào phòng nào.');
        }

        $room = LandlordRoom::with('property')->find($roomId);

        if (!$room) {
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
// $today = Carbon::today();
$today = Carbon::create(2025,8,5);
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

