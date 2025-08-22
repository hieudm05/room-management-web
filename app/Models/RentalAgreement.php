<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Landlord\Room;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class RentalAgreement extends Model
{
    use HasFactory;

    protected $primaryKey = 'rental_id';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = ['room_id', 'renter_id', 'landlord_id', 'start_date', 'end_date', 'rental_price', 'deposit', 'status', 'contract_file', 'agreement_terms', 'created_by'];


    public function renter()
    {
        return $this->belongsTo(User::class, 'renter_id');
    }

    public function room()
{
    return $this->belongsTo(Room::class, 'room_id', 'room_id');
}
 public function leaveRequests()
{
    return $this->hasMany(RoomLeaveRequest::class, 'rental_agreement_id');
}
}





