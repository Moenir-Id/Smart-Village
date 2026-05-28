<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Jenis Surat ──────────────────────────────────────────────────────
        Schema::create('jenis_surat', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('template_path')->nullable()->comment('Path file .docx template');
            $table->json('variabel_tersedia')->nullable()->comment('Daftar tag variabel yang tersedia');
            $table->text('persyaratan')->nullable()->comment('Daftar persyaratan (HTML/teks)');
            $table->boolean('aktif')->default(true);
            $table->unsignedInteger('urutan')->default(0);
            $table->timestamps();
        });

        // ── Permohonan Surat ─────────────────────────────────────────────────
        Schema::create('permohonan', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('kode_unik', 12)->unique()->comment('Kode lacak warga: PRWS-XXXX');

            // Data pemohon (tidak ada tabel master penduduk)
            $table->string('nik', 16)->index();
            $table->string('nama_lengkap');
            $table->string('nomor_wa', 20);

            $table->foreignId('jenis_surat_id')->constrained('jenis_surat');
            $table->text('keperluan');

            // Status & alur
            $table->enum('status', ['pending', 'diproses', 'selesai', 'ditolak'])
                  ->default('pending')
                  ->index();

            // Berkas (foto KTP / KK)
            $table->string('foto_ktp_path')->nullable();
            $table->string('foto_kk_path')->nullable();
            $table->timestamp('foto_purged_at')->nullable()->comment('Waktu foto dihapus otomatis');

            // Dokumen hasil ekspor
            $table->string('docx_path')->nullable();

            // Proses
            $table->text('catatan_petugas')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('processed_at')->nullable();

            // SLA
            $table->timestamp('sla_deadline')->nullable();

            // Versi dokumen (untuk re-upload setelah ditolak)
            $table->unsignedTinyInteger('versi_berkas')->default(1);

            // Consent UU PDP
            $table->boolean('setuju_pdp')->default(false);
            $table->string('ip_address', 45)->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['nik', 'created_at']);
            $table->index(['status', 'sla_deadline']);
        });

        // ── Timeline Status (Audit Trail Permohonan) ─────────────────────────
        Schema::create('permohonan_timeline', function (Blueprint $table) {
            $table->id();
            $table->string('permohonan_id', 26)->index();
            $table->foreign('permohonan_id')->references('id')->on('permohonan')->cascadeOnDelete();

            $table->enum('status', ['pending', 'diproses', 'selesai', 'ditolak']);
            $table->text('catatan')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();
        });

        // ── Versi Berkas ─────────────────────────────────────────────────────
        Schema::create('permohonan_berkas', function (Blueprint $table) {
            $table->id();
            $table->string('permohonan_id', 26);
            $table->foreign('permohonan_id')->references('id')->on('permohonan')->cascadeOnDelete();

            $table->tinyInteger('versi');
            $table->enum('tipe', ['foto_ktp', 'foto_kk', 'dokumen_lain']);
            $table->string('file_path');
            $table->unsignedInteger('file_size_kb')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        // ── Spam Log ─────────────────────────────────────────────────────────
        Schema::create('spam_log', function (Blueprint $table) {
            $table->id();
            $table->string('nik', 16)->index();
            $table->string('ip_address', 45)->nullable();
            $table->unsignedSmallInteger('count')->default(1);
            $table->timestamp('window_start')->useCurrent();
            $table->timestamp('blocked_until')->nullable();

            $table->index(['nik', 'window_start']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spam_log');
        Schema::dropIfExists('permohonan_berkas');
        Schema::dropIfExists('permohonan_timeline');
        Schema::dropIfExists('permohonan');
        Schema::dropIfExists('jenis_surat');
    }
};
