<?= $this->extend('templates/guru/guru_template') ?>

<?= $this->section('content') ?>
<br><br>
<div class="container-fluid py-4">
  <div class="row mb-4 align-items-center">
    <div class="col">
      <h2 class="fw-bold text-primary">Mata Pelajaran</h2>
      <p class="text-muted">Kelola kategori dan Mata Pelajaran untuk kelas yang Anda ajar</p>
    </div>
    <div class="col-auto">
      <?php if (!empty($kelas_guru)): ?>
        <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#tambahModal">
          <i class="bi bi-plus-circle me-2"></i>Tambah Mata Pelajaran
        </button>
      <?php else: ?>
        <button type="button" class="btn btn-secondary shadow-sm" disabled title="Anda belum di-assign ke kelas manapun">
          <i class="bi bi-plus-circle me-2"></i>Tambah Mata Pelajaran
        </button>
      <?php endif; ?>
    </div>
  </div>

  <!-- Alert Messages -->
  <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="bi bi-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <?php if (empty($kelas_guru)): ?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
      <i class="bi bi-exclamation-triangle me-2"></i>
      <strong>Perhatian!</strong> Anda belum di-assign ke kelas manapun.
      Silakan hubungi admin untuk mendapatkan assignment kelas agar bisa mengelola Mata Pelajaran.
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>


  <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <i class="bi bi-exclamation-circle me-2"></i><?= session()->getFlashdata('error') ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <!-- Daftar Mata Pelajaran -->
  <div class="row g-4">
    <?php if (!empty($jenis_ujian)): ?>
      <?php foreach ($jenis_ujian as $jenis): ?>
        <div class="col-lg-6 col-md-12">
          <div class="card h-100 shadow-sm hover-card">
            <div class="card-body p-4">
              <div class="d-flex align-items-start justify-content-between">
                <div class="flex-grow-1">
                  <div class="d-flex align-items-center mb-2">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px;">
                      <i class="bi bi-journal-text text-primary fs-5"></i>
                    </div>
                    <div>
                      <h5 class="card-title fw-bold mb-0"><?= esc($jenis['nama_jenis']) ?></h5>
                      <?php if (!empty($jenis['nama_kelas'])): ?>
                        <small class="text-primary">
                          <i class="bi bi-mortarboard me-1"></i><?= esc($jenis['nama_kelas']) ?>
                        </small>
                      <?php else: ?>
                        <small class="text-muted">
                          <i class="bi bi-globe me-1"></i>Umum
                        </small>
                      <?php endif; ?>
                    </div>
                  </div>
                  <p class="card-text text-muted"><?= esc($jenis['deskripsi']) ?></p>
                </div>
                <div class="dropdown">
                  <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-three-dots-vertical"></i>
                  </button>
                  <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                      <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editModal<?= $jenis['jenis_ujian_id'] ?>">
                        <i class="bi bi-pencil me-2"></i>Edit
                      </button>
                    </li>
                    <li>
                      <hr class="dropdown-divider">
                    </li>
                    <li>
                      <a class="dropdown-item text-danger" href="<?= base_url('guru/jenis-ujian/hapus/' . $jenis['jenis_ujian_id']) ?>"
                        onclick="return confirm('Apakah Anda yakin ingin menghapus Mata Pelajaran ini?')">
                        <i class="bi bi-trash me-2"></i>Hapus
                      </a>
                    </li>
                  </ul>
                </div>
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
              <i class="bi bi-journal-x text-muted" style="font-size: 4rem;"></i>
            </div>
            <?php if (empty($kelas_guru)): ?>
              <h5 class="text-muted">Tidak dapat membuat Mata Pelajaran</h5>
              <p class="text-muted">Anda belum di-assign ke kelas manapun.<br>Silakan hubungi admin untuk mendapatkan assignment kelas.</p>
            <?php else: ?>
              <h5 class="text-muted">Belum ada Mata Pelajaran</h5>
              <p class="text-muted">Tambahkan Mata Pelajaran pertama untuk kelas yang Anda ajar</p>
              <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahModal">
                <i class="bi bi-plus-circle me-2"></i>Tambah Mata Pelajaran
              </button>
            <?php endif; ?>
          </div>
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="tambahModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header border-0">
        <h5 class="modal-title fw-bold">
          <i class="bi bi-plus-circle  text-primary me-2"></i>Tambah Mata Pelajaran
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="<?= base_url('guru/jenis-ujian/tambah') ?>" method="post">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label fw-semibold">Nama Mata Pelajaran</label>
            <input type="text" class="form-control" name="nama_jenis" placeholder="Contoh: Fisika, Matematika, Kimia" required>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Kelas <span class="text-danger">*</span></label>
            <select class="form-select" name="kelas_id" required>
              <option value="">-- Pilih Kelas --</option>
              <?php if (!empty($kelas_guru)): ?>
                <?php foreach ($kelas_guru as $kelas): ?>
                  <option value="<?= $kelas['kelas_id'] ?>"><?= esc($kelas['nama_kelas']) ?></option>
                <?php endforeach; ?>
              <?php endif; ?>
            </select>
            <div class="form-text">Mata Pelajaran hanya berlaku untuk kelas yang dipilih</div>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Deskripsi</label>
            <textarea class="form-control" name="deskripsi" rows="3" placeholder="Deskripsi singkat tentang Mata Pelajaran..." required></textarea>
          </div>
        </div>
        <div class="modal-footer border-0">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-lg me-2"></i>Simpan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Edit -->
<?php foreach ($jenis_ujian as $jenis): ?>
  <div class="modal fade" id="editModal<?= $jenis['jenis_ujian_id'] ?>" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header border-0">
          <h5 class="modal-title fw-bold">
            <i class="bi bi-pencil text-warning me-2"></i>Edit Mata Pelajaran
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form action="<?= base_url('guru/jenis-ujian/edit/' . $jenis['jenis_ujian_id']) ?>" method="post">
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label fw-semibold">Nama Mata Pelajaran</label>
              <input type="text" class="form-control" name="nama_jenis" value="<?= esc($jenis['nama_jenis']) ?>" required>
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Kelas <span class="text-danger">*</span></label>
              <select class="form-select" name="kelas_id" required>
                <option value="">-- Pilih Kelas --</option>
                <?php if (!empty($kelas_guru)): ?>
                  <?php foreach ($kelas_guru as $kelas): ?>
                    <option value="<?= $kelas['kelas_id'] ?>"
                      <?= (isset($jenis['kelas_id']) && $jenis['kelas_id'] == $kelas['kelas_id']) ? 'selected' : '' ?>>
                      <?= esc($kelas['nama_kelas']) ?>
                    </option>
                  <?php endforeach; ?>
                <?php endif; ?>
              </select>
              <div class="form-text">Mata Pelajaran hanya berlaku untuk kelas yang dipilih</div>
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Deskripsi</label>
              <textarea class="form-control" name="deskripsi" rows="3" required><?= esc($jenis['deskripsi']) ?></textarea>
            </div>
          </div>
          <div class="modal-footer border-0">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-warning">
              <i class="bi bi-check-lg me-2"></i>Update
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
<?php endforeach; ?>

<style>
  .hover-card {
    transition: all 0.3s ease;
    border: none;
  }

  .hover-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
  }

  .modal-content {
    border: none;
    border-radius: 1rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
  }

  .form-control:focus,
  .form-select:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
  }
</style>

<?= $this->endSection() ?>