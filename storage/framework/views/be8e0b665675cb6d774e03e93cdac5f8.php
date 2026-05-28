<?php $__env->startSection('title', 'Jenis Surat'); ?>
<?php $__env->startSection('content'); ?>

<style>
  .js-tbl-wrap{overflow-x:auto;-webkit-overflow-scrolling:touch}
  .js-cards{display:none;flex-direction:column;gap:0}
  .js-card{display:flex;align-items:center;gap:12px;padding:13px 16px;border-bottom:1px solid var(--border)}
  .js-card:last-child{border-bottom:none}
  @media(max-width:600px){
    .js-tbl-wrap{display:none}
    .js-cards{display:flex}
  }
</style>

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:10px">
  <h2 style="font-size:19px;font-weight:800;margin:0;letter-spacing:-.02em">Jenis Surat</h2>
  <a href="<?php echo e(route('admin.jenis-surat.create')); ?>" class="btn btn-p btn-sm"><i class="bi bi-plus-lg"></i> Tambah</a>
</div>

<div class="card">
  
  <div class="js-tbl-wrap">
    <table class="tbl">
      <thead>
        <tr><th>No</th><th>Nama</th><th>Template</th><th>Status</th><th>Aksi</th></tr>
      </thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $jenisSurat; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $js): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr>
          <td class="mono" style="color:var(--subtle)"><?php echo e($js->urutan); ?></td>
          <td style="font-weight:600"><?php echo e($js->nama); ?></td>
          <td>
            <?php if($js->hasTemplate()): ?>
              <span class="bdg" style="background:#dcfce7;color:#166534"><i class="bi bi-check-lg"></i> Ada</span>
            <?php else: ?>
              <span class="bdg bdg-gray">Belum</span>
            <?php endif; ?>
          </td>
          <td><span class="bdg <?php echo e($js->aktif ? 'bdg-selesai' : 'bdg-gray'); ?>"><?php echo e($js->aktif ? 'Aktif' : 'Nonaktif'); ?></span></td>
          <td>
            <div style="display:flex;gap:6px">
              <a href="<?php echo e(route('admin.jenis-surat.edit', $js)); ?>" class="btn btn-ol btn-sm">Edit</a>
              <form method="POST" action="<?php echo e(route('admin.jenis-surat.destroy', $js)); ?>" onsubmit="return confirm('Hapus jenis surat ini?')">
                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                <button class="btn btn-sm" style="background:#fef2f2;color:#dc2626;border:1.5px solid #fecaca">Hapus</button>
              </form>
            </div>
          </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td colspan="5" style="text-align:center;color:var(--subtle);padding:40px">Belum ada jenis surat.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  
  <div class="js-cards">
    <?php $__empty_1 = true; $__currentLoopData = $jenisSurat; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $js): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <div class="js-card">
      <div style="width:36px;height:36px;border-radius:9px;background:var(--p-lt);display:flex;align-items:center;justify-content:center;font-size:14px;color:var(--p);flex-shrink:0">
        <i class="bi bi-collection-fill"></i>
      </div>
      <div style="flex:1;min-width:0">
        <div style="font-weight:700;font-size:13.5px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?php echo e($js->nama); ?></div>
        <div style="display:flex;gap:6px;margin-top:4px;flex-wrap:wrap">
          <span class="bdg <?php echo e($js->aktif ? 'bdg-selesai' : 'bdg-gray'); ?>" style="font-size:10px"><?php echo e($js->aktif ? 'Aktif' : 'Nonaktif'); ?></span>
          <?php if($js->hasTemplate()): ?>
            <span class="bdg" style="background:#dcfce7;color:#166534;font-size:10px"><i class="bi bi-check-lg"></i> Template</span>
          <?php else: ?>
            <span class="bdg bdg-gray" style="font-size:10px">Belum ada template</span>
          <?php endif; ?>
        </div>
      </div>
      <div style="display:flex;flex-direction:column;gap:6px;flex-shrink:0">
        <a href="<?php echo e(route('admin.jenis-surat.edit', $js)); ?>" class="btn btn-ol btn-sm"><i class="bi bi-pencil"></i></a>
        <form method="POST" action="<?php echo e(route('admin.jenis-surat.destroy', $js)); ?>" onsubmit="return confirm('Hapus jenis surat ini?')">
          <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
          <button class="btn btn-sm" style="background:#fef2f2;color:#dc2626;border:1.5px solid #fecaca"><i class="bi bi-trash"></i></button>
        </form>
      </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div style="text-align:center;padding:40px;color:var(--subtle)">Belum ada jenis surat.</div>
    <?php endif; ?>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\desa\resources\views/admin/jenis-surat/index.blade.php ENDPATH**/ ?>