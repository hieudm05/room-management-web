<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Landlord\RoomLeaveRequest;
use App\Models\RoomLeaveLog;
use App\Models\Landlord\RentalAgreement;
use App\Models\RoomLeaveLog as ModelsRoomLeaveLog;

use App\Models\Notification;
use Carbon\Carbon;
use App\Models\Landlord\Room;
use App\Models\UserInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LandlordRoomLeaveController extends Controller
{
    public function index()
    {
        $requests = RoomLeaveRequest::with(['user', 'room'])
            ->where('status', 'pending')
            ->where('landlord_id', Auth::id())
            ->get();

        return view('landlord.roomleave.index', compact('requests'));
    }

    public function processed()
    {
        $requests = RoomLeaveRequest::with(['user', 'room', 'newRenter.info'])
            ->where('landlord_id', Auth::id())
            ->whereIn('status', ['approved', 'rejected'])
            ->orderByDesc('handled_at')
            ->get();

        return view('landlord.roomleave.processed', compact('requests'));
    }

    public function show($id)
    {
        $request = RoomLeaveRequest::with(['user', 'room'])->findOrFail($id);
        return view('landlord.roomleave.show', compact('request'));
    }

    public function approve(Request $request, $id)
    {
      $roomLeaveRequest = RoomLeaveRequest::findOrFail($id);
$agreement = RentalAgreement::where('room_id', $roomLeaveRequest->room_id)
    ->where('status', 'active')
    ->first();

// ================== VALIDATE ==================
if ($roomLeaveRequest->action_type === 'leave') {
    // 🔹 Trường hợp 1 người rời
    if ($agreement && $agreement->renter_id === $roomLeaveRequest->user_id) {
        // Chủ hợp đồng
        $request->validate([
            'refund_deposit' => 'required|in:0,1',
            'refund_reason'  => $request->input('refund_deposit') == "0" 
                                ? 'required|string|max:255' 
                                : 'nullable|string|max:255',
            'proof_image'    => 'nullable|image|max:2048',
        ]);
    } else {
        // Người ở ghép
        $request->validate([
            'proof_image' => 'nullable|image|max:2048',
        ]);
    }
} elseif ($roomLeaveRequest->action_type === 'leave_all') {
    // 🔹 Trường hợp tất cả rời
    if ($agreement && $agreement->renter_id === $roomLeaveRequest->user_id) {
        // Chủ hợp đồng
        $request->validate([
            'refund_deposit' => 'required|in:0,1',
            'refund_reason'  => $request->input('refund_deposit') == "0" 
                                ? 'required|string|max:255' 
                                : 'nullable|string|max:255',
            'proof_image'    => 'nullable|image|max:2048',
        ]);
    } else {
        // Người ở ghép (trong leave_all thường không có, nhưng vẫn để phòng ngừa)
        $request->validate([
            'proof_image' => 'nullable|image|max:2048',
        ]);
    }
}
        DB::transaction(function () use ($request, $id) {
            $roomLeaveRequest = RoomLeaveRequest::findOrFail($id);

            // --- Nếu có upload chứng từ QR từ landlord ---
           if ($request->hasFile('proof_image')) {
    $path = $request->file('proof_image')->store('deposits', 'public');
    $roomLeaveRequest->proof_image = $path; // Lưu luôn path full trong disk 'public'
}



            $roomLeaveRequest->status = 'approved';
            $roomLeaveRequest->handled_by = Auth::id();
            $roomLeaveRequest->handled_at = now();
            $roomLeaveRequest->save();

            $room = Room::find($roomLeaveRequest->room_id);
            $agreement = RentalAgreement::where('room_id', $roomLeaveRequest->room_id)
                ->where('status', 'active')
                ->first();

            // =======================
            // 1. Chuyển nhượng
            // =======================
            if ($roomLeaveRequest->action_type === 'transfer' && $request->new_renter_id) {
                $roomLeaveRequest->new_renter_id = $request->new_renter_id;
                $roomLeaveRequest->status = 'waiting_new_renter_accept';
                $roomLeaveRequest->save();

                $this->sendNotificationToUser(
                    $request->new_renter_id,
                    '📬 Bạn được chuyển quyền thuê phòng',
                    'Bạn vừa nhận được yêu cầu xác nhận chuyển quyền thuê phòng từ người thuê hiện tại.',
                    route('my-room')
                );

                $this->sendNotificationToUser(
                    $roomLeaveRequest->user_id,
                    '📤 Đã gửi yêu cầu chuyển nhượng',
                    'Yêu cầu chuyển nhượng của bạn đã được landlord duyệt. Đang chờ người mới xác nhận.',
                    route('my-room')
                );
            }

            // =======================
            // 2. Rời phòng cá nhân
            // =======================
            if ($roomLeaveRequest->action_type === 'leave') {
                UserInfo::where('user_id', $roomLeaveRequest->user_id)
                    ->where('room_id', $roomLeaveRequest->room_id)
                    ->update(['active' => 0, 'left_at' => now()]);

                if ($room && $agreement) {
                    $isContractOwner = $agreement->renter_id === $roomLeaveRequest->user_id;
                    $remainingOccupants = UserInfo::where('room_id', $roomLeaveRequest->room_id)
                        ->where('active', 1)
                        ->where('user_id', '!=', $roomLeaveRequest->user_id)
                        ->count();

                    if ($isContractOwner && $remainingOccupants === 0) {
                        $room->status = 'available';
                        $room->save();

                        $agreement->status = 'terminated';
                        $agreement->end_date = now();
                        $agreement->save();
                    }

                    if ($isContractOwner && $agreement->deposit > 0) {
                        $refundChoice = $request->input('refund_deposit'); // 1 = hoàn, 0 = giữ lại
                        $refundReason = $request->input('refund_reason', null);

                        DepositRefund::updateOrCreate(
                            [
                                'rental_id' => $agreement->rental_id,
                                'user_id'   => $roomLeaveRequest->user_id,
                            ],
                            [
                                'amount'      => $agreement->deposit,
                                'refund_date' =>  now(),
                                'status'      => $refundChoice == "1" ? 'refunded' : 'not_refunded',
                                'reason'      => $refundChoice == "0" ? $refundReason : null,
                                'proof_image' => $request->hasFile('proof_image') ? $path : null, 
                            ]
                        );

                        if ($refundChoice == "1") {
                            $this->sendNotificationToUser(
                                $roomLeaveRequest->user_id,
                                '💰 Cọc phòng được hoàn',
                                'Cọc phòng của bạn sẽ được hoàn vào tài khoản trong thời gian sớm nhất.',
                                route('my-room')
                            );
                        } else {
                            $this->sendNotificationToUser(
                                $roomLeaveRequest->user_id,
                                '💰 Cọc phòng không được hoàn',
                                'Cọc phòng của bạn sẽ không được hoàn. Lý do: ' . ($refundReason ?? 'Không có'),
                                route('my-room')
                            );
                        }
                    }
                }

                RoomLeaveLog::create([
                    'user_id'     => $roomLeaveRequest->user_id,
                    'room_id'     => $roomLeaveRequest->room_id,
                    'reason'      => $roomLeaveRequest->reason ?? 'Rời phòng',
                    'action_type' => 'leave',
                    'leave_date'  => now(),
                ]);
            }

            // =======================
            // 3. Rời toàn bộ phòng
            // =======================
         if ($roomLeaveRequest->action_type === 'leave_all') {
    if ($room && $agreement) {
        // 1. Inactive tất cả occupants
        UserInfo::where('room_id', $roomLeaveRequest->room_id)
            ->update(['active' => 0, 'left_at' => now()]);

        // 2. Nếu không còn người active, phòng available và terminate hợp đồng
        $activeUsersCount = UserInfo::where('room_id', $roomLeaveRequest->room_id)
            ->where('active', 1)
            ->count();

        if ($activeUsersCount === 0) {
            $room->status = 'available';
            $room->save();

            $agreement->status = 'terminated';
            $agreement->end_date = now();
            $agreement->save();
        }

        // 3. Refund deposit nếu người ký hợp đồng là contract owner
        $isContractOwner = $agreement->renter_id === $roomLeaveRequest->user_id;
        if ($isContractOwner && $agreement->deposit > 0) {
            $refundChoice = $request->input('refund_deposit'); // 1 = hoàn, 0 = giữ lại
            $refundReason = $request->input('refund_reason', null);

            // ✅ Upload ảnh nếu có
            $path = null;
            if ($request->hasFile('proof_image')) {
                $path = $request->file('proof_image')->store('refunds', 'public');
            }

            DepositRefund::updateOrCreate(
                [
                    'rental_id' => $agreement->rental_id,
                    'user_id'   => $roomLeaveRequest->user_id,
                ],
                [
                    'amount'      => $agreement->deposit,
                    'refund_date' => now(),
                    'status'      => $refundChoice == "1" ? 'refunded' : 'not_refunded',
                    'reason'      => $refundChoice == "0" ? $refundReason : null,
                    'proof_image' => $path, // ✅ Lưu ảnh minh chứng
                ]
            );

            $title = $refundChoice == "1" ? '💰 Cọc phòng được hoàn' : '💰 Cọc phòng không được hoàn';
            $message = $refundChoice == "1"
                ? 'Cọc phòng của bạn sẽ được hoàn vào tài khoản trong thời gian sớm nhất.'
                : 'Cọc phòng của bạn sẽ không được hoàn. Lý do: ' . ($refundReason ?? 'Không có');

            $this->sendNotificationToUser(
                $roomLeaveRequest->user_id,
                $title,
                $message,
                route('my-room')
            );
        }
    }

    // 4. Tạo log leave_all
    RoomLeaveLog::create([
        'user_id'     => $roomLeaveRequest->user_id,
        'room_id'     => $roomLeaveRequest->room_id,
        'reason'      => $roomLeaveRequest->reason ?? 'Tất cả rời phòng',
        'action_type' => 'leave_all',
        'leave_date'  => now(),
    ]);
}

        });

        return redirect()->route('landlord.roomleave.index')
            ->with('success', '✅ Đã duyệt yêu cầu thành công.');
    }


    // Hiện form từ chối



    public function acceptTransfer($id)
    {
        $request = RoomLeaveRequest::with(['user', 'room.rentalAgreement'])->findOrFail($id);

        if ($request->new_renter_id !== Auth::id()) {
            return redirect()->back()->with('error', '❌ Bạn không có quyền xác nhận yêu cầu này.');
        }

        if ($request->status !== 'waiting_new_renter_accept') {
            return redirect()->back()->with('error', '❌ Yêu cầu này không hợp lệ hoặc đã được xử lý.');
        }

        DB::transaction(function () use ($request) {
            $agreement = RentalAgreement::where('room_id', $request->room_id)->first();
            if ($agreement) {
                $agreement->renter_id = $request->new_renter_id;
                $agreement->save();
            }

            UserInfo::updateOrInsert(
                ['user_id' => $request->new_renter_id],
                ['room_id' => $request->room_id, 'active' => 1, 'updated_at' => now()]
            );

            UserInfo::where('user_id', $request->user_id)
                ->where('room_id', $request->room_id)
                ->update(['active' => 0, 'left_at' => now()]);

            RoomLeaveLog::create([
                'user_id' => $request->user_id,
                'room_id' => $request->room_id,
                'reason' => 'Chuyển quyền',
                'leave_date' => now(),
            ]);

            $request->status = 'approved';
            $request->save();
        });

        return redirect()->route('my-room')->with('success', '✅ Bạn đã xác nhận nhận quyền thuê phòng.');
    }


    public function rejectForm($id)
    {
        $request = RoomLeaveRequest::findOrFail($id);
        return view('landlord.roomleave.reject', compact('request'));
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'reject_reason' => 'required|string|max:255',
        ]);

        $leaveRequest = RoomLeaveRequest::findOrFail($id);
        $leaveRequest->status = 'rejected';
        $leaveRequest->reject_reason = $request->reject_reason;
        $leaveRequest->handled_by = Auth::id();
        $leaveRequest->handled_at = now();
        $leaveRequest->save();

        return redirect()->route('landlord.roomleave.index')->with('info', '❌ Đã từ chối yêu cầu và gửi lý do cho người thuê.');
    }

    public function submitTransferForm(Request $request, $id)
    {
        $request->validate([
            'new_renter_id' => 'required|exists:users,id',
        ]);

        $roomLeaveRequest = RoomLeaveRequest::findOrFail($id);

        if ($roomLeaveRequest->action_type !== 'transfer') {
            return redirect()->back()->with('error', '❌ Yêu cầu không phải là loại chuyển nhượng.');
        }

        $roomLeaveRequest->new_renter_id = $request->new_renter_id;
        $roomLeaveRequest->status = 'waiting_new_renter_accept';
        $roomLeaveRequest->handled_by = Auth::id();
        $roomLeaveRequest->handled_at = now();
        $roomLeaveRequest->save();

        return redirect()->route('landlord.roomleave.index')->with('success', '✅ Đã gửi yêu cầu chuyển nhượng cho người được chỉ định.');
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
            'received_at' => Carbon::now(),
        ]);
    }
}
