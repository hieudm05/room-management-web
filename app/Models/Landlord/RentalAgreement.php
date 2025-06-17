<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Model;

class RentalAgreement extends Model
{
    protected $primaryKey = 'rental_id'; 
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = ['room_id', 'renter_id', 'landlord_id', 'start_date', 'end_date', 'rental_price', 'deposit', 'status', 'contract_file', 'agreement_terms', 'created_by'];
}
