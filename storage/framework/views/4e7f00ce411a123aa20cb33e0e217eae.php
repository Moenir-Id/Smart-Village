<?php $__env->startSection('title', isset($user) ? 'Edit Pengguna' : 'Tambah Pengguna'); ?>
<?php $__env->startSection('content'); ?>

<div style="display:flex;align-items:center;gap:12px;margin-bottom:24px;flex-wrap:wrap">
  <a href="<?php echo e(route('admin.users.index')); ?>" class="btn btn-ol btn-sm"><i class="bi bi-arrow-left"></i> Kembali</a>
  <h2 style="font-size:20px;font-weight:800;margin:0;letter-spacing:-.02em">
    <?php echo e(isset($user) ? 'Edit' : 'Tambah'); ?> Pengguna
  </h2>
</div>

<div class="card" style="max-width:520px">
  <div class="ch"><i class="bi bi-person-fill"></i> <?php echo e(isset($user) ? 'Edit' : 'Data'); ?> Pengguna</div>
  <div class="cb">
    <form method="POST" action="<?php echo e(isset($user) ? route('admin.users.update', $user) : route('admin.users.store')); ?>">
      <?php echo csrf_field(); ?>
      <?php if(isset($user)): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>

      <div style="margin-bottom:18px">
        <label class="fl">Nama <span style="color:#ef4444">*</span></label>
        <input type="text" name="name" value="<?php echo e(old('name', $user->name ?? '')); ?>"
               class="fc <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="Nama lengkap" required>
        <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="fi"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>

      <div style="margin-bottom:18px">
        <label class="fl">Email <span style="color:#ef4444">*</span></label>
        <input type="email" name="email" value="<?php echo e(old('email', $user->email ?? '')); ?>"
               class="fc <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="nama@desa.id" required>
        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="fi"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>

      <div style="margin-bottom:18px">
        <label class="fl">
          Password
          <?php if(isset($user)): ?>
            <span style="color:var(--subtle);font-weight:400;text-transform:none;letter-spacing:0;font-size:11.5px">(kosongkan jika tidak diganti)</span>
          <?php else: ?>
            <span style="color:#ef4444">*</span>
          <?php endif; ?>
        </label>
        <input type="password" name="password"
               class="fc <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
               placeholder="••••••••"
               <?php echo e(isset($user) ? '' : 'required'); ?>>
        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="fi"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>

      <div style="margin-bottom:28px">
        <label class="fl">Role <span style="color:#ef4444">*</span></label>
        <select name="role" class="fc <?php $__errorArgs = ['role'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
          <option value="admin"   <?php echo e(old('role', $user->role ?? '') === 'admin'   ? 'selected' : ''); ?>>Admin</option>
          <option value="petugas" <?php echo e(old('role', $user->role ?? '') === 'petugas' ? 'selected' : ''); ?>>Petugas</option>
          <option value="viewer"  <?php echo e(old('role', $user->role ?? '') === 'viewer'  ? 'selected' : ''); ?>>Viewer</option>
        </select>
        <?php $__errorArgs = ['role'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="fi"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        <div style="margin-top:10px;display:grid;grid-template-columns:repeat(3,1fr);gap:8px">
          <?php $__currentLoopData = ['admin'=>['Akses penuh ke semua fitur','#dbeafe','#1e40af'],'petugas'=>['Kelola antrean surat','#dcfce7','#166534'],'viewer'=>['Hanya lihat data','#f3f4f6','#374151']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r=>[$desc,$bg,$clr]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <div style="padding:10px 12px;background:<?php echo e($bg); ?>;border-radius:8px">
            <div style="font-size:11px;font-weight:700;color:<?php echo e($clr); ?>;text-transform:uppercase;letter-spacing:.06em;margin-bottom:2px"><?php echo e(ucfirst($r)); ?></div>
            <div style="font-size:11px;color:<?php echo e($clr); ?>;opacity:.75"><?php echo e($desc); ?></div>
          </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      </div>

      <div style="display:flex;gap:8px">
        <button type="submit" class="btn btn-p">
          <i class="bi bi-check-lg"></i> Simpan
        </button>
        <a href="<?php echo e(route('admin.users.index')); ?>" class="btn btn-ol">Batal</a>
      </div>
    </form>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\desa\resources\views/admin/users/form.blade.php ENDPATH**/ ?>