<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $fillable = [
        'full_name', 'phone_number', 'identity_number',
        'dob', 'gender', 'address', 'job', 'avatar', 'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
