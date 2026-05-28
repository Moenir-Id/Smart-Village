<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\EksporDocxJob;
use App\Models\AuditLog;
use App\Models\Permohonan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EksporController extends Controller
{
    /**
     * Dispatch job ekspor DOCX ke queue.
     * Response segera — unduhan tersedia via polling /admin/ekspor/status/{permohonan}
     */
    public function ekspor(Request $request, Permohonan $permohonan): JsonResponse
    {
        $this->authorize('ekspor', $permohonan);

        // Jangan ekspor kalau jenis surat tidak punya template
        if (!$permohonan->jenisSurat->hasTemplate()) {
            return response()->json([
                'success' => false,
                'pesan'   => 'Jenis surat ini belum memiliki template Word. Upload template terlebih dahulu.',
            ], 422);
        }

        // Dispatch ke queue (async)
        EksporDocxJob::dispatch($permohonan->id, auth()->id());

        AuditLog::record('docx_export_queued', $permohonan);

        return response()->json([
            'success' => true,
            'pesan'   => 'Dokumen sedang disiapkan. Halaman akan otomatis mengunduh dalam beberapa detik.',
            'poll_url'=> route('admin.ekspor.status', $permohonan),
        ]);
    }

    /**
     * Polling — cek apakah file DOCX sudah siap.
     */
    public function status(Permohonan $permohonan): JsonResponse
    {
        $permohonan->refresh();

        if ($permohonan->docx_path && file_exists(storage_path('app/' . $permohonan->docx_path))) {
            return response()->json([
                'ready'       => true,
                'download_url'=> route('admin.ekspor.unduh', $permohonan),
            ]);
        }

        return response()->json(['ready' => false]);
    }

    /**
     * Unduh file DOCX hasil ekspor.
     */
    public function unduh(Permohonan $permohonan)
    {
        $this->authorize('ekspor', $permohonan);

        abort_unless($permohonan->docx_path, 404, 'File belum tersedia.');

        $path = storage_path('app/' . $permohonan->docx_path);
        abort_unless(file_exists($path), 404, 'File tidak ditemukan di server.');

        $nama = sprintf(
            '%s_%s_%s.docx',
            str_replace(' ', '_', $permohonan->jenisSurat->nama),
            $permohonan->nik,
            $permohonan->kode_unik
        );

        AuditLog::record('docx_downloaded', $permohonan);

        return response()->download($path, $nama);
    }
}
