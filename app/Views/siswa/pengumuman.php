<?= $this->extend('templates/siswa/siswa_template') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-5">
  <div class="row">
    <div class="col-12">
      <div class="card shadow">
        <div class="card-header bg-primary text-white">
          <h4 class="card-title mb-0">Pengumuman</h4>
        </div>
        <div class="card-body">
          <?php if (empty($pengumuman)) : ?>
            <div class="alert alert-info">
              <i class="fas fa-info-circle"></i> Belum ada pengumuman.
            </div>
          <?php else : ?>
            <div class="timeline">
              <?php foreach ($pengumuman as $p) : ?>
                <?php
                $isExpired = strtotime($p['tanggal_berakhir']) < time();
                $statusClass = $isExpired ? 'border-danger' : 'border-success';
                ?>
                <div class="timeline-item">
                  <div class="card mb-4 <?= $statusClass ?>">
                    <div class="card-header d-flex justify-content-between align-items-center">
                      <h5 class="mb-0"><?= esc($p['judul']) ?></h5>
                      <?php if ($isExpired) : ?>
                        <span class="badge bg-danger">Berakhir</span>
                      <?php else : ?>
                        <span class="badge bg-success">Aktif</span>
                      <?php endif; ?>
                    </div>
                    <div class="card-body">
                      <div class="mb-3">
                        <?= nl2br(esc($p['isi_pengumuman'])) ?>
                      </div>
                      <div class="text-muted small">
                        <div class="d-flex justify-content-between">
                          <span>
                            <i class="fas fa-user"></i>
                            Posted by: <?= esc($p['username']) ?>
                          </span>
                          <span>
                            <i class="fas fa-calendar"></i>
                            Publish: <?= date('d M Y H:i', strtotime($p['tanggal_publish'])) ?>
                          </span>
                        </div>
                        <?php if ($p['tanggal_berakhir']) : ?>
                          <div class="mt-2">
                            <i class="fas fa-clock"></i>
                            Berakhir: <?= date('d M Y H:i', strtotime($p['tanggal_berakhir'])) ?>
                          </div>
                        <?php endif; ?>
                      </div>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  .timeline {
    position: relative;
    padding: 20px 0;
  }

  .timeline-item {
    position: relative;
    padding-left: 40px;
  }

  .timeline-item:before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
  }

  .timeline-item:after {
    content: '';
    position: absolute;
    left: -6px;
    top: 20px;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    background: #007bff;
  }

  .card {
    transition: transform 0.2s;
  }

  .card:hover {
    transform: translateY(-5px);
  }
</style>
<?= $this->endSection() ?>