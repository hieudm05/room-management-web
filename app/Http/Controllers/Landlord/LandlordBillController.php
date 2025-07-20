<?php
namespace App\Http\Controllers\Landlord;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Landlord\Property;
use App\Models\Landlord\Staff\Rooms\RoomBill;

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
    $target = \Carbon\Carbon::parse($bill->month); // sửa chỗ này để tránh trailing data

    $bill->load([
        'room.property',
        'room.rentalAgreement.renter',
        'room.complaints' => function ($query) use ($target) {
            $query->where('status', 'resolved')
                  ->whereMonth('updated_at', $target->month)
                  ->whereYear('updated_at', $target->year);
        },
        'additionalFees',
        'utilityPhotos',
    ]);

    return view('landlord.bills.show', compact('bill'));
}



}
