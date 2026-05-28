<?php $__env->startSection('title','Detail '.$permohonan->kode_unik); ?>
<?php $__env->startSection('content'); ?>

<div style="display:flex;align-items:center;gap:12px;margin-bottom:24px;flex-wrap:wrap">
  <a href="<?php echo e(route('admin.antrean.index')); ?>" class="btn btn-ol btn-sm"><i class="bi bi-arrow-left"></i> Kembali</a>
  <h2 class="mono" style="font-size:16px;font-weight:700;margin:0;color:var(--p)"><?php echo e($permohonan->kode_unik); ?></h2>
  <span class="bdg bdg-<?php echo e($permohonan->status); ?>" style="font-size:12.5px"><?php echo e($permohonan->statusLabel()); ?></span>
  <?php if($permohonan->isSlaOverdue()): ?>
    <span class="bdg" style="background:#fee2e2;color:#991b1b">⚠ Overdue SLA</span>
  <?php endif; ?>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">

  
  <div class="card">
    <div class="ch"><i class="bi bi-person-badge-fill"></i> Data Pemohon</div>
    <div class="cb">
      <table style="width:100%;border-collapse:collapse;font-size:13px">
        <?php $__currentLoopData = [
          ['Nama Lengkap', $permohonan->nama_lengkap],
          ['NIK',          $permohonan->nik],
          ['WhatsApp',     $permohonan->nomor_wa],
          ['Jenis Surat',  $permohonan->jenisSurat->nama],
          ['Keperluan',    $permohonan->keperluan],
          ['Dikirim',      $permohonan->created_at->format('d/m/Y H:i')],
          ['SLA Deadline', $permohonan->sla_deadline?->format('d/m/Y H:i') ?? '-'],
        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$k,$v]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
          <td style="color:var(--subtle);padding:8px 0;width:115px;vertical-align:top;font-size:11.5px;font-weight:600;text-transform:uppercase;letter-spacing:.04em"><?php echo e($k); ?></td>
          <td style="padding:8px 0 8px 14px;font-weight:500;border-bottom:1px solid var(--border)"><?php echo e($v); ?></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </table>
    </div>
  </div>

  
  <div class="card">
    <div class="ch"><i class="bi bi-images"></i> Berkas Foto</div>
    <div class="cb">
      <?php if($permohonan->fotoSudahDihapus()): ?>
        <div class="al al-warn"><i class="bi bi-trash"></i> Foto telah dihapus otomatis (<?php echo e($permohonan->foto_purged_at?->format('d/m/Y')); ?>).</div>
      <?php else: ?>
        <?php if($fotoKtp): ?>
          <p style="font-size:10.5px;font-weight:700;color:var(--subtle);margin-bottom:7px;text-transform:uppercase;letter-spacing:.07em">Foto KTP</p>
          <a href="<?php echo e($fotoKtp); ?>" target="_blank">
            <img src="<?php echo e($fotoKtp); ?>" style="max-height:160px;width:100%;object-fit:contain;border-radius:9px;border:1px solid var(--border);margin-bottom:14px;cursor:zoom-in;transition:opacity .15s" onmouseover="this.style.opacity=.85" onmouseout="this.style.opacity=1">
          </a>
        <?php endif; ?>
        <?php if($fotoKk): ?>
          <p style="font-size:10.5px;font-weight:700;color:var(--subtle);margin-bottom:7px;text-transform:uppercase;letter-spacing:.07em">Foto KK</p>
          <a href="<?php echo e($fotoKk); ?>" target="_blank">
            <img src="<?php echo e($fotoKk); ?>" style="max-height:160px;width:100%;object-fit:contain;border-radius:9px;border:1px solid var(--border);cursor:zoom-in;transition:opacity .15s" onmouseover="this.style.opacity=.85" onmouseout="this.style.opacity=1">
          </a>
        <?php endif; ?>
        <?php if(!$fotoKtp && !$fotoKk): ?>
          <p style="color:var(--subtle);font-size:13px">Tidak ada foto yang diunggah.</p>
        <?php endif; ?>
      <?php endif; ?>
    </div>
  </div>

  
  <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $permohonan)): ?>
  <div class="card" style="grid-column:span 2">
    <div class="ch"><i class="bi bi-pencil-square"></i> Ubah Status</div>
    <div class="cb">
      <div style="display:flex;gap:14px;align-items:flex-end;flex-wrap:wrap">
        <div>
          <label class="fl">Status Baru</label>
          <select id="sel-status" class="fc" style="width:auto" onchange="checkCatatan()">
            <option value="diproses" <?php echo e($permohonan->status==='diproses'?'selected':''); ?>>Sedang Diproses</option>
            <option value="selesai"  <?php echo e($permohonan->status==='selesai' ?'selected':''); ?>>Selesai</option>
            <option value="ditolak"  <?php echo e($permohonan->status==='ditolak' ?'selected':''); ?>>Ditolak</option>
          </select>
        </div>
        <div style="flex:1;min-width:200px">
          <label class="fl">
            Catatan Petugas
            <span id="catatan-required" style="color:#ef4444;display:none"> *</span>
          </label>
          <input type="text" id="inp-catatan" class="fc" placeholder="Catatan petugas…" value="<?php echo e($permohonan->catatan_petugas); ?>">
          <p id="catatan-hint" style="font-size:11.5px;color:#ef4444;margin-top:4px;display:none">Wajib diisi saat menolak</p>
        </div>
        <div style="display:flex;gap:8px">
          <button class="btn btn-p" onclick="ubahStatus()"><i class="bi bi-check-lg"></i> Simpan</button>
          <button class="btn btn-ol" onclick="eksporWord()"><i class="bi bi-file-earmark-word"></i> Ekspor Word</button>
        </div>
      </div>
      <div id="status-msg" style="display:none;margin-top:14px;padding:11px 15px;border-radius:9px;font-size:13px"></div>
    </div>
  </div>
  <?php endif; ?>

  
  <div class="card" style="grid-column:span 2">
    <div class="ch"><i class="bi bi-clock-history"></i> Riwayat Status</div>
    <div class="cb">
      <?php $__currentLoopData = $permohonan->timeline; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <div class="tl-item">
        <div class="tl-dot <?php echo e($loop->last ? 'on' : ''); ?>"></div>
        <div>
          <div style="font-size:13.5px;font-weight:700"><?php echo e($t->status); ?></div>
          <?php if($t->catatan): ?><div style="font-size:12.5px;color:var(--subtle);margin-top:2px"><?php echo e($t->catatan); ?></div><?php endif; ?>
          <div style="font-size:11.5px;color:var(--subtle);margin-top:3px">
            <?php echo e($t->created_at->format('d/m/Y H:i')); ?>

            <?php if($t->user): ?> · <strong><?php echo e($t->user->name); ?></strong><?php endif; ?>
          </div>
        </div>
      </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
  </div>

</div>

<?php $__env->stopSection(); ?>
<?php $__env->startPush('scripts'); ?>
<script>
function checkCatatan(){
  const isDitolak = document.getElementById('sel-status').value === 'ditolak';
  document.getElementById('catatan-required').style.display = isDitolak ? 'inline' : 'none';
  document.getElementById('catatan-hint').style.display = isDitolak ? 'block' : 'none';
}
checkCatatan();

async function ubahStatus(){
  const status  = document.getElementById('sel-status').value;
  const catatan = document.getElementById('inp-catatan').value;
  const msg     = document.getElementById('status-msg');

  if(status==='ditolak' && !catatan.trim()){
    document.getElementById('inp-catatan').classList.add('is-invalid');
    document.getElementById('catatan-hint').style.display='block';
    return;
  }

  const res  = await fetch('<?php echo e(route("admin.antrean.ubah-status",$permohonan)); ?>', {
    method:'PATCH',
    headers:{'X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content,'Content-Type':'application/json','Accept':'application/json'},
    body:JSON.stringify({status,catatan}),
  });
  const data = await res.json();

  msg.style.display = 'block';
  if(res.ok){
    msg.style.cssText = 'display:block;margin-top:14px;padding:11px 15px;border-radius:9px;font-size:13px;background:#f0fdf4;border:1px solid #bbf7d0;color:#166534';
    msg.innerHTML = '✓ Status berhasil diubah.' + (data.wa_terkirim ? ' <span style="opacity:.7">WA terkirim.</span>' : '');
    setTimeout(()=>location.reload(), 1000);
  } else {
    msg.style.cssText = 'display:block;margin-top:14px;padding:11px 15px;border-radius:9px;font-size:13px;background:#fef2f2;border:1px solid #fecaca;color:#991b1b';
    msg.textContent = data.message || 'Gagal mengubah status.';
  }
}

async function eksporWord(){
  const res  = await fetch('<?php echo e(route("admin.ekspor.ekspor",$permohonan)); ?>', {
    method:'POST', headers:{'X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content}
  });
  const json = await res.json();
  alert(json.message ?? 'Ekspor dijadwalkan.');
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\desa\resources\views/admin/antrean/show.blade.php ENDPATH**/ ?>