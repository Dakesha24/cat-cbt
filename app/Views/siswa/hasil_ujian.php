<?= $this->extend('templates/siswa/siswa_template') ?>
<?= $this->section('content') ?>

<style>
.hs-wrap { padding:72px 0 48px; background:#f4f6f9; min-height:100vh; }
.hs-head { display:flex; flex-wrap:wrap; justify-content:space-between; align-items:flex-start; gap:1rem; margin-bottom:1.25rem; }
.hs-title { font-size:1.05rem; font-weight:700; color:#0f172a; margin:0 0 .2rem; }
.hs-meta { font-size:.8rem; color:#6b7280; }
.hs-btn { display:inline-flex; align-items:center; gap:.3rem; font-size:.82rem; font-weight:500; padding:.35rem .75rem; border-radius:6px; border:1px solid; text-decoration:none; background:transparent; line-height:1.4; white-space:nowrap; transition:background .12s; }
.hs-btn-primary { border-color:#bfdbfe; color:#1d4ed8; } .hs-btn-primary:hover { background:#eff6ff; }
.hs-btn-secondary { border-color:#d1d5db; color:#374151; } .hs-btn-secondary:hover { background:#f9fafb; }
.hs-btn-sm-success { border-color:#bbf7d0; color:#15803d; } .hs-btn-sm-success:hover { background:#f0fdf4; }

.hs-card { background:#fff; border:1px solid #e2e8f0; border-radius:8px; overflow:hidden; margin-bottom:1rem; }
.hs-card-body { padding:16px 18px; }

.type-cat { background:#dbeafe; color:#1e40af; font-size:.68rem; font-weight:700; padding:2px 7px; border-radius:4px; }
.type-cbt { background:#d1fae5; color:#065f46; font-size:.68rem; font-weight:700; padding:2px 7px; border-radius:4px; }

.att-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(270px,1fr)); gap:12px; }
.att-card { background:#fff; border:1px solid #e2e8f0; border-radius:8px; overflow:hidden; display:flex; flex-direction:column; transition:box-shadow .15s; }
.att-card:hover { box-shadow:0 4px 16px rgba(15,23,42,.08); }
.att-card-body { padding:16px 18px; flex:1; }
.att-num { font-size:.68rem; font-weight:700; padding:2px 8px; border-radius:4px; background:#f1f5f9; color:#475569; display:inline-block; margin-bottom:10px; }
.att-val { font-size:1.4rem; font-weight:700; color:#0f172a; line-height:1; }
.att-lbl { font-size:.72rem; color:#9ca3af; margin-top:3px; text-transform:uppercase; letter-spacing:.04em; }
.att-info { display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-top:12px; }
.att-info-item .lbl { font-size:.7rem; color:#9ca3af; }
.att-info-item .val { font-size:.82rem; font-weight:600; color:#374151; }
.att-card-foot { padding:12px 18px; border-top:1px solid #f1f5f9; display:flex; gap:8px; }
</style>

<div class="hs-wrap">
<div class="container px-3">

  <nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb small mb-0">
      <li class="breadcrumb-item"><a href="<?= base_url('siswa/hasil') ?>">Riwayat Ujian</a></li>
      <li class="breadcrumb-item active">Daftar Percobaan</li>
    </ol>
  </nav>

  <div class="hs-head">
    <div>
      <?php $isCbt = ($ujian['tipe_ujian'] ?? 'CAT') === 'CBT'; ?>
      <div class="hs-title">
        <span class="<?= $isCbt ? 'type-cbt' : 'type-cat' ?>" style="vertical-align:middle;margin-right:6px"><?= $isCbt ? 'CBT' : 'CAT' ?></span>
        <?= esc($ujian['nama_ujian']) ?>
      </div>
      <div class="hs-meta">
        <?= esc($ujian['nama_jenis']) ?>
        &bull; <code style="font-size:.78rem"><?= esc($ujian['kode_ujian']) ?></code>
        &bull; Durasi: <?= esc($ujian['durasi']) ?> menit
      </div>
    </div>
    <a href="<?= base_url('siswa/hasil') ?>" class="hs-btn hs-btn-secondary">
      <i class="bi bi-arrow-left"></i>Kembali
    </a>
  </div>

  <?php if (!empty($ujian['deskripsi'])): ?>
    <div class="hs-card mb-3">
      <div class="hs-card-body" style="font-size:.85rem;color:#6b7280"><?= esc($ujian['deskripsi']) ?></div>
    </div>
  <?php endif; ?>

  <div class="att-grid">
    <?php foreach ($attempts as $attempt): ?>
      <div class="att-card">
        <div class="att-card-body">
          <span class="att-num">Percobaan <?= esc($attempt['nomor_attempt']) ?></span>

          <div class="att-val">
            <?php if ($isCbt): ?>
              <?= number_format((float)$attempt['nilai_tampil'], 2) ?>
            <?php else: ?>
              <?= number_format((float)$attempt['nilai_tampil'], 2) ?>
            <?php endif; ?>
          </div>
          <div class="att-lbl"><?= $isCbt ? 'Nilai EAP' : 'Skor Kognitif' ?></div>

          <?php if ($isCbt && !empty($attempt['theta_akhir'])): ?>
            <div style="font-size:.72rem;color:#9ca3af;margin-top:4px;font-family:monospace">
              θ <?= number_format((float)$attempt['theta_akhir'], 4) ?>
              &nbsp;|&nbsp; SEM <?= number_format((float)($attempt['sem_akhir'] ?? 0), 4) ?>
            </div>
          <?php endif; ?>

          <div class="att-info">
            <div class="att-info-item">
              <div class="lbl">Soal</div>
              <div class="val"><?= $attempt['jumlah_soal'] ?></div>
            </div>
            <div class="att-info-item">
              <div class="lbl">Durasi</div>
              <div class="val" style="font-family:monospace"><?= $attempt['durasi_format'] ?></div>
            </div>
            <div class="att-info-item">
              <div class="lbl">Mulai</div>
              <div class="val"><?= $attempt['waktu_mulai_format'] ?></div>
            </div>
            <div class="att-info-item">
              <div class="lbl">Selesai</div>
              <div class="val"><?= $attempt['waktu_selesai_format'] ?></div>
            </div>
          </div>
        </div>
        <div class="att-card-foot">
          <a href="<?= base_url('siswa/hasil/detail/' . $attempt['attempt_id']) ?>"
             class="hs-btn hs-btn-primary" style="flex:1;justify-content:center">
            <i class="bi bi-eye"></i>Detail
          </a>
          <a href="<?= base_url('siswa/hasil/unduh/' . $attempt['attempt_id']) ?>"
             class="hs-btn hs-btn-secondary" target="_blank">
            <i class="bi bi-download"></i>
          </a>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

</div>
</div>

<?= $this->endSection() ?>
