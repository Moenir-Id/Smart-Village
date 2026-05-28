<?php $__env->startSection('title','WhatsApp Web'); ?>
<?php $__env->startSection('content'); ?>

<style>
  /* 2 col desktop → 1 col mobile */
  .wa-grid{display:grid;grid-template-columns:1fr 1.4fr;gap:16px;align-items:start}
  .wa-left{display:flex;flex-direction:column;gap:16px}
  @media(max-width:800px){
    .wa-grid{grid-template-columns:1fr}
  }
  /* Test kirim row */
  .wa-test-row{display:flex;gap:8px}
  @media(max-width:420px){
    .wa-test-row{flex-direction:column}
    .wa-test-row .btn{width:100%;justify-content:center}
  }
</style>

<div style="margin-bottom:20px">
  <h2 style="font-size:19px;font-weight:800;margin:0;letter-spacing:-.02em">WhatsApp Web</h2>
</div>

<div class="wa-grid">

  
  <div class="wa-left">

    
    <div class="card">
      <div class="ch"><i class="bi bi-whatsapp" style="color:#25d366"></i> Status Koneksi</div>
      <div class="cb" style="text-align:center;padding:20px">

        
        <div id="state-connected" style="display:none">
          <div style="width:64px;height:64px;background:#dcfce7;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;font-size:26px">✅</div>
          <div style="font-size:15px;font-weight:700;color:#166534;margin-bottom:4px">Terhubung</div>
          <div style="font-size:13px;color:var(--subtle)" id="wa-name-display"></div>
          <div class="mono" style="font-size:12px;color:var(--subtle)" id="wa-nomor-display"></div>
          <form method="POST" action="<?php echo e(route('admin.whatsapp.disconnect')); ?>" style="margin-top:18px"
                onsubmit="return confirm('Putuskan koneksi WhatsApp?')">
            <?php echo csrf_field(); ?>
            <button type="submit" class="btn btn-sm" style="border:1.5px solid #ef4444;color:#ef4444;background:#fff">
              <i class="bi bi-plug"></i> Putuskan
            </button>
          </form>
        </div>

        
        <div id="state-qr">
          <div style="font-size:13px;color:var(--subtle);margin-bottom:16px;line-height:1.7">
            Buka <b>WhatsApp</b> di HP →<br><b>Perangkat Tertaut</b> → <b>Tautkan Perangkat</b> → scan QR
          </div>
          <div id="qr-wrapper" style="width:220px;height:220px;margin:0 auto;border:2px solid var(--border);border-radius:12px;display:flex;align-items:center;justify-content:center;background:var(--bg);overflow:hidden">
            <div id="qr-loading" style="text-align:center">
              <div style="font-size:28px;margin-bottom:8px">⏳</div>
              <div style="font-size:12px;color:var(--subtle)">Memuat QR…</div>
            </div>
            <img id="qr-img" src="" alt="QR Code" style="display:none;width:200px;height:200px;object-fit:contain">
          </div>
          <div style="margin-top:10px;font-size:11.5px;color:var(--subtle)">QR refresh otomatis tiap 3 detik</div>
        </div>

        
        <div id="state-error" style="display:none">
          <div style="font-size:28px;margin-bottom:10px">🔌</div>
          <div style="font-size:13.5px;color:#ef4444;font-weight:700">Bridge tidak bisa dihubungi</div>
          <div style="font-size:12px;color:var(--subtle);margin-top:6px">Jalankan <code>node wa-bridge.js</code></div>
          <button onclick="pollStatus()" class="btn btn-ol btn-sm" style="margin-top:14px">
            <i class="bi bi-arrow-clockwise"></i> Coba lagi
          </button>
        </div>

      </div>
    </div>

    
    <div class="card">
      <div class="ch"><i class="bi bi-gear-fill"></i> Pengaturan</div>
      <div class="cb" style="padding:16px">
        <form method="POST" action="<?php echo e(route('admin.whatsapp.pengaturan')); ?>">
          <?php echo csrf_field(); ?>
          <div style="display:flex;flex-direction:column;gap:14px">
            <label style="display:flex;align-items:center;gap:11px;cursor:pointer;padding:13px 15px;background:var(--bg);border-radius:9px;border:1.5px solid var(--border)">
              <input type="checkbox" name="wa_notif_aktif" value="1"
                <?php echo e(($s['wa_notif_aktif']??'0')==='1'?'checked':''); ?>

                style="width:18px;height:18px;accent-color:var(--p);cursor:pointer;flex-shrink:0">
              <div>
                <div style="font-size:13.5px;font-weight:600">Notifikasi Otomatis</div>
                <div style="font-size:12px;color:var(--subtle);margin-top:1px">Kirim WA saat status berubah</div>
              </div>
            </label>
            <div>
              <label class="fl">Footer Pesan</label>
              <input type="text" name="wa_footer" class="fc"
                value="<?php echo e($s['wa_footer']??''); ?>"
                placeholder="_Pesan otomatis, mohon tidak dibalas._">
              <p style="font-size:11.5px;color:var(--subtle);margin-top:5px">Disisipkan di akhir semua pesan WA</p>
            </div>
          </div>
          <div style="margin-top:16px">
            <button type="submit" class="btn btn-p"><i class="bi bi-check-lg"></i> Simpan</button>
          </div>
        </form>

        <div style="border-top:1px solid var(--border);margin:20px 0"></div>

        <form method="POST" action="<?php echo e(route('admin.whatsapp.test')); ?>">
          <?php echo csrf_field(); ?>
          <label class="fl">Kirim Pesan Uji Coba</label>
          <div class="wa-test-row">
            <input type="text" name="nomor_tujuan" class="fc" placeholder="08xxxxxxxxxx" style="flex:1">
            <button type="submit" class="btn btn-ol">
              <i class="bi bi-send"></i> Kirim
            </button>
          </div>
          <p style="font-size:11.5px;color:var(--subtle);margin-top:5px">Gunakan nomor WA Anda sendiri</p>
        </form>
      </div>
    </div>
  </div>

  
  <div class="card">
    <div class="ch"><i class="bi bi-chat-text-fill"></i> Template Pesan per Status</div>
    <div class="cb" style="padding:16px">
      <div class="al al-info" style="margin-bottom:18px">
        <i class="bi bi-info-circle-fill"></i>
        <div style="font-size:12.5px">
          Variabel:
          <code style="background:rgba(255,255,255,.5);padding:1px 5px;border-radius:4px">{nama}</code>
          <code style="background:rgba(255,255,255,.5);padding:1px 5px;border-radius:4px">{kode_unik}</code>
          <code style="background:rgba(255,255,255,.5);padding:1px 5px;border-radius:4px">{jenis_surat}</code>
          <code style="background:rgba(255,255,255,.5);padding:1px 5px;border-radius:4px">{status}</code>
          <code style="background:rgba(255,255,255,.5);padding:1px 5px;border-radius:4px">{catatan}</code>
          — Kosongkan untuk template bawaan.
        </div>
      </div>

      <form method="POST" action="<?php echo e(route('admin.whatsapp.template')); ?>">
        <?php echo csrf_field(); ?>
        <div style="display:flex;flex-direction:column;gap:20px">
          <?php $__currentLoopData = ['diproses'=>['Sedang Diproses','#1e40af','#dbeafe'],'selesai'=>['Selesai','#166534','#dcfce7'],'ditolak'=>['Ditolak','#991b1b','#fee2e2']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status => [$label,$clr,$bg]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <div>
            <div style="display:flex;align-items:center;gap:9px;margin-bottom:8px">
              <span style="padding:3px 11px;border-radius:20px;font-size:11.5px;font-weight:700;background:<?php echo e($bg); ?>;color:<?php echo e($clr); ?>"><?php echo e($label); ?></span>
              <span class="fl" style="margin:0;font-weight:500;color:var(--subtle)">Template pesan</span>
            </div>
            <textarea name="wa_template_<?php echo e($status); ?>" rows="<?php echo e($status==='ditolak'?5:4); ?>" class="fc"
              style="font-family:'DM Mono',monospace;font-size:12.5px;resize:vertical"
              placeholder="Kosongkan untuk pakai template bawaan"><?php echo e($s['wa_template_'.$status]??''); ?></textarea>
            <?php if($status==='ditolak'): ?>
              <p style="font-size:11.5px;color:#991b1b;margin-top:5px">
                <i class="bi bi-exclamation-triangle"></i> Wajib mengandung <code style="background:#fee2e2;padding:1px 5px;border-radius:3px">{catatan}</code>
              </p>
            <?php endif; ?>
          </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <div style="margin-top:18px">
          <button type="submit" class="btn btn-p"><i class="bi bi-check-lg"></i> Simpan Template</button>
        </div>
      </form>

      <div style="margin-top:20px;padding:14px;background:var(--bg);border-radius:9px;border:1px solid var(--border)">
        <div style="font-size:10.5px;font-weight:700;color:var(--subtle);margin-bottom:8px;text-transform:uppercase;letter-spacing:.08em">💡 Template Bawaan Sistem</div>
        <pre style="font-size:12px;color:var(--subtle);white-space:pre-wrap;font-family:'DM Mono',monospace;line-height:1.7">Selesai : "…Silakan ambil di Kantor Desa jam 08:00–15:00…"
Ditolak : "…Alasan: {catatan}…"
Diproses: "…sedang kami proses…"</pre>
      </div>

      <div style="margin-top:16px;padding:14px;background:#fffbeb;border-radius:9px;border:1px solid #fde68a">
        <div style="font-size:10.5px;font-weight:700;color:#92400e;margin-bottom:8px;text-transform:uppercase;letter-spacing:.08em">🚀 Cara Menjalankan WA Bridge</div>
        <pre style="font-size:12px;color:#78350f;line-height:1.9;font-family:'DM Mono',monospace;white-space:pre-wrap">1. Buka terminal / CMD baru
2. cd C:\laragon\www\desa\wa-bridge
3. npm install  (sekali saja)
4. node wa-bridge.js
5. Kembali ke halaman ini → scan QR</pre>
      </div>
    </div>
  </div>

</div>


<script>
let pollTimer = null;

function showState(state) {
  document.getElementById('state-connected').style.display = state === 'connected' ? '' : 'none';
  document.getElementById('state-qr').style.display        = state === 'qr'        ? '' : 'none';
  document.getElementById('state-error').style.display     = state === 'error'     ? '' : 'none';
}

async function pollStatus() {
  try {
    const res  = await fetch('<?php echo e(route("admin.whatsapp.qr")); ?>', {
      headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' }
    });
    const data = await res.json();

    if (data.ready) {
      const full = await fetch('<?php echo e(route("admin.whatsapp.status")); ?>', {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' }
      });
      const info = await full.json();
      document.getElementById('wa-name-display').textContent  = info.name  || '';
      document.getElementById('wa-nomor-display').textContent = info.nomor ? '+' + info.nomor : '';
      showState('connected');
      clearInterval(pollTimer);
      return;
    }

    showState('qr');
    const qrImg     = document.getElementById('qr-img');
    const qrLoading = document.getElementById('qr-loading');
    if (data.qr) {
      qrImg.src               = data.qr;
      qrImg.style.display     = '';
      qrLoading.style.display = 'none';
    } else {
      qrImg.style.display     = 'none';
      qrLoading.style.display = '';
    }
  } catch (e) {
    showState('error');
    clearInterval(pollTimer);
  }
}

pollStatus();
pollTimer = setInterval(pollStatus, 3000);
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\desa\resources\views/admin/whatsapp/index.blade.php ENDPATH**/ ?>