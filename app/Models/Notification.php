<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'title',
        'message',
        'type',
        'link',
        'created_at',
        'expired_at',
        'is_global',
    ];

    public $timestamps = false;
    
 
   public function users()
{
    return $this->belongsToMany(User::class, 'notification_user')
                ->withPivot(['is_read', 'read_at', 'received_at', 'created_at', 'updated_at'])
                ->withTimestamps();
}
}