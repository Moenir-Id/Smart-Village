<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisSurat extends Model
{
    protected $table = 'jenis_surat';

    protected $fillable = [
        'nama', 'template_path', 'variabel_tersedia',
        'persyaratan', 'aktif', 'urutan',
    ];

    protected $casts = [
        'variabel_tersedia' => 'array',
        'aktif'             => 'boolean',
    ];

    public function scopeAktif($query)
    {
        return $query->where('aktif', true)->orderBy('urutan');
    }

    public function permohonan(): HasMany
    {
        return $this->hasMany(Permohonan::class);
    }

    public function hasTemplate(): bool
    {
        return !empty($this->template_path);
    }
}
