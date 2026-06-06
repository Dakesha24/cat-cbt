<?= $this->extend('templates/admin/admin_template') ?>

<?= $this->section('content') ?>

<style>
.exam-page { background: linear-gradient(180deg, #f8fbff 0%, #ffffff 100%); border: 1px solid #e9eef5; }
.exam-card { border-radius: 0; overflow: hidden; border: 1px solid #e9ecef; border-left: 4px solid #0d6efd; transition: transform 0.18s ease, box-shadow 0.2s ease; }
.exam-card:hover { transform: translateY(-2px); box-shadow: 0 12px 28px rgba(15, 23, 42, 0.10) !important; }
.exam-meta-pill { display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.28rem 0.6rem; border-radius: 999px; font-size: 0.72rem; font-weight: 600; line-height: 1; border: 1px solid transparent; }
.exam-meta-pill.school { background: #eef6ff; border-color: #cfe2ff; color: #0b5ed7; }
.exam-meta-pill.classroom { background: #eefbf3; border-color: #ccebd7; color: #137547; }
.exam-meta-pill.general { background: #f8f9fa; border-color: #e5e7eb; color: #6c757d; }
.btn-icon { width: 34px; height: 34px; display: inline-flex; align-items: center; justify-content: center; border: none; background: none; color: #9ca3af; border-radius: 4px; font-size: 1.1rem; transition: all 0.15s; }
.btn-icon:hover { background: #f0f0f0; color: #333; }
.line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.empty-sec { background: #f8f9fa; padding: 1.5rem; text-align: center; color: #adb5bd; font-size: 0.85rem; }
.modal-content { border-radius: 0 !important; overflow: hidden; }
.modal-header, .modal-footer { border-radius: 0 !important; }
</style>

<div class="container-fluid py-4">

  <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div>
      <h2 class="fw-bold text-dark mb-1">Kelola Mata Pelajaran</h2>
      <p class="text-muted mb-0">Kelola semua mata pelajaran dari seluruh sekolah dan guru</p>
    </div>
    <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#tambahModal">
      <i class="bi bi-plus-lg me-2"></i>Tambah Mata Pelajaran
    </button>
  </div>

  <div class="exam-page shadow-sm px-3 px-md-4 py-4">

    <?php if (session()->getFlashdata('success')): ?>
      <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm"><i class="bi bi-check-circle-fill me-2"></i><?= session()->getFlashdata('success') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
      <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm"><i class="bi bi-exclamation-triangle-fill me-2"></i><?= session()->getFlashdata('error') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('errors')): ?>
      <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <ul class="mb-0"><?php foreach (session()->getFlashdata('errors') as $err): ?><li><?= esc($err) ?></li><?php endforeach; ?></ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <?php if (empty($jenis_ujian)): ?>
      <div class="text-center py-5">
        <div class="mb-3"><i class="bi bi-journal-x text-muted" style="font-size:4rem"></i></div>
        <h5 class="text-muted">Belum ada Mata Pelajaran</h5>
        <p class="text-muted mb-3">Tambahkan mata pelajaran pertama untuk sistem</p>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahModal">
          <i class="bi bi-plus-lg me-2"></i>Tambah Mata Pelajaran
        </button>
      </div>
    <?php else: ?>
      <div class="row g-3">
        <?php foreach ($jenis_ujian as $jenis): ?>
          <div class="col-xl-4 col-lg-6">
            <div class="card border-0 shadow-sm h-100 exam-card">
              <div class="card-body d-flex flex-column p-0">

                <div class="d-flex align-items-center justify-content-end px-4 pt-3 pb-2">
                  <div class="dropdown">
                    <button class="btn-icon" type="button" data-bs-toggle="dropdown">
                      <i class="bi bi-three-dots"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                      <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editModal<?= $jenis['jenis_ujian_id'] ?>"><i class="bi bi-pencil me-2"></i>Edit</button></li>
                      <li><hr class="dropdown-divider"></li>
                      <li><a class="dropdown-item text-danger" href="<?= base_url('admin/jenis-ujian/hapus/' . $jenis['jenis_ujian_id']) ?>" onclick="return confirm('Hapus mata pelajaran ini?')"><i class="bi bi-trash me-2"></i>Hapus</a></li>
                    </ul>
                  </div>
                </div>

                <div class="px-4 pb-4 flex-grow-1 d-flex flex-column">
                  <h5 class="fw-bold mb-2" style="font-size:1rem"><?= esc($jenis['nama_jenis']) ?></h5>

                  <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    <?php if (!empty($jenis['nama_sekolah'])): ?>
                      <span class="exam-meta-pill school"><i class="bi bi-buildings"></i><?= esc($jenis['nama_sekolah']) ?></span>
                    <?php endif; ?>
                    <?php if (!empty($jenis['nama_kelas'])): ?>
                      <span class="exam-meta-pill classroom"><i class="bi bi-mortarboard"></i><?= esc($jenis['nama_kelas']) ?><?= !empty($jenis['tahun_ajaran']) ? ' — ' . esc($jenis['tahun_ajaran']) : '' ?></span>
                    <?php else: ?>
                      <span class="exam-meta-pill general"><i class="bi bi-globe me-1"></i>Umum</span>
                    <?php endif; ?>
                  </div>

                  <p class="text-muted small mb-0 line-clamp-2 flex-grow-1"><?= esc($jenis['deskripsi']) ?></p>

                  <?php $namaGuru = $jenis['guru_nama'] ?? $jenis['creator_name'] ?? null; ?>
                  <?php if ($namaGuru): ?>
                    <div class="mt-3 pt-3 border-top" style="font-size:0.78rem;color:#9ca3af">
                      <i class="bi bi-person me-1"></i><?= esc($namaGuru) ?>
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

<!-- Modal Tambah -->
<div class="modal fade" id="tambahModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-primary text-white px-4 py-3">
        <h5 class="modal-title fw-semibold"><i class="bi bi-plus-circle me-2"></i>Tambah Mata Pelajaran</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form action="<?= base_url('admin/jenis-ujian/tambah') ?>" method="post">
        <div class="modal-body px-4 py-4">
          <div class="mb-3">
            <label class="form-label small fw-semibold">Nama Mata Pelajaran <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="nama_jenis" placeholder="Contoh: Fisika, Kimia, Matematika" required>
          </div>
          <div class="mb-3">
            <label class="form-label small fw-semibold">Kelas</label>
            <select class="form-select" name="kelas_id">
              <option value="">-- Umum (semua kelas) --</option>
              <?php if (!empty($semua_kelas)):
                $currentSekolah = '';
                foreach ($semua_kelas as $kelas):
                  if ($currentSekolah !== $kelas['nama_sekolah']):
                    if ($currentSekolah !== ''): ?></optgroup><?php endif; ?>
                    <optgroup label="<?= esc($kelas['nama_sekolah']) ?>">
                    <?php $currentSekolah = $kelas['nama_sekolah'];
                  endif; ?>
                  <option value="<?= $kelas['kelas_id'] ?>"><?= esc($kelas['nama_kelas']) ?><?= !empty($kelas['tahun_ajaran']) ? ' - ' . esc($kelas['tahun_ajaran']) : '' ?></option>
                <?php endforeach;
                if ($currentSekolah !== ''): ?></optgroup><?php endif;
              endif; ?>
            </select>
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
      <form action="<?= base_url('admin/jenis-ujian/edit/' . $jenis['jenis_ujian_id']) ?>" method="post">
        <div class="modal-body px-4 py-4">
          <div class="mb-3">
            <label class="form-label small fw-semibold">Nama Mata Pelajaran <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="nama_jenis" value="<?= esc($jenis['nama_jenis']) ?>" required>
          </div>
          <div class="mb-3">
            <label class="form-label small fw-semibold">Kelas</label>
            <select class="form-select" name="kelas_id">
              <option value="" <?= empty($jenis['kelas_id'] ?? null) ? 'selected' : '' ?>>-- Umum (semua kelas) --</option>
              <?php if (!empty($semua_kelas)):
                $currentSekolah = '';
                foreach ($semua_kelas as $kelas):
                  if ($currentSekolah !== $kelas['nama_sekolah']):
                    if ($currentSekolah !== ''): ?></optgroup><?php endif; ?>
                    <optgroup label="<?= esc($kelas['nama_sekolah']) ?>">
                    <?php $currentSekolah = $kelas['nama_sekolah'];
                  endif; ?>
                  <option value="<?= $kelas['kelas_id'] ?>" <?= (isset($jenis['kelas_id']) && $jenis['kelas_id'] == $kelas['kelas_id']) ? 'selected' : '' ?>><?= esc($kelas['nama_kelas']) ?><?= !empty($kelas['tahun_ajaran']) ? ' - ' . esc($kelas['tahun_ajaran']) : '' ?></option>
                <?php endforeach;
                if ($currentSekolah !== ''): ?></optgroup><?php endif;
              endif; ?>
            </select>
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
