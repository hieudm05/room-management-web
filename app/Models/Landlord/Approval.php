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
        'user_id',
        'rental_id',
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
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id', 'room_id');
    }
    public function rental()
    {
        return $this->belongsTo(RentalAgreement::class, 'rental_id');
    }
}
