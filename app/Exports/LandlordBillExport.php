<?php
namespace App\Exports;

use App\Models\RoomBill;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class LandlordBillExport implements FromView
{
    protected $month;

    public function __construct($month)
    {
        $this->month = $month;
    }

    public function view(): View
    {
        $bills = RoomBill::with('room.property', 'room.rentalAgreement.renter')
            ->where('month', 'like', $this->month . '%')
            ->get();

        return view('exports.landlord_bills', [
            'bills' => $bills,
            'month' => $this->month
        ]);
    }
}
