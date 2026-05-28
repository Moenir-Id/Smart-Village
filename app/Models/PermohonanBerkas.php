<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PermohonanBerkas extends Model
{
    public $timestamps = false;

    protected $table = 'permohonan_berkas';

    protected $fillable = [
        'permohonan_id', 'versi', 'tipe', 'file_path', 'file_size_kb',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function permohonan(): BelongsTo
    {
        return $this->belongsTo(Permohonan::class);
    }
}
