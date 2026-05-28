@extends('layouts.admin')
@section('title','Antrean Surat')
@section('content')

<style>
  /* Stats: 5 cols desktop, 3 cols mobile, 2 cols tiny */
  .antr-sg{grid-template-columns:repeat(5,1fr)!important}
  @media(max-width:640px){.antr-sg{grid-template-columns:repeat(3,1fr)!important}}
  @media(max-width:380px){.antr-sg{grid-template-columns:repeat(2,1fr)!important}}

  /* Filter bar: stacked on mobile */
  .antr-filter{display:flex;gap:10px;flex-wrap:wrap;padding:14px 16px;border-bottom:1px solid var(--border);background:#fafbfe;border-radius:var(--r) var(--r) 0 0}
  .antr-filter .fc-q{flex:1;min-width:140px}
  .antr-filter .fc-s{width:auto;min-width:130px}
  .antr-filter .fc-d{width:auto}
  @media(max-width:520px){
    .antr-filter{gap:8px}
    .antr-filter .fc-q{min-width:100%;order:0}
    .antr-filter .fc-s{flex:1}
    .antr-filter .fc-d{flex:1}
  }

  /* Desktop table */
  .antr-tbl-wrap{overflow-x:auto;-webkit-overflow-scrolling:touch}

  /* Mobile card list (replaces table rows) */
  .antr-cards{display:none;flex-direction:column;gap:0}
  .antr-card-item{
    display:flex;align-items:flex-start;gap:12px;
    padding:13px 16px;border-bottom:1px solid var(--border);
    text-decoration:none;color:inherit;
    transition:background .12s;
  }
  .antr-card-item:last-child{border-bottom:none}
  .antr-card-item:active{background:#f9fafc}
  .antr-card-icon{
    width:38px;height:38px;border-radius:9px;
    background:var(--p-lt);display:flex;align-items:center;
    justify-content:center;flex-shrink:0;font-size:15px;color:var(--p);margin-top:1px;
  }
  .antr-card-body{flex:1;min-width:0}
  .antr-card-top{display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:3px}
  .antr-card-name{font-weight:700;font-size:13.5px;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
  .antr-card-meta{font-size:11.5px;color:var(--subtle);display:flex;align-items:center;gap:6px;flex-wrap:wrap;margin-top:3px}

  @media(max-width:700px){
    .antr-tbl-wrap{display:none}
    .antr-cards{display:flex}
  }

  /* Pagination */
  .antr-pager{padding:12px 16px;border-top:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;background:#fafbfe;border-radius:0 0 var(--r) var(--r);flex-wrap:wrap;gap:8px}
</style>

<div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;margin-bottom:20px">
  <h2 style="font-size:19px;font-weight:800;margin:0;letter-spacing:-.02em">Antrean Surat</h2>
</div>

<div class="sg antr-sg" style="margin-bottom:20px">
  <div class="sc cp" style="cursor:pointer" onclick="filterStatus('')">
    <div class="sn">{{ $stats['total'] }}</div><div class="sl">Semua</div>
  </div>
  <div class="sc cy" style="cursor:pointer" onclick="filterStatus('pending')">
    <div class="sn">{{ $stats['pending'] }}</div><div class="sl">Menunggu</div>
  </div>
  <div class="sc cb2" style="cursor:pointer" onclick="filterStatus('diproses')">
    <div class="sn">{{ $stats['diproses'] }}</div><div class="sl">Diproses</div>
  </div>
  <div class="sc cg" style="cursor:pointer" onclick="filterStatus('selesai')">
    <div class="sn">{{ $stats['selesai'] }}</div><div class="sl">Selesai</div>
  </div>
  <div class="sc cr" style="cursor:pointer" onclick="filterStatus('overdue')">
    <div class="sn">{{ $stats['overdue'] }}</div><div class="sl">Overdue</div>
  </div>
</div>

<div class="card">
  <div class="antr-filter">
    <input type="text" id="q" class="fc fc-q" placeholder="Cari nama, NIK, kode…" oninput="debounce(loadData,400)()">
    <select id="sel-status" class="fc fc-s" onchange="loadData()">
      <option value="">Semua Status</option>
      <option value="pending">Menunggu</option>
      <option value="diproses">Diproses</option>
      <option value="selesai">Selesai</option>
      <option value="ditolak">Ditolak</option>
    </select>
    <input type="date" id="tgl" class="fc fc-d" onchange="loadData()">
  </div>

  {{-- Desktop table --}}
  <div class="antr-tbl-wrap">
    <table class="tbl">
      <thead>
        <tr>
          <th>Kode</th><th>Pemohon</th><th>Jenis Surat</th>
          <th>Berkas</th><th>Status</th><th>SLA</th><th>Masuk</th><th></th>
        </tr>
      </thead>
      <tbody id="tbl-body">
        <tr><td colspan="8" style="text-align:center;padding:40px;color:var(--subtle)">Memuat…</td></tr>
      </tbody>
    </table>
  </div>

  {{-- Mobile card list --}}
  <div class="antr-cards" id="mob-body">
    <div style="text-align:center;padding:40px;color:var(--subtle)">Memuat…</div>
  </div>

  <div class="antr-pager">
    <span id="tbl-info" style="font-size:12px;color:var(--subtle)"></span>
    <div style="display:flex;gap:6px;align-items:center">
      <button class="btn btn-ol btn-sm" id="btn-prev" onclick="goPage(-1)"><i class="bi bi-chevron-left"></i></button>
      <span id="tbl-page" style="font-size:12.5px;padding:4px 10px;color:var(--subtle)"></span>
      <button class="btn btn-ol btn-sm" id="btn-next" onclick="goPage(1)"><i class="bi bi-chevron-right"></i></button>
    </div>
  </div>
</div>

@endsection
@push('scripts')
<script>
let currentPage=1,lastPage=1;
function debounce(fn,ms){let t;return(...a)=>{clearTimeout(t);t=setTimeout(()=>fn(...a),ms)};}
function filterStatus(s){document.getElementById('sel-status').value=s;currentPage=1;loadData();}
function goPage(d){currentPage=Math.max(1,Math.min(lastPage,currentPage+d));loadData();}

const statusColor={pending:'#fef9c3;color:#854d0e',diproses:'#dbeafe;color:#1e40af',selesai:'#dcfce7;color:#166534',ditolak:'#fee2e2;color:#991b1b'};

function berkasCol(p){
  if(p.foto_purged)return`<span style="font-size:11px;color:var(--subtle)">🗑 Dihapus</span>`;
  const ktp=p.foto_ktp_url
    ?`<a href="${p.foto_ktp_url}" target="_blank" style="display:inline-flex;align-items:center;gap:3px;padding:2px 8px;border-radius:5px;background:#dbeafe;color:#1d4ed8;font-size:11px;font-weight:600;text-decoration:none"><i class="bi bi-person-vcard"></i> KTP</a>`
    :`<span style="display:inline-flex;align-items:center;gap:3px;padding:2px 8px;border-radius:5px;background:#f3f4f6;color:#9ca3af;font-size:11px"><i class="bi bi-person-vcard"></i> KTP</span>`;
  const kk=p.foto_kk_url
    ?`<a href="${p.foto_kk_url}" target="_blank" style="display:inline-flex;align-items:center;gap:3px;padding:2px 8px;border-radius:5px;background:#dcfce7;color:#166534;font-size:11px;font-weight:600;text-decoration:none"><i class="bi bi-people"></i> KK</a>`
    :`<span style="display:inline-flex;align-items:center;gap:3px;padding:2px 8px;border-radius:5px;background:#f3f4f6;color:#9ca3af;font-size:11px"><i class="bi bi-people"></i> KK</span>`;
  return`<div style="display:flex;flex-direction:column;gap:4px">${ktp}${kk}</div>`;
}

async function loadData(){
  const q=document.getElementById('q').value;
  const s=document.getElementById('sel-status').value;
  const t=document.getElementById('tgl').value;
  const params=new URLSearchParams({page:currentPage,q,status:s,tanggal:t});
  const loading='<tr><td colspan="8" style="text-align:center;padding:32px;color:var(--subtle)">Memuat…</td></tr>';
  document.getElementById('tbl-body').innerHTML=loading;
  document.getElementById('mob-body').innerHTML='<div style="text-align:center;padding:32px;color:var(--subtle)">Memuat…</div>';
  try{
    const res=await fetch(`{{ route('admin.antrean.data') }}?${params}`,{headers:{'Accept':'application/json'}});
    const data=await res.json();
    lastPage=data.meta.last_page;
    document.getElementById('tbl-info').textContent=`${data.meta.total} permohonan`;
    document.getElementById('tbl-page').textContent=`${data.meta.current_page} / ${lastPage}`;
    document.getElementById('btn-prev').disabled=currentPage<=1;
    document.getElementById('btn-next').disabled=currentPage>=lastPage;

    if(!data.data.length){
      document.getElementById('tbl-body').innerHTML='<tr><td colspan="8" style="text-align:center;padding:40px;color:var(--subtle)">Tidak ada permohonan.</td></tr>';
      document.getElementById('mob-body').innerHTML='<p style="text-align:center;padding:40px;color:var(--subtle)">Tidak ada permohonan.</p>';
      return;
    }

    // Desktop rows
    document.getElementById('tbl-body').innerHTML=data.data.map(p=>`
      <tr>
        <td><span class="mono" style="font-weight:700;color:var(--p)">${p.kode_unik}</span></td>
        <td><div style="font-weight:600">${p.nama_lengkap}</div><div style="font-size:11.5px;color:var(--subtle)">${p.nik}</div></td>
        <td style="color:var(--subtle);font-size:12.5px">${p.jenis_surat}</td>
        <td>${berkasCol(p)}</td>
        <td>
          <span class="bdg bdg-${p.status}">${p.status_label}</span>
          ${p.sla_overdue?'<span class="bdg" style="background:#fee2e2;color:#991b1b;margin-left:4px">⚠ SLA</span>':''}
        </td>
        <td style="font-size:12px;color:${p.sla_overdue?'#dc2626':'var(--subtle)'};font-family:'DM Mono',monospace">
          ${p.sla_deadline?new Date(p.sla_deadline).toLocaleString('id-ID',{day:'2-digit',month:'2-digit',hour:'2-digit',minute:'2-digit'}):'-'}
        </td>
        <td style="font-size:12px;color:var(--subtle)">${p.created_at}</td>
        <td><a href="/admin/antrean/${p.id}" class="btn btn-ol btn-sm">Detail</a></td>
      </tr>`).join('');

    // Mobile cards
    document.getElementById('mob-body').innerHTML=data.data.map(p=>`
      <a href="/admin/antrean/${p.id}" class="antr-card-item">
        <div class="antr-card-icon"><i class="bi bi-file-earmark-text"></i></div>
        <div class="antr-card-body">
          <div class="antr-card-top">
            <span class="mono" style="font-weight:700;color:var(--p);font-size:11.5px">${p.kode_unik}</span>
            <span class="bdg bdg-${p.status}" style="font-size:10.5px">${p.status_label}</span>
            ${p.sla_overdue?'<span class="bdg" style="background:#fee2e2;color:#991b1b;font-size:10px">⚠ SLA</span>':''}
          </div>
          <div class="antr-card-name">${p.nama_lengkap}</div>
          <div class="antr-card-meta">
            <span>${p.jenis_surat}</span>
            <span style="color:var(--border)">·</span>
            <span>${p.created_at}</span>
          </div>
        </div>
        <i class="bi bi-chevron-right" style="color:var(--border);font-size:13px;margin-top:4px"></i>
      </a>`).join('');
  }catch{
    document.getElementById('tbl-body').innerHTML='<tr><td colspan="8" style="text-align:center;padding:32px;color:#dc2626">Gagal memuat data.</td></tr>';
    document.getElementById('mob-body').innerHTML='<p style="text-align:center;padding:32px;color:#dc2626">Gagal memuat data.</p>';
  }
}

loadData();
setInterval(loadData,30000);
const p=new URLSearchParams(location.search);
if(p.get('status'))document.getElementById('sel-status').value=p.get('status');
</script>
@endpush
