<?php

namespace App\Http\Controllers\Landlord\Staff;

use App\Http\Controllers\Controller;
use App\Models\Landlord\Room;
use App\Models\UserInfo;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    //
    public function index(Room $room)
    {
        $roomDatas = Room::where('room_id', $room->room_id)->first();
        $roomInfoS = UserInfo::where('room_id', $room->room_id)
            ->join('users', 'user_infos.user_id', '=', 'users.id')
            ->select(
                'user_infos.*',
                'users.name as full_name',
                'users.email'
            )
            ->get();

        return view('landlord.Staff.rooms.Documents.index', compact('roomDatas',"roomInfoS"));
    }
}
