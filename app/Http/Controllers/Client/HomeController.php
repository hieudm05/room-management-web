<?php

namespace App\Http\Controllers\Client;

use App\Models\User;
use App\Models\RoomUser;
use App\Models\UserInfo;
use App\Models\StaffPost;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Landlord\Room;
use PhpOffice\PhpWord\IOFactory;
use App\Models\Landlord\Property;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Models\Landlord\PendingRoomUser;
use App\Models\Landlord\RentalAgreement;
use Illuminate\Pagination\LengthAwarePaginator;

class HomeController extends Controller
{
    /**
     * Hiển thị trang chủ với form tìm kiếm
     */
private function removeVietnameseAccents($str)
{
    $str = preg_replace("/[àáạảãâầấậẩẫăằắặẳẵ]/u", "a", $str);
    $str = preg_replace("/[èéẹẻẽêềếệểễ]/u", "e", $str);
    $str = preg_replace("/[ìíịỉĩ]/u", "i", $str);
    $str = preg_replace("/[òóọỏõôồốộổỗơờớợởỡ]/u", "o", $str);
    $str = preg_replace("/[ùúụủũưừứựửữ]/u", "u", $str);
    $str = preg_replace("/[ỳýỵỷỹ]/u", "y", $str);
    $str = preg_replace("/[đ]/u", "d", $str);
    return $str;
}
public function index(Request $request)
{
    $posts = $this->filterPosts($request)->latest()->paginate(10);
    return view('home.render', compact('posts'));
}

    /**
     * Xử lý tìm kiếm
     */
public function search(Request $request)
{
    // Lấy keyword và chuẩn hóa (giữ nguyên từ code cũ)
    $keyword = trim(preg_replace('/\s+/', ' ', $request->input('keyword', '')));

    // Tách keyword theo dấu phẩy
    $parts = array_map('trim', explode(',', $keyword));

    // Bắt đầu query
    $query = StaffPost::query();

    // --- Xử lý keyword ---
    if ($request->filled('keyword')) {
        \Log::info('KEYWORD PROCESSING:', [
            'original' => $request->keyword,
            'cleaned' => $keyword,
            'parts' => $parts,
            'parts_count' => count($parts)
        ]);

        $query->where(function ($q) use ($parts) {
            foreach ($parts as $part) {
                $partLower = strtolower($part);
                $q->orWhereRaw('LOWER(title) LIKE ?', ["%{$partLower}%"])
                  ->orWhereRaw('LOWER(description) LIKE ?', ["%{$partLower}%"])
                  ->orWhereRaw('LOWER(city) LIKE ?', ["%{$partLower}%"])
                  ->orWhereRaw('LOWER(district) LIKE ?', ["%{$partLower}%"])
                  ->orWhereRaw('LOWER(ward) LIKE ?', ["%{$partLower}%"]);
            }
        });
    }

    // --- Lọc theo giá (bỏ nhân *1000000 vì value đã là đồng) ---
    if ($request->filled('price')) {
        $raw = preg_replace('/[^0-9\-]/', '', $request->price);
        if (!preg_match('/^\d+-\d+$/', $raw)) {
            \Log::warning('INVALID PRICE FORMAT:', ['input' => $request->price]);
        } else {
            $parts = explode('-', $raw);
            $numbers = array_map('intval', array_filter($parts, function($value) {
                return trim($value) !== '';
            }));

            if (count($numbers) >= 2) {
                $min = $numbers[0]; // Không nhân nữa
                $max = $numbers[1]; // Không nhân nữa

                if ($min > $max) {
                    [$min, $max] = [$max, $min];
                }

                if ($min < 0 || $max < 0) {
                    \Log::warning('NEGATIVE PRICE DETECTED:', ['min' => $min, 'max' => $max]);
                } else {
                    \Log::info('PRICE FILTER APPLIED:', [
                        'min' => $min,
                        'max' => $max,
                        'min_formatted' => number_format($min) . ' VND',
                        'max_formatted' => number_format($max) . ' VND'
                    ]);

                    $query->whereBetween('price', [$min, $max]);
                }
            } else {
                \Log::warning('PRICE FILTER SKIPPED - Not enough numbers:', [
                    'numbers' => $numbers,
                    'count' => count($numbers)
                ]);
            }
        }
    }

    // --- Lọc theo diện tích ---
    if ($request->filled('area')) {
        [$min, $max] = explode('-', $request->area);
        $min = (int) $min;
        $max = (int) $max;
        \Log::info('AREA FILTER APPLIED:', ['min' => $min, 'max' => $max]);
        $query->whereBetween('area', [$min, $max]);
    }

    // --- Lọc theo danh mục ---
    if ($request->filled('category_id')) {
        \Log::info('CATEGORY FILTER APPLIED:', ['category_id' => $request->category_id]);
        $query->where('category_id', $request->category_id);
    }

    // --- Lọc theo đặc điểm (amenities) ---
   if ($request->filled('amenities')) {
    $amenities = array_filter($request->input('amenities', []));

    if (!empty($amenities)) {
        $query->whereHas('features', function ($q) use ($amenities) {
            $q->whereIn('name', $amenities);
        });
    }
    }
    // Debug SQL query (không dùng echo, dùng Log để tránh hỏng output)
    \Log::info('FINAL SQL QUERY:', [
        'sql' => $query->toSql(),
        'bindings' => $query->getBindings()
    ]);

    // Lấy kết quả với paginate và sắp xếp latest
    $posts = $query->latest()->paginate(10);

    return view('home.search', [
        'posts' => $posts,
        'keyword' => $keyword,
    ]);
}

    /** =========================
     *        RENTER
     *  ========================= */
    public function renter(Request $request)
    {
        $rooms = Room::latest()->paginate(6);

        $allPosts = StaffPost::with(['category', 'features', 'property'])
            ->where('status', 1)
            ->where('is_public', true)
            ->orderByDesc('approved_at')
            ->get();

        $grouped = $allPosts->groupBy('property_id');

        $topPosts = $grouped->flatMap(function ($group) {
            return $group->take(2);
        });

        $remainingPosts = $allPosts->diff($topPosts);
        $orderedPosts = $topPosts->merge($remainingPosts);

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 6;
        $pagedPosts = new LengthAwarePaginator(
            $orderedPosts->forPage($currentPage, $perPage),
            $orderedPosts->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );
        return view('home.render', [
            'posts' => $pagedPosts,
            'rooms' => $rooms,
        ]);
    }

    /** =========================
     *        FAVORITES
     *  ========================= */
    public function favorites()
    {
        $favorites = Auth::user()->favorites()->get();
        return view('home.favourite', compact('favorites'));
    }

    public function toggleFavorite(Property $property)
    {
        $user = Auth::user();

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

    /** =========================
     *        AGREEMENT
     *  ========================= */
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
    }

    /** =========================
     *        CREATE USER
     *  ========================= */
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

        RoomUser::create($validated);

        return redirect()->back()->with('success', 'Đăng ký thành công! Chúng tôi sẽ liên hệ với bạn sớm nhất có thể.');
    }

    /** =========================
     *        MY ROOM
     *  ========================= */
    public function myRoom()
    {
        $user = Auth::user();
        $rooms = $user->rentedRooms()->with('property', 'photos')->paginate(6);
        return view('home.my-room', compact('rooms'));
    }

    /** =========================
     *        STOP RENT
     *  ========================= */
    public function stopRentForm()
    {
        $user = auth()->user();

        $rentalAgreement = RentalAgreement::where('renter_id', $user->id)
            ->with('room')
            ->latest()
            ->first();

        if (!$rentalAgreement || !$rentalAgreement->room) {
            return view('home.roomleave.stopRentForm', ['roomUsers' => collect()]);
        }

        $roomUsers = RentalAgreement::with('renter')
            ->where('room_id', $rentalAgreement->room->room_id)
            ->where('is_active', true)
            ->get();

        return view('home.roomleave.stopRentForm', compact('roomUsers'));
    }

    public function stopUserRental(Request $request, $id)
    {
        $request->validate([
            'leave_date' => 'required|date|after:today',
        ]);

        $user = auth()->user();
        $agreement = RentalAgreement::with('room')->findOrFail($id);

        $currentAgreement = $user->rentalAgreements()->latest()->first();
        if (!$currentAgreement || $agreement->room_id !== $currentAgreement->room_id) {
            abort(403, 'Bạn không có quyền gửi yêu cầu cho hợp đồng này.');
        }

        $agreement->leave_date = $request->leave_date;
        $agreement->status = 'pending';
        $agreement->stop_requested = true;
        $agreement->save();

        return redirect()->back()->with('success', '📝 Yêu cầu dừng thuê đã được gửi và đang chờ duyệt.');
    }
}
