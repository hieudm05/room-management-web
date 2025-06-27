<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Landlord\BankAccount;


use App\Models\Landlord\Property;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    // Define role constants
    const ROLE_ADMIN = 'Admin';
    const ROLE_RENTER = 'Renter';
    const ROLE_LANDLORD = 'Landlord';
    const ROLE_STAFF = 'Staff';
    const ROLE_MANAGER = 'Manager';
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // mặc định đăng ký tài khoản mới sẽ có role là user
    protected $attributes = [
        'role' => self::ROLE_RENTER,
    ];
    public function IsRoleLandlord()
    {
        return $this->role === self::ROLE_LANDLORD;
    }

    public function tenant()
    {
        return $this->hasOne(Tenant::class);
    }

    public function adminProfile()
    {
        return $this->hasOne(AdminProfile::class);
    }
    public function rentalAgreements()
    {
        return $this->hasMany(RentalAgreement::class, 'id');
    }
     public function bankAccounts() {
        return $this->hasMany(BankAccount::class, 'user_id');
    }
    public function properties()
    {
        return $this->hasMany(Property::class, 'landlord_id');
    }

    public function favorites()
{
    return $this->belongsToMany(Property::class, 'favorites', 'user_id', 'property_id')
                ->withTimestamps();
}



}
