<?= $this->extend('templates/admin/admin_template') ?>

<?= $this->section('content') ?>
<br><br>
<div class="container">
  <div class="row mb-4 py-4">
    <div class="col">
      <!-- Breadcrumb -->
      <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?= base_url('admin/sekolah') ?>">Kelola Sekolah</a></li>
          <li class="breadcrumb-item active" aria-current="page"><?= esc($sekolah['nama_sekolah']) ?></li>
        </ol>
      </nav>

      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h2>Kelola Kelas - <?= esc($sekolah['nama_sekolah']) ?></h2>
          <p class="text-muted mb-0"><?= esc($sekolah['alamat']) ?></p>
        </div>
        <a href="<?= base_url('admin/sekolah/' . $sekolah['sekolah_id'] . '/kelas/tambah') ?>" class="btn btn-warning">
          <i class="bi bi-plus-circle me-2"></i>Tambah Kelas
        </a>
      </div>

      <!-- Flash Messages -->
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

      <!-- Info Summary -->
      <div class="row mb-4">
        <div class="col-md-3">
          <div class="card bg-primary text-white">
            <div class="card-body">
              <h5><?= count($kelas) ?></h5>
              <p class="mb-0">Total Kelas</p>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card bg-success text-white">
            <div class="card-body">
              <h5><?= array_sum(array_column($kelas, 'total_siswa')) ?></h5>
              <p class="mb-0">Total Siswa</p>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card bg-info text-white">
            <div class="card-body">
              <h5><?= $sekolah['total_guru'] ?></h5>
              <p class="mb-0">Total Guru</p>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card bg-warning text-dark">
            <div class="card-body">
              <h5><?= array_unique(array_column($kelas, 'tahun_ajaran')) ? count(array_unique(array_column($kelas, 'tahun_ajaran'))) : 0 ?></h5>
              <p class="mb-0">Tahun Ajaran Aktif</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Tabel Kelas -->
      <div class="card">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Nama Kelas</th>
                  <th>Tahun Ajaran</th>
                  <th>Jumlah Siswa</th>
                  <th>Jumlah Guru</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($kelas)): ?>
                  <tr>
                    <td colspan="6" class="text-center">
                      <div class="py-4">
                        <i class="bi bi-building fs-1 text-muted"></i>
                        <p class="text-muted mt-2">Belum ada kelas di sekolah ini</p>
                        <a href="<?= base_url('admin/sekolah/' . $sekolah['sekolah_id'] . '/kelas/tambah') ?>"
                          class="btn btn-outline-primary">
                          <i class="bi bi-plus-circle me-2"></i>Tambah Kelas Pertama
                        </a>
                      </div>
                    </td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($kelas as $index => $k): ?>
                    <tr>
                      <td><?= $index + 1 ?></td>
                      <td><strong><?= esc($k['nama_kelas']) ?></strong></td>
                      <td>
                        <span class="badge bg-info"><?= esc($k['tahun_ajaran']) ?></span>
                      </td>
                      <td>
                        <span class="badge bg-success"><?= $k['total_siswa'] ?> Siswa</span>
                      </td>
                      <td>
                        <span class="badge bg-primary"><?= $k['total_guru'] ?? 0 ?> Guru</span>
                      </td>
                      <td>
                        <div class="btn-group" role="group">
                          <!-- Detail Kelas (Kelola Anggota) -->
                          <a href="<?= base_url('admin/sekolah/' . $sekolah['sekolah_id'] . '/kelas/' . $k['kelas_id'] . '/detail') ?>"
                            class="btn btn-sm btn-outline-info" title="Kelola Anggota Kelas">
                            <i class="bi bi-people"></i>
                          </a>

                          <!-- Edit Kelas -->
                          <a href="<?= base_url('admin/sekolah/' . $sekolah['sekolah_id'] . '/kelas/edit/' . $k['kelas_id']) ?>"
                            class="btn btn-sm btn-outline-primary" title="Edit Kelas">
                            <i class="bi bi-pencil"></i>
                          </a>

                          <!-- Hapus Kelas -->
                          <?php if ($k['total_siswa'] == 0 && ($k['total_guru'] ?? 0) == 0): ?>
                            <a href="<?= base_url('admin/sekolah/' . $sekolah['sekolah_id'] . '/kelas/hapus/' . $k['kelas_id']) ?>"
                              class="btn btn-sm btn-outline-danger"
                              title="Hapus Kelas"
                              onclick="return confirm('Yakin ingin menghapus kelas <?= esc($k['nama_kelas']) ?>?')">
                              <i class="bi bi-trash"></i>
                            </a>
                          <?php else: ?>
                            <button class="btn btn-sm btn-outline-secondary"
                              title="Tidak dapat dihapus karena masih memiliki anggota" disabled>
                              <i class="bi bi-lock"></i>
                            </button>
                          <?php endif; ?>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>