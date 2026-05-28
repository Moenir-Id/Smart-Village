<?php
namespace App\Services;

use App\Models\Permohonan;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappService
{
    // ── URL & Secret bridge (sesuaikan jika ganti port/secret) ───────────────
    private static string $bridgeUrl    = 'http://localhost:3001';
    private static string $bridgeSecret = 'desa-bridge-secret';

    // ── Helper: panggil bridge ────────────────────────────────────────────────
    private static function bridge(string $method, string $path, array $data = []): ?\Illuminate\Http\Client\Response
    {
        try {
            $req = Http::withHeaders(['x-bridge-token' => self::$bridgeSecret])
                       ->timeout(10);

            return strtoupper($method) === 'POST'
                ? $req->post(self::$bridgeUrl . $path, $data)
                : $req->get(self::$bridgeUrl . $path, $data);
        } catch (\Throwable $e) {
            Log::error('[WA Bridge] ' . $e->getMessage());
            return null;
        }
    }

    // ── Status koneksi ────────────────────────────────────────────────────────
    public static function status(): array
    {
        $res = self::bridge('GET', '/status');
        if (!$res || !$res->successful()) {
            return ['ready' => false, 'qr' => null, 'error' => 'Bridge tidak bisa dihubungi. Pastikan wa-bridge.js sudah jalan.'];
        }
        return $res->json();
    }

    // ── QR saja (polling) ─────────────────────────────────────────────────────
    public static function qr(): array
    {
        $res = self::bridge('GET', '/qr');
        if (!$res || !$res->successful()) {
            return ['ready' => false, 'qr' => null];
        }
        return $res->json();
    }

    // ── Kirim pesan langsung ──────────────────────────────────────────────────
    public static function kirim(string $nomor, string $pesan): bool
    {
        $res = self::bridge('POST', '/send', ['nomor' => $nomor, 'pesan' => $pesan]);
        if (!$res || !$res->successful()) {
            Log::error('[WA Bridge] Gagal kirim ke ' . $nomor);
            return false;
        }
        $body = $res->json();
        if (!($body['ok'] ?? false)) {
            Log::error('[WA Bridge] Error: ' . ($body['error'] ?? 'unknown'));
            return false;
        }
        return true;
    }

    // ── Disconnect ────────────────────────────────────────────────────────────
    public static function disconnect(): bool
    {
        $res = self::bridge('POST', '/disconnect');
        return $res && $res->successful() && ($res->json()['ok'] ?? false);
    }

    // ── Template default ──────────────────────────────────────────────────────
    private static function templateDefault(string $status): string
    {
        $nama_desa = Setting::get('desa_nama', 'Desa');
        $jam_buka  = Setting::get('jam_kerja_buka', '08:00');
        $jam_tutup = Setting::get('jam_kerja_tutup', '15:00');

        return match ($status) {
            'diproses' => "Halo *{nama}*, permohonan *{jenis_surat}* Anda dengan kode *{kode_unik}* sedang kami proses. Kami akan menghubungi Anda kembali setelah selesai. Terima kasih 🙏",
            'selesai'  => "Halo *{nama}*, permohonan *{jenis_surat}* Anda dengan kode *{kode_unik}* telah *selesai* ✅\n\nSilakan ambil surat Anda di *Kantor {$nama_desa}* pada jam kerja:\n🕐 Senin–Jumat, {$jam_buka} – {$jam_tutup}\n\nHarap tunjukkan kode ini saat pengambilan.",
            'ditolak'  => "Halo *{nama}*, mohon maaf permohonan *{jenis_surat}* Anda dengan kode *{kode_unik}* tidak dapat kami proses ❌\n\n📋 *Alasan:*\n{catatan}\n\nSilakan hubungi kantor desa untuk informasi lebih lanjut.",
            default    => "Halo *{nama}*, status permohonan *{jenis_surat}* Anda (kode: *{kode_unik}*) telah diperbarui menjadi *{status}*.",
        };
    }

    // ── Kirim notifikasi permohonan ───────────────────────────────────────────
    public static function kirimNotifikasi(Permohonan $permohonan): bool
    {
        if (!Setting::get('wa_notif_aktif', '0')) return false;

        $status  = $permohonan->status;
        $catatan = trim($permohonan->catatan_petugas ?? '');
        if ($status === 'ditolak' && $catatan === '') {
            $catatan = 'Tidak ada keterangan tambahan dari petugas.';
        }

        $templateKey = "wa_template_{$status}";
        $template    = Setting::get($templateKey) ?: self::templateDefault($status);

        $footer = Setting::get('wa_footer', '');
        $pesan  = str_replace(
            ['{nama}', '{kode_unik}', '{status}', '{jenis_surat}', '{catatan}'],
            [$permohonan->nama_lengkap, $permohonan->kode_unik, $permohonan->statusLabel(),
             $permohonan->jenisSurat?->nama ?? '', $catatan],
            $template
        );
        if ($footer) $pesan .= "\n\n" . $footer;

        return self::kirim($permohonan->nomor_wa, $pesan);
    }

    // ── Kirim test ────────────────────────────────────────────────────────────
    public static function sendTest(string $nomor): bool
    {
        $nama  = Setting::get('desa_nama', 'Desa');
        $pesan = "🎉 *Uji Coba Berhasil!*\nKonfigurasi WhatsApp sistem layanan surat *{$nama}* telah aktif dan siap digunakan.\n\n_Pesan ini dikirim otomatis. Mohon tidak dibalas._";
        return self::kirim($nomor, $pesan);
    }
}
