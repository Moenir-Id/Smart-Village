<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
  <title><?php echo $__env->yieldContent('title','Panel'); ?> — <?php echo e(\App\Models\Setting::get('desa_nama','Desa')); ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <?php
    $hijau   = \App\Models\Setting::get('warna_primer','#1a6b3a');
    $logoPath= \App\Models\Setting::get('desa_logo_path','');
    $logoUrl = $logoPath ? asset('storage/'.$logoPath) : null;
  ?>
  <style>
    :root{
      --p:<?php echo e($hijau); ?>;
      --p-dk:color-mix(in srgb,var(--p) 75%,#000);
      --p-lt:color-mix(in srgb,var(--p) 10%,#fff);
      --p-md:color-mix(in srgb,var(--p) 20%,#fff);
      --sw:240px;
      --th:56px;
      --mn:60px;       /* mobile navbar height */
      --bg:#f6f7fb;
      --surface:#ffffff;
      --border:#eaecf2;
      --text:#111827;
      --subtle:#6b7280;
      --r:10px;
      --sh:0 1px 2px rgba(0,0,0,.04),0 4px 12px rgba(0,0,0,.06);
      --sh-sm:0 1px 2px rgba(0,0,0,.06);
    }
    *,::before,::after{box-sizing:border-box;margin:0;padding:0}
    body{font-family:'DM Sans',sans-serif;background:var(--bg);color:var(--text);font-size:14px;line-height:1.55;display:flex;min-height:100vh}

    /* ─── SIDEBAR (desktop) ─── */
    .sb{width:var(--sw);background:var(--text);display:flex;flex-direction:column;flex-shrink:0;position:fixed;top:0;left:0;height:100vh;z-index:200;transition:transform .25s cubic-bezier(.4,0,.2,1)}
    .sb-top{padding:20px 16px 16px;display:flex;align-items:center;gap:11px;border-bottom:1px solid rgba(255,255,255,.07)}
    .sb-logo{width:36px;height:36px;border-radius:9px;object-fit:contain;background:rgba(255,255,255,.1);flex-shrink:0}
    .sb-logobox{width:36px;height:36px;border-radius:9px;background:var(--p);display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:17px}
    .sb-name{color:#fff;font-size:13px;font-weight:700;line-height:1.25;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:155px}
    .sb-sub{color:rgba(255,255,255,.35);font-size:10.5px;margin-top:1px;font-weight:400}
    .sb-user{padding:10px 16px;border-bottom:1px solid rgba(255,255,255,.07);display:flex;align-items:center;gap:9px}
    .sb-av{width:30px;height:30px;border-radius:50%;background:var(--p);display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:#fff;flex-shrink:0}
    .sb-uname{font-size:12.5px;color:rgba(255,255,255,.85);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:135px;font-weight:500}
    .sb-urole{font-size:10px;color:rgba(255,255,255,.3);text-transform:uppercase;letter-spacing:.07em;margin-top:1px}
    .sb-nav{flex:1;overflow-y:auto;padding:12px 10px;scrollbar-width:none}
    .sb-nav::-webkit-scrollbar{display:none}
    .sb-sec{font-size:9px;color:rgba(255,255,255,.22);text-transform:uppercase;letter-spacing:.12em;padding:14px 8px 5px;font-weight:600}
    .sb-a{display:flex;align-items:center;gap:9px;padding:9px 10px;border-radius:8px;color:rgba(255,255,255,.48);font-size:13px;font-weight:500;cursor:pointer;margin-bottom:2px;text-decoration:none;transition:all .14s}
    .sb-a:hover{background:rgba(255,255,255,.06);color:rgba(255,255,255,.85)}
    .sb-a.on{background:var(--p);color:#fff;font-weight:600}
    .sb-a i{font-size:15px;flex-shrink:0;width:18px;text-align:center;opacity:.8}
    .sb-a.on i{opacity:1}
    .sb-badge{margin-left:auto;background:#ef4444;color:#fff;font-size:9.5px;border-radius:10px;padding:1px 6px;font-weight:700}
    .sb-dot{margin-left:auto;width:7px;height:7px;border-radius:50%;background:#22c55e;flex-shrink:0}
    .sb-foot{padding:10px;border-top:1px solid rgba(255,255,255,.07)}
    .sb-out{display:flex;align-items:center;gap:8px;padding:9px 10px;border-radius:8px;color:rgba(255,255,255,.35);font-size:13px;cursor:pointer;text-decoration:none;transition:all .14s;background:none;border:none;width:100%;text-align:left}
    .sb-out:hover{background:rgba(239,68,68,.1);color:#f87171}
    .sb-out i{font-size:15px}
    .sb-ov{display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:190}

    /* ─── MAIN ─── */
    .main{margin-left:var(--sw);flex:1;display:flex;flex-direction:column;min-height:100vh}
    .topbar{height:var(--th);background:var(--surface);border-bottom:1px solid var(--border);display:flex;align-items:center;padding:0 24px;gap:12px;position:sticky;top:0;z-index:100;box-shadow:var(--sh-sm)}
    .tb-title{font-size:15px;font-weight:700;color:var(--text);letter-spacing:-.01em;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
    .tb-right{margin-left:auto;display:flex;align-items:center;gap:8px;flex-shrink:0}
    .content{padding:24px;flex:1}

    /* ─── Cards ─── */
    .card{background:var(--surface);border-radius:var(--r);box-shadow:var(--sh);border:1px solid var(--border)}
    .ch{padding:16px 20px 0;font-size:13.5px;font-weight:700;color:var(--text);display:flex;align-items:center;gap:8px;letter-spacing:-.01em}
    .ch i{color:var(--subtle);font-size:15px}
    .cb{padding:20px}

    /* ─── Stats grid ─── */
    .sg{display:grid;grid-template-columns:repeat(auto-fit,minmax(110px,1fr));gap:12px;margin-bottom:20px}
    .sc{background:var(--surface);border-radius:var(--r);border:1px solid var(--border);padding:16px 18px;box-shadow:var(--sh-sm);transition:box-shadow .15s}
    .sc:hover{box-shadow:var(--sh)}
    .sn{font-size:26px;font-weight:800;line-height:1;letter-spacing:-.03em}
    .sl{font-size:12px;color:var(--subtle);margin-top:4px;font-weight:500}
    .sc.cg .sn{color:#16a34a}.sc.cy .sn{color:#d97706}.sc.cb2 .sn{color:#2563eb}
    .sc.cr .sn{color:#dc2626}.sc.cm .sn{color:var(--subtle)}.sc.cp .sn{color:var(--p)}

    /* ─── Badges ─── */
    .bdg{display:inline-flex;align-items:center;padding:3px 9px;border-radius:20px;font-size:11.5px;font-weight:600;letter-spacing:.01em}
    .bdg-pending{background:#fef9c3;color:#854d0e}
    .bdg-diproses{background:#dbeafe;color:#1e40af}
    .bdg-selesai{background:#dcfce7;color:#166534}
    .bdg-ditolak{background:#fee2e2;color:#991b1b}
    .bdg-outline{background:transparent;border:1.5px solid var(--border);color:var(--subtle)}
    .bdg-gray{background:#f3f4f6;color:#374151}

    /* ─── Table ─── */
    .tbl{width:100%;border-collapse:collapse;font-size:13px}
    .tbl th{padding:10px 14px;text-align:left;font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--subtle);border-bottom:2px solid var(--border);background:#f9fafc;white-space:nowrap}
    .tbl td{padding:12px 14px;border-bottom:1px solid var(--border);vertical-align:middle}
    .tbl tbody tr:hover{background:#fafbfe}
    .tbl tbody tr:last-child td{border-bottom:none}
    .tbl-wrap{overflow-x:auto;-webkit-overflow-scrolling:touch}

    /* ─── Forms ─── */
    .fl{display:block;font-size:12px;font-weight:600;color:var(--text);margin-bottom:5px;letter-spacing:.01em}
    .fc{width:100%;padding:9px 12px;border:1.5px solid var(--border);border-radius:8px;font-family:inherit;font-size:13.5px;color:var(--text);background:#fff;outline:none;transition:border-color .15s,box-shadow .15s}
    .fc:focus{border-color:var(--p);box-shadow:0 0 0 3px color-mix(in srgb,var(--p) 14%,transparent)}
    .fc.is-invalid{border-color:#ef4444}
    select.fc{background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%236b7280' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 12px center;padding-right:32px;appearance:none;cursor:pointer}
    .fi{font-size:11.5px;color:#ef4444;margin-top:4px}

    /* ─── Buttons ─── */
    .btn{display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:8px;font-family:inherit;font-size:13px;font-weight:600;cursor:pointer;border:none;transition:all .15s;text-decoration:none;white-space:nowrap}
    .btn-sm{padding:6px 12px;font-size:12px}
    .btn-p{background:var(--p);color:#fff}
    .btn-p:hover{background:var(--p-dk);color:#fff}
    .btn-ol{background:#fff;color:var(--text);border:1.5px solid var(--border)}
    .btn-ol:hover{border-color:var(--p);color:var(--p)}
    .btn-danger{background:#ef4444;color:#fff}
    .btn-danger:hover{background:#dc2626;color:#fff}
    .btn-ghost{background:transparent;color:var(--subtle);border:none;padding:6px 10px}
    .btn-ghost:hover{background:var(--bg);color:var(--text)}

    /* ─── Alerts ─── */
    .al{padding:12px 16px;border-radius:9px;font-size:13px;display:flex;align-items:flex-start;gap:10px;margin-bottom:16px;border:1px solid}
    .al i{font-size:16px;flex-shrink:0;margin-top:1px}
    .al-ok{background:#f0fdf4;border-color:#bbf7d0;color:#166534}
    .al-err{background:#fef2f2;border-color:#fecaca;color:#991b1b}
    .al-warn{background:#fffbeb;border-color:#fde68a;color:#92400e}
    .al-info{background:#eff6ff;border-color:#bfdbfe;color:#1e40af}

    /* ─── Timeline ─── */
    .tl-item{display:flex;gap:12px;padding-bottom:18px;position:relative}
    .tl-item:not(:last-child)::before{content:'';position:absolute;left:7px;top:18px;bottom:0;width:1.5px;background:var(--border)}
    .tl-dot{width:16px;height:16px;border-radius:50%;background:var(--border);border:2.5px solid #fff;box-shadow:0 0 0 2px var(--border);flex-shrink:0;margin-top:2px}
    .tl-dot.on{background:var(--p);box-shadow:0 0 0 3px color-mix(in srgb,var(--p) 22%,transparent)}

    /* ─── Mono ─── */
    .mono{font-family:'DM Mono',monospace;font-size:12.5px}

    /* ─── Hamburger (desktop drawer trigger, hidden on mobile) ─── */
    .hbg{display:none;background:none;border:none;cursor:pointer;padding:6px;border-radius:7px;color:var(--subtle);align-items:center}
    .hbg:hover{background:var(--bg)}
    .hbg i{font-size:20px}

    /* ═══════════════════════════════════════
       MOBILE  ≤ 900 px
    ═══════════════════════════════════════ */
    @media(max-width:900px){
      /* Kill desktop sidebar */
      .sb,.sb-ov,.hbg{display:none!important}

      .main{margin-left:0}
      .content{padding:14px 12px;padding-bottom:calc(var(--mn) + env(safe-area-inset-bottom) + 12px)}

      /* Slim topbar */
      .topbar{padding:0 14px;gap:8px}
      .tb-right .btn .btn-lbl{display:none}
      .tb-right .btn{padding:6px 9px}

      /* ── Bottom Nav ── */
      .mob-nav{
        display:flex;
        position:fixed;
        bottom:0;left:0;right:0;
        height:calc(var(--mn) + env(safe-area-inset-bottom));
        padding-bottom:env(safe-area-inset-bottom);
        background:var(--text);
        border-top:1px solid rgba(255,255,255,.07);
        box-shadow:0 -4px 20px rgba(0,0,0,.25);
        z-index:500;
        align-items:stretch;
        padding-left:4px;padding-right:4px;
        transform:translateY(0);
        transition:transform .3s cubic-bezier(.4,0,.2,1);
        will-change:transform;
      }
      .mob-nav.nav-hidden{transform:translateY(100%)}

      /* Nav item */
      .mn-item{
        flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;
        gap:3px;text-decoration:none;color:rgba(255,255,255,.38);
        padding:8px 2px 6px;border-radius:10px;margin:5px 2px 5px;
        transition:color .15s,background .15s;
        position:relative;cursor:pointer;
        border:none;background:none;font-family:inherit;
        -webkit-tap-highlight-color:transparent;
      }
      .mn-item:active{opacity:.7}
      .mn-item.on{color:var(--p)}
      .mn-item.on .mn-ico{transform:translateY(-1px)}
      /* active indicator */
      .mn-item.on::before{
        content:'';position:absolute;top:5px;
        width:28px;height:3px;border-radius:2px;
        background:var(--p);top:3px;
      }
      .mn-ico{font-size:20px;line-height:1;transition:transform .15s cubic-bezier(.34,1.4,.64,1);display:block}
      .mn-lbl{font-size:9.5px;font-weight:600;letter-spacing:.02em;line-height:1}

      /* Badge on icon */
      .mn-badge{
        position:absolute;top:3px;right:calc(50% - 18px);
        background:#ef4444;color:#fff;font-size:8px;font-weight:700;
        border-radius:8px;padding:1px 4px;border:1.5px solid var(--text);
        min-width:14px;text-align:center;line-height:1.4;
      }

      /* ── More drawer ── */
      .mob-drawer-ov{
        display:none;position:fixed;inset:0;z-index:510;
        background:rgba(0,0,0,.5);
        animation:fadeIn .2s both;
      }
      .mob-drawer-ov.open{display:block}
      @keyframes fadeIn{from{opacity:0}to{opacity:1}}

      .mob-drawer{
        display:none;
        position:fixed;bottom:0;left:0;right:0;
        background:var(--text);
        border-radius:18px 18px 0 0;
        z-index:520;
        padding-bottom:calc(env(safe-area-inset-bottom) + 8px);
        animation:slideUp .24s cubic-bezier(.4,0,.2,1) both;
        max-height:80vh;overflow-y:auto;
      }
      .mob-drawer.open{display:block}
      @keyframes slideUp{from{opacity:0;transform:translateY(24px)}to{opacity:1;transform:translateY(0)}}

      /* Drag handle */
      .md-handle{width:36px;height:4px;border-radius:2px;background:rgba(255,255,255,.15);margin:12px auto 8px}

      .md-head{
        padding:4px 16px 12px;
        display:flex;align-items:center;gap:10px;
        border-bottom:1px solid rgba(255,255,255,.07);
      }
      .md-av{width:36px;height:36px;border-radius:50%;background:var(--p);display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:#fff;flex-shrink:0}
      .md-uname{font-size:13px;font-weight:700;color:rgba(255,255,255,.9)}
      .md-urole{font-size:10.5px;color:rgba(255,255,255,.3);text-transform:uppercase;letter-spacing:.06em;margin-top:1px}

      .md-sec{font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:.12em;color:rgba(255,255,255,.22);padding:14px 16px 5px}

      .md-a{
        display:flex;align-items:center;gap:12px;
        padding:12px 16px;border-radius:10px;
        color:rgba(255,255,255,.6);font-size:13.5px;font-weight:500;
        text-decoration:none;margin:2px 8px;
        transition:all .13s;
      }
      .md-a:active,.md-a:hover{background:rgba(255,255,255,.07);color:#fff}
      .md-a.on{background:var(--p);color:#fff}
      .md-a i{font-size:17px;width:22px;text-align:center;flex-shrink:0}
      .md-a-dot{margin-left:auto;width:7px;height:7px;border-radius:50%;background:#22c55e}
      .md-a-bdg{margin-left:auto;background:#ef4444;color:#fff;font-size:9px;font-weight:700;border-radius:8px;padding:1px 5px}

      .md-out{
        display:flex;align-items:center;gap:12px;
        padding:13px 16px;border-radius:10px;
        color:rgba(239,68,68,.75);font-size:13.5px;font-weight:500;
        width:100%;background:none;border:none;font-family:inherit;cursor:pointer;
        margin:2px 8px;margin-top:8px;
        border-top:1px solid rgba(255,255,255,.06);padding-top:15px;
        transition:all .13s;
        text-align:left;
      }
      .md-out:active,.md-out:hover{background:rgba(239,68,68,.08);color:#f87171}
      .md-out i{font-size:17px;width:22px;text-align:center}
    }

    /* Hide mobile nav on desktop */
    @media(min-width:901px){
      .mob-nav,.mob-drawer,.mob-drawer-ov{display:none!important}
    }
  </style>
  <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>


<aside class="sb" id="sidebar">
  <div class="sb-top">
    <?php if($logoUrl): ?>
      <img src="<?php echo e($logoUrl); ?>" class="sb-logo" alt="Logo">
    <?php else: ?>
      <div class="sb-logobox">🏛️</div>
    <?php endif; ?>
    <div>
      <div class="sb-name"><?php echo e(\App\Models\Setting::get('desa_nama','Desa')); ?></div>
      <div class="sb-sub">Panel Administrasi</div>
    </div>
  </div>

  <div class="sb-user">
    <div class="sb-av"><?php echo e(strtoupper(substr(auth()->user()->name,0,2))); ?></div>
    <div>
      <div class="sb-uname"><?php echo e(auth()->user()->name); ?></div>
      <div class="sb-urole"><?php echo e(auth()->user()->role); ?></div>
    </div>
  </div>

  <nav class="sb-nav">
    <div class="sb-sec">Utama</div>
    <a href="<?php echo e(route('admin.dashboard')); ?>" class="sb-a <?php echo e(request()->routeIs('admin.dashboard') ? 'on' : ''); ?>">
      <i class="bi bi-grid-1x2-fill"></i> Dashboard
    </a>
    <a href="<?php echo e(route('admin.antrean.index')); ?>" class="sb-a <?php echo e(request()->routeIs('admin.antrean.*') ? 'on' : ''); ?>">
      <i class="bi bi-inbox-fill"></i> Antrean Surat
      <?php $pending = \App\Models\Permohonan::where('status','pending')->count() ?>
      <?php if($pending > 0): ?><span class="sb-badge"><?php echo e($pending); ?></span><?php endif; ?>
    </a>
    <a href="<?php echo e(url('admin/laporan')); ?>" class="sb-a <?php echo e(request()->is('admin/laporan*') ? 'on' : ''); ?>">
      <i class="bi bi-bar-chart-fill"></i> Laporan
    </a>

    @role('admin,petugas')
    <div class="sb-sec">Konten</div>
    <a href="<?php echo e(route('admin.jenis-surat.index')); ?>" class="sb-a <?php echo e(request()->routeIs('admin.jenis-surat.*') ? 'on' : ''); ?>">
      <i class="bi bi-collection-fill"></i> Jenis Surat
    </a>
    <a href="<?php echo e(route('admin.template.index')); ?>" class="sb-a <?php echo e(request()->routeIs('admin.template.*') ? 'on' : ''); ?>">
      <i class="bi bi-file-earmark-word-fill"></i> Template Surat
    </a>
    @endrole

    @role('admin')
    <div class="sb-sec">Sistem</div>
    <a href="<?php echo e(route('admin.whatsapp.index')); ?>" class="sb-a <?php echo e(request()->routeIs('admin.whatsapp.*') ? 'on' : ''); ?>">
      <i class="bi bi-whatsapp"></i> WhatsApp
      <?php if(\App\Models\Setting::get('wa_notif_aktif','0') == '1'): ?><span class="sb-dot"></span><?php endif; ?>
    </a>
    <a href="<?php echo e(route('admin.users.index')); ?>" class="sb-a <?php echo e(request()->routeIs('admin.users.*') ? 'on' : ''); ?>">
      <i class="bi bi-people-fill"></i> Pengguna
    </a>
    <a href="<?php echo e(route('admin.pengaturan.index')); ?>" class="sb-a <?php echo e(request()->routeIs('admin.pengaturan.*') ? 'on' : ''); ?>">
      <i class="bi bi-gear-fill"></i> Pengaturan
    </a>
    @endrole

    @role('admin,viewer')
    <a href="<?php echo e(url('admin/backup')); ?>" class="sb-a <?php echo e(request()->is('admin/backup*') ? 'on' : ''); ?>">
      <i class="bi bi-shield-fill-check"></i> Backup &amp; Restore
    </a>
    <a href="<?php echo e(route('admin.audit-log.index')); ?>" class="sb-a <?php echo e(request()->routeIs('admin.audit-log.*') ? 'on' : ''); ?>">
      <i class="bi bi-journal-text"></i> Audit Log
    </a>
    @endrole
  </nav>

  <div class="sb-foot">
    <form method="POST" action="<?php echo e(route('admin.logout')); ?>"><?php echo csrf_field(); ?>
      <button type="submit" class="sb-out"><i class="bi bi-box-arrow-left"></i> Keluar</button>
    </form>
  </div>
</aside>
<div class="sb-ov" id="sbOv" onclick="closeSb()"></div>


<div class="main">
  <header class="topbar">
    <button class="hbg" id="hbg" onclick="openSb()"><i class="bi bi-list"></i></button>
    <div class="tb-title"><?php echo $__env->yieldContent('title','Dashboard'); ?></div>
    <div class="tb-right">
      <a href="<?php echo e(route('home')); ?>" class="btn btn-ol btn-sm" target="_blank">
        <i class="bi bi-globe2"></i><span class="btn-lbl"> Halaman Warga</span>
      </a>
    </div>
  </header>

  <div class="content">
    <?php if(session('success')): ?>
      <div class="al al-ok"><i class="bi bi-check-circle-fill"></i><span><?php echo e(session('success')); ?></span></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
      <div class="al al-err"><i class="bi bi-exclamation-circle-fill"></i><span><?php echo e(session('error')); ?></span></div>
    <?php endif; ?>
    <?php echo $__env->yieldContent('content'); ?>
  </div>
</div>


<?php $pendingMob = \App\Models\Permohonan::where('status','pending')->count(); ?>

<nav class="mob-nav" id="mobNav" aria-label="Navigasi utama">

  <a href="<?php echo e(route('admin.dashboard')); ?>"
     class="mn-item <?php echo e(request()->routeIs('admin.dashboard') ? 'on' : ''); ?>">
    <i class="bi bi-grid-1x2-fill mn-ico"></i>
    <span class="mn-lbl">Dashboard</span>
  </a>

  <a href="<?php echo e(route('admin.antrean.index')); ?>"
     class="mn-item <?php echo e(request()->routeIs('admin.antrean.*') ? 'on' : ''); ?>">
    <i class="bi bi-inbox-fill mn-ico"></i>
    <?php if($pendingMob > 0): ?>
      <span class="mn-badge"><?php echo e($pendingMob > 9 ? '9+' : $pendingMob); ?></span>
    <?php endif; ?>
    <span class="mn-lbl">Antrean</span>
  </a>

  <a href="<?php echo e(url('admin/laporan')); ?>"
     class="mn-item <?php echo e(request()->is('admin/laporan*') ? 'on' : ''); ?>">
    <i class="bi bi-bar-chart-fill mn-ico"></i>
    <span class="mn-lbl">Laporan</span>
  </a>

  <button class="mn-item" id="mnMoreBtn" onclick="openDrawer()" type="button"
          aria-label="Menu lainnya" aria-expanded="false">
    <i class="bi bi-grid-3x3-gap-fill mn-ico" id="mnMoreIco"></i>
    <span class="mn-lbl">Lainnya</span>
  </button>

</nav>


<div class="mob-drawer-ov" id="drawerOv" onclick="closeDrawer()"></div>


<div class="mob-drawer" id="mobDrawer" role="dialog" aria-modal="true">
  <div class="md-handle"></div>

  
  <div class="md-head">
    <div class="md-av"><?php echo e(strtoupper(substr(auth()->user()->name,0,2))); ?></div>
    <div>
      <div class="md-uname"><?php echo e(auth()->user()->name); ?></div>
      <div class="md-urole"><?php echo e(auth()->user()->role); ?></div>
    </div>
  </div>

  @role('admin,petugas')
  <div class="md-sec">Konten</div>
  <a href="<?php echo e(route('admin.jenis-surat.index')); ?>"
     class="md-a <?php echo e(request()->routeIs('admin.jenis-surat.*') ? 'on' : ''); ?>">
    <i class="bi bi-collection-fill"></i> Jenis Surat
  </a>
  <a href="<?php echo e(route('admin.template.index')); ?>"
     class="md-a <?php echo e(request()->routeIs('admin.template.*') ? 'on' : ''); ?>">
    <i class="bi bi-file-earmark-word-fill"></i> Template Surat
  </a>
  @endrole

  @role('admin')
  <div class="md-sec">Sistem</div>
  <a href="<?php echo e(route('admin.whatsapp.index')); ?>"
     class="md-a <?php echo e(request()->routeIs('admin.whatsapp.*') ? 'on' : ''); ?>">
    <i class="bi bi-whatsapp"></i> WhatsApp
    <?php if(\App\Models\Setting::get('wa_notif_aktif','0') == '1'): ?><span class="md-a-dot"></span><?php endif; ?>
  </a>
  <a href="<?php echo e(route('admin.users.index')); ?>"
     class="md-a <?php echo e(request()->routeIs('admin.users.*') ? 'on' : ''); ?>">
    <i class="bi bi-people-fill"></i> Pengguna
  </a>
  <a href="<?php echo e(route('admin.pengaturan.index')); ?>"
     class="md-a <?php echo e(request()->routeIs('admin.pengaturan.*') ? 'on' : ''); ?>">
    <i class="bi bi-gear-fill"></i> Pengaturan
  </a>
  @endrole

  @role('admin,viewer')
  <a href="<?php echo e(url('admin/backup')); ?>"
     class="md-a <?php echo e(request()->is('admin/backup*') ? 'on' : ''); ?>">
    <i class="bi bi-shield-fill-check"></i> Backup &amp; Restore
  </a>
  <a href="<?php echo e(route('admin.audit-log.index')); ?>"
     class="md-a <?php echo e(request()->routeIs('admin.audit-log.*') ? 'on' : ''); ?>">
    <i class="bi bi-journal-text"></i> Audit Log
  </a>
  @endrole

  <a href="<?php echo e(route('home')); ?>" target="_blank" class="md-a">
    <i class="bi bi-globe2"></i> Halaman Warga
  </a>

  <form method="POST" action="<?php echo e(route('admin.logout')); ?>" style="margin:0 0 4px"><?php echo csrf_field(); ?>
    <button type="submit" class="md-out"><i class="bi bi-box-arrow-left"></i> Keluar</button>
  </form>
</div>

<script>
// desktop
function openSb(){document.getElementById('sidebar').classList.add('open');document.getElementById('sbOv').classList.add('show')}
function closeSb(){document.getElementById('sidebar').classList.remove('open');document.getElementById('sbOv').classList.remove('show')}

// mobile drawer
function openDrawer(){
  document.getElementById('mobDrawer').classList.add('open');
  document.getElementById('drawerOv').classList.add('open');
  document.getElementById('mnMoreBtn').setAttribute('aria-expanded','true');
}
function closeDrawer(){
  document.getElementById('mobDrawer').classList.remove('open');
  document.getElementById('drawerOv').classList.remove('open');
  document.getElementById('mnMoreBtn').setAttribute('aria-expanded','false');
}

// auto-hide navbar on scroll
(function(){
  var nav=document.getElementById('mobNav');
  if(!nav)return;
  var lastY=0,ticking=false,timer=null;
  window.addEventListener('scroll',function(){
    if(!ticking){
      requestAnimationFrame(function(){
        var y=window.scrollY,d=y-lastY;
        if(d>10){nav.classList.add('nav-hidden');closeDrawer();}
        else if(d<-4){nav.classList.remove('nav-hidden');}
        lastY=y;ticking=false;
        clearTimeout(timer);
        timer=setTimeout(function(){nav.classList.remove('nav-hidden');},350);
      });
      ticking=true;
    }
  },{passive:true});
})();
</script>
<?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\laragon\www\desa\resources\views/layouts/admin.blade.php ENDPATH**/ ?>