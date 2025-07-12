<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Landlord\Room;

class UserInfo extends Model
{
    protected $table = 'user_infos';
    protected $primaryKey = 'id';
    protected $fillable = ['user_id', 'cccd', 'phone', 'email', 'room_id', 'full_name'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Quan hệ đến phòng
    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }
}
