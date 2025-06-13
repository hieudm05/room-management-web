<?php

namespace App\Models\Landlord;

use App\Models\Landlord\Room;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $primaryKey = 'service_id';
    protected $fillable = ['name', 'description'];

    public function rooms()
    {
        return $this->belongsToMany(Room::class, 'room_services', 'service_id', 'room_id')->withPivot('is_free', 'price');
    }
}
