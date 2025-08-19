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
    public function index(Request $request)
    {
        $cities = $this->getCitiesFromApi();
        $posts = $this->filterPosts($request)->latest()->paginate(10);
        return view('home.render', compact('posts', 'cities'));
    }

    /**
     * Xử lý tìm kiếm
     */
    public function search(Request $request)
    {
        $posts = $this->filterPosts($request)->latest()->paginate(10);
        $cities = $this->getCitiesFromApi();
        return view('home.render', compact('posts', 'cities'));
    }

    /**
     * Lọc bài đăng dựa trên input tìm kiếm
     */
    private function filterPosts(Request $request)
    {
        $query = StaffPost::query();

        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }
        if ($request->filled('district')) {
            $query->where('district', $request->district);
        }
        if ($request->filled('ward')) {
            $query->where('ward', $request->ward);
        }
        return $query;
    }

    /**
     * Lấy danh sách tỉnh/thành từ API
     */
    private function getCitiesFromApi()
    {
        return Cache::remember('cities', now()->addHours(24), function () {
            try {
                $response = Http::get('https://provinces.open-api.vn/api/p/');
                if ($response->successful()) {
                    $cities = collect($response->json())->pluck('name')->sort()->values();
                    \Log::info("Danh sách tỉnh/thành từ API:", ['cities' => $cities->toArray()]);
                    return $cities;
                }
                \Log::warning("API tỉnh/thành trả về không thành công: " . $response->status());
                return collect([]);
            } catch (\Exception $e) {
                \Log::error("Lỗi khi gọi API tỉnh/thành: " . $e->getMessage());
                return collect([]);
            }
        });
    }

    /**
     * Lấy danh sách quận/huyện dựa trên tỉnh/thành
     */
    public function getDistricts($city)
    {
        try {
            \Log::info("Yêu cầu lấy quận/huyện cho tỉnh/thành: " . $city);

            // Chuẩn hóa tên tỉnh/thành
            $normalizedCity = $this->normalizeCityName($city);
            \Log::info("Tên tỉnh/thành sau chuẩn hóa: " . $normalizedCity);

            // Thử tìm mã tỉnh/thành bằng API search
            $response = Http::get('https://provinces.open-api.vn/api/p/search/', [
                'q' => $normalizedCity
            ]);
            $cityData = null;
            if ($response->successful()) {
                $cityData = collect($response->json())->firstWhere('name', $normalizedCity);
                \Log::info("Kết quả tìm tỉnh/thành qua API search:", ['cityData' => $cityData]);
            } else {
                \Log::warning("API tìm tỉnh/thành trả về không thành công: " . $response->status());
            }

            // Nếu API search thất bại, tra cứu trực tiếp từ danh sách tỉnh/thành
            if (!$cityData || !isset($cityData['code'])) {
                \Log::info("Thử tra cứu tỉnh/thành trực tiếp từ API /p/");
                $response = Http::get('https://provinces.open-api.vn/api/p/');
                if ($response->successful()) {
                    $cityData = collect($response->json())->firstWhere('name', $normalizedCity);
                    \Log::info("Kết quả tra cứu trực tiếp:", ['cityData' => $cityData]);
                }
            }

            if (!$cityData || !isset($cityData['code'])) {
                \Log::warning("Không tìm thấy mã tỉnh/thành cho: " . $normalizedCity);
                return response()->json([], 404);
            }

            // Lấy danh sách quận/huyện (sử dụng depth=2 vì depth=3 có thể không cần thiết)
            $districtsResponse = Http::get("https://provinces.open-api.vn/api/p/{$cityData['code']}?depth=2");
            if ($districtsResponse->successful()) {
                $districts = collect($districtsResponse->json()['districts'])->pluck('name')->sort()->values();
                \Log::info("Danh sách quận/huyện:", ['districts' => $districts->toArray()]);
                if ($districts->isEmpty()) {
                    \Log::warning("Không có quận/huyện cho tỉnh/thành: " . $normalizedCity);
                    return response()->json([], 404);
                }
                return response()->json($districts);
            }
            \Log::warning("API quận/huyện trả về không thành công: " . $districtsResponse->status());
            return response()->json([], 404);
        } catch (\Exception $e) {
            \Log::error("Lỗi khi lấy quận/huyện cho thành phố {$city}: " . $e->getMessage());
            return response()->json([], 500);
        }
    }

   /**
 * Lấy danh sách phường/xã dựa trên quận/huyện
 */
public function getWards($district)
{
    try {
        \Log::info("Yêu cầu lấy phường/xã cho quận/huyện: " . $district);

        // Chuẩn hóa tên quận/huyện
        $normalizedDistrict = $this->normalizeDistrictName($district);
        \Log::info("Tên quận/huyện sau chuẩn hóa: " . $normalizedDistrict);

        // Tìm mã quận/huyện bằng API search
        $districtData = null;
        $response = Http::get('https://provinces.open-api.vn/api/d/search/', [
            'q' => $normalizedDistrict
        ]);
        if ($response->successful()) {
            $districtData = collect($response->json())->firstWhere('name', $normalizedDistrict);
            \Log::info("Kết quả tìm quận/huyện qua API search:", ['districtData' => $districtData]);
        } else {
            \Log::warning("API tìm quận/huyện thất bại: " . $response->status() . " - " . $response->body());
        }

        // Nếu không tìm thấy qua search, thử tra cứu tất cả quận/huyện
        if (!$districtData || !isset($districtData['code'])) {
            \Log::info("Thử tra cứu quận/huyện trực tiếp từ API /d/");
            $response = Http::get('https://provinces.open-api.vn/api/d/');
            if ($response->successful()) {
                $districtData = collect($response->json())->firstWhere('name', $normalizedDistrict);
                \Log::info("Kết quả tra cứu quận/huyện trực tiếp:", ['districtData' => $districtData]);
            } else {
                \Log::warning("API danh sách quận/huyện thất bại: " . $response->status() . " - " . $response->body());
            }
        }

        // Nếu vẫn không tìm thấy mã quận/huyện
        if (!$districtData || !isset($districtData['code'])) {
            \Log::warning("Không tìm thấy mã quận/huyện cho: " . $normalizedDistrict);
            return response()->json(['error' => 'Không tìm thấy quận/huyện'], 404);
        }

        // Lấy danh sách phường/xã từ cache hoặc API
        $cacheKey = "wards_{$districtData['code']}";
        $wards = Cache::remember($cacheKey, now()->addHours(24), function () use ($districtData, $normalizedDistrict) {
            \Log::info("Gọi API phường/xã cho mã quận/huyện: " . $districtData['code']);
            // Thử với depth=2 trước
            $wardsResponse = Http::get("https://provinces.open-api.vn/api/d/{$districtData['code']}?depth=2");
            if ($wardsResponse->successful()) {
                $wards = collect($wardsResponse->json()['wards'] ?? [])->pluck('name')->sort()->values();
                \Log::info("Danh sách phường/xã từ API chính (depth=2):", ['wards' => $wards->toArray()]);

                // Nếu danh sách phường rỗng, thử lại với depth=3 (bỏ điều kiện division_type sai)
                if ($wards->isEmpty()) {
                    \Log::info("Danh sách phường rỗng, thử lại với depth=3");
                    $retryResponse = Http::get("https://provinces.open-api.vn/api/d/{$districtData['code']}?depth=2");
                    if ($retryResponse->successful()) {
                        $wards = collect($retryResponse->json()['wards'] ?? [])->pluck('name')->sort()->values();
                        \Log::info("Danh sách phường/xã từ API retry (depth=3):", ['wards' => $wards->toArray()]);
                    }
                }

                // Nếu vẫn rỗng, thử API dự phòng (bao gồm kiểm tra mã 19)
                if ($wards->isEmpty()) {
                    \Log::info("Danh sách phường vẫn rỗng, thử API dự phòng cho: " . $normalizedDistrict);
                    $wards = $this->getWardsFromFallbackApi($districtData['code']);
                }
                return $wards;
            }
            \Log::warning("API phường/xã thất bại: " . $wardsResponse->status() . " - " . $wardsResponse->body());
            return $this->getWardsFromFallbackApi($districtData['code']);
        });

        if ($wards->isEmpty()) {
            \Log::info("Không có phường/xã cho quận/huyện: " . $normalizedDistrict);
            return response()->json([], 200); // Trả về mảng rỗng với mã 200
        }

        return response()->json($wards);
    } catch (\Exception $e) {
        \Log::error("Lỗi khi lấy phường/xã cho quận/huyện {$district}: " . $e->getMessage() . " - Stack trace: " . $e->getTraceAsString());
        return response()->json(['error' => 'Không thể tải danh sách phường/xã'], 500);
    }
}

    /**
     * API dự phòng để lấy danh sách phường/xã
     */

    /**
     * Chuẩn hóa tên tỉnh/thành để khớp với API
     */
    private function normalizeCityName($city)
    {
        $cityMap = [
            'Hà Nội' => 'Thành phố Hà Nội',
            'Hồ Chí Minh' => 'Thành phố Hồ Chí Minh',
            'Đà Nẵng' => 'Thành phố Đà Nẵng',
            'Hải Phòng' => 'Thành phố Hải Phòng',
            'Cần Thơ' => 'Thành phố Cần Thơ',
        ];
        return $cityMap[$city] ?? $city;
    }

    /**
     * Chuẩn hóa tên quận/huyện để khớp với API
     */
    private function normalizeDistrictName($district)
    {
        $districtMap = [
            'Nam Từ Liêm' => 'Quận Nam Từ Liêm',
            'Ba Vì' => 'Huyện Ba Vì',
            'Hoàn Kiếm' => 'Quận Hoàn Kiếm',
            'Cầu Giấy' => 'Quận Cầu Giấy',
            // Thêm các ánh xạ khác nếu cần
        ];
        return $districtMap[$district] ?? $district;
    }

    /**
     * Gợi ý bài viết gần vị trí người dùng
     */
    public function suggestNearby(Request $request)
    {
        $lat = $request->input('lat');
        $lng = $request->input('lng');

        try {
            $posts = StaffPost::selectRaw("*,
                (6371 * acos(cos(radians(?)) * cos(radians(lat)) * cos(radians(lng) - radians(?)) + sin(radians(?)) * sin(radians(lat)))) AS distance",
                [$lat, $lng, $lat])
                ->having('distance', '<', 10)
                ->orderBy('distance')
                ->take(6)
                ->get();

            return response()->json($posts);
        } catch (\Exception $e) {
            \Log::error("Lỗi khi lấy bài viết gần bạn: " . $e->getMessage());
            return response()->json([]);
        }
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
        $cities = $this->getCitiesFromApi();
        $posts = $this->filterPosts($request)->latest()->paginate(10);
        return view('home.render', [
            'posts' => $pagedPosts,
            'rooms' => $rooms,
            'cities' => $cities,
            'posts' => $posts,
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
