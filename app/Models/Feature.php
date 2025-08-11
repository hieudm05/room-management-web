<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Feature extends Model
{
    use HasFactory;

    protected $primaryKey = 'feature_id';

    protected $fillable = [
        'name',
    ];

    // Một feature có thể thuộc nhiều bài đăng
    public function staffPosts()
    {
        return $this->belongsToMany(StaffPost::class, 'staff_post_features', 'feature_id', 'post_id');
    }
}
