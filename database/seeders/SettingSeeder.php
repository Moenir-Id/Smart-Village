<?php
namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            // Identitas desa
            ['key' => 'desa_nama',          'value' => 'Desa Carangrejo',         'group' => 'identitas'],
            ['key' => 'desa_kecamatan',     'value' => 'Kecamatan Sampung',       'group' => 'identitas'],
            ['key' => 'desa_kabupaten',     'value' => 'Kabupaten Ponorogo',      'group' => 'identitas'],
            ['key' => 'desa_provinsi',      'value' => 'Jawa Timur',              'group' => 'identitas'],
            ['key' => 'desa_kode_pos',      'value' => '63452',                   'group' => 'identitas'],
            ['key' => 'desa_telepon',       'value' => '',                        'group' => 'identitas'],
            ['key' => 'desa_email',         'value' => '',                        'group' => 'identitas'],
            ['key' => 'desa_kepala_nama',   'value' => 'Nama Kepala Desa',        'group' => 'identitas'],
            ['key' => 'desa_kepala_nip',    'value' => '',                        'group' => 'identitas'],
            ['key' => 'desa_logo_path',     'value' => '',                        'group' => 'identitas'],
            // Tampilan
            ['key' => 'warna_primer',       'value' => '#1a6b3a',                 'group' => 'tampilan'],
            ['key' => 'warna_sekunder',     'value' => '#f0a500',                 'group' => 'tampilan'],
            // Operasional
            ['key' => 'sla_jam',            'value' => '72',                      'group' => 'operasional'],
            ['key' => 'purge_foto_hari',    'value' => '30',                      'group' => 'operasional'],
            ['key' => 'spam_max_per_jam',   'value' => '3',                       'group' => 'operasional'],
            ['key' => 'foto_max_kb',        'value' => '5120',                    'group' => 'operasional'],
            ['key' => 'auto_logout_menit',  'value' => '30',                      'group' => 'operasional'],
            // Jam kerja
            ['key' => 'jam_kerja_buka',     'value' => '08:00',                   'group' => 'jam_kerja'],
            ['key' => 'jam_kerja_tutup',    'value' => '15:00',                   'group' => 'jam_kerja'],
            ['key' => 'hari_kerja',         'value' => '1,2,3,4,5',              'group' => 'jam_kerja'],
            // WhatsApp
            ['key' => 'wa_gateway',         'value' => 'fonnte',                  'group' => 'whatsapp'],
            ['key' => 'wa_nomor_dinas',     'value' => '',                        'group' => 'whatsapp'],
            ['key' => 'wa_token',           'value' => '',                        'group' => 'whatsapp'],
            ['key' => 'wa_notif_aktif',     'value' => '0',                       'group' => 'whatsapp'],
            ['key' => 'wa_footer',          'value' => '_Pesan ini dikirim otomatis oleh sistem layanan surat Desa. Mohon tidak membalas pesan ini._', 'group' => 'whatsapp'],
            ['key' => 'wa_template_diproses','value' => '',                       'group' => 'whatsapp'],
            ['key' => 'wa_template_selesai', 'value' => '',                       'group' => 'whatsapp'],
            ['key' => 'wa_template_ditolak', 'value' => '',                       'group' => 'whatsapp'],
        ];

        foreach ($defaults as $row) {
            Setting::updateOrCreate(['key' => $row['key']], ['value' => $row['value'], 'group' => $row['group']]);
        }
    }
}
