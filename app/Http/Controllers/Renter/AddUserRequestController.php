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
            'full_name'  => 'required|string|max:100',
            'cccd'  => 'required|string|max:20|unique:user_infos,cccd',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|unique:user_infos,email',
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

        // ✅ Lưu vào user_infos có full_name
        UserInfo::create([
            'room_id'   => $roomId,
            'cccd'      => $request->cccd,
            'phone'     => $request->phone,
            'email'     => $request->email,
            'user_id'   => null,
            'full_name' => $request->full_name,
        ]);

        // ✅ Lưu yêu cầu kèm theo tên & email
        Approval::create([
            'room_id'     => $roomId,
            'landlord_id' => $landlordId,
            'type'        => 'add_user',
            'note'        => "Tên: {$request->full_name} | Email: {$request->email}",
            'status'      => 'pending',
            'file_path'   => null,
        ]);

        return back()->with('success', '✅ Yêu cầu thêm người đã được gửi.');
    }
}
