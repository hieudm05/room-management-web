<?php

namespace App\Observers;

use App\Models\Landlord\Room;
use App\Models\StaffPost;
use Illuminate\Support\Facades\Log;

class RoomObserver
{
    public function updated(Room $room)
    {
        Room::hidePostsIfFull($room->room_id);
    }
}
