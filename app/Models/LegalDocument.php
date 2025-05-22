<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LegalDocument extends Model
{
    //
    protected $table = 'legal_documents';

    protected $primaryKey = 'document_id';

    public $timestamps = false; // Vì bạn dùng uploaded_at và reviewed_at thay cho created_at/updated_at

    protected $fillable = [
        'user_id',
        'document_type',
        'file_path',
        'status',
        'verified_by',
        'uploaded_at',
        'reviewed_at',
    ];

    /**
     * Người dùng đã tải giấy tờ (chủ trọ).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Người duyệt giấy tờ (Admin hoặc Staff).
     */
    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
