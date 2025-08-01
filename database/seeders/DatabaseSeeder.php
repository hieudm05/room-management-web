<?php

namespace Database\Seeders;

use App\Models\Landlord\Property;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $this->call([
            UserSeeder::class,
            FacilitySeeder::class,
            RoomPhotoSeeder::class,
            ServiceSeeder::class,
            AttachAllServicesToRoomsSeeder::class,
            BankAccountsTableSeeder::class,
            CommonIssueSeeder::class,
            RoomBillSeeder::class,


        ]);
        // Tạo dữ liệu PropertySeeder
    //      $this->call([
    //     PropertySeeder::class,
    // ]);

    }


}
