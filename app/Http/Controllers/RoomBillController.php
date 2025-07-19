<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

namespace App\Http\Controllers;

use App\Models\Landlord\Staff\Rooms\RoomBill as RoomsRoomBill;
use Illuminate\Http\Request;
use App\Models\RoomBill;

class RoomBillController extends Controller
{
    public function markPending(Request $request, $id)
{
    $bill = RoomsRoomBill::findOrFail($id);

    $request->validate([
        'payment_time' => 'required|date',
        'receipt_image' => 'required|image|max:2048',
    ]);

    $path = $request->file('receipt_image')->store('receipts', 'public');

    $bill->status = 'pending';
    $bill->payment_time = $request->payment_time;
    $bill->receipt_image = $path;
    $bill->save();

    return back()->with('success', 'Thông tin thanh toán đã được gửi để xác nhận.');
}

}

