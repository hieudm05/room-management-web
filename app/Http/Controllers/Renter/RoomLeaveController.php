<?php

namespace App\Http\Controllers\Renter;

use App\Http\Controllers\Controller;
use App\Models\DepositRefund;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\Notification;
use App\Models\NotificationUser;
use App\Models\UserInfo;
use App\Models\RentalAgreement;
use App\Models\Landlord\Room;
use App\Models\Landlord\RoomLeaveRequest;
use App\Models\RoomLeaveLog;
use App\Models\User;
class RoomLeaveController extends Controller
{
    /**
     * Trang danh sách yêu cầu rời phòng
     */
  public function index()
{
    $userId = Auth::id();
    $userInfo = UserInfo::where('user_id', $userId)->first();

    if (!$userInfo || !$userInfo->room_id) {
        return redirect()->back()->withErrors([
            'message' => '❌ Bạn chưa thuộc phòng nào.'
        ]);
    }

    // Lấy phòng hiện tại
    $room = Room::with(['userInfos.user', 'rentalAgreement.renter'])->findOrFail($userInfo->room_id);
    
 
    $isContractOwner = optional($room->rentalAgreement)->renter_id === $userId;

    $leaveRequests = RoomLeaveRequest::where('room_id', $room->room_id)
        ->when(!$isContractOwner, fn ($q) => $q->where('user_id', $userId))
        ->latest()
        ->get();

    $incomingTransferRequest = RoomLeaveRequest::with('room.property', 'user')
        ->where('new_renter_id', $userId)
        ->where('status', 'waiting_new_renter_accept')
        ->whereNull('transfer_accepted_at')
        ->where('action_type', 'transfer')
        ->first();

    return view('home.roomleave.stopRentForm', compact(
        'room',
        'userId',
        'isContractOwner',
        'leaveRequests',
        'incomingTransferRequest',

      
    ));
}

/**
 * Gửi yêu cầu rời phòng
 */
public function sendLeaveRequest(Request $request)
{
    //  dd($request->file('qr_file'));

    $userId = Auth::id();
       $room = Room::with(['rentalAgreement', 'property'])->findOrFail($request->room_id);
    $isOwner = optional($room->rentalAgreement)->renter_id === $userId;

    $request->validate([
        'room_id'       => 'required|exists:rooms,room_id',
        'leave_date'    => 'required|date|after_or_equal:today',
        'note'          => 'nullable|string|max:255',
        'action_type'   => 'required|in:leave,transfer,leave_all',
        'new_renter_id' => 'nullable|exists:users,id',
        'deposit_qr_image' => [
          function ($attribute, $value, $fail) use ($request, $room, $isOwner) {
        if ($isOwner && $room->rentalAgreement) {
            $endDate = $room->rentalAgreement->end_date;

            // Chủ hợp đồng chọn leave sau ngày hết hạn
            if (
                $request->action_type === 'leave' &&
                $request->leave_date >= $endDate &&
                !$request->hasFile('deposit_qr_image')
            ) {
                $fail('Vui lòng tải QR để nhận lại tiền cọc.');
            }

            // Chủ hợp đồng chọn leave_all sau ngày hết hạn
            if (
                $request->action_type === 'leave_all' &&
                $request->leave_date >= $endDate &&
                !$request->hasFile('deposit_qr_image')
            ) {
                $fail('Rời phòng toàn bộ sau khi hết hạn hợp đồng cần QR để hoàn tiền cọc.');
            }
        }
    },
        'image',
        'mimes:png,jpg,jpeg,webp',
        'max:2048',
    ],
]);
    
     
    // Chỉ bắt buộc QR khi CHỦ hợp đồng chọn "leave"


    // Không gửi thay người khác
    if ($userId != $request->user_id) {
        return back()->withErrors('Không thể gửi yêu cầu thay người khác.');
    }

    // Không chuyển nhượng cho chính mình
    if ($request->action_type === 'transfer' && $request->new_renter_id == $userId) {
        return back()->withErrors('Không thể chuyển nhượng cho chính bạn.');
    }

    $userInfo = UserInfo::where('user_id', $userId)->firstOrFail();
    $room = Room::with(['rentalAgreement', 'property'])->findOrFail($request->room_id);

    // Chỉ chủ hợp đồng mới được transfer
    $isOwner = $room->rentalAgreement && $room->rentalAgreement->renter_id == $userId;
    if ($request->action_type === 'transfer' && !$isOwner) {
        return back()->withErrors('Chỉ chủ hợp đồng mới có quyền nhượng hợp đồng.');
    }

    // Kiểm tra yêu cầu đang chờ xử lý
    $hasPending = RoomLeaveRequest::where('user_id', $userId)
        ->where('status', 'pending')
        ->exists();
    if ($hasPending) {
        return back()->withErrors('Bạn đã gửi yêu cầu và đang chờ xử lý.');
    }

    // Validate leave_date với hợp đồng
    $depositImagePath = null;
if ($request->hasFile('deposit_qr_image')) {
    $depositImagePath = $request->file('deposit_qr_image')->store('deposits', 'public');
  
}

// Tạo yêu cầu rời phòng
$leaveRequest = new RoomLeaveRequest([
    'user_id'          => $userId,
    'room_id'          => $room->room_id,
    'leave_date'       => $request->leave_date,
    'note'             => $request->note,
    'status'           => 'pending',
    'landlord_id'      => $room->property->landlord_id ?? null,
    'action_type'      => $request->action_type,
    'new_renter_id'    => $request->action_type === 'transfer' ? $request->new_renter_id : null,
    'deposit_qr_image' =>   $depositImagePath, // đúng tên cột
   
]);


$leaveRequest->save();
    // Gửi notification cho landlord
    $landlord = $room->property->landlord ?? null;
    if ($landlord) {
        $this->sendNotificationToUser(
            $landlord->id,
            '📤 Yêu cầu rời phòng mới',
            'Người thuê ' . auth()->user()->name . ' đã gửi yêu cầu rời phòng ' . $room->room_number,
            route('landlord.roomleave.index', $leaveRequest->id)
        );
    }

    return redirect()->route('home.roomleave.stopRentForm')
        ->with('success', '✅ Yêu cầu đã được gửi.');
}


    /**
     * Hủy yêu cầu
     */
    public function cancelRequest($id)
    {
        $userId = Auth::id();

        $request = RoomLeaveRequest::where('id', $id)
            ->where('user_id', $userId)
            ->where('status', 'pending')
            ->first();

        if (!$request) {
            return redirect()->back()->withErrors('Không thể hủy yêu cầu này.');
        }

        $request->delete();

        return redirect()->route('home.roomleave.stopRentForm')
            ->with('success', '🗑️ Yêu cầu đã được hủy.');
    }

    /**
     * Xem chi tiết yêu cầu
     */
    public function viewRequest($id)
    {
        $userId = Auth::id();

        $request = RoomLeaveRequest::with(['room.property', 'newRenter'])
            ->where('id', $id)
            ->where('user_id', $userId)
            ->first();

        if (!$request) {
            return redirect()->back()->withErrors('Không tìm thấy yêu cầu.');
        }

        return view('home.roomleave.viewRequest', [
            'request' => $request
        ]);
    }

    /**
     * DUYỆT yêu cầu
     */
    public function approveRequest($id)
    {
        $request = RoomLeaveRequest::with(['userInfo', 'room', 'room.rentalAgreement'])->findOrFail($id);

        if ($request->status !== 'Pending') {
            return back()->withErrors('Yêu cầu đã được xử lý.');
        }

        DB::transaction(function () use ($request) {
            $request->status = 'Approved';
            $request->approved_at = now();
            $request->save();

            $userInfo = $request->userInfo;

            if ($request->action_type === 'transfer') {
            
                $contract = $request->room->rentalAgreement;

                if ($contract && $contract->renter_id == $request->user_id) {
                    $contract->renter_id = $request->new_renter_id;
                    $contract->save();

                    UserInfo::updateOrCreate(
                        ['user_id' => $request->new_renter_id],
                        ['room_id' => $request->room_id]
                    );
                }
            }

            // Người cũ rời khỏi phòng
            $userInfo->room_id = null;
            $userInfo->save();
        });

        return back()->with('success', '✅ Yêu cầu đã được duyệt.');
    }

    /**
     * TỪ CHỐI yêu cầu
     */
    public function rejectRequest($id)
    {
        $request = RoomLeaveRequest::findOrFail($id);

        if ($request->status !== 'Pending') {
            return back()->withErrors('Yêu cầu đã được xử lý.');
        }

        $request->status = 'Rejected';
        $request->rejected_at = now();
        $request->save();

        return back()->with('success', '❌ Yêu cầu đã bị từ chối.');
    }
    public function finalize($id)
{
    $request = RoomLeaveRequest::findOrFail($id);

    if ($request->user_id !== auth()->id() || !in_array($request->status, ['approved', 'Approved'])) {
        abort(403, 'Bạn không có quyền thực hiện hành động này');
    }

    DB::transaction(function () use ($request) {

    // Rời riêng lẻ
    if ($request->action_type === 'leave') {
        UserInfo::where('user_id', $request->user_id)
            ->where('room_id', $request->room_id)
            ->update([
                'room_id'   => null,
                'rental_id' => null,
                'active'    => 0,
                'left_at'   => now(),
            ]);

        RoomLeaveLog::create([
            'user_id'            => $request->user_id,
            'room_id'            => $request->room_id,
            'rental_id'          => optional($request->room->rentalAgreement)->rental_id,
            'leave_date'         => $request->leave_date,
            'action_type'        => 'leave',
            'previous_renter_id' => $request->user_id,
            'new_renter_id'      => null,
            'reason'             => $request->note,
            'status'             => 'Approved',
            'handled_by'         => $request->approved_by ?? null,
        ]);

        // 🔔 Thông báo cho landlord
        $this->sendNotificationToUser(
            $request->landlord_id,
            '🏠 Người thuê đã rời phòng',
            'Người thuê ' . $request->user->name . ' đã rời khỏi phòng ' . $request->room->room_number,
            route('landlord.roomleave.processed')
        );
    }

    // Rời tất cả
    if ($request->action_type === 'leave_all') {
        $usersInRoom = UserInfo::where('room_id', $request->room_id)->get();

        // Cập nhật tất cả user rời phòng
        UserInfo::where('room_id', $request->room_id)
            ->update([
                'room_id'   => null,
                'rental_id' => null,
                'active'    => 0,
                'left_at'   => now(),
            ]);

        // Ghi log cho từng user
        foreach ($usersInRoom as $user) {
            RoomLeaveLog::create([
                'user_id'            => $user->user_id,
                'room_id'            => $request->room_id,
                'rental_id'          => optional($request->room->rentalAgreement)->rental_id,
                'leave_date'         => $request->leave_date,
                'action_type'        => 'leave_all',
                'previous_renter_id' => $user->user_id,
                'new_renter_id'      => null,
                'reason'             => $request->note,
                'status'             => 'Approved',
                'handled_by'         => $request->approved_by ?? null,
            ]);
        }

        // Cập nhật trạng thái phòng
        $request->room->update(['status' => 'available']);

        // 🔔 Thông báo cho landlord
        $this->sendNotificationToUser(
            $request->landlord_id,
            '🏠 Tất cả người thuê đã rời phòng',
            'Tất cả người thuê đã rời khỏi phòng ' . $request->room->room_number . '. Phòng đã sẵn sàng cho thuê mới.',
route('landlord.rooms.show', $request->room_id)
        );
    }

    // Cập nhật trạng thái yêu cầu
    $request->status = 'approved';
    $request->save();
});

return redirect()->route('renter')->with('success', 'Bạn đã rời phòng thành công!');

}

    public function confirmTransfer()
{
    $userId = Auth::id();

    $pending = RoomLeaveRequest::with('room.property', 'user')
        ->where('new_renter_id', $userId)
        ->where('status', 'waiting_new_renter_accept') 
        ->whereNull('transfer_accepted_at')
        ->where('action_type', 'transfer')
        ->first();

    return view('home.roomleave.confirmTransfer', compact('pending'));
}
public function acceptTransfer(Request $request)
{
    $userId = Auth::id();

    $leaveRequest = RoomLeaveRequest::where('new_renter_id', $userId)
        ->where('status', 'waiting_new_renter_accept')
        ->where('action_type', 'transfer')
        ->whereNull('transfer_accepted_at')
        ->firstOrFail();

    DB::transaction(function () use ($leaveRequest, $userId) {
        // Cập nhật trạng thái
        $leaveRequest->transfer_accepted_at = now();
        $leaveRequest->status = 'approved';
        $leaveRequest->save();

        // Gán người mới vào hợp đồng
        if ($leaveRequest->room->rentalAgreement) {
            $leaveRequest->room->rentalAgreement->renter_id = $userId;
            $leaveRequest->room->rentalAgreement->save();
        }

        // Gán UserInfo cho người mới
        UserInfo::updateOrCreate(
            ['user_id' => $userId],
            [
                'room_id' => $leaveRequest->room_id,
                'rental_id' => $leaveRequest->room->rentalAgreement->rental_id ?? null,
                'active' => 1
            ]
        );

        UserInfo::where('user_id', $leaveRequest->user_id)
            ->update([
                'room_id' => null,
                'rental_id' => null,
                'active' => 0
            ]);
    });

    return redirect()->route('home.roomleave.stopRentForm')
        ->with('success', 'Bạn đã nhận chuyển nhượng hợp đồng thành công!');
}
 public function depositHistory()
{
    $userId = Auth::id();

    $refunds = DepositRefund::where('user_id', $userId)
        ->with('rental.room.property')
        ->orderByDesc('refund_date')
        ->get();

    return view('home.roomleave.deposits', compact('refunds'));
}
   public function rejectTransfer($id)
{
    $userId = Auth::id();

    $incomingTransferRequest= RoomLeaveRequest::where('id', $id)
        ->where('new_renter_id', $userId)
        ->where('status', 'waiting_new_renter_accept')
        ->firstOrFail();

    DB::transaction(function() use ($incomingTransferRequest) {
        $incomingTransferRequest->status = 'rejected';
        $incomingTransferRequest->transfer_accepted_at = now();
        $incomingTransferRequest->save();

        // Gửi notification cho người nhượng
        $this->sendNotificationToUser(
            $incomingTransferRequest->user_id,
            '❌ Yêu cầu chuyển nhượng bị từ chối',
            'Người được chuyển nhượng đã từ chối hợp đồng của bạn.',
            route('home.roomleave.stopRentForm')
        );
    });

    return redirect()->route('home.roomleave.stopRentForm')
        ->with('success', 'Bạn đã từ chối nhận chuyển nhượng hợp đồng.');
}
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

        $notification->users()->attach($userId, [
            'is_read' => false,
            'received_at' => now(),
        ]);
    }
  

}