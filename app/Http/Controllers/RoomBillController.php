<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

namespace App\Http\Controllers;

use App\Models\Landlord\Staff\Rooms\RoomBill as RoomsRoomBill;
use Illuminate\Http\Request;
use App\Models\RoomBill;

class RoomBillController extends Controller
{
    public function markPending($id)
    {
        $bill = RoomsRoomBill::findOrFail($id);

        // Nếu bill đã thanh toán thì không cho cập nhật
        if ($bill->status === 'paid') {
            return back()->with('error', 'Hóa đơn đã thanh toán.');
        }

        $bill->status = 'pending';
        $bill->save();

        return back()->with('success', 'Cập nhật trạng thái hóa đơn thành công!');
    }
}

