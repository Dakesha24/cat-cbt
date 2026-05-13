<?= $this->extend('templates/guru/guru_template') ?>

<?= $this->section('content') ?>
<br><br>
<div class="container-fluid py-4">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h2 class="fw-bold text-primary">Pengumuman</h2>
            <p class="text-muted">Buat dan kelola pengumuman untuk siswa</p>
        </div>
        <div class="col-auto">
            <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#tambahPengumumanModal">
                <i class="bi bi-plus-circle me-2"></i>Tambah Pengumuman
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

    <!-- Daftar Pengumuman -->
    <div class="row g-4">
        <?php if (!empty($pengumuman)): ?>
            <?php foreach ($pengumuman as $p): ?>
                <?php
                $now = new DateTime();
                $publishDate = new DateTime($p['tanggal_publish']);
                $endDate = $p['tanggal_berakhir'] ? new DateTime($p['tanggal_berakhir']) : null;
                $isActive = $publishDate <= $now && (!$endDate || $endDate >= $now);
                ?>
                <div class="col-lg-6 col-xl-4">
                    <div class="card h-100 shadow-sm hover-card">
                        <div class="card-body p-4 d-flex flex-column">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px;">
                                        <i class="bi bi-megaphone text-primary fs-5"></i>
                                    </div>
                                    <div>
                                        <span class="badge <?= $isActive ? 'bg-success' : 'bg-light text-dark' ?> small">
                                            <?= $isActive ? 'Aktif' : 'Berakhir' ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#lihatPengumumanModal<?= $p['pengumuman_id'] ?>">
                                                <i class="bi bi-eye me-2"></i>Lihat
                                            </button>
                                        </li>
                                        <li>
                                            <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editPengumumanModal<?= $p['pengumuman_id'] ?>">
                                                <i class="bi bi-pencil me-2"></i>Edit
                                            </button>
                                        </li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li>
                                            <a class="dropdown-item text-danger" href="<?= base_url('guru/pengumuman/hapus/' . $p['pengumuman_id']) ?>"
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus pengumuman ini?')">
                                                <i class="bi bi-trash me-2"></i>Hapus
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <div class="flex-grow-1">
                                <h5 class="card-title fw-bold mb-2"><?= esc($p['judul']) ?></h5>
                                <p class="card-text text-muted small mb-3"><?= esc(substr($p['isi_pengumuman'], 0, 100)) ?><?= strlen($p['isi_pengumuman']) > 100 ? '...' : '' ?></p>

                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center text-muted small">
                                        <span><i class="bi bi-calendar me-1"></i><?= date('d/m/Y H:i', strtotime($p['tanggal_publish'])) ?></span>
                                        <span><i class="bi bi-person me-1"></i><?= esc($p['username']) ?></span>
                                    </div>
                                    <?php if ($p['tanggal_berakhir']): ?>
                                        <div class="text-muted small mt-1">
                                            <i class="bi bi-clock me-1"></i>Berakhir: <?= date('d/m/Y H:i', strtotime($p['tanggal_berakhir'])) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="mt-auto">
                                <button class="btn btn-outline-primary btn-sm w-100" data-bs-toggle="modal" data-bs-target="#lihatPengumumanModal<?= $p['pengumuman_id'] ?>">
                                    <i class="bi bi-eye me-2"></i>Lihat Detail
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
                            <i class="bi bi-megaphone-fill text-muted" style="font-size: 4rem;"></i>
                        </div>
                        <h5 class="text-muted">Belum ada pengumuman</h5>
                        <p class="text-muted">Tambahkan pengumuman pertama untuk siswa</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="tambahPengumumanModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-plus-circle text-primary me-2"></i>Tambah Pengumuman
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('guru/pengumuman/tambah') ?>" method="post">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Judul</label>
                            <input type="text" name="judul" class="form-control" placeholder="Judul pengumuman..." required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Isi Pengumuman</label>
                            <textarea name="isi_pengumuman" class="form-control" rows="5" placeholder="Tulis isi pengumuman..." required></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal Publish</label>
                            <input type="datetime-local" name="tanggal_publish" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal Berakhir</label>
                            <input type="datetime-local" name="tanggal_berakhir" class="form-control">
                            <div class="form-text">Kosongkan jika tidak ada batas waktu</div>
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

<!-- Modal Lihat -->
<?php foreach ($pengumuman as $p): ?>
    <div class="modal fade" id="lihatPengumumanModal<?= $p['pengumuman_id'] ?>" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold"><?= esc($p['judul']) ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3 p-3 bg-light rounded">
                        <div class="row g-2 text-muted small">
                            <div class="col-md-4">
                                <i class="bi bi-calendar me-1"></i>Dipublikasikan: <?= date('d/m/Y H:i', strtotime($p['tanggal_publish'])) ?>
                            </div>
                            <?php if ($p['tanggal_berakhir']): ?>
                                <div class="col-md-4">
                                    <i class="bi bi-clock me-1"></i>Berakhir: <?= date('d/m/Y H:i', strtotime($p['tanggal_berakhir'])) ?>
                                </div>
                            <?php endif; ?>
                            <div class="col-md-4">
                                <i class="bi bi-person me-1"></i>Oleh: <?= esc($p['username']) ?>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <?= nl2br(esc($p['isi_pengumuman'])) ?>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<!-- Modal Edit -->
<?php foreach ($pengumuman as $p): ?>
    <div class="modal fade" id="editPengumumanModal<?= $p['pengumuman_id'] ?>" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-pencil text-warning me-2"></i>Edit Pengumuman
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="<?= base_url('guru/pengumuman/edit/' . $p['pengumuman_id']) ?>" method="post">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Judul</label>
                                <input type="text" name="judul" class="form-control" value="<?= esc($p['judul']) ?>" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Isi Pengumuman</label>
                                <textarea name="isi_pengumuman" class="form-control" rows="5" required><?= esc($p['isi_pengumuman']) ?></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tanggal Publish</label>
                                <input type="datetime-local" name="tanggal_publish" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime($p['tanggal_publish'])) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tanggal Berakhir</label>
                                <input type="datetime-local" name="tanggal_berakhir" class="form-control" value="<?= $p['tanggal_berakhir'] ? date('Y-m-d\TH:i', strtotime($p['tanggal_berakhir'])) : '' ?>">
                                <div class="form-text">Kosongkan jika tidak ada batas waktu</div>
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