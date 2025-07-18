<?php

namespace App\Http\Controllers\Landlord\Staff;

use App\Http\Controllers\Controller;
use App\Models\Landlord\Approval;
use App\Models\Landlord\RentalAgreement;
use App\Models\Landlord\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Smalot\PdfParser\Parser;

class ContractController extends Controller
{
    // Trang hiển thị hợp đồng của phòng
    public function index(Room $room)
    {
        $pendingApproval = Approval::where('room_id', $room->room_id)
            ->where('status', 'pending')
            ->latest()
            ->first();

        // Hợp đồng đang hoạt động (nếu có)
        $activeAgreement = RentalAgreement::where('room_id', $room->room_id)
            ->whereIn('status', ['Signed', 'Active']) // hoặc trạng thái của bạn
            ->latest()
            ->first();

        // Các hợp đồng cũ đã bị khóa
        $terminatedAgreements = RentalAgreement::where('room_id', $room->room_id)
            ->where('status', 'Terminated')
            ->latest()
            ->get();

        $pdfText = null;
        if ($pendingApproval && $pendingApproval->file_path) {
            try {
                $parser = new Parser();
                $pdf = $parser->parseFile(storage_path('app/public/' . $pendingApproval->file_path));
                $pdfText = $pdf->getText();
            } catch (\Exception $e) {
                $pdfText = "⚠️ Không thể đọc nội dung file PDF: " . $e->getMessage();
            }
        }

        return view('landlord.staff.rooms.contract', compact(
            'room',
            'pendingApproval',
            'activeAgreement',
            'terminatedAgreements',
            'pdfText'
        ));
    }


    // Staff tải hợp đồng lên → Gửi duyệt
    public function uploadAgreementFile(Request $request, Room $room)
    {
        $request->validate([
            'agreement_file' => 'required|mimes:pdf|max:5120',
        ]);

        // Lưu file
        $file = $request->file('agreement_file');
        $path = $file->store('contracts/manual', 'public');

        // Tìm chủ trọ của phòng
        $landlordId = $room->property->landlord_id ?? null;
        if (!$landlordId) {
            return back()->withErrors('Không tìm thấy chủ trọ của phòng này!');
        }

        // Đọc nội dung file PDF để trích rental_price và deposit
        $rental_price = null;
        $deposit = null;

        try {
            $parser = new Parser();
            $pdf = $parser->parseFile(storage_path('app/public/' . $path));
            $text = $pdf->getText();

            // ⚠️ Điều chỉnh theo nội dung hợp đồng PDF bạn dùng
            preg_match('/Giá thuê\s*[:\-]?\s*([\d.,]+)/ui', $text, $rentMatch);
            preg_match('/Tiền cọc\s*[:\-]?\s*([\d.,]+)/ui', $text, $depositMatch);

            $rental_price = isset($rentMatch[1])
                ? (float) str_replace([',', '.'], '', $rentMatch[1])
                : null;

            $deposit = isset($depositMatch[1])
                ? (float) str_replace([',', '.'], '', $depositMatch[1])
                : null;
        } catch (\Exception $e) {
            return back()->withErrors('Không thể đọc nội dung file PDF: ' . $e->getMessage());
        }

        // Tạo bản ghi yêu cầu duyệt
        Approval::create([
            'room_id'      => $room->room_id,
            'staff_id'     => Auth::id(),
            'landlord_id'  => $landlordId,
            'rental_price' => $rental_price,
            'deposit'      => $deposit,
            'file_path'    => $path,
            'type'         => 'contract',
            'status'       => 'pending',
            'note'         => 'Tệp hợp đồng mới cần duyệt.',
        ]);

        return redirect()->back()->with('success', 'Hợp đồng đã được gửi để chờ duyệt!');
    }
}
