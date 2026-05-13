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
      <li class="breadcrumb-item">
        <a href="<?= base_url('admin/bank-soal/kategori/' . urlencode($kategori)) ?>" class="text-decoration-none">
          <?= $kategori === 'umum' ? 'Bank Soal Umum' : 'Kelas ' . esc($kategori) ?>
        </a>
      </li>
      <li class="breadcrumb-item active" aria-current="page">
        <?= esc($jenisUjian['nama_jenis']) ?>
      </li>
    </ol>
  </nav>

  <div class="row mb-4 align-items-center">
    <div class="col">
      <h2 class="fw-bold text-primary"><?= esc($jenisUjian['nama_jenis']) ?></h2>
      <p class="text-muted">
        Bank ujian untuk <?= $kategori === 'umum' ? 'kategori umum' : 'kelas ' . esc($kategori) ?>
      </p>
    </div>
    <div class="col-auto">
      <a href="<?= base_url('admin/bank-soal/kategori/' . urlencode($kategori)) ?>" class="btn btn-outline-secondary">
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

  <!-- Bank Ujian List -->
  <div class="row g-4">
    <?php if (!empty($ujianList)): ?>
      <?php foreach ($ujianList as $ujian): ?>
        <div class="col-lg-6 col-xl-4">
          <div class="card h-100 shadow-sm hover-shadow">
            <div class="card-body p-4">
              <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="flex-grow-1">
                  <h5 class="card-title fw-bold mb-2"><?= esc($ujian['nama_ujian']) ?></h5>
                  <p class="card-text text-muted small mb-2"><?= esc($ujian['deskripsi']) ?></p>
                </div>
                <div class="dropdown">
                  <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-ellipsis-v"></i>
                  </button>
                  <ul class="dropdown-menu">
                    <li>
                      <a class="dropdown-item" href="<?= base_url('admin/bank-soal/kategori/' . urlencode($kategori) . '/jenis-ujian/' . $jenisUjian['jenis_ujian_id'] . '/ujian/' . $ujian['bank_ujian_id']) ?>">
                        <i class="fas fa-eye me-2"></i>Lihat Soal
                      </a>
                    </li>
                    <li>
                      <hr class="dropdown-divider">
                    </li>
                    <li>
                      <a class="dropdown-item text-danger" href="<?= base_url('admin/bank-soal/hapus/' . $ujian['bank_ujian_id']) ?>"
                        onclick="return confirm('Yakin ingin menghapus bank ujian ini?')">
                        <i class="fas fa-trash me-2"></i>Hapus Bank Ujian
                      </a>
                    </li>
                  </ul>
                </div>
              </div>

              <div class="row g-2 mb-3">
                <div class="col-6">
                  <div class="text-center p-2 bg-light rounded">
                    <div class="fw-bold text-primary"><?= $ujian['jumlah_soal'] ?></div>
                    <small class="text-muted">Soal</small>
                  </div>
                </div>
                <div class="col-6">
                  <div class="text-center p-2 bg-light rounded">
                    <div class="fw-bold text-success">
                      <i class="fas fa-user"></i>
                    </div>
                    <small class="text-muted"><?= esc($ujian['creator_name']) ?></small>
                  </div>
                </div>
              </div>

              <div class="d-grid">
                <a href="<?= base_url('admin/bank-soal/kategori/' . urlencode($kategori) . '/jenis-ujian/' . $jenisUjian['jenis_ujian_id'] . '/ujian/' . $ujian['bank_ujian_id']) ?>"
                  class="btn btn-outline-primary">
                  <i class="fas fa-list me-2"></i>Kelola Soal
                </a>
              </div>

              <div class="mt-3 pt-3 border-top">
                <small class="text-muted">
                  <i class="fas fa-clock me-1"></i>
                  Dibuat: <?= date('d/m/Y H:i', strtotime($ujian['created_at'])) ?>
                </small>
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
            <h5 class="card-title">Belum Ada Bank Ujian</h5>
            <p class="card-text text-muted">
              Belum ada bank ujian untuk jenis "<?= esc($jenisUjian['nama_jenis']) ?>"
              dalam kategori "<?= esc($kategori) ?>"
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