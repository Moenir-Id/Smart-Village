# 📦 Cara Install Smart Village di Laragon
> Panduan ini khusus untuk menginstall project ini dari ZIP ke Laragon hingga `php artisan` bisa jalan.

---

## Langkah 1 — Ekstrak ZIP ke Laragon

Ekstrak file ZIP ke folder web Laragon:

```
C:\laragon\www\desa\
```

Pastikan isinya seperti ini (ada file `artisan` di root):

```
desa/
├── artisan               ← wajib ada!
├── composer.json
├── package.json
├── .env.example
├── app/
├── bootstrap/
├── config/
├── database/
├── public/
├── resources/
├── routes/
├── storage/
└── wa-bridge/            ← folder WhatsApp bridge
    ├── wa-bridge.js
    └── package.json
```

---

## Langkah 2 — Buat Database

Buka **HeidiSQL** (Laragon → Database), lalu jalankan:

```sql
CREATE DATABASE maaz CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

---

## Langkah 3 — Salin & Edit .env

Buka **Laragon Terminal**, masuk ke folder proyek:

```bash
cd C:\laragon\www\desa
copy .env.example .env
```

Buka file `.env` dengan teks editor, ubah bagian ini:

```env
APP_URL=http://desa.test
DB_DATABASE=maaz
DB_USERNAME=root
DB_PASSWORD=
DESA_NAMA="Nama Desa Anda"
DESA_KECAMATAN="Kecamatan Anda"
DESA_KABUPATEN="Kabupaten Anda"
DESA_KEPALA_NAMA="Nama Kepala Desa"
```

---

## Langkah 4 — Install Composer

```bash
composer install
```

Tunggu sampai selesai (~2–5 menit). Ini akan membuat folder `vendor/`.

---

## Langkah 5 — Generate Key & Migrate

```bash
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
```

Output migrate yang benar:

```
  INFO  Running migrations.
  2025_01_01_000000_create_users_table .......... DONE
  2025_01_01_000001_create_permohonan_tables .... DONE
  2025_01_01_000002_create_audit_and_queue ...... DONE
```

Seeder membuat 3 akun default + 8 jenis surat.

---

## Langkah 6 — Install Node & Build

```bash
npm install
npm run build
```

Untuk development dengan hot-reload (terminal terpisah):

```bash
npm run dev
```

---

## Langkah 7 — Install WA Bridge (sekali saja)

Masuk ke folder bridge lalu install dependensinya:

```bash
cd C:\laragon\www\desa\wa-bridge
npm install
```

Tunggu sampai selesai (~1–2 menit). Ini hanya perlu dilakukan **sekali**.

---

## Langkah 8 — Virtual Host Laragon

Laragon otomatis membuat `http://desa.test`.
Tapi perlu diarahkan ke subfolder `/public`. Buka:

**Laragon → kanan → Apache → sites-enabled → desa.test.conf**

Ubah `DocumentRoot` menjadi:

```apache
DocumentRoot "C:/laragon/www/desa/public"
<Directory "C:/laragon/www/desa/public">
    AllowOverride All
    Require all granted
</Directory>
```

Klik kanan Laragon → **Reload**.

---

## Langkah 9 — Jalankan Queue Worker

Buka terminal baru, jalankan:

```bash
cd C:\laragon\www\desa
php artisan queue:work --sleep=3 --tries=3
```

Biarkan berjalan selama aplikasi digunakan.

---

## 🚀 Menjalankan Aplikasi Sehari-hari

Buat file `jalankan-desa.bat` di Desktop, isi dengan:

```bat
start cmd /k "cd C:\laragon\www\desa && php artisan serve"
start cmd /k "cd C:\laragon\www\desa\wa-bridge && node wa-bridge.js"
```

Klik dua kali file `.bat` tersebut — dua terminal langsung kebuka sekaligus:
- **Terminal 1**: Laravel (port 8000)
- **Terminal 2**: WA Bridge (port 3001, idle di background)

> WA Bridge harus tetap jalan selama aplikasi digunakan agar fitur WhatsApp berfungsi. Setelah scan QR sekali, sesi tersimpan otomatis — tidak perlu scan lagi kecuali logout.

---

## ✅ Akses Aplikasi

| Halaman | URL |
|---------|-----|
| Landing warga | http://127.0.0.1:8000 |
| Login admin | http://127.0.0.1:8000/admin |

## 🔑 Akun Default

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@desa.id | admin123 |
| Petugas | petugas@desa.id | petugas123 |
| Viewer (Kades) | kades@desa.id | viewer123 |

> ⚠️ Ganti semua password default sebelum digunakan secara resmi!

---

## 📱 Setup WhatsApp Web

1. Pastikan WA Bridge sudah jalan (`node wa-bridge.js`)
2. Buka **Admin → WhatsApp Web**
3. QR Code akan muncul otomatis
4. Buka WhatsApp di HP → **Perangkat Tertaut** → **Tautkan Perangkat** → scan QR
5. Status berubah jadi **Terhubung** ✅
6. Aktifkan **Notifikasi Otomatis** di halaman yang sama

> Sesi WA tersimpan di folder `wa-bridge/.wa-session/`. Selama folder ini ada, tidak perlu scan ulang meski bridge di-restart.

---

## Troubleshooting Cepat

**`php artisan` tidak dikenal:**
- Pastikan PHP Laragon sudah ada di PATH
- Atau pakai: `C:\laragon\bin\php\php-8.x.x\php.exe artisan`

**Error `vendor/autoload.php not found`:**
```bash
composer install
```

**Error 500 saat buka browser:**
```bash
php artisan config:clear
php artisan cache:clear
```

**Foto/logo tidak tampil:**
```bash
php artisan storage:link
```

**WA Bridge tidak bisa dihubungi:**
- Pastikan terminal `node wa-bridge.js` sedang aktif
- Cek port 3001 tidak dipakai aplikasi lain
- Restart bridge: tutup terminal, buka lagi, jalankan ulang

**QR WA tidak muncul:**
- Refresh halaman admin WhatsApp
- Jika masih kosong, restart bridge lalu refresh

**Ekspor Word tidak berjalan:**
- Pastikan terminal `queue:work` sedang aktif

---

## Perintah Artisan Penting

```bash
# Reset database (HATI-HATI!)
php artisan migrate:fresh --seed

# Purge foto kadaluarsa manual
php artisan smartvillage:purge-foto

# Clear semua cache
php artisan optimize:clear

# Lihat semua route
php artisan route:list

# Lihat route backup saja
php artisan route:list --name=backup

# Lihat route whatsapp saja
php artisan route:list --name=whatsapp
```