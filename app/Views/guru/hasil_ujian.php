<?= $this->extend('templates/guru/guru_template') ?>

<?= $this->section('title') ?>Hasil Ujian<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php
$ujianCAT = array_filter($daftarUjian ?? [], fn($u) => ($u['tipe_ujian'] ?? 'CAT') === 'CAT');
$ujianCBT = array_filter($daftarUjian ?? [], fn($u) => ($u['tipe_ujian'] ?? '') === 'CBT');
?>

<style>
.exam-page { background: linear-gradient(180deg, #f8fbff 0%, #ffffff 100%); border: 1px solid #e9eef5; }
.page-header-card { background: linear-gradient(135deg, #ffffff 0%, #f2f7ff 100%); border: 1px solid #dbe7ff; }
.exam-card { border-radius: 0; overflow: hidden; border: 1px solid #e9ecef; border-left: 4px solid #dee2e6; transition: transform 0.18s ease, box-shadow 0.2s ease; }
.exam-card.cat-card { border-left-color: #0d6efd; }
.exam-card.cbt-card { border-left-color: #198754; }
.exam-card:hover { transform: translateY(-2px); box-shadow: 0 12px 28px rgba(15, 23, 42, 0.10) !important; }
.stat-box { background: linear-gradient(180deg, #f8f9fa 0%, #f1f3f5 100%); padding: 0.6rem 0.5rem; text-align: center; border: 1px solid #e9ecef; }
.stat-box .stat-val { font-size: 0.8rem; font-weight: 700; color: #212529; line-height: 1.25; min-height: 2.4em; display: flex; align-items: center; justify-content: center; text-wrap: balance; }
.stat-box .stat-lbl { font-size: 0.68rem; color: #9ca3af; }
.exam-meta-pill { display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.28rem 0.6rem; border-radius: 999px; font-size: 0.72rem; font-weight: 600; line-height: 1; border: 1px solid transparent; }
.exam-meta-pill.classroom { background: #eefbf3; border-color: #ccebd7; color: #137547; }
.exam-meta-pill.subject { background: #f8f9fa; border-color: #e5e7eb; color: #495057; }
.exam-code { color: #6b7280; font-size: 0.74rem; }
.exam-code i { color: #9ca3af; }
.line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.section-hd { display: flex; align-items: center; gap: 0.6rem; margin-bottom: 1rem; margin-top: 1.25rem; }
.section-hd .s-badge { font-size: 0.7rem; font-weight: 700; padding: 0.2em 0.55em; border-radius: 3px; letter-spacing: 0.04em; }
.section-hd .s-title { font-size: 0.9rem; font-weight: 600; color: #343a40; }
.section-hd .s-count { font-size: 0.8rem; color: #9ca3af; }
.section-hd .s-line { flex: 1; border-bottom: 1px solid #e9ecef; margin-left: 0.25rem; }
.empty-sec { background: #f8f9fa; padding: 1.5rem; text-align: center; color: #adb5bd; font-size: 0.85rem; margin-bottom: 1rem; }
@media (max-width: 767.98px) {
  .exam-page { padding: 1rem !important; }
  .page-header-card { padding: 1rem !important; }
  .section-hd { align-items: flex-start; flex-wrap: wrap; }
  .section-hd .s-line { display: none; }
}
</style>

<br><br><br>
<div class="container-fluid py-4">
  <div class="exam-page shadow-sm px-3 px-md-4 py-4">
    <div class="page-header-card d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 px-3 px-md-4 py-3 shadow-sm">
      <div>
        <h2 class="fw-bold text-dark mb-1">Hasil Ujian</h2>
        <p class="text-muted mb-0">Lihat hasil dan progres ujian untuk kelas yang Anda ajar</p>
      </div>
      <div class="d-flex gap-2 flex-wrap">
        <a href="<?= base_url('guru/jadwal-ujian') ?>" class="btn btn-outline-primary shadow-sm">
          <i class="bi bi-calendar3 me-2"></i>Jadwal Ujian
        </a>
        <a href="<?= base_url('guru/ujian') ?>" class="btn btn-primary shadow-sm">
          <i class="bi bi-journal-text me-2"></i>Kelola Ujian
        </a>
      </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
      <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i><?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
      <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i><?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <?php if (empty($daftarUjian)): ?>
      <div class="text-center py-5">
        <div class="mb-3"><i class="bi bi-clipboard-x text-muted" style="font-size:4rem"></i></div>
        <h5 class="text-muted">Belum ada hasil ujian</h5>
        <p class="text-muted mb-3">Hasil akan muncul setelah siswa menyelesaikan ujian</p>
      </div>
    <?php else: ?>

      <div class="section-hd">
        <span class="s-badge bg-primary text-white">CAT</span>
        <span class="s-title">Computer Adaptive Test</span>
        <span class="s-count"><?= count($ujianCAT) ?> ujian</span>
        <div class="s-line"></div>
      </div>

      <?php if (!empty($ujianCAT)): ?>
        <div class="row g-3 mb-4">
          <?php foreach ($ujianCAT as $u): ?>
            <div class="col-xl-4 col-lg-6">
              <div class="card border-0 shadow-sm h-100 exam-card cat-card">
                <div class="card-body d-flex flex-column p-0">
                  <div class="d-flex align-items-center justify-content-between px-4 pt-3 pb-2">
                    <span class="badge bg-primary bg-opacity-10 text-primary px-2 py-1" style="font-size:0.72rem;font-weight:700;border-radius:3px">CAT Adaptif</span>
                    <span class="badge bg-<?= $u['status'] === 'selesai' ? 'success' : ($u['status'] === 'sedang_berlangsung' ? 'warning text-dark' : 'secondary') ?>">
                      <?= esc(ucwords(str_replace('_', ' ', $u['status']))) ?>
                    </span>
                  </div>
                  <div class="px-4 pb-3 flex-grow-1">
                    <h5 class="fw-bold mb-1" style="font-size:1rem"><?= esc($u['nama_ujian']) ?></h5>
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                      <span class="exam-meta-pill subject"><?= esc($u['nama_jenis'] ?? 'Mata Pelajaran') ?></span>
                      <span class="exam-meta-pill classroom"><i class="bi bi-mortarboard"></i><?= esc($u['nama_kelas'] ?? '-') ?></span>
                      <?php if (!empty($u['kode_ujian'])): ?>
                        <small class="exam-code"><i class="bi bi-key me-1"></i><?= esc($u['kode_ujian']) ?></small>
                      <?php endif; ?>
                    </div>
                    <p class="text-muted small mb-3 line-clamp-2"><?= esc($u['deskripsi'] ?? '') ?></p>
                    <div class="row g-2 text-center mb-3">
                      <div class="col-4"><div class="stat-box"><div class="stat-val"><?= esc($u['jumlah_peserta']) ?></div><div class="stat-lbl">Peserta</div></div></div>
                      <div class="col-4"><div class="stat-box"><div class="stat-val"><?= esc($u['rata_rata_durasi_format'] ?? '-') ?></div><div class="stat-lbl">Rata-rata</div></div></div>
                      <div class="col-4"><div class="stat-box"><div class="stat-val"><?= esc($u['durasi_tercepat_format'] ?? '-') ?></div><div class="stat-lbl">Tercepat</div></div></div>
                    </div>
                  </div>
                  <div class="px-4 pb-4 mt-auto">
                    <a href="<?= base_url('guru/hasil-ujian/siswa/' . $u['jadwal_id']) ?>" class="btn btn-outline-primary w-100" style="border-radius:0">
                      <i class="bi bi-eye me-2"></i>Lihat Hasil
                    </a>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div class="empty-sec mb-4"><i class="bi bi-inbox me-2"></i>Belum ada hasil ujian CAT</div>
      <?php endif; ?>

      <div class="section-hd">
        <span class="s-badge bg-success text-white">CBT</span>
        <span class="s-title">Computer Based Test</span>
        <span class="s-count"><?= count($ujianCBT) ?> ujian</span>
        <div class="s-line"></div>
      </div>

      <?php if (!empty($ujianCBT)): ?>
        <div class="row g-3">
          <?php foreach ($ujianCBT as $u): ?>
            <div class="col-xl-4 col-lg-6">
              <div class="card border-0 shadow-sm h-100 exam-card cbt-card">
                <div class="card-body d-flex flex-column p-0">
                  <div class="d-flex align-items-center justify-content-between px-4 pt-3 pb-2">
                    <span class="badge bg-success bg-opacity-10 text-success px-2 py-1" style="font-size:0.72rem;font-weight:700;border-radius:3px">CBT</span>
                    <span class="badge bg-<?= $u['status'] === 'selesai' ? 'success' : ($u['status'] === 'sedang_berlangsung' ? 'warning text-dark' : 'secondary') ?>">
                      <?= esc(ucwords(str_replace('_', ' ', $u['status']))) ?>
                    </span>
                  </div>
                  <div class="px-4 pb-3 flex-grow-1">
                    <h5 class="fw-bold mb-1" style="font-size:1rem"><?= esc($u['nama_ujian']) ?></h5>
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                      <span class="exam-meta-pill subject"><?= esc($u['nama_jenis'] ?? 'Mata Pelajaran') ?></span>
                      <span class="exam-meta-pill classroom"><i class="bi bi-mortarboard"></i><?= esc($u['nama_kelas'] ?? '-') ?></span>
                      <?php if (!empty($u['kode_ujian'])): ?>
                        <small class="exam-code"><i class="bi bi-key me-1"></i><?= esc($u['kode_ujian']) ?></small>
                      <?php endif; ?>
                    </div>
                    <p class="text-muted small mb-3 line-clamp-2"><?= esc($u['deskripsi'] ?? '') ?></p>
                    <div class="row g-2 text-center mb-3">
                      <div class="col-4"><div class="stat-box"><div class="stat-val"><?= esc($u['jumlah_peserta']) ?></div><div class="stat-lbl">Peserta</div></div></div>
                      <div class="col-4"><div class="stat-box"><div class="stat-val"><?= esc($u['rata_rata_durasi_format'] ?? '-') ?></div><div class="stat-lbl">Rata-rata</div></div></div>
                      <div class="col-4"><div class="stat-box"><div class="stat-val"><?= esc($u['durasi_terlama_format'] ?? '-') ?></div><div class="stat-lbl">Terlama</div></div></div>
                    </div>
                  </div>
                  <div class="px-4 pb-4 mt-auto">
                    <a href="<?= base_url('guru/hasil-ujian/siswa/' . $u['jadwal_id']) ?>" class="btn btn-outline-success w-100" style="border-radius:0">
                      <i class="bi bi-eye me-2"></i>Lihat Hasil
                    </a>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div class="empty-sec"><i class="bi bi-inbox me-2"></i>Belum ada hasil ujian CBT</div>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</div>

<?= $this->endSection() ?>
