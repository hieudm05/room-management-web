<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepositRefund extends Model
{
    use HasFactory;

    protected $table = 'deposit_refunds';
    protected $primaryKey = 'id';

    protected $fillable = [
        'rental_id',
        'user_id',
        'amount',
        'refund_date',
        'status',
    ];

    /**
     * Quan hệ tới hợp đồng thuê (rental agreement)
     */
    public function rentalAgreement()
    {
        return $this->belongsTo(\App\Models\RentalAgreement::class, 'rental_id', 'rental_id');
    }

    /**
     * Quan hệ tới người được hoàn cọc (user)
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id');
    }
}