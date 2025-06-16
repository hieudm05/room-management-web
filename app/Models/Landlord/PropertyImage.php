<?php

namespace App\Models\Landlord;
use Illuminate\Database\Eloquent\Model;
class PropertyImage extends Model
{
    protected $table = 'images_property';
    protected $fillable = ['property_id', 'image_path'];

    public function property()
    {
        return $this->belongsTo(\App\Models\Landlord\Property::class);
    }
}