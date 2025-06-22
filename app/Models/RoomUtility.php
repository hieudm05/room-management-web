<?php

namespace App\Models;

use App\Models\Landlord\Room;
use Illuminate\Database\Eloquent\Model;

class RoomUtility extends Model
{
    protected $table = 'room_utilities';
    protected $primaryKey = 'id';
    protected $fillable = [
        'room_id',
        'start_date', 'end_date',
        'electric_start', 'electric_end', 'electric_kwh', 'electricity',
        'water_unit', 'water_occupants', 'water_m3', 'water',
        'images'
    ];

    protected $casts = [
        'images' => 'array',
    ];

    public function room()
{
    return $this->belongsTo(Room::class, 'room_id', 'room_id');
}
public function photos()
{
    return $this->hasMany(RoomUtilityPhoto::class);
}
}
