<?= $this->extend('templates/admin/admin_template') ?>

<?= $this->section('content') ?>
<br><br>
<div class="container-fluid py-4">
  <!-- Breadcrumb -->
  <nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
      <li class="breadcrumb-item">
        <a href="<?= base_url('admin/bank-soal') ?>" class="text-decoration-none">
          <i class="fas fa-database me-1"></i>Bank Soal
        </a>
      </li>
      <li class="breadcrumb-item active" aria-current="page">
        <?= $kategori === 'umum' ? 'Bank Soal Umum' : 'Kelas ' . esc($kategori) ?>
      </li>
    </ol>
  </nav>

  <div class="row mb-4 align-items-center">
    <div class="col">
      <h2 class="fw-bold text-primary">
        <?= $kategori === 'umum' ? 'Bank Soal Umum' : 'Bank Soal Kelas ' . esc($kategori) ?>
      </h2>
      <p class="text-muted">Mata Pelajaran yang tersedia dalam kategori ini</p>
    </div>
    <div class="col-auto">
      <a href="<?= base_url('admin/bank-soal') ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Kembali
      </a>
    </div>
  </div>

  <!-- Alert Messages -->
  <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <i class="fas fa-exclamation-circle me-2"></i><?= session()->getFlashdata('error') ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <!-- Mata Pelajaran List -->
  <div class="row g-4">
    <?php if (!empty($jenisUjianList)): ?>
      <?php foreach ($jenisUjianList as $jenis): ?>
        <div class="col-lg-4 col-md-6">
          <div class="card h-100 shadow-sm hover-shadow">
            <div class="card-body p-4">
              <div class="d-flex align-items-start mb-3">
                <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                  <i class="fas fa-file-alt fa-lg text-info"></i>
                </div>
                <div class="flex-grow-1">
                  <h5 class="card-title fw-bold mb-1"><?= esc($jenis['nama_jenis']) ?></h5>
                  <span class="badge bg-info"><?= $jenis['jumlah_ujian'] ?> Bank Ujian</span>
                </div>
              </div>

              <div class="d-grid">
                <a href="<?= base_url('admin/bank-soal/kategori/' . urlencode($kategori) . '/jenis-ujian/' . $jenis['jenis_ujian_id']) ?>"
                  class="btn btn-outline-info">
                  <i class="fas fa-arrow-right me-2"></i>Lihat Bank Ujian
                </a>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="col-12">
        <div class="card shadow-sm">
          <div class="card-body text-center p-5">
            <div class="mb-3">
              <i class="fas fa-inbox fa-3x text-muted"></i>
            </div>
            <h5 class="card-title">Belum Ada Mata Pelajaran</h5>
            <p class="card-text text-muted">
              Belum ada bank ujian yang dibuat untuk kategori "<?= esc($kategori) ?>"
            </p>
            <a href="<?= base_url('admin/bank-soal') ?>" class="btn btn-primary">
              <i class="fas fa-plus me-2"></i>Tambah Bank Soal Baru
            </a>
          </div>
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>

<style>
  .hover-shadow:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
    transition: all 0.3s ease;
  }

  .card {
    border: none;
    transition: all 0.3s ease;
  }

  .breadcrumb-item a {
    color: #6c757d;
  }

  .breadcrumb-item a:hover {
    color: #0d6efd;
  }
</style>

<?= $this->endSection() ?>