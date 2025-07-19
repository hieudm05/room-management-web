<?php

namespace App\Exports;

use App\Models\Landlord\Room;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RoomBillExport implements FromArray, WithHeadings, WithStyles
{
    protected $room;
    protected $data;

    public function __construct(Room $room, array $data)
    {
        $this->room = $room;
        $this->data = $data;
    }

    public function array(): array
    {
        // Chuẩn bị dữ liệu dịch vụ phụ
        $services = [];
        foreach ($this->data['services'] as $service) {
            $services[] = [
                'Tên dịch vụ' => $service['name'],
                'Giá' => number_format($service['price'], 0, ',', '.'),
                'Số lượng' => $service['qty'],
                'Tổng' => number_format($service['total'], 0, ',', '.'),
            ];
        }

        // Chuẩn bị dữ liệu chi phí phát sinh
        $additionalFees = [];
        foreach ($this->data['additional_fees'] as $fee) {
            $additionalFees[] = [
                'Tên chi phí' => $fee['name'] ?? 'N/A',
                'Giá' => number_format($fee['price'] ?? 0, 0, ',', '.'),
                'Số lượng' => $fee['qty'] ?? 1,
                'Tổng' => number_format($fee['total'] ?? 0, 0, ',', '.'),
            ];
        }

        // Xử lý cột "Nước cuối" dựa trên water_unit
        $waterEndLabel = $this->data['water_unit'] == 'per_person' 
            ? ($this->data['water_occupants'] ?? 0) . ' người' 
            : ($this->data['water_start'] + $this->data['water_m3']) . ' m³';

        return [
            [
                'Phòng' => $this->data['room_name'],
                'Khách thuê' => $this->data['tenant_name'],
                'Diện tích (m²)' => $this->data['area'],
                'Tiền thuê (VND)' => number_format($this->data['rent_price'], 0, ',', '.'),
                'Tháng' => $this->data['month'],
                'Điện đầu (kWh)' => $this->data['electric_start'] ?? 0,
                'Điện cuối (kWh)' => $this->data['electric_end'] ?? 0,
                'Điện dùng (kWh)' => $this->data['electric_kwh'] ?? 0,
                'Đơn giá điện (VND/kWh)' => number_format($this->data['electric_price'] ?? 3000, 0, ',', '.'),
                'Tiền điện (VND)' => number_format($this->data['electric_total'] ?? 0, 0, ',', '.'),
                'Nước đầu (m³)' => $this->data['water_unit'] == 'per_m3' ? ($this->data['water_start'] ?? 0) : 'N/A',
                'Nước cuối' => $waterEndLabel,
                'Nước dùng (m³)' => $this->data['water_unit'] == 'per_m3' ? ($this->data['water_m3'] ?? 0) : 'N/A',
                'Đơn giá nước (VND)' => $this->data['water_unit'] == 'per_person' 
                    ? number_format($this->data['water_price'] ?? 20000, 0, ',', '.') . '/người'
                    : number_format($this->data['water_price'] ?? 20000, 0, ',', '.') . '/m³',
                'Tiền nước (VND)' => number_format($this->data['water_total'] ?? 0, 0, ',', '.'),
                'Tổng tiền dịch vụ phụ (VND)' => number_format($this->data['service_total'] ?? 0, 0, ',', '.'),
                'Tổng chi phí phát sinh (VND)' => number_format($this->data['additional_fees_total'] ?? 0, 0, ',', '.'),
                'Tổng tiền (VND)' => number_format($this->data['total'] ?? 0, 0, ',', '.'),
            ],
            [],
            ['Dịch vụ phụ:'],
            ...$services,
            [],
            ['Chi phí phát sinh:'],
            ...$additionalFees,
        ];
    }

    public function headings(): array
    {
        return [
            'Phòng',
            'Khách thuê',
            'Diện tích (m²)',
            'Tiền thuê (VND)',
            'Tháng',
            'Điện đầu (kWh)',
            'Điện cuối (kWh)',
            'Điện dùng (kWh)',
            'Đơn giá điện (VND/kWh)',
            'Tiền điện (VND)',
            'Nước đầu (m³)',
            'Nước cuối',
            'Nước dùng (m³)',
            'Đơn giá nước (VND)',
            'Tiền nước (VND)',
            'Tổng tiền dịch vụ phụ (VND)',
            'Tổng chi phí phát sinh (VND)',
            'Tổng tiền (VND)',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // In đậm tiêu đề
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFCCCCCC']]],
            // Căn giữa các cột số
            'C:G' => ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]],
            'I:J' => ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]],
            'L:O' => ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]],
            'Q:R' => ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]],
            // Thêm viền cho toàn bộ bảng
            'A1:R' . ($sheet->getHighestRow()) => ['borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]],
            // In đậm dòng "Dịch vụ phụ" và "Chi phí phát sinh"
            3 => ['font' => ['bold' => true]],
            ($sheet->getHighestRow() - count($this->data['additional_fees']) - 1) => ['font' => ['bold' => true]],
        ];
    }
}