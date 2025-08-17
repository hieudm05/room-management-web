<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\Landlord\Room;
use App\Models\Landlord\Staff\Rooms\RoomBill ;

class RoomLeaveLog extends Model
{
    protected $table = 'room_leave_logs';

    protected $fillable = [
        'room_id',
        'user_id',
        'rental_id',
        'leave_date', // Date when the user plans to leave
        'previous_renter_id', // ID of the previous renter
        'new_renter_id', // ID of the new renter if this is a transfer request
        'action_type', // 'leave' or 'transfer'
        'status', // Status of the request
        'reason', // Reason for leaving
        'note', // Additional notes
        'handled_by', // ID of the staff who handled the request
        'handled_at', // Timestamp when the request was handled
        'reject_reason', // Reason for rejection if applicable
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }
  public function roomBills()
{
    return $this->hasMany(RoomBill::class, 'room_id', 'room_id');
}
public function rental()
{
    return $this->belongsTo(RentalAgreement::class, 'rental_id', 'rental_id');
}
}
