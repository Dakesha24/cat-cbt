<?= $this->extend('templates/admin/admin_template') ?>
<?= $this->section('content') ?>

<style>
.page-table { width: 100%; border-collapse: collapse; }
.page-table thead th { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #9ca3af; background: #f9fafb; border-bottom: 1px solid #e9ecef; padding: 0.6rem 1rem; white-space: nowrap; }
.page-table tbody td { padding: 0.85rem 1rem; vertical-align: middle; border-bottom: 1px solid #f3f4f6; font-size: 0.875rem; color: #374151; }
.page-table tbody tr:last-child td { border-bottom: 0; }
.page-table tbody tr:hover td { background: #f8f9ff; }
.stat-pill { display: inline-flex; align-items: center; gap: 0.3rem; padding: 0.22rem 0.6rem; border-radius: 999px; font-size: 0.72rem; font-weight: 600; }
.stat-pill.guru  { background: #ede9fe; color: #5b21b6; }
.stat-pill.kelas { background: #fef3c7; color: #92400e; }
.btn-icon { width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border: none; border-radius: 6px; font-size: 0.875rem; transition: all 0.15s; text-decoration: none; }
.btn-icon.success { background: #ecfdf5; color: #059669; } .btn-icon.success:hover { background: #d1fae5; }
.btn-icon.primary { background: #eff6ff; color: #2563eb; } .btn-icon.primary:hover { background: #dbeafe; }
.btn-icon.danger  { background: #fef2f2; color: #dc2626; } .btn-icon.danger:hover  { background: #fee2e2; }
.btn-icon.muted   { background: #f3f4f6; color: #9ca3af; cursor: not-allowed; }
.school-name { font-weight: 600; color: #111827; }
.school-meta { font-size: 0.78rem; color: #9ca3af; margin-top: 2px; }
</style>

<div class="container-fluid py-4">

  <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div>
      <h2 class="fw-bold text-dark mb-1">Sekolah & Kelas</h2>
      <p class="text-muted mb-0">Kelola data sekolah yang terdaftar dalam sistem</p>
    </div>
    <a href="<?= base_url('admin/sekolah/tambah') ?>" class="btn btn-primary shadow-sm">
      <i class="bi bi-plus-lg me-2"></i>Tambah Sekolah
    </a>
  </div>

  <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm">
      <i class="bi bi-check-circle-fill me-2"></i><?= session()->getFlashdata('success') ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>
  <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm">
      <i class="bi bi-exclamation-triangle-fill me-2"></i><?= session()->getFlashdata('error') ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <div class="card border-0 shadow-sm" style="border-radius:0">
    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center" style="padding:0.85rem 1.25rem">
      <span class="fw-semibold text-dark">Daftar Sekolah</span>
      <small class="text-muted"><?= count($sekolah) ?> sekolah</small>
    </div>
    <div class="card-body p-0">
      <?php if (empty($sekolah)): ?>
        <div class="text-center py-5">
          <i class="bi bi-building-x text-muted d-block mb-2" style="font-size:3rem;opacity:.4"></i>
          <h6 class="text-muted">Belum ada data sekolah</h6>
          <p class="text-muted small mb-3">Tambahkan sekolah pertama untuk memulai</p>
          <a href="<?= base_url('admin/sekolah/tambah') ?>" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i>Tambah Sekolah
          </a>
        </div>
      <?php else: ?>
        <div class="table-responsive">
          <table class="page-table">
            <thead>
              <tr>
                <th style="width:44px">#</th>
                <th>Nama Sekolah</th>
                <th>Kontak</th>
                <th class="text-center">Guru</th>
                <th class="text-center">Kelas</th>
                <th style="width:120px">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($sekolah as $i => $s): ?>
                <tr>
                  <td style="color:#9ca3af;font-size:0.8rem"><?= $i + 1 ?></td>
                  <td>
                    <div class="school-name"><?= esc($s['nama_sekolah']) ?></div>
                    <?php if (!empty($s['alamat'])): ?>
                      <div class="school-meta"><i class="bi bi-geo-alt me-1"></i><?= esc($s['alamat']) ?></div>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php if (!empty($s['telepon'])): ?>
                      <div style="font-size:0.82rem"><i class="bi bi-telephone me-1 text-muted"></i><?= esc($s['telepon']) ?></div>
                    <?php endif; ?>
                    <?php if (!empty($s['email'])): ?>
                      <div style="font-size:0.82rem"><i class="bi bi-envelope me-1 text-muted"></i><?= esc($s['email']) ?></div>
                    <?php endif; ?>
                    <?php if (empty($s['telepon']) && empty($s['email'])): ?>
                      <span style="color:#d1d5db">—</span>
                    <?php endif; ?>
                  </td>
                  <td class="text-center">
                    <span class="stat-pill guru"><i class="bi bi-person"></i><?= $s['total_guru'] ?></span>
                  </td>
                  <td class="text-center">
                    <span class="stat-pill kelas"><i class="bi bi-grid"></i><?= $s['total_kelas'] ?? 0 ?></span>
                  </td>
                  <td>
                    <div class="d-flex gap-1">
                      <a href="<?= base_url('admin/sekolah/' . $s['sekolah_id'] . '/kelas') ?>"
                         class="btn-icon success" title="Kelola Kelas">
                        <i class="bi bi-grid-3x3-gap"></i>
                      </a>
                      <a href="<?= base_url('admin/sekolah/edit/' . $s['sekolah_id']) ?>"
                         class="btn-icon primary" title="Edit">
                        <i class="bi bi-pencil"></i>
                      </a>
                      <?php if ($s['total_guru'] == 0 && ($s['total_kelas'] ?? 0) == 0): ?>
                        <a href="<?= base_url('admin/sekolah/hapus/' . $s['sekolah_id']) ?>"
                           class="btn-icon danger" title="Hapus"
                           onclick="return confirm('Yakin ingin menghapus sekolah ini?')">
                          <i class="bi bi-trash"></i>
                        </a>
                      <?php else: ?>
                        <span class="btn-icon muted" title="Tidak dapat dihapus — masih memiliki guru atau kelas">
                          <i class="bi bi-lock"></i>
                        </span>
                      <?php endif; ?>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </div>

</div>
<?= $this->endSection() ?>
