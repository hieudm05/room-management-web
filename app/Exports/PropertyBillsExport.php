<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;

class PropertyBillsExport implements FromArray, WithHeadings, WithStyles, WithEvents, WithTitle, WithColumnWidths
{
    protected $bills;
    protected $month;

    public function __construct($bills, $month)
    {
        $this->bills = $bills;
        $this->month = $month;
    }

    public function array(): array
    {
        $rows = [];
        $total = 0;
        foreach ($this->bills as $i => $item) {
            $room = $item['room'];
            $bill = $item['bill'];
            // Xác định trạng thái thanh toán
            $paymentStatus = ($bill->status == 'unpaid') ? 'Chưa thanh toán' : 'Đã thanh toán';
            $rows[] = [
                $i + 1,
                $room->room_number ?? $room->room_name ?? '',
                $bill->electric_start ?? '',
                $bill->electric_end ?? '',
                $bill->electric_kwh ?? '',
                $bill->water_m3 ?? '',
                $bill->rent_price ?? '',
                $bill->electric_total ?? '',
                $bill->water_total ?? '',
                $item['service_total'] ?? '',
                $item['total'] ?? '',
                $paymentStatus, // Cột thanh toán
            ];
            $total += $item['total'] ?? 0;
        }
        // Thêm dòng tổng doanh thu
        $rows[] = [
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            'TỔNG DOANH THU',
            number_format($total)
        ];
        return $rows;
    }

    public function headings(): array
    {
        return [
            ['QUẢN LÝ TIỀN ĐIỆN/NƯỚC/PHÒNG TRỌ THÁNG ' . $this->month], // Tiêu đề lớn
            [
                'STT',
                'Phòng',
                'Điện cũ',
                'Điện mới',
                'Số điện',
                'Số nước',
                'Tiền phòng',
                'Tiền điện',
                'Tiền nước',
                'Dịch vụ',
                'Tổng cộng',
                'Thanh toán'
            ]
        ];
    }

    public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
    {
        // Tiêu đề lớn
        $sheet->mergeCells('A1:L1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

        // Header
        $sheet->getStyle('A2:L2')->getFont()->setBold(true);
        $sheet->getStyle('A2:L2')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A2:L2')->getFill()->setFillType('solid')->getStartColor()->setARGB('FFFFCC00');

        // Dòng tổng doanh thu
        $lastRow = count($this->bills) + 3;
        $sheet->getStyle("K{$lastRow}:L{$lastRow}")->getFont()->setBold(true)->setSize(13);
        $sheet->getStyle("K{$lastRow}:L{$lastRow}")->getFill()->setFillType('solid')->getStartColor()->setARGB('FFCCFFCC');
        $sheet->getStyle("K{$lastRow}:L{$lastRow}")->getAlignment()->setHorizontal('center');
    }

    public function registerEvents(): array
    {
        return [
            \Maatwebsite\Excel\Events\AfterSheet::class => function ($event) {
                // Căn giữa toàn bộ bảng
                $event->sheet->getStyle('A2:L' . ($event->sheet->getHighestRow()))
                    ->getAlignment()->setHorizontal('center')->setVertical('center');
                // Set border cho bảng
                $event->sheet->getStyle('A2:L' . ($event->sheet->getHighestRow()))
                    ->getBorders()->getAllBorders()->setBorderStyle('thin');
            },
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,
            'B' => 10,
            'C' => 10,
            'D' => 10,
            'E' => 10,
            'F' => 10,
            'G' => 15,
            'H' => 15,
            'I' => 15,
            'J' => 15,
            'K' => 18,
            'L' => 18,
        ];
    }

    public function title(): string
    {
        return 'Tổng hợp hóa đơn';
    }


}