<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Permohonan;
use App\Models\PermohonanTimeline;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermohonanApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Permohonan::with('jenisSurat');

        if ($request->filled('status'))         $query->where('status', $request->status);
        if ($request->filled('jenis_surat_id')) $query->where('jenis_surat_id', $request->jenis_surat_id);
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(fn($s) => $s->where('nik', 'like', "%$q%")->orWhere('nama_lengkap', 'like', "%$q%")->orWhere('kode_unik', 'like', "%$q%"));
        }

        $data = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json(['data' => $data]);
    }

    public function show(string $id): JsonResponse
    {
        $p = Permohonan::with(['jenisSurat', 'timeline.user', 'berkas', 'processedBy'])->findOrFail($id);
        return response()->json(['data' => $p]);
    }

    public function ubahStatus(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'status'  => ['required', 'in:diproses,selesai,ditolak'],
            'catatan' => ['nullable', 'string', 'max:1000'],
        ]);

        $permohonan = Permohonan::findOrFail($id);
        $before = ['status' => $permohonan->status];

        DB::transaction(function () use ($request, $permohonan) {
            $permohonan->update([
                'status'          => $request->status,
                'catatan_petugas' => $request->catatan,
                'processed_by'    => auth()->id(),
                'processed_at'    => now(),
            ]);
            PermohonanTimeline::create([
                'permohonan_id' => $permohonan->id,
                'status'        => $request->status,
                'catatan'       => $request->catatan,
                'user_id'       => auth()->id(),
            ]);
        });

        AuditLog::record('status_changed', $permohonan, $before, ['status' => $request->status]);

        return response()->json(['success' => true, 'status' => $permohonan->fresh()->status]);
    }
}
