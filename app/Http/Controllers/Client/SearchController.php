<?php

namespace App\Http\Controllers\Client;

use App\Models\StaffPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;

class SearchController extends Controller
{
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

            // Lấy danh sách quận/huyện
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
                $wardsResponse = Http::get("https://provinces.open-api.vn/api/d/{$districtData['code']}?depth=2");
                if ($wardsResponse->successful()) {
                    $wards = collect($wardsResponse->json()['wards'] ?? [])->pluck('name')->sort()->values();
                    \Log::info("Danh sách phường/xã từ API chính (depth=2):", ['wards' => $wards->toArray()]);

                    // Nếu danh sách phường rỗng, thử lại với depth=2 (không cần depth=3)
                    if ($wards->isEmpty()) {
                        \Log::info("Danh sách phường rỗng, thử lại với depth=2");
                        $retryResponse = Http::get("https://provinces.open-api.vn/api/d/{$districtData['code']}?depth=2");
                        if ($retryResponse->successful()) {
                            $wards = collect($retryResponse->json()['wards'] ?? [])->pluck('name')->sort()->values();
                            \Log::info("Danh sách phường/xã từ API retry (depth=2):", ['wards' => $wards->toArray()]);
                        }
                    }

                    // Nếu vẫn rỗng, thử API dự phòng
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
                return response()->json([], 200);
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
    private function getWardsFromFallbackApi($districtCode)
    {
        // Implement fallback logic if needed
        return collect([]);
    }

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
}
