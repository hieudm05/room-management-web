<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Landlord\Approval;
use App\Models\Landlord\RentalAgreement;
use App\Models\Landlord\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

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

    // Thêm vào bảng rental_agreements
    RentalAgreement::create([
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

    // Xoá bản ghi approval
    $approval->delete();

    return redirect()->back()->with('success', 'Hợp đồng đã được duyệt và thêm vào hệ thống.');
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
