<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Landlord\Approval;
use App\Models\Landlord\ImageDeposit;
use App\Models\Landlord\RentalAgreement;
use App\Models\Landlord\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Smalot\PdfParser\Parser;
use App\Models\UserInfo;

class ApprovalController extends Controller
{
    // Danh sách hợp đồng + ảnh đặt cọc chờ duyệt
    public function index()
    {
        $landlordId = Auth::id();

        $pendingApprovals = Approval::with('room.property')
            ->where('landlord_id', $landlordId)
            ->where('status', 'pending')
            ->latest()
            ->get();

        return view('landlord.approvals.index', compact('pendingApprovals'));
    }

    // Duyệt hợp đồng
    public function approve(Request $request, $id)
    {
        $approval = Approval::findOrFail($id);

        if ($approval->type === 'contract') {
            // Xử lý hợp đồng (giữ nguyên logic bạn đang có)
            $rental = RentalAgreement::create([
                'room_id'        => $approval->room_id,
                'staff_id'       => $approval->staff_id,
                'created_by'     => $approval->staff_id,
                'landlord_id'    => $approval->landlord_id,
                'start_date'     => now(),
                'end_date'       => now()->addMonths(6),
                'rental_price'   => $approval->rental_price,
                'deposit'        => $approval->deposit,
                'status'         => 'active',
                'contract_file'  => $approval->file_path,
                'agreement_terms' => 'Thỏa thuận cơ bản: Thanh toán đúng hạn, không phá hoại tài sản.',
            ]);

            $room = Room::findOrFail($approval->room_id);
            $room->status = 'Rented';
            $room->id_rental_agreements = $rental->rental_id;
            $room->people_renter = 1;
            $room->is_contract_locked = false;
            $room->save();

            // Đọc file pdf để lấy thông tin khách thuê
            $contractPath = $approval->file_path;
            $fullPath = storage_path('app/public/' . $contractPath);

            try {
                $parser = new Parser();
                $pdf = $parser->parseFile($fullPath);
                $text = $pdf->getText();
            } catch (\Exception $e) {
                return back()->withErrors('❌ Không thể đọc file hợp đồng.');
            }

            $fullName = $cccd = $phone = $tenantEmail = null;
            if (preg_match('/BÊN THUÊ PHÒNG TRỌ.*?\(Bên B\):(.*?)Nội dung hợp đồng/isu', $text, $match)) {
                $infoBlock = $match[1];

                preg_match('/Họ\s*tên:\s*(.+)/iu', $infoBlock, $nameMatch);
                preg_match('/SĐT:\s*([0-9]+)/iu', $infoBlock, $phoneMatch);
                preg_match('/CCCD:\s*([0-9]+)/iu', $infoBlock, $cccdMatch);
                preg_match('/Email:\s*([^\s]+)/iu', $infoBlock, $emailMatch);

                $fullName    = trim($nameMatch[1] ?? '');
                $phone       = $phoneMatch[1] ?? '';
                $cccd        = $cccdMatch[1] ?? '';
                $tenantEmail = $emailMatch[1] ?? '';
            }

            if (empty($fullName) || empty($tenantEmail)) {
                return back()->withErrors('❌ Không tìm thấy đủ thông tin (Họ tên/Email) trong hợp đồng.');
            }

            // Kiểm tra hoặc tạo user
            $user = User::where('email', $tenantEmail)->first();
            if (!$user) {
                $password = Str::random(8);
                $user = User::create([
                    'name'     => $fullName,
                    'email'    => $tenantEmail,
                    'password' => Hash::make($password),
                    'role'     => 'Renter',
                ]);

                Mail::raw(
                    "Chào $fullName,\n\nTài khoản của bạn đã được tạo:\nEmail: $tenantEmail\nMật khẩu: $password\n\nVui lòng đăng nhập và đổi mật khẩu ngay.\n\nTrân trọng.",
                    function ($message) use ($tenantEmail) {
                        $message->to($tenantEmail)->subject('Tài khoản thuê phòng đã được tạo');
                    }
                );
            }

            $rental->update(['renter_id' => $user->id]);

            UserInfo::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'user_id'   => $user->id,
                    'full_name' => $fullName ?: $user->name,
                    'cccd'      => $cccd,
                    'phone'     => $phone,
                    'email'     => $tenantEmail,
                    'room_id'   => $approval->room_id,
                    'rental_id' => $rental->rental_id,
                ]
            );

            $approval->delete();

            return back()->with('success', '✅ Hợp đồng đã được duyệt và thêm vào hệ thống.');
        }

        if ($approval->type === 'deposit_image') {
            // Lấy file_path đã lưu từ staff upload
            $filePath = $approval->file_path;

            ImageDeposit::create([
                'room_id'   => $approval->room_id,
                'image_url' => $filePath,
            ]);

            $approval->update(['status' => 'approved']);

            return back()->with('success', '✅ Ảnh đặt cọc đã được duyệt và lưu vào hệ thống.');
        }

        return back()->withErrors('❌ Loại phê duyệt không hợp lệ.');
    }

    // Từ chối hợp đồng/ảnh đặt cọc
    public function reject($id)
    {
        $approval = Approval::findOrFail($id);

        if ($approval->landlord_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền từ chối yêu cầu này.');
        }

        $approval->delete();

        return back()->with('warning', '❌ Yêu cầu đã bị từ chối và xóa bỏ.');
    }
}
