<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComplaintPhoto extends Model
{
    protected $fillable = [
        'complaint_id',
        'photo_path',
        'type',
    ];

    public function complaint()
    {
        return $this->belongsTo(Complaint::class, 'complaint_id');
    }
}
