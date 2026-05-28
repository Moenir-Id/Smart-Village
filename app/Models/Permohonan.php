<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Permohonan extends Model
{
    use HasUlids, SoftDeletes;

    protected $table = 'permohonan';

    protected $fillable = [
        'kode_unik', 'nik', 'nama_lengkap', 'nomor_wa',
        'jenis_surat_id', 'keperluan', 'status',
        'foto_ktp_path', 'foto_kk_path', 'foto_purged_at',
        'docx_path', 'catatan_petugas', 'processed_by',
        'processed_at', 'sla_deadline', 'versi_berkas',
        'setuju_pdp', 'ip_address',
    ];

    protected $casts = [
        'foto_purged_at' => 'datetime',
        'processed_at'   => 'datetime',
        'sla_deadline'   => 'datetime',
        'setuju_pdp'     => 'boolean',
    ];

    // ── Scopes ───────────────────────────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeOverdueSla($query)
    {
        return $query->whereIn('status', ['pending', 'diproses'])
                     ->where('sla_deadline', '<', now());
    }

    public function scopeNeedsPurge($query)
    {
        $days = config('desa.purge_foto_hari', 30);
        return $query->whereIn('status', ['selesai', 'ditolak'])
                     ->whereNull('foto_purged_at')
                     ->where('updated_at', '<=', now()->subDays($days));
    }

    // ── Relasi ───────────────────────────────────────────────────────────────

    public function jenisSurat(): BelongsTo
    {
        return $this->belongsTo(JenisSurat::class);
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function timeline(): HasMany
    {
        return $this->hasMany(PermohonanTimeline::class)->orderBy('created_at');
    }

    public function berkas(): HasMany
    {
        return $this->hasMany(PermohonanBerkas::class)->orderBy('versi');
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    public function isSlaOverdue(): bool
    {
        return in_array($this->status, ['pending', 'diproses'])
            && $this->sla_deadline
            && $this->sla_deadline->isPast();
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'pending'   => 'Menunggu',
            'diproses'  => 'Sedang Diproses',
            'selesai'   => 'Selesai',
            'ditolak'   => 'Ditolak',
            default     => ucfirst($this->status),
        };
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'pending'   => 'yellow',
            'diproses'  => 'blue',
            'selesai'   => 'green',
            'ditolak'   => 'red',
            default     => 'gray',
        };
    }

    public function fotoSudahDihapus(): bool
    {
        return $this->foto_purged_at !== null;
    }

    // ── Static Helpers ───────────────────────────────────────────────────────

    public static function generateKodeUnik(): string
    {
        do {
            $kode = 'PRWS-' . strtoupper(substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 5));
        } while (static::where('kode_unik', $kode)->exists());

        return $kode;
    }
}
