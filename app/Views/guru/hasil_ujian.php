<?= $this->extend('templates/guru/guru_template') ?>

<?= $this->section('content') ?>
<br><br>
<div class="container-fluid py-4">
  <div class="row mb-4 align-items-center">
    <div class="col">
      <h2 class="fw-bold text-primary">Hasil Ujian</h2>
      <p class="text-muted">Daftar hasil ujian siswa untuk kelas yang Anda ajar</p>
    </div>
  </div>

  <!-- Alert Messages -->
  <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="bi bi-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <i class="bi bi-exclamation-circle me-2"></i><?= session()->getFlashdata('error') ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <!-- Daftar Hasil Ujian -->
  <div class="row g-4">
    <?php if (!empty($daftarUjian)): ?>
      <?php foreach ($daftarUjian as $ujian): ?>
        <?php
        $statusColor = 'secondary';
        $statusText = str_replace('_', ' ', $ujian['status']);
        if ($ujian['status'] == 'sedang_berlangsung') {
          $statusColor = 'success';
        } elseif ($ujian['status'] == 'selesai') {
          $statusColor = 'primary';
        }
        ?>
        <div class="col-lg-6 col-xl-4">
          <div class="card h-100 shadow-sm hover-card">
            <div class="card-body p-4 d-flex flex-column">
              <div class="d-flex align-items-start justify-content-between mb-3">
                <div class="d-flex align-items-center">
                  <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px;">
                    <i class="bi bi-clipboard-data text-primary fs-5"></i>
                  </div>
                  <div>
                    <span class="badge bg-light text-dark small mb-1">
                      <?= esc($ujian['nama_jenis']) ?>
                    </span>
                    <br><span class="badge bg-<?= $statusColor ?> small">
                      <?= ucwords($statusText) ?>
                    </span>
                  </div>
                </div>
                <div class="text-end">
                  <span class="badge bg-info text-white">
                    <i class="bi bi-people me-1"></i><?= esc($ujian['nama_kelas']) ?>
                  </span>
                </div>
              </div>

              <div class="flex-grow-1">
                <h5 class="card-title fw-bold mb-2"><?= esc($ujian['nama_ujian']) ?></h5>
                <p class="text-muted small mb-1">
                  <i class="bi bi-code-square me-1"></i>Kode: <?= esc($ujian['kode_ujian']) ?>
                </p>
                <p class="card-text text-muted small mb-3"><?= esc($ujian['deskripsi']) ?></p>

                <!-- Informasi Waktu -->
                <div class="mb-3">
                  <div class="text-muted small">
                    <div class="d-flex justify-content-between mb-1">
                      <span><i class="bi bi-calendar me-1"></i>Mulai:</span>
                      <span><?= $ujian['tanggal_mulai_format'] ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                      <span><i class="bi bi-calendar-check me-1"></i>Selesai:</span>
                      <span><?= $ujian['tanggal_selesai_format'] ?></span>
                    </div>
                  </div>
                </div>

                <!-- Statistik Peserta dan Waktu -->
                <div class="row g-2 text-center mb-3">
                  <div class="col-6">
                    <div class="bg-light rounded p-2">
                      <div class="fw-bold text-dark"><?= $ujian['jumlah_peserta'] ?></div>
                      <small class="text-muted">Peserta Selesai</small>
                    </div>
                  </div>
                  <div class="col-6">
                    <div class="bg-light rounded p-2">
                      <div class="fw-bold text-dark small"><?= $ujian['rata_rata_durasi_format'] ?></div>
                      <small class="text-muted">Rata-rata Durasi</small>
                    </div>
                  </div>
                </div>

                <!-- Info Waktu Tercepat dan Terlama -->
                <?php if ($ujian['jumlah_peserta'] > 0): ?>
                  <div class="mb-3">
                    <div class="text-muted small">
                      <div class="d-flex justify-content-between">
                        <span><i class="bi bi-lightning me-1"></i>Tercepat:</span>
                        <span><?= $ujian['durasi_tercepat_format'] ?></span>
                      </div>
                      <div class="d-flex justify-content-between">
                        <span><i class="bi bi-hourglass me-1"></i>Terlama:</span>
                        <span><?= $ujian['durasi_terlama_format'] ?></span>
                      </div>
                    </div>
                  </div>
                <?php endif; ?>
              </div>

              <div class="mt-auto">
                <?php if ($ujian['jumlah_peserta'] > 0): ?>
                  <a href="<?= base_url('guru/hasil-ujian/siswa/' . $ujian['jadwal_id']) ?>"
                    class="btn btn-outline-primary btn-sm w-100">
                    <i class="bi bi-eye me-2"></i>Lihat Hasil (<?= $ujian['jumlah_peserta'] ?> siswa)
                  </a>
                <?php else: ?>
                  <button class="btn btn-outline-secondary btn-sm w-100" disabled>
                    <i class="bi bi-hourglass me-2"></i>Belum ada hasil
                  </button>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="col-12">
        <div class="card shadow-sm">
          <div class="card-body text-center py-5">
            <div class="mb-3">
              <i class="bi bi-clipboard-x text-muted" style="font-size: 4rem;"></i>
            </div>
            <h5 class="text-muted">Belum ada hasil ujian</h5>
            <p class="text-muted">Hasil ujian akan muncul setelah siswa menyelesaikan ujian di kelas yang Anda ajar</p>
            <a href="<?= base_url('guru/jadwal-ujian') ?>" class="btn btn-outline-primary">
              <i class="bi bi-calendar-plus me-2"></i>Buat Jadwal Ujian
            </a>
          </div>
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>

<style>
  .hover-card {
    transition: all 0.3s ease;
    border: none;
    min-height: 380px;
  }

  .hover-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
  }

  .hover-card .card-body {
    display: flex;
    flex-direction: column;
    height: 100%;
  }

  .badge {
    font-size: 0.75rem;
  }

  .card-title {
    line-height: 1.3;
    min-height: 2.6em;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }

  .card-text {
    min-height: 3em;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }

  .text-dark {
    color: #212529 !important;
  }
</style>

<?= $this->endSection() ?>