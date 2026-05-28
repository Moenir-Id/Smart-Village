<?php

namespace Database\Seeders;

use App\Models\JenisSurat;
use Illuminate\Database\Seeder;

class JenisSuratSeeder extends Seeder
{
    public function run(): void
    {
        $jenis = [
            ['nama' => 'Surat Keterangan Domisili',          'urutan' => 1],
            ['nama' => 'Surat Keterangan Tidak Mampu (SKTM)', 'urutan' => 2],
            ['nama' => 'Surat Keterangan Usaha',              'urutan' => 3],
            ['nama' => 'Surat Keterangan Kelahiran',          'urutan' => 4],
            ['nama' => 'Surat Keterangan Kematian',           'urutan' => 5],
            ['nama' => 'Surat Keterangan Belum Menikah',      'urutan' => 6],
            ['nama' => 'Surat Pengantar KTP/KK',              'urutan' => 7],
            ['nama' => 'Surat Keterangan Pindah',             'urutan' => 8],
        ];

        foreach ($jenis as $j) {
            JenisSurat::updateOrCreate(['nama' => $j['nama']], [
                'aktif'   => true,
                'urutan'  => $j['urutan'],
                'variabel_tersedia' => json_encode(['nama', 'nik', 'keperluan', 'tanggal_cetak', 'desa_nama', 'desa_kepala_nama']),
            ]);
        }
    }
}
