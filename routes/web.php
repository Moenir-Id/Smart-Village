<?php
use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth\AdminLoginController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('warga.index'))->name('home');
Route::get('/ajukan', fn () => view('warga.ajukan'))->name('warga.ajukan');
Route::get('/lacak/{kode}', fn ($kode) => view('warga.lacak', compact('kode')))->name('warga.lacak');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login',  [AdminLoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminLoginController::class, 'login'])->name('login.post');
    Route::post('/logout',[AdminLoginController::class, 'logout'])->name('logout');
});

Route::prefix('admin')->name('admin.')->middleware(['auth','auto.logout','role:admin,petugas,viewer'])->group(function () {
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

    // Antrean
    Route::prefix('antrean')->name('antrean.')->group(function () {
        Route::get('/',       [Admin\AntreanController::class, 'index'])->name('index');
        Route::get('/data',   [Admin\AntreanController::class, 'data'])->name('data');
        Route::get('/{permohonan}', [Admin\AntreanController::class, 'show'])->name('show');
        Route::patch('/{permohonan}/status', [Admin\AntreanController::class, 'ubahStatus'])->name('ubah-status')->middleware('role:admin,petugas');
    });

    // Ekspor Word
    Route::prefix('ekspor')->name('ekspor.')->middleware('role:admin,petugas')->group(function () {
        Route::post('/{permohonan}',        [Admin\EksporController::class, 'ekspor'])->name('ekspor');
        Route::get('/{permohonan}/status',  [Admin\EksporController::class, 'status'])->name('status');
        Route::get('/{permohonan}/unduh',   [Admin\EksporController::class, 'unduh'])->name('unduh');
    });

    // Template surat
    Route::prefix('template')->name('template.')->middleware('role:admin')->group(function () {
        Route::get('/',              [Admin\TemplateSuratController::class, 'index'])->name('index');
        Route::post('/{jenisSurat}', [Admin\TemplateSuratController::class, 'upload'])->name('upload');
        Route::delete('/{jenisSurat}',[Admin\TemplateSuratController::class, 'hapus'])->name('hapus');
    });

    // Jenis surat
    Route::resource('jenis-surat', Admin\JenisSuratController::class)->middleware('role:admin')->except(['show']);

    // WhatsApp Web
    Route::prefix('whatsapp')->name('whatsapp.')->middleware('role:admin')->group(function () {
        Route::get('/',            [Admin\WhatsappController::class, 'index'])->name('index');
        Route::get('/qr',          [Admin\WhatsappController::class, 'qr'])->name('qr');
        Route::get('/status',      [Admin\WhatsappController::class, 'statusAjax'])->name('status');
        Route::post('/pengaturan', [Admin\WhatsappController::class, 'simpanPengaturan'])->name('pengaturan');
        Route::post('/template',   [Admin\WhatsappController::class, 'simpanTemplate'])->name('template');
        Route::post('/test',       [Admin\WhatsappController::class, 'kirimTest'])->name('test');
        Route::post('/disconnect', [Admin\WhatsappController::class, 'disconnect'])->name('disconnect');
    });

    // Laporan
    Route::prefix('laporan')->name('laporan.')->middleware('role:admin,viewer')->group(function () {
        Route::get('/',     [Admin\LaporanController::class, 'index'])->name('index');
        Route::get('/csv',  [Admin\LaporanController::class, 'eksporCsv'])->name('csv');
        Route::get('/pdf',  [Admin\LaporanController::class, 'eksporPdf'])->name('pdf');  // ← baru
    });

    // Audit Log
    Route::get('/audit-log', [Admin\AuditLogController::class, 'index'])->name('audit-log.index')->middleware('role:admin,viewer');

    // Pengaturan
    Route::prefix('pengaturan')->name('pengaturan.')->middleware('role:admin')->group(function () {
        Route::get('/',           [Admin\PengaturanController::class, 'index'])->name('index');
        Route::post('/identitas', [Admin\PengaturanController::class, 'simpanIdentitas'])->name('identitas');
        Route::post('/operasional',[Admin\PengaturanController::class,'simpanOperasional'])->name('operasional');
    });

    // Users
    Route::resource('users', Admin\UserController::class)->middleware('role:admin');

    // Backup & Restore
    Route::prefix('backup')->name('backup.')->middleware('role:admin')->group(function () {
        Route::get('/',               [Admin\BackupController::class, 'index'])->name('index');
        Route::post('/buat',          [Admin\BackupController::class, 'buat'])->name('buat');
        Route::get('/unduh/{name}',   [Admin\BackupController::class, 'unduh'])->name('unduh');
        Route::delete('/hapus/{name}',[Admin\BackupController::class, 'hapus'])->name('hapus');
        Route::post('/restore',       [Admin\BackupController::class, 'restore'])->name('restore');
    });
});