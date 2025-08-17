<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class RoomBillSeeder extends Seeder
{
 public function run(){
    //     $roomId = 12;
    //     $year = now()->year;

    //     for ($month = 1; $month <= 12; $month++) {
    //         // Tiền phòng ngẫu nhiên từ 900k - 1.2tr
    //         $rentPrice = rand(900000, 1200000);

    //         // Tiền điện ngẫu nhiên
    //         $electricKwh = rand(30, 70);
    //         $electricUnitPrice = 3500;
    //         $electricTotal = $electricKwh * $electricUnitPrice;
    //         $electricStart = 100;
    //         $electricEnd = $electricStart + $electricKwh;

    //         // Tiền nước ngẫu nhiên
    //         $waterM3 = rand(3, 8);
    //         $waterPrice = 100000; // giá gốc (dùng để tính trong DB)
    //         $waterTotal = $waterM3 * 10000;

    //         // Tính tổng
    //         $total = $rentPrice + $electricTotal + $waterTotal;

    //         DB::table('room_bills')->insert([
    //             'room_id' => $roomId,
    //             'month' => sprintf('%d-%02d-01', $year, $month),
    //             'tenant_name' => 'Fake Tenant',
    //             'area' => 20,
    //             'rent_price' => $rentPrice,
    //             'electric_start' => $electricStart,
    //             'electric_end' => $electricEnd,
    //             'electric_kwh' => $electricKwh,
    //             'electric_unit_price' => $electricUnitPrice,
    //             'electric_total' => $electricTotal,
    //             'water_price' => $waterPrice,
    //             'water_unit' => 'm3',
    //             'water_occupants' => 2,
    //             'water_m3' => $waterM3,
    //             'water_total' => $waterTotal,
    //             'total' => $total,
    //             'status' => 'paid',
    //             'created_at' => Carbon::create($year, $month, 5),
    //             'updated_at' => now(),
    //         ]);
    //     }}
}
}
