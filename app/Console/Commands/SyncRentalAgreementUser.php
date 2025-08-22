<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Landlord\RentalAgreement;
use App\Models\User;
use App\Models\UserInfo;

class SyncRentalAgreementUser extends Command
{
    /**
     * Tên lệnh artisan
     */
    protected $signature = 'sync:rental-users';

    /**
     * Mô tả
     */
    protected $description = 'Đồng bộ thông tin renter từ bảng users hoặc user_infos sang rental_agreements';

    /**
     * Thực thi lệnh
     */
    public function handle()
    {
        $agreements = RentalAgreement::where(function ($q) {
            $q->whereNull('full_name')->orWhere('full_name', '')
              ->orWhereNull('email')->orWhere('email', '');
        })->get();

        $this->info("🔎 Tìm thấy {$agreements->count()} hợp đồng thiếu dữ liệu...");

        foreach ($agreements as $agreement) {
            $user = User::find($agreement->renter_id);
            $userInfo = UserInfo::where('user_id', $agreement->renter_id)->first();

            if ($userInfo) {
                $agreement->full_name = $userInfo->full_name ?: ($user->name ?? null);
                $agreement->email     = $userInfo->email ?: ($user->email ?? null);
                $agreement->phone     = $userInfo->phone;
                $agreement->cccd      = $userInfo->cccd;
            } elseif ($user) {
                $agreement->full_name = $user->name;
                $agreement->email     = $user->email;
            }

            if ($agreement->isDirty(['full_name','email','phone','cccd'])) {
                $agreement->save();
                $this->info("✅ Đồng bộ rental_id {$agreement->rental_id}");
            }
        }

        $this->info("🎉 Hoàn thành đồng bộ!");
    }
}
