<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RentalAgreement;
use App\Models\UserInfo;

class SyncRentalAgreementUserData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:rental-agreement-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Đồng bộ thông tin người thuê từ bảng user_infos sang rental_agreements';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("🔄 Bắt đầu đồng bộ dữ liệu...");

        $agreements = RentalAgreement::all();
        $count = 0;

        foreach ($agreements as $agreement) {
            // Nếu hợp đồng chưa có thông tin Bên B
            if (empty($agreement->full_name) || empty($agreement->email)) {
                $userInfo = UserInfo::where('user_id', $agreement->renter_id)->first();

                if ($userInfo) {
                    $agreement->update([
                        'full_name' => $userInfo->full_name,
                        'email'     => $userInfo->email,
                        'phone'     => $userInfo->phone,
                        'cccd'      => $userInfo->cccd,
                    ]);
                    $count++;
                    $this->info("✅ Đã cập nhật hợp đồng ID {$agreement->rental_id}");
                }
            }
        }

        $this->info("🎉 Hoàn tất đồng bộ: {$count} hợp đồng đã được cập nhật.");
    }
}
