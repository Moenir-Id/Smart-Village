<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login — <?php echo e(\App\Models\Setting::get('desa_nama','Desa')); ?></title>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <?php
    $hijau    = \App\Models\Setting::get('warna_primer','#1a6b3a');
    $logoPath = \App\Models\Setting::get('desa_logo_path','');
    $logoUrl  = $logoPath ? \Illuminate\Support\Facades\Storage::disk('public')->url($logoPath) : null;
  ?>
  <style>
    *{box-sizing:border-box;margin:0;padding:0}
    body{font-family:'DM Sans',sans-serif;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px;position:relative;background:#f6f7fb;overflow:hidden}
    body::before{content:'';position:fixed;inset:0;background:linear-gradient(135deg,color-mix(in srgb,<?php echo e($hijau); ?> 18%,#f6f7fb) 0%,#f6f7fb 55%,color-mix(in srgb,<?php echo e($hijau); ?> 7%,#f6f7fb) 100%);z-index:0}
    body::after{content:'';position:fixed;top:-30%;right:-10%;width:500px;height:500px;border-radius:50%;background:radial-gradient(circle,color-mix(in srgb,<?php echo e($hijau); ?> 12%,transparent),transparent 70%);z-index:0}
    .box{width:100%;max-width:400px;position:relative;z-index:1}
    .logo-wrap{text-align:center;margin-bottom:30px}
    .logo-img{width:68px;height:68px;object-fit:contain;border-radius:16px;border:2px solid rgba(0,0,0,.06);box-shadow:0 4px 16px rgba(0,0,0,.1)}
    .logo-icon{width:68px;height:68px;background:<?php echo e($hijau); ?>;border-radius:16px;display:inline-flex;align-items:center;justify-content:center;font-size:30px;box-shadow:0 4px 16px color-mix(in srgb,<?php echo e($hijau); ?> 40%,transparent)}
    .desa-name{font-size:20px;font-weight:800;color:#111827;margin-top:12px;letter-spacing:-.02em}
    .desa-sub{font-size:13px;color:#6b7280;margin-top:4px}
    .card{background:#fff;border-radius:16px;box-shadow:0 4px 6px rgba(0,0,0,.04),0 16px 32px rgba(0,0,0,.08);padding:32px;border:1px solid #eaecf2}
    .fg{margin-bottom:18px}
    label{display:block;font-size:11.5px;font-weight:700;color:#374151;margin-bottom:7px;text-transform:uppercase;letter-spacing:.06em}
    input[type=email],input[type=password]{width:100%;padding:11px 14px;border:1.5px solid #e5e7eb;border-radius:9px;font-family:inherit;font-size:14.5px;outline:none;transition:border-color .15s,box-shadow .15s;color:#111827}
    input[type=email]:focus,input[type=password]:focus{border-color:<?php echo e($hijau); ?>;box-shadow:0 0 0 3px color-mix(in srgb,<?php echo e($hijau); ?> 14%,transparent)}
    .btn{width:100%;padding:13px;background:<?php echo e($hijau); ?>;color:#fff;border:none;border-radius:9px;font-family:inherit;font-size:14.5px;font-weight:700;cursor:pointer;transition:opacity .15s,transform .1s;margin-top:4px}
    .btn:hover{opacity:.9}
    .btn:active{transform:scale(.99)}
    .remember{display:flex;align-items:center;gap:9px;font-size:13px;color:#6b7280;margin-bottom:22px;cursor:pointer}
    .remember input{width:16px;height:16px;accent-color:<?php echo e($hijau); ?>;cursor:pointer}
    .al{padding:12px 14px;border-radius:9px;font-size:13px;margin-bottom:18px;display:flex;align-items:center;gap:9px;border:1px solid}
    .al-err{background:#fef2f2;border-color:#fecaca;color:#991b1b}
    .al-warn{background:#fffbeb;border-color:#fde68a;color:#92400e}
    .back{text-align:center;margin-top:22px;font-size:13px;color:#9ca3af}
    .back a{color:#6b7280;text-decoration:none;font-weight:500}
    .back a:hover{color:<?php echo e($hijau); ?>}
  </style>
</head>
<body>
  <div class="box">
    <div class="logo-wrap">
      <?php if($logoUrl): ?>
        <img src="<?php echo e($logoUrl); ?>" class="logo-img" alt="Logo">
      <?php else: ?>
        <div class="logo-icon">🏛️</div>
      <?php endif; ?>
      <div class="desa-name"><?php echo e(\App\Models\Setting::get('desa_nama','Desa')); ?></div>
      <div class="desa-sub">Panel Administrasi Staff</div>
    </div>

    <div class="card">
      <?php if(session('timeout')): ?>
        <div class="al al-warn"><i class="bi bi-clock"></i> Sesi berakhir. Silakan login kembali.</div>
      <?php endif; ?>
      <?php if($errors->any()): ?>
        <div class="al al-err"><i class="bi bi-exclamation-circle"></i> <?php echo e($errors->first()); ?></div>
      <?php endif; ?>

      <form method="POST" action="<?php echo e(route('admin.login.post')); ?>">
        <?php echo csrf_field(); ?>
        <div class="fg">
          <label>Email</label>
          <input type="email" name="email" value="<?php echo e(old('email')); ?>" placeholder="admin@desa.id" required autofocus>
        </div>
        <div class="fg">
          <label>Password</label>
          <input type="password" name="password" placeholder="••••••••" required>
        </div>
        <label class="remember">
          <input type="checkbox" name="remember"> Ingat saya
        </label>
        <button type="submit" class="btn">Masuk ke Panel Admin</button>
      </form>
    </div>

    <div class="back"><a href="<?php echo e(route('home')); ?>">← Kembali ke Layanan Warga</a></div>
  </div>
</body>
</html>
<?php /**PATH C:\laragon\www\desa\resources\views/auth/login.blade.php ENDPATH**/ ?>