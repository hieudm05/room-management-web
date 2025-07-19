<?php

namespace App\Http\Controllers\Renter;

use App\Http\Controllers\Controller;
use App\Models\UserInfo;
use App\Models\Landlord\Approval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddUserRequestController extends Controller
{
    public function create()
    {
        return view('renter.storeuser');
    }

    public function store(Request $request)
    {
        $request->validate([
            'full_name'  => 'required|array',
            'cccd'       => 'required|array',
            'phone'      => 'required|array',
            'email'      => 'required|array',

            'full_name.*'  => 'required|string|max:100',
            'cccd.*'       => 'required|string|max:20|distinct|unique:user_infos,cccd',
            'phone.*'      => 'required|string|max:20',
            'email.*'      => 'required|email|distinct|unique:user_infos,email',
        ]);

        $renter = Auth::user();

        $renterInfo = UserInfo::where('user_id', $renter->id)
            ->with('room.property')
            ->first();

        if (!$renterInfo || !$renterInfo->room) {
            return back()->with('error', 'Không tìm thấy phòng trọ của bạn.');
        }

        $room = $renterInfo->room;
        $roomId = $room->room_id ?? $room->id;
        $landlordId = $room->property->landlord_id ?? null;

        if (!$landlordId) {
            return back()->with('error', 'Không xác định được chủ trọ.');
        }

        // 🔍 Số người hiện tại đã ở trong phòng (đã có tài khoản user)
        $currentUsers = UserInfo::where('room_id', $roomId)
            ->whereNotNull('user_id')
            ->count();

        // 🔍 Số người đang chờ duyệt (chưa có user_id)
        $pendingUsers = UserInfo::where('room_id', $roomId)
            ->whereNull('user_id')
            ->count();

        // 🔍 Số người đang gửi trong form
        $newRequestCount = count($request->full_name);

        // 🔍 Tổng số người nếu thêm vào
        $totalAfter = $currentUsers + $pendingUsers + $newRequestCount;

        if ($totalAfter > $room->occupants) {
            $remaining = max(0, $room->occupants - $currentUsers - $pendingUsers);
            return back()->withErrors("❌ Phòng chỉ còn có thể thêm tối đa {$remaining} người.");
        }

        // Lặp qua từng người được gửi từ form
        foreach ($request->full_name as $index => $name) {
            $cccd = $request->cccd[$index];
            $phone = $request->phone[$index];
            $email = $request->email[$index];

            // Lưu vào user_infos
            UserInfo::create([
                'room_id'   => $roomId,
                'cccd'      => $cccd,
                'phone'     => $phone,
                'email'     => $email,
                'user_id'   => null,
                'full_name' => $name,
            ]);

            // Lưu vào approvals
            Approval::create([
                'room_id'     => $roomId,
                'landlord_id' => $landlordId,
                'type'        => 'add_user',
                'note'        => "Tên: {$name} | Email: {$email}",
                'status'      => 'pending',
                'file_path'   => null,
            ]);
        }

        return back()->with('success', '✅ Yêu cầu thêm người đã được gửi.');
    }
}
