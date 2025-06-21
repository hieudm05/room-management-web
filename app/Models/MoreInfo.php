<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MoreInfo extends Model
{
    protected $table = 'more_info';

    // Laravel mặc định xử lý created_at và updated_at nên không cần set public $timestamps = false;

    protected $fillable = [
        'user_id',
        'address',
        'cccd_front',
        'cccd_back',
    ];

    /**
     * Quan hệ với user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
