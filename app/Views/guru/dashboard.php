<?= $this->extend('templates/guru/guru_template') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="row mb-4  py-5">
        <div class="col">
            <h2 class="mb-4">Dashboard Guru</h2>
            <div class="row g-4">
                <!-- Mata Pelajaran Card -->
                <div class="col-md-4">
                    <div class="card menu-card h-100">
                        <div class="card-body text-center">
                            <div class="icon-wrapper bg-primary-subtle mx-auto">
                                <i class="bi bi-journal-text text-primary fs-1"></i>
                            </div>
                            <h5 class="card-title">Mata Pelajaran</h5>
                            <p class="card-text">Kelola kategori dan Mata Pelajaran</p>
                            <a href="<?= base_url('guru/jenis-ujian') ?>" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>Kelola Mata Pelajaran
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Bank Soal Card - BARU -->
                <div class="col-md-4">
                    <div class="card menu-card h-100">
                        <div class="card-body text-center">
                            <div class="icon-wrapper bg-purple-subtle mx-auto">
                                <i class="bi bi-bank text-purple fs-1"></i>
                            </div>
                            <h5 class="card-title">Bank Soal</h5>
                            <p class="card-text">Kelola koleksi soal yang dapat digunakan untuk berbagai ujian</p>
                            <a href="<?= base_url('guru/bank-soal') ?>" class="btn btn-outline-purple">
                                <i class="bi bi-collection me-2"></i>Kelola Bank Soal
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Ujian Card -->
                <div class="col-md-4">
                    <div class="card menu-card h-100">
                        <div class="card-body text-center">
                            <div class="icon-wrapper bg-success-subtle mx-auto">
                                <i class="bi bi-file-earmark-text text-success fs-1"></i>
                            </div>
                            <h5 class="card-title">Ujian</h5>
                            <p class="card-text">Buat dan kelola ujian beserta soal-soalnya</p>
                            <a href="<?= base_url('guru/ujian') ?>" class="btn btn-success">
                                <i class="bi bi-plus-circle me-2"></i>Kelola Ujian
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Jadwal Ujian Card -->
                <div class="col-md-4">
                    <div class="card menu-card h-100">
                        <div class="card-body text-center">
                            <div class="icon-wrapper bg-info-subtle mx-auto">
                                <i class="bi bi-calendar-event text-info fs-1"></i>
                            </div>
                            <h5 class="card-title">Jadwal Ujian</h5>
                            <p class="card-text">Atur jadwal pelaksanaan ujian</p>
                            <a href="<?= base_url('guru/jadwal-ujian') ?>" class="btn btn-info">
                                <i class="bi bi-plus-circle me-2"></i>Kelola Jadwal
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Hasil Ujian Card -->
                <div class="col-md-4">
                    <div class="card menu-card h-100">
                        <div class="card-body text-center">
                            <div class="icon-wrapper bg-danger-subtle mx-auto">
                                <i class="bi bi-clipboard-data text-danger fs-1"></i>
                            </div>
                            <h5 class="card-title">Hasil Ujian</h5>
                            <p class="card-text">Lihat dan analisis hasil ujian siswa</p>
                            <a href="<?= base_url('guru/hasil-ujian') ?>" class="btn btn-danger">
                                <i class="bi bi-bar-chart me-2"></i>Lihat Hasil
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Pengumuman Card -->
                <div class="col-md-4">
                    <div class="card menu-card h-100">
                        <div class="card-body text-center">
                            <div class="icon-wrapper bg-warning-subtle mx-auto">
                                <i class="bi bi-megaphone text-warning fs-1"></i>
                            </div>
                            <h5 class="card-title">Pengumuman</h5>
                            <p class="card-text">Buat dan kelola pengumuman</p>
                            <a href="<?= base_url('guru/pengumuman') ?>" class="btn btn-warning">
                                <i class="bi bi-plus-circle me-2"></i>Kelola Pengumuman
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Custom purple color untuk Bank Soal */
    .bg-purple-subtle {
        background-color: rgba(138, 43, 226, 0.1) !important;
    }

    .text-purple {
        color: #8a2be2 !important;
    }

    .btn-outline-purple {
        color: #8a2be2;
        border-color: #8a2be2;
    }

    .btn-outline-purple:hover {
        color: #fff;
        background-color: #8a2be2;
        border-color: #8a2be2;
    }

    .icon-wrapper {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
    }

    .menu-card {
        transition: transform 0.3s ease;
        border: none;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .menu-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
    }
</style>

<?= $this->endSection() ?>