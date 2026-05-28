<?php

namespace App\Jobs;

use App\Models\AuditLog;
use App\Models\Permohonan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\TemplateProcessor;

class EksporDocxJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 60;

    public function __construct(
        public readonly string $permohonanId,
        public readonly int    $userId
    ) {}

    public function handle(): void
    {
        $permohonan = Permohonan::with('jenisSurat')->findOrFail($this->permohonanId);
        $jenisSurat = $permohonan->jenisSurat;

        if (!$jenisSurat->template_path || !Storage::exists($jenisSurat->template_path)) {
            throw new \RuntimeException("Template surat tidak ditemukan untuk: {$jenisSurat->nama}");
        }

        $templatePath = Storage::path($jenisSurat->template_path);
        $processor    = new TemplateProcessor($templatePath);

        $vars = array_merge(
            $this->buildDesaVars(),
            $this->buildPermohonanVars($permohonan)
        );

        foreach ($vars as $key => $value) {
            $processor->setValue($key, htmlspecialchars((string) ($value ?? '')));
        }

        $outputFilename = "surat_{$permohonan->kode_unik}_" . now()->format('YmdHis') . '.docx';
        $outputPath     = storage_path("app/exports/{$outputFilename}");

        // Pastikan direktori ada
        $dir = storage_path('app/exports');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $processor->saveAs($outputPath);

        $permohonan->update(['docx_path' => "exports/{$outputFilename}"]);

        AuditLog::create([
            'user_id'    => $this->userId,
            'action'     => 'docx_exported',
            'model_type' => Permohonan::class,
            'model_id'   => $permohonan->id,
            'after'      => ['docx_path' => $outputFilename],
            'ip_address' => null,
        ]);
    }

    private function buildDesaVars(): array
    {
        return [
            'desa_nama'        => config('desa.nama'),
            'desa_kecamatan'   => config('desa.kecamatan'),
            'desa_kabupaten'   => config('desa.kabupaten'),
            'desa_provinsi'    => config('desa.provinsi'),
            'desa_kepala_nama' => config('desa.kepala_nama'),
            'desa_kepala_nip'  => config('desa.kepala_nip'),
            'tanggal_cetak'    => now()->translatedFormat('d F Y'),
        ];
    }

    private function buildPermohonanVars(Permohonan $p): array
    {
        return [
            'nama'          => $p->nama_lengkap,
            'nik'           => $p->nik,
            'nomor_wa'      => $p->nomor_wa,
            'keperluan'     => $p->keperluan,
            'kode_unik'     => $p->kode_unik,
            'jenis_surat'   => $p->jenisSurat->nama,
            'tanggal_ajuan' => $p->created_at->translatedFormat('d F Y'),
        ];
    }
}
