<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Landlord\Approval;
use App\Models\Landlord\ImageDeposit;
use App\Models\Landlord\RentalAgreement;
use App\Models\Landlord\Room;
use App\Models\User;
use App\Models\UserInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\Storage;

class ApprovalController extends Controller
{
    /**
     * Danh sách hợp đồng + ảnh đặt cọc chờ duyệt
     */
    public function index()
    {
        $landlordId = Auth::id();

        $pendingApprovals = Approval::with('room.property')
            ->where('landlord_id', $landlordId)
            ->where('status', 'pending')
            ->whereIn('type', ['contract', 'deposit_image'])
            ->latest()
            ->get();

        return view('landlord.approvals.index', compact('pendingApprovals'));
    }

    /**
     * Duyệt hợp đồng thuê phòng
     */
    public function approveContract($id)
    {
        // Lấy room từ id
        $approval = Approval::findOrFail($id);
        $room = Room::findOrFail($approval->room_id);

        // Kiểm tra quyền
        if ($room->property->landlord_id !== Auth::id()) {
            return redirect()->route('landlords.approval.index', $room->room_id)
                ->withErrors('❌ Bạn không có quyền quản lý phòng này.');
        }
        if ($room->status === 'Rented') {
            return redirect()->route('landlords.approval.index', $room->room_id)
                ->withErrors('❌ Phòng này đã có hợp đồng đang hiệu lực.');
        }

        // Tìm ảnh đặt cọc gần nhất
        $depositImage = ImageDeposit::where('room_id', $room->room_id)
            ->orderByDesc('id')
            ->first();

        if (!$depositImage) {
            return back()->withErrors('❌ Chưa có ảnh đặt cọc cho phòng này.');
        }
        // dd($depositImage);
        // die;

        // 1. Tạo hợp đồng (chưa có renter_id)
        $rental = \App\Models\RentalAgreement::create([
            'room_id'       => $approval->room_id,
            'staff_id'      => $approval->staff_id,
            'created_by'    => $approval->staff_id,
            'landlord_id'   => $approval->landlord_id,
            'deposit_id'    => $depositImage->id,
            'start_date'    => now(),
            'end_date'      => now()->addMonths(6),
            'rental_price'  => $approval->rental_price,
            'deposit'       => $approval->deposit,
            'status'        => 'Active',
            'contract_file' => $approval->file_path,
            'agreement_terms' => 'Thỏa thuận cơ bản: Thanh toán đúng hạn, không phá hoại tài sản.',
        ]);
        $depositImage->update(['rental_id' => $rental->rental_id]);

        // 2. Cập nhật phòng
        $room->update([
            'status'               => 'Rented',
            'id_rental_agreements' => $rental->rental_id,
            'is_contract_locked'   => false,
        ]);

        // 3. Đọc file PDF hợp đồng
        $fullPath = storage_path('app/public/' . $approval->file_path);
        $text = '';
        try {
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($fullPath);
            $text = $pdf->getText();
        } catch (\Exception $e) {
            $text = '';
        }

        // 4. Lấy thông tin người thuê từ hợp đồng
        $fullName = $cccd = $phone = $tenantEmail = null;
        if (preg_match('/BÊN THUÊ PHÒNG TRỌ.*?\(Bên B\):(.*?)(?:Căn cứ pháp lý|$)/su', $text, $match)) {
            $infoBlock = $match[1];
            preg_match('/Họ\/tên:\s*(.+)/u', $infoBlock, $nameMatch);
            preg_match('/CMND\/CCCD\s*số:\s*([0-9]+)/u', $infoBlock, $cccdMatch);
            preg_match('/SĐT:\s*([0-9]+)/u', $infoBlock, $phoneMatch);
            preg_match('/Email:\s*([^\s]+)/iu', $infoBlock, $emailMatch);

            $fullName    = trim($nameMatch[1] ?? '');
            $phone       = $phoneMatch[1] ?? '';
            $cccd        = $cccdMatch[1] ?? '';
            $tenantEmail = $emailMatch[1] ?? '';
        }

        // 5. Tạo hoặc lấy user renter
        $user = User::where('email', $tenantEmail)->first();
        if (!$user && $tenantEmail) {
            $password = Str::random(8);
            $user = User::create([
                'name'     => $fullName ?: 'Người thuê',
                'email'    => $tenantEmail,
                'password' => Hash::make($password),
                'role'     => 'Renter',
            ]);

            // Gửi mail tài khoản
            Mail::raw(
                "Chào $fullName,\n\nTài khoản của bạn đã được tạo:\nEmail: $tenantEmail\nMật khẩu: $password\n\nVui lòng đổi mật khẩu sau khi đăng nhập.",
                function ($message) use ($tenantEmail) {
                    $message->to($tenantEmail)->subject('Tài khoản thuê phòng đã được tạo');
                }
            );
        }

        if ($user) {
            // 6. Gắn renter_id vào hợp đồng
            $rental->update(['renter_id' => $user->id]);

            // 7. Lưu thông tin người thuê
            UserInfo::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'full_name' => $fullName ?: $user->name,
                    'cccd'      => $cccd,
                    'phone'     => $phone,
                    'email'     => $tenantEmail,
                    'room_id'   => $approval->room_id,
                    'rental_id' => $rental->rental_id,
                ]
            );
        }

        // 8. Xóa approval
        $approval->delete();

        return back()->with('success', '✅ Hợp đồng đã được duyệt và thêm vào hệ thống.');
    }



    /**
     * Duyệt minh chứng đặt cọc
     */
    public function approveDeposit($id)
    {
        $approval = Approval::with('room.property')->findOrFail($id);

        if ($approval->landlord_id !== Auth::id() || $approval->room->property->landlord_id !== Auth::id()) {
            abort(403, '❌ Bạn không có quyền duyệt minh chứng này.');
        }

        ImageDeposit::create([
            'room_id'   => $approval->room_id,
            'user_id'   => $approval->user_id,
            'image_url' => $approval->file_path,
        ]);

        $approval->delete();

        return back()->with('success', '✅ Đã duyệt và lưu minh chứng đặt cọc.');
    }

    /**
     * Từ chối hợp đồng / minh chứng
     */
    public function reject($id)
    {
        $approval = Approval::findOrFail($id);

        if ($approval->landlord_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền từ chối yêu cầu này.');
        }

        $approval->delete();

        return back()->with('warning', '❌ Yêu cầu đã bị từ chối.');
    }
}
