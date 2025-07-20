<?php

namespace App\Exports;

use App\Models\Landlord\Room;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\Comment;
use PhpOffice\PhpSpreadsheet\Style\Conditional;

class RoomBillExport implements FromArray, WithHeadings, WithStyles, WithEvents
{
    protected $room;
    protected $data;
    protected $totalRow;

    public function __construct(Room $room, array $data)
    {
        $this->room = $room;
        $this->data = $data;
    }

    public function array(): array
    {
        $rows = [];

        // Tiêu đề chính
        $rows[] = ['HÓA ĐƠN THANH TOÁN PHÒNG TRỌ', '', '', '', '', ''];

        // Thông tin hóa đơn
        $rows[] = ['THÔNG TIN HÓA ĐƠN', '', '', '', '', ''];
        $rows[] = ['Phòng', $this->data['room_name'], '', 'Địa chỉ phòng', $this->data['room_address'] ?? 'Không có thông tin', ''];
        $rows[] = ['Khách thuê', $this->data['tenant_name'], '', 'SĐT khách thuê', $this->data['tenant_phone'] ?? 'Không có thông tin', ''];
        $rows[] = ['Diện tích (m²)', $this->data['area'], '', 'Tiền thuê mặc định (VND)', $this->data['default_rent_price'] ?? $this->data['rent_price'], ''];
        $rows[] = ['Tháng', $this->data['month'], '', '', '', ''];
        $rows[] = [];

        // Tiền thuê phòng
        $rows[] = ['TIỀN THUÊ PHÒNG', '', '', '', '', ''];
        $rows[] = ['Tiền thuê (VND)', $this->data['rent_price'], '', '', '', ''];
        $rows[] = [];

        // Tiền điện
        $rows[] = ['TIỀN ĐIỆN', '', '', '', '', ''];
        $rows[] = ['Chỉ số đầu (kWh)', $this->data['electric_start'], '', 'Chỉ số cuối (kWh)', $this->data['electric_end'], ''];
        $rows[] = ['Số điện tiêu thụ (kWh)', '=D12-B12', '', 'Đơn giá điện (VND/kWh)', $this->data['electric_price'], ''];
        $rows[] = ['Thành tiền điện (VND)', '=B13*E13', '', '', '', ''];
        $rows[] = [];

        // Tiền nước
        $rows[] = ['TIỀN NƯỚC', '', '', '', '', ''];
        $rows[] = ['Đơn vị tính', $this->data['water_unit'] == 'per_m3' ? 'Theo số khối' : 'Theo số người', '', '', '', ''];
        if ($this->data['water_unit'] == 'per_m3') {
            $rows[] = ['Chỉ số đầu (m³)', $this->data['water_start'], '', 'Chỉ số cuối (m³)', '=B17+' . $this->data['water_m3'], ''];
            $rows[] = ['Số nước tiêu thụ (m³)', $this->data['water_m3'], '', '', '', ''];
            $rows[] = ['Đơn giá nước (VND)', $this->data['water_price'], '', 'Thành tiền nước (VND)', '=B18*B19', ''];
        } else {
            $rows[] = ['Số người sử dụng', $this->data['water_occupants'], '', '', '', ''];
            $rows[] = ['Đơn giá nước (VND)', $this->data['water_price'], '', 'Thành tiền nước (VND)', '=B18*B19', ''];
        }
        $rows[] = [];

        // Chi phí dịch vụ phụ
        $serviceStartRow = count($rows) + 2;
        if (!empty($this->data['services'])) {
            $rows[] = ['CHI PHÍ DỊCH VỤ PHỤ', '', '', '', '', ''];
            $rows[] = ['Tên dịch vụ', 'Giá (VND)', 'Số lượng', 'Tổng (VND)', '', ''];
            foreach ($this->data['services'] as $sv) {
                $rows[] = [
                    $sv['name'],
                    $sv['price'],
                    $sv['qty'],
                    '=B' . (count($rows) + 1) . '*C' . (count($rows) + 1),
                    '',
                    ''
                ];
            }
            $rows[] = ['Tổng chi phí dịch vụ', '', '', '=SUM(D' . $serviceStartRow . ':D' . (count($rows)) . ')', '', ''];
            $rows[] = [];
        }

        // Chi phí phát sinh
        $feeStartRow = count($rows) + 2;
        if (!empty($this->data['additional_fees'])) {
            $rows[] = ['CHI PHÍ PHÁT SINH', '', '', '', '', ''];
            $rows[] = ['Tên chi phí', 'Giá (VND)', 'Số lượng', 'Tổng (VND)', '', ''];
            foreach ($this->data['additional_fees'] as $fee) {
                $rows[] = [
                    $fee['name'],
                    $fee['price'],
                    $fee['qty'],
                    '=B' . (count($rows) + 1) . '*C' . (count($rows) + 1),
                    '',
                    ''
                ];
            }
            $rows[] = ['Tổng chi phí phát sinh', '', '', '=SUM(D' . $feeStartRow . ':D' . (count($rows)) . ')', '', ''];
            $rows[] = [];
        }

        // Tổng thanh toán
        $this->totalRow = count($rows) + 2;
        $rows[] = ['TỔNG THANH TOÁN', '', '', '', '', ''];
        $rows[] = ['Tổng cộng (VND)', '=B10+B14+' . ($this->data['water_unit'] == 'per_m3' ? 'E19' : 'E18') . (empty($this->data['services']) ? '' : '+D' . ($serviceStartRow + count($this->data['services'])) . '') . (empty($this->data['additional_fees']) ? '' : '+D' . ($feeStartRow + count($this->data['additional_fees'])) . ''), '', '', '', ''];

        // Ghi chú
        $rows[] = [];
        $rows[] = ['GHI CHÚ', '', '', '', '', ''];
        $rows[] = ['Hạn thanh toán: 05/' . $this->data['month'], '', '', '', '', ''];
        $rows[] = ['Vui lòng thanh toán qua chuyển khoản hoặc tiền mặt. Liên hệ chủ nhà trọ nếu có thắc mắc.', '', '', '', '', ''];

        return $rows;
    }

    public function headings(): array
    {
        return [];
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();
        $highestColumn = 'F';

        // Định dạng tiêu đề chính
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 18, 'color' => ['argb' => 'FF003087']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFCCE5FF']],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(40);

        // Định dạng các tiêu đề khu vực
        $sectionHeaders = [
            'THÔNG TIN HÓA ĐƠN', 'TIỀN THUÊ PHÒNG', 'TIỀN ĐIỆN', 'TIỀN NƯỚC',
            'CHI PHÍ DỊCH VỤ PHỤ', 'CHI PHÍ PHÁT SINH', 'TỔNG THANH TOÁN', 'GHI CHÚ'
        ];
        foreach (range(1, $highestRow) as $row) {
            $cellA = $sheet->getCell("A$row")->getValue();
            if (in_array($cellA, $sectionHeaders)) {
                $sheet->mergeCells("A$row:F$row");
                $sheet->getStyle("A$row:F$row")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14, 'color' => ['argb' => 'FFFFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF003087']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension($row)->setRowHeight(30);
            }

            // Định dạng dòng tổng
            if (strpos($cellA, 'Tổng') === 0) {
                $sheet->mergeCells("A$row:C$row");
                $sheet->mergeCells("D$row:F$row");
                $sheet->getStyle("A$row:F$row")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12, 'color' => ['argb' => 'FF000000']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFCCFFCC']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                ]);
            }
        }

        // Định dạng bảng dịch vụ và chi phí phát sinh
        $tableHeaders = ['Tên dịch vụ', 'Tên chi phí'];
        foreach (range(1, $highestRow) as $row) {
            if (in_array($sheet->getCell("A$row")->getValue(), $tableHeaders)) {
                $sheet->getStyle("A$row:D$row")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12, 'color' => ['argb' => 'FF000000']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFE6F0FA']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
            }
        }

        // Kẻ viền
        $sheet->getStyle("A1:{$highestColumn}{$highestRow}")->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF000000']]],
        ]);

        // Điều chỉnh chiều rộng cột
        $sheet->getColumnDimension('A')->setWidth(35);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(25);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(15);

        // Giãn dòng
        for ($i = 1; $i <= $highestRow; $i++) {
            if ($sheet->getRowDimension($i)->getRowHeight() == -1) {
                $sheet->getRowDimension($i)->setRowHeight(25);
            }
        }

        // Font mặc định
        $sheet->getStyle("A1:{$highestColumn}{$highestRow}")->getFont()->setName('Arial')->setSize(12);

        // Định dạng số tiền
        $sheet->getStyle("B6:B$highestRow")->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle("D6:D$highestRow")->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle("E6:E$highestRow")->getNumberFormat()->setFormatCode('#,##0');

        // Định dạng có điều kiện (nếu tổng tiền > 5 triệu, tô màu đỏ nhạt)
        $conditional = new Conditional();
        $conditional->setConditionType(Conditional::CONDITION_CELLIS);
        $conditional->setOperatorType(Conditional::OPERATOR_GREATERTHAN);
        $conditional->addCondition(5000000);
        $conditional->getStyle()->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFCCCC');
        $sheet->getConditionalStyles("B{$this->totalRow}")->add($conditional);
    }

    public function registerEvents(): array
    {
        return [
            \Maatwebsite\Excel\Events\AfterSheet::class => function (\Maatwebsite\Excel\Events\AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Thêm ghi chú (comments)
                $sheet->getComment('A1')->getText()->createTextRun('Hóa đơn phòng trọ')->setBold(true);
                $sheet->getComment('B14')->getText()->createTextRun('Tổng tiền điện được tính bằng công thức: (Chỉ số cuối - Chỉ số đầu) * Đơn giá');
                if ($this->data['water_unit'] == 'per_m3') {
                    $sheet->getComment('E19')->getText()->createTextRun('Tổng tiền nước được tính bằng công thức: Số nước tiêu thụ * Đơn giá');
                } else {
                    $sheet->getComment('E18')->getText()->createTextRun('Tổng tiền nước được tính bằng công thức: Số người * Đơn giá');
                }
            },
        ];
    }
}