<?php

namespace App\Models;

use App\Models\Landlord\Room;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'user_id',
        'check_in',
        'note',
        'status',
        'confirmed_by',
        'proof_image',
        'guest_name',
        'phone',
        'room_id',
        'created_at',
    ];

    protected $casts = [
        'check_in' => 'datetime',
    ];

    public function post()
    {
        return $this->belongsTo(StaffPost::class);
    }
    public function room()
{
    return $this->belongsTo(Room::class, 'room_id');
}
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function confirmer()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }
    public function info()
    {
        return $this->hasOne(UserInfo::class);
    }
}
