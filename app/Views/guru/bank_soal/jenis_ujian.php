<?= $this->extend('templates/guru/guru_template') ?>

<?= $this->section('content') ?>
<br><br>
<div class="container-fluid py-4">
  <div class="row mb-4 align-items-center">
    <div class="col">
      <h2 class="fw-bold text-primary">
        <?= esc($jenisUjian['nama_jenis']) ?> - <?= $kategori === 'umum' ? 'Umum' : 'Kelas ' . esc($kategori) ?>
      </h2>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
          <li class="breadcrumb-item">
            <a href="<?= base_url('guru/dashboard') ?>" class="text-decoration-none">
              <i class="fas fa-home me-1"></i>Dashboard
            </a>
          </li>
          <li class="breadcrumb-item">
            <a href="<?= base_url('guru/bank-soal') ?>" class="text-decoration-none">Bank Soal</a>
          </li>
          <li class="breadcrumb-item">
            <a href="<?= base_url('guru/bank-soal/kategori/' . urlencode($kategori)) ?>" class="text-decoration-none">
              <?= $kategori === 'umum' ? 'Umum' : 'Kelas ' . esc($kategori) ?>
            </a>
          </li>
          <li class="breadcrumb-item active" aria-current="page">
            <?= esc($jenisUjian['nama_jenis']) ?>
          </li>
        </ol>
      </nav>
    </div>
    <div class="col-auto">
      <a href="<?= base_url('guru/bank-soal/kategori/' . urlencode($kategori)) ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Kembali ke Kategori
      </a>
    </div>
  </div>

  <?php if (empty($ujianList)): ?>
    <!-- Jika belum ada ujian -->
    <div class="row justify-content-center">
      <div class="col-lg-6">
        <div class="card shadow-sm text-center">
          <div class="card-body py-5">
            <div class="mb-4">
              <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                <i class="fas fa-file-alt fa-3x text-muted"></i>
              </div>
            </div>
            <h5 class="fw-bold text-muted mb-3">Belum Ada Bank Ujian</h5>
            <p class="text-muted mb-4">
              Belum ada bank ujian yang dibuat untuk Mata Pelajaran
              <strong><?= esc($jenisUjian['nama_jenis']) ?></strong>
              pada kategori <strong><?= $kategori === 'umum' ? 'Umum' : 'Kelas ' . esc($kategori) ?></strong>
            </p>
            <a href="<?= base_url('guru/bank-soal') ?>" class="btn btn-primary">
              <i class="fas fa-plus me-2"></i>Tambah Bank Ujian Pertama
            </a>
          </div>
        </div>
      </div>
    </div>
  <?php else: ?>
    <!-- Info Box -->
    <div class="alert alert-info alert-dismissible fade show" role="alert">
      <i class="fas fa-clipboard-list me-2"></i>
      <strong><?= esc($jenisUjian['nama_jenis']) ?></strong> -
      Daftar bank ujian yang tersedia untuk Mata Pelajaran ini pada kategori
      <strong><?= $kategori === 'umum' ? 'Umum' : 'Kelas ' . esc($kategori) ?></strong>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>

    <!-- Daftar Bank Ujian -->
    <div class="row g-4">
      <?php foreach ($ujianList as $ujian): ?>
        <div class="col-lg-6 col-xl-4">
          <div class="card h-100 shadow-sm hover-shadow">
            <div class="card-header border-0 bg-light">
              <div class="d-flex align-items-center">
                <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                  <i class="fas fa-book text-primary"></i>
                </div>
                <div class="flex-grow-1">
                  <h6 class="card-title mb-0 fw-bold text-truncate" title="<?= esc($ujian['nama_ujian']) ?>">
                    <?= esc($ujian['nama_ujian']) ?>
                  </h6>
                </div>
              </div>
            </div>
            <div class="card-body">
              <?php if (!empty($ujian['deskripsi'])): ?>
                <p class="card-text text-muted small mb-3">
                  <?= esc(substr($ujian['deskripsi'], 0, 100)) ?><?= strlen($ujian['deskripsi']) > 100 ? '...' : '' ?>
                </p>
              <?php else: ?>
                <p class="card-text text-muted small mb-3 fst-italic">Tidak ada deskripsi</p>
              <?php endif; ?>

              <div class="border-top pt-3">
                <div class="row text-center g-0">
                  <div class="col">
                    <small class="text-muted d-block">Dibuat oleh</small>
                    <small class="fw-semibold"><?= esc($ujian['creator_name']) ?></small>
                  </div>
                  <div class="col border-start">
                    <small class="text-muted d-block">Tanggal</small>
                    <small class="fw-semibold"><?= date('d/m/Y', strtotime($ujian['created_at'])) ?></small>
                  </div>
                </div>
              </div>
            </div>
            <div class="card-footer border-0 bg-transparent">
              <a href="<?= base_url('guru/bank-soal/kategori/' . urlencode($kategori) . '/jenis-ujian/' . $jenisUjian['jenis_ujian_id'] . '/ujian/' . $ujian['bank_ujian_id']) ?>"
                class="btn btn-primary w-100">
                <i class="fas fa-eye me-2"></i>Lihat & Kelola Soal
              </a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Summary Info -->
    <div class="row mt-5">
      <div class="col-12">
        <div class="card bg-light border-0">
          <div class="card-body text-center py-3">
            <small class="text-muted">
              <i class="fas fa-chart-bar me-2"></i>
              Total <strong><?= count($ujianList) ?></strong> bank ujian tersedia untuk Mata Pelajaran
              <strong><?= esc($jenisUjian['nama_jenis']) ?></strong>
            </small>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>
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

  .card-body {
    border-radius: 0.5rem;
  }

  .btn-primary:hover {
    transform: scale(1.02);
    transition: transform 0.2s ease;
  }

  .breadcrumb {
    background: none;
    padding: 0;
  }

  .breadcrumb-item+.breadcrumb-item::before {
    content: "›";
    color: #6c757d;
    font-weight: bold;
  }

  .breadcrumb-item.active {
    color: #6c757d;
    font-weight: 500;
  }

  .breadcrumb-item a {
    color: #0d6efd;
    font-weight: 500;
  }

  .breadcrumb-item a:hover {
    color: #0b5ed7;
  }

  .alert-info {
    border: none;
    background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
    border-left: 4px solid #2196f3;
  }

  .card-header {
    padding: 1rem;
  }

  .card-footer {
    padding: 1rem;
  }

  .text-truncate {
    max-width: 200px;
  }

  @media (max-width: 768px) {
    .text-truncate {
      max-width: 150px;
    }
  }
</style>

<!-- Modal cleanup script -->
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Handle modal cleanup if any
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
      modal.addEventListener('hidden.bs.modal', function() {
        const backdrop = document.querySelector('.modal-backdrop');
        if (backdrop) {
          backdrop.remove();
        }
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
      });
    });
  });
</script>

<?= $this->endSection() ?>