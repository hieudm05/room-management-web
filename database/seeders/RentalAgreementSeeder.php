<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RentalAgreementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('rental_agreements')->insert([
            [
                'room_id' => 1,
                'renter_id' => 10,
                'landlord_id' => 1,
                'start_date' => '2024-05-01',
                'end_date' => '2025-05-01',
                'rental_price' => 3500000,
                'deposit' => 3500000,
                'status' => 'Active',
                'contract_file' => '/storage/contracts/hd01.pdf',
                'agreement_terms' => 'Người thuê phải thanh toán trước ngày 5 hàng tháng.',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'room_id' => 2,
                'renter_id' => 11,
                'landlord_id' => 1,
                'start_date' => '2023-12-01',
                'end_date' => '2024-12-01',
                'rental_price' => 2800000,
                'deposit' => 2800000,
                'status' => 'Signed',
                'contract_file' => '/storage/contracts/hd02.pdf',
                'agreement_terms' => 'Cấm nuôi thú cưng trong phòng.',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'room_id' => 3,
                'renter_id' => 12,
                'landlord_id' => 1,
                'start_date' => '2024-01-15',
                'end_date' => '2024-10-15',
                'rental_price' => 3000000,
                'deposit' => 2000000,
                'status' => 'Terminated',
                'contract_file' => null,
                'agreement_terms' => 'Phí gửi xe 100.000đ/tháng.',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'room_id' => 1,
                'renter_id' => 13,
                'landlord_id' => 1,
                'start_date' => '2023-01-01',
                'end_date' => '2024-01-01',
                'rental_price' => 3400000,
                'deposit' => 3400000,
                'status' => 'Expired',
                'contract_file' => '/storage/contracts/hd_old.pdf',
                'agreement_terms' => 'Người thuê chịu trách nhiệm vệ sinh khu vực chung.',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'room_id' => 4,
                'renter_id' => 14,
                'landlord_id' => 1,
                'start_date' => '2025-01-01',
                'end_date' => '2026-01-01',
                'rental_price' => 4000000,
                'deposit' => 4000000,
                'status' => 'Pending',
                'contract_file' => null,
                'agreement_terms' => 'Không được hút thuốc trong phòng.',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
