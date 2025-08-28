<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Landlord\Staff\Rooms\RoomBill as RoomsRoomBill;
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
            'payment_time'   => 'required|date',
            'receipt_image'  => 'required|image|max:2048',
        ]);

        $path = $request->file('receipt_image')->store('receipts', 'public');

        $bill->status       = 'pending';
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
        $monthlyTotals = collect(range(1, 12))->mapWithKeys(fn($m) => [
            $m => $monthlyTotals->get($m, 0)
        ]);

        $quarterTotals = RoomsRoomBill::selectRaw('QUARTER(`month`) as quarter, SUM(total) as total')
            ->where('room_id', $room->room_id)
            ->whereYear('month', now()->year)
            ->groupBy('quarter')
            ->orderBy('quarter')
            ->pluck('total', 'quarter');

        // Đảm bảo đủ 4 quý
        $quarterTotals = collect(range(1, 4))->mapWithKeys(fn($q) => [
            $q => $quarterTotals->get($q, 0)
        ]);

        return view('landlord.rooms.statistics', compact('room', 'monthlyTotals', 'quarterTotals'));
    }

    public function compareMonths(Request $request, Room $room)
    {
        $m1 = (int) $request->get('m1');
        $m2 = (int) $request->get('m2');

        $items = RoomsRoomBill::where('room_id', $room->room_id)
            ->whereYear('month', now()->year)
            ->whereIn(DB::raw('MONTH(month)'), [$m1, $m2])
            ->get();

        $data = $items->groupBy(fn($item) => (int) Carbon::parse($item->month)->format('m'))
            ->map(fn($group) => $group->first());

        // Check if both months have no data
        $hasMonth1Data = $data->has($m1);
        $hasMonth2Data = $data->has($m2);

        // Chỉ trả về lỗi khi CẢ HAI tháng đều không có dữ liệu
        if (!$hasMonth1Data && !$hasMonth2Data) {
            return response()->json([
                'success' => false,
'message' => "Không có dữ liệu để so sánh giữa tháng $m1 và tháng $m2.",
                'both_no_data' => true
            ]);
        }

        // Labels cho các trường dữ liệu
        $labels = [
            'rent_price'          => 'Tiền phòng',
            'electric_start'      => 'Chỉ số điện đầu',
            'electric_end'        => 'Chỉ số điện cuối',
            'electric_kwh'        => 'Số kWh',
            'electric_unit_price' => 'Giá điện/kWh',
            'electric_total'      => 'Tổng tiền điện',
            'water_price'         => 'Giá nước',
            'water_occupants'     => 'Số người dùng nước',
            'water_m3'            => 'Khối nước',
            'water_total'         => 'Tổng tiền nước',
            'total'               => 'Tổng tiền phải trả',
            'status'              => 'Trạng thái'
        ];

        // Tạo dữ liệu mặc định (0 cho số, '-' cho status)
        $defaultData = [];
        foreach (array_keys($labels) as $key) {
            $defaultData[$key] = ($key === 'status') ? '-' : 0;
        }

        // Lấy dữ liệu cho từng tháng, nếu không có thì dùng default
        $month1Data = $hasMonth1Data
            ? $data[$m1]->only(array_keys($labels))
            : $defaultData;

        $month2Data = $hasMonth2Data
            ? $data[$m2]->only(array_keys($labels))
            : $defaultData;

        return response()->json([
            'success' => true,
            'labels' => $labels,
            'month1' => $month1Data,
            'month2' => $month2Data,
            'has_month1_data' => $hasMonth1Data,
            'has_month2_data' => $hasMonth2Data
        ]);
    }


    public function monthDetail(Request $request, Room $room)
    {
        $month = (int) $request->get('month');

        $bill = RoomsRoomBill::where('room_id', $room->room_id)
            ->whereYear('month', now()->year)
            ->whereMonth('month', $month)
            ->first();

        if (!$bill) {
            return response()->json(['success' => false, 'message' => 'Không có dữ liệu cho tháng này.']);
        }

        $labels = [
            'rent_price'        => 'Tiền phòng',
            'electric_start'    => 'Chỉ số điện đầu',
            'electric_end'      => 'Chỉ số điện cuối',
            'electric_kwh'      => 'Số kWh',
            'electric_unit_price' => 'Giá điện/kWh',
            'electric_total'    => 'Tổng tiền điện',
            'water_price'       => 'Giá nước',
            'water_occupants'   => 'Số người dùng nước',
            'water_m3'          => 'Khối nước',
            'water_total'       => 'Tổng tiền nước',
            'total'             => 'Tổng tiền phải trả',
            'status'            => 'Trạng thái'
        ];

        return response()->json([
            'success' => true,
            'labels'  => $labels,
'values'  => $bill->only(array_keys($labels))
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
            ->whereYear('month', now()->year)
            ->whereIn(DB::raw('MONTH(month)'), $months)
            ->get();

        if ($bills->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Không có dữ liệu cho quý này.']);
        }

        $labels = [
            'rent_price'        => 'Tiền phòng',
            'electric_start'    => 'Chỉ số điện đầu',
            'electric_end'      => 'Chỉ số điện cuối',
            'electric_kwh'      => 'Số kWh',
            'electric_unit_price' => 'Giá điện/kWh',
            'electric_total'    => 'Tổng tiền điện',
            'water_price'       => 'Giá nước',
            'water_occupants'   => 'Số người dùng nước',
            'water_m3'          => 'Khối nước',
            'water_total'       => 'Tổng tiền nước',
            'total'             => 'Tổng tiền phải trả',
            'status'            => 'Trạng thái'
        ];

        $totals = [];
        foreach (array_keys($labels) as $key) {
            if ($key === 'status') {
                $totals[$key] = $bills->last()?->status ?? '-';
            } else {
                $totals[$key] = $bills->sum($key);
            }
        }

        return response()->json([
            'success' => true,
            'labels'  => $labels,
            'values'  => $totals
        ]);
    }

    public function compareQuarters(Request $request, Room $room)
    {
        $q1 = (int) $request->get('q1');
        $q2 = (int) $request->get('q2');

        $quarterMap = fn($q) => match ($q) {
            1 => [1, 2, 3],
            2 => [4, 5, 6],
            3 => [7, 8, 9],
            4 => [10, 11, 12],
            default => []
        };

        $months1 = $quarterMap($q1);
        $months2 = $quarterMap($q2);

        $bills = RoomsRoomBill::where('room_id', $room->room_id)
            ->whereYear('month', now()->year)
            ->where(function ($query) use ($months1, $months2) {
                $query->whereIn(DB::raw('MONTH(month)'), $months1)
                    ->orWhereIn(DB::raw('MONTH(month)'), $months2);
            })
            ->get();

        $grouped = $bills->groupBy(function ($bill) use ($months1, $months2, $q1, $q2) {
            $month = Carbon::parse($bill->month)->month;
            if (in_array($month, $months1)) return $q1;
            if (in_array($month, $months2)) return $q2;
            return null;
        });

        // Kiểm tra xem quý nào có dữ liệu
$hasQ1Data = $grouped->has($q1) && $grouped[$q1]->isNotEmpty();
        $hasQ2Data = $grouped->has($q2) && $grouped[$q2]->isNotEmpty();

        // Chỉ trả về lỗi khi CẢ HAI quý đều không có dữ liệu
        if (!$hasQ1Data && !$hasQ2Data) {
            return response()->json([
                'success' => false,
                'message' => "Không có dữ liệu để so sánh giữa quý $q1 và quý $q2.",
                'both_no_data' => true
            ]);
        }

        $labels = [
            'rent_price'          => 'Tiền phòng',
            'electric_start'      => 'Chỉ số điện đầu',
            'electric_end'        => 'Chỉ số điện cuối',
            'electric_kwh'        => 'Số kWh',
            'electric_unit_price' => 'Giá điện/kWh',
            'electric_total'      => 'Tổng tiền điện',
            'water_price'         => 'Giá nước',
            'water_occupants'     => 'Số người dùng nước',
            'water_m3'            => 'Khối nước',
            'water_total'         => 'Tổng tiền nước',
            'total'               => 'Tổng tiền phải trả',
            'status'              => 'Trạng thái'
        ];

        $default = collect($labels)->map(fn($label, $key) => $key === 'status' ? '-' : 0)->toArray();

        $result = [];
        foreach ([$q1, $q2] as $q) {
            $group = $grouped[$q] ?? collect();
            $result[$q] = $default;

            if ($group->isNotEmpty()) {
                foreach (array_keys($labels) as $key) {
                    if ($key === 'status') {
                        $result[$q][$key] = $group->last()?->status ?? '-';
                    } else {
                        $result[$q][$key] = $group->sum($key);
                    }
                }
            }
        }

        return response()->json([
            'success'   => true,
            'labels'    => $labels,
            'quarter1'  => $result[$q1],
            'quarter2'  => $result[$q2],
            'has_quarter1_data' => $hasQ1Data,
            'has_quarter2_data' => $hasQ2Data
        ]);
    }

    private function calculateQuarterData($monthlyData, $monthsInQuarter)
    {
        $quarterData = [
            'rent_price' => 0,
            'electric_kwh' => 0,
            'electric_total' => 0,
            'water_m3' => 0,
            'water_total' => 0,
            'total' => 0,
            'months_with_data' => 0
        ];

        foreach ($monthsInQuarter as $month) {
            if (isset($monthlyData[$month])) {
                $data = $monthlyData[$month];
                $quarterData['rent_price'] += $data->rent_price ?? 0;
                $quarterData['electric_kwh'] += $data->electric_kwh ?? 0;
                $quarterData['electric_total'] += $data->electric_total ?? 0;
                $quarterData['water_m3'] += $data->water_m3 ?? 0;
                $quarterData['water_total'] += $data->water_total ?? 0;
$quarterData['total'] += $data->total ?? 0;
                $quarterData['months_with_data']++;
            }
        }

        return $quarterData;
    }

    private function hasQuarterData($monthlyData, $monthsInQuarter)
    {
        foreach ($monthsInQuarter as $month) {
            if (isset($monthlyData[$month])) {
                return true;
            }
        }
        return false;
    }
}