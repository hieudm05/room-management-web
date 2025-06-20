<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Landlord\RentalAgreement;
use App\Models\Landlord\Room;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Landlord\PendingRoomUser;
use App\Models\RoomUser;
use Illuminate\Http\Request;
use App\Models\User;
use PhpOffice\PhpWord\IOFactory;

class HomeController extends Controller
{
public function renter()
{
    // Lấy danh sách phòng trọ mới nhất có phân trang
    $rooms = Room::latest()->paginate(6); // thêm paginate

    return view('home.render', compact('rooms'));
}
PUblic function StausAgreement()
{
     $user = Auth::user();
    $renter_id = $user->id;
    $rentalAgreement = RentalAgreement::find($renter_id);
  
    if (!$rentalAgreement) {
        return view('home.statusAgreement', [
            'rentalAgreement' => null,
            'wordText' => '',
            'tenant_name' => '',
            'tenant_email' => '',
            'renter_id' => $renter_id,
            'room' => null
        ]);
    }

    $contractPath = $rentalAgreement->contract_file;
    $fullPath = storage_path('app/public/' . $contractPath);

    if (!$contractPath || !file_exists($fullPath)) {
        return view('home.statusAgreement', [
            'rentalAgreement' => $rentalAgreement,
            'wordText' => '',
            'tenant_name' => '',
            'tenant_email' => '',
            'renter_id' => $renter_id,
            'room' => $rentalAgreement->room ?? null
        ]);
    }

    // Đọc file Word
    $text = '';
    try {
        $phpWord = IOFactory::load($fullPath);
        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if (method_exists($element, 'getText')) {
                    $text .= $element->getText() . "\n";
                }
            }
        }
    } catch (\Exception $e) {
        $text = 'Không thể đọc file Word: ' . $e->getMessage();
    }

    // Trích thông tin
    preg_match('/Họ tên:\s*(.*)/i', $text, $nameMatch);
    preg_match('/Email:\s*([^\s]+)/i', $text, $emailMatch);

    return view('home.statusAgreement', [
        'rentalAgreement' => $rentalAgreement,
        'wordText' => $text,
        'tenant_name' => trim($nameMatch[1] ?? ''),
        'tenant_email' => trim($emailMatch[1] ?? ''),
        'renter_id' => $renter_id,
        'room' => $rentalAgreement->room ?? null
    ]);
    // Lấy danh sách phòng trọ mới nhất có phân trang
     

    return view('home.statusAgreement');
}
 public function create(Request $request)
    {
      
       $roomId = $request->input('room_id');
       $retalId = $request->input('rental_id');
       $rooms = Room::where('room_id', $roomId)->first();
        return view('home.create-user', compact('roomId', 'retalId' , 'rooms'));
    }
public function store(Request $request)
    {

       
       $validated = $request->validate([
        'room_id' => 'required|exists:rooms,room_id',
        'name' => 'required|string|max:255',
        'phone' => 'required|string|max:20',
        'cccd' => 'required|string|max:20',
        'email' => 'required|email',
        'rental_id' => 'required|exists:rental_agreements,rental_id',
    ]);

    RoomUser::create([
        'room_id' => $validated['room_id'],
        'name' => $validated['name'],
        'phone' => $validated['phone'],
        'cccd' => $validated['cccd'],
        'email' => $validated['email'],
        'rental_id' => $validated['rental_id'], // map đúng tên cột
    ]);

    return redirect()->back()->with('success', 'Đăng ký thành công! Chúng tôi sẽ liên hệ với bạn sớm nhất có thể.');
    }

}