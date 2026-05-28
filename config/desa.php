<?php
/**
 * Konfigurasi desa — nilai diambil dari tabel settings (DB).
 * Fallback ke .env untuk kompatibilitas mundur.
 * Gunakan Setting::get('key') langsung di kode, bukan config('desa.*').
 */
return [
    // Fallback statis (dipakai sebelum DB siap / saat migrate)
    'nama'             => env('DESA_NAMA', 'Desa'),
    'warna_primer'     => env('DESA_WARNA_PRIMER', '#1a6b3a'),
    'warna_sekunder'   => env('DESA_WARNA_SEKUNDER', '#f0a500'),
    'sla_jam'          => (int) env('SLA_JAM', 72),
    'purge_foto_hari'  => (int) env('PURGE_FOTO_HARI', 30),
    'spam_max_per_jam' => (int) env('SPAM_MAX_PER_JAM', 3),
    'foto_max_kb'      => (int) env('FOTO_MAX_KB', 5120),
    'auto_logout_menit'=> (int) env('AUTO_LOGOUT_MENIT', 30),
];
