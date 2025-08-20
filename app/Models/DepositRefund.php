<?php

namespace App\Models;

use App\Models\Landlord\RentalAgreement;
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
    public function rental()
    {
        return $this->belongsTo(RentalAgreement::class, 'rental_id', 'rental_id');
    }

    /**
     * Quan hệ tới người được hoàn cọc (user)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}