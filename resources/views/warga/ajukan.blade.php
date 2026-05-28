@extends('layouts.warga')
@section('title','Ajukan Surat')
@push('styles')
<style>
  .ajukan-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}
  @media(max-width:520px){.ajukan-grid{grid-template-columns:1fr}}
</style>
@endpush
@section('content')

<div style="margin-bottom:24px">
  <h2 style="font-size:20px;font-weight:800;margin:0;letter-spacing:-.02em">Layanan Surat Desa</h2>
  <p style="font-size:13px;color:var(--subtle);margin:4px 0 0">Ajukan permohonan surat atau lacak status pengajuan Anda.</p>
</div>

{{-- TABS --}}
<div style="display:flex;gap:8px;margin-bottom:20px">
  <button class="tab-btn on" id="btn-form" onclick="switchTab('form')">
    <i class="bi bi-pencil-square"></i> Ajukan Surat
  </button>
  <button class="tab-btn" id="btn-lacak" onclick="switchTab('lacak')">
    <i class="bi bi-search"></i> Lacak Status
  </button>
</div>

{{-- TAB: FORM --}}
<div class="tp on" id="panel-form">

  {{-- Success state --}}
  <div class="card" id="success-card" style="display:none">
    <div class="ch"><i class="bi bi-check-circle-fill" style="color:#16a34a"></i> Permohonan Berhasil Dikirim</div>
    <div class="cb">
      <div style="display:flex;flex-direction:column;align-items:center;text-align:center;padding:12px 0 8px">
        <div style="width:56px;height:56px;background:#dcfce7;border-radius:50%;display:flex;align-items:center;justify-content:center;margin-bottom:16px">
          <i class="bi bi-check-lg" style="font-size:24px;color:#16a34a"></i>
        </div>
        <p style="font-size:14px;color:var(--subtle);margin:0 0 16px">Simpan kode ini untuk memantau status surat Anda</p>
        <div style="background:var(--bg);border:1.5px solid var(--border);border-radius:10px;padding:16px 28px;margin-bottom:20px;min-width:200px">
          <div style="font-size:22px;font-weight:800;letter-spacing:.08em;color:var(--text)" id="kode-result">—</div>
          <div style="font-size:11px;color:var(--subtle);margin-top:4px;text-transform:uppercase;letter-spacing:.1em">Kode Permohonan</div>
        </div>
        <button class="btn btn-p" onclick="resetForm()"><i class="bi bi-plus-circle"></i> Ajukan Surat Lain</button>
      </div>
    </div>
  </div>

  {{-- Form card --}}
  <div id="form-card">

    {{-- Error global --}}
    <div class="al-err" id="err-global" style="display:none;margin-bottom:16px">
      <i class="bi bi-exclamation-circle"></i><span id="err-global-text"></span>
    </div>

    {{-- Section 1: Data Diri --}}
    <div class="card" style="margin-bottom:20px">
      <div class="ch">
        <span style="width:22px;height:22px;background:var(--p);border-radius:50%;display:inline-flex;align-items:center;justify-content:center;color:#fff;font-size:11px;font-weight:800;flex-shrink:0;margin-right:4px">1</span>
        Data Diri
      </div>
      <div class="cb">
        <div class="ajukan-grid">
          <div style="grid-column:span 2">
            <label class="fl">NIK <span style="color:#ef4444">*</span></label>
            <input type="text" class="fc" id="f-nik" inputmode="numeric" maxlength="16" placeholder="16 digit NIK sesuai KTP">
            <div class="fe" id="fe-nik"></div>
          </div>
          <div style="grid-column:span 2">
            <label class="fl">Nama Lengkap <span style="color:#ef4444">*</span></label>
            <input type="text" class="fc" id="f-nama" placeholder="Sesuai KTP">
            <div class="fe" id="fe-nama"></div>
          </div>
          <div style="grid-column:span 2">
            <label class="fl">Nomor WhatsApp <span style="color:#ef4444">*</span></label>
            <input type="tel" class="fc" id="f-wa" placeholder="08xxxxxxxxxx">
            <div class="fe" id="fe-wa"></div>
          </div>
        </div>
      </div>
    </div>

    {{-- Section 2: Detail Surat --}}
    <div class="card" style="margin-bottom:20px">
      <div class="ch">
        <span style="width:22px;height:22px;background:var(--p);border-radius:50%;display:inline-flex;align-items:center;justify-content:center;color:#fff;font-size:11px;font-weight:800;flex-shrink:0;margin-right:4px">2</span>
        Detail Surat
      </div>
      <div class="cb">
        <div style="display:flex;flex-direction:column;gap:16px">
          <div>
            <label class="fl">Jenis Surat <span style="color:#ef4444">*</span></label>
            <select class="fc" id="f-jenis">
              <option value="">Memuat…</option>
            </select>
            <div class="fe" id="fe-jenis"></div>
          </div>
          <div>
            <label class="fl">Keperluan <span style="color:#ef4444">*</span></label>
            <textarea class="fc" id="f-keperluan" rows="3" placeholder="Jelaskan keperluan pengajuan surat ini…"></textarea>
            <div class="fe" id="fe-keperluan"></div>
          </div>
        </div>
      </div>
    </div>

    {{-- Section 3: Berkas --}}
    <div class="card" style="margin-bottom:20px">
      <div class="ch">
        <span style="width:22px;height:22px;background:var(--p);border-radius:50%;display:inline-flex;align-items:center;justify-content:center;color:#fff;font-size:11px;font-weight:800;flex-shrink:0;margin-right:4px">3</span>
        Berkas Pendukung
        <span style="font-size:12px;font-weight:400;color:var(--subtle);margin-left:6px">(opsional)</span>
      </div>
      <div class="cb">
        <div class="ajukan-grid">
          <div>
            <label class="fl">Foto KTP <span style="color:var(--subtle);font-weight:400;font-size:12px">(maks 5MB)</span></label>
            <div class="fu">
              <input type="file" id="f-ktp" accept="image/*" onchange="updateFile('ktp')">
              <div class="fu-lbl">
                <i class="bi bi-credit-card-2-front"></i>
                <div>
                  <div class="fu-name" id="fn-ktp">Upload foto KTP</div>
                  <div class="fu-hint">JPG, PNG · maks 5MB</div>
                </div>
              </div>
            </div>
            <div class="fe" id="fe-ktp"></div>
          </div>
          <div>
            <label class="fl">Foto Kartu Keluarga <span style="color:var(--subtle);font-weight:400;font-size:12px">(maks 5MB)</span></label>
            <div class="fu">
              <input type="file" id="f-kk" accept="image/*" onchange="updateFile('kk')">
              <div class="fu-lbl">
                <i class="bi bi-people"></i>
                <div>
                  <div class="fu-name" id="fn-kk">Upload foto KK</div>
                  <div class="fu-hint">JPG, PNG · maks 5MB</div>
                </div>
              </div>
            </div>
            <div class="fe" id="fe-kk"></div>
          </div>
        </div>
      </div>
    </div>

    {{-- Section 4: PDP + Submit --}}
    <div class="card">
      <div class="ch"><i class="bi bi-shield-check"></i> Persetujuan Data</div>
      <div class="cb">
        <div style="padding:13px 15px;background:#f0fdf4;border-radius:9px;border:1px solid #bbf7d0;margin-bottom:16px">
          <p style="font-size:12.5px;color:#166534;margin:0">
            <i class="bi bi-info-circle me-1"></i>
            Data Anda (NIK, nama, foto KTP/KK) diproses oleh perangkat desa untuk penerbitan surat sesuai <strong>UU No. 27 Tahun 2022 tentang PDP</strong>. Foto dihapus otomatis {{ \App\Models\Setting::get('purge_foto_hari',30) }} hari setelah permohonan selesai.
          </p>
        </div>
        <label style="display:flex;align-items:flex-start;gap:10px;cursor:pointer;margin-bottom:20px">
          <input type="checkbox" id="f-pdp" style="accent-color:var(--p);margin-top:2px;flex-shrink:0" onchange="document.getElementById('btn-submit').disabled=!this.checked">
          <span style="font-size:13px;color:var(--text)">Saya menyetujui pemrosesan data di atas</span>
        </label>
        <div class="fe" id="fe-pdp"></div>
        <button class="btn btn-p" id="btn-submit" disabled onclick="submitForm()">
          <span id="btn-text"><i class="bi bi-send"></i> Kirim Permohonan</span>
          <span id="btn-load" style="display:none;align-items:center;gap:8px"><div class="spin"></div> Memproses…</span>
        </button>
      </div>
    </div>

  </div>
</div>

{{-- TAB: LACAK (REDESIGNED) --}}
<div class="tp" id="panel-lacak">
  
  {{-- Kartu 1: Pencarian --}}
  <div class="card" style="margin-bottom:20px">
    <div class="ch">
      <i class="bi bi-search"></i> Cari Permohonan
    </div>
    <div class="cb">
      <label class="fl">Kode Permohonan</label>
		<div style="display:flex;flex-direction:column;gap:10px;margin-bottom:14px">
		  <input type="text" class="fc" id="lacak-kode" placeholder="Contoh: PRWS-XXXXX" maxlength="20"
			style="text-transform:uppercase"
			onkeydown="if(event.key==='Enter')lacak()">
		  <button class="btn btn-p" onclick="lacak()" style="width:100%">
			<i class="bi bi-search"></i> Cek Status
		  </button>
		</div>
      
      <div class="al-err" id="lacak-err" style="display:none">
        <i class="bi bi-exclamation-circle"></i><span id="lacak-err-text"></span>
      </div>
      <div class="ld-w" id="lacak-ld" style="display:none"><div class="ld-sp"></div></div>
    </div>
  </div>

  {{-- Kartu 2: Hasil Pencarian (Hidden by default) --}}
  <div class="card" id="card-result" style="display:none">
    <div class="ch">
      <i class="bi bi-card-checklist"></i> Status & Riwayat
    </div>
    <div class="cb">
      {{-- Info Header --}}
      <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;margin-bottom:20px;padding-bottom:16px;border-bottom:1px solid var(--border)">
        <div>
          <div style="font-size:18px;font-weight:800;letter-spacing:.04em;margin-bottom:4px;color:var(--text)" id="r-kode"></div>
          <div style="font-size:14px;font-weight:600;color:var(--text)" id="r-nama"></div>
          <div style="font-size:12px;color:var(--subtle);margin-top:2px" id="r-jenis"></div>
        </div>
        <span class="sbdg" id="r-badge" style="font-size:11px;padding:4px 10px;border-radius:20px;white-space:nowrap"></span>
      </div>

      {{-- Catatan Petugas --}}
      <div id="r-ctn" class="note-box" style="display:none;margin-bottom:20px;background:#eff6ff;border:1px solid #bfdbfe;padding:12px;border-radius:8px;color:#1e40af;font-size:13px">
        <strong style="display:block;margin-bottom:4px"><i class="bi bi-clipboard-data"></i> Catatan Petugas:</strong>
        <span id="r-ctn-text"></span>
      </div>

      {{-- Timeline --}}
      <div style="background:#f0fdf4;border-radius:9px;border:1px solid #bbf7d0;padding:16px">
        <div style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#166534;margin-bottom:12px">Riwayat Status</div>
        <div id="r-tl"></div>
      </div>
    </div>
  </div>

</div>

{{-- Info jam pelayanan --}}
<div style="text-align:center;margin-top:20px;font-size:12px;color:var(--subtle);display:flex;align-items:center;justify-content:center;gap:6px">
  <i class="bi bi-clock"></i>
  Jam Pelayanan: Senin–Jumat {{ \App\Models\Setting::get('jam_kerja_buka','08:00') }}–{{ \App\Models\Setting::get('jam_kerja_tutup','15:00') }} WIB
</div>

@endsection
@push('scripts')
<script>
const SL = {pending:'Menunggu',diproses:'Sedang Diproses',selesai:'Selesai',ditolak:'Ditolak'};
const SC = {pending:'s-pending',diproses:'s-diproses',selesai:'s-selesai',ditolak:'s-ditolak'};

function switchTab(t){
  ['form','lacak'].forEach(x=>{
    document.getElementById('panel-'+x).classList.toggle('on',x===t);
    document.getElementById('btn-'+x).classList.toggle('on',x===t);
  });
}

function updateFile(f){
  const file=document.getElementById('f-'+f).files[0];
  document.getElementById('fn-'+f).textContent=file?file.name:(f==='ktp'?'Upload foto KTP':'Upload foto KK');
}

(async()=>{
  try{
    const r=await fetch('/api/v1/jenis-surat');
    const j=await r.json();
    const s=document.getElementById('f-jenis');
    s.innerHTML='<option value="">— Pilih jenis surat —</option>';
    (j.data||[]).forEach(x=>s.innerHTML+=`<option value="${x.id}">${x.nama}</option>`);
  }catch{document.getElementById('f-jenis').innerHTML='<option value="">Gagal memuat. Refresh halaman.</option>';}
})();

function setErr(f,m){
  const el=document.getElementById('f-'+f);if(el)el.classList.add('is-invalid');
  const fe=document.getElementById('fe-'+f);if(fe){fe.textContent=m;fe.classList.add('show');}
}
function clearErrs(){
  ['nik','nama','wa','jenis','keperluan','ktp','kk','pdp'].forEach(f=>{
    const el=document.getElementById('f-'+f);if(el)el.classList.remove('is-invalid');
    const fe=document.getElementById('fe-'+f);if(fe){fe.textContent='';fe.classList.remove('show');}
  });
  document.getElementById('err-global').style.display='none';
}

async function submitForm(){
  clearErrs();
  if(!document.getElementById('f-pdp').checked){
    const fe=document.getElementById('fe-pdp');fe.textContent='Wajib disetujui.';fe.classList.add('show');return;
  }
  const btn=document.getElementById('btn-submit');
  btn.disabled=true;
  document.getElementById('btn-text').style.display='none';
  document.getElementById('btn-load').style.display='flex';
  try{
    const fd=new FormData();
    fd.append('nik',            document.getElementById('f-nik').value);
    fd.append('nama_lengkap',   document.getElementById('f-nama').value);
    fd.append('nomor_wa',       document.getElementById('f-wa').value);
    fd.append('jenis_surat_id', document.getElementById('f-jenis').value);
    fd.append('keperluan',      document.getElementById('f-keperluan').value);
    fd.append('setuju_pdp',     '1');
    const ktp=document.getElementById('f-ktp').files[0];
    const kk =document.getElementById('f-kk').files[0];
    if(ktp)fd.append('foto_ktp',ktp);
    if(kk) fd.append('foto_kk',kk);
    const res=await fetch('/api/v1/permohonan/buat',{
      method:'POST',body:fd,
      headers:{'Accept':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content}
    });
    const data=await res.json();
    if(!res.ok){
      if(res.status===422&&data.errors){
        const map={nik:'nik',nama_lengkap:'nama',nomor_wa:'wa',jenis_surat_id:'jenis',keperluan:'keperluan',foto_ktp:'ktp',foto_kk:'kk'};
        Object.entries(data.errors).forEach(([k,v])=>{if(map[k])setErr(map[k],v[0]);});
      }else{
        document.getElementById('err-global-text').textContent=data.message||'Terjadi kesalahan.';
        document.getElementById('err-global').style.display='flex';
      }
      return;
    }
    document.getElementById('kode-result').textContent=data.kode_unik;
    document.getElementById('form-card').style.display='none';
    document.getElementById('success-card').style.display='block';
  }catch{
    document.getElementById('err-global-text').textContent='Gagal mengirim. Periksa koneksi internet.';
    document.getElementById('err-global').style.display='flex';
  }finally{
    btn.disabled=false;
    document.getElementById('btn-text').style.display='flex';
    document.getElementById('btn-load').style.display='none';
    if(document.getElementById('f-pdp').checked)btn.disabled=false;
  }
}

function resetForm(){
  ['f-nik','f-nama','f-wa','f-keperluan'].forEach(id=>document.getElementById(id).value='');
  document.getElementById('f-jenis').selectedIndex=0;
  document.getElementById('f-ktp').value='';
  document.getElementById('f-kk').value='';
  document.getElementById('f-pdp').checked=false;
  document.getElementById('btn-submit').disabled=true;
  document.getElementById('success-card').style.display='none';
  document.getElementById('form-card').style.display='block';
  updateFile('ktp');updateFile('kk');
}

async function lacak(){
  const kode=document.getElementById('lacak-kode').value.trim().toUpperCase();
  if(!kode)return;
  document.getElementById('lacak-err').style.display='none';
  document.getElementById('card-result').style.display='none'; // Updated ID
  document.getElementById('lacak-ld').style.display='flex';
  try{
    const res=await fetch(`/api/v1/permohonan/lacak/${kode}`,{headers:{'Accept':'application/json'}});
    const data=await res.json();
    document.getElementById('lacak-ld').style.display='none';
    if(!res.ok){
      document.getElementById('lacak-err-text').textContent=data.message||'Kode tidak ditemukan.';
      document.getElementById('lacak-err').style.display='flex';return;
    }
    document.getElementById('r-kode').textContent=data.kode_unik;
    document.getElementById('r-nama').textContent=data.nama_lengkap;
    document.getElementById('r-jenis').textContent=data.jenis_surat;
    const b=document.getElementById('r-badge');
    b.textContent=SL[data.status]||data.status;
    b.className='sbdg '+(SC[data.status]||'');
    const ctn=document.getElementById('r-ctn');
    if(data.catatan){document.getElementById('r-ctn-text').textContent=data.catatan;ctn.style.display='block';}
    else ctn.style.display='none';
    document.getElementById('r-tl').innerHTML=(data.timeline||[]).map((t,i,a)=>`
      <div class="tl-i">
        <div class="tl-dot ${i===a.length-1?'on':''}"></div>
        <div>
          <div class="tl-st">${SL[t.status]||t.status}</div>
          <div class="tl-time">${t.created_at}</div>
          ${t.catatan?`<div class="tl-note">${t.catatan}</div>`:''}
        </div>
      </div>`).join('');
    document.getElementById('card-result').style.display='block'; // Updated ID
  }catch{
    document.getElementById('lacak-ld').style.display='none';
    document.getElementById('lacak-err-text').textContent='Gagal menghubungi server.';
    document.getElementById('lacak-err').style.display='flex';
  }
}

const p=new URLSearchParams(location.search);
if(p.get('lacak')){switchTab('lacak');document.getElementById('lacak-kode').value=p.get('lacak');lacak();}
if(p.get('tab')==='lacak'){switchTab('lacak');}

// Tab button style toggle
document.querySelectorAll('.tab-btn').forEach(btn=>{
  btn.addEventListener('click',()=>{
    document.querySelectorAll('.tab-btn').forEach(b=>b.classList.remove('on'));
    btn.classList.add('on');
  });
});
</script>

<style>
.tab-btn {
  display:inline-flex;align-items:center;gap:7px;
  padding:8px 18px;border-radius:8px;
  font-size:13px;font-weight:600;
  border:1.5px solid var(--border);
  background:var(--bg);color:var(--subtle);
  cursor:pointer;transition:all .15s;
}
.tab-btn.on {
  background:var(--p);color:#fff;border-color:var(--p);
}
.tab-btn:not(.on):hover {
  background:var(--p-lt);color:var(--p);border-color:var(--p);
}
</style>
@endpush