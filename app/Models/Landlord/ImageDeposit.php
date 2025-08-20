<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Model;

class ImageDeposit extends Model
{
    protected $table = 'image_deposit';

    protected $fillable = [
        'room_id',
        'rental_id',
        'image_url',
    ];

    // Một image_deposit thuộc về 1 phòng
    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    // Một image_deposit thuộc về 1 hợp đồng
   public function rentalAgreement()
    {
        return $this->hasOne(RentalAgreement::class, 'deposit_id', 'id');
    }
}
