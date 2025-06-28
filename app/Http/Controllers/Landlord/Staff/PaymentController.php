<?php
namespace App\Http\Controllers\Landlord\Staff;

use App\Models\User;
use App\Models\RoomUser;
use Illuminate\Http\Request;
use Smalot\PdfParser\Parser;
use App\Models\Landlord\Room;

use App\Exports\RoomBillExport;
use App\Models\RentalAgreement;
use Illuminate\Support\Facades\DB;
// use PhpOffice\PhpWord\IOFactory;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Landlord\Staff\Rooms\RoomBill;
use App\Models\Landlord\Staff\Rooms\RoomUtility;
use App\Models\Landlord\Staff\Rooms\RoomBillService;


class PaymentController extends Controller
{
    public function index(Room $room, Request $request)
    {
        $rentalAgreement = RentalAgreement::find($room->id_rental_agreements);
        $contractPath = $rentalAgreement->contract_file;
        $fullPath = storage_path('app/public/' . $contractPath);
        if (!$contractPath) {
            return redirect()->back()->with('error', 'Hợp đồng không tồn tại.');
        }
        // Đọc file Word
        $text = '';
        try {
            $parser = new Parser();
            $pdf = $parser->parseFile($fullPath);
            $text = $pdf->getText();
        } catch (\Exception $e) {
            $text = 'Không thể đọc file Word: ' . $e->getMessage();
        }
        // dd($text);

        // Lấy số lượng người ở
        preg_match('/Số lượng người ở\s*:?\s*(\d+)/i', $text, $occupantsMatch);
        $occupants = isset($occupantsMatch[1]) ? (int) $occupantsMatch[1] : 1;
        // Lấy các dịch vụ (trừ điện, nước)
       $services = [];
if (preg_match('/3\.\s*Dịch vụ:(.*)4\./sU', $text, $serviceBlock)) {
    $lines = preg_split('/\r\n|\r|\n/', trim($serviceBlock[1]));
    foreach ($lines as $line) {
        if (preg_match('/-\s*([^:]+):\s*([\d,.]+)\s*VNĐ\/?([^\s]*)/u', $line, $m)) {
            $name = trim($m[1]);
            $price = (int) str_replace([',', '.'], '', $m[2]);
            $unit = isset($m[3]) ? trim($m[3]) : '';
            if (!preg_match('/Điện|Nước/i', $name)) {
                // Gán service_id nếu có
                $matchedService = $room->services->firstWhere('name', $name);
                $service_id = $matchedService->pivot->service_id ?? null;

                $services[] = [
                    'service_id' => $service_id,
                    'name' => $name,
                    'price' => $price,
                    'unit' => $unit,
                ];
            }
        }
    }
}
        $contractPath = 'storage/landlord/rooms/contracts/' . $room->contract_file;
        $fullPath = storage_path('app/public/' . $contractPath);
        $tenant = User::find($rentalAgreement->renter_id);

        // Lấy tháng/năm từ request hoặc mặc định là tháng hiện tại
        $month = $request->input('month', now()->format('Y-m'));
        $monthParts = explode('-', $month);
        $monthNum = $monthParts[1] ?? now()->format('m');
        $yearNum = $monthParts[0] ?? now()->format('Y');

        // Lấy hóa đơn tiện ích theo tháng/năm
        $bills = RoomUtility::where('room_id', $room->room_id)
            ->whereMonth('start_date', $monthNum)
            ->whereYear('start_date', $yearNum)
            ->first();
        $noBill = false;
        if (!$bills) {
            $noBill = true;
            // Truyền dữ liệu mặc định để tránh lỗi view
            $electric_kwh = 0;
            $electricity = 0;
            $water_m3 = 0;
            $water = 0;
            $electric_total = 0;
            $water_total = 0;
            $rent_price = $room->rental_price ?? 0;
        } else {
            $electric_kwh = $bills->electric_kwh ?? 0;
            $electric_total = $bills->electricity ?? 0;
            $water_m3 = $bills->water_m3 ?? 0;
            $water = $bills->water ?? 0;
            $water_total = $bills->water ?? 0;
            $rent_price = $room->rental_price ?? 0;
        }
        $service_total = 0;
        foreach ($services as &$sv) {
            if ($sv['unit'] === 'người') {
                $sv['qty'] = $occupants;
                $sv['total'] = $sv['price'] * $occupants;
            } else {
                $sv['qty'] = 1;
                $sv['total'] = $sv['price'];
            }
            $service_total += $sv['total'];
        }
        unset($sv);
        // Nếu không có hóa đơn, trả về dữ liệu mặc định
        $electric_kwh = $bills->electric_kwh ?? 0;
        $electric_total = $bills->electricity ?? 0;
        $water_m3 = $bills->water_m3 ?? 0;
        // Đơn giá tiền điện
        $electricService = $room->services->firstWhere('service_id', 1);
        $eletricPrice = $electricService->pivot->price ?? 0;

        // Đơn giá tiền nước
        $waterService = $room->services->firstWhere('service_id', 2);
        $waterPrice = $waterService->pivot->price ?? 0;
        $water_total = $bills->water ?? 0;
        // Tổng hóa đơn
        $rent_price = $room->rental_price ?? 0;
        $internet = 0; // Nếu có trường internet thì lấy, không thì để 0
        $total = $rent_price + $electric_total + $water_total + $service_total + $internet;

        $data = [
            'room_name' => $room->room_number ?? $room->room_name ?? 'P101',
            'tenant_name' => $tenant->name ?? 'Chưa có',
            'area' => $room->area ?? 0,
            'rent_price' => $rent_price,
            'month' => $month,
            'electric_start' => $bills->electric_start ?? null,
            'electric_end' => $bills->electric_end ?? null,
            'electric_kwh' => $electric_kwh,
            'electric_price' => $eletricPrice,
            'eletriccity' => $bills->electricity ?? 0,
            'water_price' => $waterPrice,
            'electric_total' => $electric_total,
            'water_unit' => $bills->water_unit ?? null,
            'water_occupants' => $bills->water_occupants ?? null,
            'water_m3' => $water_m3,
            'water_total' => $water_total,
            'internet' => $internet,
            'occupants' => $occupants,
            'services' => $services,
            'service_total' => $service_total,
            'total' => $total
        ];
        // dd($data['services']);


        return view('landlord.Staff.rooms.bills.index', compact('room', 'data', 'noBill'));
    }
    public function store(Request $request, Room $room)
    {
        $validated = $request->validate([
            'data.month' => 'required|date_format:Y-m',
            'data.tenant_name' => 'required|string|max:255',
            'data.area' => 'required|numeric|min:0',
            'data.rent_price' => 'required|numeric|min:0',
            'data.electric_start' => 'nullable|integer|min:0',
            'data.electric_end' => 'nullable|integer|min:0|gte:data.electric_start',
            'data.electric_kwh' => 'nullable|integer|min:0',
            'data.electric_unit_price' => 'nullable|numeric|min:0',
            'data.electric_total' => 'nullable|numeric|min:0',
            'data.water_price' => 'nullable|numeric|min:0',
            'data.water_unit' => 'nullable|string',
            'data.water_occupants' => 'nullable|integer|min:0',
            'data.water_m3' => 'nullable|numeric|min:0',
            'data.water_total' => 'nullable|numeric|min:0',
            'data.total' => 'required|numeric|min:0',
            'data.services' => 'nullable|array',
            'data.services.*.service_id' => 'required|integer|exists:services,service_id',
            'data.services.*.price' => 'required|numeric|min:0',
            'data.services.*.qty' => 'required|integer|min:1',
            'data.services.*.total' => 'required|numeric|min:0',
        ]);
        $data = $validated['data'];
        // dd($data);
        DB::beginTransaction();
    try {
        // Lưu hóa đơn phòng
        $bill = RoomBill::create([
            'room_id'             => $room->room_id,
            'month'               => $data['month'] . '-01',
            'tenant_name'         => $data['tenant_name'],
            'area'                => $data['area'],
            'rent_price'          => $data['rent_price'],
            'electric_start'      => $data['electric_start'],
            'electric_end'        => $data['electric_end'],
            'electric_kwh'        => $data['electric_kwh'] ?? 0,
            'electric_unit_price' => $data['electric_price'] ?? 0,
            'electric_total'      => $data['electric_total'],
            'water_price'         => $data['water_price'],
            'water_unit'          => $data['water_unit'],
            'water_occupants'     => $data['water_occupants'],
            'water_m3'            => $data['water_m3'],
            'water_total'         => $data['water_total'],
            'total'               => $data['total'],
            'status'              => 'unpaid',
        ]);

        // Lưu các dịch vụ phụ
        if (!empty($data['services'])) {
            foreach ($data['services'] as $sv) {
                RoomBillService::create([
                    'room_bill_id' => $bill->id,
                    'service_id'   => $sv['service_id'],
                    'price'       => $sv['price'],
                    'qty'         => $sv['qty'],
                    'total'       => $sv['total'],
                ]);
            }
        }        DB::commit();
        return redirect()->back()->with('success', 'Lưu hóa đơn thành công!');
    } catch (\Exception $e) {
       DB::rollBack();
    \Log::error('Lỗi lưu hóa đơn: ' . $e->getMessage());
    return redirect()->back()->with('error', 'Lỗi lưu hóa đơn: ' . $e->getMessage());
    }
    }
public function exportExcel(Room $room, Request $request)
{
    $month = $request->input('month', now()->format('Y-m'));
    $monthParts = explode('-', $month);
    $monthNum = $monthParts[1] ?? now()->format('m');
    $yearNum = $monthParts[0] ?? now()->format('Y');

    $rentalAgreement = RentalAgreement::find($room->id_rental_agreements);
    $tenant = User::find($rentalAgreement->renter_id);

    // Lấy hóa đơn đã lưu (nếu có)
    $bill = RoomBill::where('room_id', $room->room_id)
        ->whereMonth('month', $monthNum)
        ->whereYear('month', $yearNum)
        ->first();

    if (!$bill) {
        return redirect()->back()->with('error', 'Chưa có hóa đơn để xuất file.');
    }

    // Dữ liệu tiện ích
    $electric_kwh = $bill->electric_kwh ?? 0;
    $electric_total = $bill->electric_total ?? 0;
    $water_m3 = $bill->water_m3 ?? 0;
    $water_total = $bill->water_total ?? 0;

    $electricPrice = $bill->electric_unit_price ?? 0;
    $waterPrice = $bill->water_price ?? 0;
    $rent_price = $bill->rent_price ?? 0;
    $occupants = $bill->water_occupants ?? 1;

    // Lấy dịch vụ phụ từ bảng room_bill_service
    $services = [];
    $service_total = 0;
    $billServices = RoomBillService::where('room_bill_id', $bill->id)->get();

    foreach ($billServices as $sv) {
        $service = \App\Models\Landlord\Service::find($sv->service_id);
        $services[] = [
            'name' => $service->name ?? 'Không rõ',
            'price' => $sv->price,
            'qty' => $sv->qty,
            'total' => $sv->total,
        ];
        $service_total += $sv->total;
    }

    $total = $rent_price + $electric_total + $water_total + $service_total;

    $data = [
        'room_name' => $room->room_number ?? $room->room_name ?? 'P101',
        'tenant_name' => $tenant->name ?? 'Chưa có',
        'area' => $room->area ?? 0,
        'rent_price' => $rent_price,
        'month' => $month,
        'electric_start' => $bill->electric_start ?? null,
        'electric_end' => $bill->electric_end ?? null,
        'electric_kwh' => $electric_kwh,
        'electric_price' => $electricPrice,
        'water_price' => $waterPrice,
        'electric_total' => $electric_total,
        'water_unit' => $bill->water_unit ?? null,
        'water_occupants' => $bill->water_occupants ?? null,
        'water_m3' => $water_m3,
        'water_total' => $water_total,
        'internet' => 0,
        'occupants' => $occupants,
        'services' => $services,
        'service_total' => $service_total,
        'total' => $total,
    ];

    return Excel::download(new RoomBillExport($room, $data), 'hoadon_' . $month . '.xlsx');
}


}
