<?php

namespace Database\Seeders;
use App\Models\CommonIssue;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CommonIssueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
   public function run(): void
    {
        $issues = ['Vấn đề an ninh ', 'Vấn đề điện nước ', 'Vấn đề về cơ sở vật chất ', 'Vấn đề về người cùng trọ' , 'Vấn đề về hợp đồng ',  'Vấn đề về chủ nhà trọ', 'Vấn đề về giá cả', 'Vấn đề về vệ sinh môi trường', 'Vấn đề về sửa chữa đồ đạc', 'Vấn đề về dịch vụ hỗ trợ'];

        foreach ($issues as $issue) {
            CommonIssue::create(['name' => $issue]);
        }
    }
}
