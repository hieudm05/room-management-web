<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            ['name' => 'Điện', 'description' => 'Tính tiền theo số kWh'],
            ['name' => 'Nước', 'description' => 'Tính theo đầu người hoặc đồng hồ'],
            ['name' => 'Internet', 'description' => 'Wi-Fi tốc độ cao'],
            ['name' => 'Gửi xe máy', 'description' => 'Chi phí gửi xe hàng tháng'],
            ['name' => 'Rác thải', 'description' => 'Phí thu gom rác định kỳ'],
            // ['name' => 'Truyền hình cáp', 'description' => 'Truyền hình cáp hoặc K+ nếu có'],
            ['name' => 'Dọn vệ sinh', 'description' => 'Dịch vụ dọn phòng theo tuần/tháng'],
        ];

        foreach ($services as $service) {
            DB::table('services')->insert([
                'name' => $service['name'],
                'description' => $service['description'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
