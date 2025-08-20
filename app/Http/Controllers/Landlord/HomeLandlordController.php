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
    // Lấy keyword và chuẩn hóa
    $keyword = trim(preg_replace('/\s+/', ' ', $request->input('keyword')));

    // Tách theo dấu phẩy để người dùng có thể nhập nhiều phần
    $parts = array_map('trim', explode(',', $keyword));

    $posts = StaffPost::query()
        ->when($parts, function ($query) use ($parts) {
            foreach ($parts as $part) {
                $query->where(function ($sub) use ($part) {
                    $sub->orWhere('city', 'like', "%{$part}%")
                        ->orWhere('district', 'like', "%{$part}%")
                        ->orWhere('ward', 'like', "%{$part}%")
                        ->orWhere('title', 'like', "%{$part}%")
                        ->orWhere('description', 'like', "%{$part}%");
                });
            }
        })
        ->latest()
        ->paginate(10);

    return view('home.search', [
        'posts' => $posts,
        'keyword' => $keyword,
    ]);
}    /**
     * Lọc bài đăng dựa trên input tìm kiếm
     */
   private function filterPosts(Request $request)
    {
        $query = StaffPost::query();

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $keywordNormalized = $this->removeVietnameseAccents(strtolower($keyword));
            $query->where(function($q) use ($keyword, $keywordNormalized) {
                $q->whereRaw('LOWER(title) LIKE ?', ["%{$keyword}%"])
                  ->orWhereRaw('LOWER(description) LIKE ?', ["%{$keyword}%"])
                  ->orWhereRaw('LOWER(city) LIKE ?', ["%{$keyword}%"])
                  ->orWhereRaw('LOWER(district) LIKE ?', ["%{$keyword}%"])
                  ->orWhereRaw('LOWER(ward) LIKE ?', ["%{$keyword}%"]);
            });
        }

        return $query;
    }

// public function apiSuggestions(Request $request)
// {
//     $query = trim($request->input('query'));

//     try {
//         // Lấy dữ liệu từ cache, ưu tiên data.json đầy đủ phường/xã
//         $locations = Cache::remember('vn_locations_full', 86400, function() {
//             $endpoints = [
//                 'https://raw.githubusercontent.com/kenzouno1/DiaGioiHanhChinhVN/master/data.json', // full
//                 'https://provinces.open-api.vn/api/?depth=3'
//             ];

//             foreach ($endpoints as $endpoint) {
//                 try {
//                     \Log::info("Trying endpoint: " . $endpoint);
//                     $response = Http::timeout(30)->get($endpoint);

//                     if ($response->successful()) {
//                         $data = $response->json();
//                         if (!empty($data)) {
//                             \Log::info("Success with endpoint: " . $endpoint);
//                             return $data;
//                         }
//                     }
//                 } catch (\Exception $e) {
//                     \Log::error("Failed endpoint {$endpoint}: " . $e->getMessage());
//                     continue;
//                 }
//             }

//             \Log::error("All API endpoints failed");
//             return [];
//         });

//         if (empty($locations)) {
//             \Log::error("No location data available");
//             return response()->json([]);
//         }

//         $suggestions = [];

//         foreach ($locations as $province) {
//             // Tên tỉnh
//             $provinceName = $province['name'] ?? $province['Name'] ?? '';
//             if (!empty($provinceName)) $suggestions[] = $provinceName;

//             $districts = $province['districts'] ?? $province['Districts'] ?? $province['district'] ?? [];
//             foreach ($districts as $district) {
//                 // Tên huyện/quận
//                 $districtName = $district['name'] ?? $district['Name'] ?? '';
//                 if (!empty($districtName)) $suggestions[] = $districtName;

//                 // Lấy phường/xã
//                 $wards = [];
//                 if (!empty($district['wards']) && is_array($district['wards'])) $wards = $district['wards'];
//                 elseif (!empty($district['xas']) && is_array($district['xas'])) $wards = $district['xas'];
//                 elseif (!empty($district['communes']) && is_array($district['communes'])) $wards = $district['communes'];
//                 elseif (!empty($district['xaPhuong']) && is_array($district['xaPhuong'])) $wards = $district['xaPhuong'];

//                 foreach ($wards as $ward) {
//                     $wardName = $ward['name'] ?? $ward['Name'] ?? $ward['title'] ?? '';
//                     if (!empty($wardName)) $suggestions[] = $wardName;
//                 }
//             }
//             // \Log::info('Sample ward:', $wards[0] ?? 'empty');
//         }

//         // Loại bỏ trùng rỗng
//         $suggestions = array_values(array_unique(array_filter($suggestions)));

//         // Nếu có query, filter và sort
//         if ($query) {
//             $queryNormalized = $this->removeVietnameseAccents(strtolower($query));

//             $filteredSuggestions = array_filter($suggestions, function($item) use ($queryNormalized) {
//                 $itemNormalized = $this->removeVietnameseAccents(strtolower($item));
//                 return stripos($itemNormalized, $queryNormalized) !== false;
//             });

//             // Sort theo vị trí query xuất hiện, rồi độ dài
//             usort($filteredSuggestions, function($a, $b) use ($queryNormalized) {
//                 $aNormalized = $this->removeVietnameseAccents(strtolower($a));
//                 $bNormalized = $this->removeVietnameseAccents(strtolower($b));

//                 $posA = stripos($aNormalized, $queryNormalized);
//                 $posB = stripos($bNormalized, $queryNormalized);

//                 if ($posA === $posB) return strlen($a) - strlen($b);
//                 return $posA - $posB;
//             });

//             $suggestions = array_slice($filteredSuggestions, 0, 10);
//         } else {
//             $suggestions = array_slice($suggestions, 0, 10);
//         }

//         return response()->json(array_values($suggestions));

//     } catch (\Exception $e) {
//         \Log::error("Lỗi gợi ý API: " . $e->getMessage());
//         return response()->json([]);
//     }
// }

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
