<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Landlord\Property;
use Illuminate\Http\Request;

class HomeLandlordController extends Controller
{
    public function index()
    {
        // Lấy tất cả các bất động sản mà chủ trọ đang quản lý
        $properties = Property::with(['rooms', 'rooms.bills', 'rooms.complaints'])->get();

        $propertyStats = $properties->map(function ($property) {
            $total_rooms = $property->rooms->count();
            $rented_rooms = $property->rooms->where('status', 'Rented')->count();
            $empty_rooms = $total_rooms - $rented_rooms;

            // Tổng chi phí điện, nước, phát sinh
            $electric_cost = $property->rooms->flatMap->bills->sum('electric_total');
            $water_cost = $property->rooms->flatMap->bills->sum('water_total');
            $other_cost = $property->rooms
                ->flatMap->bills
                ->flatMap->additionalFees
                ->sum('total')
                + $property->rooms->flatMap->bills->sum('complaint_landlord_cost');

            // Số khiếu nại
            $complaints = $property->rooms->flatMap->complaints->count();

            // Doanh thu: tổng tiền từ bills
            $revenue = $property->rooms
            ->flatMap->bills
             ->filter(fn($b) => $b->status === 'paid')
            ->sum('total');


            // Lợi nhuận
            $profit = $revenue - ($electric_cost + $water_cost + $other_cost);

            return [
                'name' => $property->name ?? 'Unknown',
                'total_rooms' => $total_rooms,
                'rented_rooms' => $rented_rooms,
                'empty_rooms' => $empty_rooms,
                'electric_cost' => $electric_cost,
                'water_cost' => $water_cost,
                'other_cost' => $other_cost,
                'complaints' => $complaints,
                'revenue' => $revenue,
                'profit' => $profit,
            ];
        });

        // Tổng hợp toàn hệ thống
        $total_rooms = $propertyStats->sum('total_rooms');
        $total_rented = $propertyStats->sum('rented_rooms');
        $total_empty = $propertyStats->sum('empty_rooms');
        $total_electric = $propertyStats->sum('electric_cost');
        $total_water = $propertyStats->sum('water_cost');
        $total_other = $propertyStats->sum('other_cost');
        $total_complaints = $propertyStats->sum('complaints');
        $total_revenue = $propertyStats->sum('revenue');
        $total_profit = $propertyStats->sum('profit');

        return view('landlord.dashboard', compact(
            'properties',
            'propertyStats',
            'total_rooms',
            'total_rented',
            'total_empty',
            'total_electric',
            'total_water',
            'total_other',
            'total_complaints',
            'total_revenue',
            'total_profit'
        ));
    }

 public function filterStats(Request $request)
{
    $month = $request->input('month');       // dạng: "2024-07"
    $quarter = $request->input('quarter');   // 1–4
    $year = $request->input('year');         // 2024
    $compareA = $request->input('compareA');
    $compareB = $request->input('compareB');

    $query = Property::with(['rooms', 'rooms.bills', 'rooms.complaints']);

    if ($compareA || $compareB) {
        $query->whereIn('name', array_filter([$compareA, $compareB]));
    }

    $properties = $query->get();

    $propertyStats = $properties->map(function ($property) use ($month, $quarter, $year) {
        $allRooms = $property->rooms;

        // === Bước 1: xác định thời điểm cắt (cutoff) ===
        if ($month) {
            $cutoff = \Carbon\Carbon::createFromFormat('Y-m', $month)->endOfMonth();
        } elseif ($quarter && $year) {
            $endMonth = $quarter * 3;
            $cutoff = \Carbon\Carbon::create($year, $endMonth, 1)->endOfMonth();
        } elseif ($year) {
            $cutoff = \Carbon\Carbon::create($year, 12, 31)->endOfDay();
        } else {
            $cutoff = null;
        }

        // === Bước 2: lọc những phòng đã tồn tại trước thời điểm cutoff ===
        if ($cutoff) {
            $rooms = $allRooms->filter(function ($room) use ($cutoff) {
                return $room->created_at <= $cutoff;
            });
        } else {
            $rooms = $allRooms;
        }

        $total_rooms = $rooms->count();
        $rented_rooms = $rooms->where('status', 'Rented')->count();
        $empty_rooms = $total_rooms - $rented_rooms;

        // === Bước 3: lấy bills từ những phòng hợp lệ ===
        $bills = $rooms->flatMap->bills;

        // Lọc bills theo thời gian
        if ($month) {
            $bills = $bills->filter(function ($b) use ($month) {
                return $b->created_at->format('Y-m') === $month;
            });
        } elseif ($quarter && $year) {
            $startMonth = ($quarter - 1) * 3 + 1;
            $endMonth = $startMonth + 2;
            $bills = $bills->filter(function ($b) use ($startMonth, $endMonth, $year) {
                return $b->created_at->year == $year &&
                       $b->created_at->month >= $startMonth &&
                       $b->created_at->month <= $endMonth;
            });
        } elseif ($year) {
            $bills = $bills->filter(function ($b) use ($year) {
                return $b->created_at->year == $year;
            });
        }

        // === Bước 4: tính các giá trị ===
        $electric_cost = $bills->sum('electric_total');
        $water_cost = $bills->sum('water_total');
        $other_cost = $bills->flatMap->additionalFees->sum('total') + $bills->sum('complaint_landlord_cost');
        $revenue = $bills->where('status', 'paid')->sum('total');
        $profit = $revenue - ($electric_cost + $water_cost + $other_cost);
        $complaints = $rooms->flatMap->complaints->count();

        return [
            'name' => $property->name ?? 'Unknown',
            'total_rooms' => $total_rooms,
            'rented_rooms' => $rented_rooms,
            'empty_rooms' => $empty_rooms,
            'electric_cost' => $electric_cost,
            'water_cost' => $water_cost,
            'other_cost' => $other_cost,
            'complaints' => $complaints,
            'revenue' => $revenue,
            'profit' => $profit,
        ];
    });

    // Tổng hợp toàn hệ thống
    $total_rooms = $propertyStats->sum('total_rooms');
    $total_rented = $propertyStats->sum('rented_rooms');
    $total_empty = $propertyStats->sum('empty_rooms');
    $total_electric = $propertyStats->sum('electric_cost');
    $total_water = $propertyStats->sum('water_cost');
    $total_other = $propertyStats->sum('other_cost');
    $total_complaints = $propertyStats->sum('complaints');
    $total_revenue = $propertyStats->sum('revenue');
    $total_profit = $propertyStats->sum('profit');

    return response()->json([
        'propertyStats' => $propertyStats,
        'summary' => [
            'total_rooms' => $total_rooms,
            'total_rented' => $total_rented,
            'total_empty' => $total_empty,
            'total_electric' => $total_electric,
            'total_water' => $total_water,
            'total_other' => $total_other,
            'total_complaints' => $total_complaints,
            'total_revenue' => $total_revenue,
            'total_profit' => $total_profit,
        ]
    ]);
}


}
?>