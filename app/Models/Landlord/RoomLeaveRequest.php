<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Landlord\Room;
use App\Models\Landlord\RentalAgreement;

class RoomLeaveRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'user_id',
        'rental_agreement_id',
        'status',
        'leave_date',
        'note',
        'staff_id',
        'action_type', // 'leave' or 'transfer'
        'landlord_id', // ID of the landlord who owns the room
        'landlord_status', // Status of the request from the landlord's perspective
         'staff_status',
        'handled_by', // ID of the staff who handled the request
        'handled_at', // Timestamp when the request was handled
        'new_renter_id', // ID of the new renter if this is a transfer request
        'reject_reason', // Reason for rejection if applicable
         'created_at',
        'updated_at',

    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function rentalAgreement()
    {
        return $this->belongsTo(RentalAgreement::class, 'rental_agreement_id');
    }
    public function newRenter()
    {
        return $this->belongsTo(User::class, 'new_renter_id');
    }
    public function landlord()
{
    return $this->belongsTo(User::class, 'landlord_id');
}
}