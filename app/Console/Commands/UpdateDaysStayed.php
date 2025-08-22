<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Landlord\RentalAgreement;
use Carbon\Carbon;

class UpdateDaysStayed extends Command
{
    protected $signature = 'users:update-days-stayed';
    protected $description = 'Cập nhật số ngày ở cho tenant dựa trên hợp đồng thuê đang hoạt động';

    public function handle()
    {
        $agreements = RentalAgreement::where('is_active', 1)->get();
        $count = 0;
        $today = Carbon::today();

        foreach ($agreements as $agreement) {
            foreach ($agreement->userInfos as $userInfo) {
                $startDate = Carbon::parse($agreement->start_date);
                $days = max(0, $startDate->diffInDays($today, false));

                $userInfo->days_stayed = (int) $days;
                $userInfo->save();
                $count++;
            }
        }

        $this->info("Cập nhật ngày ở thành công cho {$count} tenants.");
    }
}
