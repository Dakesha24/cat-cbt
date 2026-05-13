<?= $this->extend('templates/admin/admin_template') ?>

<?= $this->section('content') ?>
<br><br>
<div class="container-fluid py-4">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h2 class="fw-bold text-primary">Kelola Ujian</h2>
            <p class="text-muted">Buat dan kelola ujian beserta pengaturan Phy-FA-CAT untuk kelas yang Anda ajar</p>
        </div>
        <div class="col-auto">
            <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#tambahUjianModal">
                <i class="bi bi-plus-circle me-2"></i>Tambah Ujian
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
            <i class="bi bi-exclamation-circle me-2"></i>
            <?php
            $errors = session()->getFlashdata('error');
            if (is_array($errors)) {
                echo '<ul class="mb-0">';
                foreach ($errors as $error) {
                    echo '<li>' . esc($error) . '</li>';
                }
                echo '</ul>';
            } else {
                echo esc($errors);
            }
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Daftar Ujian -->
    <div class="row g-4">
        <?php if (!empty($ujian)): ?>
            <?php foreach ($ujian as $u): ?>
                <div class="col-lg-6 col-xl-4">
                    <div class="card h-100 shadow-sm hover-card">
                        <div class="card-body p-4 d-flex flex-column">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px;">
                                        <i class="bi bi-file-earmark-text text-primary fs-5"></i>
                                    </div>
                                    <div>
                                        <span class="badge bg-light text-dark small mb-1">
                                            <?= isset($u['nama_jenis']) ? esc($u['nama_jenis']) : 'Mata Pelajaran tidak ditemukan' ?>
                                        </span>
                                        <?php if (!empty($u['nama_kelas'])): ?>
                                            <br><small class="text-primary">
                                                <i class="bi bi-mortarboard me-1"></i><?= esc($u['nama_kelas']) ?>
                                            </small>
                                        <?php else: ?>
                                            <br><small class="text-muted">
                                                <i class="bi bi-globe me-1"></i>Umum
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editUjianModal<?= $u['id_ujian'] ?>">
                                                <i class="bi bi-pencil me-2"></i>Edit
                                            </button>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="<?= base_url('admin/soal/' . $u['id_ujian']) ?>">
                                                <i class="bi bi-list-task me-2"></i>Kelola Soal
                                            </a>
                                        </li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li>
                                            <a class="dropdown-item text-danger" href="<?= base_url('admin/ujian/hapus/' . $u['id_ujian']) ?>"
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus ujian ini?')">
                                                <i class="bi bi-trash me-2"></i>Hapus
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <div class="flex-grow-1">
                                <h5 class="card-title fw-bold mb-2"><?= esc($u['nama_ujian']) ?></h5>
                                
                                <?php if (!empty($u['kode_ujian'])): ?>
                                    <p class="card-subtitle text-muted small mb-2">
                                        <i class="bi bi-key me-1"></i>Kode: <strong><?= esc($u['kode_ujian']) ?></strong>
                                    </p>
                                <?php endif; ?>
                                
                                <p class="card-text text-muted small mb-3"><?= esc($u['deskripsi']) ?></p>

                                <div class="row g-2 text-center mb-3">
                                    <div class="col-6">
                                        <div class="bg-light rounded p-2">
                                            <div class="fw-bold text-dark"><?= esc($u['durasi']) ?></div>
                                            <small class="text-muted">Durasi</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="bg-light rounded p-2">
                                            <div class="fw-bold text-dark"><?= esc($u['se_awal']) ?></div>
                                            <small class="text-muted">SE Awal</small>
                                        </div>
                                    </div>
                                </div>

                                <?php if (!empty($u['guru_pembuat'])): ?>
                                    <p class="small text-muted mb-2">
                                        <i class="bi bi-person me-1"></i>Dibuat oleh: <?= esc($u['guru_pembuat']) ?>
                                    </p>
                                <?php endif; ?>
                            </div>

                            <div class="mt-auto">
                                <a href="<?= base_url('admin/soal/' . $u['id_ujian']) ?>" class="btn btn-outline-primary btn-sm w-100">
                                    <i class="bi bi-list-task me-2"></i>Kelola Soal
                                </a>
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
                            <i class="bi bi-file-earmark-x text-muted" style="font-size: 4rem;"></i>
                        </div>
                        <h5 class="text-muted">Belum ada ujian</h5>
                        <p class="text-muted">Tambahkan ujian pertama untuk memulai</p>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahUjianModal">
                            <i class="bi bi-plus-circle me-2"></i>Tambah Ujian
                        </button>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Tambah Ujian -->
<div class="modal fade" id="tambahUjianModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-plus-circle text-primary me-2"></i>Tambah Ujian
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('admin/ujian/tambah') ?>" method="post" id="formTambahUjian">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Sekolah <span class="text-danger">*</span></label>
                            <select id="sekolah-select-tambah" name="sekolah_id" class="form-select" required>
                                <option value="">Pilih Sekolah Terlebih Dahulu</option>
                                <?php if (!empty($sekolah)): ?>
                                    <?php foreach ($sekolah as $s): ?>
                                        <option value="<?= $s['sekolah_id'] ?>"><?= esc($s['nama_sekolah']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Kelas</label>
                            <select id="kelas-select-tambah" class="form-select" name="kelas_id" disabled>
                                <option value="">Pilih Sekolah Terlebih Dahulu</option>
                            </select>
                            <div class="form-text">Jika tidak dipilih, ujian akan bersifat umum.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Mata Pelajaran <span class="text-danger">*</span></label>
                            <select name="jenis_ujian_id" class="form-select" required>
                                <option value="">Pilih Mata Pelajaran</option>
                                <?php if (!empty($jenis_ujian)): ?>
                                    <?php foreach ($jenis_ujian as $ju): ?>
                                        <option value="<?= $ju['jenis_ujian_id'] ?>">
                                            <?= esc($ju['nama_jenis']) ?>
                                            <?php if (!empty($ju['nama_kelas'])): ?>
                                                - <?= esc($ju['nama_kelas']) ?>
                                            <?php endif; ?>
                                            <?php if (!empty($ju['nama_sekolah'])): ?>
                                                (<?= esc($ju['nama_sekolah']) ?>)
                                            <?php endif; ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Nama Ujian <span class="text-danger">*</span></label>
                            <input type="text" name="nama_ujian" class="form-control" placeholder="Contoh: UTS Matematika Semester 1" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Kode Ujian <span class="text-danger">*</span></label>
                            <input type="text" name="kode_ujian" class="form-control" placeholder="Contoh: MTK_UTS_2025_01" required>
                            <div class="form-text">Kode unik untuk ujian ini (digunakan untuk identifikasi).</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Deskripsi <span class="text-danger">*</span></label>
                            <textarea name="deskripsi" class="form-control" rows="3" placeholder="Deskripsi ujian..." required></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Durasi (HH:MM:SS) <span class="text-danger">*</span></label>
                            <input type="time" name="durasi" class="form-control" step="1" value="01:30:00" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">SE Awal <span class="text-danger">*</span></label>
                            <input type="number" name="se_awal" class="form-control" step="0.0001" value="1.0000" required>
                            <div class="form-text">Standard Error awal</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">SE Minimum <span class="text-danger">*</span></label>
                            <input type="number" name="se_minimum" class="form-control" step="0.0001" value="0.2500" required>
                            <div class="form-text">Batas SE minimum</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Delta SE Minimum <span class="text-danger">*</span></label>
                            <input type="number" name="delta_se_minimum" class="form-control" step="0.0001" value="0.0100" required>
                            <div class="form-text">Perubahan SE minimum</div>
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

<!-- Modal Edit Ujian -->
<?php if (!empty($ujian)): ?>
    <?php foreach ($ujian as $u): ?>
        <div class="modal fade" id="editUjianModal<?= $u['id_ujian'] ?>" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <h5 class="modal-title fw-bold">
                            <i class="bi bi-pencil text-warning me-2"></i>Edit Ujian
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="<?= base_url('admin/ujian/edit/' . $u['id_ujian']) ?>" method="post">
                        <div class="modal-body">
                            <div class="row g-3">
                                <!-- Info Ujian yang Sedang Diedit -->
                                <div class="col-12">
                                    <div class="alert alert-info">
                                        <h6 class="alert-heading mb-2">
                                            <i class="bi bi-info-circle me-2"></i>Informasi Ujian
                                        </h6>
                                        <p class="mb-1"><strong>Nama:</strong> <?= esc($u['nama_ujian']) ?></p>
                                        <p class="mb-0"><strong>Kode:</strong> <?= esc($u['kode_ujian']) ?></p>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Sekolah <span class="text-danger">*</span></label>
                                    <select class="form-select sekolah-select-edit" name="sekolah_id" data-ujian-id="<?= $u['id_ujian'] ?>" required>
                                        <option value="">Pilih Sekolah</option>
                                        <?php if (!empty($sekolah)): ?>
                                            <?php foreach ($sekolah as $s): ?>
                                                <option value="<?= $s['sekolah_id'] ?>" 
                                                    <?= (isset($u['sekolah_id']) && $u['sekolah_id'] == $s['sekolah_id']) ? 'selected' : '' ?>>
                                                    <?= esc($s['nama_sekolah']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Kelas</label>
                                    <select class="form-select kelas-select-edit" name="kelas_id" data-ujian-id="<?= $u['id_ujian'] ?>">
                                        <option value="">Pilih Kelas (Kosongkan untuk umum)</option>
                                        <?php if (!empty($kelas_guru)): ?>
                                            <?php foreach ($kelas_guru as $kelas): ?>
                                                <option value="<?= $kelas['kelas_id'] ?>"
                                                    <?= (isset($u['kelas_id']) && $u['kelas_id'] == $kelas['kelas_id']) ? 'selected' : '' ?>>
                                                    <?= esc($kelas['nama_kelas']) ?>
                                                    <?php if (!empty($kelas['nama_sekolah'])): ?>
                                                        - <?= esc($kelas['nama_sekolah']) ?>
                                                    <?php endif; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                    <div class="form-text">Jika tidak dipilih, ujian akan bersifat umum</div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold">Mata Pelajaran <span class="text-danger">*</span></label>
                                    <select name="jenis_ujian_id" class="form-select" required>
                                        <?php if (!empty($jenis_ujian)): ?>
                                            <?php foreach ($jenis_ujian as $ju): ?>
                                                <option value="<?= $ju['jenis_ujian_id'] ?>" 
                                                    <?= $ju['jenis_ujian_id'] == $u['jenis_ujian_id'] ? 'selected' : '' ?>>
                                                    <?= esc($ju['nama_jenis']) ?>
                                                    <?php if (!empty($ju['nama_kelas'])): ?>
                                                        - <?= esc($ju['nama_kelas']) ?>
                                                    <?php endif; ?>
                                                    <?php if (!empty($ju['nama_sekolah'])): ?>
                                                        (<?= esc($ju['nama_sekolah']) ?>)
                                                    <?php endif; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold">Nama Ujian <span class="text-danger">*</span></label>
                                    <input type="text" name="nama_ujian" class="form-control" value="<?= esc($u['nama_ujian']) ?>" required>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold">Kode Ujian <span class="text-danger">*</span></label>
                                    <input type="text" name="kode_ujian" class="form-control" value="<?= esc($u['kode_ujian']) ?>" required>
                                    <div class="form-text">Kode unik untuk ujian ini (digunakan untuk identifikasi).</div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold">Deskripsi <span class="text-danger">*</span></label>
                                    <textarea name="deskripsi" class="form-control" rows="3" required><?= esc($u['deskripsi']) ?></textarea>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Durasi (HH:MM:SS) <span class="text-danger">*</span></label>
                                    <input type="time" name="durasi" class="form-control" step="1" value="<?= esc($u['durasi']) ?>" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">SE Awal <span class="text-danger">*</span></label>
                                    <input type="number" name="se_awal" class="form-control" step="0.0001" value="<?= esc($u['se_awal']) ?>" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">SE Minimum <span class="text-danger">*</span></label>
                                    <input type="number" name="se_minimum" class="form-control" step="0.0001" value="<?= esc($u['se_minimum']) ?>" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Delta SE Minimum <span class="text-danger">*</span></label>
                                    <input type="number" name="delta_se_minimum" class="form-control" step="0.0001" value="<?= esc($u['delta_se_minimum']) ?>" required>
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

    .form-label .text-danger {
        font-size: 0.8em;
    }

    .alert-info {
        background-color: #e7f3ff;
        border-color: #b3d9ff;
        color: #004085;
    }

    .text-orange {
        color: #fd7e14 !important;
    }

    .bg-orange {
        background-color: #fd7e14 !important;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handler untuk Modal Tambah Ujian
    const sekolahSelectTambah = document.getElementById('sekolah-select-tambah');
    const kelasSelectTambah = document.getElementById('kelas-select-tambah');

    if (sekolahSelectTambah && kelasSelectTambah) {
        sekolahSelectTambah.addEventListener('change', function() {
            handleSekolahChange(this, kelasSelectTambah);
        });
    }

    // Handler untuk Modal Edit Ujian
    const sekolahSelectsEdit = document.querySelectorAll('.sekolah-select-edit');
    sekolahSelectsEdit.forEach(function(sekolahSelect) {
        const ujianId = sekolahSelect.dataset.ujianId;
        const kelasSelect = document.querySelector(`.kelas-select-edit[data-ujian-id="${ujianId}"]`);
        
        if (kelasSelect) {
            sekolahSelect.addEventListener('change', function() {
                handleSekolahChange(this, kelasSelect);
            });
        }
    });

    function handleSekolahChange(sekolahSelect, kelasSelect) {
        const sekolahId = sekolahSelect.value;
        
        // Reset dropdown kelas
        kelasSelect.innerHTML = '<option value="">Memuat kelas...</option>';
        kelasSelect.disabled = true;

        if (!sekolahId) {
            kelasSelect.innerHTML = '<option value="">Pilih Sekolah Terlebih Dahulu</option>';
            return;
        }

        // Fetch kelas berdasarkan sekolah
        fetch(`<?= base_url('admin/api/kelas-by-sekolah/') ?>${sekolahId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(responseData => {
                console.log('Response received:', responseData);
                
                // Reset dropdown
                kelasSelect.innerHTML = '';
                
                // Tambahkan opsi default
                const defaultOption = document.createElement('option');
                defaultOption.value = '';
                defaultOption.textContent = 'Pilih Kelas (Kosongkan untuk umum)';
                kelasSelect.appendChild(defaultOption);

                // Periksa apakah response valid
                if (responseData.status === 'success' && Array.isArray(responseData.data)) {
                    if (responseData.data.length > 0) {
                        // Tambahkan opsi kelas
                        responseData.data.forEach(kelas => {
                            const option = document.createElement('option');
                            option.value = kelas.kelas_id;
                            option.textContent = `${kelas.nama_kelas} (${kelas.tahun_ajaran})`;
                            kelasSelect.appendChild(option);
                        });
                    } else {
                        // Tidak ada kelas
                        const noDataOption = document.createElement('option');
                        noDataOption.value = '';
                        noDataOption.textContent = 'Tidak ada kelas tersedia';
                        noDataOption.disabled = true;
                        kelasSelect.appendChild(noDataOption);
                    }
                } else {
                    throw new Error('Format response tidak valid');
                }

                // Aktifkan dropdown
                kelasSelect.disabled = false;
            })
            .catch(error => {
                console.error('Error fetching kelas:', error);
                
                kelasSelect.innerHTML = '<option value="">Gagal memuat kelas</option>';
                kelasSelect.disabled = false;
                
                // Tampilkan pesan error
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-warning alert-dismissible fade show mt-2';
                alertDiv.innerHTML = `
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Gagal memuat data kelas. Silakan coba lagi atau hubungi administrator.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                
                // Insert alert setelah dropdown kelas
                kelasSelect.parentNode.insertBefore(alertDiv, kelasSelect.nextSibling);
                
                // Auto remove alert after 5 seconds
                setTimeout(() => {
                    if (alertDiv.parentNode) {
                        alertDiv.remove();
                    }
                }, 5000);
            });
    }

    // Form validation
    const forms = document.querySelectorAll('form[id^="formTambahUjian"], form[action*="ujian/edit"]');
    forms.forEach(function(form) {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let hasErrors = false;

            requiredFields.forEach(function(field) {
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    hasErrors = true;
                } else {
                    field.classList.remove('is-invalid');
                }
            });

            if (hasErrors) {
                e.preventDefault();
                
                // Scroll to first error
                const firstError = form.querySelector('.is-invalid');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstError.focus();
                }

                // Show error message
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-danger alert-dismissible fade show';
                alertDiv.innerHTML = `
                    <i class="bi bi-exclamation-circle me-2"></i>
                    Harap lengkapi semua field yang wajib diisi.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                
                const modalBody = form.querySelector('.modal-body');
                modalBody.insertBefore(alertDiv, modalBody.firstChild);

                // Auto remove after 5 seconds
                setTimeout(() => {
                    if (alertDiv.parentNode) {
                        alertDiv.remove();
                    }
                }, 5000);
            }
        });
    });

    // Clear form validation on input
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('is-invalid')) {
            if (e.target.value.trim()) {
                e.target.classList.remove('is-invalid');
            }
        }
    });

    // Reset form when modal is closed
    const modals = document.querySelectorAll('.modal');
    modals.forEach(function(modal) {
        modal.addEventListener('hidden.bs.modal', function() {
            const form = modal.querySelector('form');
            if (form) {
                // Remove validation classes
                const invalidFields = form.querySelectorAll('.is-invalid');
                invalidFields.forEach(field => field.classList.remove('is-invalid'));
                
                // Remove alert messages
                const alerts = form.querySelectorAll('.alert');
                alerts.forEach(alert => alert.remove());
                
                // Reset form if it's tambah form
                if (form.id === 'formTambahUjian') {
                    form.reset();
                    
                    // Reset dropdowns
                    const kelasSelect = form.querySelector('#kelas-select-tambah');
                    if (kelasSelect) {
                        kelasSelect.innerHTML = '<option value="">Pilih Sekolah Terlebih Dahulu</option>';
                        kelasSelect.disabled = true;
                    }
                }
            }
        });
    });
});
</script>

<?= $this->endSection() ?>