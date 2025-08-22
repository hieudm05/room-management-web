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
        $propertyIds = Property::pluck('property_id')->toArray();

        if (empty($propertyIds)) {
            $this->command->error('Không có property nào để gán cho room.');
            return;
        }

        for ($i = 1; $i <= 10; $i++) {
            $occupants = rand(0, 5); // số người hiện tại
            $peopleRenter = rand(1, 5); // sức chứa tối đa

            $room = Room::create([
                'property_id' => fake()->randomElement($propertyIds),
                'room_number' => 'P' . $i,
                'area' => rand(20, 50),
                'rental_price' => rand(1500000, 4000000),
                'status' => ['Available', 'Rented', 'Hidden', 'Suspended', 'Confirmed'][rand(0, 4)],
                'occupants' => $occupants,
                'people_renter' => $peopleRenter,
            ]);

            // Gán ngẫu nhiên 2-3 tiện nghi
            $facilityIds = Facility::inRandomOrder()->take(rand(2, 3))->pluck('facility_id')->toArray();
            $room->facilities()->sync($facilityIds);

            // Gọi hàm auto-hide/unhide posts theo occupants
            \App\Models\Landlord\Room::hidePostsIfFull($room->room_id);
        }
    }
}
