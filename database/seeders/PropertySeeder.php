<?php

namespace Database\Seeders;

use App\Models\Landlord\Property;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class PropertySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
         $faker = Faker::create('vi_VN');

        // Lấy danh sách user có role là 'Landlord'
        $landlords = User::where('role', 'Landlord')->pluck('id')->toArray();

        // Nếu không có landlord thì dừng lại
        if (empty($landlords)) {
            $this->command->info('Không có user nào có role = Landlord.');
            return;
        }

        foreach (range(1, 10) as $i) {
            Property::create([
                'landlord_id' => $faker->randomElement($landlords),
                'name' => 'Khu trọ ' . Str::random(5),
                'address' => $faker->address,
                'latitude' => $faker->latitude(10.5, 11.0),
                'longitude' => $faker->longitude(106.5, 107.0),
                'description' => $faker->paragraph,
                'status' => $faker->randomElement(['Pending', 'Approved', 'Rejected', 'Suspended']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
