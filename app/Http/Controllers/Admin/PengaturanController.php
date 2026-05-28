<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PengaturanController extends Controller
{
    public function index()
    {
        $s = Setting::all()->keyBy('key')->map->value;
        return view('admin.pengaturan.index', compact('s'));
    }

    public function simpanIdentitas(Request $request)
    {
        $request->validate([
            'desa_nama'        => ['required', 'string', 'max:100'],
            'desa_kecamatan'   => ['nullable', 'string', 'max:100'],
            'desa_kabupaten'   => ['nullable', 'string', 'max:100'],
            'desa_provinsi'    => ['nullable', 'string', 'max:100'],
            'desa_kode_pos'    => ['nullable', 'string', 'max:10'],
            'desa_telepon'     => ['nullable', 'string', 'max:20'],
            'desa_email'       => ['nullable', 'email', 'max:100'],
            'desa_kepala_nama' => ['nullable', 'string', 'max:150'],
            'desa_kepala_nip'  => ['nullable', 'string', 'max:30'],
            'warna_primer'     => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'warna_sekunder'   => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'logo'             => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg', 'max:1024'],
        ]);

        $keys = ['desa_nama','desa_kecamatan','desa_kabupaten','desa_provinsi',
                 'desa_kode_pos','desa_telepon','desa_email','desa_kepala_nama',
                 'desa_kepala_nip','warna_primer','warna_sekunder'];

        foreach ($keys as $key) {
            if ($request->has($key)) {
                Setting::set($key, $request->input($key), $key === 'warna_primer' || $key === 'warna_sekunder' ? 'tampilan' : 'identitas');
            }
        }

        if ($request->hasFile('logo')) {
            $old = Setting::get('desa_logo_path');
            if ($old) Storage::disk('public')->delete($old);
            $path = $request->file('logo')->store('logo', 'public');
            Setting::set('desa_logo_path', $path, 'identitas');
        }

        AuditLog::record('pengaturan_identitas_updated');
        return back()->with('success', 'Identitas desa berhasil disimpan.');
    }

    public function simpanOperasional(Request $request)
    {
        $request->validate([
            'sla_jam'           => ['required', 'integer', 'min:1', 'max:720'],
            'purge_foto_hari'   => ['required', 'integer', 'min:1', 'max:365'],
            'spam_max_per_jam'  => ['required', 'integer', 'min:1', 'max:20'],
            'foto_max_kb'       => ['required', 'integer', 'min:512', 'max:10240'],
            'auto_logout_menit' => ['required', 'integer', 'min:5', 'max:480'],
            'jam_kerja_buka'    => ['required', 'date_format:H:i'],
            'jam_kerja_tutup'   => ['required', 'date_format:H:i'],
            'hari_kerja'        => ['required', 'array', 'min:1'],
            'hari_kerja.*'      => ['in:0,1,2,3,4,5,6'],
        ]);

        $keys = ['sla_jam','purge_foto_hari','spam_max_per_jam','foto_max_kb','auto_logout_menit','jam_kerja_buka','jam_kerja_tutup'];
        foreach ($keys as $key) Setting::set($key, $request->input($key), str_starts_with($key,'jam_') || $key==='hari_kerja' ? 'jam_kerja' : 'operasional');
        Setting::set('hari_kerja', implode(',', $request->hari_kerja), 'jam_kerja');

        AuditLog::record('pengaturan_operasional_updated');
        return back()->with('success', 'Pengaturan operasional berhasil disimpan.');
    }
}
