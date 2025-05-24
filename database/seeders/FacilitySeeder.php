<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FacilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
{
    $data = ['Máy lạnh', 'Tủ lạnh', 'Giường', 'Tivi', 'Wifi'];
    foreach ($data as $item) {
        DB::table('facilities')->insert([
            'name' => $item,
            'icon' => strtolower(str_replace(' ', '_', $item)) . '.png',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
}
