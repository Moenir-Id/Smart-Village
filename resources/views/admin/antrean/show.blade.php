@extends('layouts.admin')
@section('title','Detail '.$permohonan->kode_unik)
@section('content')

<style>
  .show-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}
  .show-grid .span2{grid-column:span 2}
  @media(max-width:640px){
    .show-grid{grid-template-columns:1fr}
    .show-grid .span2{grid-column:span 1}
  }
  .aksi-row{display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap}
  .aksi-row > div{flex:1;min-width:140px}
  .aksi-btns{display:flex;gap:8px;flex-shrink:0;flex-wrap:wrap}
  @media(max-width:500px){
    .aksi-row{flex-direction:column}
    .aksi-btns{width:100%}
    .aksi-btns .btn{flex:1;justify-content:center}
  }
</style>

<div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;flex-wrap:wrap">
  <a href="{{ route('admin.antrean.index') }}" class="btn btn-ol btn-sm"><i class="bi bi-arrow-left"></i> Kembali</a>
  <span class="mono" style="font-size:15px;font-weight:700;color:var(--p)">{{ $permohonan->kode_unik }}</span>
  <span class="bdg bdg-{{ $permohonan->status }}">{{ $permohonan->statusLabel() }}</span>
  @if($permohonan->isSlaOverdue())
    <span class="bdg" style="background:#fee2e2;color:#991b1b">⚠ Overdue SLA</span>
  @endif
</div>

<div class="show-grid">

  {{-- Data Pemohon --}}
  <div class="card">
    <div class="ch"><i class="bi bi-person-badge-fill"></i> Data Pemohon</div>
    <div class="cb" style="padding:16px">
      @foreach([
        ['Nama Lengkap', $permohonan->nama_lengkap],
        ['NIK',          $permohonan->nik],
        ['WhatsApp',     $permohonan->nomor_wa],
        ['Jenis Surat',  $permohonan->jenisSurat->nama],
        ['Keperluan',    $permohonan->keperluan],
        ['Dikirim',      $permohonan->created_at->format('d/m/Y H:i')],
        ['SLA Deadline', $permohonan->sla_deadline?->format('d/m/Y H:i') ?? '-'],
      ] as [$k,$v])
      <div style="display:flex;gap:10px;padding:9px 0;border-bottom:1px solid var(--border)">
        <span style="color:var(--subtle);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;width:100px;flex-shrink:0;padding-top:1px">{{ $k }}</span>
        <span style="font-weight:500;font-size:13px;color:var(--text);word-break:break-word">{{ $v }}</span>
      </div>
      @endforeach
    </div>
  </div>

  {{-- Berkas Foto --}}
  <div class="card">
    <div class="ch"><i class="bi bi-images"></i> Berkas Foto</div>
    <div class="cb" style="padding:16px">
      @if($permohonan->fotoSudahDihapus())
        <div class="al al-warn"><i class="bi bi-trash"></i> Foto telah dihapus otomatis ({{ $permohonan->foto_purged_at?->format('d/m/Y') }}).</div>
      @else
        @if($fotoKtp)
          <p style="font-size:10.5px;font-weight:700;color:var(--subtle);margin-bottom:6px;text-transform:uppercase;letter-spacing:.07em">Foto KTP</p>
          <a href="{{ $fotoKtp }}" target="_blank">
            <img src="{{ $fotoKtp }}" style="max-height:150px;width:100%;object-fit:contain;border-radius:9px;border:1px solid var(--border);margin-bottom:12px">
          </a>
        @endif
        @if($fotoKk)
          <p style="font-size:10.5px;font-weight:700;color:var(--subtle);margin-bottom:6px;text-transform:uppercase;letter-spacing:.07em">Foto KK</p>
          <a href="{{ $fotoKk }}" target="_blank">
            <img src="{{ $fotoKk }}" style="max-height:150px;width:100%;object-fit:contain;border-radius:9px;border:1px solid var(--border)">
          </a>
        @endif
        @if(!$fotoKtp && !$fotoKk)
          <p style="color:var(--subtle);font-size:13px">Tidak ada foto yang diunggah.</p>
        @endif
      @endif
    </div>
  </div>

  {{-- Aksi Petugas --}}
  @can('update', $permohonan)
  <div class="card span2">
    <div class="ch"><i class="bi bi-pencil-square"></i> Ubah Status</div>
    <div class="cb" style="padding:16px">
      <div class="aksi-row">
        <div>
          <label class="fl">Status Baru</label>
          <select id="sel-status" class="fc" onchange="checkCatatan()">
            <option value="diproses" {{ $permohonan->status==='diproses'?'selected':'' }}>Sedang Diproses</option>
            <option value="selesai"  {{ $permohonan->status==='selesai' ?'selected':'' }}>Selesai</option>
            <option value="ditolak"  {{ $permohonan->status==='ditolak' ?'selected':'' }}>Ditolak</option>
          </select>
        </div>
        <div style="flex:2">
          <label class="fl">
            Catatan Petugas
            <span id="catatan-required" style="color:#ef4444;display:none"> *</span>
          </label>
          <input type="text" id="inp-catatan" class="fc" placeholder="Catatan petugas…" value="{{ $permohonan->catatan_petugas }}">
          <p id="catatan-hint" style="font-size:11.5px;color:#ef4444;margin-top:4px;display:none">Wajib diisi saat menolak</p>
        </div>
        <div class="aksi-btns">
          <button class="btn btn-p" onclick="ubahStatus()"><i class="bi bi-check-lg"></i> Simpan</button>
          <button class="btn btn-ol" onclick="eksporWord()"><i class="bi bi-file-earmark-word"></i> Word</button>
        </div>
      </div>
      <div id="status-msg" style="display:none;margin-top:14px;padding:11px 15px;border-radius:9px;font-size:13px"></div>
    </div>
  </div>
  @endcan

  {{-- Timeline --}}
  <div class="card span2">
    <div class="ch"><i class="bi bi-clock-history"></i> Riwayat Status</div>
    <div class="cb" style="padding:16px">
      @foreach($permohonan->timeline as $t)
      <div class="tl-item">
        <div class="tl-dot {{ $loop->last ? 'on' : '' }}"></div>
        <div>
          <div style="font-size:13.5px;font-weight:700">{{ $t->status }}</div>
          @if($t->catatan)<div style="font-size:12.5px;color:var(--subtle);margin-top:2px">{{ $t->catatan }}</div>@endif
          <div style="font-size:11.5px;color:var(--subtle);margin-top:3px">
            {{ $t->created_at->format('d/m/Y H:i') }}
            @if($t->user) · <strong>{{ $t->user->name }}</strong>@endif
          </div>
        </div>
      </div>
      @endforeach
    </div>
  </div>

</div>
@endsection
@push('scripts')
<script>
function checkCatatan(){
  const isDitolak=document.getElementById('sel-status').value==='ditolak';
  document.getElementById('catatan-required').style.display=isDitolak?'inline':'none';
  document.getElementById('catatan-hint').style.display=isDitolak?'block':'none';
}
checkCatatan();

async function ubahStatus(){
  const status=document.getElementById('sel-status').value;
  const catatan=document.getElementById('inp-catatan').value;
  const msg=document.getElementById('status-msg');
  if(status==='ditolak'&&!catatan.trim()){
    document.getElementById('inp-catatan').classList.add('is-invalid');
    document.getElementById('catatan-hint').style.display='block';
    return;
  }
  const res=await fetch('{{ route("admin.antrean.ubah-status",$permohonan) }}',{
    method:'PATCH',
    headers:{'X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content,'Content-Type':'application/json','Accept':'application/json'},
    body:JSON.stringify({status,catatan}),
  });
  const data=await res.json();
  msg.style.display='block';
  if(res.ok){
    msg.style.cssText='display:block;margin-top:14px;padding:11px 15px;border-radius:9px;font-size:13px;background:#f0fdf4;border:1px solid #bbf7d0;color:#166534';
    msg.innerHTML='✓ Status berhasil diubah.'+(data.wa_terkirim?' <span style="opacity:.7">WA terkirim.</span>':'');
    setTimeout(()=>location.reload(),1000);
  } else {
    msg.style.cssText='display:block;margin-top:14px;padding:11px 15px;border-radius:9px;font-size:13px;background:#fef2f2;border:1px solid #fecaca;color:#991b1b';
    msg.textContent=data.message||'Gagal mengubah status.';
  }
}

async function eksporWord(){
  const res=await fetch('{{ route("admin.ekspor.ekspor",$permohonan) }}',{
    method:'POST',headers:{'X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content}
  });
  const json=await res.json();
  alert(json.message??'Ekspor dijadwalkan.');
}
</script>
@endpush
