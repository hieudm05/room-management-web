<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CommonIssue;
use App\Models\Landlord\Property;
use App\Models\Landlord\Room;
use App\Models\Landlord\Staff\Rooms\RoomBill;

class Complaint extends Model
{
      protected $fillable = [
        'property_id',
        'room_id',
        'full_name',
        'phone',
        'common_issue_id',
        'detail',
        'staff_id',
        'user_id',
        'user_cost',
        'landlord_cost',
        'note',
        'resolved_at',
        'reject_reason',
        'main_photo',
        'photo_album',
        'handled_by',
        'status',
         'rejected_staff_ids' => 'array',
        'room_bill_id', // Added field for room_bill_id
    ];

   public function property()
{
    return $this->belongsTo(Property::class, 'property_id', 'property_id');
}

public function room()
{
    return $this->belongsTo(Room::class, 'room_id', 'room_id');
}

public function commonIssue()
{
    return $this->belongsTo(CommonIssue::class, 'common_issue_id');
}
public function photos()
   {
        return $this->hasMany(ComplaintPhoto::class, 'complaint_id');
    }
    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }
public function resolvedPhotos()
{
    return $this->hasMany(ComplaintPhoto::class)->where('type', 'resolved');
}
public function bill()
{
    return $this->belongsTo(RoomBill::class, 'room_bill_id');
}


}