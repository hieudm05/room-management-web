<?php

namespace App\Models;

use App\Models\RoomUtility;
use Illuminate\Database\Eloquent\Model;

class RoomUtilityPhoto extends Model
{
    protected $fillable = ['room_utility_id', 'image_path'];

    public function roomUtility()
    {
        return $this->belongsTo(RoomUtility::class);
    }
}
