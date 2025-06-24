<?php

namespace App\Models\Landlord;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Approval extends Model
{
    use HasFactory;

    protected $table = 'approvals';

    protected $fillable = [
        'room_id',
        'staff_id',
        'landlord_id',
        'rental_price',
        'deposit',
        'type',
        'file_path',
        'note',
        'status',
    ];

    /**
     * Người gửi yêu cầu duyệt (Staff).
     */
    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    /**
     * Phòng trọ liên quan đến yêu cầu.
     */
    public function room()
    {
        return $this->belongsTo(\App\Models\Landlord\Room::class, 'room_id', 'room_id');
    }
}
