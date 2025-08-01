<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Landlord\Room;
use Illuminate\Database\Eloquent\Model;

class RoomUser extends Model
{
    /** @use HasFactory<\Database\Factories\RoomUserFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'cccd',
        'room_id',
        'rental_id',
    ];
    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id', 'room_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function rentalAgreement()
    {
        return $this->belongsTo(RentalAgreement::class, 'rental_id');
    }
}
