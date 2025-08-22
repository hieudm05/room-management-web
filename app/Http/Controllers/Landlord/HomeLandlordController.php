<?php
namespace App\Http\Controllers\LandLord;

use App\Http\Controllers\Controller;
use App\Models\LandLord\Property;
use App\Models\Landlord\Staff\Rooms\RoomBill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class HomeLandLordController extends Controller
{
    public function index()
    {
        // Lấy danh sách tất cả tòa nhà
        $properties = Property::all();

        // Tính toán thống kê tổng hợp
        $total_rooms = Property::with('rooms')->get()->sum(function ($property) {
            return $property->rooms->count();
        });
        $total_rented = Property::with('rooms')->get()->sum(function ($property) {
            return $property->rooms->where('status', 'Rented')->count();
        });
        $total_empty = $total_rooms - $total_rented;
        $total_revenue = Property::with(['rooms.bills'])->get()->sum(function ($property) {
            $bills = $property->rooms->flatMap->bills;
            $revenue = $bills->sum('total');
            Log::debug("Revenue for property {$property->name}: {$revenue}"); // Log để debug
            return $revenue;
        });

        $total_complaints = Property::with(['rooms.complaints'])->get()->sum(function ($property) {
            return $property->rooms->flatMap->complaints->count();
        });

        // Tính $propertyStats (mặc định 5 tòa nhà)
        $propertyStats = Property::with(['rooms', 'rooms.bills', 'rooms.complaints'])
            ->take(5)
            ->get()
            ->map(function ($property) {
                $rooms = $property->rooms;
                $total_rooms = $rooms->count();
                $rented_rooms = $rooms->where('status', 'Rented')->count();
                $empty_rooms = $total_rooms - $rented_rooms;
                $bills = $rooms->flatMap->bills;
                $revenue = $bills->sum('total');
                $electric_cost = $bills->sum('electric_total');
                $water_cost = $bills->sum('water_total');
                $other_cost = $bills->sum('other_total');

                Log::debug("Property {$property->name}: revenue = {$revenue}"); // Log để debug

                return [
                    'name' => $property->name,
                    'revenue' => $revenue ?? 0, // Đảm bảo không trả về null
                    'total_rooms' => $total_rooms,
                    'rented_rooms' => $rented_rooms,
                    'empty_rooms' => $empty_rooms,
                    'electric_cost' => $electric_cost ?? 0,
                    'water_cost' => $water_cost ?? 0,
                    'other_cost' => $other_cost ?? 0,
                    'complaints' => $rooms->flatMap->complaints->count(),

                     // Thêm thu và chi để biểu đồ
                    'income' => $revenue ?? 0,
                    'expense' => ($electric_cost + $water_cost + $other_cost) ?? 0,
                ];
            })->values();

            // Lấy dữ liệu doanh thu 12 tháng
           $monthlyRevenue = collect(range(1, 12))->map(function ($month) {
                $bills = RoomBill::whereMonth('month', $month)
                    ->whereYear('month', now()->year)
                    ->get();
                return $bills->sum('total'); // chỉ lấy doanh thu
            });

            $revenueChartData = [
                'labels' => collect(range(1, 12))->map(fn($m) => 'Tháng ' . $m)->toArray(),
                'revenue' => $monthlyRevenue->toArray()
            ];

            $incomeExpenseStats = [
                'labels' => $propertyStats->pluck('name')->toArray(),
                'income' => $propertyStats->pluck('income')->toArray(),
                'expense' => $propertyStats->pluck('expense')->toArray(),
            ];

        return view('landlord.dashboard', compact(
            'properties',
            'total_rooms',
            'total_rented',
            'total_empty',
            'total_revenue',
            'total_complaints',
            'propertyStats',
            'incomeExpenseStats',
            'revenueChartData'
        ));
    }

    public function filterStats(Request $request)
    {
        Cache::forget('property_stats_' . md5(serialize($request->all())));
        $month = $request->input('month'); // e.g., "2024-07"
        $quarter = $request->input('quarter'); // e.g., "1", "2", "3", "4"
        $year = $request->input('year', now()->year); // Mặc định năm hiện tại
        $selected_properties = $request->input('properties', []);
        $compareA = $request->input('compareA');
        $compareB = $request->input('compareB');

        // Tạo cache key
        $cacheKey = 'property_stats_' . md5(serialize($request->all()));

        $data = Cache::remember($cacheKey, now()->addMinutes(60), function () use ($month, $quarter, $year, $selected_properties, $compareA, $compareB) {
            $query = Property::with(['rooms', 'rooms.bills', 'rooms.complaints']);

            // Lọc theo tòa nhà
            if (!empty($selected_properties)) {
                $query->whereIn('name', $selected_properties);
            } elseif ($compareA || $compareB) {
                $query->whereIn('name', array_filter([$compareA, $compareB]));
            } else {
                $query->take(5); // Mặc định 5 tòa nhà
            }

            $properties = $query->get();

            if ($properties->isEmpty()) {
                Log::warning("No properties found for filter: ", [
                    'month' => $month,
                    'quarter' => $quarter,
                    'year' => $year,
                    'properties' => $selected_properties,
                    'compareA' => $compareA,
                    'compareB' => $compareB
                ]);
            }

            $propertyStats = $properties->map(function ($property) use ($month, $quarter, $year) {
                $rooms = $property->rooms;
                $total_rooms = $rooms->count();
                $rented_rooms = $rooms->where('status', 'Rented')->count();
                $empty_rooms = $total_rooms - $rented_rooms;

                // Lọc bills theo tháng/quý/năm
                $bills = $rooms->flatMap->bills->filter(function ($bill) use ($month, $quarter, $year) {
                    $billMonth = substr($bill->month, 0, 7);
                    $billYear = substr($bill->month, 0, 4);
                    $billQuarter = ceil((int)substr($bill->month, 5, 2) / 3);

                    return (
                        (!$month || $billMonth === $month) &&
                        (!$quarter || $billQuarter == $quarter) &&
                        (!$year || $billYear == $year)
                    );
                });

                $revenue = $bills->sum('total');
                $electric_cost = $bills->sum('electric_total');
                $water_cost = $bills->sum('water_total');
                $other_cost = $bills->sum('other_total');

                return [
                    'name' => $property->name,
                    'revenue' => $revenue ?? 0,
                    'total_rooms' => $total_rooms,
                    'rented_rooms' => $rented_rooms,
                    'empty_rooms' => $empty_rooms,
                    'electric_cost' => $electric_cost ?? 0,
                    'water_cost' => $water_cost ?? 0,
                    'other_cost' => $other_cost ?? 0,
                    'complaints' => $rooms->flatMap->complaints->where('month', $month)->count(),
                    'income' => $revenue ?? 0,
                    'expense' => ($electric_cost + $water_cost + $other_cost) ?? 0,
                ];
            })->values();

           $incomeExpenseStats = [
                'labels' => $propertyStats->pluck('name')->toArray(),
                'income' => $propertyStats->pluck('income')->toArray(),
                'expense' => $propertyStats->pluck('expense')->toArray(),
            ];

            // Tính tổng hợp
            $summary = [
                'total_rooms' => $propertyStats->sum('total_rooms'),
                'total_rented' => $propertyStats->sum('rented_rooms'),
                'total_empty' => $propertyStats->sum('empty_rooms'),
                'total_revenue' => $propertyStats->sum('revenue'),
                'total_complaints' => $propertyStats->sum('complaints'),
            ];

            return [
                'propertyStats' => $propertyStats,
                'summary' => $summary,
                'incomeExpenseStats' => $incomeExpenseStats,
                'revenueChartData' => [
                'labels' => collect(range(1, 12))->map(fn($m) => 'Tháng ' . $m)->toArray(),
                'revenue' => collect(range(1, 12))->map(function ($month) {
                    $bills = RoomBill::whereMonth('month', $month)
                        ->whereYear('month', now()->year)
                        ->get();
                    $revenue = $bills->sum('total');
                    return $revenue;
                })->toArray()
            ]
            ];
        });

        return response()->json($data);
    }
}
