<?php

namespace App\Http\Controllers\Client;

use App\Models\Landlord\Property;
use App\Http\Controllers\Controller;
use App\Models\Landlord\RentalAgreement;
use App\Models\Landlord\Room;
use Illuminate\Support\Facades\Auth;
use App\Models\UserInfo;
use Illuminate\Support\Str;
use App\Models\Landlord\PendingRoomUser;
use App\Models\RoomUser;
use Illuminate\Http\Request;
use App\Models\User;
use PhpOffice\PhpWord\IOFactory;
use Illuminate\Support\Facades\DB;
use App\Models\StaffPost;

class HomeController extends Controller
{
    public function renter()
    {
        // Lấy danh sách phòng trọ mới nhất có phân trang
        $rooms = Room::latest()->paginate(6);

        $posts = StaffPost::with(['category', 'features', 'property'])
            ->where('status', 1)
            ->where('is_public', true)
            ->latest('approved_at')
            ->paginate(6);

        return view('home.render', compact('posts', 'rooms'));
    }


    public function favorites()
    {
        $favorites = Auth::user()->favorites()->get();
        return view('home.favourite', compact('favorites'));
    }

    public function toggleFavorite(Property $property)
    {
        $user = Auth::user();

        // ✅ So sánh rõ bảng: favorites.property_id
        $isFavorited = DB::table('favorites')
            ->where('user_id', $user->id)
            ->where('property_id', $property->property_id)
            ->exists();

        if ($isFavorited) {
            $user->favorites()->detach($property->property_id);
            return redirect()->route('home.favorites')->with('success', 'Đã xóa khỏi danh sách yêu thích.');
        } else {
            $user->favorites()->attach($property->property_id);
            return redirect()->route('home.favorites')->with('success', 'Đã thêm vào danh sách yêu thích!');
        }
    }
    public function StausAgreement()
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
        $rentalId = $request->input('rental_id');
        $rooms = Room::where('room_id', $roomId)->first();
        return view('home.create-user', compact('roomId', 'rentalId', 'rooms'));
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
    public function myRoom()
    {
        $user = Auth::user();

        // Ví dụ: lấy danh sách các phòng mà user đang thuê
        $rooms = $user->rentedRooms()->with('property', 'photos')->paginate(6);

        return view('home.my-room', compact('rooms'));
    }
public function stopRentForm()
{
    $user = auth()->user();

    // Lấy phòng của user đang thuê
    $rentalAgreement = RentalAgreement::where('renter_id', $user->id)
        ->with('room')
        ->latest()
        ->first();

    if (!$rentalAgreement || !$rentalAgreement->room) {
        return view('home.stopRentForm', ['roomUsers' => collect()]);
    }

    // Lấy tất cả hợp đồng của người trong phòng này
    $roomUsers = RentalAgreement::with('renter')
        ->where('room_id', $rentalAgreement->room->room_id)
        ->where('is_active', true)
        ->get();

    return view('home.stopRentForm', compact('roomUsers'));
}
  public function stopUserRental(Request $request, $id)
{
    $request->validate([
        'leave_date' => 'required|date|after:today',
    ]);

    $user = auth()->user();

    $agreement = RentalAgreement::with('room')->findOrFail($id);

    // ✅ Chỉ người đang thuê trong phòng mới được phép gửi yêu cầu
    $currentAgreement = $user->rentalAgreements()->latest()->first();
    if (!$currentAgreement || $agreement->room_id !== $currentAgreement->room_id) {
        abort(403, 'Bạn không có quyền gửi yêu cầu cho hợp đồng này.');

    }

    // ✅ Gán thông tin dừng thuê
    $agreement->leave_date = $request->leave_date;
    $agreement->status = 'pending'; // chuyển trạng thái chờ duyệt
    $agreement->stop_requested = true;
    $agreement->save();

    // 👉 (Tùy chọn) Gửi thông báo/mail cho staff tại đây nếu cần

//     return redirect()->back()->with('success', '📝 Yêu cầu dừng thuê đã được gửi và đang chờ duyệt.');
 }
}
