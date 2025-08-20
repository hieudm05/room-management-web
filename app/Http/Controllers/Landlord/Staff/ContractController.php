<?php

namespace App\Http\Controllers\Landlord\Staff;

use App\Http\Controllers\Controller;
use App\Models\Landlord\Approval;
use App\Models\Landlord\ImageDeposit;
use App\Models\Landlord\RentalAgreement;
use App\Models\Landlord\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser;

class ContractController extends Controller
{
    /**
     * Hiển thị trang hợp đồng của phòng
     */
    public function index(Room $room)
    {
        $pendingApproval = Approval::where('room_id', $room->room_id)
            ->where('status', 'pending')
            ->latest()
            ->first();

        $activeAgreement = RentalAgreement::where('room_id', $room->room_id)
            ->whereIn('status', ['Signed', 'Active'])
            ->latest()
            ->first();

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

    /**
     * Tải file hợp đồng và xem trước
     */
    public function uploadAgreementFile(Request $request, Room $room)
    {
        $request->validate([
            'agreement_file' => 'required|mimes:pdf|max:5120', // tối đa 5MB
        ]);

        $file = $request->file('agreement_file');
        $path = $file->store('contracts/temp', 'public');

        session(['previewPath' => $path]);

        return view('landlord.staff.rooms.contract-preview', [
            'room' => $room,
            'tempPath' => $path,
            'publicUrl' => asset('storage/' . $path),
        ]);
    }

    /**
     * Xác nhận file hợp đồng và gửi duyệt
     */
    public function confirm(Request $request, Room $room)
    {
        $request->validate([
            'temp_path' => 'required|string',
        ]);

        $tempPath = $request->input('temp_path');
        $fullPath = storage_path('app/public/' . $tempPath);

        if (!file_exists($fullPath)) {
            return back()->withErrors('Không tìm thấy file tạm để xác nhận!');
        }

        $landlordId = optional($room->property)->landlord_id;

        if (!$landlordId) {
            return back()->withErrors('Không tìm thấy chủ trọ của phòng này!');
        }

        $rental_price = null;
        $deposit = null;

        try {
            $parser = new Parser();
            $pdf = $parser->parseFile($fullPath);
            $text = $pdf->getText();

            // Tách giá thuê
            if (preg_match('/Giá thuê\s*[:\-]?\s*([\d.,]+)/ui', $text, $rentMatch)) {
                $rental_price = (float) str_replace([',', '.'], '', $rentMatch[1]);
            }

            // Tách tiền cọc
            if (preg_match('/Tiền cọc\s*[:\-]?\s*([\d.,]+)/ui', $text, $depositMatch)) {
                $deposit = (float) str_replace([',', '.'], '', $depositMatch[1]);
            }
        } catch (\Exception $e) {
            return back()->withErrors('Không thể đọc nội dung file PDF: ' . $e->getMessage());
        }

        // Tạo yêu cầu duyệt
        Approval::create([
            'room_id'      => $room->room_id,
            'staff_id'     => Auth::id(),
            'landlord_id'  => $landlordId,
            'rental_price' => $rental_price,
            'deposit'      => $deposit,
            'file_path'    => $tempPath,
            'type'         => 'contract',
            'status'       => 'pending',
            'note'         => 'Tệp hợp đồng được xác nhận sau khi xem trước.',
        ]);
        session()->forget('previewPath');

        return redirect()->route('landlords.staff.contract.index', $room)
            ->with('success', 'Hợp đồng đã được gửi duyệt!');
    }

    /**
     * Upload ảnh minh chứng đặt cọc
     */
    public function uploadDepositImage(Request $request, Room $room)
    {
        $request->validate([
            'deposit_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $file = $request->file('deposit_image');
        $path = $file->store('deposits', 'public');

        // Lưu vào bảng image_deposit
        $depositImage = ImageDeposit::create([
            'room_id'   => $room->room_id,
            'file_path' => $path,
            'uploaded_by' => Auth::id(),
        ]);

        // Tìm hợp đồng mới nhất của phòng này
        $agreement = RentalAgreement::where('room_id', $room->room_id)
            ->latest()
            ->first();

        if ($agreement) {
            // Gán deposit_id cho hợp đồng
            $agreement->deposit_id = $depositImage->room_id; // vì room_id là PK trong bảng image_deposit
            $agreement->save();
        }

        return redirect()->route('landlords.staff.contract.index', $room)
            ->with('success', 'Ảnh minh chứng đặt cọc đã được tải lên và gắn với hợp đồng!');
    }
}
