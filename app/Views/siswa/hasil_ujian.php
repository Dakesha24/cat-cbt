<?= $this->extend('templates/siswa/siswa_template') ?>
<?= $this->section('content') ?>

<div class="container py-4">
  <nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb small mb-0">
      <li class="breadcrumb-item"><a href="<?= base_url('siswa/hasil') ?>">Riwayat Ujian</a></li>
      <li class="breadcrumb-item active" aria-current="page">Daftar Percobaan</li>
    </ol>
  </nav>

  <div class="d-flex justify-content-between align-items-center mb-4 py-4">
    <div>
      <div class="small text-muted mb-1">Daftar Percobaan</div>
      <h2 class="mb-1"><?= esc($ujian['nama_ujian']) ?></h2>
      <div class="text-muted small"><?= esc($ujian['nama_jenis']) ?> | <?= esc($ujian['kode_ujian']) ?></div>
    </div>
    <a href="<?= base_url('siswa/hasil') ?>" class="btn btn-outline-secondary btn-sm">
      <i class="bi bi-arrow-left"></i> Kembali
    </a>
  </div>

  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
      <div class="row g-3 align-items-start">
        <div class="col-md-8">
          <?php if (!empty($ujian['deskripsi'])): ?>
            <p class="text-muted mb-0"><?= esc($ujian['deskripsi']) ?></p>
          <?php else: ?>
            <p class="text-muted mb-0">Daftar percobaan untuk ujian ini.</p>
          <?php endif; ?>
        </div>
        <div class="col-md-4">
          <div class="small text-muted">Batas waktu</div>
          <div class="fw-semibold"><?= esc($ujian['durasi']) ?></div>
          <div class="small text-muted mt-2">Total percobaan</div>
          <div class="fw-semibold"><?= count($attempts) ?></div>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-3">
    <?php foreach ($attempts as $attempt): ?>
      <div class="col-md-6 col-xl-4">
        <div class="card border-0 shadow-sm h-100 hover-shadow">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-3">
              <span class="badge bg-primary">Percobaan <?= esc($attempt['nomor_attempt']) ?></span>
              <span class="badge bg-success">Selesai</span>
            </div>

            <div class="small text-muted mb-2">
              <?= $attempt['jumlah_soal'] ?> soal | <?= $attempt['durasi_format'] ?>
            </div>

            <div class="mb-3">
              <div class="small text-muted">Hasil</div>
              <div class="fw-bold text-primary">
                <?php if (($ujian['tipe_ujian'] ?? 'CAT') === 'CBT'): ?>
                  Nilai <?= number_format((float) $attempt['nilai_tampil'], 2) ?>
                <?php else: ?>
                  Skor <?= number_format((float) $attempt['nilai_tampil'], 2) ?>
                <?php endif; ?>
              </div>
            </div>

            <div class="mb-3">
              <div class="small text-muted">Mulai</div>
              <div class="small fw-semibold"><?= $attempt['waktu_mulai_format'] ?></div>
            </div>

            <div class="mb-4">
              <div class="small text-muted">Selesai</div>
              <div class="small fw-semibold"><?= $attempt['waktu_selesai_format'] ?></div>
            </div>

            <div class="d-flex gap-2">
              <a href="<?= base_url('siswa/hasil/detail/' . $attempt['attempt_id']) ?>" class="btn btn-outline-primary btn-sm flex-fill">
                <i class="bi bi-eye"></i> Detail
              </a>
              <a href="<?= base_url('siswa/hasil/unduh/' . $attempt['attempt_id']) ?>" class="btn btn-outline-secondary btn-sm" target="_blank">
                <i class="bi bi-download"></i> Unduh
              </a>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<style>
  .hover-shadow {
    transition: all 0.2s ease;
  }

  .hover-shadow:hover {
    box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .12) !important;
  }
</style>

<?= $this->endSection() ?>
