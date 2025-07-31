<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Landlord\Room;
use App\Models\User;

class RoomUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'cccd',
        'room_id',
        'rental_id',
        'user_id', // Thêm dòng này
    ];

    /**
     * Người thuê thuộc về một phòng trọ.
     */
    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id', 'room_id');
    }

    /**
     * Người thuê có thể là một user (nếu họ có tài khoản trong hệ thống).
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
