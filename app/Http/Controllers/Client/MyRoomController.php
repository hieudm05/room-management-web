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

class MyRoomController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today();

        $roomId = $user->info?->room_id;
        if (!$roomId) {
            return redirect()->back()->with('error', 'Bạn chưa được gán vào phòng nào.');
        }

        $hasLeftRoom = RoomLeaveLog::where('user_id', $user->id)
            ->where('room_id', $roomId)
            ->where('status', 'Approved')
            ->whereDate('leave_date', '<=', $today)
            ->exists();

        $room = $hasLeftRoom ? null : Room::with(['property', 'currentUserInfos'])->find($roomId);
        if (!$room && !$hasLeftRoom) {
            return redirect()->back()->with('error', 'Không tìm thấy thông tin phòng.');
        }

        $bills = $room ? RoomBill::where('room_id', $room->room_id)->orderByDesc('month')->get() : collect();

        $contract = $room ? RentalAgreement::where('room_id', $room->room_id)
            ->where('status', 'active')
            ->latest('end_date')
            ->first() : null;

        $alert = null;
        $alertType = null;
        $showRenewButtons = false;

        if ($contract) {
            $today = Carbon::today();
            $endDate = Carbon::parse($contract->end_date);
            $monthsRemaining = round($today->floatDiffInMonths($endDate));
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

        // Tránh lỗi khi $room = null
        $hasRenewalPending = $room ? $room->contractRenewals()
            ->where('user_id', auth()->id())
            ->where('status', 'pending')
            ->exists() : false;

        // Nhắc nhở hóa đơn
        $showBillReminder = false;
        $billReminderType = null;

        // Lưu ý: bạn đang set cứng ngày để test
        $today = Carbon::create(2025, 8, 5);
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

        // 1) Xác định người nhận
        $receiverId = null;

        $staffRecord = RoomStaff::where('room_id', $room->room_id)->first();
        if ($staffRecord) {
            $receiverId = $staffRecord->staff_id;
        } else {
            $agreement = RentalAgreement::where('room_id', $room->room_id)
                ->where('status', 'active')
                ->latest('end_date')
                ->first();

            if ($agreement) {
                $receiverId = $agreement->landlord_id;
            }
        }

        if (!$receiverId) {
            return back()->with('renewal_error', 'Không xác định được người nhận thông báo.');
        }

        // 2) Kiểm tra trùng yêu cầu đang pending
        $exists = ContractRenewal::where('room_id', $room->room_id)
            ->where('user_id', auth()->id())
            ->where('status', 'pending')
            ->exists();

        if ($exists) {
            return back()->with('renewal_error', 'Bạn đã gửi yêu cầu tái ký rồi.');
        }

        // 3) Lưu yêu cầu
        ContractRenewal::create([
            'room_id'     => $room->room_id,
            'user_id'     => auth()->id(),
            'receiver_id' => $receiverId,
            'status'      => 'pending',
        ]);

        // 4) Tạo thông báo cho người nhận
        $notification = Notification::create([
            'title'      => 'Yêu cầu tái ký hợp đồng',
            'message'    => 'Người thuê ' . auth()->user()->name . ' đã gửi yêu cầu tái ký cho phòng ' . ($room->name ?? $room->room_id),
            'type'       => 'user',
            'link'       => route('staff.contract.renewals.index'), // hoặc route khác bạn muốn
            'expired_at' => now()->addDays(7),
            'is_global'  => false,
        ]);

        $notification->users()->attach($receiverId, [
            'is_read'     => false,
            'received_at' => now(),
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        return back()->with('renewal_success', 'Gửi yêu cầu tái ký thành công.');
    }
}
