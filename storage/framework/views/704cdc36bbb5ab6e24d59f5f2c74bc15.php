<?php $__env->startSection('title','Antrean Surat'); ?>
<?php $__env->startSection('content'); ?>

<div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;margin-bottom:24px">
  <h2 style="font-size:20px;font-weight:800;margin:0;letter-spacing:-.02em">Antrean Surat</h2>
</div>

<div class="sg" style="grid-template-columns:repeat(5,1fr);margin-bottom:22px">
  <div class="sc cp" style="cursor:pointer" onclick="filterStatus('')">
    <div class="sn"><?php echo e($stats['total']); ?></div><div class="sl">Semua</div>
  </div>
  <div class="sc cy" style="cursor:pointer" onclick="filterStatus('pending')">
    <div class="sn"><?php echo e($stats['pending']); ?></div><div class="sl">Menunggu</div>
  </div>
  <div class="sc cb2" style="cursor:pointer" onclick="filterStatus('diproses')">
    <div class="sn"><?php echo e($stats['diproses']); ?></div><div class="sl">Diproses</div>
  </div>
  <div class="sc cg" style="cursor:pointer" onclick="filterStatus('selesai')">
    <div class="sn"><?php echo e($stats['selesai']); ?></div><div class="sl">Selesai</div>
  </div>
  <div class="sc cr" style="cursor:pointer" onclick="filterStatus('overdue')">
    <div class="sn"><?php echo e($stats['overdue']); ?></div><div class="sl">Overdue</div>
  </div>
</div>

<div class="card">
  <div style="padding:16px 22px;border-bottom:1px solid var(--border);display:flex;gap:10px;flex-wrap:wrap;background:#fafbfe;border-radius:var(--r) var(--r) 0 0">
    <input type="text" id="q" class="fc" style="flex:1;min-width:180px" placeholder="Cari nama, NIK, kode…" oninput="debounce(loadData,400)()">
    <select id="sel-status" class="fc" style="width:auto" onchange="loadData()">
      <option value="">Semua Status</option>
      <option value="pending">Menunggu</option>
      <option value="diproses">Diproses</option>
      <option value="selesai">Selesai</option>
      <option value="ditolak">Ditolak</option>
    </select>
    <input type="date" id="tgl" class="fc" style="width:auto" onchange="loadData()">
  </div>

  <div style="overflow-x:auto">
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

  <div style="padding:14px 22px;border-top:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;background:#fafbfe;border-radius:0 0 var(--r) var(--r)">
    <span id="tbl-info" style="font-size:12px;color:var(--subtle)"></span>
    <div style="display:flex;gap:6px;align-items:center">
      <button class="btn btn-ol btn-sm" id="btn-prev" onclick="goPage(-1)"><i class="bi bi-chevron-left"></i></button>
      <span id="tbl-page" style="font-size:12.5px;padding:4px 10px;color:var(--subtle)"></span>
      <button class="btn btn-ol btn-sm" id="btn-next" onclick="goPage(1)"><i class="bi bi-chevron-right"></i></button>
    </div>
  </div>
</div>

<?php $__env->stopSection(); ?>
<?php $__env->startPush('scripts'); ?>
<script>
let currentPage=1, lastPage=1;

function debounce(fn,ms){let t;return(...a)=>{clearTimeout(t);t=setTimeout(()=>fn(...a),ms)};}
function filterStatus(s){document.getElementById('sel-status').value=s;currentPage=1;loadData();}
function goPage(d){currentPage=Math.max(1,Math.min(lastPage,currentPage+d));loadData();}

function berkasCol(p) {
  if (p.foto_purged) {
    return `<span style="font-size:11px;color:var(--subtle)">🗑 Dihapus</span>`;
  }
  const ktp = p.foto_ktp_url
    ? `<a href="${p.foto_ktp_url}" target="_blank"
          style="display:inline-flex;align-items:center;gap:3px;padding:2px 8px;border-radius:5px;
                 background:#dbeafe;color:#1d4ed8;font-size:11px;font-weight:600;text-decoration:none"
          title="Lihat foto KTP">
          <i class="bi bi-person-vcard"></i> KTP
       </a>`
    : `<span style="display:inline-flex;align-items:center;gap:3px;padding:2px 8px;border-radius:5px;
                    background:#f3f4f6;color:#9ca3af;font-size:11px">
          <i class="bi bi-person-vcard"></i> KTP
       </span>`;
  const kk = p.foto_kk_url
    ? `<a href="${p.foto_kk_url}" target="_blank"
          style="display:inline-flex;align-items:center;gap:3px;padding:2px 8px;border-radius:5px;
                 background:#dcfce7;color:#166534;font-size:11px;font-weight:600;text-decoration:none"
          title="Lihat foto KK">
          <i class="bi bi-people"></i> KK
       </a>`
    : `<span style="display:inline-flex;align-items:center;gap:3px;padding:2px 8px;border-radius:5px;
                    background:#f3f4f6;color:#9ca3af;font-size:11px">
          <i class="bi bi-people"></i> KK
       </span>`;
  return `<div style="display:flex;flex-direction:column;gap:4px">${ktp}${kk}</div>`;
}

async function loadData(){
  const q=document.getElementById('q').value;
  const s=document.getElementById('sel-status').value;
  const t=document.getElementById('tgl').value;
  const params=new URLSearchParams({page:currentPage,q,status:s,tanggal:t});
  document.getElementById('tbl-body').innerHTML='<tr><td colspan="8" style="text-align:center;padding:32px;color:var(--subtle)">Memuat…</td></tr>';
  try{
    const res=await fetch(`<?php echo e(route('admin.antrean.data')); ?>?${params}`,{headers:{'Accept':'application/json'}});
    const data=await res.json();
    lastPage=data.meta.last_page;
    document.getElementById('tbl-info').textContent=`${data.meta.total} permohonan`;
    document.getElementById('tbl-page').textContent=`${data.meta.current_page} / ${lastPage}`;
    document.getElementById('btn-prev').disabled=currentPage<=1;
    document.getElementById('btn-next').disabled=currentPage>=lastPage;

    if(!data.data.length){
      document.getElementById('tbl-body').innerHTML='<tr><td colspan="8" style="text-align:center;padding:40px;color:var(--subtle)">Tidak ada permohonan.</td></tr>';
      return;
    }

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
  }catch{
    document.getElementById('tbl-body').innerHTML='<tr><td colspan="8" style="text-align:center;padding:32px;color:#dc2626">Gagal memuat data.</td></tr>';
  }
}

loadData();
setInterval(loadData,30000);

const p=new URLSearchParams(location.search);
if(p.get('status'))document.getElementById('sel-status').value=p.get('status');
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\desa\resources\views/admin/antrean/index.blade.php ENDPATH**/ ?>