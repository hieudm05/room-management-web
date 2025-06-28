<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Landlord\Approval;
use App\Models\Landlord\RentalAgreement;
use App\Models\Landlord\Room;
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

class ApprovalUserController extends Controller
{
    // Danh sách hợp đồng chờ duyệt (landlord dashboard)
    public function index()
    {
        $landlordId = Auth::id();
        $pendingApprovals = Approval::where('landlord_id', $landlordId)
            ->where('status', 'pending')
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
            abort(403, 'Bạn không có quyền từ chối hợp đồng này.');
        }

        $approval->delete();

        return redirect()->back()->with('warning', 'Hợp đồng đã bị từ chối và xóa bỏ.');
    }

    public function approveUser($id)
    {
        $approval = Approval::findOrFail($id);

        if ($approval->type !== 'add_user') {
            return back()->withErrors('❌ Loại yêu cầu không hợp lệ.');
        }

        // 🔍 Tách họ tên và email từ note: "Tên: Nguyễn Văn A | Email: abc@example.com"
        preg_match('/Tên:\s*(.*?)\s*\|\s*Email:\s*(.*)/', $approval->note, $matches);
        $fullNameFromNote = trim($matches[1] ?? '');
        $email = trim($matches[2] ?? '');

        if (empty($fullNameFromNote) || empty($email)) {
            return back()->withErrors('❌ Không thể tách thông tin người dùng từ yêu cầu.');
        }

        // 🔍 Tìm user_info chưa có user_id
        $userInfo = UserInfo::where('room_id', $approval->room_id)
            ->where('email', $email)
            ->whereNull('user_id')
            ->latest()
            ->first();

        if (!$userInfo) {
            return back()->withErrors('❌ Không tìm thấy thông tin người cần thêm.');
        }


        // 🔐 Tạo tài khoản user
        try {
            $password = Str::random(8);

            $user = User::create([
                'name'     => $userInfo->full_name ?: $fullNameFromNote ,
                'email'    => $userInfo->email,
                'password' => Hash::make($password),
                'role'     => 'Renter', // hoặc dùng constant nếu có
            ]);

            // 🔄 Gán user_id vào user_info
            $userInfo->update(['user_id' => $user->id]);

            // 📧 Gửi mail thông báo
            Mail::raw(
                "🎉 Chào {$userInfo->full_name},\n\nTài khoản của bạn đã được tạo thành công!\n\n📧 Email: {$user->email}\n🔑 Mật khẩu: {$password}\n\nVui lòng đăng nhập và đổi mật khẩu ngay khi có thể.\n\nTrân trọng.",
                function ($message) use ($user) {
                    $message->to($user->email)
                        ->subject('Thông tin tài khoản thuê phòng');
                }
            );

            // 🧹 Xóa yêu cầu sau khi xử lý xong
            $approval->delete();

            return back()->with('success', '✅ Đã duyệt và tạo tài khoản thành công. Thông tin đã được gửi qua email.');
        } catch (\Exception $e) {
            return back()->withErrors('❌ Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}
