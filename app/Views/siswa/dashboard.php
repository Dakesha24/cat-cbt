<?= $this->extend('templates/siswa/siswa_template') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-5">
    <h2 class="mb-4 py-4">Dashboard</h2>
    
    <div class="row g-4">
        <!-- Pengumuman Card -->
        <div class="col-md-4">
            <a href="<?= base_url('siswa/pengumuman') ?>" class="text-decoration-none">
                <div class="card menu-card">
                    <div class="card-body text-center">
                        <div class="icon-wrapper mx-auto" style="background-color: #e3f2fd;">
                            <i class="bi bi-megaphone fs-1 text-primary"></i>
                        </div>
                        <h5 class="card-title mb-3">Pengumuman</h5>
                        <p class="card-text text-muted">Lihat pengumuman dan informasi terbaru</p>
                    </div>
                </div>
            </a>
        </div>
        
        <!-- Ujian Card -->
        <div class="col-md-4">
            <a href="<?= base_url('siswa/ujian') ?>" class="text-decoration-none">
                <div class="card menu-card">
                    <div class="card-body text-center">
                        <div class="icon-wrapper mx-auto" style="background-color: #e8f5e9;">
                            <i class="bi bi-journal-text fs-1 text-success"></i>
                        </div>
                        <h5 class="card-title mb-3">Ujian</h5>
                        <p class="card-text text-muted">Akses dan ikuti ujian yang tersedia</p>
                    </div>
                </div>
            </a>
        </div>
        
        <!-- Hasil Ujian Card -->
        <div class="col-md-4">
            <a href="<?= base_url('siswa/hasil') ?>" class="text-decoration-none">
                <div class="card menu-card">
                    <div class="card-body text-center">
                        <div class="icon-wrapper mx-auto" style="background-color: #e0f7fa;">
                            <i class="bi bi-clipboard-data fs-1 text-info"></i>
                        </div>
                        <h5 class="card-title mb-3">Hasil Ujian</h5>
                        <p class="card-text text-muted">Lihat hasil dan nilai ujian Anda</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>
<?= $this->endSection() ?>