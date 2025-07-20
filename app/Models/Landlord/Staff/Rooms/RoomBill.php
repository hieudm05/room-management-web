<?php

namespace App\Models\Landlord\Staff\Rooms;

use App\Models\Landlord\BankAccount;
use Illuminate\Database\Eloquent\Model;

class RoomBill extends Model
{
    protected $fillable = [
        'room_id', 'month', 'tenant_name','bank_account_id', 'area', 'rent_price',
        'electric_start', 'electric_end', 'electric_kwh', 'electric_unit_price',
        'electric_total', 'water_price', 'water_unit', 'water_occupants', 'water_start', 'water_end',
        'water_m3', 'water_total', 'total', 'status',  'complaint_user_cost','complaint_landlord_cost',
    ];

     public function services()
    {
        return $this->hasMany(RoomBillService::class);
    }
    public function getIsPaidAttribute(): bool
{
    return $this->status === 'paid';
}
    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }


}
