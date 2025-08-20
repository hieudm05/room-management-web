<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Model;
use App\Models\Landlord\Room;
use App\Models\User;
class RoomUsers extends Model
{
    protected $table = 'room_users';
     protected $primaryKey = 'id';
    protected $fillable = [
        'room_id',
        'rental_id',
        'user_id',
        'name',
        'email',
        'phone',
        'cccd',
        'started_at',
        'stopped_at',
        'is_active',
        'deposit_amount',
        'deduction_amount',
        'returned_amount',
        'deduction_reason',
        'deposit_status',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'started_at' => 'datetime',
        'stopped_at' => 'datetime',
    ];
    public function room()
{
    return $this->belongsTo(Room::class);
}

public function user()
{
    return $this->belongsTo(User::class, 'user_id');
}
public function rental()
{
    return $this->belongsTo(RentalAgreement::class, 'rental_id');
}

}