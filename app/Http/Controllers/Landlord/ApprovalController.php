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
        if (preg_match('/BÊN THUÊ PHÒNG TR Ọ.*?\(gọi tắt là Bên B\):(.*?)Căn cứ pháp lý/s', $text, $benBBlock)) {
            $benBText = $benBBlock[1];

            preg_match('/- Ông\/Bà:\s*(.+)/u', $benBText, $nameMatch);
            preg_match('/- CMND\/CCCD số:\s*([0-9]+)/u', $benBText, $cccdMatch);
            preg_match('/- SĐT:\s*([0-9]+)/u', $benBText, $phoneMatch);
            preg_match('/- Email:\s*([^\s]+)/iu', $benBText, $emailMatch);

            $fullName = trim($nameMatch[1] ?? '');
            $cccd = $cccdMatch[1] ?? '';
            $phone = $phoneMatch[1] ?? '';
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
}
