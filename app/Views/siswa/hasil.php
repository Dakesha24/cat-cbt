<?= $this->extend('templates/siswa/siswa_template') ?>
<?= $this->section('content') ?>

<div class="container py-5">
  <h2 class="mb-4 py-2">Riwayat Ujian</h2>

  <?php if (empty($riwayatUjian)): ?>
    <div class="alert alert-info">
      Anda belum mengikuti ujian apapun.
    </div>
  <?php else: ?>
    <div class="row g-4">
      <?php foreach ($riwayatUjian as $ujian): ?>
        <div class="col-md-6">
          <div class="card h-100 border-0 shadow-sm hover-shadow">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                  <h5 class="card-title text-primary mb-1"><?= esc($ujian['nama_ujian']) ?></h5>
                  <small class="text-muted d-block"><?= esc($ujian['nama_jenis']) ?></small>
                  <!-- TAMBAHAN: Tampilkan kode ujian -->
                  <small class="text-muted"><i class="bi bi-hash"></i> <?= esc($ujian['kode_ujian']) ?></small>
                </div>
                <span class="badge bg-success">Selesai</span>
              </div>

              <!-- Informasi Waktu -->
              <div class="mb-3">
                <div class="row text-center">
                  <div class="col-4">
                    <div class="small text-muted">Mulai</div>
                    <div class="fw-bold small"><?= $ujian['waktu_mulai_format'] ?></div>
                  </div>
                  <div class="col-4">
                    <div class="small text-muted">Selesai</div>
                    <div class="fw-bold small"><?= $ujian['waktu_selesai_format'] ?></div>
                  </div>
                  <div class="col-4">
                    <div class="small text-muted">Durasi</div>
                    <div class="fw-bold small text-primary"><?= $ujian['durasi_format'] ?></div>
                  </div>
                </div>
              </div>

              <!-- Border pemisah -->
              <hr class="my-3">

              <!-- Informasi Detail -->
              <div class="mb-3">
                <div class="row">
                  <div class="col-6">
                    <small class="text-muted d-block">
                      <i class="bi bi-list-ol"></i>
                      Soal Dikerjakan: <strong><?= $ujian['jumlah_soal'] ?></strong>
                    </small>
                  </div>
                  <div class="col-6">
                    <small class="text-muted d-block">
                      <i class="bi bi-alarm"></i>
                      Batas Waktu: <strong><?= $ujian['durasi'] ?></strong>
                    </small>
                  </div>
                </div>
              </div>

              <p class="card-text small text-muted mb-4"><?= esc($ujian['deskripsi']) ?></p>

              <div class="d-flex gap-2">
                <a href="<?= base_url('siswa/hasil/detail/' . $ujian['peserta_ujian_id']) ?>"
                  class="btn btn-outline-primary btn-sm flex-fill">
                  <i class="bi bi-eye"></i> Lihat Detail
                </a>
                <a href="<?= base_url('siswa/hasil/unduh/' . $ujian['peserta_ujian_id']) ?>"
                  class="btn btn-outline-secondary btn-sm" target="_blank">
                  <i class="bi bi-download"></i> Unduh
                </a>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<style>
  .hover-shadow {
    transition: all 0.3s ease;
  }

  .hover-shadow:hover {
    transform: translateY(-5px);
    box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
  }
</style>

<?= $this->endSection() ?>