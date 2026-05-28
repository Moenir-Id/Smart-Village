<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\JenisSurat;
use Illuminate\Http\Request;

class TemplateSuratController extends Controller
{
    public function index()
    {
        $jenisSurat = JenisSurat::orderBy('urutan')->get();
        return view('admin.template.index', compact('jenisSurat'));
    }

    public function upload(Request $request, JenisSurat $jenisSurat)
    {
        $request->validate([
            'template' => ['required', 'file', 'mimes:docx', 'max:5120'],
        ]);

        // Hapus template lama jika ada
        if ($jenisSurat->template_path) {
            $oldPath = storage_path('app/' . $jenisSurat->template_path);
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }

        $file     = $request->file('template');
        $filename = 'template_' . $jenisSurat->id . '_' . now()->format('YmdHis') . '.docx';
        $path     = 'templates/' . $filename;

        $dir = storage_path('app/templates');
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        $file->move($dir, $filename);

        // Deteksi variabel dari isi template
        $variabel = $this->deteksiVariabel(storage_path('app/' . $path));

        $jenisSurat->update([
            'template_path'     => $path,
            'variabel_tersedia' => $variabel,
        ]);

        AuditLog::record('template_uploaded', $jenisSurat, [], ['path' => $path]);

        return back()->with('success', "Template '{$jenisSurat->nama}' berhasil diupload.");
    }

    public function hapus(JenisSurat $jenisSurat)
    {
        if ($jenisSurat->template_path) {
            $fullPath = storage_path('app/' . $jenisSurat->template_path);
            if (file_exists($fullPath)) unlink($fullPath);
        }

        $jenisSurat->update(['template_path' => null, 'variabel_tersedia' => null]);

        AuditLog::record('template_deleted', $jenisSurat);

        return back()->with('success', 'Template berhasil dihapus.');
    }

    /**
     * Scan isi .docx untuk menemukan tag ${variabel}.
     */
    private function deteksiVariabel(string $path): array
    {
        try {
            $zip     = new \ZipArchive();
            $variabel = [];
            if ($zip->open($path) === true) {
                $xml = $zip->getFromName('word/document.xml');
                $zip->close();
                preg_match_all('/\$\{(\w+)\}/', $xml ?? '', $matches);
                $variabel = array_unique($matches[1] ?? []);
            }
            return array_values($variabel);
        } catch (\Throwable) {
            return [];
        }
    }
}
