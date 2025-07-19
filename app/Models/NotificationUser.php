<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationUser extends Model
{
    protected $table = 'notification_user';

    protected $fillable = [
        'notification_id',
        'user_id',
        'is_read',
        'read_at',
        'received_at',
    ];

    public $timestamps = false;
    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'received_at' => 'datetime',
    ];
    public function notification()
    {
        return $this->belongsTo(Notification::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}