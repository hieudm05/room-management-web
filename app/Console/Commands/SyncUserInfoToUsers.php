<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\UserInfo;

class SyncUserInfoToUsers extends Command
{
    protected $signature = 'sync:user-info';
    protected $description = 'Đồng bộ thông tin từ bảng user_infos sang bảng users';

    public function handle()
    {
        $this->info("Bắt đầu đồng bộ thông tin người dùng...");

        $users = User::with('info')->get();
        $count = 0;

        foreach ($users as $user) {
            if ($user->info) {
                $updated = false;

                if (!$user->name && $user->info->full_name) {
                    $user->name = $user->info->full_name;
                    $updated = true;
                }

                if (!$user->phone_number && $user->info->phone) {
                    $user->phone_number = $user->info->phone;
                    $updated = true;
                }

                if (!$user->identity_number && $user->info->cccd) {
                    $user->identity_number = $user->info->cccd;
                    $updated = true;
                }

                if ($updated) {
                    $user->save();
                    $count++;
                }
            }
        }

        $this->info("Đã đồng bộ thành công {$count} người dùng.");
        return 0;
    }
}
