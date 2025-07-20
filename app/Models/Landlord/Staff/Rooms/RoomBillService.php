<?php

namespace App\Models\Landlord\Staff\Rooms;

use App\Models\Landlord\Service;
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
// App\Models\Landlord\Staff\Rooms\RoomBillService.php

public function service()
{
    return $this->belongsTo(Service::class, 'service_id');
}

}
