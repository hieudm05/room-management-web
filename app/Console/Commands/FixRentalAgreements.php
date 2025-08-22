<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RentalAgreement;
use App\Models\User;

class FixRentalAgreements extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:rental-agreements';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sửa dữ liệu hợp đồng cũ: thay Landlord bằng thông tin Renter';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $agreements = RentalAgreement::where(function ($q) {
            $q->whereNull('full_name')
              ->orWhere('full_name', 'Landlord');
        })->get();

        if ($agreements->isEmpty()) {
            $this->info("✅ Không có hợp đồng nào cần sửa.");
            return 0;
        }

        foreach ($agreements as $agreement) {
            $user = User::find($agreement->renter_id);

            if ($user) {
                $agreement->full_name = $user->name;
                $agreement->email     = $user->email;
                $agreement->phone     = $user->phone_number ?? null;
                $agreement->cccd      = $user->identity_number ?? null;
                $agreement->save();

                $this->info("Đã sửa hợp đồng ID {$agreement->rental_id} → {$user->name}");
            } else {
                $this->warn("⚠️ Không tìm thấy renter cho hợp đồng ID {$agreement->rental_id}");
            }
        }

        $this->info("🎉 Hoàn tất cập nhật dữ liệu hợp đồng.");
        return 0;
    }
}
