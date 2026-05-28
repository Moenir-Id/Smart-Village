<?php

namespace App\Console\Commands;

use App\Models\Permohonan;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class PurgeFotoCommand extends Command
{
    protected $signature   = 'smartvillage:purge-foto';
    protected $description = 'Hapus foto KTP/KK warga yang sudah melewati batas hari purge.';

    public function handle(): int
    {
        $permohonan = Permohonan::needsPurge()->get();
        $count = 0;

        foreach ($permohonan as $p) {
            foreach (['foto_ktp_path', 'foto_kk_path'] as $field) {
                if ($p->$field && Storage::disk('public')->exists($p->$field)) {
                    Storage::disk('public')->delete($p->$field);
                }
            }

            $p->update([
                'foto_ktp_path'  => null,
                'foto_kk_path'   => null,
                'foto_purged_at' => now(),
            ]);

            $count++;
        }

        $this->info("✓ {$count} permohonan foto berhasil di-purge.");
        return self::SUCCESS;
    }
}
