<?php

namespace App\Http\Controllers\Client;

use App\Models\Notification;
use App\Models\RoomLeaveLog;
use Illuminate\Http\Request;
use App\Models\Landlord\Room;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Landlord\ContractRenewal;
use App\Models\Landlord\RentalAgreement;
use App\Models\Landlord\Staff\Rooms\RoomBill;

use App\Models\Landlord\Staff\Rooms\RoomStaff;

// use App\Models\RoomLeaveLog;


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

        // Lấy danh sách hóa đơn (nếu có phòng)
        $bills = $room ? RoomBill::where('room_id', $room->room_id)->orderByDesc('month')->get() : collect();

        // Lấy hợp đồng đang hoạt động
        $contract = $room ? RentalAgreement::where('room_id', $room->room_id)
            ->where('status', 'active')
            ->latest('end_date')
            ->first() : null;
        // dd($contract);

        $alert = null;
        $alertType = null;
        $showRenewButtons = false;
        $alertType = null;
        if ($contract) {
            $today = Carbon::today();
            $endDate = Carbon::parse($contract->end_date);
           $monthsRemaining = round($today->floatDiffInMonths($endDate));
            $endDateFormatted = $endDate->format('d/m/Y');
            // dd($monthsRemaining);

            if ($monthsRemaining == 2) {
                // dd("đã vào đây");
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
        // dd($hasRenewalPending);
        // Kiểm tra cảnh báo hóa đơn chưa thanh toán
        $showBillReminder = false;
        $billReminderType = null;
        $today = Carbon::create(2025,8,5);
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

    if ($request->input('action') !== 'accept') {
        return back();
    }

    // 1️⃣ Lấy người nhận
    $receiverId = null;

    // Kiểm tra nhân viên phụ trách
    $staffRecord = RoomStaff::where('room_id', $room->room_id)->first();
    if ($staffRecord) {
        $receiverId = $staffRecord->staff_id;
    } else {
        // Nếu không có nhân viên → lấy landlord từ hợp đồng hiện tại
        $agreement = RentalAgreement::where('room_id', $room->room_id)
            ->where('status', 'active')
            ->latest('end_date')
            ->first();

        if ($agreement) {
            $receiverId = $agreement->landlord_id;
        }
    }

    // Nếu không xác định được người nhận → báo lỗi
    if (!$receiverId) {
        return back()->with('renewal_error', 'Không xác định được người nhận thông báo.');
    }

    // 2️⃣ Kiểm tra đã gửi yêu cầu tái ký chưa
    $exists = ContractRenewal::where('room_id', $room->room_id)
        ->where('user_id', auth()->id())
        ->where('status', 'pending')
        ->exists();

    if ($exists) {
        return back()->with('renewal_error', 'Bạn đã gửi yêu cầu tái ký rồi.');
    }

    // 3️⃣ Lưu yêu cầu tái ký
    ContractRenewal::create([
        'room_id'     => $room->room_id,
        'user_id'     => auth()->id(), // Người gửi
        'receiver_id' => $receiverId,  // Người nhận
        'status'      => 'pending',
    ]);

    // 4️⃣ Tạo thông báo
    $notification = Notification::create([
        'title'      => 'Yêu cầu tái ký hợp đồng',
        'message'    => 'Người thuê ' . auth()->user()->name . ' đã gửi yêu cầu tái ký cho phòng ' . $room->name,
        'type'       => 'user', // Vì người gửi là user
        'link'       => route('staff.contract.renewals.index'), // Link xử lý
        'expired_at' => now()->addDays(7),
        'is_global'  => false,
    ]);

    // 5️⃣ Gắn người nhận thông báo vào bảng notification_user
    $notification->users()->attach($receiverId, [
        'is_read'     => false,
        'received_at' => now(),
        'created_at'  => now(),
        'updated_at'  => now(),
    ]);

    // 6️⃣ Trả về thông báo thành công
    return back()->with('renewal_success', 'Gửi yêu cầu tái ký thành công.');
}

}

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

