<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permohonan;
use App\Models\Setting;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $bulan = $request->input('bulan', now()->format('Y-m'));
        [$tahun, $bln] = explode('-', $bulan);

        $permohonan = Permohonan::with('jenisSurat')
            ->whereYear('created_at', $tahun)
            ->whereMonth('created_at', $bln)
            ->orderBy('created_at', 'desc')
            ->get();

        $stats = [
            'total'    => $permohonan->count(),
            'pending'  => $permohonan->where('status', 'pending')->count(),
            'diproses' => $permohonan->where('status', 'diproses')->count(),
            'selesai'  => $permohonan->where('status', 'selesai')->count(),
            'ditolak'  => $permohonan->where('status', 'ditolak')->count(),
        ];

        $perJenis = $permohonan->groupBy(fn($p) => $p->jenisSurat->nama ?? 'Tidak diketahui')
            ->map(fn($g) => ['total' => $g->count(), 'selesai' => $g->where('status', 'selesai')->count()])
            ->sortByDesc('total');

        $harian = $permohonan->groupBy(fn($p) => $p->created_at->format('d'))
            ->map->count()
            ->sortKeys();

        $avgSla = $permohonan->where('status', 'selesai')
            ->filter(fn($p) => $p->processed_at && $p->created_at)
            ->map(fn($p) => $p->created_at->diffInHours($p->processed_at))
            ->avg();

        return view('admin.laporan.index', compact('permohonan', 'stats', 'perJenis', 'harian', 'avgSla', 'bulan'));
    }

    public function eksporCsv(Request $request)
    {
        $bulan = $request->input('bulan', now()->format('Y-m'));
        [$tahun, $bln] = explode('-', $bulan);

        $permohonan = Permohonan::with('jenisSurat', 'processedBy')
            ->whereYear('created_at', $tahun)
            ->whereMonth('created_at', $bln)
            ->orderBy('created_at')
            ->get();

        $filename = "laporan_permohonan_{$bulan}.csv";
        $headers  = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($permohonan) {
            $f = fopen('php://output', 'w');
            fprintf($f, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($f, ['No', 'Kode', 'NIK', 'Nama', 'No WA', 'Jenis Surat', 'Keperluan', 'Status', 'Catatan', 'Petugas', 'Tgl Masuk', 'Tgl Selesai'], ';');
            foreach ($permohonan as $i => $p) {
                fputcsv($f, [
                    $i + 1, $p->kode_unik, $p->nik, $p->nama_lengkap, $p->nomor_wa,
                    $p->jenisSurat->nama ?? '-', $p->keperluan, $p->statusLabel(),
                    $p->catatan_petugas ?? '-', $p->processedBy->name ?? '-',
                    $p->created_at->format('d/m/Y H:i'),
                    $p->processed_at?->format('d/m/Y H:i') ?? '-',
                ], ';');
            }
            fclose($f);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function eksporPdf(Request $request)
    {
        $bulan = $request->input('bulan', now()->format('Y-m'));
        [$tahun, $bln] = explode('-', $bulan);

        $permohonan = Permohonan::with('jenisSurat', 'processedBy')
            ->whereYear('created_at', $tahun)
            ->whereMonth('created_at', $bln)
            ->orderBy('created_at')
            ->get();

        $stats = [
            'total'    => $permohonan->count(),
            'pending'  => $permohonan->where('status', 'pending')->count(),
            'diproses' => $permohonan->where('status', 'diproses')->count(),
            'selesai'  => $permohonan->where('status', 'selesai')->count(),
            'ditolak'  => $permohonan->where('status', 'ditolak')->count(),
        ];

        $perJenis = $permohonan->groupBy(fn($p) => $p->jenisSurat->nama ?? 'Tidak diketahui')
            ->map(fn($g) => ['total' => $g->count(), 'selesai' => $g->where('status', 'selesai')->count()])
            ->sortByDesc('total')
            ->values();

        $harian = $permohonan->groupBy(fn($p) => $p->created_at->format('d'))
            ->map->count()
            ->toArray();

        $avgSla = $permohonan->where('status', 'selesai')
            ->filter(fn($p) => $p->processed_at && $p->created_at)
            ->map(fn($p) => $p->created_at->diffInHours($p->processed_at))
            ->avg();

        // ── Ambil semua setting identitas sekaligus (1 query) ──
        $identitas = Setting::group('identitas');

        $desaNama        = $identitas['desa_nama']        ?? 'Kantor Desa';
        $desaKecamatan   = $identitas['desa_kecamatan']   ?? '';
        $desaKabupaten   = $identitas['desa_kabupaten']   ?? '';
        $desaProvinsi    = $identitas['desa_provinsi']    ?? '';
        $desaKodePos     = $identitas['desa_kode_pos']    ?? '';
        $desaTelepon     = $identitas['desa_telepon']     ?? '';
        $desaEmail       = $identitas['desa_email']       ?? '';
        $kepalaDesaNama  = $identitas['desa_kepala_nama'] ?? '';
        $kepalaDesaNip   = $identitas['desa_kepala_nip']  ?? '';
        $logoPath        = $identitas['desa_logo_path']   ?? '';

        // Susun alamat lengkap untuk kop
        $alamatParts = array_filter([
            $desaKecamatan,
            $desaKabupaten,
            $desaProvinsi,
            $desaKodePos ? "Kode Pos $desaKodePos" : '',
        ]);
        $alamat = implode(', ', $alamatParts);
        if ($desaTelepon) $alamat .= $desaTelepon ? "  |  Telp. $desaTelepon" : '';

        $bulanLabel = \Carbon\Carbon::parse($bulan . '-01')->translatedFormat('F Y');
        $dicetak    = now()->translatedFormat('d F Y H:i');

        // Resolve logo ke absolute path di storage
        $logoAbsPath = '';
        if ($logoPath) {
            $abs = storage_path('app/public/' . $logoPath);
            if (file_exists($abs)) {
                $logoAbsPath = $abs;
            }
        }

        $pdfContent = \App\Services\LaporanPdfService::generate(
            permohonan:     $permohonan,
            stats:          $stats,
            perJenis:       $perJenis,
            harian:         $harian,
            avgSla:         $avgSla,
            namaInstansi:   $desaNama,
            alamat:         $alamat,
            bulanLabel:     $bulanLabel,
            bulan:          $bulan,
            dicetak:        $dicetak,
            kepalaDesaNama: $kepalaDesaNama,
            kepalaDesaNip:  $kepalaDesaNip,
            logoAbsPath:    $logoAbsPath,
        );

        $filename = "laporan_permohonan_{$bulan}.pdf";

        return response($pdfContent, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => "inline; filename=\"{$filename}\"",
        ]);
    }
}