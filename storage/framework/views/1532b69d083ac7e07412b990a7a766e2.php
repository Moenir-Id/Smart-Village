<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
  <title><?php echo $__env->yieldContent('title','Layanan Surat'); ?> — <?php echo e(\App\Models\Setting::get('desa_nama','Desa')); ?></title>
  <?php
    $hijau    = \App\Models\Setting::get('warna_primer','#1a6b3a');
    $logoPath = \App\Models\Setting::get('desa_logo_path','');
    $logoUrl  = $logoPath ? asset('storage/' . $logoPath) : null;
  ?>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <style>
    :root{
      --p:<?php echo e($hijau); ?>;
      --p-dk:color-mix(in srgb,var(--p) 78%,#000);
      --p-lt:color-mix(in srgb,var(--p) 10%,#fff);
      --p-md:color-mix(in srgb,var(--p) 22%,#fff);
      --text:#111827;
      --subtle:#6b7280;
      --border:#e5e7eb;
      --bg:#f6f7fb;
      --surface:#fff;
      --r:12px;
      --r-sm:9px;
      --sh:0 2px 8px rgba(0,0,0,.05),0 8px 24px rgba(0,0,0,.07);
    }
    *,::before,::after{box-sizing:border-box;margin:0;padding:0}
    body{font-family:'DM Sans',sans-serif;background:var(--bg);color:var(--text);min-height:100vh;display:flex;flex-direction:column;font-size:15px;line-height:1.6}
    body.hero-page{background:linear-gradient(160deg,#0f1c4d 0%,#1a2e6b 35%,#2d3fa3 70%,#1e3a8a 100%);}
    body.hero-page footer{border-top-color:rgba(255,255,255,.12);color:rgba(255,255,255,.45)}
    body.hero-page footer a{color:rgba(255,255,255,.45)}
    body.hero-page footer a:hover{color:#fff}

    /* Header */
    .hdr{background:var(--p);color:#fff;position:sticky;top:0;z-index:100;box-shadow:0 2px 20px rgba(0,0,0,.2)}
    .hdr-in{max-width:640px;margin:0 auto;padding:14px 20px;display:flex;align-items:center;gap:12px}
    .hdr-logo{width:40px;height:40px;border-radius:10px;object-fit:contain;background:rgba(255,255,255,.12);border:1.5px solid rgba(255,255,255,.18)}
    .hdr-icon{width:40px;height:40px;border-radius:10px;background:rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0}
    .hdr-name{font-size:15px;font-weight:700;line-height:1.2;letter-spacing:-.01em}
    .hdr-sub{font-size:11px;opacity:.65;font-weight:400;margin-top:1px}
    .hdr-pill{margin-left:auto;background:rgba(255,255,255,.15);border:1.5px solid rgba(255,255,255,.22);border-radius:20px;padding:4px 13px;font-size:10.5px;font-weight:700;letter-spacing:.07em;text-transform:uppercase;flex-shrink:0}

    /* Main */
    main{flex:1;padding:28px 16px 48px}
    .pw{max-width:600px;margin:0 auto}

    /* Tabs */
    .tabs{display:flex;background:var(--surface);border-radius:11px;padding:4px;gap:3px;box-shadow:0 1px 3px rgba(0,0,0,.06);border:1px solid var(--border);margin-bottom:22px}
    .tab{flex:1;padding:10px 16px;border:none;background:transparent;border-radius:8px;font-family:inherit;font-size:13.5px;font-weight:600;color:var(--subtle);cursor:pointer;transition:all .18s;display:flex;align-items:center;justify-content:center;gap:7px}
    .tab.on{background:var(--p);color:#fff;box-shadow:0 2px 10px rgba(0,0,0,.14)}
    .tab:not(.on):hover{background:var(--p-lt);color:var(--p)}
    .tp{display:none;animation:fadeUp .22s ease}
    .tp.on{display:block}
    @keyframes fadeUp{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:translateY(0)}}

    /* Card */
    .card{background:var(--surface);border-radius:var(--r);box-shadow:var(--sh);border:1px solid var(--border);overflow:hidden}
    .cb{padding:24px}

    /* Form */
    .fg{margin-bottom:18px}
    .fl{display:block;font-size:12px;font-weight:700;color:var(--text);margin-bottom:6px;letter-spacing:.02em;text-transform:uppercase}
    .fl .opt{font-weight:400;color:var(--subtle);font-size:11px;text-transform:none;letter-spacing:0}
    .fl .req{color:#ef4444;margin-left:2px}
    .fc{width:100%;padding:11px 14px;border:1.5px solid var(--border);border-radius:var(--r-sm);font-family:inherit;font-size:14.5px;color:var(--text);background:#fff;outline:none;transition:border-color .15s,box-shadow .15s;appearance:none}
    .fc:focus{border-color:var(--p);box-shadow:0 0 0 3px color-mix(in srgb,var(--p) 13%,transparent)}
    .fc.is-invalid{border-color:#ef4444}
    .fc::placeholder{color:#c4c9d4}
    select.fc{background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%236b7280' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 13px center;padding-right:36px;cursor:pointer}
    textarea.fc{resize:vertical;min-height:90px}
    .fe{font-size:11.5px;color:#ef4444;margin-top:5px;display:none}
    .fe.show{display:block}

    /* File upload */
    .fu{border:1.5px dashed var(--border);border-radius:var(--r-sm);background:var(--bg);cursor:pointer;transition:all .15s;position:relative;overflow:hidden}
    .fu:hover{border-color:var(--p);background:var(--p-lt)}
    .fu input{position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%}
    .fu-lbl{display:flex;align-items:center;gap:11px;padding:13px 15px;pointer-events:none}
    .fu-lbl i{font-size:24px;color:var(--p)}
    .fu-name{font-size:13.5px;font-weight:600;color:var(--text)}
    .fu-hint{font-size:11.5px;color:var(--subtle)}

    /* PDP */
    .pdp{background:#fffbeb;border:1.5px solid #fde68a;border-radius:var(--r-sm);padding:14px;margin-bottom:18px}
    .pdp p{font-size:12.5px;color:#78350f;margin-bottom:10px;line-height:1.6}
    .pdp-ck{display:flex;align-items:flex-start;gap:10px;cursor:pointer}
    .pdp-ck input{width:18px;height:18px;margin-top:1px;accent-color:var(--p);cursor:pointer;flex-shrink:0}
    .pdp-ck span{font-size:13px;font-weight:700;color:#78350f}

    /* Buttons */
    .btn-p{width:100%;padding:14px;background:var(--p);color:#fff;border:none;border-radius:var(--r-sm);font-family:inherit;font-size:15px;font-weight:700;cursor:pointer;transition:opacity .15s,transform .1s;display:flex;align-items:center;justify-content:center;gap:8px}
    .btn-p:hover:not(:disabled){opacity:.9}
    .btn-p:active:not(:disabled){transform:scale(.99)}
    .btn-p:disabled{opacity:.45;cursor:not-allowed}
    .btn-ol{padding:10px 20px;background:transparent;color:var(--p);border:1.5px solid var(--p);border-radius:var(--r-sm);font-family:inherit;font-size:13.5px;font-weight:700;cursor:pointer;transition:background .15s;display:inline-flex;align-items:center;gap:7px}
    .btn-ol:hover{background:var(--p-lt)}
    .btn-cek{padding:11px 22px;background:var(--p);color:#fff;border:none;border-radius:var(--r-sm);font-family:inherit;font-size:14px;font-weight:700;cursor:pointer;white-space:nowrap;display:flex;align-items:center;gap:6px;transition:opacity .15s;flex-shrink:0}
    .btn-cek:hover{opacity:.88}

    /* Spinner */
    .spin{width:18px;height:18px;border:2.5px solid rgba(255,255,255,.3);border-top-color:#fff;border-radius:50%;animation:sp .7s linear infinite;flex-shrink:0}
    @keyframes sp{to{transform:rotate(360deg)}}

    /* Success */
    .suc{text-align:center;padding:36px 24px}
    .suc-ico{width:72px;height:72px;background:var(--p-lt);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 18px}
    .suc-ico i{font-size:34px;color:var(--p)}
    .suc-ttl{font-size:22px;font-weight:800;margin-bottom:6px;letter-spacing:-.02em}
    .suc-sub{font-size:13.5px;color:var(--subtle);margin-bottom:22px}
    .kode-box{display:inline-block;background:var(--p-lt);border:2px solid var(--p);border-radius:11px;padding:14px 32px;margin-bottom:22px}
    .kode-val{font-family:'DM Mono',monospace;font-size:26px;font-weight:700;color:var(--p);letter-spacing:.18em}
    .kode-hint{font-size:11px;color:var(--subtle);margin-top:5px}

    /* Track */
    .tr-wrap{display:flex;gap:9px;margin-bottom:14px}
    .tr-wrap .fc{font-family:'DM Mono',monospace;font-size:15.5px;letter-spacing:.08em;text-transform:uppercase}
    .res-head{background:var(--p-lt);border-bottom:1px solid var(--border);padding:18px 22px;display:flex;align-items:flex-start;justify-content:space-between;gap:12px}
    .res-kode{font-family:'DM Mono',monospace;font-size:11px;color:var(--subtle);margin-bottom:3px;letter-spacing:.06em}
    .res-name{font-size:18px;font-weight:800;margin-bottom:3px;letter-spacing:-.02em}
    .res-jenis{font-size:13px;color:var(--subtle)}
    .sbdg{padding:5px 14px;border-radius:20px;font-size:12px;font-weight:700;flex-shrink:0}
    .s-pending {background:#fef9c3;color:#854d0e}
    .s-diproses{background:#dbeafe;color:#1e40af}
    .s-selesai {background:#dcfce7;color:#166534}
    .s-ditolak {background:#fee2e2;color:#991b1b}

    /* Timeline */
    .tl-w{padding:20px 22px}
    .tl-ttl{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--subtle);margin-bottom:16px}
    .tl-i{display:flex;gap:12px;padding-bottom:18px;position:relative}
    .tl-i:last-child{padding-bottom:0}
    .tl-i:not(:last-child)::before{content:'';position:absolute;left:7px;top:18px;bottom:0;width:1.5px;background:var(--border)}
    .tl-dot{width:16px;height:16px;border-radius:50%;background:var(--border);border:2.5px solid #fff;box-shadow:0 0 0 2px var(--border);flex-shrink:0;margin-top:3px}
    .tl-dot.on{background:var(--p);box-shadow:0 0 0 3px color-mix(in srgb,var(--p) 18%,transparent)}
    .tl-st{font-size:14px;font-weight:700}
    .tl-time{font-size:12px;color:var(--subtle);margin-top:1px}
    .tl-note{font-size:12.5px;color:var(--subtle);margin-top:4px;font-style:italic}

    /* Note box */
    .note-box{margin:0 22px 16px;background:#fff7ed;border:1px solid #fed7aa;border-radius:9px;padding:13px;font-size:13px;color:#9a3412}
    .note-box strong{display:block;margin-bottom:4px;font-size:10px;text-transform:uppercase;letter-spacing:.07em;font-weight:700}

    /* Error / load */
    .al-err{background:#fef2f2;border:1px solid #fca5a5;color:#991b1b;border-radius:var(--r-sm);padding:12px 15px;font-size:13.5px;display:flex;align-items:center;gap:8px;margin-bottom:12px}
    .ld-w{display:flex;justify-content:center;padding:30px 0}
    .ld-sp{width:28px;height:28px;border:3px solid rgba(0,0,0,.08);border-top-color:var(--p);border-radius:50%;animation:sp .7s linear infinite}

    footer{text-align:center;padding:20px 16px;font-size:12px;color:var(--subtle);border-top:1px solid var(--border)}
    footer a{color:var(--subtle);text-decoration:none}
    footer a:hover{color:var(--p)}
    @media(max-width:480px){.cb,.tl-w{padding:18px 16px}}
  </style>
</head>
<body class="<?php echo $__env->yieldContent('body_class',''); ?>">
  <header class="hdr">
    <div class="hdr-in">
      <?php if($logoUrl): ?>
        <img src="<?php echo e($logoUrl); ?>" class="hdr-logo" alt="Logo">
      <?php else: ?>
        <div class="hdr-icon">🏛️</div>
      <?php endif; ?>
      <div>
        <div class="hdr-name"><?php echo e(\App\Models\Setting::get('desa_nama','Desa')); ?></div>
        <div class="hdr-sub">Layanan Surat Mandiri Digital</div>
      </div>
      <a href="<?php echo e(route('admin.login')); ?>" class="hdr-pill" style="text-decoration:none;color:inherit"><i class="bi bi-person" style="margin-right:5px"></i>Login Staff</a>
    </div>
  </header>
  <main>
    <div class="pw"><?php echo $__env->yieldContent('content'); ?></div>
  </main>
  <footer>
    <?php echo e(\App\Models\Setting::get('desa_nama','Desa')); ?> · Layanan Surat Mandiri Digital
    &nbsp;·&nbsp;<a href="<?php echo e(route('admin.login')); ?>">Login Staff</a>
  </footer>
  <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\laragon\www\desa\resources\views/layouts/warga.blade.php ENDPATH**/ ?>