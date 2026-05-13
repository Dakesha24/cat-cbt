<?= $this->extend('templates/admin/admin_template') ?>

<?= $this->section('title') ?>Transfer Siswa<?= $this->endSection() ?>

<?= $this->section('content') ?>
<br><br><br>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/sekolah') ?>">Kelola Sekolah</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/sekolah/' . $sekolah['sekolah_id'] . '/kelas') ?>"><?= esc($sekolah['nama_sekolah']) ?></a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/sekolah/' . $sekolah['sekolah_id'] . '/kelas/' . $kelasAsal . '/detail') ?>"><?= esc($siswa['nama_kelas']) ?></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Transfer Siswa</li>
                </ol>
            </nav>
            
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title mb-0">Transfer Siswa ke Kelas Lain</h4>
                        <small class="text-muted">Pindahkan siswa ke kelas lain dalam sekolah yang sama</small>
                    </div>
                    <a href="<?= base_url('admin/sekolah/' . $sekolah['sekolah_id'] . '/kelas/' . $kelasAsal . '/detail') ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
                <div class="card-body">
                    <!-- Info Siswa -->
                    <div class="alert alert-info mb-4">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h6 class="alert-heading mb-2">
                                    <i class="bi bi-person-circle me-2"></i>Informasi Siswa
                                </h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Nama:</strong> <?= esc($siswa['nama_lengkap']) ?></p>
                                        <p class="mb-1"><strong>Username:</strong> <?= esc($siswa['username']) ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>NIS:</strong> <?= esc($siswa['nomor_peserta']) ?></p>
                                        <p class="mb-1"><strong>Kelas Saat Ini:</strong> <span class="badge bg-warning"><?= esc($siswa['nama_kelas']) ?></span></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 text-end">
                                <i class="bi bi-arrow-right-circle fs-1 text-warning"></i>
                            </div>
                        </div>
                    </div>

                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('admin/sekolah/transfer-siswa/proses') ?>" method="POST">
                        <?= csrf_field() ?>
                        <input type="hidden" name="siswa_id" value="<?= $siswa['siswa_id'] ?>">
                        <input type="hidden" name="sekolah_id" value="<?= $sekolah['sekolah_id'] ?>">
                        <input type="hidden" name="kelas_asal_id" value="<?= $kelasAsal ?>">
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="kelas_tujuan_id" class="form-label">
                                        Pilih Kelas Tujuan <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select form-select-lg" id="kelas_tujuan_id" name="kelas_tujuan_id" required>
                                        <option value="">-- Pilih Kelas Tujuan --</option>
                                        <?php if (empty($kelasLain)): ?>
                                            <option disabled>Tidak ada kelas lain di sekolah ini</option>
                                        <?php else: ?>
                                            <?php foreach ($kelasLain as $kelas): ?>
                                                <option value="<?= $kelas['kelas_id'] ?>">
                                                    <?= esc($kelas['nama_kelas']) ?> - <?= esc($kelas['tahun_ajaran']) ?>
                                                    (<?= $kelas['jumlah_siswa'] ?> siswa)
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                    <div class="form-text">
                                        Hanya menampilkan kelas lain dalam sekolah yang sama: <strong><?= esc($sekolah['nama_sekolah']) ?></strong>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light h-100">
                                    <div class="card-body d-flex flex-column justify-content-center">
                                        <h6 class="text-center mb-2">
                                            <i class="bi bi-info-circle text-info"></i> Catatan
                                        </h6>
                                        <small class="text-muted text-center">
                                            Siswa akan dipindahkan ke kelas yang dipilih. 
                                            Pastikan kelas tujuan sesuai dengan tingkat dan jurusan siswa.
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php if (empty($kelasLain)): ?>
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <strong>Tidak ada kelas lain</strong> di sekolah ini untuk transfer siswa.
                                <br>
                                <small>Anda perlu membuat kelas baru terlebih dahulu atau menggunakan fitur Edit Siswa untuk memindahkan ke sekolah lain.</small>
                            </div>
                        <?php endif; ?>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="<?= base_url('admin/sekolah/' . $sekolah['sekolah_id'] . '/kelas/' . $kelasAsal . '/detail') ?>" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Batal
                            </a>
                            
                            <?php if (!empty($kelasLain)): ?>
                                <button type="submit" class="btn btn-warning" 
                                        onclick="return confirm('Yakin ingin memindahkan <?= esc($siswa['nama_lengkap']) ?> ke kelas yang dipilih?')">
                                    <i class="bi bi-arrow-right-circle me-2"></i>Transfer Siswa
                                </button>
                            <?php else: ?>
                                <a href="<?= base_url('admin/siswa/edit/' . $siswa['user_id']) ?>" class="btn btn-primary">
                                    <i class="bi bi-pencil me-2"></i>Edit Siswa Manual
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Kelas Lain Preview (jika ada) -->
            <?php if (!empty($kelasLain)): ?>
                <div class="card mt-4">
                    <div class="card-header">
                        <h6 class="mb-0">Kelas Lain di <?= esc($sekolah['nama_sekolah']) ?></h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php foreach ($kelasLain as $kelas): ?>
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card border">
                                        <div class="card-body text-center">
                                            <h6 class="card-title"><?= esc($kelas['nama_kelas']) ?></h6>
                                            <p class="card-text">
                                                <small class="text-muted"><?= esc($kelas['tahun_ajaran']) ?></small><br>
                                                <span class="badge bg-info"><?= $kelas['jumlah_siswa'] ?> siswa</span>
                                            </p>
                                            <button type="button" class="btn btn-sm btn-outline-primary"
                                                    onclick="document.getElementById('kelas_tujuan_id').value = '<?= $kelas['kelas_id'] ?>'">
                                                Pilih Kelas Ini
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>