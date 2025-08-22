<?php

// database/seeders/RoomOccupantsSeeder.php
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Landlord\Room;

class RoomOccupantsSeeder extends Seeder
{
    public function run()
    {
        DB::table('rooms')->where('room_id', 1)->update(['occupants' => 2]);
        Room::hidePostsIfFull(1);
    }
}
