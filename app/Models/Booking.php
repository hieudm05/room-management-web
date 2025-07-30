<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    ];

    protected $casts = [
        'check_in' => 'datetime',
    ];

    public function post()
    {
        return $this->belongsTo(StaffPost::class);
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
