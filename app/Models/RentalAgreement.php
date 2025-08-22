<?php

namespace App\Models;

use App\Models\Landlord\Room;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RentalAgreement extends Model
{
    use HasFactory;
    protected $table = 'rental_agreements';

    protected $primaryKey = 'rental_id';

    protected $fillable = [
        'room_id',
        'renter_id',
        'landlord_id',
        'start_date',
        'end_date',
        'rental_price',
        'deposit',
        'status',
        'contract_file',
        'agreement_terms',
        'price_edit_count',
        'deposit_edit_count',
        'expected_leave_date',
        'is_active',
        'created_by',

        // ✅ Thêm các cột thông tin người thuê (fix lỗi không copy được dữ liệu từ hợp đồng cũ)
        'full_name',   // thêm
        'email',       // thêm
        'phone',       // thêm
        'cccd',        // thêm
    ];

    const STATUS_PENDING    = 'Pending';
    const STATUS_ACTIVE     = 'Active';
    const STATUS_SIGNED     = 'Signed';
    const STATUS_TERMINATED = 'Terminated';
    const STATUS_EXPIRED    = 'Expired';

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id', 'room_id');
    }

    public function renter()
    {
        return $this->belongsTo(User::class, 'renter_id');
    }

    public function landlord()
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }

    public function userInfos()
    {
        return $this->hasMany(UserInfo::class, 'rental_id');
    }

    public function roomUser()
    {
        return $this->hasMany(RoomUser::class, 'rental_id');
    }
}
