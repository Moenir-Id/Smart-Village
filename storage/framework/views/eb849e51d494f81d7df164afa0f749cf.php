<?php $__env->startSection('title','Backup & Restore'); ?>
<?php $__env->startSection('content'); ?>

<div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;margin-bottom:24px">
  <div>
    <h2 style="font-size:20px;font-weight:800;margin:0;letter-spacing:-.02em">Backup &amp; Restore</h2>
    <p style="font-size:13px;color:var(--subtle);margin:3px 0 0">Kelola cadangan data sistem</p>
  </div>
  <form method="POST" action="<?php echo e(route('admin.backup.buat')); ?>">
    <?php echo csrf_field(); ?>
    <button type="submit" class="btn btn-p" onclick="return confirm('Buat backup sekarang? Proses ini mungkin membutuhkan beberapa detik.')">
      <i class="bi bi-download"></i> Buat Backup Sekarang
    </button>
  </form>
</div>

<div class="al al-info" style="margin-bottom:22px">
  <i class="bi bi-info-circle-fill"></i>
  <div>
    <strong>Yang dicadangkan:</strong> Seluruh database, file berkas (foto KTP/KK, logo desa), dan konfigurasi sistem.
    Backup tersimpan di <code style="background:rgba(255,255,255,.5);padding:1px 6px;border-radius:4px">storage/app/backups/</code> di server.
  </div>
</div>

<div style="display:grid;grid-template-columns:1fr 360px;gap:20px">

  
  <div class="card">
    <div class="ch"><i class="bi bi-archive-fill"></i> Riwayat Backup</div>
    <div style="padding:0">
      <?php $__empty_1 = true; $__currentLoopData = $files; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <div style="display:flex;align-items:center;gap:14px;padding:15px 22px;border-bottom:1px solid var(--border)">
        <div style="width:42px;height:42px;background:#eff6ff;border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
          <i class="bi bi-file-zip-fill" style="font-size:20px;color:#2563eb"></i>
        </div>
        <div style="flex:1;min-width:0">
          <div class="mono" style="font-size:13px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?php echo e($f['name']); ?></div>
          <div style="font-size:12px;color:var(--subtle);margin-top:2px"><?php echo e($f['tanggal']); ?> · <?php echo e($f['size']); ?></div>
        </div>
        <div style="display:flex;gap:6px;flex-shrink:0">
          <a href="<?php echo e(route('admin.backup.unduh', $f['name'])); ?>" class="btn btn-ol btn-sm">
            <i class="bi bi-cloud-download"></i> Unduh
          </a>
          <form method="POST" action="<?php echo e(route('admin.backup.hapus', $f['name'])); ?>" onsubmit="return confirm('Hapus backup ini?')">
            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
            <button type="submit" class="btn btn-sm" style="background:#fef2f2;color:#dc2626;border:1.5px solid #fecaca">
              <i class="bi bi-trash"></i>
            </button>
          </form>
        </div>
      </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <div style="text-align:center;padding:48px;color:var(--subtle)">
        <i class="bi bi-archive" style="font-size:36px;display:block;margin-bottom:10px;opacity:.25"></i>
        <div style="font-size:13.5px;font-weight:600;margin-bottom:4px">Belum ada backup</div>
        <div style="font-size:13px">Klik "Buat Backup Sekarang" untuk memulai.</div>
      </div>
      <?php endif; ?>
    </div>
  </div>

  
  <div style="display:flex;flex-direction:column;gap:20px">
    <div class="card" style="border-left:3px solid #f59e0b">
      <div class="ch"><i class="bi bi-arrow-counterclockwise" style="color:#f59e0b"></i> Restore dari File</div>
      <div class="cb">
        <div class="al al-warn" style="margin-bottom:18px">
          <i class="bi bi-exclamation-triangle-fill"></i>
          <div><strong>Peringatan!</strong> Restore akan <strong>menimpa semua data</strong> yang ada. Tindakan ini tidak dapat dibatalkan.</div>
        </div>

        <form method="POST" action="<?php echo e(route('admin.backup.restore')); ?>" enctype="multipart/form-data" id="restore-form">
          <?php echo csrf_field(); ?>
          <div style="margin-bottom:16px">
            <label class="fl">File Backup (.zip)</label>
            <input type="file" name="file" accept=".zip" class="fc" required id="restore-file" onchange="updateFileInfo()">
            <div id="file-info" style="font-size:12px;color:var(--subtle);margin-top:5px"></div>
          </div>

          <div style="margin-bottom:18px;padding:14px;background:#fef9c3;border-radius:9px;border:1px solid #fde047">
            <label class="fl" style="color:#854d0e">Ketik RESTORE untuk konfirmasi</label>
            <input type="text" name="konfirmasi" class="fc" placeholder="RESTORE" id="restore-confirm"
              oninput="checkConfirm()" autocomplete="off"
              style="font-family:'DM Mono',monospace;font-weight:700;letter-spacing:.1em;text-transform:uppercase">
            <?php $__errorArgs = ['konfirmasi'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="fi"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
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
      <div class="cb">
        <div style="display:flex;flex-direction:column;gap:10px">
          <?php $__currentLoopData = [
            'Buat backup sebelum update atau perubahan besar',
            'Simpan backup di tempat lain (Google Drive, PC lokal)',
            'Backup otomatis belum tersedia — lakukan secara berkala',
            'File backup bisa besar jika ada banyak foto KTP/KK',
            'Restore hanya bisa dilakukan oleh Admin',
          ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tip): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <div style="display:flex;gap:9px;font-size:12.5px;color:var(--subtle)">
            <span style="color:var(--p);flex-shrink:0;font-weight:700">·</span>
            <?php echo e($tip); ?>

          </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      </div>
    </div>
  </div>

</div>

<?php $__env->stopSection(); ?>
<?php $__env->startPush('scripts'); ?>
<script>
function updateFileInfo(){
  const f=document.getElementById('restore-file').files[0];
  if(f){
    const mb=(f.size/1048576).toFixed(1);
    document.getElementById('file-info').textContent=`${f.name} (${mb} MB)`;
  }
  checkConfirm();
}
function checkConfirm(){
  const val=document.getElementById('restore-confirm').value;
  const btn=document.getElementById('restore-btn');
  const ok=val==='RESTORE'&&document.getElementById('restore-file').files.length>0;
  btn.disabled=!ok;
  btn.style.opacity=ok?'1':'0.45';
  btn.style.cursor=ok?'pointer':'not-allowed';
}
document.getElementById('restore-file').addEventListener('change',checkConfirm);
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\desa\resources\views/admin/backup/index.blade.php ENDPATH**/ ?>