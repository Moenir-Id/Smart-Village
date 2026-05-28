<?php

namespace App\Services;

use App\Models\SpamLog;
use Illuminate\Support\Facades\DB;

class SpamDetectorService
{
    public function isBlocked(string $nik): bool
    {
        return SpamLog::where('nik', $nik)
            ->where('blocked_until', '>', now())
            ->exists();
    }

    public function record(string $nik, string $ip): void
    {
        $maxPerJam = config('desa.spam_max_per_jam', 3);

        $log = SpamLog::firstOrCreate(
            ['nik' => $nik, 'window_start' => now()->startOfHour()],
            ['count' => 0, 'ip_address' => $ip]
        );

        $log->increment('count');

        if ($log->fresh()->count >= $maxPerJam) {
            $log->update(['blocked_until' => now()->addHours(1)]);
        }
    }
}
