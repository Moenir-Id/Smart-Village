<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Setting;
use App\Services\WhatsappService;
use Illuminate\Http\Request;

class WhatsappController extends Controller
{
    public function index()
    {
        $s      = Setting::where('group', 'whatsapp')->pluck('value', 'key');
        $status = WhatsappService::status();
        return view('admin.whatsapp.index', compact('s', 'status'));
    }

    // Polling QR (AJAX) — dipanggil tiap 3 detik dari view
    public function qr()
    {
        return response()->json(WhatsappService::qr());
    }

    // Status (AJAX)
    public function statusAjax()
    {
        return response()->json(WhatsappService::status());
    }

    // Disconnect
    public function disconnect()
    {
        $ok = WhatsappService::disconnect();
        return back()->with($ok ? 'success' : 'error', $ok ? 'WhatsApp berhasil diputus.' : 'Gagal memutus koneksi.');
    }

    public function simpanPengaturan(Request $request)
    {
        $request->validate([
            'wa_notif_aktif' => ['nullable'],
            'wa_footer'      => ['nullable', 'string', 'max:300'],
        ]);

        Setting::set('wa_notif_aktif', $request->boolean('wa_notif_aktif') ? '1' : '0', 'whatsapp');
        Setting::set('wa_footer',      $request->wa_footer ?? '', 'whatsapp');

        AuditLog::record('whatsapp_pengaturan_updated');
        return back()->with('success', 'Pengaturan WhatsApp berhasil disimpan.');
    }

    public function simpanTemplate(Request $request)
    {
        $request->validate([
            'wa_template_diproses' => ['nullable', 'string', 'max:1000'],
            'wa_template_selesai'  => ['nullable', 'string', 'max:1000'],
            'wa_template_ditolak'  => ['nullable', 'string', 'max:1000'],
        ]);

        $ditolak = $request->wa_template_ditolak;
        if ($ditolak && !str_contains($ditolak, '{catatan}')) {
            return back()->withInput()->with('error', 'Template "Ditolak" harus mengandung variabel {catatan}.');
        }

        Setting::set('wa_template_diproses', $request->wa_template_diproses ?? '', 'whatsapp');
        Setting::set('wa_template_selesai',  $request->wa_template_selesai  ?? '', 'whatsapp');
        Setting::set('wa_template_ditolak',  $request->wa_template_ditolak  ?? '', 'whatsapp');

        AuditLog::record('whatsapp_template_updated');
        return back()->with('success', 'Template pesan berhasil disimpan.');
    }

    public function kirimTest(Request $request)
    {
        $request->validate(['nomor_tujuan' => ['required', 'string', 'max:20']]);
        $ok = WhatsappService::sendTest($request->nomor_tujuan);
        return back()->with($ok ? 'success' : 'error', $ok
            ? 'Pesan uji coba berhasil dikirim.'
            : 'Gagal mengirim. Pastikan WA sudah tersambung dan bridge jalan.');
    }
}
