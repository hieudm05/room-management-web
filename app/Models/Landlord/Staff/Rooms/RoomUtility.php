<?php

namespace App\Models\Landlord\Staff\Rooms;
use App\Models\Landlord\Room;
use Illuminate\Database\Eloquent\Model;


class RoomUtility extends Model
{
    protected $table = 'room_utilities';
    protected $primaryKey = 'id';

    protected $fillable = [
        'room_id',
        'start_date',
        'end_date',
        'electric_start',
        'electric_end',
        'electric_kwh',
        'electricity',
        'water_unit',
        'water_occupants',
         'water_start',
        'water_m3',
        'water',
        // 'images'
    ];

    // protected $casts = [
    //     'images' => 'array',
    // ];

    // Quan hệ photos
    public function photos()
    {
        return $this->hasMany(RoomUtilityPhoto::class, 'room_utility_id');
    }
}
