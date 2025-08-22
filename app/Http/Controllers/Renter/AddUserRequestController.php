<?php

namespace App\Http\Controllers\Renter;

use App\Http\Controllers\Controller;
use App\Models\UserInfo;
use App\Models\Landlord\Approval;
use App\Models\Landlord\RentalAgreement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddUserRequestController extends Controller
{
    public function create()
    {
        $renter = Auth::user();

        // Lấy thông tin phòng người thuê đang ở
        $renterInfo = UserInfo::where('user_id', $renter->id)
            ->with('room.property') // nếu muốn lấy thông tin property luôn
            ->first();

        if (!$renterInfo || !$renterInfo->room) {
            return back()->with('error', 'Không tìm thấy phòng trọ của bạn.');
        }

        $room = $renterInfo->room;
        $roomId = $room->room_id ?? $room->id;

        // Lấy hợp đồng gần nhất của người thuê với phòng đó
        $rental = RentalAgreement::where('room_id', $roomId)
            ->where('renter_id', $renter->id)
            ->latest()
            ->first();

        $rentalId = $rental?->rental_id ?? null;
        // dd($rentalId);
        return view('renter.storeuser', [
            'roomId' => $roomId,
            'rooms' => $room,
            'rental' => $rental,
            'rentalId' => $rentalId,
        ]);
    }



    public function store(Request $request)
    {
        $request->validate([
            'rental_id' => 'required|exists:rental_agreements,rental_id',
            'full_name' => 'required|array',
            'cccd' => 'required|array',
            'phone' => 'required|array',
            'email' => 'required|array',

            'full_name.*' => 'required|string|max:100',
            'cccd.*' => 'required|string|max:20|distinct|unique:user_infos,cccd',
            'phone.*' => 'required|string|max:20',
            'email.*' => 'required|email|distinct|unique:user_infos,email',
        ]);

        $renter = Auth::user();

        $renterInfo = UserInfo::where('user_id', $renter->id)
            ->with('room.property')
            ->first();

        if (!$renterInfo || !$renterInfo->room) {
            return back()->with('error', 'Không tìm thấy phòng trọ của bạn.');
        }

        $room = $renterInfo->room;
        $roomId = $room->room_id ?? $room->id;
        $landlordId = $room->property->landlord_id ?? null;

        if (!$landlordId) {
            return back()->with('error', 'Không xác định được chủ trọ.');
        }

        $rentalId = $request->rental_id;

        $currentUsers = UserInfo::where('room_id', $roomId)
            ->whereNotNull('user_id')
            ->count();

        $pendingUsers = UserInfo::where('room_id', $roomId)
            ->whereNull('user_id')
            ->count();

        $newRequestCount = count($request->full_name);
        $totalAfter = $currentUsers + $pendingUsers + $newRequestCount;

        if ($totalAfter > $room->occupants) {
            $remaining = max(0, $room->occupants - $currentUsers - $pendingUsers);
            return back()->withErrors("❌ Phòng chỉ còn có thể thêm tối đa {$remaining} người.");
        }

        foreach ($request->full_name as $index => $name) {
            $cccd = $request->cccd[$index];
            $phone = $request->phone[$index];
            $email = $request->email[$index];

            UserInfo::create([
                'room_id' => $roomId,
                'cccd' => $cccd,
                'phone' => $phone,
                'email' => $email,
                'user_id' => null,
                'full_name' => $name,
                'rental_id' => $rentalId,
            ]);

            Approval::create([
                'room_id' => $roomId,
                'landlord_id' => $landlordId,
                'user_id' => $renter->id,
                'rental_id' => $rentalId,
                'type' => 'add_user',
                'note' => "Tên: {$name} | Email: {$email} | CCCD: {$cccd} | SĐT: {$phone}",
                'status' => 'pending',
                'file_path' => null,
            ]);
        }

        return back()->with('success', '✅ Yêu cầu thêm người đã được gửi.');
    }
    public function parseCCCD(Request $request)
    {
        $request->validate([
            'cccd_image' => 'required|image|max:5120', // 5MB
        ]);

        $image = $request->file('cccd_image');

        // Lưu tạm
        $path = $image->store('tmp', 'public');

        // Gọi Tesseract OCR
        $text = $this->extractTextFromImage(storage_path('app/public/' . $path));

        // Parse text ra họ tên và số CCCD
        $info = $this->parseCCCDText($text);

        // Xoá file tạm
        Storage::disk('public')->delete($path);

        return response()->json([
            'success' => !empty($info),
            'full_name' => $info['full_name'] ?? '',
            'cccd' => $info['cccd'] ?? '',
        ]);
    }

    /**
     * Gọi Tesseract OCR
     */
    private function extractTextFromImage($filePath)
    {
        $output = null;
        $returnVar = null;
        exec("tesseract " . escapeshellarg($filePath) . " stdout", $output, $returnVar);
        return implode("\n", $output);
    }
}
