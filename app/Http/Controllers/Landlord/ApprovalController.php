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

class ApprovalController extends Controller
{
    // Danh sách hợp đồng chờ duyệt (landlord dashboard)
    public function index()
    {
        $landlordId = Auth::id();
        $pendingApprovals = Approval::where('landlord_id', $landlordId)
            ->where('status', 'pending')
            ->where('type', 'contract')
            ->with('room')
            ->latest()
            ->get();

        return view('landlord.approvals.index', compact('pendingApprovals'));
    }

    // Duyệt hợp đồng

    public function approve($id)
    {
        $approval = Approval::findOrFail($id);

        // 1. Tạo hợp đồng
        $rental = RentalAgreement::create([
            'room_id' => $approval->room_id,
            'staff_id' => $approval->staff_id,
            'created_by' => $approval->staff_id,
            'landlord_id' => $approval->landlord_id,
            'start_date' => now(),
            'end_date' => now()->addMonths(6),
            'rental_price' => $approval->rental_price,
            'deposit' => $approval->deposit,
            'status' => 'active',
            'contract_file' => $approval->file_path,
            'agreement_terms' => 'Thỏa thuận cơ bản: Thanh toán đúng hạn, không phá hoại tài sản.',
        ]);

        // 2. Cập nhật thông tin phòng
        $room = Room::findOrFail($approval->room_id);
        $room->status = 'Rented';
        $room->id_rental_agreements = $rental->rental_id;
        $room->people_renter = 1; // Giả sử chỉ có 1 người thuê
        $room->save();

        // 3. Đọc file PDF
        $contractPath = $approval->file_path;
        $fullPath = storage_path('app/public/' . $contractPath);
        $text = '';
        try {
            $parser = new Parser();
            $pdf = $parser->parseFile($fullPath);
            $text = $pdf->getText();
        } catch (\Exception $e) {
            $text = '';
        }
        // 4. Lấy thông tin khách thuê
        $fullName = $cccd = $phone = $tenantEmail = null;

        // Trích toàn bộ khối từ "BÊN THUÊ PHÒNG TRỌ" đến "Nội dung hợp đồng"
        if (preg_match('/BÊN THUÊ PHÒNG TRỌ \(Bên B\):(.+?)Nội dung hợp đồng/su', $text, $match)) {
            $infoBlock = $match[1];

            preg_match('/Họ tên:\s*(.+)/u', $infoBlock, $nameMatch);
            preg_match('/SĐT:\s*([0-9]+)/u', $infoBlock, $phoneMatch);
            preg_match('/CCCD:\s*([0-9]+)/u', $infoBlock, $cccdMatch);
            preg_match('/Email:\s*([^\s]+)/iu', $infoBlock, $emailMatch);

            $fullName = trim($nameMatch[1] ?? '');
            $phone = $phoneMatch[1] ?? '';
            $cccd = $cccdMatch[1] ?? '';
            $tenantEmail = $emailMatch[1] ?? '';
        }



        // 5. Kiểm tra user tồn tại
        $user = User::where('email', $tenantEmail)->first();
        if (!$user) {
            $password = Str::random(8);
            $user = User::create([
                'name' => $fullName,
                'email' => $tenantEmail,
                'password' => Hash::make($password),
                'role' => 'Renter',
            ]);

            // Gửi mail thông báo
            Mail::raw(
                "Chào $fullName,\n\nTài khoản của bạn đã được tạo:\nEmail: $tenantEmail\nMật khẩu: $password\n\nVui lòng đăng nhập và thay đổi mật khẩu sau lần đăng nhập đầu tiên.\n\nTrân trọng,\nHệ thống quản lý phòng trọ",
                function ($message) use ($tenantEmail) {
                    $message->to($tenantEmail)->subject('Tài khoản thuê phòng đã được tạo');
                }
            );
        }

        // 6. Cập nhật renter_id trong hợp đồng
        $rental->update(['renter_id' => $user->id]);

        // 7. Lưu thông tin vào user_infos
        UserInfo::updateOrCreate(
            ['user_id' => $user->id],
            [
                'full_name' => $fullName,
                'cccd' => $cccd,
                'phone' => $phone,
                "room_id" => $approval->room_id,
            ]
        );

        // 8. Xóa bản ghi chờ phê duyệt
        $approval->delete();

        return back()->with('success', 'Hợp đồng đã được duyệt và thêm vào hệ thống.');
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
        dd($userInfo);

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
