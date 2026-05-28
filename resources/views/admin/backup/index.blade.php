@extends('layouts.admin')
@section('title','Backup & Restore')
@section('content')

<style>
  /* 2 col desktop → 1 col mobile, restore sidebar moves below */
  .bk-grid{display:grid;grid-template-columns:1fr 360px;gap:16px;align-items:start}
  @media(max-width:760px){.bk-grid{grid-template-columns:1fr}}
  /* Backup row buttons: stack icon on tiny screens */
  .bk-item-btns{display:flex;gap:6px;flex-shrink:0}
  @media(max-width:420px){
    .bk-item{flex-wrap:wrap}
    .bk-item-btns{width:100%;justify-content:flex-end}
  }
</style>

<div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;margin-bottom:20px">
  <div>
    <h2 style="font-size:19px;font-weight:800;margin:0;letter-spacing:-.02em">Backup &amp; Restore</h2>
    <p style="font-size:12.5px;color:var(--subtle);margin:3px 0 0">Kelola cadangan data sistem</p>
  </div>
  <form method="POST" action="{{ route('admin.backup.buat') }}">
    @csrf
    <button type="submit" class="btn btn-p btn-sm" onclick="return confirm('Buat backup sekarang?')">
      <i class="bi bi-download"></i> Buat Backup
    </button>
  </form>
</div>

<div class="al al-info" style="margin-bottom:18px">
  <i class="bi bi-info-circle-fill"></i>
  <div style="font-size:13px">
    <strong>Yang dicadangkan:</strong> Seluruh database, file berkas (foto KTP/KK, logo desa), dan konfigurasi sistem.
    Backup tersimpan di <code style="background:rgba(255,255,255,.5);padding:1px 6px;border-radius:4px">storage/app/backups/</code> di server.
  </div>
</div>

<div class="bk-grid">

  {{-- Daftar Backup --}}
  <div class="card">
    <div class="ch"><i class="bi bi-archive-fill"></i> Riwayat Backup</div>
    <div style="padding:0">
      @forelse($files as $f)
      <div class="bk-item" style="display:flex;align-items:center;gap:12px;padding:14px 18px;border-bottom:1px solid var(--border)">
        <div style="width:40px;height:40px;background:#eff6ff;border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
          <i class="bi bi-file-zip-fill" style="font-size:19px;color:#2563eb"></i>
        </div>
        <div style="flex:1;min-width:0">
          <div class="mono" style="font-size:12.5px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $f['name'] }}</div>
          <div style="font-size:12px;color:var(--subtle);margin-top:2px">{{ $f['tanggal'] }} · {{ $f['size'] }}</div>
        </div>
        <div class="bk-item-btns">
          <a href="{{ route('admin.backup.unduh', $f['name']) }}" class="btn btn-ol btn-sm">
            <i class="bi bi-cloud-download"></i><span style="display:none"> Unduh</span>
          </a>
          <form method="POST" action="{{ route('admin.backup.hapus', $f['name']) }}" onsubmit="return confirm('Hapus backup ini?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-sm" style="background:#fef2f2;color:#dc2626;border:1.5px solid #fecaca">
              <i class="bi bi-trash"></i>
            </button>
          </form>
        </div>
      </div>
      @empty
      <div style="text-align:center;padding:48px;color:var(--subtle)">
        <i class="bi bi-archive" style="font-size:36px;display:block;margin-bottom:10px;opacity:.25"></i>
        <div style="font-size:13.5px;font-weight:600;margin-bottom:4px">Belum ada backup</div>
        <div style="font-size:13px">Klik "Buat Backup" untuk memulai.</div>
      </div>
      @endforelse
    </div>
  </div>

  {{-- Restore + Tips --}}
  <div style="display:flex;flex-direction:column;gap:16px">
    <div class="card" style="border-left:3px solid #f59e0b">
      <div class="ch"><i class="bi bi-arrow-counterclockwise" style="color:#f59e0b"></i> Restore dari File</div>
      <div class="cb" style="padding:16px">
        <div class="al al-warn" style="margin-bottom:16px">
          <i class="bi bi-exclamation-triangle-fill"></i>
          <div style="font-size:13px"><strong>Peringatan!</strong> Restore akan <strong>menimpa semua data</strong>. Tidak dapat dibatalkan.</div>
        </div>

        <form method="POST" action="{{ route('admin.backup.restore') }}" enctype="multipart/form-data" id="restore-form">
          @csrf
          <div style="margin-bottom:14px">
            <label class="fl">File Backup (.zip)</label>
            <input type="file" name="file" accept=".zip" class="fc" required id="restore-file" onchange="updateFileInfo()">
            <div id="file-info" style="font-size:12px;color:var(--subtle);margin-top:5px"></div>
          </div>

          <div style="margin-bottom:16px;padding:14px;background:#fef9c3;border-radius:9px;border:1px solid #fde047">
            <label class="fl" style="color:#854d0e">Ketik RESTORE untuk konfirmasi</label>
            <input type="text" name="konfirmasi" class="fc" placeholder="RESTORE" id="restore-confirm"
              oninput="checkConfirm()" autocomplete="off"
              style="font-family:'DM Mono',monospace;font-weight:700;letter-spacing:.1em;text-transform:uppercase">
            @error('konfirmasi')<div class="fi">{{ $message }}</div>@enderror
          </div>

          <button type="submit" class="btn" id="restore-btn" disabled
            style="width:100%;background:#f59e0b;color:#fff;justify-content:center;opacity:.45;cursor:not-allowed"
            onclick="return confirm('YAKIN ingin melakukan restore? Semua data saat ini akan digantikan.')">
            <i class="bi bi-arrow-counterclockwise"></i> Lakukan Restore
          </button>
        </form>
      </div>
    </div>

    <div class="card">
      <div class="ch"><i class="bi bi-lightbulb-fill"></i> Tips</div>
      <div class="cb" style="padding:16px">
        <div style="display:flex;flex-direction:column;gap:10px">
          @foreach([
            'Buat backup sebelum update atau perubahan besar',
            'Simpan backup di tempat lain (Google Drive, PC lokal)',
            'Backup otomatis belum tersedia — lakukan secara berkala',
            'File backup bisa besar jika ada banyak foto KTP/KK',
            'Restore hanya bisa dilakukan oleh Admin',
          ] as $tip)
          <div style="display:flex;gap:9px;font-size:12.5px;color:var(--subtle)">
            <span style="color:var(--p);flex-shrink:0;font-weight:700">·</span>{{ $tip }}
          </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>

</div>

@endsection
@push('scripts')
<script>
function updateFileInfo(){
  const f=document.getElementById('restore-file').files[0];
  if(f){const mb=(f.size/1048576).toFixed(1);document.getElementById('file-info').textContent=`${f.name} (${mb} MB)`;}
  checkConfirm();
}
function checkConfirm(){
  const val=document.getElementById('restore-confirm').value;
  const btn=document.getElementById('restore-btn');
  const ok=val==='RESTORE'&&document.getElementById('restore-file').files.length>0;
  btn.disabled=!ok;btn.style.opacity=ok?'1':'0.45';btn.style.cursor=ok?'pointer':'not-allowed';
}
document.getElementById('restore-file').addEventListener('change',checkConfirm);
</script>
@endpush
