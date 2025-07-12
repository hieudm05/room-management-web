<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class RoomEditRequest extends Model
{
    use HasFactory;

    protected $table = 'room_edit_requests';

    protected $fillable = [
        'room_id',
        'staff_id',
        'original_data',
        'requested_data',
        'status',
        'note',
    ];

    protected $casts = [
        'original_data' => 'array',
        'requested_data' => 'array',
    ];

    // Quan hệ với phòng
    public function room()
    {
        return $this->belongsTo(\App\Models\Landlord\Room::class, 'room_id');
    }

    // Quan hệ với nhân viên
    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }
}
