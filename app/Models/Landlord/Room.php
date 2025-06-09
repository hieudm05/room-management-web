<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $primaryKey = 'room_id';
    protected $fillable =
    [
        'property_id',
        'room_number',
        'area',
        'rental_price',
        'status',
        'occupants',
        'contract_file',
        'contract_pdf_file',
        'contract_word_file',
        'wifi_price_per_person',
        'water_price_per_person',
        'created_by'
    ];

    // Accessor tính tổng tiền
    public function getTotalWifiAttribute()
    {
        $wifi = $this->services->firstWhere('service_id', 3);
        if ($wifi && !$wifi->pivot->is_free && $wifi->pivot->price) {
            return $wifi->pivot->unit === 'per_room'
                ? $wifi->pivot->price
                : $this->occupants * $wifi->pivot->price;
        }
        return 0;
    }

    public function getTotalWaterAttribute()
    {
        $water = $this->services->firstWhere('service_id', 2);
        if ($water && !$water->pivot->is_free && $water->pivot->price) {
            return $water->pivot->unit === 'per_m3'
                ? 0 // chưa có số khối, bạn có thể cập nhật sau
                : $this->occupants * $water->pivot->price;
        }
        return 0;
    }

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
        return $this->belongsToMany(Service::class, 'room_services', 'room_id', 'service_id')
            ->withPivot('is_free', 'price', 'unit'); // thêm unit
    }
}
