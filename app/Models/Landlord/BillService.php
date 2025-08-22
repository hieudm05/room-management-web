<?php

namespace App\Models\Landlord;

use App\Models\Landlord\Staff\Rooms\RoomBill;
use Illuminate\Database\Eloquent\Model;

class BillService extends Model
{
    // Nếu tên bảng không theo quy ước Laravel (bill_services)
    protected $table = 'bill_services';

    // Trường được phép gán tự động
    protected $fillable = [
        'bill_id',
        'service_id',
        'name',
        'price',
        'qty',
        'total',
        'type_display',
    ];

    // Ép kiểu dữ liệu cho các trường số
    protected $casts = [
        'price' => 'float',
        'qty' => 'integer',
        'total' => 'float',
    ];

    /**
     * Quan hệ đến bảng hóa đơn
     */
    public function bill()
    {
        return $this->belongsTo(RoomBill::class);
    }

    /**
     * Quan hệ đến bảng dịch vụ
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
