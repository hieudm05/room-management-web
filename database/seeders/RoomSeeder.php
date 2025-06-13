<?php

namespace Database\Seeders;

use App\Models\Landlord\Facility;
use App\Models\Landlord\Room;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        // Giả định property_id = 1 có tồn tại
        $propertyId = 1;

        for ($i = 1; $i <= 10; $i++) {
            $room = Room::create([
                'property_id' => $propertyId,
                'room_number' => 'P' . $i,
                'area' => rand(20, 50),
                'rental_price' => rand(1500000, 4000000),
                'status' => ['Available', 'Rented', 'Hidden', 'Suspended', 'Confirmed'][rand(0, 4)],
            ]);

            // Gán ngẫu nhiên 2-3 tiện nghi
            $facilityIds = Facility::inRandomOrder()->take(rand(2, 3))->pluck('facility_id')->toArray();
            $room->facilities()->sync($facilityIds);
        }
    }
}
