<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Model;
use App\Models\Landlord\Room;

class Service extends Model
{
    protected $primaryKey = 'service_id';

    protected $fillable = [
        'name',
        'description',
        'is_hidden',
    ];

    protected $casts = [
        'is_hidden' => 'boolean',
    ];

    public function rooms()
    {
        return $this->belongsToMany(Room::class, 'room_services', 'service_id', 'room_id')
                    ->withPivot('is_free', 'price');
    }
}
