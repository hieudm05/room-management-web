<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Model;

class RoomPhoto extends Model
{
    protected $primaryKey = 'photo_id';
    protected $fillable = ['room_id', 'image_url'];

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }
}
