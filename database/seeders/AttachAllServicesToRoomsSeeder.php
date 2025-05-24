<?php

namespace Database\Seeders;

use App\Models\Landlord\Room;
use App\Models\Landlord\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AttachAllServicesToRoomsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rooms = Room::all();
        $services = Service::all();

        foreach ($rooms as $room) {
            $attach = [];
            foreach ($services as $service) {
                $attach[$service->service_id] = [
                    'is_free' => true,
                    'price' => null,
                ];
            }

            $room->services()->syncWithoutDetaching($attach);
        }
    }
}
