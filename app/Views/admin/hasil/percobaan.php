<?= $this->extend('templates/admin/admin_template') ?>
<?= $this->section('title') ?>Daftar Percobaan<?= $this->endSection() ?>
<?= $this->section('content') ?>

<style>
.hs-wrap { padding:72px 0 48px; background:#f4f6f9; min-height:100vh; }
.hs-head { display:flex; flex-wrap:wrap; justify-content:space-between; align-items:flex-start; gap:1rem; margin-bottom:1.25rem; }
.hs-title { font-size:1.05rem; font-weight:700; color:#0f172a; margin:0 0 .2rem; }
.hs-meta { font-size:.8rem; color:#6b7280; }
.hs-btn { display:inline-flex; align-items:center; gap:.3rem; font-size:.8rem; font-weight:500; padding:.35rem .75rem; border-radius:6px; border:1px solid; cursor:pointer; text-decoration:none; background:transparent; line-height:1.4; white-space:nowrap; transition:background .12s; }
.hs-btn-primary { border-color:#bfdbfe; color:#1d4ed8; } .hs-btn-primary:hover { background:#eff6ff; }
.hs-btn-success { border-color:#bbf7d0; color:#15803d; } .hs-btn-success:hover { background:#f0fdf4; }
.hs-btn-danger  { border-color:#fca5a5; color:#b91c1c; } .hs-btn-danger:hover  { background:#fef2f2; }
.hs-btn-secondary { border-color:#d1d5db; color:#374151; } .hs-btn-secondary:hover { background:#f9fafb; }
.hs-stats { display:grid; grid-template-columns:repeat(4,1fr); border:1px solid #e2e8f0; border-radius:8px; overflow:hidden; margin-bottom:1.25rem; }
.hs-stat { background:#fff; padding:.9rem 1.1rem; border-right:1px solid #e2e8f0; }
.hs-stat:last-child { border-right:none; }
.hs-stat-num { font-size:1.5rem; font-weight:700; line-height:1; color:#0f172a; }
.hs-stat-lbl { font-size:.68rem; color:#9ca3af; text-transform:uppercase; letter-spacing:.05em; margin-top:.2rem; }
.hs-card { background:#fff; border:1px solid #e2e8f0; border-radius:8px; overflow:hidden; margin-bottom:1rem; }
.hs-card-hd { display:flex; justify-content:space-between; align-items:center; gap:12px; padding:.85rem 1.25rem; border-bottom:1px solid #f1f5f9; }
.hs-card-title { font-size:.88rem; font-weight:600; color:#0f172a; }
.hs-table { width:100%; border-collapse:collapse; font-size:.875rem; }
.hs-table thead th { background:#f8fafc; color:#64748b; font-size:.75rem; font-weight:600; text-transform:uppercase; letter-spacing:.04em; padding:10px 14px; border-bottom:2px solid #e2e8f0; white-space:nowrap; }
.hs-table tbody td { padding:11px 14px; border-bottom:1px solid #f1f5f9; color:#374151; vertical-align:middle; }
.hs-table tbody tr:last-child td { border-bottom:none; }
.hs-table tbody tr:hover td { background:#fafbfc; }
.hs-num { color:#9ca3af; font-size:.8rem; text-align:center; width:40px; }
.hs-mono { font-family:monospace; font-weight:600; color:#0f172a; }
.hs-muted { color:#9ca3af; font-size:.78rem; margin-top:2px; }
.hs-act { display:flex; flex-wrap:wrap; gap:5px; }
.type-cat { background:#dbeafe; color:#1e40af; font-size:.68rem; font-weight:700; padding:2px 7px; border-radius:4px; }
.type-cbt { background:#d1fae5; color:#065f46; font-size:.68rem; font-weight:700; padding:2px 7px; border-radius:4px; }
</style>

<div class="hs-wrap">
<div class="container-fluid px-3 px-md-4">

  <?php foreach (['success','error'] as $t): ?>
    <?php if ($msg = session()->getFlashdata($t)): ?>
      <div class="alert alert-<?= $t ?> alert-dismissible fade show border-0 mb-3 rounded-2" role="alert">
        <?= $msg ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>
  <?php endforeach; ?>

  <!-- Header -->
  <div class="hs-head">
    <div>
      <div class="hs-title">
        <?php $isCbt = ($peserta['tipe_ujian'] ?? 'CAT') === 'CBT'; ?>
        <span class="<?= $isCbt ? 'type-cbt' : 'type-cat' ?>" style="vertical-align:middle;margin-right:6px"><?= $isCbt ? 'CBT' : 'CAT' ?></span>
        Daftar Percobaan
      </div>
      <div class="hs-meta">
        <?= esc($peserta['nama_lengkap']) ?>
        &middot; <?= esc($peserta['nama_ujian']) ?>
        &middot; Kode: <code><?= esc($peserta['kode_ujian']) ?></code>
      </div>
    </div>
    <a href="<?= base_url('admin/hasil-ujian/siswa/' . $peserta['jadwal_id']) ?>" class="hs-btn hs-btn-secondary">
      <i class="fas fa-arrow-left"></i>Kembali
    </a>
  </div>

  <!-- Info Siswa -->
  <div class="hs-stats">
    <div class="hs-stat">
      <div class="hs-stat-num" style="font-size:1rem;font-weight:600"><?= esc($peserta['nama_lengkap']) ?></div>
      <div class="hs-stat-lbl">Siswa</div>
    </div>
    <div class="hs-stat">
      <div class="hs-stat-num" style="font-size:1rem;font-family:monospace"><?= esc($peserta['nomor_peserta']) ?></div>
      <div class="hs-stat-lbl">No. Peserta</div>
    </div>
    <div class="hs-stat">
      <div class="hs-stat-num" style="font-size:1rem"><?= esc($peserta['nama_kelas']) ?></div>
      <div class="hs-stat-lbl">Kelas</div>
    </div>
    <div class="hs-stat">
      <div class="hs-stat-num"><?= count($attempts) ?></div>
      <div class="hs-stat-lbl">Total Percobaan</div>
    </div>
  </div>

  <!-- Tabel Percobaan -->
  <div class="hs-card">
    <div class="hs-card-hd">
      <span class="hs-card-title">Percobaan Tersimpan</span>
    </div>
    <div class="table-responsive">
      <table class="hs-table">
        <thead>
          <tr>
            <th class="hs-num text-center">#</th>
            <th>Waktu Mulai</th>
            <th>Waktu Selesai</th>
            <th>Durasi</th>
            <th>Benar / Total</th>
            <th>Hasil</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($attempts as $idx => $attempt): ?>
            <tr>
              <td class="hs-num text-center">
                <span style="font-size:.72rem;font-weight:700;color:#9ca3af">P<?= (int)$attempt['nomor_attempt'] ?></span>
              </td>
              <td>
                <span class="hs-mono" style="font-size:.82rem"><?= esc($attempt['waktu_mulai_format']) ?></span>
              </td>
              <td>
                <span class="hs-mono" style="font-size:.82rem"><?= esc($attempt['waktu_selesai_format']) ?></span>
              </td>
              <td>
                <span class="hs-mono"><?= esc($attempt['durasi_format']) ?></span>
              </td>
              <td>
                <span style="font-weight:600;color:#0f172a"><?= $attempt['jawaban_benar'] ?></span>
                <span style="color:#9ca3af"> / <?= $attempt['total_soal'] ?></span>
              </td>
              <td>
                <?php if (!empty($attempt['is_cat_mode'])): ?>
                  <div style="font-weight:700;color:#0f172a"><?= number_format((float)$attempt['skor'], 1) ?></div>
                  <div class="hs-muted">θ <?= number_format((float)$attempt['theta_akhir'], 3) ?> &bull; SE <?= number_format((float)$attempt['se_akhir'], 3) ?></div>
                <?php else: ?>
                  <div style="font-weight:700;color:#0f172a"><?= number_format((float)$attempt['nilai'], 2) ?></div>
                  <?php if (($attempt['theta_akhir'] ?? null) !== null): ?>
                    <div class="hs-muted">θ_EAP <?= number_format((float)$attempt['theta_akhir'], 3) ?> &bull; SEM <?= number_format((float)$attempt['se_akhir'], 3) ?></div>
                  <?php endif; ?>
                <?php endif; ?>
              </td>
              <td>
                <div class="hs-act">
                  <a href="<?= base_url('admin/hasil-ujian/detail/' . $peserta['peserta_ujian_id']) . '?attempt_id=' . $attempt['attempt_id'] ?>"
                     class="hs-btn hs-btn-primary">
                    <i class="fas fa-eye"></i>Detail
                  </a>
                  <a href="<?= base_url('admin/hasil-ujian/download-excel/' . $peserta['peserta_ujian_id']) . '?attempt_id=' . $attempt['attempt_id'] ?>"
                     class="hs-btn hs-btn-success">
                    <i class="fas fa-file-excel"></i>Excel
                  </a>
                  <a href="<?= base_url('admin/hasil-ujian/download-pdf/' . $peserta['peserta_ujian_id']) . '?attempt_id=' . $attempt['attempt_id'] ?>"
                     class="hs-btn hs-btn-danger" target="_blank">
                    <i class="fas fa-file-pdf"></i>PDF
                  </a>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>
</div>

<?= $this->endSection() ?>
