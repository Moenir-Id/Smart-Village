<?php $__env->startSection('title','Dashboard'); ?>
<?php $__env->startSection('content'); ?>

<style>
  .dash-grid{display:grid;grid-template-columns:1fr 300px;gap:20px}
  @media(max-width:900px){
    .dash-grid{grid-template-columns:1fr}
    .sg{grid-template-columns:repeat(3,1fr)!important}
  }
  @media(max-width:480px){
    .sg{grid-template-columns:repeat(2,1fr)!important}
  }
</style>

<div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:20px;flex-wrap:wrap;gap:8px">
  <div>
    <h2 style="font-size:19px;font-weight:800;margin:0;letter-spacing:-.02em">Dashboard</h2>
    <p style="font-size:12.5px;color:var(--subtle);margin:3px 0 0"><?php echo e(now()->translatedFormat('l, d F Y')); ?></p>
  </div>
</div>

<div class="sg">
  <div class="sc cp"><div class="sn"><?php echo e($stats['total']); ?></div><div class="sl">Total</div></div>
  <div class="sc cy"><div class="sn"><?php echo e($stats['pending']); ?></div><div class="sl">Menunggu</div></div>
  <div class="sc cb2"><div class="sn"><?php echo e($stats['diproses']); ?></div><div class="sl">Diproses</div></div>
  <div class="sc cg"><div class="sn"><?php echo e($stats['selesai']); ?></div><div class="sl">Selesai</div></div>
  <div class="sc cr"><div class="sn"><?php echo e($stats['ditolak']); ?></div><div class="sl">Ditolak</div></div>
  <div class="sc" style="border-left:3px solid #dc2626">
    <div class="sn" style="color:#dc2626"><?php echo e($stats['overdue']); ?></div>
    <div class="sl">Overdue SLA</div>
  </div>
</div>

<div class="dash-grid">

  <div class="card">
    <div class="ch"><i class="bi bi-clock-history"></i> Permohonan Terbaru</div>

    
    <div class="mob-list" style="padding:10px 12px;display:flex;flex-direction:column;gap:8px">
      <?php $__empty_1 = true; $__currentLoopData = $terbaru; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <a href="<?php echo e(route('admin.antrean.show',$p)); ?>"
         style="display:flex;align-items:center;gap:12px;padding:11px 14px;border-radius:9px;border:1px solid var(--border);text-decoration:none;background:#fff;transition:box-shadow .14s"
         onmouseover="this.style.boxShadow='var(--sh)'" onmouseout="this.style.boxShadow='none'">
        <div style="flex:1;min-width:0">
          <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:3px">
            <span class="mono" style="font-weight:700;color:var(--p);font-size:12px"><?php echo e($p->kode_unik); ?></span>
            <span class="bdg bdg-<?php echo e($p->status); ?>" style="font-size:10.5px"><?php echo e($p->statusLabel()); ?></span>
          </div>
          <div style="font-weight:600;font-size:13px;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?php echo e($p->nama_lengkap); ?></div>
          <div style="font-size:11.5px;color:var(--subtle)"><?php echo e($p->jenisSurat->nama); ?></div>
        </div>
        <i class="bi bi-chevron-right" style="color:var(--border);font-size:13px;flex-shrink:0"></i>
      </a>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <p style="text-align:center;color:var(--subtle);padding:32px 0;font-size:13px">Belum ada permohonan.</p>
      <?php endif; ?>
    </div>

    <div style="padding:12px 16px;border-top:1px solid var(--border);text-align:right">
      <a href="<?php echo e(route('admin.antrean.index')); ?>" class="btn btn-ol btn-sm">Lihat Semua <i class="bi bi-arrow-right"></i></a>
    </div>
  </div>

  <div style="display:flex;flex-direction:column;gap:20px">
    <div class="card">
      <div class="ch"><i class="bi bi-bar-chart"></i> Tren 7 Hari</div>
      <div class="cb">
        <?php $max = $tren->max('total') ?: 1 ?>
        <?php $__empty_1 = true; $__currentLoopData = $tren; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:9px">
          <span style="font-size:11px;color:var(--subtle);width:28px;font-family:'DM Mono',monospace"><?php echo e(\Carbon\Carbon::parse($t->tanggal)->format('d/m')); ?></span>
          <div style="flex:1;height:7px;background:#f0f1f5;border-radius:4px;overflow:hidden">
            <div style="height:100%;background:var(--p);width:<?php echo e(round($t->total/$max*100)); ?>%;border-radius:4px"></div>
          </div>
          <span style="font-size:12px;font-weight:700;width:18px;text-align:right;color:var(--text)"><?php echo e($t->total); ?></span>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <p style="text-align:center;color:var(--subtle);font-size:13px;padding:16px 0">Belum ada data</p>
        <?php endif; ?>
      </div>
    </div>

    <?php if($stats['overdue'] > 0): ?>
    <div class="card" style="border-left:3px solid #dc2626">
      <div class="cb" style="padding:16px">
        <div style="display:flex;align-items:center;gap:12px">
          <div style="width:38px;height:38px;border-radius:9px;background:#fee2e2;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i class="bi bi-exclamation-triangle-fill" style="color:#dc2626;font-size:17px"></i>
          </div>
          <div>
            <div style="font-size:14px;font-weight:700;color:#dc2626"><?php echo e($stats['overdue']); ?> Overdue SLA</div>
            <div style="font-size:12px;color:var(--subtle)">Melebihi batas waktu</div>
          </div>
        </div>
        <a href="<?php echo e(route('admin.antrean.index')); ?>?status=pending" class="btn btn-sm"
           style="margin-top:12px;width:100%;background:#fef2f2;color:#dc2626;border:1.5px solid #fecaca;justify-content:center">
          Lihat Sekarang
        </a>
      </div>
    </div>
    <?php endif; ?>
  </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\desa\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>