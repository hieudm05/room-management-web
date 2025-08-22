<?php

namespace App\Models;

use App\Models\Landlord\Property;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Casts\Attribute;

class StaffPost extends Model
{
    const STATUS_PENDING = 0;
    const STATUS_APPROVED = 1;
    const STATUS_REJECTED = 2;

    use HasFactory;

    protected $primaryKey = 'post_id';

    protected $fillable = [
        'staff_id',
        'landlord_id',
        'property_id',
        'category_id',
        'title',
        'slug',
        'price',
        'area',
        'address',
        'district',
        'city',
        'published_at',
        'ward',
        'expired_at',
        'latitude',
        'longitude',
        'description',
        'amenities',
        'furnitures',
        'suitability',
        'contract_term',
        'post_code',
        'thumbnail',
        'gallery',
        'status',
        'approved_by',
        'approved_at',
        'rejected_reason',
        'is_public', // New field added
    ];

    protected $casts = [
        'amenities' => 'array',
        'furnitures' => 'array',
        'gallery' => 'array',
        'published_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
    public function features()
    {
        return $this->belongsToMany(Feature::class, 'staff_post_features', 'post_id', 'feature_id');
    }
    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }


    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function landlord()
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }

    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function room()
    {
        return $this->belongsTo(\App\Models\Landlord\Room::class, 'room_id', 'room_id');
    }


    /*
    |--------------------------------------------------------------------------
    | Accessors / Mutators
    |--------------------------------------------------------------------------
    */

    // Hiển thị link ảnh thumbnail đầy đủ
    protected function thumbnailUrl(): Attribute
    {
        return Attribute::get(fn() => $this->thumbnail ? asset('storage/' . $this->thumbnail) : null);
    }

    // Gallery ảnh dạng link URL
    protected function galleryUrls(): Attribute
    {
        return Attribute::get(
            fn() =>
            collect($this->gallery)->map(fn($path) => asset('storage/' . $path))->toArray()
        );
    }
}
