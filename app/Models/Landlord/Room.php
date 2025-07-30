<?php

namespace App\Models\Landlord;

use App\Models\User;
use App\Models\RoomUser;
use App\Models\UserInfo;
use App\Models\RentalAgreement;

use App\Models\Landlord\Facility;
use Illuminate\Database\Eloquent\Model;
use App\Models\Landlord\ContractRenewal;
use App\Models\Landlord\Staff\Rooms\RoomBill;
use App\Models\Landlord\Staff\Rooms\RoomUtility;

class Room extends Model
{
    protected $primaryKey = 'room_id';
    protected $fillable = [
        'property_id',
        'room_number',
        'area',
        'rental_price',
        'status',
        'occupants',
        'deposit_price',
        'contract_file',
        'contract_pdf_file',
        'contract_word_file',
        'wifi_price_per_person',
        'water_price_per_person',
        'created_by',
        'id_rental_agreements',
        'is_contract_locked'
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
        return $this->hasMany(RentalAgreement::class, 'room_id', 'room_id');
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
    // public function rentalAgreement()
    // {
    //     return $this->hasOne(\App\Models\RentalAgreement::class);
    // }
        public function rentalAgreement()
    {
        return $this->hasOne(RentalAgreement::class, 'room_id', 'room_id');
    }

    public function bills()
    {
        return $this->hasMany(RoomBill::class, 'room_id', 'room_id');
    }

    public function currentAgreement()
    {
        return $this->belongsTo(RentalAgreement::class, 'id_rental_agreements', 'rental_id')
            ->whereIn('status', ['Active', 'Signed']);
    }
   public function userInfos()
{
    return $this->hasMany(UserInfo::class, 'room_id', 'room_id');
}


    public function utilities()
    {
        return $this->hasMany(RoomUtility::class, 'room_id', 'room_id');
    }
    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class, 'property_bank_account_id');
    }

    public function staffs()

    {
        return $this->belongsToMany(User::class, 'room_staff', 'room_id', 'staff_id')
            ->where('role', 'Staff')
            ->withPivot('status');
    }

    public function getRenterAttribute()
    {
        return $this->currentAgreement?->renter;
    }

    public function roomStaff()
    {
        return $this->belongsToMany(User::class, 'room_staff', 'room_id', 'staff_id');
    }
    public function complaints()
{
    return $this->hasMany(\App\Models\Complaint::class, 'room_id', 'room_id');

}
 // RoomUtilityPhoto.php
public function roomBill()
{
    return $this->belongsTo(RoomBill::class, 'room_bill_id');

}
   public function roomUsers()
    {
        return $this->hasMany(RoomUser::class, 'room_id', 'room_id');
    }

    public function contractRenewals()
{
    return $this->hasMany(ContractRenewal::class, 'room_id', 'room_id');
}

}
