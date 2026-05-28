<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PermohonanTimeline extends Model
{
    public $timestamps = false;

    protected $table = 'permohonan_timeline';

    protected $fillable = [
        'permohonan_id', 'status', 'catatan', 'user_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function permohonan(): BelongsTo
    {
        return $this->belongsTo(Permohonan::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
