<?php $__env->startSection('title','Beranda'); ?>
<?php $__env->startSection('body_class','hero-page'); ?>
<?php $__env->startSection('content'); ?>

<?php
  $primer    = \App\Models\Setting::get('warna_primer','#1a6b3a');
  $sekunder  = \App\Models\Setting::get('warna_sekunder','#f0a500');
  $desaNama  = \App\Models\Setting::get('desa_nama','Desa');
  $jamBuka   = \App\Models\Setting::get('jam_kerja_buka','08:00');
  $jamTutup  = \App\Models\Setting::get('jam_kerja_tutup','15:00');
  $totalSelesai = \App\Models\Permohonan::where('status','selesai')->count();
  $totalSurat   = \App\Models\JenisSurat::where('aktif',true)->count();
?>

<style>
  :root {
    --primer:    <?php echo e($primer); ?>;
    --sekunder:  <?php echo e($sekunder); ?>;
    --primer-dk: color-mix(in srgb, var(--primer) 60%, #000);
    --primer-md: color-mix(in srgb, var(--primer) 80%, #000);
    --primer-lt: color-mix(in srgb, var(--primer) 30%, #fff);
    --sek-lt:    color-mix(in srgb, var(--sekunder) 70%, #fff);
  }

  body.hero-page {
    background:
      radial-gradient(ellipse 80% 50% at 110% 20%, color-mix(in srgb, var(--sekunder) 22%, transparent) 0%, transparent 60%),
      radial-gradient(ellipse 60% 60% at -10% 80%, color-mix(in srgb, var(--primer-lt) 18%, transparent) 0%, transparent 55%),
      linear-gradient(160deg, var(--primer-dk) 0%, var(--primer-md) 45%, color-mix(in srgb, var(--primer) 85%, #000) 100%);
  }

  /* Dot grid pattern overlay — warna ikut tema */
  .hero-pattern {
    position: fixed;
    inset: 0;
    pointer-events: none;
    z-index: 0;
    background-image: radial-gradient(
      circle,
      color-mix(in srgb, var(--primer-lt) 35%, transparent) 1px,
      transparent 1px
    );
    background-size: 26px 26px;
    opacity: .5;
  }
  /* Diagonal stripe layer */
  .hero-pattern::after {
    content: '';
    position: absolute;
    inset: 0;
    background-image: repeating-linear-gradient(
      -48deg,
      color-mix(in srgb, var(--sekunder) 6%, transparent) 0px,
      color-mix(in srgb, var(--sekunder) 6%, transparent) 1px,
      transparent 1px,
      transparent 38px
    );
    opacity: .7;
  }

  /* Content sits above pattern */
  .hero-wrap, .cara-section { position: relative; z-index: 1; }

  /* Wave separator between hero and tata cara */
  .hero-wave {
    position: relative;
    z-index: 1;
    margin: 0 -16px;
    line-height: 0;
  }
  .hero-wave svg { display: block; width: 100%; }
  body.hero-page .hdr {
    background: color-mix(in srgb, var(--primer-dk) 90%, transparent);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border-bottom: 1px solid color-mix(in srgb, var(--primer-lt) 20%, transparent);
  }
  body.hero-page footer {
    background: color-mix(in srgb, var(--primer-dk) 60%, transparent);
    border-top: 1px solid color-mix(in srgb, #fff 10%, transparent);
    color: rgba(255,255,255,.45);
  }
  body.hero-page footer a { color: rgba(255,255,255,.45); }
  body.hero-page footer a:hover { color: #fff; }

  .hero-wrap {
    min-height: calc(100svh - 70px);
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 20px 0 60px;
  }

  /* Badge */
  .hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: color-mix(in srgb, var(--sekunder) 14%, transparent);
    border: 1.5px solid color-mix(in srgb, var(--sekunder) 40%, transparent);
    border-radius: 30px;
    padding: 5px 16px;
    font-size: 10.5px;
    font-weight: 700;
    letter-spacing: .12em;
    text-transform: uppercase;
    color: var(--sek-lt);
    margin: 0 auto 28px;
  }
  .hero-badge-dot {
    width: 7px; height: 7px;
    background: var(--sekunder);
    border-radius: 50%;
    box-shadow: 0 0 0 3px color-mix(in srgb, var(--sekunder) 28%, transparent);
    animation: pulse 2.2s ease-in-out infinite;
  }
  @keyframes pulse {
    0%,100%{ box-shadow: 0 0 0 3px color-mix(in srgb, var(--sekunder) 28%, transparent); }
    50%    { box-shadow: 0 0 0 6px color-mix(in srgb, var(--sekunder) 10%, transparent); }
  }

  /* Headline */
  .hero-title {
    text-align: center;
    font-size: clamp(30px, 9vw, 46px);
    font-weight: 900;
    line-height: 1.15;
    letter-spacing: -.03em;
    color: #fff;
    margin: 0 0 18px;
  }
  .hero-title-accent {
    color: var(--sek-lt);
  }
  .hero-sub {
    text-align: center;
    font-size: 14.5px;
    color: rgba(255,255,255,.65);
    line-height: 1.75;
    max-width: 380px;
    margin: 0 auto 38px;
  }

  /* Buttons */
  .hero-btn-primary {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 15px 24px;
    background: linear-gradient(135deg,
      color-mix(in srgb, var(--sekunder) 90%, #fff) 0%,
      var(--sekunder) 100%);
    color: color-mix(in srgb, var(--primer-dk) 90%, #000);
    border-radius: 13px;
    font-size: 15.5px;
    font-weight: 800;
    text-decoration: none;
    box-shadow:
      0 6px 20px color-mix(in srgb, var(--sekunder) 35%, transparent),
      0 1px 2px rgba(0,0,0,.15);
    transition: transform .15s, box-shadow .15s;
    letter-spacing: -.01em;
  }
  .hero-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow:
      0 10px 28px color-mix(in srgb, var(--sekunder) 45%, transparent),
      0 1px 3px rgba(0,0,0,.18);
  }
  .hero-btn-primary:active { transform: scale(.98); }

  .hero-btn-ghost {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    padding: 12px 14px;
    background: color-mix(in srgb, #fff 8%, transparent);
    border: 1.5px solid color-mix(in srgb, #fff 20%, transparent);
    color: rgba(255,255,255,.85);
    border-radius: 11px;
    font-size: 13.5px;
    font-weight: 600;
    text-decoration: none;
    transition: background .15s, border-color .15s;
    backdrop-filter: blur(6px);
  }
  .hero-btn-ghost:hover {
    background: color-mix(in srgb, #fff 14%, transparent);
    border-color: color-mix(in srgb, #fff 35%, transparent);
    color: #fff;
  }

  /* Stats */
  .hero-stats {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 0;
    margin-top: 52px;
    background: color-mix(in srgb, #fff 6%, transparent);
    border: 1px solid color-mix(in srgb, #fff 12%, transparent);
    border-radius: 14px;
    backdrop-filter: blur(8px);
    overflow: hidden;
  }
  .hero-stat {
    flex: 1;
    text-align: center;
    padding: 16px 10px;
  }
  .hero-stat + .hero-stat {
    border-left: 1px solid color-mix(in srgb, #fff 12%, transparent);
  }
  .hero-stat-val {
    font-size: 22px;
    font-weight: 800;
    color: #fff;
    letter-spacing: -.02em;
    line-height: 1;
  }
  .hero-stat-val span {
    color: var(--sek-lt);
  }
  .hero-stat-lbl {
    font-size: 10.5px;
    color: rgba(255,255,255,.5);
    margin-top: 4px;
    text-transform: uppercase;
    letter-spacing: .07em;
    font-weight: 600;
  }

  /* Cara section */
  .cara-section {
    padding: 52px 0 24px;
  }
  .cara-title {
    text-align: center;
    font-size: 18px;
    font-weight: 800;
    color: rgba(255,255,255,.9);
    margin: 0 0 8px;
    letter-spacing: -.02em;
  }
  .cara-sub {
    text-align: center;
    font-size: 13px;
    color: rgba(255,255,255,.45);
    margin: 0 0 28px;
  }
  .cara-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
  }
  .cara-item {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    background: color-mix(in srgb, #fff 6%, transparent);
    border: 1px solid color-mix(in srgb, #fff 11%, transparent);
    border-radius: 13px;
    padding: 14px 16px;
    backdrop-filter: blur(6px);
    transition: background .15s;
  }
  .cara-item:hover {
    background: color-mix(in srgb, #fff 10%, transparent);
  }
  .cara-num {
    width: 32px; height: 32px;
    background: linear-gradient(135deg,
      color-mix(in srgb, var(--sekunder) 80%, #fff),
      var(--sekunder));
    border-radius: 9px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    font-weight: 800;
    color: color-mix(in srgb, var(--primer-dk) 85%, #000);
    flex-shrink: 0;
    box-shadow: 0 3px 10px color-mix(in srgb, var(--sekunder) 30%, transparent);
  }
  .cara-body-title {
    font-size: 13.5px;
    font-weight: 700;
    color: rgba(255,255,255,.9);
    margin: 0 0 3px;
  }
  .cara-body-desc {
    font-size: 12px;
    color: rgba(255,255,255,.52);
    line-height: 1.6;
    margin: 0;
  }

  .cara-cta {
    text-align: center;
    margin-top: 28px;
  }

  /* Jam pelayanan chip */
  .jam-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: color-mix(in srgb, #fff 7%, transparent);
    border: 1px solid color-mix(in srgb, #fff 14%, transparent);
    border-radius: 20px;
    padding: 6px 14px;
    font-size: 12px;
    color: rgba(255,255,255,.55);
    margin: 36px auto 0;
    display: flex;
    width: fit-content;
  }
</style>


<div class="hero-pattern" aria-hidden="true"></div>

<div class="hero-wrap">

  
  <div style="display:flex;justify-content:center">
    <div class="hero-badge">
      <span class="hero-badge-dot"></span>
      Layanan Publik Digital
    </div>
  </div>

  
  <h1 class="hero-title">
    Pelayanan <span class="hero-title-accent">Surat Desa</span><br>
    Kini Lebih Mudah
  </h1>

  
  <p class="hero-sub">
    Sistem informasi pelayanan administrasi surat menyurat <?php echo e($desaNama); ?>.
    Ajukan permohonan secara mandiri dengan cepat, transparan, dan efisien.
  </p>

  
  <div style="display:flex;flex-direction:column;gap:10px;max-width:400px;margin:0 auto;width:100%">
    <a href="<?php echo e(route('warga.ajukan')); ?>" class="hero-btn-primary">
      <i class="bi bi-pencil-square" style="font-size:17px"></i>
      Buat Permintaan Baru
    </a>
    <div style="display:flex;gap:10px">
      <a href="<?php echo e(route('warga.ajukan')); ?>?tab=lacak" class="hero-btn-ghost">
        <i class="bi bi-search"></i> Lacak Status
      </a>
      <a href="#tata-cara" class="hero-btn-ghost">
        <i class="bi bi-question-circle"></i> Tata Cara
      </a>
    </div>
  </div>

  
  <div class="hero-stats" style="max-width:400px;margin:52px auto 0;width:100%">
    <div class="hero-stat">
      <div class="hero-stat-val"><span><?php echo e($totalSelesai); ?></span></div>
      <div class="hero-stat-lbl">Surat Selesai</div>
    </div>
    <div class="hero-stat">
      <div class="hero-stat-val"><span><?php echo e($totalSurat); ?></span></div>
      <div class="hero-stat-lbl">Jenis Surat</div>
    </div>
    <div class="hero-stat">
      <div class="hero-stat-val">24/7</div>
      <div class="hero-stat-lbl">Pengajuan Online</div>
    </div>
  </div>

</div>


<div class="hero-wave">
  <svg viewBox="0 0 390 56" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" height="56">
    <path d="M0 20 C60 50, 120 0, 195 28 C270 56, 330 8, 390 32 L390 56 L0 56 Z"
          fill="color-mix(in srgb, var(--primer-dk) 70%, transparent)" opacity=".5"/>
    <path d="M0 32 C80 10, 150 48, 220 24 C290 0, 340 44, 390 20 L390 56 L0 56 Z"
          fill="color-mix(in srgb, var(--primer-dk) 90%, transparent)"/>
  </svg>
</div>


<div class="cara-section" id="tata-cara">
  <h2 class="cara-title">Cara Mengajukan Surat</h2>
  <p class="cara-sub">Empat langkah mudah, selesai dari genggaman</p>

  <div class="cara-list" style="max-width:480px;margin:0 auto">
    <?php $__currentLoopData = [
      ['Isi Data Diri',       'Masukkan NIK, nama lengkap, dan nomor WhatsApp aktif.'],
      ['Pilih Jenis Surat',   'Pilih jenis surat yang dibutuhkan dan tulis keperluan.'],
      ['Upload Berkas',       'Lampirkan foto KTP dan KK sebagai berkas pendukung.'],
      ['Terima Notifikasi',   'Status surat dikirim via WhatsApp ke nomor Anda.'],
    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => [$judul, $deskripsi]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="cara-item">
      <div class="cara-num"><?php echo e($i + 1); ?></div>
      <div>
        <p class="cara-body-title"><?php echo e($judul); ?></p>
        <p class="cara-body-desc"><?php echo e($deskripsi); ?></p>
      </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </div>

  <div class="cara-cta">
    <a href="<?php echo e(route('warga.ajukan')); ?>" class="hero-btn-primary" style="display:inline-flex;width:auto">
      <i class="bi bi-arrow-right-circle"></i> Mulai Ajukan Sekarang
    </a>
  </div>

  <div class="jam-chip">
    <i class="bi bi-clock" style="font-size:13px"></i>
    Jam Pelayanan: Senin–Jumat <?php echo e($jamBuka); ?>–<?php echo e($jamTutup); ?> WIB
  </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.warga', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\desa\resources\views/warga/index.blade.php ENDPATH**/ ?>