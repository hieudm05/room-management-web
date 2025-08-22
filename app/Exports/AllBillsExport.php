<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Carbon\Carbon;

class AllBillsExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, \Maatwebsite\Excel\Concerns\WithCustomStartCell

{
    protected $bills;

    public function __construct($bills)
    {
        $this->bills = $bills;
    }

    public function collection()
    {
        return $this->bills->map(function ($bill) {
            return [
                $bill->id,
                $bill->room_id,
                $bill->tenant_name,
                $bill->area,
                number_format($bill->rent_price, 0, ',', '.') . ' VNĐ',
                $bill->electric_start,
                $bill->electric_end,
                $bill->electric_kwh,
                number_format($bill->electric_unit_price, 0, ',', '.') . ' VNĐ',
                number_format($bill->electric_total, 0, ',', '.') . ' VNĐ',
                $bill->water_start,
                $bill->water_end,
                $bill->water_m3,
                number_format($bill->water_price, 0, ',', '.') . ' VNĐ',
                number_format($bill->water_total, 0, ',', '.') . ' VNĐ',
                number_format($bill->total, 0, ',', '.') . ' VNĐ',
                number_format($bill->complaint_user_cost, 0, ',', '.') . ' VNĐ',
                number_format($bill->complaint_landlord_cost, 0, ',', '.') . ' VNĐ',
                $bill->created_at ? $bill->created_at->format('d/m/Y') : '',
                $bill->payment_time ? Carbon::parse($bill->payment_time)->format('d/m/Y H:i') : '',
                $bill->status == 'paid' ? 'Đã thanh toán' : ($bill->status == 'pending' ? 'Đang xử lý' : 'Chưa thanh toán'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Mã HĐ',
            'Mã Phòng',
            'Tên Khách Thuê',
            'Diện Tích (m²)',
            'Tiền Phòng',
            'Chỉ Số Điện Đầu',
            'Chỉ Số Điện Cuối',
            'Số KWh',
            'Giá Điện/KWh',
            'Tiền Điện',
            'Chỉ Số Nước Đầu',
            'Chỉ Số Nước Cuối',
            'Số m³',
            'Giá Nước/m³',
            'Tiền Nước',
            'Tổng Cộng',
            'Chi Phí Khiếu Nại (Người Thuê)',
            'Chi Phí Khiếu Nại (Chủ Nhà)',
            'Ngày Lập',
            'Thời Gian Thanh Toán',
            'Trạng Thái',
        ];
    }
       public function startCell(): string
    {
        return 'A2'; // Headings bắt đầu ở hàng 2
    }

    public function styles(Worksheet $sheet)
    {
        // Gộp tiêu đề
        $sheet->mergeCells('A1:U1');
        $sheet->setCellValue('A1', 'DANH SÁCH HÓA ĐƠN PHÒNG TRỌ');

        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['rgb' => '2F75B5'] // Xanh thương hiệu
            ]
        ]);
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Header bảng
        $sheet->getStyle('A2:U2')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '4BACC6']],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true
            ],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(30);

        // Căn phải các cột tiền
        foreach (['E','I','J','N','O','P','Q','R'] as $col) {
            $sheet->getStyle($col . '3:' . $col . ($this->bills->count() + 2))
                  ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }

        // Căn giữa ngày tháng và trạng thái
        foreach (['S','T','U'] as $col) {
            $sheet->getStyle($col . '3:' . $col . ($this->bills->count() + 2))
                  ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        // Border toàn bộ
        $sheet->getStyle('A2:U' . ($this->bills->count() + 2))->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);

        // In đậm dòng tổng cộng nếu có
        $lastRow = $sheet->getHighestRow();
        for ($row = 3; $row <= $lastRow; $row++) {
            $cellValue = $sheet->getCell("A$row")->getValue();
            if (stripos($cellValue, 'Tổng') !== false) {
                $sheet->getStyle("A$row:U$row")->getFont()->setBold(true)->setColor(['rgb' => 'FF0000']);
            }
        }
    }
}
