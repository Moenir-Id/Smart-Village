@extends('layouts.admin')
@section('title','Pengaturan')
@section('content')

<style>
  /* 2-col outer grid → single col mobile */
  .pg-outer{display:grid;grid-template-columns:1fr 1fr;gap:16px}
  .pg-span2{grid-column:span 2}
  /* 2-col inner field grid → single col mobile */
  .pg-fields{display:grid;grid-template-columns:1fr 1fr;gap:14px}
  /* Jam buka/tutup row */
  .pg-time{display:grid;grid-template-columns:1fr 1fr;gap:12px}
  @media(max-width:700px){
    .pg-outer{grid-template-columns:1fr}
    .pg-span2{grid-column:span 1}
    .pg-fields{grid-template-columns:1fr}
  }
  @media(max-width:480px){
    .content{padding:12px 10px}
  }
</style>

<div style="margin-bottom:20px">
  <h2 style="font-size:19px;font-weight:800;margin:0;letter-spacing:-.02em">Pengaturan</h2>
</div>

<div class="pg-outer">

  {{-- Identitas Desa (full width) --}}
  <div class="card pg-span2">
    <div class="ch"><i class="bi bi-building"></i> Identitas Desa</div>
    <div class="cb" style="padding:16px">
      <form method="POST" action="{{ route('admin.pengaturan.identitas') }}" enctype="multipart/form-data">
        @csrf
        <div class="pg-fields">
          <div>
            <label class="fl">Nama Desa <span style="color:#ef4444">*</span></label>
            <input type="text" name="desa_nama" class="fc" value="{{ $s['desa_nama'] ?? '' }}" required>
          </div>
          <div>
            <label class="fl">Kecamatan</label>
            <input type="text" name="desa_kecamatan" class="fc" value="{{ $s['desa_kecamatan'] ?? '' }}">
          </div>
          <div>
            <label class="fl">Kabupaten</label>
            <input type="text" name="desa_kabupaten" class="fc" value="{{ $s['desa_kabupaten'] ?? '' }}">
          </div>
          <div>
            <label class="fl">Provinsi</label>
            <input type="text" name="desa_provinsi" class="fc" value="{{ $s['desa_provinsi'] ?? '' }}">
          </div>
          <div>
            <label class="fl">Kode Pos</label>
            <input type="text" name="desa_kode_pos" class="fc" value="{{ $s['desa_kode_pos'] ?? '' }}">
          </div>
          <div>
            <label class="fl">Telepon</label>
            <input type="text" name="desa_telepon" class="fc" value="{{ $s['desa_telepon'] ?? '' }}">
          </div>
          <div>
            <label class="fl">Email</label>
            <input type="email" name="desa_email" class="fc" value="{{ $s['desa_email'] ?? '' }}">
          </div>
          <div>
            <label class="fl">Nama Kepala Desa</label>
            <input type="text" name="desa_kepala_nama" class="fc" value="{{ $s['desa_kepala_nama'] ?? '' }}">
          </div>
          <div>
            <label class="fl">NIP Kepala Desa</label>
            <input type="text" name="desa_kepala_nip" class="fc" value="{{ $s['desa_kepala_nip'] ?? '' }}">
          </div>
          <div>
            <label class="fl">Warna Primer</label>
            <div style="display:flex;gap:10px;align-items:center">
              <input type="color" name="warna_primer" value="{{ $s['warna_primer'] ?? '#1a6b3a' }}"
                style="width:44px;height:38px;border:1.5px solid var(--border);border-radius:8px;cursor:pointer;padding:2px;background:#fff">
              <span style="font-size:12px;color:var(--subtle)">Warna tema sistem</span>
            </div>
          </div>
          <div style="grid-column:span 1">
            <label class="fl">Logo Desa <span style="color:var(--subtle);font-weight:400;font-size:11.5px">(PNG/JPG, maks 1MB)</span></label>
            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
              @php $logoPath = $s['desa_logo_path'] ?? ''; @endphp
              @if($logoPath)
                <img src="{{ asset('storage/' . $logoPath) }}" style="height:40px;width:40px;object-fit:contain;border-radius:7px;border:1px solid var(--border);flex-shrink:0">
              @endif
              <input type="file" name="logo" accept="image/*" class="fc" style="flex:1;min-width:0">
            </div>
          </div>
        </div>
        <div style="margin-top:20px">
          <button type="submit" class="btn btn-p"><i class="bi bi-check-lg"></i> Simpan Identitas</button>
        </div>
      </form>
    </div>
  </div>

  {{-- Parameter Operasional --}}
  <div class="card">
    <div class="ch"><i class="bi bi-sliders"></i> Parameter Operasional</div>
    <div class="cb" style="padding:16px">
      <form method="POST" action="{{ route('admin.pengaturan.operasional') }}">
        @csrf
        <div style="display:flex;flex-direction:column;gap:14px">
          <div>
            <label class="fl">SLA Penyelesaian (jam)</label>
            <input type="number" name="sla_jam" class="fc" value="{{ $s['sla_jam'] ?? 72 }}" min="1" max="720">
            <p style="font-size:11.5px;color:var(--subtle);margin-top:4px">Batas waktu penyelesaian surat. Default: 72 jam</p>
          </div>
          <div>
            <label class="fl">Auto-hapus foto (hari)</label>
            <input type="number" name="purge_foto_hari" class="fc" value="{{ $s['purge_foto_hari'] ?? 30 }}" min="1" max="365">
          </div>
          <div>
            <label class="fl">Maks pengajuan per jam / NIK</label>
            <input type="number" name="spam_max_per_jam" class="fc" value="{{ $s['spam_max_per_jam'] ?? 3 }}" min="1" max="20">
          </div>
          <div>
            <label class="fl">Maks ukuran foto (KB)</label>
            <input type="number" name="foto_max_kb" class="fc" value="{{ $s['foto_max_kb'] ?? 5120 }}" min="512" max="10240">
          </div>
          <div>
            <label class="fl">Auto logout admin (menit)</label>
            <input type="number" name="auto_logout_menit" class="fc" value="{{ $s['auto_logout_menit'] ?? 30 }}" min="5" max="480">
          </div>
        </div>
        <div style="margin-top:20px">
          <button type="submit" class="btn btn-p"><i class="bi bi-check-lg"></i> Simpan</button>
        </div>
      </form>
    </div>
  </div>

  {{-- Jam Kerja --}}
  <div class="card">
    <div class="ch"><i class="bi bi-clock-fill"></i> Jam Kerja</div>
    <div class="cb" style="padding:16px">
      <form method="POST" action="{{ route('admin.pengaturan.operasional') }}">
        @csrf
        {{-- hidden fields agar tidak override operasional lain --}}
        <input type="hidden" name="sla_jam"           value="{{ $s['sla_jam'] ?? 72 }}">
        <input type="hidden" name="purge_foto_hari"   value="{{ $s['purge_foto_hari'] ?? 30 }}">
        <input type="hidden" name="spam_max_per_jam"  value="{{ $s['spam_max_per_jam'] ?? 3 }}">
        <input type="hidden" name="foto_max_kb"       value="{{ $s['foto_max_kb'] ?? 5120 }}">
        <input type="hidden" name="auto_logout_menit" value="{{ $s['auto_logout_menit'] ?? 30 }}">

        <div style="display:flex;flex-direction:column;gap:14px">
          <div class="pg-time">
            <div>
              <label class="fl">Jam Buka</label>
              <input type="time" name="jam_kerja_buka" class="fc" value="{{ $s['jam_kerja_buka'] ?? '08:00' }}">
            </div>
            <div>
              <label class="fl">Jam Tutup</label>
              <input type="time" name="jam_kerja_tutup" class="fc" value="{{ $s['jam_kerja_tutup'] ?? '15:00' }}">
            </div>
          </div>
          <div>
            <label class="fl" style="margin-bottom:10px">Hari Kerja</label>
            @php
              $hariAktif = explode(',', $s['hari_kerja'] ?? '1,2,3,4,5');
              $hariLabel = ['0'=>'Min','1'=>'Sen','2'=>'Sel','3'=>'Rab','4'=>'Kam','5'=>'Jum','6'=>'Sab'];
            @endphp
            <div style="display:flex;flex-wrap:wrap;gap:7px">
              @foreach($hariLabel as $val => $nama)
              <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:13px;font-weight:600;
                background:{{ in_array($val,$hariAktif)?'var(--p-lt)':'var(--bg)' }};
                padding:7px 13px;border-radius:20px;
                border:1.5px solid {{ in_array($val,$hariAktif)?'var(--p)':'var(--border)' }};
                color:{{ in_array($val,$hariAktif)?'var(--p)':'var(--subtle)' }};transition:all .15s">
                <input type="checkbox" name="hari_kerja[]" value="{{ $val }}" {{ in_array($val,$hariAktif)?'checked':'' }} style="accent-color:var(--p);display:none">
                {{ $nama }}
              </label>
              @endforeach
            </div>
          </div>
          <div style="padding:12px 14px;background:#f0fdf4;border-radius:9px;border:1px solid #bbf7d0;font-size:12.5px;color:#166534">
            <i class="bi bi-info-circle"></i> Ditampilkan di pesan WA dan halaman warga.
          </div>
        </div>
        <div style="margin-top:20px">
          <button type="submit" class="btn btn-p"><i class="bi bi-check-lg"></i> Simpan Jam Kerja</button>
        </div>
      </form>
    </div>
  </div>

</div>
@endsection
