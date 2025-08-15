<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;

use App\Models\Landlord\RoomLeaveRequest;
use App\Models\RoomLeaveLog;
use App\Models\Landlord\RentalAgreement;
use App\Models\RoomLeaveLog as ModelsRoomLeaveLog;

use App\Models\Notification;
use Carbon\Carbon;
// use App\Models\Landlord\RoomLeaveRequest;
// use App\Models\RoomLeaveLog;
// use App\Models\Landlord\RentalAgreement;
use App\Models\Landlord\Room;

use App\Models\UserInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LandlordRoomLeaveController extends Controller
{

    // Danh sách yêu cầu đang chờ duyệt
    // public function index()
    // {
    //     $requests = RoomLeaveRequest::with(['user', 'room'])
    //         ->where('status', 'staff_approved')

    public function index()
    {
        $requests = RoomLeaveRequest::with(['user', 'room'])
            ->where('status', 'pending')

            ->where('landlord_id', Auth::id())
            ->get();

        return view('landlord.roomleave.index', compact('requests'));
    }


    // Danh sách yêu cầu đã xử lý


    public function processed()
    {
        $requests = RoomLeaveRequest::with(['user', 'room', 'newRenter.info'])
            ->where('landlord_id', Auth::id())
            ->whereIn('status', ['approved', 'rejected'])
            ->orderByDesc('handled_at')
            ->get();

        return view('landlord.roomleave.processed', compact('requests'));
    }


    // Xem chi tiết một yêu cầu


    public function show($id)
    {
        $request = RoomLeaveRequest::with(['user', 'room'])->findOrFail($id);
        return view('landlord.roomleave.show', compact('request'));
    }


    // Duyệt yêu cầu
  public function approve(Request $request, $id)
{
    DB::transaction(function () use ($request, $id) {
        $roomLeaveRequest = RoomLeaveRequest::findOrFail($id);

        if ($roomLeaveRequest->action_type === 'transfer' && $request->new_renter_id) {
            $roomLeaveRequest->new_renter_id = $request->new_renter_id;
            $roomLeaveRequest->status = 'waiting_new_renter_accept';
        }

        if ($roomLeaveRequest->action_type === 'leave') {
            $roomLeaveRequest->status = 'approved';

            // Xóa hoặc vô hiệu người khỏi phòng
            UserInfo::where('user_id', $roomLeaveRequest->user_id)
                ->where('room_id', $roomLeaveRequest->room_id)
                ->delete();

            RoomLeaveLog::create([
                'user_id' => $roomLeaveRequest->user_id,
                'room_id' => $roomLeaveRequest->room_id,
                'reason' => 'Rời phòng',
                'action_type' => 'leave',
                'leave_date' => now(),
            ]);
        }

        $roomLeaveRequest->handled_by = Auth::id();
        $roomLeaveRequest->handled_at = now();
        $roomLeaveRequest->save();
    });

    return redirect()->route('landlord.roomleave.index')
        ->with('success', '✅ Đã duyệt yêu cầu thành công.');
}

   public function acceptTransfer($id)
{
    $request = RoomLeaveRequest::findOrFail($id);

    // Bảo vệ: chỉ đúng người mới được xác nhận
    if ($request->new_renter_id !== Auth::id()) {
        return redirect()->back()->with('error', '❌ Bạn không có quyền xác nhận yêu cầu này.');
    }

    // Bảo vệ: chỉ xử lý trạng thái phù hợp
    if ($request->status !== 'waiting_new_renter_accept') {
        return redirect()->back()->with('error', '❌ Yêu cầu này không hợp lệ hoặc đã được xử lý.');
    }

    DB::transaction(function () use ($request) {
        // Cập nhật người thuê chính trong hợp đồng
        $agreement = RentalAgreement::where('room_id', $request->room_id)->first();
        if ($agreement) {
            $agreement->renter_id = $request->new_renter_id;
            $agreement->save();
        }

        // Gán phòng cho người được chuyển quyền
        UserInfo::updateOrInsert(
            ['user_id' => $request->new_renter_id],
            ['room_id' => $request->room_id, 'active' => 1, 'updated_at' => now()]
        );

        // Vô hiệu người cũ
        UserInfo::where('user_id', $request->user_id)
            ->where('room_id', $request->room_id)
            ->update(['active' => 0, 'left_at' => now()]);

        // Ghi log (tuỳ chọn)
        RoomLeaveLog::create([
            'user_id' => $request->user_id,
            'room_id' => $request->room_id,
            'reason' => 'Chuyển quyền',
            'leave_date' => now(),
        ]);

        // Cập nhật trạng thái
        $request->status = 'approved';
        $request->save();
    });

    return redirect()->route('my-room')->with('success', '✅ Bạn đã xác nhận nhận quyền thuê phòng.');
}

    // Hiện form từ chối

    // public function approve(Request $request, $id)
    // {
    //     DB::transaction(function () use ($request, $id) {
    //         $roomLeaveRequest = RoomLeaveRequest::findOrFail($id);

    //         if ($roomLeaveRequest->action_type === 'transfer' && $request->new_renter_id) {
    //             $roomLeaveRequest->new_renter_id = $request->new_renter_id;
    //             $roomLeaveRequest->status = 'waiting_new_renter_accept';

    //             $this->sendNotificationToUser(
    //                 $request->new_renter_id,
    //                 '📬 Bạn được chuyển quyền thuê phòng',
    //                 'Bạn vừa nhận được yêu cầu xác nhận chuyển quyền thuê phòng từ người thuê hiện tại.',
    //                 route('my-room')
    //             );

    //             $this->sendNotificationToUser(
    //                 $roomLeaveRequest->user_id,
    //                 '📤 Đã gửi yêu cầu chuyển nhượng',
    //                 'Yêu cầu chuyển nhượng của bạn đã được landlord duyệt. Đang chờ người mới xác nhận.',
    //                 route('my-room')
    //             );
    //         }

    //         if ($roomLeaveRequest->action_type === 'leave') {
    //             $roomLeaveRequest->status = 'approved';

    //             UserInfo::where('user_id', $roomLeaveRequest->user_id)
    //                 ->where('room_id', $roomLeaveRequest->room_id)
    //                 ->update(['active' => 0, 'left_at' => now()]);

    //             $room = Room::find($roomLeaveRequest->room_id);
    //             $agreement = RentalAgreement::where('room_id', $roomLeaveRequest->room_id)
    //                 ->where('status', 'active')
    //                 ->first();

    //             if ($room && $agreement) {
    //                 $isContractOwner = $agreement->renter_id === $roomLeaveRequest->user_id;

    //                 $remainingOccupants = UserInfo::where('room_id', $roomLeaveRequest->room_id)
    //                     ->where('active', 1)
    //                     ->where('user_id', '!=', $roomLeaveRequest->user_id)
    //                     ->count();

    //                 if ($isContractOwner && $remainingOccupants === 0) {
    //                     $room->status = 'available';
    //                     $room->save();

    //                     $agreement->status = 'terminated';
    //                     $agreement->end_date = now();
    //                     $agreement->save();
    //                 }
    //             }

    //             RoomLeaveLog::create([
    //                 'user_id' => $roomLeaveRequest->user_id,
    //                 'room_id' => $roomLeaveRequest->room_id,
    //                 'reason' => 'Rời phòng',
    //                 'action_type' => 'leave',
    //                 'leave_date' => now(),
    //             ]);
    //         }

    //         $roomLeaveRequest->handled_by = Auth::id();
    //         $roomLeaveRequest->handled_at = now();
    //         $roomLeaveRequest->save();
    //     });

    //     return redirect()->route('landlord.roomleave.index')
    //         ->with('success', '✅ Đã duyệt yêu cầu thành công.');
    // }

    // public function acceptTransfer($id)
    // {
    //     $request = RoomLeaveRequest::findOrFail($id);

    //     if ($request->new_renter_id !== Auth::id()) {
    //         return redirect()->back()->with('error', '❌ Bạn không có quyền xác nhận yêu cầu này.');
    //     }

    //     if ($request->status !== 'waiting_new_renter_accept') {
    //         return redirect()->back()->with('error', '❌ Yêu cầu này không hợp lệ hoặc đã được xử lý.');
    //     }

    //     DB::transaction(function () use ($request) {
    //         $agreement = RentalAgreement::where('room_id', $request->room_id)->first();
    //         if ($agreement) {
    //             $agreement->renter_id = $request->new_renter_id;
    //             $agreement->save();
    //         }

    //         UserInfo::updateOrInsert(
    //             ['user_id' => $request->new_renter_id],
    //             ['room_id' => $request->room_id, 'active' => 1, 'updated_at' => now()]
    //         );

    //         UserInfo::where('user_id', $request->user_id)
    //             ->where('room_id', $request->room_id)
    //             ->update(['active' => 0, 'left_at' => now()]);

    //         RoomLeaveLog::create([
    //             'user_id' => $request->user_id,
    //             'room_id' => $request->room_id,
    //             'reason' => 'Chuyển quyền',
    //             'leave_date' => now(),
    //         ]);

    //         $request->status = 'approved';
    //         $request->save();
    //     });

    //     return redirect()->route('my-room')->with('success', '✅ Bạn đã xác nhận nhận quyền thuê phòng.');
    // }


    public function rejectForm($id)
    {
        $request = RoomLeaveRequest::findOrFail($id);
        return view('landlord.roomleave.reject', compact('request'));
    }


    // Từ chối yêu cầu


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

    // Kiểm tra xem có đúng loại chuyển nhượng không
    if ($roomLeaveRequest->action_type !== 'transfer') {
        return redirect()->back()->with('error', '❌ Yêu cầu không phải là loại chuyển nhượng.');
    }

    // Cập nhật người được chuyển nhượng & chuyển trạng thái về cho nhân viên
    $roomLeaveRequest->new_renter_id = $request->new_renter_id;
    $roomLeaveRequest->status = 'waiting_new_renter_accept'; // trạng thái mới
    $roomLeaveRequest->handled_by = Auth::id();
    $roomLeaveRequest->handled_at = now();
    $roomLeaveRequest->save();

    return redirect()->route('landlord.roomleave.index')->with('success', '✅ Đã gửi yêu cầu chuyển nhượng cho người được chỉ định.');
}
}




    // public function submitTransferForm(Request $request, $id)
    // {
    //     $request->validate([
    //         'new_renter_id' => 'required|exists:users,id',
    //     ]);

    //     $roomLeaveRequest = RoomLeaveRequest::findOrFail($id);

    //     if ($roomLeaveRequest->action_type !== 'transfer') {
    //         return redirect()->back()->with('error', '❌ Yêu cầu không phải là loại chuyển nhượng.');
    //     }

    //     $roomLeaveRequest->new_renter_id = $request->new_renter_id;
    //     $roomLeaveRequest->status = 'waiting_new_renter_accept';
    //     $roomLeaveRequest->handled_by = Auth::id();
    //     $roomLeaveRequest->handled_at = now();
    //     $roomLeaveRequest->save();

    //     return redirect()->route('landlord.roomleave.index')->with('success', '✅ Đã gửi yêu cầu chuyển nhượng cho người được chỉ định.');
    // }

    // private function sendNotificationToUser($userId, $title, $message, $link = null)
    // {
    //     $notification = Notification::create([
    //         'title' => $title,
    //         'message' => $message,
    //         'type' => 'user',
    //         'link' => $link,
    //         'created_at' => now(),
    //         'expired_at' => now()->addDays(7),
    //         'is_global' => false,
    //     ]);

    //     $notification->users()->attach($userId, [
    //         'is_read' => false,
    //         'received_at' => Carbon::now(),
    //     ]);
    // }


