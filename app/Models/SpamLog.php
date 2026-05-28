<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpamLog extends Model
{
    public $timestamps = false;

    protected $table = 'spam_log';

    protected $fillable = [
        'nik', 'ip_address', 'count', 'window_start', 'blocked_until',
    ];

    protected $casts = [
        'window_start'  => 'datetime',
        'blocked_until' => 'datetime',
    ];

    /**
     * Cek apakah NIK/IP sedang diblokir atau melebihi batas.
     */
    public static function isBlocked(string $nik, string $ip, int $maxPerJam): bool
    {
        // Cek blokir eksplisit
        $blocked = static::where('nik', $nik)
            ->whereNotNull('blocked_until')
            ->where('blocked_until', '>', now())
            ->exists();

        if ($blocked) return true;

        // Cek jumlah dalam window 1 jam
        $count = static::where('nik', $nik)
            ->where('window_start', '>=', now()->subHour())
            ->sum('count');

        return $count >= $maxPerJam;
    }

    /**
     * Catat pengajuan baru untuk NIK ini.
     */
    public static function recordSubmission(string $nik, string $ip): void
    {
        $existing = static::where('nik', $nik)
            ->where('window_start', '>=', now()->subHour())
            ->first();

        if ($existing) {
            $existing->increment('count');
        } else {
            static::create([
                'nik'          => $nik,
                'ip_address'   => $ip,
                'count'        => 1,
                'window_start' => now(),
            ]);
        }
    }
}
