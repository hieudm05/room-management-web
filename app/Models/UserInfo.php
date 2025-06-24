<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserInfo extends Model
{
    protected $table = 'user_infos';
    protected $primaryKey = 'id';
    protected $fillable = ['user_id', 'cccd', 'phone','room_id'];
}
