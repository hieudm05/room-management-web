<?php
namespace App\Http\Controllers\Landlord\Staff;

use App\Http\Controllers\Controller;
use App\Models\Landlord\RentalAgreement;
use App\Models\Landlord\Room;
use App\Models\Landlord\Staff\Rooms\RoomUtility;
use App\Models\RoomUser;
use App\Models\User;
use Illuminate\Http\Request;
// use PhpOffice\PhpWord\IOFactory;
use Smalot\PdfParser\Parser;

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
                        $services[] = [
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
            'eletric_price' => $eletricPrice,
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

        return view('landlord.Staff.rooms.bills.index', compact('room', 'data', 'noBill'));
    }

}