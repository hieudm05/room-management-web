<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class RoomBillExport implements FromView, WithEvents
{
    protected $data;
    protected $room;

    public function __construct($room, $data)
    {
        $this->room = $room;
        $this->data = $data;
    }

    public function view(): View
    {
        return view('landlord.Staff.rooms.excel.Contract', [
            'room' => $this->room,
            'data' => $this->data,
        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet;

                // Font, căn giữa, border cho toàn bộ từ A2 đến K4
                $sheet->getStyle('A2:K4')->applyFromArray([
                    'font' => ['name' => 'Arial', 'size' => 12],
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center', 'wrapText' => true],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);

                // Tiêu đề (A2:K2) màu xanh #99FF00
                $sheet->getStyle('A2:K2')->getFill()
                    ->setFillType('solid')->getStartColor()->setRGB('99FF00');

                // Header hàng 3 (A3:K3) màu xanh #99FF00
                $sheet->getStyle('A3:K3')->getFill()
                    ->setFillType('solid')->getStartColor()->setRGB('99FF00');

                // In đậm riêng ô B3, C3
                $sheet->getStyle('B3:C3')->getFont()->setBold(true);

                // Dòng dữ liệu (hàng 4)
                // B4, C4 màu cam nhạt #FFD580
                $sheet->getStyle('C4:D4')->getFill()
                    ->setFillType('solid')->getStartColor()->setRGB('FFD580');

                // E4 màu hồng nhạt #FFB6C1
                $sheet->getStyle('E4')->getFill()
                    ->setFillType('solid')->getStartColor()->setRGB('FFD580');

                // F4, G4, H4, I4, J4 màu vàng nhạt #FFFF99
                foreach (['F4','G4','H4','I4','J4'] as $cell) {
                    $sheet->getStyle($cell)->getFill()
                        ->setFillType('solid')->getStartColor()->setRGB('FFFF99');
                }
            },
        ];
    }
}
