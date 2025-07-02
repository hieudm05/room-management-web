<?php


namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BankAccountsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('bank_accounts')->insert([
            [
                'user_id' => 1,
                'bank_name' => 'Viettinbank',
                'bank_account_name' => 'Nguyen Trong Minh',
                'bank_account_number' => '105880362101',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_id' => 2,
                'bank_name' => 'ACB',
                'bank_account_name' => 'Tran Thi B',
                'bank_account_number' => '987654321',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_id' => 3,
                'bank_name' => 'Techcombank',
                'bank_account_name' => 'Le Van C',
                'bank_account_number' => '111122223333',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_id' => 4,
                'bank_name' => 'BIDV',
                'bank_account_name' => 'Pham Thi D',
                'bank_account_number' => '444455556666',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_id' => 5,
                'bank_name' => 'VPBank',
                'bank_account_name' => 'Nguyen Van E',
                'bank_account_number' => '777788889999',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_id' => 6,
                'bank_name' => 'MB Bank',
                'bank_account_name' => 'Tran Van F',
                'bank_account_number' => '000011112222',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
