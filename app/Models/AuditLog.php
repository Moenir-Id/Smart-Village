<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id', 'action', 'model_type', 'model_id',
        'before', 'after', 'ip_address', 'user_agent',
    ];

    protected $casts = [
        'before'     => 'array',
        'after'      => 'array',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Record an audit entry (shorthand helper).
     */
    public static function record(
        string $action,
        ?Model $subject = null,
        array  $before  = [],
        array  $after   = []
    ): void {
        static::create([
            'user_id'    => auth()->id(),
            'action'     => $action,
            'model_type' => $subject ? get_class($subject) : null,
            'model_id'   => $subject?->getKey(),
            'before'     => $before ?: null,
            'after'      => $after  ?: null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
