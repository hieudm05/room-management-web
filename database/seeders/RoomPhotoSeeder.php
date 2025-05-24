<?php

namespace Database\Seeders;

use App\Models\Landlord\Room;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoomPhotoSeeder extends Seeder
{
    public function run(): void
    {
        $rooms = Room::all();

        foreach ($rooms as $room) {
            // Gán từ 1–3 ảnh mỗi phòng
            $photoCount = rand(1, 3);

            for ($i = 0; $i < $photoCount; $i++) {
                DB::table('room_photos')->insert([
                    'room_id' => $room->room_id,
                    'image_url' => 'https://via.placeholder.com/150?text=Room+' . $room->room_number . '+Image' . ($i + 1),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
