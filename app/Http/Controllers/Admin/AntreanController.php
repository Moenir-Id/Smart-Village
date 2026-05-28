<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Permohonan;
use App\Models\PermohonanTimeline;
use App\Services\WhatsappService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AntreanController extends Controller
{
    public function index()
    {
        $stats = [
            'total'    => Permohonan::count(),
            'pending'  => Permohonan::where('status', 'pending')->count(),
            'diproses' => Permohonan::where('status', 'diproses')->count(),
            'selesai'  => Permohonan::where('status', 'selesai')->count(),
            'overdue'  => Permohonan::overdueSla()->count(),
        ];
        return view('admin.antrean.index', compact('stats'));
    }

    public function data(Request $request): JsonResponse
    {
        $query = Permohonan::with(['jenisSurat'])
            ->select(['id','kode_unik','nik','nama_lengkap','nomor_wa','jenis_surat_id','status','sla_deadline','foto_ktp_path','foto_kk_path','foto_purged_at','created_at']);

        if ($request->filled('status'))         $query->where('status', $request->status);
        if ($request->filled('jenis_surat_id')) $query->where('jenis_surat_id', $request->jenis_surat_id);
        if ($request->filled('tanggal'))        $query->whereDate('created_at', $request->tanggal);
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(fn($s) => $s->where('nik','like',"%$q%")->orWhere('nama_lengkap','like',"%$q%")->orWhere('kode_unik','like',"%$q%"));
        }

        $data = $query->orderByRaw("FIELD(status,'pending','diproses','selesai','ditolak')")->orderBy('created_at','asc')->paginate(20);

        return response()->json([
            'data' => $data->map(fn($p) => [
                'id'           => $p->id,
                'kode_unik'    => $p->kode_unik,
                'nik'          => $p->nik,
                'nama_lengkap' => $p->nama_lengkap,
                'jenis_surat'  => $p->jenisSurat->nama,
                'status'       => $p->status,
                'status_label' => $p->statusLabel(),
                'status_color' => $p->statusColor(),
                'sla_deadline' => $p->sla_deadline?->toIso8601String(),
                'sla_overdue'  => $p->isSlaOverdue(),
                'created_at'   => $p->created_at->format('d/m/Y H:i'),
                'foto_purged'  => $p->fotoSudahDihapus(),
                'foto_ktp_url' => !$p->fotoSudahDihapus() && $p->foto_ktp_path
                                    ? asset('storage/' . $p->foto_ktp_path) : null,
                'foto_kk_url'  => !$p->fotoSudahDihapus() && $p->foto_kk_path
                                    ? asset('storage/' . $p->foto_kk_path) : null,
            ]),
            'meta' => ['current_page'=>$data->currentPage(),'last_page'=>$data->lastPage(),'total'=>$data->total()],
        ]);
    }

    public function show(Permohonan $permohonan)
    {
        $permohonan->load(['jenisSurat','processedBy','timeline.user','berkas']);

        $fotoKtp = null;
        $fotoKk  = null;
        if (!$permohonan->fotoSudahDihapus()) {
            if ($permohonan->foto_ktp_path && Storage::disk('public')->exists($permohonan->foto_ktp_path)) {
                $fotoKtp = asset('storage/' . $permohonan->foto_ktp_path);
            }
            if ($permohonan->foto_kk_path && Storage::disk('public')->exists($permohonan->foto_kk_path)) {
                $fotoKk = asset('storage/' . $permohonan->foto_kk_path);
            }
        }

        return view('admin.antrean.show', compact('permohonan','fotoKtp','fotoKk'));
    }

    public function ubahStatus(Request $request, Permohonan $permohonan): JsonResponse
    {
        $request->validate([
            'status'  => ['required', 'in:diproses,selesai,ditolak'],
            'catatan' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($request->status === 'ditolak' && empty($request->catatan)) {
            return response()->json(['success' => false, 'message' => 'Catatan alasan wajib diisi saat menolak permohonan.'], 422);
        }

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

        $permohonan->load('jenisSurat');
        $waOk = WhatsappService::kirimNotifikasi($permohonan);

        return response()->json([
            'success'      => true,
            'status'       => $permohonan->fresh()->status,
            'status_label' => $permohonan->fresh()->statusLabel(),
            'wa_terkirim'  => $waOk,
        ]);
    }
}
