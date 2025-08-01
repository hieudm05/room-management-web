<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $primaryKey = 'category_id';

    protected $fillable = [
        'name',
        'slug',
    ];

    // Một chuyên mục có nhiều bài đăng
    public function staffPosts()
    {
        return $this->hasMany(StaffPost::class, 'category_id', 'category_id');
    }

    public function posts()
{
    return $this->staffPosts();
}

}
