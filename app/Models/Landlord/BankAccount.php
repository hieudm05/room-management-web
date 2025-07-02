<?php
namespace App\Models\Landlord;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use App\Models\Landlord\Property;

class BankAccount extends Model
{
    protected $fillable = ['user_id','bank_name','bank_code','bank_account_name','bank_account_number','status'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function properties() {
        return $this->belongsToMany(Property::class, 'property_bank_account')
                    ->withTimestamps();
    }

    public function rooms() {
        return $this->hasMany(Room::class, 'property_bank_account_id');
    }
    public function userStaff()
{
    return $this->belongsTo(\App\Models\User::class, 'user_id');
}
}

