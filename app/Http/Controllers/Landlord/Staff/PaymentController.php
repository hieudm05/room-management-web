<?php

namespace App\Http\Controllers\Landlord\Staff;

use App\Models\User;
use App\Models\RentalAgreement;
use App\Exports\RoomBillExport;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Landlord\BankAccount;
use App\Models\Landlord\Room;
use App\Models\Landlord\Staff\Rooms\RoomBill;
use App\Models\Landlord\Staff\Rooms\RoomBillService;
use App\Models\Landlord\Staff\Rooms\RoomBillAdditionalFee;
use App\Models\Landlord\Staff\Rooms\RoomStaff;
use App\Models\Landlord\Staff\Rooms\RoomUtility;
use App\Models\Landlord\Staff\Rooms\RoomUtilityPhoto;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $staffId = Auth::id();
        $month = $request->input('month', now()->format('Y-m'));

        $roomIds = RoomStaff::where('staff_id', $staffId)->where('status', 'active')->pluck('room_id');

        $rooms = Room::whereIn('room_id', $roomIds)
            ->with([
                'rentalAgreement.renter',
                'bills' => fn($q) => $q->where('month', 'like', $month . '%'),
                'services',
            ])
            ->get();
        $data = [];
        foreach ($rooms as $room) {
            $rentalAgreement = $room->rentalAgreement;
            $tenant = $rentalAgreement ? User::find($rentalAgreement->renter_id) : null;
            $bill = $room->bills->first();
            $utility = $room->utilities->first();

            // Lấy chỉ số đầu từ tháng trước
            $previousMonth = date('Y-m', strtotime($month . '-01 -1 month'));
            $previousBill = RoomBill::where('room_id', $room->room_id)
                ->where('month', 'like', $previousMonth . '%')
                ->first();

            $electricService = $room->services->firstWhere('service_id', 1);
            $electricPrice = $electricService ? $electricService->pivot->price : 3000;
            $waterService = $room->services->firstWhere('service_id', 2);
            $waterPrice = $waterService ? $waterService->pivot->price : 20000;
            $waterUnit = $waterService ? $waterService->pivot->unit : 'per_m3';

            $services = [];
            // $serviceTotal = 0;
            // if ($bill) {
            //     $billServices = RoomBillService::where('room_bill_id', $bill->id)->get();
            //     foreach ($billServices as $sv) {
            //         $service = \App\Models\Landlord\Service::find($sv->service_id);
            //         $services[] = [
            //             'service_id' => $sv->service_id,
            //             'name' => $service->name ?? 'Không rõ',
            //             'price' => $sv->price,
            //             'qty' => $sv->qty,
            //             'total' => $sv->total,
            //         ];
            //         $serviceTotal += $sv->total;
            //     }
            // }
            $services = [];
            $serviceTotal = 0;

            foreach ($room->services as $service) {
                // Loại bỏ dịch vụ "Điện" và "Nước"
                if (in_array(mb_strtolower($service->name), ['điện', 'nước'])) {
                    continue;
                }

                $pivot = $service->pivot;
                $isFree = $pivot->is_free;
                $isPerPerson = $pivot->is_per_person;
                $price = $pivot->price;

                $qty = $isFree ? 1 : ($isPerPerson ? ($room->people_renter ?? 1) : 1);
                $total = $isFree ? 0 : $price * $qty;

                $services[] = [
                    'service_id' => $service->id,
                    'name' => $service->name,
                    'price' => $price,
                    'qty' => $qty,
                    'total' => $total,
                    'type_display' => $isFree ? 'Miễn phí' : ($isPerPerson ? 'Tính theo người' : 'Tính cố định'),
                ];

                $serviceTotal += $total;
            }
            ;

            // Khiếu nại
            $target = Carbon::createFromFormat('Y-m', $month);

            // dd($target);
            $complaints = Complaint::where('room_id', $room->room_id)
                ->where('status', 'resolved')
                ->whereMonth('updated_at', $target->month)
                ->whereYear('updated_at', $target->year)
                ->get();

            $complaintUserCost = $complaints->sum('user_cost');
            $complaintLandlordCost = $complaints->sum('landlord_cost');


            // 3. Tổng cuối cùng
            $totalAfterComplaint = $complaintUserCost - $complaintLandlordCost;

            // end khiếu nại
            $additionalFees = [];
            $additionalFeesTotal = 0;
            if ($bill) {
                $billAdditionalFees = RoomBillAdditionalFee::where('room_bill_id', $bill->id)->get();
                foreach ($billAdditionalFees as $fee) {
                    $additionalFees[] = [
                        'name' => $fee->name,
                        'price' => $fee->price,
                        'qty' => $fee->qty,
                        'total' => $fee->total,
                    ];
                    $additionalFeesTotal += $fee->total;
                }
            }

            $electricPhotos = $bill
                ? RoomUtilityPhoto::where('room_bill_id', $bill->id)
                    ->where('type', 'electric')
                    ->pluck('image_path')
                    ->toArray()
                : [];

            $waterPhotos = $bill && $waterUnit == 'per_m3'
                ? RoomUtilityPhoto::where('room_bill_id', $bill->id)
                    ->where('type', 'water')
                    ->pluck('image_path')
                    ->toArray()
                : [];


            $rentPrice = $bill ? $bill->rent_price : ($room->rental_price ?? 0);
            $electricTotal = $utility ? $utility->electricity : ($bill ? $bill->electric_total : 0);
            $waterTotal = $utility ? $utility->water : ($bill ? $bill->water_total : 0);
            $total = $rentPrice + $electricTotal + $waterTotal + $additionalFeesTotal + $serviceTotal + $totalAfterComplaint;

            // Kiểm tra hóa đơn đã đầy đủ thông tin chưa
            $isBillLocked = $bill &&
                $bill->electric_kwh > 0 && $bill->electric_total > 0 &&
                (
                    ($bill->water_unit == 'per_m3' && $bill->water_m3 > 0 && $bill->water_total > 0)
                    || ($bill->water_unit == 'per_person' && $bill->water_occupants > 0 && $bill->water_total > 0)
                );
            $data[] = [
                'id_bill' => $bill->id,
                'bill' => $bill,
                'room_id' => $room->room_id,
                'room_name' => $room->room_number ?? $room->room_name ?? 'P101',
                'tenant_name' => $tenant ? $tenant->name : 'Chưa có',
                'area' => $room->area ?? 0,
                'rent_price' => $rentPrice,
                'month' => $month,
                'electric_start' => $previousBill ? $previousBill->electric_end : ($bill ? $bill->electric_start : 0),
                'electric_end' => $utility ? $utility->electric_end : 0,
                'electric_kwh' => $utility ? $utility->electric_kwh : 0,
                'electric_price' => $electricPrice,
                'electric_total' => $electricTotal,
                'electric_photos' => $electricPhotos,
                'water_price' => $waterPrice,
                'water_unit' => $waterUnit ?? 'per_m3',
                'water_occupants' => $utility ? $utility->water_occupants : ($bill ? $bill->water_occupants : 1),
                'water_start' => $waterUnit == 'per_m3'
                    ? ($previousBill ? $previousBill->water_end : 0)
                    : ($bill ? $bill->water_start : 0),

                'water_m3' => $utility ? $utility->water_m3 : 0,
                'water_total' => $waterTotal,
                'water_photos' => $waterPhotos,
                'services' => $services,
                'service_total' => $serviceTotal,
                'complaints' => $complaints,
                'complaint_user_cost' => $complaintUserCost,
                'complaint_landlord_cost' => $complaintLandlordCost,
                'total_after_complaint' => $totalAfterComplaint,
                'additional_fees' => $additionalFees,
                'additional_fees_total' => $additionalFeesTotal,
                'total' => $total,
                'status' => $bill ? $bill->status : 'unpaid',
                'is_bill_locked' => $isBillLocked,
            ];
        }

        return view('landlord.Staff.rooms.bills.index', compact('rooms', 'data'));
    }

    public function store(Request $request, Room $room)
    {
        $staffId = Auth::id();
        $validated = $request->validate([
            'data.month' => 'required|date_format:Y-m',
            'data.tenant_name' => 'required|string|max:255',
            'data.area' => 'required|numeric|min:0',
            'data.rent_price' => 'required|numeric|min:0',
            'data.electric_start' => 'nullable|integer|min:0',
            'data.electric_end' => 'nullable|integer|min:0|gte:data.electric_start',
            'data.electric_kwh' => 'required|numeric|min:0',
            'data.electric_price' => 'required|numeric|min:0',
            'data.electric_total' => 'required|numeric|min:0',
            'data.electric_photos.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'data.water_price' => 'required|numeric|min:0',
            'data.water_unit' => 'required|string|in:per_person,per_m3',
            'data.water_occupants' => 'nullable|integer|min:0',
            'data.water_start' => 'nullable|integer|min:0',
            'data.water_end' => 'nullable|integer|min:0',
            'data.water_m3' => 'nullable|numeric|min:0',
            'data.water_total' => 'required|numeric|min:0',
            'data.water_photos.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'data.total' => 'required|numeric|min:0',
            'data.services' => 'nullable|array',
            'data.services.*.service_id' => 'required|integer',
            'data.services.*.price' => 'required|numeric|min:0',
            'data.services.*.qty' => 'required|integer|min:1',
            'data.services.*.total' => 'required|numeric|min:0',
            'data.additional_fees' => 'nullable|array',
            'data.additional_fees.*.name' => 'required|string|max:255',
            'data.additional_fees.*.price' => 'required|numeric|min:0',
            'data.additional_fees.*.qty' => 'required|integer|min:1',
            'data.additional_fees.*.total' => 'required|numeric|min:0',
            'data.complaint_user_cost' => 'nullable|numeric|min:0',
            'data.complaint_landlord_cost' => 'nullable|numeric|min:0',

        ]);
        // dd($validated);
        \Log::info('Data sent to store:', $request->all()); // Debug

        DB::beginTransaction();
        try {
            $data = $validated['data'];
            // Đảm bảo water_unit có giá trị mặc định
            $data['water_unit'] = $data['water_unit'] ?? 'per_m3';
            $data['electric_price'] = $data['electric_price'] ?? 3000;
            $data['water_price'] = $data['water_price'] ?? 20000;

            $bankAccountId = null;

            if ($room && $room->room_id) {
                $staffId = Auth::id();

                $isStaff = RoomStaff::where('room_id', $room->room_id)
                    ->where('staff_id', $staffId)
                    ->where('status', 'active')
                    ->exists();

                if ($isStaff) {
                    $bankAccountId = BankAccount::where('user_id', $staffId)->value('id');
                }

                if (!$bankAccountId) {
                    $bankAccountId = optional($room->property)->bank_account_id;
                }
            }


            // Lưu vào bảng room_bills
            $bill = RoomBill::updateOrCreate(
                [
                    'room_id' => $room->room_id,
                    'month' => $data['month'] . '-01',
                ],
                [
                    'bank_account_id' => $bankAccountId,
                    'tenant_name' => $data['tenant_name'],
                    'area' => $data['area'],
                    'rent_price' => $data['rent_price'],
                    'electric_start' => $data['electric_start'],
                    'electric_end' => $data['electric_end'],
                    'electric_kwh' => $data['electric_kwh'] ?? 0,
                    'electric_unit_price' => $data['electric_price'],
                    'electric_total' => $data['electric_total'],
                    'water_price' => $data['water_price'],
                    'water_unit' => $data['water_unit'],
                    'water_occupants' => $data['water_occupants'] ?? 0,
                    'water_start' => $data['water_unit'] == 'per_m3' ? ($data['water_start'] ?? 0) : 0,
                    'water_m3' => $data['water_m3'] ?? 0,
                    'water_end' => $data['water_end'] ?? 0,
                    'water_total' => $data['water_total'],
                    'complaint_user_cost' => $data['complaint_user_cost'] ?? 0,
                    'complaint_landlord_cost' => $data['complaint_landlord_cost'] ?? 0,
                    'total' => $data['total'],
                    'status' => 'unpaid',
                ]
            );

            // Xử lý upload ảnh
            RoomUtilityPhoto::where('room_bill_id', $bill->id)->delete();
            if ($request->hasFile('data.electric_photos')) {
                foreach ($request->file('data.electric_photos') as $photo) {
                    $path = $photo->store('utilities/electric', 'public');
                    RoomUtilityPhoto::create([
                        'room_bill_id' => $bill->id,
                        'type' => 'electric',
                        'image_path' => $path,
                    ]);
                }
            }
            if ($data['water_unit'] == 'per_m3' && $request->hasFile('data.water_photos')) {
                foreach ($request->file('data.water_photos') as $photo) {
                    $path = $photo->store('utilities/water', 'public');
                    RoomUtilityPhoto::create([
                        'room_bill_id' => $bill->id,
                        'type' => 'water',
                        'image_path' => $path,
                    ]);
                }
            }

            // Xóa và lưu dịch vụ
            RoomBillService::where('room_bill_id', $bill->id)->delete();
            if (!empty($data['services'])) {
                foreach ($data['services'] as $sv) {
                    RoomBillService::create([
                        'room_bill_id' => $bill->id,
                        'service_id' => $sv['service_id'],
                        'price' => $sv['price'],
                        'qty' => $sv['qty'],
                        'total' => $sv['total'],
                    ]);
                }
            }

            // Xóa và lưu chi phí phát sinh
            RoomBillAdditionalFee::where('room_bill_id', $bill->id)->delete();
            if (!empty($data['additional_fees'])) {
                foreach ($data['additional_fees'] as $fee) {
                    RoomBillAdditionalFee::create([
                        'room_bill_id' => $bill->id,
                        'name' => $fee['name'],
                        'price' => $fee['price'],
                        'qty' => $fee['qty'],
                        'total' => $fee['total'],
                    ]);
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Lưu hóa đơn phòng ' . $room->room_number . ' thành công!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Lỗi lưu hóa đơn: ' . $e->getMessage());
        }
    }

    public function exportExcel(Request $request, Room $room)
    {
        $month = $request->input('month', now()->format('Y-m'));
        $monthParts = explode('-', $month);
        $monthNum = $monthParts[1] ?? now()->format('m');
        $yearNum = $monthParts[0] ?? now()->format('Y');

        $bill = RoomBill::where('room_id', $room->room_id)
            ->whereMonth('month', $monthNum)
            ->whereYear('month', $yearNum)
            ->first();

        if (!$bill) {
            return redirect()->back()->with('error', 'Chưa có hóa đơn để xuất file.');
        }

        $rentalAgreement = $room->rentalAgreement;
        $tenant = $rentalAgreement ? User::find($rentalAgreement->renter_id) : null;

        // Lấy dịch vụ
        $services = RoomBillService::where('room_bill_id', $bill->id)->get()->map(function ($sv) {
            return [
                'name' => optional($sv->service)->name ?? 'Không rõ',
                'price' => $sv->price,
                'qty' => $sv->qty,
                'total' => $sv->total,
            ];
        })->toArray();
        $serviceTotal = array_sum(array_column($services, 'total'));

        // Lấy chi phí phát sinh
        $additionalFees = RoomBillAdditionalFee::where('room_bill_id', $bill->id)->get()->map(function ($fee) {
            return [
                'name' => $fee->name,
                'price' => $fee->price,
                'qty' => $fee->qty,
                'total' => $fee->total,
            ];
        })->toArray();
        $additionalFeesTotal = array_sum(array_column($additionalFees, 'total'));

        // Lấy ảnh điện & nước
        $electricPhotos = RoomUtilityPhoto::where('room_bill_id', $bill->id)
            ->where('type', 'electric')
            ->pluck('image_path')
            ->toArray();

        $waterPhotos = RoomUtilityPhoto::where('room_bill_id', $bill->id)
            ->where('type', 'water')
            ->pluck('image_path')
            ->toArray();

        // Chuẩn bị data truyền vào Excel
        $data = [
            'room_name' => $room->room_number ?? $room->room_name ?? 'P101',
            'tenant_name' => $tenant ? $tenant->name : 'Chưa có',
            'area' => $room->area ?? 0,
            'rent_price' => $bill->rent_price ?? 0,
            'month' => $month,
            'electric_start' => $bill->electric_start ?? 0,
            'electric_end' => $bill->electric_end ?? 0,
            'electric_kwh' => $bill->electric_kwh ?? 0,
            'electric_price' => $bill->electric_unit_price ?? 3000,
            'electric_total' => $bill->electric_total ?? 0,
            'electric_photos' => $electricPhotos,

            'water_price' => $bill->water_price ?? 20000,
            'water_unit' => $bill->water_unit ?? 'per_m3',
            'water_occupants' => $bill->water_occupants ?? 1,
            'water_start' => $bill->water_start ?? 0,
            'water_m3' => $bill->water_m3 ?? 0,
            'water_total' => $bill->water_total ?? 0,
            'water_photos' => $waterPhotos,

            'services' => $services,
            'service_total' => $serviceTotal,
            'additional_fees' => $additionalFees,
            'additional_fees_total' => $additionalFeesTotal,
            'total' => $bill->total ?? 0,
        ];

        return Excel::download(new RoomBillExport($room, $data), 'hoadon_' . $month . '.xlsx');
    }

    // Update trạng thái thanh toán
 public function updateStatus(Request $request, $id)

{
    $bill = RoomBill::findOrFail($id);
    $newStatus = $request->input('status');

    $validStatuses = ['unpaid', 'pending', 'paid'];

    if (!in_array($newStatus, $validStatuses)) {
        return response()->json(['error' => '❌ Trạng thái không hợp lệ.'], 400);
    }

    $currentIndex = array_search($bill->status, $validStatuses);
    $newIndex = array_search($newStatus, $validStatuses);

    if ($newIndex <= $currentIndex) {
        return response()->json(['error' => '❌ Không thể chuyển về trạng thái trước đó.'], 400);
    }

    $bill->status = $newStatus;
    $bill->save();

    return response()->json(['success' => '✅ Đã cập nhật trạng thái!', 'status' => $newStatus]);
}



}