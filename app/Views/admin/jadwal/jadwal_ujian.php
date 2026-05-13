<?= $this->extend('templates/admin/admin_template') ?>

<?= $this->section('content') ?>
<br><br>
<div class="container-fluid py-4">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h2 class="fw-bold text-primary">Jadwal Ujian</h2>
            <p class="text-muted">Atur jadwal pelaksanaan ujian untuk kelas yang Anda ajar</p>
        </div>
        <div class="col-auto">
            <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#tambahJadwalModal">
                <i class="bi bi-plus-circle me-2"></i>Tambah Jadwal
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
            <i class="bi bi-exclamation-circle me-2"></i><?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Daftar Jadwal Ujian -->
    <div class="row g-4">
        <?php if (!empty($jadwal)): ?>
            <?php foreach ($jadwal as $j): ?>
                <?php
                $now = new DateTime();
                $startDate = new DateTime($j['tanggal_mulai']);
                $endDate = new DateTime($j['tanggal_selesai']);

                $statusColor = 'secondary';
                $statusText = str_replace('_', ' ', $j['status']);
                if ($j['status'] == 'sedang_berlangsung') {
                    $statusColor = 'success';
                } elseif ($j['status'] == 'selesai') {
                    $statusColor = 'dark';
                }
                ?>
                <div class="col-lg-6 col-xl-4">
                    <div class="card h-100 shadow-sm hover-card">
                        <div class="card-body p-4 d-flex flex-column">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px;">
                                        <i class="bi bi-calendar-event text-primary fs-5"></i>
                                    </div>
                                    <div>
                                        <span class="badge bg-<?= $statusColor ?> small">
                                            <?= ucwords($statusText) ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editJadwalModal<?= $j['jadwal_id'] ?>">
                                                <i class="bi bi-pencil me-2"></i>Edit
                                            </button>
                                        </li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li>
                                            <a class="dropdown-item text-danger" href="<?= base_url('admin/jadwal-ujian/hapus/' . $j['jadwal_id']) ?>"
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus jadwal ini?')">
                                                <i class="bi bi-trash me-2"></i>Hapus
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <div class="flex-grow-1">
                                <h5 class="card-title fw-bold mb-2"><?= esc($j['nama_ujian']) ?></h5>
                                <p class="text-muted small mb-1">
                                    <i class="bi bi-code-square me-1"></i>Kode: <?= esc($j['kode_ujian']) ?>
                                </p>
                                <p class="card-text text-muted small mb-3">
                                    <i class="bi bi-building me-1"></i><?= esc($j['nama_sekolah']) ?>
                                    <br><i class="bi bi-people me-1"></i>Kelas <?= esc($j['nama_kelas']) ?>
                                    <br><i class="bi bi-person-check me-1"></i>Pengawas: <?= esc($j['nama_lengkap']) ?>
                                </p>

                                <div class="mb-3">
                                    <div class="row g-2 text-center">
                                        <div class="col-12">
                                            <div class="bg-light rounded p-2">
                                                <div class="fw-bold text-dark small"><?= date('d/m/Y H:i', strtotime($j['tanggal_mulai'])) ?></div>
                                                <small class="text-muted">Mulai</small>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="bg-light rounded p-2">
                                                <div class="fw-bold text-dark small"><?= date('d/m/Y H:i', strtotime($j['tanggal_selesai'])) ?></div>
                                                <small class="text-muted">Selesai</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted small">Kode Akses:</span>
                                        <span class="badge bg-light text-dark"><?= esc($j['kode_akses']) ?></span>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-auto">
                                <button class="btn btn-outline-primary btn-sm w-100" data-bs-toggle="modal" data-bs-target="#editJadwalModal<?= $j['jadwal_id'] ?>">
                                    <i class="bi bi-pencil me-2"></i>Edit Jadwal
                                </button>
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
                            <i class="bi bi-calendar-x text-muted" style="font-size: 4rem;"></i>
                        </div>
                        <h5 class="text-muted">Belum ada jadwal ujian</h5>
                        <p class="text-muted">Tambahkan jadwal ujian pertama untuk kelas yang Anda ajar</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="tambahJadwalModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-plus-circle text-primary me-2"></i>Tambah Jadwal Ujian
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('admin/jadwal-ujian/tambah') ?>" method="post">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Ujian</label>
                            <select name="ujian_id" class="form-select" required>
                                <option value="">Pilih Ujian</option>
                                <?php if (!empty($ujian_tambah)): ?>
                                    <?php foreach ($ujian_tambah as $u): ?>
                                        <option value="<?= $u['id_ujian'] ?>">
                                            <?= esc($u['nama_ujian']) ?> (<?= esc($u['kode_ujian']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Kelas</label>
                            <select name="kelas_id" class="form-select" required>
                                <option value="">Pilih Kelas</option>
                                <?php if (!empty($kelas)): ?>
                                    <?php foreach ($kelas as $k): ?>
                                        <option value="<?= $k['kelas_id'] ?>">
                                            <?= esc($k['nama_sekolah']) ?> - <?= esc($k['nama_kelas']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Guru Pengawas</label>
                            <select name="guru_id" class="form-select" required>
                                <option value="">Pilih Guru</option>
                                <?php if (!empty($guru)): ?>
                                    <?php foreach ($guru as $g): ?>
                                        <option value="<?= $g['guru_id'] ?>"><?= esc($g['nama_lengkap']) ?> - <?= esc($g['mata_pelajaran']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal & Waktu Mulai</label>
                            <input type="datetime-local" name="tanggal_mulai" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal & Waktu Selesai</label>
                            <input type="datetime-local" name="tanggal_selesai" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Kode Akses</label>
                            <input type="text" name="kode_akses" class="form-control" placeholder="Masukkan kode akses ujian" required>
                            <div class="form-text">Kode yang akan digunakan siswa untuk mengakses ujian</div>
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

<!-- Modal Edit -->
<?php if (!empty($jadwal)): ?>
    <?php foreach ($jadwal as $j): ?>
        <div class="modal fade" id="editJadwalModal<?= $j['jadwal_id'] ?>" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <h5 class="modal-title fw-bold">
                            <i class="bi bi-pencil text-warning me-2"></i>Edit Jadwal Ujian
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="<?= base_url('admin/jadwal-ujian/edit/' . $j['jadwal_id']) ?>" method="post">
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Ujian</label>
                                    <select name="ujian_id" class="form-select" required>
                                        <?php if (!empty($ujian_edit)): ?>
                                            <?php foreach ($ujian_edit as $u): ?>
                                                <option value="<?= $u['id_ujian'] ?>" <?= ($u['id_ujian'] == $j['ujian_id']) ? 'selected' : '' ?>>
                                                    <?= esc($u['nama_ujian']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Kelas</label>
                                    <select name="kelas_id" class="form-select" required>
                                        <?php if (!empty($kelas)): ?>
                                            <?php foreach ($kelas as $k): ?>
                                                <option value="<?= $k['kelas_id'] ?>" <?= ($k['kelas_id'] == $j['kelas_id']) ? 'selected' : '' ?>>
                                                    <?= esc($k['nama_sekolah']) ?> - <?= esc($k['nama_kelas']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Guru Pengawas</label>
                                    <select name="guru_id" class="form-select" required>
                                        <option value="">Pilih Guru</option>
                                        <?php if (!empty($guru)): ?>
                                            <?php foreach ($guru as $g): ?>
                                                <option value="<?= $g['guru_id'] ?>" <?= ($g['guru_id'] == $j['guru_id']) ? 'selected' : '' ?>>
                                                    <?= esc($g['nama_lengkap']) ?> - <?= esc($g['mata_pelajaran']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Tanggal & Waktu Mulai</label>
                                    <input type="datetime-local" name="tanggal_mulai" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime($j['tanggal_mulai'])) ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Tanggal & Waktu Selesai</label>
                                    <input type="datetime-local" name="tanggal_selesai" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime($j['tanggal_selesai'])) ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Kode Akses</label>
                                    <input type="text" name="kode_akses" class="form-control" value="<?= esc($j['kode_akses']) ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Status</label>
                                    <select name="status" class="form-select" required>
                                        <option value="belum_mulai" <?= ($j['status'] == 'belum_mulai') ? 'selected' : '' ?>>Belum Mulai</option>
                                        <option value="sedang_berlangsung" <?= ($j['status'] == 'sedang_berlangsung') ? 'selected' : '' ?>>Sedang Berlangsung</option>
                                        <option value="selesai" <?= ($j['status'] == 'selesai') ? 'selected' : '' ?>>Selesai</option>
                                    </select>
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
<?php endif; ?>

<style>
    .hover-card {
        transition: all 0.3s ease;
        border: none;
        min-height: 380px;
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
        min-height: 1.5em;
    }
</style>

<?= $this->endSection() ?>