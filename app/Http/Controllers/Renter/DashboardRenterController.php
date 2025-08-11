<?php

namespace App\Http\Controllers\Renter;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Landlord\Staff\Rooms\RoomBill;
use App\Models\Landlord\Staff\Rooms\RoomBillService;

use App\Models\Complaint;
use App\Models\UserInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardRenterController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $roomId = UserInfo::where('user_id', $userId)->value('room_id');

        if (!$roomId) {
            return back()->with('error', 'Không tìm thấy phòng đang thuê.');
        }

        $type = $request->input('type', 'month');
        $period = $request->input('period');

        if (!$period) {
            return view('profile.tenants.dashboard')->with([
                'totalCost' => 0,
                'complaintCount' => 0,
                'serviceTotals' => collect(),
            ]);
        }

        switch ($type) {
            case 'month':
                [$year, $month] = explode('-', $period);
                $data = $this->getMonthlyData($roomId, $year, $month);
                break;
            case 'quarter':
                $year = substr($period, 0, 4);
                $quarter = substr($period, 5);
                $data = $this->getQuarterData($roomId, $year, $quarter);
                break;
            case 'year':
                $year = $period;
                $data = $this->getYearlyData($roomId, $year);
                break;
            default:
                $data = collect();
        }

        $totalCost = $data->sum();
        $complaintCount = $data->get('Khiếu nại', 0);
        $serviceTotals = $data->except('Khiếu nại');

        return view('profile.tenants.dashboard', compact('totalCost', 'complaintCount', 'serviceTotals'));
    }
    public function compare(Request $request)
    {
        $userId = Auth::id();

        $roomId = UserInfo::where('user_id', $userId)->value('room_id');

        if (!$roomId) {
            return back()->with('error', 'Không tìm thấy phòng đang thuê.');
        }

        $type = $request->input('type', 'month');
        $period1 = $request->input('period1');
        $period2 = $request->input('period2');

        if (!$period1 || !$period2) {
            return view('compare-room-bills');
        }

        if ($type === 'month') {
            [$year1, $month1] = explode('-', $period1);
            [$year2, $month2] = explode('-', $period2);

            $bills1 = $this->getMonthlyData($roomId, $year1, $month1);
            $bills2 = $this->getMonthlyData($roomId, $year2, $month2);

            $label1 = "Tháng $month1/$year1";
            $label2 = "Tháng $month2/$year2";
        } else {
            $year1 = substr($period1, 0, 4);
            $quarter1 = substr($period1, 5);
            $year2 = substr($period2, 0, 4);
            $quarter2 = substr($period2, 5);

            $bills1 = $this->getQuarterData($roomId, $year1, $quarter1);
            $bills2 = $this->getQuarterData($roomId, $year2, $quarter2);

            $label1 = "Quý $quarter1/$year1";
            $label2 = "Quý $quarter2/$year2";
        }

        return view('compare-room-bills', [
            'type' => $type,
            'label1' => $label1,
            'label2' => $label2,
            'bills1' => $bills1,
            'bills2' => $bills2
        ]);
    }

    private function getMonthlyData($roomId, $year, $month)
    {
        $bill = RoomBill::where('room_id', $roomId)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->first();

        $services = $bill
            ? $bill->services()->pluck('amount', 'service_name')
            : collect();

        $complaints = Complaint::where('room_id', $roomId)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count();

        return $services->put('Khiếu nại', $complaints);
    }

    private function getQuarterData($roomId, $year, $quarter)
    {
        $startMonth = ($quarter - 1) * 3 + 1;
        $endMonth = $startMonth + 2;

        $bills = RoomBill::where('room_id', $roomId)
            ->whereYear('created_at', $year)
            ->whereBetween(DB::raw('MONTH(created_at)'), [$startMonth, $endMonth])
            ->pluck('id');

        $services = RoomBillService::whereIn('room_bill_id', $bills)
            ->select('service_name', DB::raw('SUM(amount) as total'))
            ->groupBy('service_name')
            ->pluck('total', 'service_name');

        $complaints = Complaint::where('room_id', $roomId)
            ->whereYear('created_at', $year)
            ->whereBetween(DB::raw('MONTH(created_at)'), [$startMonth, $endMonth])
            ->count();

        return $services->put('Khiếu nại', $complaints);
    }
}