<?php
namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Landlord\Property;
use App\Models\Landlord\Staff\Rooms\RoomBill;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class LandlordBillController extends Controller
{
    public function index(Request $request)
    {
        $landlordId = Auth::id();
        $month = $request->input('month', now()->format('Y-m'));
        $status = $request->input('status');

        $properties = Property::with(['rooms.bills' => function ($query) use ($month, $status) {
            $query->where('month', 'like', $month . '%');
            if ($status) {
                $query->where('status', $status);
            }
        }, 'rooms.rentalAgreement.renter'])
            ->where('landlord_id', $landlordId)
            ->get();

        return view('landlord.bills.index', compact('properties', 'month', 'status'));
    }

    public function show(RoomBill $bill)
    {
        $room = $bill->room;
        $property = $room->property;
        $tenant = optional($room->rentalAgreement)->renter;
        $month = Carbon::parse($bill->month)->format('Y-m');

        // Lấy giá điện, nước từ dịch vụ
        $electricService = $room->services->firstWhere('service_id', 1);
        $electricPrice = $electricService ? $electricService->pivot->price : 3000;

        $waterService = $room->services->firstWhere('service_id', 2);
        $waterPrice = $waterService ? $waterService->pivot->price : 20000;
        $waterUnit = $waterService ? $waterService->pivot->unit : 'per_m3';

        // Khiếu nại tháng đó
        $target = Carbon::parse($bill->month);
        $complaints = $room->complaints()
            ->where('status', 'resolved')
            ->whereMonth('updated_at', $target->month)
            ->whereYear('updated_at', $target->year)
            ->get();

        $complaintUserCost = $complaints->sum('user_cost');
        $complaintLandlordCost = $complaints->sum('landlord_cost');

        // Tổng cộng người thuê phải trả thêm
        $totalAfterComplaint = $complaintUserCost;

        // Phí phát sinh
        $additionalFees = $bill->additionalFees ?? [];
        $additionalFeesTotal = collect($additionalFees)->sum('total');

        // Kiểm tra và log nếu additionalFeesTotal không khớp
        $calculatedTotal = 0;
        foreach ($additionalFees as $fee) {
            $calculatedTotal += ($fee['price'] ?? 0) * ($fee['qty'] ?? 1);
        }
        if ($calculatedTotal != $additionalFeesTotal) {
            Log::warning('Mismatch in additional fees total for bill ID: ' . $bill->id, [
                'calculated' => $calculatedTotal,
                'stored' => $additionalFeesTotal,
                'additionalFees' => $additionalFees
            ]);
        }

        // Ảnh điện/nước
        $electricPhotos = $bill->utilityPhotos()->where('type', 'electric')->pluck('image_path');
        $waterPhotos = $bill->utilityPhotos()->where('type', 'water')->pluck('image_path');

        // Dịch vụ phụ (ngoại trừ điện và nước)
        $services = $bill->services ?? [];
        $serviceTotal = collect($services)->sum('total');

        // Tổng hóa đơn
        $total = $bill->total;

        return view('landlord.bills.show', compact(
            'bill', 'room', 'tenant', 'property',
            'electricPhotos', 'waterPhotos', 'services', 'additionalFees', 'complaints',
            'complaintUserCost', 'complaintLandlordCost', 'totalAfterComplaint', 'serviceTotal',
            'additionalFeesTotal', 'total', 'waterUnit', 'waterPrice', 'electricPrice'
        ));
    }
}