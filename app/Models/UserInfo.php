<?php

namespace App\Models;

use App\Models\Landlord\Room;
use Illuminate\Database\Eloquent\Model;
use App\Models\Landlord\Staff\Rooms\RoomBill;

class UserInfo extends Model
{
    protected $table = 'user_infos';
    protected $primaryKey = 'id';
    protected $fillable = ['user_id', 'cccd', 'phone', 'email', 'room_id','rental_id' , 'full_name'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Quan hệ đến phòng
    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }
    public function rental()
    {
        return $this->belongsTo(RentalAgreement::class, 'rental_id');
    }
public function bills() {
    return $this->hasMany(RoomBill::class, 'room_id', 'room_id');
}
}
