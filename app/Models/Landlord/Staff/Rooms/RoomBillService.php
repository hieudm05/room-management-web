<?php

namespace App\Models\Landlord\Staff\Rooms;

use Illuminate\Database\Eloquent\Model;

class RoomBillService extends Model
{
    //
    protected $table = 'room_bill_service';
protected $fillable = ['room_bill_id', 'service_id', 'price', 'qty', 'total'];

    public function roomBill()
{
    return $this->belongsTo(RoomBill::class);
}
}
