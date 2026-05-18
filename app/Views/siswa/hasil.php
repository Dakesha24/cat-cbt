<?= $this->extend('templates/siswa/siswa_template') ?>
<?= $this->section('content') ?>

<div class="container py-5">
  <h2 class="mb-4 py-2">Riwayat Ujian</h2>

  <?php if (empty($riwayatUjian)): ?>
    <div class="alert alert-info">
      Anda belum mengikuti ujian apapun.
    </div>
  <?php else: ?>
    <div class="row g-3">
      <?php foreach ($riwayatUjian as $ujian): ?>
        <div class="col-md-6 col-xl-4">
          <div class="card border-0 shadow-sm hover-shadow">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start gap-3 mb-2">
                <div>
                  <h6 class="card-title text-primary mb-1"><?= esc($ujian['nama_ujian']) ?></h6>
                  <div class="small text-muted">
                    <?= esc($ujian['nama_jenis']) ?> | <?= esc($ujian['kode_ujian']) ?>
                  </div>
                </div>
                <div class="text-end">
                  <div class="small text-muted">Percobaan</div>
                  <div class="fw-semibold"><?= $ujian['jumlah_attempt'] ?></div>
                </div>
              </div>

              <div class="exam-meta mb-3">
                <span class="exam-meta-item">Batas waktu: <strong><?= esc($ujian['durasi']) ?></strong></span>
                <span class="exam-meta-item">Total percobaan: <strong><?= $ujian['jumlah_attempt'] ?></strong></span>
              </div>

              <?php if (!empty($ujian['deskripsi'])): ?>
                <p class="card-text small text-muted mb-3 exam-desc"><?= esc($ujian['deskripsi']) ?></p>
              <?php endif; ?>

              <div class="result-summary mb-3">
                <?php $attemptTerakhir = $ujian['attempt_terakhir']; ?>
                <div class="small text-muted">Percobaan terakhir</div>
                <div class="small fw-semibold mb-1">Percobaan <?= esc($attemptTerakhir['nomor_attempt'] ?? 0) ?></div>
                <div class="small text-muted">
                  <?php if (($ujian['tipe_ujian'] ?? 'CAT') === 'CBT'): ?>
                    Nilai terakhir:
                    <strong><?= number_format((float) ($attemptTerakhir['nilai_akhir'] ?? 0), 2) ?></strong>
                  <?php else: ?>
                    Skor terakhir:
                    <strong><?= number_format(50 + (16.67 * (float) ($attemptTerakhir['nilai_akhir'] ?? 0)), 2) ?></strong>
                  <?php endif; ?>
                </div>
                <div class="small text-muted">
                  Selesai: <?= esc($attemptTerakhir['waktu_selesai_format'] ?? '-') ?>
                </div>
              </div>

              <a href="<?= base_url('siswa/hasil/ujian/' . $ujian['peserta_ujian_id']) ?>" class="btn btn-outline-primary btn-sm w-100">
                <i class="bi bi-list-ul"></i> Lihat Percobaan
              </a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<style>
  .hover-shadow {
    transition: all 0.2s ease;
  }

  .hover-shadow:hover {
    box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .12) !important;
  }

  .card-body {
    padding: 1rem;
  }

  .exam-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 4px 12px;
    font-size: 12px;
    color: #6c757d;
  }

  .exam-desc {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }

  .exam-meta-item strong {
    color: #212529;
  }

  .result-summary {
    min-height: 74px;
  }
</style>

<?= $this->endSection() ?>
