<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FeatureSeeder extends Seeder
{
    public function run(): void
    {
        $features = [
            'Đầy đủ nội thất',
            'Có gác',
            'Có kệ bếp',
            'Có máy lạnh',
            'Có máy giặt',
            'Có tủ lạnh',
            'Có thang máy',
            'Không chung chủ',
            'Giờ giấc tự do',
            'Có bảo vệ 24/24',
            'Có hầm để xe',
        ];

        foreach ($features as $feature) {
            DB::table('features')->insert(['name' => $feature]);
        }
    }
}
