<?= $this->extend('templates/guru/guru_template') ?>

<?= $this->section('content') ?>

<style>
.mapel-card { border-radius: 0; border: none; border-left: 3px solid #0d6efd; transition: box-shadow 0.2s; }
.mapel-card:hover { box-shadow: 0 4px 18px rgba(0,0,0,0.10) !important; }
.mapel-icon { width: 40px; height: 40px; background: #eff6ff; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.modal-content { border-radius: 0 !important; border: none; }
</style>

<div class="container-fluid py-4">
  <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div>
      <h2 class="fw-bold text-dark mb-1">Mata Pelajaran</h2>
      <p class="text-muted mb-0">Kelola kategori dan Mata Pelajaran untuk kelas yang Anda ajar</p>
    </div>
    <?php if (!empty($kelas_guru)): ?>
      <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#tambahModal">
        <i class="bi bi-plus-lg me-2"></i>Tambah Mata Pelajaran
      </button>
    <?php else: ?>
      <button type="button" class="btn btn-secondary shadow-sm" disabled>
        <i class="bi bi-plus-lg me-2"></i>Tambah Mata Pelajaran
      </button>
    <?php endif; ?>
  </div>

  <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm"><i class="bi bi-check-circle-fill me-2"></i><?= session()->getFlashdata('success') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
  <?php endif; ?>
  <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm"><i class="bi bi-exclamation-triangle-fill me-2"></i><?= session()->getFlashdata('error') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
  <?php endif; ?>
  <?php if (empty($kelas_guru)): ?>
    <div class="alert alert-warning alert-dismissible fade show border-0 shadow-sm">
      <i class="bi bi-exclamation-triangle-fill me-2"></i>
      <strong>Perhatian!</strong> Anda belum di-assign ke kelas manapun. Silakan hubungi admin untuk mendapatkan assignment kelas.
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <?php if (!empty($jenis_ujian)): ?>
    <div class="row g-3">
      <?php foreach ($jenis_ujian as $jenis): ?>
        <div class="col-lg-6">
          <div class="card shadow-sm h-100 mapel-card">
            <div class="card-body p-4">
              <div class="d-flex align-items-start justify-content-between gap-3">
                <div class="d-flex align-items-start gap-3 flex-grow-1">
                  <div class="mapel-icon">
                    <i class="bi bi-journal-text text-primary" style="font-size:1.1rem"></i>
                  </div>
                  <div class="flex-grow-1">
                    <h5 class="fw-bold mb-1" style="font-size:0.975rem"><?= esc($jenis['nama_jenis']) ?></h5>
                    <?php if (!empty($jenis['nama_kelas'])): ?>
                      <div class="mb-1">
                        <span class="badge bg-primary bg-opacity-10 text-primary" style="border-radius:3px;font-size:0.72rem">
                          <i class="bi bi-mortarboard me-1"></i><?= esc($jenis['nama_kelas']) ?>
                        </span>
                      </div>
                    <?php else: ?>
                      <div class="mb-1">
                        <span class="badge bg-secondary bg-opacity-10 text-secondary" style="border-radius:3px;font-size:0.72rem">
                          <i class="bi bi-globe me-1"></i>Umum
                        </span>
                      </div>
                    <?php endif; ?>
                    <p class="text-muted small mb-0"><?= esc($jenis['deskripsi']) ?></p>
                  </div>
                </div>
                <div class="dropdown flex-shrink-0">
                  <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-three-dots-vertical"></i>
                  </button>
                  <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                    <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editModal<?= $jenis['jenis_ujian_id'] ?>"><i class="bi bi-pencil me-2"></i>Edit</button></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="<?= base_url('guru/jenis-ujian/hapus/' . $jenis['jenis_ujian_id']) ?>" onclick="return confirm('Hapus mata pelajaran ini?')"><i class="bi bi-trash me-2"></i>Hapus</a></li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <div class="text-center py-5">
      <div class="mb-3"><i class="bi bi-journal-x text-muted" style="font-size:4rem"></i></div>
      <?php if (empty($kelas_guru)): ?>
        <h5 class="text-muted">Tidak dapat membuat Mata Pelajaran</h5>
        <p class="text-muted">Anda belum di-assign ke kelas manapun.<br>Silakan hubungi admin untuk mendapatkan assignment kelas.</p>
      <?php else: ?>
        <h5 class="text-muted">Belum ada Mata Pelajaran</h5>
        <p class="text-muted mb-3">Tambahkan Mata Pelajaran pertama untuk kelas yang Anda ajar</p>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahModal">
          <i class="bi bi-plus-lg me-2"></i>Tambah Mata Pelajaran
        </button>
      <?php endif; ?>
    </div>
  <?php endif; ?>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="tambahModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-primary text-white px-4 py-3">
        <h5 class="modal-title fw-semibold"><i class="bi bi-plus-circle me-2"></i>Tambah Mata Pelajaran</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form action="<?= base_url('guru/jenis-ujian/tambah') ?>" method="post">
        <div class="modal-body px-4 py-4">
          <div class="mb-3">
            <label class="form-label small fw-semibold">Nama Mata Pelajaran <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="nama_jenis" placeholder="Contoh: Fisika, Matematika, Kimia" required>
          </div>
          <div class="mb-3">
            <label class="form-label small fw-semibold">Kelas</label>
            <select class="form-select" name="kelas_id">
              <option value="">-- Umum (semua kelas) --</option>
              <?php if (!empty($kelas_guru)): foreach ($kelas_guru as $kelas): ?>
                <option value="<?= $kelas['kelas_id'] ?>"><?= esc($kelas['nama_kelas']) ?></option>
              <?php endforeach; endif; ?>
            </select>
            <div class="form-text">Kosongkan untuk Mata Pelajaran umum (semua kelas)</div>
          </div>
          <div class="mb-3">
            <label class="form-label small fw-semibold">Deskripsi <span class="text-danger">*</span></label>
            <textarea class="form-control" name="deskripsi" rows="3" placeholder="Deskripsi singkat..." required></textarea>
          </div>
        </div>
        <div class="modal-footer border-0 bg-light px-4 py-3">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-lg me-1"></i>Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Edit -->
<?php foreach ($jenis_ujian as $jenis): ?>
<div class="modal fade" id="editModal<?= $jenis['jenis_ujian_id'] ?>" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-warning text-dark px-4 py-3">
        <h5 class="modal-title fw-semibold"><i class="bi bi-pencil me-2"></i>Edit Mata Pelajaran</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="<?= base_url('guru/jenis-ujian/edit/' . $jenis['jenis_ujian_id']) ?>" method="post">
        <div class="modal-body px-4 py-4">
          <div class="mb-3">
            <label class="form-label small fw-semibold">Nama Mata Pelajaran <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="nama_jenis" value="<?= esc($jenis['nama_jenis']) ?>" required>
          </div>
          <div class="mb-3">
            <label class="form-label small fw-semibold">Kelas</label>
            <select class="form-select" name="kelas_id">
              <option value="" <?= empty($jenis['kelas_id'] ?? null) ? 'selected' : '' ?>>-- Umum (semua kelas) --</option>
              <?php if (!empty($kelas_guru)): foreach ($kelas_guru as $kelas): ?>
                <option value="<?= $kelas['kelas_id'] ?>" <?= (isset($jenis['kelas_id']) && $jenis['kelas_id'] == $kelas['kelas_id']) ? 'selected' : '' ?>><?= esc($kelas['nama_kelas']) ?></option>
              <?php endforeach; endif; ?>
            </select>
            <div class="form-text">Mata Pelajaran hanya berlaku untuk kelas yang dipilih</div>
          </div>
          <div class="mb-3">
            <label class="form-label small fw-semibold">Deskripsi <span class="text-danger">*</span></label>
            <textarea class="form-control" name="deskripsi" rows="3" required><?= esc($jenis['deskripsi']) ?></textarea>
          </div>
        </div>
        <div class="modal-footer border-0 bg-light px-4 py-3">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-warning px-4"><i class="bi bi-check-lg me-1"></i>Update</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php endforeach; ?>

<?= $this->endSection() ?>
