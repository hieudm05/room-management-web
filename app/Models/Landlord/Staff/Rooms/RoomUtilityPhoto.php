<?php

namespace App\Models\Landlord\Staff\Rooms;

use App\Models\Landlord\Staff\Rooms\RoomUtility;
use Illuminate\Database\Eloquent\Model;


class RoomUtilityPhoto extends Model
{
    protected $table = 'room_utility_photos';
    protected $primaryKey = 'id';
    protected $fillable = ['room_bill_id', 'type','image_path'];

    // public function utility()
    // {
    //     return $this->belongsTo(RoomUtility::class, 'room_utility_id');
    // }
    public function roomBill()
{
    return $this->belongsTo(RoomBill::class, 'room_bill_id');
}
}

