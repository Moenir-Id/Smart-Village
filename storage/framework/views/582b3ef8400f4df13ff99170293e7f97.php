<?php $__env->startSection('title', 'Template Surat'); ?>
<?php $__env->startSection('content'); ?>

<style>
  .tmpl-tbl-wrap{overflow-x:auto;-webkit-overflow-scrolling:touch}
  .tmpl-cards{display:none;flex-direction:column;gap:0}
  .tmpl-card{padding:16px;border-bottom:1px solid var(--border)}
  .tmpl-card:last-child{border-bottom:none}
  @media(max-width:640px){
    .tmpl-tbl-wrap{display:none}
    .tmpl-cards{display:flex}
  }
</style>

<div style="margin-bottom:6px">
  <h2 style="font-size:19px;font-weight:800;margin:0;letter-spacing:-.02em">Template Surat</h2>
  <p style="font-size:12.5px;color:var(--subtle);margin-top:4px">
    Upload file .docx per jenis surat. Gunakan variabel seperti
    <code style="background:var(--bg);padding:1px 6px;border-radius:4px;font-size:11.5px">&#123;&#123;nama&#125;&#125;</code>,
    <code style="background:var(--bg);padding:1px 6px;border-radius:4px;font-size:11.5px">&#123;&#123;nik&#125;&#125;</code>, dll.
  </p>
</div>
<div style="margin-bottom:20px"></div>

<div class="card">
  
  <div class="tmpl-tbl-wrap">
    <table class="tbl">
      <thead>
        <tr><th>Jenis Surat</th><th>Variabel Tersedia</th><th>Status</th><th>Aksi</th></tr>
      </thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $jenisSurat; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $js): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <?php $vars = is_array($js->variabel_tersedia) ? $js->variabel_tersedia : [] ?>
        <tr>
          <td style="font-weight:600"><?php echo e($js->nama); ?></td>
          <td>
            <?php if(count($vars) > 0): ?>
              <div style="display:flex;flex-wrap:wrap;gap:4px">
                <?php $__currentLoopData = $vars; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <code style="background:var(--bg);padding:2px 7px;border-radius:5px;font-size:11px;color:var(--p);border:1px solid var(--border)">&#123;&#123;<?php echo e($v); ?>&#125;&#125;</code>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </div>
            <?php else: ?>
              <span style="color:var(--subtle)">—</span>
            <?php endif; ?>
          </td>
          <td>
            <?php if($js->hasTemplate()): ?>
              <span class="bdg bdg-selesai"><i class="bi bi-check-lg"></i> Terpasang</span>
            <?php else: ?>
              <span class="bdg bdg-pending">Belum ada</span>
            <?php endif; ?>
          </td>
          <td>
            <div style="display:flex;flex-direction:column;gap:7px">
              <form method="POST" action="<?php echo e(route('admin.template.upload', $js)); ?>" enctype="multipart/form-data" style="display:flex;gap:7px;align-items:center">
                <?php echo csrf_field(); ?>
                <input type="file" name="template" accept=".docx" class="fc" style="max-width:180px;padding:6px 10px;font-size:12px" required>
                <button class="btn btn-p btn-sm"><i class="bi bi-upload"></i> Upload</button>
              </form>
              <?php if($js->hasTemplate()): ?>
              <form method="POST" action="<?php echo e(route('admin.template.hapus', $js)); ?>" onsubmit="return confirm('Hapus template?')">
                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                <button class="btn btn-sm" style="background:#fef2f2;color:#dc2626;border:1.5px solid #fecaca">Hapus Template</button>
              </form>
              <?php endif; ?>
            </div>
          </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td colspan="4" style="text-align:center;color:var(--subtle);padding:40px">Belum ada jenis surat.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  
  <div class="tmpl-cards">
    <?php $__empty_1 = true; $__currentLoopData = $jenisSurat; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $js): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <?php $vars = is_array($js->variabel_tersedia) ? $js->variabel_tersedia : [] ?>
    <div class="tmpl-card">
      <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px">
        <div style="flex:1;min-width:0">
          <div style="font-weight:700;font-size:13.5px"><?php echo e($js->nama); ?></div>
          <div style="margin-top:4px">
            <?php if($js->hasTemplate()): ?>
              <span class="bdg bdg-selesai" style="font-size:10.5px"><i class="bi bi-check-lg"></i> Terpasang</span>
            <?php else: ?>
              <span class="bdg bdg-pending" style="font-size:10.5px">Belum ada template</span>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <?php if(count($vars) > 0): ?>
      <div style="display:flex;flex-wrap:wrap;gap:4px;margin-bottom:10px">
        <?php $__currentLoopData = $vars; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <code style="background:var(--bg);padding:2px 6px;border-radius:4px;font-size:10.5px;color:var(--p);border:1px solid var(--border)">&#123;&#123;<?php echo e($v); ?>&#125;&#125;</code>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
      <?php endif; ?>
      <form method="POST" action="<?php echo e(route('admin.template.upload', $js)); ?>" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
          <input type="file" name="template" accept=".docx" class="fc" style="flex:1;min-width:0;padding:7px 10px;font-size:12px" required>
          <button class="btn btn-p btn-sm" style="flex-shrink:0"><i class="bi bi-upload"></i> Upload</button>
        </div>
      </form>
      <?php if($js->hasTemplate()): ?>
      <form method="POST" action="<?php echo e(route('admin.template.hapus', $js)); ?>" onsubmit="return confirm('Hapus template?')" style="margin-top:8px">
        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
        <button class="btn btn-sm" style="background:#fef2f2;color:#dc2626;border:1.5px solid #fecaca">Hapus Template</button>
      </form>
      <?php endif; ?>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div style="text-align:center;padding:40px;color:var(--subtle)">Belum ada jenis surat.</div>
    <?php endif; ?>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\desa\resources\views/admin/template/index.blade.php ENDPATH**/ ?>