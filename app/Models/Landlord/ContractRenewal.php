<?php

namespace App\Models\Landlord;

use App\Models\User;
use App\Models\Landlord\Room;
use Illuminate\Database\Eloquent\Model;

class ContractRenewal extends Model
{
     protected $fillable = [
        'room_id',
        'user_id',
        'status',
    ];
 public function room()
{
    return $this->belongsTo(Room::class, 'room_id', 'room_id');
}

public function user()
{
    return $this->belongsTo(User::class);
}
}
