<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Landlord\Room;
use App\Models\StaffPost;
use Illuminate\Support\Facades\Log;

class SyncRoomPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-room-posts {room_id? : ID của phòng, bỏ trống để sync tất cả}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Đồng bộ trạng thái bài đăng theo số người hiện có trong phòng';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $roomId = $this->argument('room_id');

        $rooms = $roomId 
            ? Room::where('room_id', $roomId)->get() 
            : Room::all();

        foreach ($rooms as $room) {
            Log::info("SyncRoomPosts: Room {$room->room_id} | occupants={$room->occupants} | max={$room->people_renter}");

            if ($room->people_renter !== null && $room->occupants >= $room->people_renter) {
                $count = StaffPost::where('room_id', $room->room_id)
                    ->update([
                        'is_public' => 0,
                        'auto_hidden_reason' => 'Phòng đã đủ người'
                    ]);
                Log::info("Room {$room->room_id} FULL → Hidden {$count} StaffPost(s)");
            } else {
                $count = StaffPost::where('room_id', $room->room_id)
                    ->where('status', 1)
                    ->update([
                        'is_public' => 1,
                        'auto_hidden_reason' => null
                    ]);
                Log::info("Room {$room->room_id} NOT FULL → Unhidden {$count} StaffPost(s)");
            }
        }

        $this->info('SyncRoomPosts completed.');
    }
}
