<?php

namespace App\Models\Landlord;

use App\Models\LegalDocument;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
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
        'rules',
        'created_at',
        'updated_at',
    ];
    public function landlord()
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }
    public function legalDocuments()
    {
        return $this->hasMany(LegalDocument::class, 'property_id');
    }

    public function rooms()
    {
        return $this->hasMany(Room::class, 'property_id', 'property_id');
    }


}
