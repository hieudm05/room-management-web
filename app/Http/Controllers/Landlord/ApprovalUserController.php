<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Landlord\Approval;
use App\Models\Landlord\RentalAgreement;
use App\Models\Landlord\Room;
use App\Models\Landlord\RoomUsers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Smalot\PdfParser\Parser;
use App\Models\UserInfo;
use Illuminate\Support\Facades\Log;

class ApprovalUserController extends Controller
{
    // Danh sách hợp đồng chờ duyệt (landlord dashboard)
    public function index()
    {
        $landlordId = Auth::id();
        $pendingApprovals = Approval::where('landlord_id', $landlordId)
            ->where('status', 'pending')
            ->where('type', 'add_user')
            ->with('room')
            ->latest()
            ->get();

        return view('landlord.approvals.adduser', compact('pendingApprovals'));
    }



    // Từ chối hợp đồng (xóa bản ghi)
    public function reject($id)
    {
        $approval = Approval::findOrFail($id);

        if ($approval->room->property->landlord_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền từ chối yêu cầu này.');
        }

        // 👉 Nếu là yêu cầu thêm người thì xóa luôn user_info tương ứng
        if ($approval->type === 'add_user') {
            preg_match('/Email:\s*(.*)/', $approval->note, $matches);
            $email = trim($matches[1] ?? '');

            if ($email) {
                $userInfo = UserInfo::where('room_id', $approval->room_id)
                    ->where('email', $email)
                    ->whereNull('user_id') // Chỉ xóa nếu chưa được duyệt
                    ->latest()
                    ->first();

                if ($userInfo) {
                    $userInfo->delete();
                }
            }
        }

        // Xóa yêu cầu duyệt
        $approval->delete();

        return redirect()->back()->with('warning', '❌ Yêu cầu đã bị từ chối và thông tin người đó đã bị xóa.');
    }


    public function approveUser($id)
    {
        Log::info('approveUser called with ID: ' . $id);
        $approval = Approval::findOrFail($id);
        Log::info('Approval found: ' . json_encode($approval->toArray()));

        if ($approval->type !== 'add_user') {
            Log::warning('Invalid approval type: ' . $approval->type);
            return back()->withErrors('❌ Loại yêu cầu không hợp lệ.');
        }

        // Tách họ tên và email từ note
        preg_match('/Tên:\s*(.*?)\s*\|\s*Email:\s*([^|]+)/', $approval->note, $matches);
        $fullNameFromNote = trim($matches[1] ?? '');
        $email = trim($matches[2] ?? '');
        Log::info("Parsed note: FullName={$fullNameFromNote}, Email={$email}");

        if (empty($fullNameFromNote) || empty($email)) {
            Log::error('Failed to parse note: ' . $approval->note);
            return back()->withErrors('❌ Không thể tách thông tin người dùng từ yêu cầu.');
        }

        // Truy vấn user_info
        $userInfo = UserInfo::where('email', $email)
            ->where(function ($query) use ($approval) {
                $query->whereNull('room_id')->orWhere('room_id', $approval->room_id);
            })
            ->where(function ($query) {
                $query->whereNull('user_id')->orWhere('user_id', 0);
            })
            ->latest()
            ->first();

        Log::info('UserInfo found: ' . ($userInfo ? json_encode($userInfo->toArray()) : 'null'));

        if (!$userInfo) {
            return back()->withErrors('❌ Không tìm thấy thông tin người cần thêm (hoặc đã được duyệt trước đó).');
        }

        try {
            $password = Str::random(8);
            Log::info('Creating user with email: ' . $userInfo->email);

            $user = User::create([
                'name' => $userInfo->full_name ?: $fullNameFromNote,
                'email' => $userInfo->email,
                'password' => Hash::make($password),
                'role' => 'Renter',
            ]);
            Log::info("User created: ID={$user->id}, Email={$user->email}");

            $userInfo->update(['user_id' => $user->id]);

            Room::where('room_id', $approval->room_id)->increment('people_renter');
            Log::info("UserInfo updated, room #{$approval->room_id} people_renter +1");

            Mail::raw(
                "🎉 Chào {$userInfo->full_name},\n\nTài khoản của bạn đã được tạo thành công!\n\n📧 Email: {$user->email}\n🔑 Mật khẩu: {$password}\n\nVui lòng đăng nhập và đổi mật khẩu ngay khi có thể.\n\nTrân trọng.",
                function ($message) use ($user) {
                    $message->to($user->email)->subject('Thông tin tài khoản thuê phòng');
                }
            );
            Log::info('Email sent to: ' . $user->email);

            $approval->delete();
            Log::info('Approval deleted');

            return back()->with('success', '✅ Đã duyệt và tạo tài khoản thành công. Thông tin đã được gửi qua email.');
        } catch (\Exception $e) {
            Log::error('Error in approveUser: ' . $e->getMessage());
            return back()->withErrors('❌ Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}
