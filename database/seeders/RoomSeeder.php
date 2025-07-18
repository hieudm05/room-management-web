<?php

namespace Database\Seeders;

use App\Models\Landlord\Facility;
use App\Models\Landlord\Room;
use App\Models\Landlord\Property;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        // Lấy danh sách tất cả property_id hiện có
        $propertyIds = Property::pluck('property_id')->toArray();

        if (empty($propertyIds)) {
            $this->command->error('Không có property nào để gán cho room.');
            return;
        }

        for ($i = 1; $i <= 10; $i++) {
            $room = Room::create([
                'property_id' => fake()->randomElement($propertyIds),
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
