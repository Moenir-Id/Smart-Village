<?php

namespace App\Policies;

use App\Models\Permohonan;
use App\Models\User;

class PermohonanPolicy
{
    /**
     * Admin & petugas boleh meng-ekspor dokumen.
     */
    public function ekspor(User $user, Permohonan $permohonan): bool
    {
        return in_array($user->role, ['admin', 'petugas']);
    }

    /**
     * Admin & petugas boleh mengubah status permohonan.
     */
    public function update(User $user, Permohonan $permohonan): bool
    {
        return in_array($user->role, ['admin', 'petugas']);
    }
}
