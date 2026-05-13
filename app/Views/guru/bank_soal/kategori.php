<?= $this->extend('templates/guru/guru_template') ?>

<?= $this->section('content') ?>

<br><br>
<div class="container-fluid py-4">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h2 class="fw-bold text-primary">
                Bank Soal - <?= $kategori === 'umum' ? 'Umum' : 'Kelas ' . esc($kategori) ?>
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
                    <li class="breadcrumb-item active" aria-current="page">
                        <?= $kategori === 'umum' ? 'Umum' : 'Kelas ' . esc($kategori) ?>
                    </li>
                </ol>
            </nav>
        </div>
        <div class="col-auto">
            <a href="<?= base_url('guru/bank-soal') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali ke Bank Soal
            </a>
        </div>
    </div>

    <?php if (empty($jenisUjianList)): ?>
        <!-- Jika belum ada Mata Pelajaran -->
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card shadow-sm text-center">
                    <div class="card-body py-5">
                        <div class="mb-4">
                            <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                                <i class="fas fa-folder-open fa-3x text-muted"></i>
                            </div>
                        </div>
                        <h5 class="fw-bold text-muted mb-3">Belum Ada Bank Soal</h5>
                        <p class="text-muted mb-4">
                            Belum ada bank soal yang dibuat untuk kategori
                            <strong><?= $kategori === 'umum' ? 'Umum' : 'Kelas ' . esc($kategori) ?></strong>
                        </p>
                        <a href="<?= base_url('guru/bank-soal') ?>" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Tambah Bank Soal Pertama
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Info Box -->
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Kategori <?= $kategori === 'umum' ? 'Umum' : 'Kelas ' . esc($kategori) ?></strong> -
            <?= $kategori === 'umum' ? 'Bank soal yang dapat diakses oleh semua guru' : 'Bank soal khusus untuk kelas yang Anda ajar' ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>

        <!-- Daftar Mata Pelajaran -->
        <div class="row g-4">
            <?php foreach ($jenisUjianList as $jenis): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 shadow-sm hover-shadow">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                    <i class="fas fa-clipboard-list fa-2x text-info"></i>
                                </div>
                            </div>
                            <h5 class="card-title fw-bold"><?= esc($jenis['nama_jenis']) ?></h5>
                            <p class="card-text text-muted mb-4">
                                <i class="fas fa-book me-2"></i>
                                <span class="fw-semibold"><?= $jenis['jumlah_ujian'] ?></span> bank ujian tersedia
                            </p>
                            <a href="<?= base_url('guru/bank-soal/kategori/' . urlencode($kategori) . '/jenis-ujian/' . $jenis['jenis_ujian_id']) ?>"
                                class="btn btn-outline-info">
                                <i class="fas fa-arrow-right me-2"></i>Lihat Bank Ujian
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
                            Total <strong><?= count($jenisUjianList) ?></strong> Mata Pelajaran dengan
                            <strong><?= array_sum(array_column($jenisUjianList, 'jumlah_ujian')) ?></strong> bank ujian
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

    .btn-outline-info:hover {
        transform: scale(1.05);
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
</style>

<?= $this->endSection() ?>