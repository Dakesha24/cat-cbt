<?= $this->extend('templates/guru/guru_template') ?>

<?= $this->section('content') ?>
<br><br>
<div class="container-fluid py-4">
  <div class="row mb-4 align-items-center">
    <div class="col">
      <h2 class="fw-bold text-primary">Kelola Ujian</h2>
      <p class="text-muted">Buat dan kelola ujian beserta pengaturan Phy-FA-CAT untuk kelas yang Anda ajar</p>
    </div>
    <div class="col-auto">
      <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#tambahUjianModal">
        <i class="bi bi-plus-circle me-2"></i>Tambah Ujian
      </button>
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
      <i class="bi bi-exclamation-circle me-2"></i>
      <?php
      // Memeriksa jika flashdata 'error' adalah array (dari validasi)
      $errors = session()->getFlashdata('error');
      if (is_array($errors)) {
        echo '<ul>';
        foreach ($errors as $error) {
          echo '<li>' . esc($error) . '</li>'; // Menampilkan setiap error dalam list
        }
        echo '</ul>';
      } else {
        echo esc($errors); // Jika bukan array, tampilkan langsung
      }
      ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <!-- Daftar Ujian -->
  <div class="row g-4">
    <?php if (!empty($ujian)): ?>
      <?php foreach ($ujian as $u): ?>
        <div class="col-lg-6 col-xl-4">
          <div class="card h-100 shadow-sm hover-card">
            <div class="card-body p-4 d-flex flex-column">
              <div class="d-flex align-items-start justify-content-between mb-3">
                <div class="d-flex align-items-center">
                  <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px;">
                    <i class="bi bi-file-earmark-text text-primary fs-5"></i>
                  </div>
                  <div>
                    <span class="badge bg-light text-dark small mb-1">
                      <?= isset($u['nama_jenis']) ? esc($u['nama_jenis']) : 'Jenis tidak ditemukan' ?>
                    </span>
                    <?php if (!empty($u['nama_kelas'])): ?>
                      <br><small class="text-primary">
                        <i class="bi bi-mortarboard me-1"></i><?= esc($u['nama_kelas']) ?>
                      </small>
                    <?php else: ?>
                      <br><small class="text-muted">
                        <i class="bi bi-globe me-1"></i>Umum
                      </small>
                    <?php endif; ?>
                  </div>
                </div>
                <div class="dropdown">
                  <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-three-dots-vertical"></i>
                  </button>
                  <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                      <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editUjianModal<?= $u['id_ujian'] ?>">
                        <i class="bi bi-pencil me-2"></i>Edit
                      </button>
                    </li>
                    <li>
                      <a class="dropdown-item" href="<?= base_url('guru/soal/' . $u['id_ujian']) ?>">
                        <i class="bi bi-list-task me-2"></i>Kelola Soal
                      </a>
                    </li>
                    <li>
                      <hr class="dropdown-divider">
                    </li>
                    <li>
                      <a class="dropdown-item text-danger" href="<?= base_url('guru/ujian/hapus/' . $u['id_ujian']) ?>"
                        onclick="return confirm('Apakah Anda yakin ingin menghapus ujian ini?')">
                        <i class="bi bi-trash me-2"></i>Hapus
                      </a>
                    </li>
                  </ul>
                </div>
              </div>

              <div class="flex-grow-1">
                <h5 class="card-title fw-bold mb-2"><?= esc($u['nama_ujian']) ?></h5>
                <!-- Tambahkan Kode Ujian di sini -->
                <?php if (!empty($u['kode_ujian'])): ?>
                  <p class="card-subtitle text-muted small mb-2">Kode Ujian: <strong><?= esc($u['kode_ujian']) ?></strong></p>
                <?php endif; ?>
                <p class="card-text text-muted small mb-3"><?= esc($u['deskripsi']) ?></p>

                <div class="row g-2 text-center mb-3">
                  <div class="col-6">
                    <div class="bg-light rounded p-2">
                      <div class="fw-bold text-dark"><?= esc($u['durasi']) ?></div>
                      <small class="text-muted">Durasi</small>
                    </div>
                  </div>
                  <div class="col-6">
                    <div class="bg-light rounded p-2">
                      <div class="fw-bold text-dark"><?= esc($u['se_awal']) ?></div>
                      <small class="text-muted">SE Awal</small>
                    </div>
                  </div>
                </div>
              </div>

              <div class="mt-auto">
                <a href="<?= base_url('guru/soal/' . $u['id_ujian']) ?>" class="btn btn-outline-primary btn-sm w-100">
                  <i class="bi bi-list-task me-2"></i>Kelola Soal
                </a>
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
              <i class="bi bi-file-earmark-x text-muted" style="font-size: 4rem;"></i>
            </div>
            <h5 class="text-muted">Belum ada ujian</h5>
            <p class="text-muted">Tambahkan ujian pertama untuk kelas yang Anda ajar</p>
          </div>
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>

<!-- Modal Tambah Ujian -->
<div class="modal fade" id="tambahUjianModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header border-0">
        <h5 class="modal-title fw-bold">
          <i class="bi bi-plus-circle text-primary me-2"></i>Tambah Ujian
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="<?= base_url('guru/ujian/tambah') ?>" method="post">
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Mata Pelajaran</label>
              <select name="jenis_ujian_id" class="form-select" required>
                <option value="">Pilih Mata Pelajaran</option>
                <?php if (!empty($jenis_ujian)): ?>
                  <?php foreach ($jenis_ujian as $ju): ?>
                    <option value="<?= $ju['jenis_ujian_id'] ?>"><?= esc($ju['nama_jenis']) ?></option>
                  <?php endforeach; ?>
                <?php endif; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Kelas</label>
              <select class="form-select" name="kelas_id">
                <option value="">Pilih Kelas (Kosongkan untuk umum)</option>
                <?php if (!empty($kelas_guru)): ?>
                  <?php foreach ($kelas_guru as $kelas): ?>
                    <option value="<?= $kelas['kelas_id'] ?>"><?= esc($kelas['nama_kelas']) ?></option>
                  <?php endforeach; ?>
                <?php endif; ?>
              </select>
              <div class="form-text">Jika tidak dipilih, ujian akan bersifat umum</div>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Nama Ujian</label>
              <input type="text" name="nama_ujian" class="form-control" placeholder="Contoh: UTS Matematika Semester 1" required>
            </div>
            <!-- Tambahkan input Kode Ujian di sini untuk modal tambah -->
            <div class="col-12">
              <label class="form-label fw-semibold">Kode Ujian</label>
              <input type="text" name="kode_ujian" class="form-control" placeholder="Contoh: MTK_UTS_2025_01" required>
              <div class="form-text">Kode unik untuk ujian ini (digunakan untuk identifikasi).</div>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Deskripsi</label>
              <textarea name="deskripsi" class="form-control" rows="3" placeholder="Deskripsi ujian..." required></textarea>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Durasi (HH:MM:SS)</label>
              <input type="time" name="durasi" class="form-control" step="1" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">SE Awal</label>
              <input type="number" name="se_awal" class="form-control" step="0.0001" value="1.0000" required>
              <div class="form-text">Standard Error awal</div>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">SE Minimum</label>
              <input type="number" name="se_minimum" class="form-control" step="0.0001" value="0.2500" required>
              <div class="form-text">Batas SE minimum</div>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Delta SE Minimum</label>
              <input type="number" name="delta_se_minimum" class="form-control" step="0.0001" value="0.0100" required>
              <div class="form-text">Perubahan SE minimum</div>
            </div>
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

<!-- Modal Edit Ujian -->
<?php foreach ($ujian as $u): ?>
  <div class="modal fade" id="editUjianModal<?= $u['id_ujian'] ?>" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header border-0">
          <h5 class="modal-title fw-bold">
            <i class="bi bi-pencil text-warning me-2"></i>Edit Ujian
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form action="<?= base_url('guru/ujian/edit/' . $u['id_ujian']) ?>" method="post">
          <div class="modal-body">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label fw-semibold">Mata Pelajaran</label>
                <select name="jenis_ujian_id" class="form-select" required>
                  <?php if (!empty($jenis_ujian)): ?>
                    <?php foreach ($jenis_ujian as $ju): ?>
                      <option value="<?= $ju['jenis_ujian_id'] ?>" <?= $ju['jenis_ujian_id'] == $u['jenis_ujian_id'] ? 'selected' : '' ?>>
                        <?= esc($ju['nama_jenis']) ?>
                      </option>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">Kelas</label>
                <select class="form-select" name="kelas_id">
                  <option value="">Pilih Kelas (Kosongkan untuk umum)</option>
                  <?php if (!empty($kelas_guru)): ?>
                    <?php foreach ($kelas_guru as $kelas): ?>
                      <option value="<?= $kelas['kelas_id'] ?>"
                        <?= (isset($u['kelas_id']) && $u['kelas_id'] == $kelas['kelas_id']) ? 'selected' : '' ?>>
                        <?= esc($kelas['nama_kelas']) ?>
                      </option>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </select>
                <div class="form-text">Jika tidak dipilih, ujian akan bersifat umum</div>
              </div>
              <div class="col-12">
                <label class="form-label fw-semibold">Nama Ujian</label>
                <input type="text" name="nama_ujian" class="form-control" value="<?= esc($u['nama_ujian']) ?>" required>
              </div>
              <!-- Tambahkan input Kode Ujian di sini untuk modal edit -->
              <div class="col-12">
                <label class="form-label fw-semibold">Kode Ujian</label>
                <input type="text" name="kode_ujian" class="form-control" value="<?= esc($u['kode_ujian']) ?>" required>
                <div class="form-text">Kode unik untuk ujian ini (digunakan untuk identifikasi).</div>
              </div>
              <div class="col-12">
                <label class="form-label fw-semibold">Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="3" required><?= esc($u['deskripsi']) ?></textarea>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">Durasi (HH:MM:SS)</label>
                <input type="time" name="durasi" class="form-control" step="1" value="<?= esc($u['durasi']) ?>" required>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">SE Awal</label>
                <input type="number" name="se_awal" class="form-control" step="0.0001" value="<?= esc($u['se_awal']) ?>" required>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">SE Minimum</label>
                <input type="number" name="se_minimum" class="form-control" step="0.0001" value="<?= esc($u['se_minimum']) ?>" required>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">Delta SE Minimum</label>
                <input type="number" name="delta_se_minimum" class="form-control" step="0.0001" value="<?= esc($u['delta_se_minimum']) ?>" required>
              </div>
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
    min-height: 320px;
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
</style>

<?= $this->endSection() ?>