<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommonIssue extends Model
{

    protected $fillable = ['name'];

    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }
}

