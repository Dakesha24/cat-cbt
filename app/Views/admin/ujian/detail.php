<?= $this->extend('templates/admin/admin_template') ?>

<?= $this->section('title') ?>Detail Ujian<?= $this->endSection() ?>

<?= $this->section('content') ?>
<br><br><br>

<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <!-- Header Ujian -->
      <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h4 class="card-title mb-0">
            <i class="fas fa-file-alt"></i> Detail Ujian: <?= esc($ujian['nama_ujian']) ?>
          </h4>
          <a href="<?= base_url('admin/ujian') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
          </a>
        </div>
        <div class="card-body">
          <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              <?= session()->getFlashdata('success') ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          <?php endif; ?>

          <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <?= session()->getFlashdata('error') ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          <?php endif; ?>

          <div class="row">
            <div class="col-md-6">
              <table class="table table-borderless">
                <tr>
                  <td><strong>Nama Ujian:</strong></td>
                  <td><?= esc($ujian['nama_ujian']) ?></td>
                </tr>
                <tr>
                  <td><strong>Mata Pelajaran:</strong></td>
                  <td><?= esc($ujian['nama_jenis']) ?: '-' ?></td>
                </tr>
                <tr>
                  <td><strong>Durasi:</strong></td>
                  <td><?= $ujian['durasi'] ? date('H:i', strtotime($ujian['durasi'])) . ' jam' : '-' ?></td>
                </tr>
              </table>
            </div>
            <div class="col-md-6">
              <table class="table table-borderless">
                <tr>
                  <td><strong>SE Awal:</strong></td>
                  <td><?= $ujian['se_awal'] ?></td>
                </tr>
                <tr>
                  <td><strong>SE Minimum:</strong></td>
                  <td><?= $ujian['se_minimum'] ?></td>
                </tr>
                <tr>
                  <td><strong>Dibuat:</strong></td>
                  <td><?= date('d/m/Y H:i', strtotime($ujian['created_at'])) ?></td>
                </tr>
              </table>
            </div>
          </div>

          <?php if (!empty($ujian['deskripsi'])): ?>
            <div class="mt-3">
              <strong>Deskripsi:</strong>
              <p class="mt-2"><?= nl2br(esc($ujian['deskripsi'])) ?></p>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <!-- Daftar Soal -->
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">
            <i class="fas fa-question-circle"></i> Daftar Soal (<?= count($soal) ?>)
          </h5>
          <div class="btn-group">
            <button type="button" class="btn btn-outline-primary btn-sm" onclick="expandAll()">
              <i class="fas fa-expand"></i> Buka Semua
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="collapseAll()">
              <i class="fas fa-compress"></i> Tutup Semua
            </button>
          </div>
        </div>
        <div class="card-body">
          <?php if (!empty($soal)): ?>
            <div class="accordion" id="soalAccordion">
              <?php foreach ($soal as $index => $s): ?>
                <div class="accordion-item">
                  <h2 class="accordion-header" id="heading<?= $s['soal_id'] ?>">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                      data-bs-target="#collapse<?= $s['soal_id'] ?>" aria-expanded="false">
                      <div class="w-100 d-flex justify-content-between align-items-center me-3">
                        <div>
                          <strong>Soal <?= $index + 1 ?>:</strong>
                          <?= strlen($s['pertanyaan']) > 100 ? substr(esc($s['pertanyaan']), 0, 100) . '...' : esc($s['pertanyaan']) ?>
                        </div>
                        <div>
                          <span class="badge bg-success me-2">Jawaban: <?= $s['jawaban_benar'] ?></span>
                          <span class="badge bg-info">Kesulitan: <?= number_format($s['tingkat_kesulitan'], 2) ?></span>
                        </div>
                      </div>
                    </button>
                  </h2>
                  <div id="collapse<?= $s['soal_id'] ?>" class="accordion-collapse collapse"
                    data-bs-parent="#soalAccordion">
                    <div class="accordion-body">
                      <div class="row">
                        <div class="col-md-8">
                          <!-- Pertanyaan -->
                          <div class="mb-4">
                            <h6 class="text-primary">Pertanyaan:</h6>
                            <div class="p-3 bg-light rounded">
                              <?= nl2br(esc($s['pertanyaan'])) ?>

                              <?php if (!empty($s['foto'])): ?>
                                <div class="mt-3">
                                  <img src="<?= base_url('uploads/soal/' . $s['foto']) ?>"
                                    class="img-fluid rounded border"
                                    alt="Gambar Soal"
                                    style="max-width: 100%; max-height: 300px;">
                                </div>
                              <?php endif; ?>
                            </div>
                          </div>

                          <!-- Pilihan Jawaban -->
                          <div class="mb-3">
                            <h6 class="text-primary">Pilihan Jawaban:</h6>
                            <div class="row">
                              <div class="col-md-6">
                                <div class="mb-2 p-2 rounded <?= $s['jawaban_benar'] === 'A' ? 'bg-success-subtle border border-success' : 'bg-light' ?>">
                                  <strong>A.</strong> <?= esc($s['pilihan_a']) ?>
                                  <?php if ($s['jawaban_benar'] === 'A'): ?>
                                    <i class="fas fa-check-circle text-success ms-2"></i>
                                  <?php endif; ?>
                                </div>
                                <div class="mb-2 p-2 rounded <?= $s['jawaban_benar'] === 'B' ? 'bg-success-subtle border border-success' : 'bg-light' ?>">
                                  <strong>B.</strong> <?= esc($s['pilihan_b']) ?>
                                  <?php if ($s['jawaban_benar'] === 'B'): ?>
                                    <i class="fas fa-check-circle text-success ms-2"></i>
                                  <?php endif; ?>
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="mb-2 p-2 rounded <?= $s['jawaban_benar'] === 'C' ? 'bg-success-subtle border border-success' : 'bg-light' ?>">
                                  <strong>C.</strong> <?= esc($s['pilihan_c']) ?>
                                  <?php if ($s['jawaban_benar'] === 'C'): ?>
                                    <i class="fas fa-check-circle text-success ms-2"></i>
                                  <?php endif; ?>
                                </div>
                                <div class="mb-2 p-2 rounded <?= $s['jawaban_benar'] === 'D' ? 'bg-success-subtle border border-success' : 'bg-light' ?>">
                                  <strong>D.</strong> <?= esc($s['pilihan_d']) ?>
                                  <?php if ($s['jawaban_benar'] === 'D'): ?>
                                    <i class="fas fa-check-circle text-success ms-2"></i>
                                  <?php endif; ?>
                                </div>
                              </div>
                            </div>
                          </div>

                          <!-- Pembahasan -->
                          <?php if (!empty($s['pembahasan'])): ?>
                            <div class="mb-3">
                              <h6 class="text-success">Pembahasan:</h6>
                              <div class="p-3 bg-success-subtle rounded border border-success">
                                <?= nl2br(esc($s['pembahasan'])) ?>
                              </div>
                            </div>
                          <?php endif; ?>
                        </div>

                        <div class="col-md-4">
                          <!-- Info Soal -->
                          <div class="card bg-light">
                            <div class="card-body">
                              <h6 class="card-title">Info Soal</h6>
                              <table class="table table-sm table-borderless">
                                <tr>
                                  <td><strong>ID Soal:</strong></td>
                                  <td><?= $s['soal_id'] ?></td>
                                </tr>
                                <tr>
                                  <td><strong>Jawaban:</strong></td>
                                  <td><span class="badge bg-success"><?= $s['jawaban_benar'] ?></span></td>
                                </tr>
                                <tr>
                                  <td><strong>Kesulitan:</strong></td>
                                  <td>
                                    <?php
                                    $kesulitan = (float)$s['tingkat_kesulitan'];
                                    $badgeClass = 'bg-secondary';
                                    $levelText = 'Tidak diketahui';

                                    if ($kesulitan >= 0.8) {
                                      $badgeClass = 'bg-danger';
                                      $levelText = 'Sangat Sulit';
                                    } elseif ($kesulitan >= 0.5) {
                                      $badgeClass = 'bg-warning';
                                      $levelText = 'Sulit';
                                    } elseif ($kesulitan >= 0.2) {
                                      $badgeClass = 'bg-info';
                                      $levelText = 'Sedang';
                                    } else {
                                      $badgeClass = 'bg-success';
                                      $levelText = 'Mudah';
                                    }
                                    ?>
                                    <span class="badge <?= $badgeClass ?>"><?= number_format($kesulitan, 4) ?></span><br>
                                    <small class="text-muted">(<?= $levelText ?>)</small>
                                  </td>
                                </tr>
                              </table>

                              <div class="mt-3">
                                <a href="<?= base_url('admin/soal/detail/' . $s['soal_id']) ?>"
                                  class="btn btn-info btn-sm me-1 mb-1">
                                  <i class="fas fa-eye me-1"></i>Detail
                                </a>
                                <a href="<?= base_url('admin/soal/hapus/' . $s['soal_id']) ?>"
                                  class="btn btn-danger btn-sm mb-1"
                                  onclick="return confirm('Apakah Anda yakin ingin menghapus soal ini?')">
                                  <i class="fas fa-trash me-1"></i>Hapus
                                </a>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <div class="text-center py-5">
              <i class="fas fa-question-circle fa-3x text-muted mb-3"></i>
              <h5 class="text-muted">Belum ada soal untuk ujian ini</h5>
              <p class="text-muted">Guru belum menambahkan soal untuk ujian ini.</p>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  .bg-success-subtle {
    background-color: rgba(25, 135, 84, 0.1) !important;
  }
</style>

<script>
  function expandAll() {
    const collapseElements = document.querySelectorAll('.accordion-collapse');
    collapseElements.forEach(element => {
      const bsCollapse = new bootstrap.Collapse(element, {
        show: true
      });
    });
  }

  function collapseAll() {
    const collapseElements = document.querySelectorAll('.accordion-collapse.show');
    collapseElements.forEach(element => {
      const bsCollapse = bootstrap.Collapse.getInstance(element);
      if (bsCollapse) {
        bsCollapse.hide();
      }
    });
  }
</script>

<?= $this->endSection() ?>