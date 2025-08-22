<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Landlord\Room;

class UserInfo extends Model
{
    protected $table = 'user_infos';
    protected $primaryKey = 'id';
    protected $fillable = [
        'full_name',
        'user_id',
        'tenant_id',
        'room_id',
        'rental_id',
        'active',
        'left_at',
        'cccd',
        'phone',
        'email',
        'days_stayed',

    ];

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
        return $this->belongsTo(RentalAgreement::class, 'rental_id', 'rental_id');
    }
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
