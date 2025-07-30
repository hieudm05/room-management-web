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
        $month = $request->input('month');
        $quarter = $request->input('quarter');
        $year = $request->input('year');
        $compareA = $request->input('compareA');
        $compareB = $request->input('compareB');

        // Lấy tất cả bất động sản với quan hệ rooms, bills, complaints
        $query = Property::with(['rooms', 'rooms.bills', 'rooms.complaints']);

        // Lọc theo tháng, quý, năm dựa trên bills
        if ($month) {
            $query->whereHas('rooms.bills', function ($q) use ($month) {
                $q->whereMonth('created_at', substr($month, 5, 2))
                  ->whereYear('created_at', substr($month, 0, 4));
            });
        }

        if ($quarter && $year) {
            $monthRanges = [
                1 => [1, 3],
                2 => [4, 6],
                3 => [7, 9],
                4 => [10, 12]
            ];
            $query->whereHas('rooms.bills', function ($q) use ($monthRanges, $quarter, $year) {
                $q->whereBetween(\DB::raw('MONTH(created_at)'), $monthRanges[$quarter])
                  ->whereYear('created_at', $year);
            });
        } elseif ($year) {
            $query->whereHas('rooms.bills', function ($q) use ($year) {
                $q->whereYear('created_at', $year);
            });
        }

        // Lọc theo tên tòa nhà nếu có compareA hoặc compareB
        if ($compareA || $compareB) {
            $query->whereIn('name', array_filter([$compareA, $compareB]));
        }

        $properties = $query->get();

        $propertyStats = $properties->map(function ($property) {
            $total_rooms = $property->rooms->count();
            $rented_rooms = $property->rooms->where('status', 'Rented')->count();
            $empty_rooms = $total_rooms - $rented_rooms;

            $electric_cost = $property->rooms->flatMap->bills->sum('electric_total');
            $water_cost = $property->rooms->flatMap->bills->sum('water_total');
            $other_cost = $property->rooms
                ->flatMap->bills
                ->flatMap->additionalFees
                ->sum('total')
                + $property->rooms->flatMap->bills->sum('complaint_landlord_cost');

            $complaints = $property->rooms->flatMap->complaints->count();
            $revenue = $property->rooms->flatMap->bills->sum('total');
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