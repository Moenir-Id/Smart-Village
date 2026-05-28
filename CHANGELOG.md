# Changelog — Smart Village (Layanan Surat Desa)

## v2.0.0 — 2026-05-24 (Full Revision)

### ✨ Fitur Baru
- **Pengaturan dari DB** — Semua konfigurasi (nama desa, warna, jam kerja, WA, dsb.) sekarang disimpan di tabel `settings`, bukan `.env`. Ubah langsung dari halaman Pengaturan tanpa edit file.
- **Logo Desa Dinamis** — Upload logo dari halaman Pengaturan; tampil otomatis di header warga, login, dan sidebar admin.
- **Jam Kerja Dinamis** — Hari kerja dan jam buka/tutup bisa diatur dari Pengaturan; tercantum di pesan WA "Selesai" dan halaman warga.
- **Laporan Bulanan** — Halaman `/admin/laporan` dengan statistik, chart harian, per jenis surat, rata-rata SLA, dan ekspor CSV.
- **Multi-Gateway WhatsApp** — Mendukung Fonnte, Wablas, dan WhaCenter. Ganti gateway dari halaman WA tanpa coding.
- **Footer Pesan WA** — Tambah teks penutup otomatis di semua pesan WA ("_Mohon tidak membalas pesan ini_") agar warga tidak membalas.
- **Template WA per Status** — 3 template terpisah: Diproses, Selesai, Ditolak. Selesai: info ambil + jam kerja. Ditolak: wajib ada `{catatan}`.
- **Backup & Restore** — Buat backup zip (database SQL + file storage), unduh, hapus, dan restore dari file zip. Konfirmasi ketik "RESTORE".
- **Foto KTP/KK Tampil di Admin** — Fix: foto warga kini tampil benar di halaman detail permohonan dengan fallback jika file tidak ada.
- **Catatan Wajib saat Ditolak** — Validasi server + client: field catatan wajib diisi jika status diubah ke "ditolak".

### 🎨 Redesign
- **Layout Admin** — Sidebar gelap modern, topbar bersih, card system konsisten, badge status berwarna, tabel dengan hover state.
- **Halaman Warga** — Mobile-first total: tab switcher, form step-by-step, file upload drag-area, timeline lacak status, jam kerja info.
- **Login Page** — Logo dinamis, typography bersih, no Bootstrap dependency.
- **Dashboard** — Stat cards dengan warna per kategori, tren 7 hari bar chart, alert overdue SLA.

### 🔒 Keamanan
- Validasi `status === ditolak` → catatan wajib (server & client).
- Backup file hanya bisa diakses Admin (`role:admin`).
- Nama file backup divalidasi regex sebelum unduh/hapus.
- Restore memerlukan konfirmasi teks "RESTORE" agar tidak tidak sengaja.
- Sanitasi `.env` saat backup (baris berisi TOKEN/SECRET/PASSWORD diberi suffix `_REDACTED`).
- Foto berkas di-generate URL via `Storage::disk('public')->url()` dengan fallback exists check.

### 🐛 Bug Fix
- `@role` / `@endrole` directive di layout (dulu error karena Spatie tidak terpasang) — sekarang di-register manual di `AppServiceProvider`.
- `$data` vs `$jenisSurat` mismatch di view jenis-surat index.
- `variabel_tersedia` foreach crash jika nilai bukan array — ditambah guard `is_array()`.
- `WhatsappService` dulu baca `.env` langsung — sekarang baca dari `Setting` model.

---

## v1.x — 2026-05-xx (Awal)
- CRUD jenis surat, template Word, antrean permohonan publik
- Multi-role: admin / petugas / viewer
- Anti-spam per NIK, SLA countdown, auto-purge foto
- Timeline status permohonan, audit log
- Auto-logout sesi tidak aktif
