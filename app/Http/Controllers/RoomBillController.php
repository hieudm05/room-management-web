<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

namespace App\Http\Controllers;

use App\Models\Landlord\Staff\Rooms\RoomBill as RoomsRoomBill;
use Illuminate\Http\Request;
use App\Models\RoomBill;
use Carbon\Carbon;
use App\Models\Landlord\Room;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class RoomBillController extends Controller
{
    public function markPending(Request $request, $id)
    {
        $bill = RoomsRoomBill::findOrFail($id);

        $request->validate([
            'payment_time' => 'required|date',
            'receipt_image' => 'required|image|max:2048',
        ]);

        $path = $request->file('receipt_image')->store('receipts', 'public');

        $bill->status = 'pending';
        $bill->payment_time = $request->payment_time;
        $bill->receipt_image = $path;
        $bill->save();

        return back()->with('success', 'Thông tin thanh toán đã được gửi để xác nhận.');
    }

    public function showRoomStatistics($roomId)
    {
        $room = Room::findOrFail($roomId);


        $monthlyTotals = RoomsRoomBill::selectRaw('MONTH(`month`) as month_number, SUM(total) as total')
            ->where('room_id', $room->room_id)
            ->whereYear('month', now()->year)
            ->groupBy('month_number')
            ->orderBy('month_number')
            ->pluck('total', 'month_number');

        // Bổ sung đủ 12 tháng
        $monthlyTotals = collect(range(1, 12))->mapWithKeys(function ($m) use ($monthlyTotals) {
            return [$m => $monthlyTotals->get($m, 0)];
        });

        $quarterTotals = RoomsRoomBill::selectRaw('QUARTER(`month`) as quarter, SUM(total) as total')
            ->where('room_id', $room->room_id)
            ->whereYear('month', now()->year)
            ->groupBy('quarter')
            ->orderBy('quarter')
            ->pluck('total', 'quarter');

        // Đảm bảo có đủ 4 quý, quý nào không có sẽ là 0
        $quarterTotals = collect(range(1, 4))->mapWithKeys(function ($q) use ($quarterTotals) {
            return [$q => $quarterTotals->get($q, 0)];
        });


        return view('landlord.rooms.statistics', [
            'room' => $room,
            'monthlyTotals' => $monthlyTotals,
            'quarterTotals' => $quarterTotals,
        ]);
    }

    public function compareMonths(Request $request, Room $room)
    {
        $m1 = (int) $request->get('m1');
        $m2 = (int) $request->get('m2');

        // Lấy dữ liệu tháng 1 và tháng 2
        $data = RoomsRoomBill::where('room_id', $room->room_id)
            ->whereYear('created_at', now()->year)
            ->whereIn(DB::raw('MONTH(created_at)'), [$m1, $m2])
            ->get()
            ->groupBy(fn($item) => (int) $item->created_at->format('m'))
            ->map(fn($group) => $group->first());

        // Danh sách các chỉ số cần so sánh
        $labels = [
            'rent_price' => 'Tiền phòng',
            'electric_start' => 'Chỉ số điện đầu',
            'electric_end' => 'Chỉ số điện cuối',
            'electric_kwh' => 'Số kWh',
            'electric_unit_price' => 'Giá điện/kWh',
            'electric_total' => 'Tổng tiền điện',
            'water_price' => 'Giá nước',
            'water_occupants' => 'Số người dùng nước',
            'water_m3' => 'Khối nước',
            'water_total' => 'Tổng tiền nước',
            'total' => 'Tổng tiền phải trả',
            'status' => 'Trạng thái'
        ];

        return response()->json([
            'labels'  => $labels,
            'month1'  => $data[$m1]?->only(array_keys($labels)) ?? [],
            'month2'  => $data[$m2]?->only(array_keys($labels)) ?? [],
        ]);
    }


    public function monthDetail(Request $request, Room $room)
    {
        $month = (int) $request->get('month');
        $bill = RoomsRoomBill::where('room_id', $room->room_id)
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', $month)
            ->first();

        if (!$bill) {
            return response()->json(['success' => false, 'message' => 'Không có dữ liệu cho tháng này.']);
        }
        $labels = [
            'rent_price' => 'Tiền phòng',
            'electric_start' => 'Chỉ số điện đầu',
            'electric_end' => 'Chỉ số điện cuối',
            'electric_kwh' => 'Số kWh',
            'electric_unit_price' => 'Giá điện/kWh',
            'electric_total' => 'Tổng tiền điện',
            'water_price' => 'Giá nước',
            'water_occupants' => 'Số người dùng nước',
            'water_m3' => 'Khối nước',
            'water_total' => 'Tổng tiền nước',
            'total' => 'Tổng tiền phải trả',
            'status' => 'Trạng thái'
        ];

        return response()->json([
            'success' => true,
            'labels' => $labels,
            'values' => $bill->only(array_keys($labels))
        ]);
    }

    public function quarterDetail(Request $request, Room $room)
{
    $quarter = (int) $request->get('quarter');
    $months = match ($quarter) {
        1 => [1, 2, 3],
        2 => [4, 5, 6],
        3 => [7, 8, 9],
        4 => [10, 11, 12],
        default => []
    };

    $bills = RoomsRoomBill::where('room_id', $room->room_id)
        ->whereYear('created_at', now()->year)
        ->whereIn(DB::raw('MONTH(created_at)'), $months)
        ->get();

    if ($bills->isEmpty()) {
        return response()->json(['success' => false, 'message' => 'Không có dữ liệu cho quý này.']);
    }

    $labels = [
        'rent_price' => 'Tiền phòng',
        'electric_start' => 'Chỉ số điện đầu',
        'electric_end' => 'Chỉ số điện cuối',
        'electric_kwh' => 'Số kWh',
        'electric_unit_price' => 'Giá điện/kWh',
        'electric_total' => 'Tổng tiền điện',
        'water_price' => 'Giá nước',
        'water_occupants' => 'Số người dùng nước',
        'water_m3' => 'Khối nước',
        'water_total' => 'Tổng tiền nước',
        'total' => 'Tổng tiền phải trả',
        'status' => 'Trạng thái'
    ];

    $totals = [];
    foreach (array_keys($labels) as $key) {
        if ($key === 'status') {
            // Lấy trạng thái cuối cùng (có thể đổi sang logic khác nếu cần)
            $totals[$key] = $bills->last()?->status ?? '-';
        } else {
            $totals[$key] = $bills->sum($key);
        }
    }

    return response()->json([
        'success' => true,
        'labels' => $labels,
        'values' => $totals
    ]);
}

public function compareQuarters(Request $request, Room $room)
{
    $q1 = (int) $request->get('q1');
    $q2 = (int) $request->get('q2');

    $map = fn($q) => match ($q) {
        1 => [1, 2, 3],
        2 => [4, 5, 6],
        3 => [7, 8, 9],
        4 => [10, 11, 12],
        default => []
    };

    $months1 = $map($q1);
    $months2 = $map($q2);

    $bills = RoomsRoomBill::where('room_id', $room->room_id)
        ->whereYear('created_at', now()->year)
        ->where(function ($query) use ($months1, $months2) {
            $query->whereIn(DB::raw('MONTH(created_at)'), $months1)
                  ->orWhereIn(DB::raw('MONTH(created_at)'), $months2);
        })
        ->get();

    $labels = [
        'rent_price' => 'Tiền phòng',
        'electric_start' => 'Chỉ số điện đầu',
        'electric_end' => 'Chỉ số điện cuối',
        'electric_kwh' => 'Số kWh',
        'electric_unit_price' => 'Giá điện/kWh',
        'electric_total' => 'Tổng tiền điện',
        'water_price' => 'Giá nước',
        'water_occupants' => 'Số người dùng nước',
        'water_m3' => 'Khối nước',
        'water_total' => 'Tổng tiền nước',
        'total' => 'Tổng tiền phải trả',
        'status' => 'Trạng thái'
    ];

    $grouped = $bills->groupBy(function ($bill) use ($months1, $months2, $q1, $q2) {
        if (in_array($bill->created_at->month, $months1)) {
            return $q1;
        } elseif (in_array($bill->created_at->month, $months2)) {
            return $q2;
        }
        return null;
    });

    $result = [];
    foreach ([$q1, $q2] as $q) {
        $group = $grouped[$q] ?? collect();
        $result[$q] = [];

        foreach (array_keys($labels) as $key) {
            if ($key === 'status') {
                $result[$q][$key] = $group->last()?->status ?? '-';
            } else {
                $result[$q][$key] = $group->sum($key);
            }
        }
    }

    return response()->json([
        'success' => true,
        'labels' => $labels,
        'quarter1' => $result[$q1] ?? [],
        'quarter2' => $result[$q2] ?? []
    ]);
}

}
