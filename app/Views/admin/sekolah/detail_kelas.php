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
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/sekolah/' . $sekolah['sekolah_id'] . '/kelas') ?>"><?= esc($sekolah['nama_sekolah']) ?></a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?= esc($kelas['nama_kelas']) ?></li>
                </ol>
            </nav>

            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2>Detail Kelas <?= esc($kelas['nama_kelas']) ?></h2>
                    <p class="text-muted mb-0">
                        <?= esc($kelas['nama_sekolah']) ?> | Tahun Ajaran <?= esc($kelas['tahun_ajaran']) ?>
                    </p>
                </div>
                <div class="btn-group">
                    <a href="<?= base_url('admin/sekolah/' . $sekolah['sekolah_id'] . '/kelas/edit/' . $kelas['kelas_id']) ?>" class="btn btn-outline-primary">
                        <i class="bi bi-pencil me-2"></i>Edit Kelas
                    </a>
                    <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown">
                        <span class="visually-hidden">Toggle Dropdown</span>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#tambah-guru" data-bs-toggle="modal">
                            <i class="bi bi-person-plus me-2"></i>Tambah Guru ke Kelas
                        </a></li>
                        <li><a class="dropdown-item" href="<?= base_url('admin/siswa/tambah?kelas=' . $kelas['kelas_id']) ?>">
                            <i class="bi bi-people me-2"></i>Tambah Siswa Baru
                        </a></li>
                    </ul>
                </div>
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

            <!-- Info Cards -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h3><?= count($daftarGuru) ?></h3>
                                    <p class="mb-0">Guru Pengajar</p>
                                </div>
                                <i class="bi bi-person-workspace fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h3><?= count($daftarSiswa) ?></h3>
                                    <p class="mb-0">Siswa Aktif</p>
                                </div>
                                <i class="bi bi-people fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <ul class="nav nav-tabs" id="kelasTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="guru-tab" data-bs-toggle="tab" data-bs-target="#guru" type="button" role="tab">
                        <i class="bi bi-person-workspace me-2"></i>Daftar Guru (<?= count($daftarGuru) ?>)
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="siswa-tab" data-bs-toggle="tab" data-bs-target="#siswa" type="button" role="tab">
                        <i class="bi bi-people me-2"></i>Daftar Siswa (<?= count($daftarSiswa) ?>)
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="kelasTabContent">
                <!-- Tab Guru -->
                <div class="tab-pane fade show active" id="guru" role="tabpanel">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Guru Pengajar</h5>
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#tambah-guru">
                                <i class="bi bi-plus-circle me-2"></i>Assign Guru
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Guru</th>
                                            <th>NIP</th>
                                            <th>Mata Pelajaran</th>
                                            <th>Username</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($daftarGuru)): ?>
                                            <tr>
                                                <td colspan="6" class="text-center py-4">
                                                    <i class="bi bi-person-workspace fs-1 text-muted"></i>
                                                    <p class="text-muted mt-2">Belum ada guru yang mengajar di kelas ini</p>
                                                    <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#tambah-guru">
                                                        <i class="bi bi-plus-circle me-2"></i>Assign Guru Pertama
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($daftarGuru as $index => $guru): ?>
                                                <tr>
                                                    <td><?= $index + 1 ?></td>
                                                    <td>
                                                        <strong><?= esc($guru['nama_lengkap']) ?></strong>
                                                        <?php if ($guru['status'] === 'inactive'): ?>
                                                            <span class="badge bg-secondary ms-2">Nonaktif</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?= esc($guru['nip'] ?: '-') ?></td>
                                                    <td>
                                                        <span class="badge bg-info"><?= esc($guru['mata_pelajaran']) ?></span>
                                                        <?php if (!empty($guru['kelas_lain'])): ?>
                                                            <br><small class="text-muted">juga mengajar: <?= esc($guru['kelas_lain']) ?></small>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?= esc($guru['username']) ?></td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <a href="<?= base_url('admin/guru/edit/' . $guru['user_id']) ?>" 
                                                               class="btn btn-sm btn-outline-primary" title="Edit Guru">
                                                                <i class="bi bi-pencil"></i>
                                                            </a>
                                                            <a href="<?= base_url('admin/sekolah/' . $sekolah['sekolah_id'] . '/kelas/' . $kelas['kelas_id'] . '/guru/remove/' . $guru['guru_id']) ?>" 
                                                               class="btn btn-sm btn-outline-warning" 
                                                               title="Remove dari Kelas"
                                                               onclick="return confirm('Yakin ingin mengeluarkan <?= esc($guru['nama_lengkap']) ?> dari kelas ini?')">
                                                                <i class="bi bi-person-dash"></i>
                                                            </a>
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

                <!-- Tab Siswa -->
                <div class="tab-pane fade" id="siswa" role="tabpanel">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Siswa Kelas <?= esc($kelas['nama_kelas']) ?></h5>
                            <div class="btn-group">
                                <a href="<?= base_url('admin/siswa/tambah?kelas=' . $kelas['kelas_id']) ?>" class="btn btn-sm btn-success">
                                    <i class="bi bi-person-plus me-2"></i>Tambah Siswa
                                </a>
                                <a href="<?= base_url('admin/siswa/batch?kelas=' . $kelas['kelas_id']) ?>" class="btn btn-sm btn-info">
                                    <i class="bi bi-people me-2"></i>Batch Create
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Siswa</th>
                                            <th>NIS</th>
                                            <th>Username</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($daftarSiswa)): ?>
                                            <tr>
                                                <td colspan="6" class="text-center py-4">
                                                    <i class="bi bi-people fs-1 text-muted"></i>
                                                    <p class="text-muted mt-2">Belum ada siswa di kelas ini</p>
                                                    <a href="<?= base_url('admin/siswa/tambah?kelas=' . $kelas['kelas_id']) ?>" class="btn btn-outline-success">
                                                        <i class="bi bi-person-plus me-2"></i>Tambah Siswa Pertama
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($daftarSiswa as $index => $siswa): ?>
                                                <tr>
                                                    <td><?= $index + 1 ?></td>
                                                    <td>
                                                        <strong><?= esc($siswa['nama_lengkap']) ?></strong>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-secondary"><?= esc($siswa['nomor_peserta']) ?></span>
                                                    </td>
                                                    <td><?= esc($siswa['username']) ?></td>
                                                    <td>
                                                        <?php if ($siswa['status'] === 'active'): ?>
                                                            <span class="badge bg-success">Aktif</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-secondary">Nonaktif</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <a href="<?= base_url('admin/siswa/edit/' . $siswa['user_id']) ?>" 
                                                               class="btn btn-sm btn-outline-primary" title="Edit Siswa">
                                                                <i class="bi bi-pencil"></i>
                                                            </a>
                                                            <a href="<?= base_url('admin/sekolah/' . $sekolah['sekolah_id'] . '/kelas/' . $kelas['kelas_id'] . '/transfer-siswa/' . $siswa['siswa_id']) ?>" 
                                                               class="btn btn-sm btn-outline-warning" 
                                                               title="Transfer ke Kelas Lain">
                                                                <i class="bi bi-arrow-right-circle"></i>
                                                            </a>
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
    </div>
</div>

<!-- Modal Tambah Guru -->
<div class="modal fade" id="tambah-guru" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= base_url('admin/sekolah/' . $sekolah['sekolah_id'] . '/kelas/' . $kelas['kelas_id'] . '/guru/assign') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title">Assign Guru ke Kelas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="guru_id" class="form-label">Pilih Guru <span class="text-danger">*</span></label>
                        <select class="form-select" id="guru_id" name="guru_id" required>
                            <option value="">-- Pilih Guru --</option>
                            <?php if (empty($availableGuru)): ?>
                                <option disabled>Tidak ada guru tersedia di sekolah ini</option>
                            <?php else: ?>
                                <?php foreach ($availableGuru as $guru): ?>
                                    <option value="<?= $guru['guru_id'] ?>">
                                        <?= esc($guru['nama_lengkap']) ?> - <?= esc($guru['mata_pelajaran']) ?>
                                        <?php if ($guru['kelas_diajar']): ?>
                                            (telah mengisi kelas <?= esc($guru['kelas_diajar']) ?>)
                                        <?php endif; ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <div class="form-text">Hanya guru dari <strong><?= esc($kelas['nama_sekolah']) ?></strong> yang dapat dipilih.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Assign Guru</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>