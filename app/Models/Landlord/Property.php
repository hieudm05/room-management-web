<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Model;

class Property  extends Model
{
    //
      protected $table = 'properties';

    protected $primaryKey = 'property_id';


    protected $fillable = [
        'landlord_id',
        'name',
        'address',
        'latitude',
        'longitude',
        'description',
        'created_at',
        'updated_at',
    ];
}
