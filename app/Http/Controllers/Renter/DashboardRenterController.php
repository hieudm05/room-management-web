<?php

namespace App\Http\Controllers\Renter;

use App\Http\Controllers\Controller;
use App\Models\Landlord\Staff\Rooms\RoomBill;
use App\Models\Landlord\Staff\Rooms\RoomBillService;


// use App\Models\Landlord\Staff\Rooms\RoomBill;
// use App\Models\Landlord\Staff\Rooms\RoomBillService;

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
            abort(404, 'Không tìm thấy phòng đang thuê.');
        }

        $type = $request->input('type', 'year');
        $period = $request->input('period', $type === 'month' ? date('Y-m') : date('Y'));

        $totalCost = 0;
        $complaintCount = 0;
        $serviceTotals = collect();

        $lineData = collect();

        if ($type === 'year') {
            if (!preg_match('/^\d{4}$/', $period)) {
                $period = date('Y');
            }

            $lineData = RoomBill::select(
                    DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                    DB::raw("SUM(total) as total")
                )
                ->where('room_id', $roomId)
                ->whereYear('created_at', $period)
                ->groupBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m')"))
                ->orderBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m')"))
                ->pluck('total', 'month');

            $totalCost = $lineData->sum();

            $complaintCount = Complaint::where('room_id', $roomId)
                ->whereYear('created_at', $period)
                ->count();

            $billIds = RoomBill::where('room_id', $roomId)
                ->whereYear('created_at', $period)
                ->pluck('id');


            if ($billIds->isNotEmpty()) {
                $serviceTotals = RoomBillService::join('services', 'room_bill_service.service_id', '=', 'services.service_id')
                    ->whereIn('room_bill_service.room_bill_id', $billIds)
                    ->select('services.name as service_name', DB::raw('SUM(room_bill_service.total) as total'))
                    ->groupBy('services.name')
                    ->pluck('total', 'service_name');

            }

        } elseif ($type === 'month') {
            if (!preg_match('/^\d{4}-\d{2}$/', $period)) {
                $period = date('Y-m');


            }

            [$year, $month] = explode('-', $period);

            $billIds = RoomBill::where('room_id', $roomId)
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->pluck('id');
            //  dd($billIds->toArray());
if ($billIds->isNotEmpty()) {
                $totalCost = RoomBill::whereIn('id', $billIds)->sum('total');
                $complaintCount = Complaint::where('room_id', $roomId)
                    ->whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->count();

                $serviceTotals = RoomBillService::join('services', 'room_bill_service.service_id', '=', 'services.service_id')
                    ->whereIn('room_bill_service.room_bill_id', $billIds)
                    ->select('services.name as service_name', DB::raw('SUM(room_bill_service.total) as total'))
                    ->groupBy('services.name')
                    ->pluck('total', 'service_name');
                    // Chuẩn hóa lại:

                //    dd($serviceTotals);
            }

        }

        // So sánh 2 mốc thời gian
        $compare = $request->has('compare');
        $bills1 = $bills2 = $label1 = $label2 = null;

        if ($compare) {
            $compareType = $request->input('compare_type', 'month');
            $period1 = $request->input('period1');
            $period2 = $request->input('period2');

            if ($compareType === 'month' && preg_match('/^\d{4}-\d{2}$/', $period1) && preg_match('/^\d{4}-\d{2}$/', $period2)) {
                [$y1, $m1] = explode('-', $period1);
                [$y2, $m2] = explode('-', $period2);
                $bills1 = $this->getServiceData($roomId, $y1, $m1);
                $bills2 = $this->getServiceData($roomId, $y2, $m2);
                $label1 = "Tháng $m1/$y1";
                $label2 = "Tháng $m2/$y2";
            } elseif ($compareType === 'year' && preg_match('/^\d{4}$/', $period1) && preg_match('/^\d{4}$/', $period2)) {
                $bills1 = $this->getServiceDataYear($roomId, $period1);
                $bills2 = $this->getServiceDataYear($roomId, $period2);
                $label1 = "Năm $period1";
                $label2 = "Năm $period2";
            }
        }

        return view('profile.tenants.dashboard', compact(
            'type', 'period', 'totalCost', 'complaintCount',
            'serviceTotals', 'lineData', 'bills1', 'bills2', 'label1', 'label2'
        ));
    }

    private function getServiceData($roomId, $year, $month)
    {
        $billIds = RoomBill::where('room_id', $roomId)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->pluck('id');

        if ($billIds->isEmpty()) {
            return collect();
        }

        return RoomBillService::join('services', 'room_bill_service.service_id', '=', 'services.service_id')
            ->whereIn('room_bill_service.room_bill_id', $billIds)
            ->select('services.name as service_name', DB::raw('SUM(room_bill_service.total) as total'))
            ->groupBy('services.name')
            ->pluck('total', 'service_name');
    }

    private function getServiceDataYear($roomId, $year)
    {
$billIds = RoomBill::where('room_id', $roomId)
            ->whereYear('created_at', $year)
->pluck('id');

        if ($billIds->isEmpty()) {
            return collect();
        }

        return RoomBillService::join('services', 'room_bill_service.service_id', '=', 'services.service_id')
            ->whereIn('room_bill_service.room_bill_id', $billIds)
            ->select('services.name as service_name', DB::raw('SUM(room_bill_service.total) as total'))
            ->groupBy('services.name')
            ->pluck('total', 'service_name');
    }
}

