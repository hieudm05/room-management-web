<?php

namespace App\Models\Landlord\Staff\Rooms;

use App\Models\Landlord\BankAccount;
use App\Models\Landlord\Room;
use Illuminate\Database\Eloquent\Model;

class RoomBill extends Model
{
    protected $fillable = [
        'room_id', 'month', 'tenant_name', 'bank_account_id', 'area', 'rent_price',
        'electric_start', 'electric_end', 'electric_kwh', 'electric_unit_price',
        'electric_total', 'water_price', 'water_unit', 'water_occupants', 'water_start', 'water_end',
        'water_m3', 'water_total', 'total', 'status', 'complaint_user_cost', 'complaint_landlord_cost',
    ];

    // ✅ 1. Quan hệ với phòng
    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id', 'room_id');
    }

    // ✅ 2. Quan hệ với ngân hàng
    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class, 'bank_account_id');
    }

    // ✅ 3. Quan hệ với dịch vụ của hóa đơn
    public function services()
    {
        return $this->hasMany(RoomBillService::class, 'room_bill_id');
    }


    // ✅ 4. Quan hệ với chi phí phát sinh
    public function additionalFees()
    {
        return $this->hasMany(RoomBillAdditionalFee::class, 'room_bill_id');
    }

    // ✅ 5. Quan hệ với ảnh điện & nước
    public function utilityPhotos()
    {
        return $this->hasMany(RoomUtilityPhoto::class, 'room_bill_id');
    }

    // ✅ 6. Helper: kiểm tra đã thanh toán chưa
    public function getIsPaidAttribute(): bool
    {
        return $this->status === 'paid';
    }
    
}
