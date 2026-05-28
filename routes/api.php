<?php
// routes/api.php

use App\Http\Controllers\Api\PermohonanController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — Smart Village
|--------------------------------------------------------------------------
| Publik (tanpa token): pengajuan & lacak surat warga
| Admin (Sanctum token): endpoint manajemen (opsional untuk mobile app)
*/

Route::prefix('v1')->name('api.v1.')->group(function () {

    // ── Publik (tanpa auth) ───────────────────────────────────────────────
    Route::get('/jenis-surat', [PermohonanController::class, 'jenisSurat'])->name('jenis-surat');
    Route::post('/permohonan/buat', [PermohonanController::class, 'buat'])->name('permohonan.buat');
    Route::get('/permohonan/lacak/{kode}', [PermohonanController::class, 'lacak'])->name('permohonan.lacak');

    // ── Admin (Sanctum token) ─────────────────────────────────────────────
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/admin/permohonan', [\App\Http\Controllers\Api\Admin\PermohonanApiController::class, 'index']);
        Route::get('/admin/permohonan/{id}', [\App\Http\Controllers\Api\Admin\PermohonanApiController::class, 'show']);
        Route::patch('/admin/permohonan/{id}/status', [\App\Http\Controllers\Api\Admin\PermohonanApiController::class, 'ubahStatus']);
        Route::get('/admin/dashboard/stats', [\App\Http\Controllers\Api\Admin\DashboardApiController::class, 'stats']);
    });
});
