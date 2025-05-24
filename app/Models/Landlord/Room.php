<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $primaryKey = 'room_id';
    protected $fillable = ['property_id', 'room_number', 'area', 'rental_price', 'status'];

    public function facilities()
    {
        return $this->belongsToMany(Facility::class, 'room_facilities', 'room_id', 'facility_id');
    }

    public function rentalAgreements()
    {
        return $this->hasMany(RentalAgreement::class, 'room_id');
    }

    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id');
    }

    public function photos()
    {
        return $this->hasMany(RoomPhoto::class, 'room_id');
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'room_services', 'room_id', 'service_id')->withPivot('is_free', 'price');
    }
}
