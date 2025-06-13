<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    protected $primaryKey = 'facility_id';
    protected $fillable = ['name', 'icon'];

    public function rooms()
    {
        return $this->belongsToMany(Room::class, 'room_facilities', 'facility_id', 'room_id');
    }
}
