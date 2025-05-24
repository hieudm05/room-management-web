<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Model;

class RoomService extends Model
{
    protected $table = 'room_services';
    public $timestamps = false;
    protected $fillable = ['room_id', 'service_id', 'is_free', 'price'];
}
