<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

class BackupController extends Controller
{
    private string $disk   = 'local';
    private string $folder = 'backups';

    public function index()
    {
        $files = collect(Storage::disk($this->disk)->files($this->folder))
            ->map(function ($path) {
                return [
                    'name'    => basename($path),
                    'path'    => $path,
                    'size'    => $this->formatSize(Storage::disk($this->disk)->size($path)),
                    'tanggal' => date('d/m/Y H:i', Storage::disk($this->disk)->lastModified($path)),
                    'ts'      => Storage::disk($this->disk)->lastModified($path),
                ];
            })
            ->sortByDesc('ts')
            ->values();

        return view('admin.backup.index', compact('files'));
    }

    public function buat(Request $request)
    {
        set_time_limit(120);

        $timestamp = now()->format('Ymd_His');
        $slug      = Str::slug(\App\Models\Setting::get('desa_nama', 'desa'));
        $zipName   = "backup_{$slug}_{$timestamp}.zip";
        $zipPath   = storage_path("app/{$this->folder}/{$zipName}");

        Storage::disk($this->disk)->makeDirectory($this->folder);

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return back()->with('error', 'Gagal membuat file zip backup.');
        }

        // 1. SQL dump semua tabel
        $sql = $this->dumpDatabase();
        $zip->addFromString('database.sql', $sql);

        // 2. File storage/app/public (berkas foto, logo)
        $publicPath = storage_path('app/public');
        if (is_dir($publicPath)) {
            $this->addDirToZip($zip, $publicPath, 'storage');
        }

        // 3. File .env (disanitasi — hapus kredensial sensitif)
        $envContent = $this->sanitizeEnv(file_get_contents(base_path('.env')));
        $zip->addFromString('env.txt', $envContent);

        $zip->close();

        AuditLog::record('backup_created', null, [], ['file' => $zipName]);

        return back()->with('success', "Backup berhasil dibuat: {$zipName}");
    }

    public function unduh(string $name)
    {
        $path = "{$this->folder}/{$name}";
        abort_unless(Storage::disk($this->disk)->exists($path), 404);
        abort_unless(preg_match('/^backup_.+\.zip$/', $name), 403);

        AuditLog::record('backup_downloaded', null, [], ['file' => $name]);

        return Storage::disk($this->disk)->download($path, $name);
    }

    public function hapus(string $name)
    {
        $path = "{$this->folder}/{$name}";
        abort_unless(Storage::disk($this->disk)->exists($path), 404);
        abort_unless(preg_match('/^backup_.+\.zip$/', $name), 403);

        Storage::disk($this->disk)->delete($path);
        AuditLog::record('backup_deleted', null, [], ['file' => $name]);

        return back()->with('success', "Backup {$name} berhasil dihapus.");
    }

    public function restore(Request $request)
    {
        $request->validate([
            'file'    => ['required', 'file', 'mimes:zip', 'max:102400'],
            'konfirmasi' => ['required', 'in:RESTORE'],
        ]);

        set_time_limit(300);

        $zip = new ZipArchive();
        $tmp = $request->file('file')->getPathname();

        if ($zip->open($tmp) !== true) {
            return back()->with('error', 'File zip tidak valid atau rusak.');
        }

        // Validasi struktur zip
        if ($zip->locateName('database.sql') === false) {
            $zip->close();
            return back()->with('error', 'File backup tidak valid: database.sql tidak ditemukan.');
        }

        // Eksekusi SQL
        $sql = $zip->getFromName('database.sql');
        if ($sql) {
            try {
                DB::unprepared($sql);
            } catch (\Throwable $e) {
                $zip->close();
                return back()->with('error', 'Gagal restore database: ' . $e->getMessage());
            }
        }

        // Restore file storage
        $tmpDir = storage_path('app/restore_tmp_' . time());
        $zip->extractTo($tmpDir);
        $zip->close();

        $storageSrc = $tmpDir . '/storage';
        if (is_dir($storageSrc)) {
            $this->copyDir($storageSrc, storage_path('app/public'));
        }

        // Cleanup
        $this->deleteDir($tmpDir);

        // Flush setting cache
        \App\Models\Setting::flush();

        AuditLog::record('restore_executed');

        return back()->with('success', 'Restore berhasil. Sistem telah dipulihkan dari backup.');
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function dumpDatabase(): string
    {
        $pdo     = DB::connection()->getPdo();
        $db      = config('database.connections.' . config('database.default') . '.database');
        $tables  = DB::select('SHOW TABLES');
        $colName = 'Tables_in_' . $db;
        $sql     = "-- Smart Village Backup\n-- Generated: " . now()->toDateTimeString() . "\n-- Database: {$db}\n\nSET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($tables as $tableRow) {
            $table   = $tableRow->$colName;
            $createRow = DB::select("SHOW CREATE TABLE `{$table}`");
            $createKey = 'Create Table';
            $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";
            $sql .= $createRow[0]->$createKey . ";\n\n";

            $rows = DB::table($table)->get();
            if ($rows->isNotEmpty()) {
                $cols   = array_keys((array) $rows->first());
                $colStr = '`' . implode('`, `', $cols) . '`';
                $sql   .= "INSERT INTO `{$table}` ({$colStr}) VALUES\n";
                $vals   = $rows->map(function ($row) use ($pdo) {
                    $escaped = array_map(fn($v) => $v === null ? 'NULL' : $pdo->quote($v), (array) $row);
                    return '(' . implode(', ', $escaped) . ')';
                })->implode(",\n");
                $sql .= $vals . ";\n\n";
            }
        }

        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
        return $sql;
    }

    private function addDirToZip(ZipArchive $zip, string $dir, string $zipBase): void
    {
        $iter = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS)
        );
        foreach ($iter as $file) {
            if ($file->isFile()) {
                $relative = $zipBase . '/' . ltrim(str_replace($dir, '', $file->getPathname()), '/\\');
                $zip->addFile($file->getPathname(), $relative);
            }
        }
    }

    private function copyDir(string $src, string $dst): void
    {
        if (!is_dir($dst)) mkdir($dst, 0755, true);
        foreach (scandir($src) as $item) {
            if ($item === '.' || $item === '..') continue;
            $s = $src . DIRECTORY_SEPARATOR . $item;
            $d = $dst . DIRECTORY_SEPARATOR . $item;
            is_dir($s) ? $this->copyDir($s, $d) : copy($s, $d);
        }
    }

    private function deleteDir(string $dir): void
    {
        if (!is_dir($dir)) return;
        foreach (scandir($dir) as $item) {
            if ($item === '.' || $item === '..') continue;
            $p = $dir . DIRECTORY_SEPARATOR . $item;
            is_dir($p) ? $this->deleteDir($p) : unlink($p);
        }
        rmdir($dir);
    }

    private function sanitizeEnv(string $content): string
    {
        // Hapus baris yang mengandung password, token, secret, key
        return preg_replace('/^(.*(?:PASSWORD|TOKEN|SECRET|APP_KEY|WA_TOKEN).*)$/mi', '$1_REDACTED', $content);
    }

    private function formatSize(int $bytes): string
    {
        if ($bytes >= 1048576) return round($bytes / 1048576, 1) . ' MB';
        if ($bytes >= 1024)    return round($bytes / 1024, 1) . ' KB';
        return $bytes . ' B';
    }
}
