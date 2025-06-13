<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Model;

class RoomFacility extends Model
{
    protected $table = 'room_facilities';
    public $timestamps = false;
    protected $fillable = ['room_id', 'facility_id'];
}