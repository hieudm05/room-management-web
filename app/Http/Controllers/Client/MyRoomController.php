<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Landlord\Room as LandlordRoom;
use App\Models\Landlord\Staff\Rooms\RoomBill;
use Illuminate\Support\Facades\Auth;

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

        return view('home.my-room', compact('room', 'bills'));

    }
}

