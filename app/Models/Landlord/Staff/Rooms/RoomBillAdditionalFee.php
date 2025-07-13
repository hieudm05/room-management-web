<?php

namespace App\Models\Landlord\Staff\Rooms;

use Illuminate\Database\Eloquent\Model;

class RoomBillAdditionalFee extends Model
{
    protected $table = 'room_bill_additional_fees';

    protected $fillable = [
        'room_bill_id',
        'name',
        'price',
        'qty',
        'total',
    ];

    public function roomBill()
    {
        return $this->belongsTo(RoomBill::class);
    }
}