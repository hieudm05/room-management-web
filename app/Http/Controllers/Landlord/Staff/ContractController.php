<?php

namespace App\Http\Controllers\Landlord\Staff;

use App\Http\Controllers\Controller;
use App\Models\Landlord\Approval;
use App\Models\Landlord\ImageDeposit;
use App\Models\Landlord\RentalAgreement;
use App\Models\Landlord\Room;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
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
            ->where('type', 'contract') // Chỉ lấy hợp đồng
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
                // Xóa /storage/ khi truy cập file trong storage_path
                $filePath = ltrim($pendingApproval->file_path, '/storage/');
                $pdf = $parser->parseFile(storage_path('app/public/' . $filePath));
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
    public function showForm($roomId)
    {
        $room = Room::with(['facilities', 'services', 'property.landlord'])
            ->findOrFail($roomId);

        $landlord = optional($room->property)->landlord;
        $deposit_price = $room->deposit_price;
        $activeAgreement = RentalAgreement::where('room_id', $room->room_id)
            ->whereIn('status', ['Signed', 'Active'])
            ->latest()
            ->first();
        return view('landlord.rooms.form-contract', compact('room', 'landlord', 'deposit_price', 'activeAgreement'));
    }
    public function generate(Request $request, $roomId)
    {
        $request->validate([
            'ten' => 'required|string',
            'cccd' => 'required|string',
            'phone' => 'required|string',
            'email' => 'required|email',
            'so_nguoi_o' => 'required|integer',
            'so_nguoi_toi_da' => 'required|integer',
            'ngay_bat_dau' => 'required|date',
            'ngay_ket_thuc' => 'required|date',
            'dien_tich' => 'required|numeric',
            'gia_thue' => 'required|numeric',
            'gia_coc' => 'required|numeric',
        ]);

        $room = Room::with(['facilities', 'services', 'property.landlord'])
            ->findOrFail($roomId);

        $landlord = optional($room->property)->landlord;
        $data = $request->all();

        $facilities = $room->facilities->pluck('name')->toArray();

        $services = [];
        foreach ($room->services as $service) {
            $unitLabel = match ($service->service_id) {
                1 => 'số',
                2 => $service->pivot->unit === 'per_m3' ? 'm³' : 'người',
                3, 4 => $service->pivot->unit === 'per_room' ? 'phòng' : 'người',
                5, 6, 7 => 'phòng',
                default => $service->pivot->unit ?? '',
            };
            $services[] = [
                'name' => $service->name,
                'price' => $service->pivot->is_free ? 'Miễn phí' : number_format($service->pivot->price) . " VNĐ/$unitLabel",
            ];
        }

        $deposit_price = $data['gia_coc'] ?? 0;
        $rules = $data['noi_quy'] ?? ($room->property->rules ?? '');

        // Format ngày bắt đầu và kết thúc
        $ngay_bat_dau = Carbon::parse($data['ngay_bat_dau'])->format('d/m/Y');
        $ngay_ket_thuc = Carbon::parse($data['ngay_ket_thuc'])->format('d/m/Y');

        // Ngày hôm nay
        $today = Carbon::now();
        $ngay_hop_dong = $today->format('d');
        $thang_hop_dong = $today->format('m');
        $nam_hop_dong = $today->format('Y');

        $pdf = Pdf::loadView('landlord.rooms.pdf.Contract', [
            'landlord' => $landlord,
            'ten_nguoi_thue' => $data['ten'],
            'cccd_nguoi_thue' => $data['cccd'],
            'sdt_nguoi_thue' => $data['phone'],
            'email_nguoi_thue' => $data['email'],
            'so_luong_nguoi_o' => $data['so_nguoi_o'],
            'so_luong_nguoi_toi_da' => $data['so_nguoi_toi_da'],
            'ngay_bat_dau' => $ngay_bat_dau,
            'ngay_ket_thuc' => $ngay_ket_thuc,
            'room_number' => $room->room_number,
            'dien_tich' => $data['dien_tich'],
            'gia_thue' => $data['gia_thue'],
            'deposit_price' => $deposit_price,
            'facilities' => $facilities,
            'services' => $services,
            'rules' => $rules,
            // truyền ngày hôm nay
            'ngay_hop_dong' => $ngay_hop_dong,
            'thang_hop_dong' => $thang_hop_dong,
            'nam_hop_dong' => $nam_hop_dong,
        ]);

        $filename = "Hop_dong_Phong_{$room->room_number}.pdf";

        return $pdf->download($filename);
    }
}
