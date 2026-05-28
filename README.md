# 🏡 Smart Village — Project Zero

> **Layanan Surat Mandiri Digital Desa**  
> Platform administrasi desa yang ringkas, simpel, keren minimalis, dan cepat.

---

## 📋 Daftar Isi

1. [Gambaran Umum](#gambaran-umum)
2. [Struktur Proyek](#struktur-proyek)
3. [Tech Stack](#tech-stack)
4. [Instalasi](#instalasi)
5. [Konfigurasi](#konfigurasi)
6. [Database](#database)
7. [Modul Sistem](#modul-sistem)
8. [API Reference](#api-reference)
9. [Role & Akses](#role--akses)
10. [Deployment](#deployment)

---

## Gambaran Umum

Smart Village adalah aplikasi web **Single-Tenant** pelayanan surat-menyurat desa. Satu instalasi = satu desa. Warga bisa mengajukan surat tanpa akun, cukup isi NIK + WhatsApp + jenis surat, lalu pantau status via kode unik.

### Filosofi Desain
- **Zero Account Warga** — tidak ada registrasi, tidak ada password
- **Zero Master Data** — tidak menyimpan database kependudukan
- **Zero Bloat** — tidak ada fitur izin/cuti, murni surat-menyurat
- **Privacy-First** — foto KTP/KK otomatis terhapus 30 hari setelah selesai

---

## Struktur Proyek

```
smart-village/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/          # Panel operator & petugas
│   │   │   ├── Auth/           # Login admin
│   │   │   └── Api/            # RESTful API publik
│   │   └── Middleware/
│   ├── Models/                 # Eloquent models
│   └── Policies/               # RBAC authorization
├── database/
│   ├── migrations/             # Skema tabel
│   └── seeders/                # Data awal (roles, admin default)
├── resources/
│   ├── views/
│   │   ├── layouts/            # Base layout Blade
│   │   ├── admin/              # Halaman panel admin
│   │   ├── warga/              # Landing page & form warga
│   │   └── components/         # Komponen Blade reusable
│   └── js/
│       ├── pages/              # Vue 3 pages (SPA partial)
│       ├── components/         # Vue components
│       └── composables/        # Vue composables (useForm, useTrack)
├── routes/
│   ├── web.php                 # Route utama
│   └── api.php                 # Route API
├── docs/                       # Dokumentasi tambahan
└── storage/app/templates/      # Upload template .docx desa
```

---

## Tech Stack

| Layer | Teknologi |
|-------|-----------|
| Backend | PHP 8.2 + Laravel 11 |
| Frontend | Vue 3 (Composition API) + Tailwind CSS v3 |
| Database | MySQL / MariaDB (nama DB: `maaz`) |
| Template Engine | Blade + `phpoffice/phpword` |
| Auth | Laravel Sanctum |
| Queue | Laravel Queue (database driver) |
| PWA | Workbox via `vite-plugin-pwa` |
| Dev Env | Laragon (Windows) |

---

## Instalasi

### Prasyarat
- PHP >= 8.2 dengan ekstensi: `zip`, `gd`, `mbstring`, `xml`, `pdo_mysql`
- Node.js >= 18
- Composer
- Laragon (atau XAMPP/Herd)

### Langkah Instalasi

```bash
# 1. Clone / ekstrak proyek
cd C:/laragon/www
# ekstrak smart-village/ di sini

# 2. Install PHP dependencies
composer install

# 3. Install Node dependencies
npm install

# 4. Salin file environment
cp .env.example .env

# 5. Generate application key
php artisan key:generate

# 6. Buat database
# Buka MySQL dan jalankan: CREATE DATABASE maaz;

# 7. Jalankan migrasi + seeder
php artisan migrate --seed

# 8. Build frontend assets
npm run build
# atau development:
npm run dev

# 9. Jalankan queue worker (terminal terpisah)
php artisan queue:work

# 10. Buat symlink storage
php artisan storage:link
```

### Akses Awal
- **Landing Warga**: `http://smart-village.test/`
- **Panel Admin**: `http://smart-village.test/admin`
- **Login Default**: `admin@desa.id` / `admin123`

---

## Konfigurasi

Edit file `.env`:

```env
APP_NAME="Smart Village"
APP_URL=http://smart-village.test

DB_DATABASE=maaz
DB_USERNAME=root
DB_PASSWORD=

# Identitas Desa (dibaca oleh config/desa.php)
DESA_NAMA="Desa Sukamaju"
DESA_KECAMATAN="Kecamatan Cimahi"
DESA_KABUPATEN="Kabupaten Bandung"
DESA_KODE_POS=40534
DESA_TELEPON="022-12345678"
DESA_WARNA_PRIMER="#1a6b3a"
DESA_WARNA_SEKUNDER="#f0a500"

# SLA dalam jam (default 3 hari kerja = 72 jam)
SLA_JAM=72

# Auto-purge foto KTP/KK (dalam hari)
PURGE_FOTO_HARI=30

# Anti-spam: max pengajuan per NIK per jam
SPAM_MAX_PER_JAM=3

# Queue driver
QUEUE_CONNECTION=database
```

---

## Database

### Tabel Utama

#### `permohonan`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | ULID | Primary key |
| kode_unik | varchar(12) | Kode lacak warga (PRWS-XXXX) |
| nik | varchar(16) | NIK pemohon |
| nama_lengkap | varchar(255) | Nama pemohon |
| nomor_wa | varchar(20) | Nomor WhatsApp |
| jenis_surat_id | FK | Jenis surat yang diminta |
| keperluan | text | Keterangan keperluan |
| status | enum | pending/diproses/selesai/ditolak |
| foto_ktp_path | varchar | Path file foto KTP |
| foto_kk_path | varchar | Path file foto KK |
| foto_purged_at | timestamp | Waktu foto dihapus |
| catatan_petugas | text | Catatan dari petugas |
| processed_by | FK users | Petugas yang memproses |
| sla_deadline | timestamp | Batas waktu SLA |
| created_at / updated_at | timestamps | |

#### `jenis_surat`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint | PK |
| nama | varchar | Nama surat (Domisili, SKCK, dll) |
| template_path | varchar | Path template .docx |
| variabel_tersedia | json | Daftar tag variabel template |
| aktif | boolean | Toggle aktif/nonaktif |

#### `users` (Admin/Petugas)
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint | PK |
| name | varchar | Nama petugas |
| email | varchar | Email login |
| password | varchar | Bcrypt |
| role | enum | admin/petugas/viewer |
| last_activity | timestamp | Untuk auto-logout |

#### `audit_logs`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint | PK |
| user_id | FK | Petugas pelaku |
| action | varchar | Nama aksi |
| model_type | varchar | Model yang diubah |
| model_id | varchar | ID record |
| before | json | Data sebelum |
| after | json | Data sesudah |
| ip_address | varchar | IP petugas |
| created_at | timestamp | |

#### `spam_log`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| nik | varchar(16) | NIK yang dipantau |
| count | int | Jumlah pengajuan |
| window_start | timestamp | Awal window 1 jam |

---

## Modul Sistem

### Modul 1: Pengajuan Warga
**File terkait:**
- `PermohonanController` (Api)
- `resources/views/warga/index.blade.php`
- `resources/js/pages/FormPengajuan.vue`
- `resources/js/composables/useImageCompress.js`

**Alur:**
1. Warga buka landing page → isi form
2. Frontend compress foto KTP/KK ke ~200KB via `useImageCompress`
3. POST ke `/api/permohonan/buat` dengan multipart
4. System generate `kode_unik` (format: `PRWS-XXXX`)
5. Warga terima kode → pantau via `/lacak/{kode}`

---

### Modul 2: Panel Admin — Antrean
**File terkait:**
- `Admin/PermohonanController`
- `resources/views/admin/antrean/index.blade.php`
- `resources/js/pages/admin/Antrean.vue`

**Fitur:**
- Tabel real-time (polling setiap 30 detik)
- Row highlight merah jika `NOW() > sla_deadline`
- Filter by status, jenis surat, tanggal
- Bulk action (tandai selesai, ekspor batch)

---

### Modul 3: Ekspor Word (.docx)
**File terkait:**
- `Admin/EksporController`
- `app/Jobs/EksporDocxJob.php`
- `app/Services/DocxTemplateService.php`

**Alur:**
1. Petugas klik "Ekspor ke Word"
2. Job masuk antrian queue
3. `DocxTemplateService` load template `.docx` desa
4. Replace semua tag `${variabel}` dengan data permohonan
5. File hasil disimpan sementara → download link dikirim ke petugas
6. File temp dihapus setelah 1 jam

**Variabel Template Tersedia:**
```
${nama}          ${nik}           ${tanggal_lahir}
${tempat_lahir}  ${jenis_kelamin} ${alamat}
${keperluan}     ${tanggal_surat} ${nomor_surat}
${nama_kepala}   ${nip_kepala}    ${nama_desa}
```

---

### Modul 4: Upload Template Surat
**File terkait:**
- `Admin/TemplateSuratController`
- `resources/views/admin/template/index.blade.php`

**Ketentuan template:**
- Format: `.docx` Microsoft Word
- Tag variabel menggunakan format `${nama_variabel}`
- Kop surat, margin, header/footer **tidak boleh diubah** oleh sistem
- Sistem hanya mengganti teks tag, tidak menyentuh layout

---

### Modul 5: WhatsApp Gateway
**File terkait:**
- `Admin/WhatsappController`
- `resources/views/admin/whatsapp/index.blade.php`
- `resources/js/components/WaGateway.vue`

**Cara kerja:**
- Halaman menampilkan QR Code WhatsApp Web via iframe embed
- Nomor WA dinas desa ditautkan manual oleh operator
- Notifikasi status permohonan dikirim via `wa.me` deeplink (tanpa API berbayar)
- Format pesan notifikasi dikonfigurasi di `config/whatsapp.php`

---

### Modul 6: Auto-Purge Foto
**File terkait:**
- `app/Console/Commands/PurgeFotoKadaluarsa.php`
- Dijadwalkan di `app/Console/Kernel.php` (daily)

**Alur:**
1. Scheduler berjalan setiap hari jam 02:00
2. Query permohonan dengan status `selesai/ditolak` dan `foto_purged_at IS NULL`
3. Jika `updated_at + PURGE_FOTO_HARI <= NOW()` → hapus file fisik
4. Catat `foto_purged_at = NOW()` di database

---

### Modul 7: RBAC & Auto-Logout
**File terkait:**
- `app/Policies/`
- `app/Http/Middleware/AutoLogout.php`
- `app/Http/Middleware/CheckRole.php`

**Matriks Akses:**

| Fitur | Admin | Petugas | Viewer |
|-------|:-----:|:-------:|:------:|
| Konfigurasi sistem | ✅ | ❌ | ❌ |
| Upload template surat | ✅ | ❌ | ❌ |
| Manajemen jenis surat | ✅ | ❌ | ❌ |
| Manajemen user | ✅ | ❌ | ❌ |
| Scan QR WhatsApp | ✅ | ❌ | ❌ |
| Verifikasi & ubah status | ✅ | ✅ | ❌ |
| Ekspor Word | ✅ | ✅ | ❌ |
| Lihat antrean | ✅ | ✅ | ✅ |
| Dashboard & grafik | ✅ | ✅ | ✅ |
| Audit log | ✅ | ❌ | ✅ |

---

## API Reference

### Base URL
```
/api/v1
```

### Autentikasi
API publik (warga) tidak memerlukan token.  
API admin menggunakan **Bearer Token** (Laravel Sanctum).

---

#### `POST /api/permohonan/buat`
Membuat permohonan baru.

**Request (multipart/form-data):**
```json
{
  "nik": "3201234567890001",
  "nama_lengkap": "Budi Santoso",
  "nomor_wa": "081234567890",
  "jenis_surat_id": 3,
  "keperluan": "Keperluan melamar pekerjaan",
  "foto_ktp": "<file>",
  "foto_kk": "<file>",
  "setuju_pdp": true
}
```

**Response 201:**
```json
{
  "success": true,
  "kode_unik": "PRWS-X89A1",
  "pesan": "Permohonan berhasil dikirim. Simpan kode unik Anda.",
  "estimasi_selesai": "2025-05-26T17:00:00+07:00"
}
```

**Response 429 (spam):**
```json
{
  "success": false,
  "pesan": "NIK ini telah melakukan terlalu banyak pengajuan. Coba lagi dalam 60 menit."
}
```

---

#### `GET /api/permohonan/lacak/{kode}`
Melacak status permohonan.

**Response 200:**
```json
{
  "kode_unik": "PRWS-X89A1",
  "nama": "Budi Santoso",
  "jenis_surat": "Surat Keterangan Domisili",
  "status": "diproses",
  "status_label": "Sedang Diproses",
  "timeline": [
    { "status": "pending", "waktu": "2025-05-23T08:00:00+07:00" },
    { "status": "diproses", "waktu": "2025-05-23T10:30:00+07:00" }
  ],
  "catatan_petugas": null
}
```

---

#### `GET /api/jenis-surat` (publik)
Daftar jenis surat yang aktif.

**Response 200:**
```json
{
  "data": [
    { "id": 1, "nama": "Surat Keterangan Domisili" },
    { "id": 2, "nama": "Surat Keterangan Tidak Mampu" },
    { "id": 3, "nama": "Surat Pengantar SKCK" }
  ]
}
```

---

## Role & Akses

### Menambah Petugas Baru
Login sebagai **Admin** → Pengaturan → Manajemen User → Tambah User → pilih role `Petugas`.

### Reset Password
```bash
php artisan tinker
>>> App\Models\User::where('email', 'petugas@desa.id')->first()->update(['password' => bcrypt('passwordbaru')]);
```

---

## Deployment

### Untuk Produksi (VPS/Shared Hosting)

```bash
# Optimasi autoloader
composer install --optimize-autoloader --no-dev

# Cache config & route
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Build assets produksi
npm run build

# Jalankan queue worker dengan Supervisor
# Lihat docs/supervisor.conf untuk konfigurasi
```

### Supervisor (Queue Worker)
Lihat file: `docs/supervisor.conf`

### Cron Job (Auto-Purge & Scheduler)
Tambahkan ke crontab server:
```
* * * * * cd /path/to/smart-village && php artisan schedule:run >> /dev/null 2>&1
```

---

## Lisensi

Sistem ini dikembangkan untuk keperluan internal pemerintahan desa.  
Hak cipta dilindungi. Dilarang mendistribusikan ulang tanpa izin.

---

*Smart Village — Project Zero | Dibuat dengan ❤️ untuk pelayanan desa yang lebih baik*
