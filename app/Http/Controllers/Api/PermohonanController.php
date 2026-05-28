<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JenisSurat;
use App\Models\Permohonan;
use App\Models\PermohonanBerkas;
use App\Models\PermohonanTimeline;
use App\Models\SpamLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PermohonanController extends Controller
{
    // ── GET /api/v1/jenis-surat ───────────────────────────────────────────
    public function jenisSurat(): JsonResponse
    {
        $data = JenisSurat::where('aktif', true)
            ->orderBy('urutan')
            ->get(['id', 'nama', 'persyaratan', 'variabel_tersedia']);

        return response()->json(['data' => $data]);
    }

    // ── POST /api/v1/permohonan/buat ─────────────────────────────────────
    public function buat(Request $request): JsonResponse
    {
        $request->validate([
            'nik'           => ['required', 'digits:16'],
            'nama_lengkap'  => ['required', 'string', 'max:100'],
            'nomor_wa'      => ['required', 'string', 'max:20'],
            'jenis_surat_id'=> ['required', 'exists:jenis_surat,id'],
            'keperluan'     => ['required', 'string', 'max:500'],
            'foto_ktp'      => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:' . intval(\App\Models\Setting::get('foto_max_kb', 5120))],
            'foto_kk'       => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:' . intval(\App\Models\Setting::get('foto_max_kb', 5120))],
            'setuju_pdp'    => ['required', 'accepted'],
        ]);

        // Anti-spam cek
        $spamCheck = SpamLog::where('nik', $request->nik)
            ->where('blocked_until', '>', now())
            ->first();

        if ($spamCheck) {
            return response()->json([
                'message' => 'NIK Anda diblokir sementara. Silakan coba lagi dalam beberapa saat.',
            ], 429);
        }

        $permohonan = DB::transaction(function () use ($request) {
            $ktpPath = null;
            $kkPath  = null;

            if ($request->hasFile('foto_ktp')) {
                $ktpPath = $request->file('foto_ktp')->store('berkas', 'public');
            }
            if ($request->hasFile('foto_kk')) {
                $kkPath = $request->file('foto_kk')->store('berkas', 'public');
            }

            $permohonan = Permohonan::create([
                'kode_unik'      => Permohonan::generateKodeUnik(),
                'nik'            => $request->nik,
                'nama_lengkap'   => $request->nama_lengkap,
                'nomor_wa'       => $request->nomor_wa,
                'jenis_surat_id' => $request->jenis_surat_id,
                'keperluan'      => $request->keperluan,
                'foto_ktp_path'  => $ktpPath,
                'foto_kk_path'   => $kkPath,
                'setuju_pdp'     => true,
                'ip_address'     => request()->ip(),
                'sla_deadline'   => now()->addHours(intval(\App\Models\Setting::get('sla_jam', 72))),
            ]);

            PermohonanTimeline::create([
                'permohonan_id' => $permohonan->id,
                'status'        => 'pending',
                'catatan'       => 'Permohonan diterima oleh sistem.',
            ]);

            // Simpan versi berkas
            foreach (['foto_ktp' => $ktpPath, 'foto_kk' => $kkPath] as $tipe => $path) {
                if ($path) {
                    PermohonanBerkas::create([
                        'permohonan_id' => $permohonan->id,
                        'versi'         => 1,
                        'tipe'          => $tipe,
                        'file_path'     => $path,
                    ]);
                }
            }

            // Catat spam log
            SpamLog::updateOrCreate(
                ['nik' => $request->nik, 'window_start' => now()->startOfHour()],
                ['count' => DB::raw('count + 1'), 'ip_address' => request()->ip()]
            );

            return $permohonan;
        });

        return response()->json([
            'message'   => 'Permohonan berhasil dikirim.',
            'kode_unik' => $permohonan->kode_unik,
        ], 201);
    }

    // ── GET /api/v1/permohonan/lacak/{kode} ───────────────────────────────
    public function lacak(string $kode): JsonResponse
    {
        $permohonan = Permohonan::with(['jenisSurat', 'timeline'])
            ->where('kode_unik', strtoupper($kode))
            ->firstOrFail();

        return response()->json([
            'kode_unik'    => $permohonan->kode_unik,
            'nama_lengkap' => $permohonan->nama_lengkap,
            'jenis_surat'  => $permohonan->jenisSurat->nama,
            'status'       => $permohonan->status,
            'status_label' => $permohonan->statusLabel(),
            'status_color' => $permohonan->statusColor(),
            'catatan'      => $permohonan->catatan_petugas,
            'sla_deadline' => $permohonan->sla_deadline?->format('d/m/Y H:i'),
            'foto_purged'  => $permohonan->fotoSudahDihapus(),
            'timeline'     => $permohonan->timeline->map(fn($t) => [
                'status'     => $t->status,
                'catatan'    => $t->catatan,
                'created_at' => $t->created_at->format('d/m/Y H:i'),
            ]),
        ]);
    }
}
