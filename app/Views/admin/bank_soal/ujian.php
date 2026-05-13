<?= $this->extend('templates/admin/admin_template') ?>

<?= $this->section('content') ?>
<br><br>
<div class="container-fluid py-4">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h2 class="fw-bold text-primary"><?= esc($bankUjian['nama_ujian']) ?></h2>
            <p class="text-muted mb-2">
                <i class="fas fa-tag me-2"></i><?= esc($bankUjian['nama_jenis']) ?> -
                <?= $kategori === 'umum' ? 'Umum' : 'Kelas ' . esc($kategori) ?>
            </p>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="<?= base_url('admin/dashboard') ?>" class="text-decoration-none">
                            <i class="fas fa-home me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="<?= base_url('admin/bank-soal') ?>" class="text-decoration-none">Bank Soal</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="<?= base_url('admin/bank-soal/kategori/' . urlencode($kategori)) ?>" class="text-decoration-none">
                            <?= $kategori === 'umum' ? 'Umum' : 'Kelas ' . esc($kategori) ?>
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="<?= base_url('admin/bank-soal/kategori/' . urlencode($kategori) . '/jenis-ujian/' . $bankUjian['jenis_ujian_id']) ?>" class="text-decoration-none">
                            <?= esc($bankUjian['nama_jenis']) ?>
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <?= esc($bankUjian['nama_ujian']) ?>
                    </li>
                </ol>
            </nav>
        </div>
        <div class="col-auto">
            <div class="d-flex gap-2">
                <a href="<?= base_url('admin/bank-soal/kategori/' . urlencode($kategori) . '/jenis-ujian/' . $bankUjian['jenis_ujian_id']) ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
                <?php if ($canEdit): ?>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahSoal">
                        <i class="fas fa-plus me-2"></i>Tambah Soal
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i><?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Info Bank Ujian -->
    <?php if (!empty($bankUjian['deskripsi'])): ?>
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="fas fa-info-circle me-2"></i><?= esc($bankUjian['deskripsi']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Info Box untuk Panduan Summernote -->
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Panduan Menulis Soal</h6>
        <p class="mb-2">Gunakan editor yang tersedia untuk format teks yang lebih kaya:</p>
        <ul class="mb-2">
            <li><strong>Format teks:</strong> Bold, italic, underline, warna, font size</li>
            <li><strong>Insert gambar:</strong> Klik tombol <i class="fas fa-image"></i> pada toolbar editor untuk upload gambar langsung</li>
            <li><strong>Rumus matematika:</strong> Gunakan superscript (x²) dan subscript (H₂O)</li>
            <li><strong>List:</strong> Bullet points dan numbering</li>
            <li><strong>Tabel:</strong> Untuk data terstruktur</li>
            <li><strong>Upload gambar file:</strong> Gunakan field "Foto Soal" di bawah editor untuk upload file</li>
        </ul>
        <div class="mt-2">
            <small class="text-muted">
                <strong>Tips:</strong>
                Untuk rumus kompleks, gunakan kombinasi superscript/subscript atau jika dibutuhkan, sisipkan sebagai gambar. Contoh: E=mc² bisa dibuat dengan mengetik "E=mc" lalu pilih superscript untuk "2".
                <br><strong>Upload Gambar:</strong> Anda punya 2 cara - (1) Klik tombol <i class="fas fa-image"></i> di toolbar editor untuk insert langsung, atau (2) Upload via field "Foto Soal" di bawah.
            </small>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>

    <?php if (empty($soalList)): ?>
        <!-- Jika belum ada soal -->
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card shadow-sm text-center">
                    <div class="card-body py-5">
                        <div class="mb-4">
                            <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                                <i class="fas fa-question-circle fa-3x text-muted"></i>
                            </div>
                        </div>
                        <h5 class="fw-bold text-muted mb-3">Belum Ada Soal</h5>
                        <p class="text-muted mb-4">
                            Belum ada soal yang dibuat untuk bank ujian
                            <strong><?= esc($bankUjian['nama_ujian']) ?></strong>
                        </p>
                        <?php if ($canEdit): ?>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahSoal">
                                <i class="fas fa-plus me-2"></i>Tambah Soal Pertama
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Daftar Soal -->
        <div class="card shadow-sm">
            <div class="card-header bg-light border-0">
                <div class="d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0 fw-bold">
                        <i class="fas fa-list me-2 text-primary"></i>Daftar Soal
                    </h5>
                    <span class="badge bg-primary"><?= count($soalList) ?> soal</span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="5%" class="px-3">No</th>
                                <th width="12%">Kode Soal</th>
                                <th width="35%">Pertanyaan</th>
                                <th width="8%" class="text-center">Jawaban</th>
                                <th width="10%" class="text-center">Kesulitan</th>
                                <th width="8%" class="text-center">Foto</th>
                                <th width="12%" class="text-center">Dibuat</th>
                                <?php if ($canEdit): ?>
                                    <th width="22%" class="text-center">Aksi</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($soalList as $index => $soal): ?>
                                <tr>
                                    <td class="px-3 fw-semibold"><?= $index + 1 ?></td>
                                    <td class="fw-bold text-primary"><?= esc($soal['kode_soal']) ?></td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 300px;" title="<?= esc(strip_tags($soal['pertanyaan'])) ?>">
                                            <?= strip_tags($soal['pertanyaan']) ?>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary fs-6"><?= esc($soal['jawaban_benar']) ?></span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-<?= $soal['tingkat_kesulitan'] <= -1 ? 'success' : ($soal['tingkat_kesulitan'] <= 1 ? 'warning' : 'danger') ?>">
                                            <?= number_format($soal['tingkat_kesulitan'], 3) ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <?php if (!empty($soal['foto'])): ?>
                                            <i class="fas fa-image text-success" title="Ada foto"></i>
                                        <?php else: ?>
                                            <i class="fas fa-minus text-muted" title="Tidak ada foto"></i>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <small class="text-muted">
                                            <?= date('d/m/Y', strtotime($soal['created_at'])) ?>
                                        </small>
                                    </td>
                                    <?php if ($canEdit): ?>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="fw-bold btn btn-info" data-bs-toggle="modal" data-bs-target="#detailModal<?= $soal['soal_id'] ?>" title="Lihat Detail">
                                                    <i class="fas fa-eye me-1"></i>Detail
                                                </button>
                                                <button type="button" class="fw-bold btn btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $soal['soal_id'] ?>" title="Edit">
                                                    <i class="fas fa-edit me-1"></i>Edit
                                                </button>
                                                <button type="button" class="fw-bold btn btn-danger" onclick="hapusSoal(<?= $soal['soal_id'] ?>)" title="Hapus">
                                                    <i class="fas fa-trash me-1"></i>Hapus
                                                </button>
                                            </div>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Modal Tambah Soal -->
<?php if ($canEdit): ?>
    <div class="modal fade" id="modalTambahSoal" tabindex="-1" data-bs-focus="false">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title fw-bold"><i class="fas fa-plus-circle me-2"></i>Tambah Soal Bank</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="<?= base_url('admin/bank-soal/tambah-soal') ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <input type="hidden" name="bank_ujian_id" value="<?= $bankUjian['bank_ujian_id'] ?>">
                    <div class="modal-body">
                        <!-- Kode Soal Section -->
                        <div class="card mb-3 shadow-sm">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-code text-warning me-2"></i>Kode Soal</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Kode Soal <span class="text-danger">*</span></label>
                                        <input type="text" name="kode_soal" class="form-control form-control-lg"
                                            placeholder="Contoh: MAT001, FIS002" required>
                                        <small class="text-muted">Masukkan kode unik untuk soal ini</small>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mt-4">
                                            <div class="alert alert-warning py-2 mb-0">
                                                <small>
                                                    <strong>Format Kode:</strong><br>
                                                    • 3-50 karakter<br>
                                                    • Boleh huruf, angka, dan simbol<br>
                                                    • Harus unik (tidak boleh sama)
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pertanyaan Section -->
                        <div class="card mb-3 shadow-sm">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-question-circle text-primary me-2"></i>Pertanyaan Soal</h6>
                            </div>
                            <div class="card-body">
                                <textarea name="pertanyaan" id="pertanyaan_tambah" class="form-control summernote" required placeholder="Masukkan pertanyaan soal..."></textarea>

                                <div class="mt-3">
                                    <label class="form-label"><i class="fas fa-image text-secondary me-1"></i>Foto Soal (Opsional)</label>
                                    <input type="file" name="foto" class="form-control" accept=".jpg,.jpeg,.png,.gif">
                                    <small class="text-muted">
                                        Upload gambar dengan format JPG, JPEG, PNG, atau GIF (maks. 2MB).
                                        <br><strong>Tips:</strong> Anda juga bisa langsung insert gambar di editor dengan klik tombol <i class="fas fa-image"></i> pada toolbar.
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Pilihan Jawaban Section -->
                        <div class="card mb-3 shadow-sm">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-list text-success me-2"></i>Pilihan Jawaban</h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-primary">A.</label>
                                        <textarea name="pilihan_a" id="pilihan_a_tambah" class="form-control summernote-small" required></textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-primary">B.</label>
                                        <textarea name="pilihan_b" id="pilihan_b_tambah" class="form-control summernote-small" required></textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-primary">C.</label>
                                        <textarea name="pilihan_c" id="pilihan_c_tambah" class="form-control summernote-small" required></textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-primary">D.</label>
                                        <textarea name="pilihan_d" id="pilihan_d_tambah" class="form-control summernote-small" required></textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-warning">E. (Opsional)</label>
                                        <textarea name="pilihan_e" id="pilihan_e_tambah" class="form-control summernote-small"></textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold"><i class="fas fa-check-circle text-success me-1"></i>Jawaban Benar</label>
                                        <select name="jawaban_benar" class="form-select form-select-lg" required>
                                            <option value="">Pilih Jawaban Benar</option>
                                            <option value="A">A</option>
                                            <option value="B">B</option>
                                            <option value="C">C</option>
                                            <option value="D">D</option>
                                            <option value="E">E</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pengaturan Soal Section -->
                        <div class="card mb-3 shadow-sm">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-cogs text-warning me-2"></i>Pengaturan Soal</h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold"><i class="fas fa-balance-scale text-info me-1"></i>Tingkat Kesulitan</label>
                                        <div class="input-group">
                                            <input type="number" name="tingkat_kesulitan" class="form-control form-control-lg" step="0.001" value="0.000" min="-3" max="3" required>
                                            <span class="input-group-text">(-3 hingga +3)</span>
                                        </div>
                                        <small class="text-muted">Negatif = mudah, Positif = sulit, 0 = sedang</small>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mt-4">
                                            <div class="alert alert-info py-2 mb-0">
                                                <small>
                                                    <strong>Panduan Tingkat Kesulitan:</strong><br>
                                                    -3.000 hingga -1.000 = Mudah<br>
                                                    -0.999 hingga +0.999 = Sedang<br>
                                                    +1.000 hingga +3.000 = Sulit
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pembahasan Section -->
                        <div class="card shadow-sm">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-lightbulb text-info me-2"></i>Pembahasan (Opsional)</h6>
                            </div>
                            <div class="card-body">
                                <textarea name="pembahasan" id="pembahasan_tambah" class="form-control summernote" placeholder="Masukkan pembahasan soal..."></textarea>
                                <small class="text-muted mt-2 d-block">Pembahasan akan ditampilkan kepada siswa setelah menyelesaikan ujian</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Batal
                        </button>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save me-1"></i>Simpan Soal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Modal Detail Soal -->
<?php foreach ($soalList as $soal): ?>
    <div class="modal fade" id="detailModal<?= $soal['soal_id'] ?>" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold">Detail Soal: <?= esc($soal['kode_soal']) ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Kode Soal:</strong> <span class="badge bg-primary ms-2"><?= esc($soal['kode_soal']) ?></span>
                        </div>
                        <div class="col-md-6">
                            <strong>Jawaban Benar:</strong> <span class="badge bg-success ms-2"><?= $soal['jawaban_benar'] ?></span>
                        </div>
                        <div class="col-md-6">
                            <strong>Tingkat Kesulitan:</strong> <span class="badge bg-info ms-2"><?= number_format($soal['tingkat_kesulitan'], 3) ?></span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <strong>Pertanyaan:</strong>
                        <div class="p-3 bg-light rounded"><?= $soal['pertanyaan'] ?></div>
                    </div>

                    <?php if (!empty($soal['foto'])): ?>
                        <div class="text-center mb-3">
                            <img src="<?= base_url('uploads/soal/' . $soal['foto']) ?>" alt="Foto Soal" class="img-fluid rounded shadow-sm" style="max-height: 200px;">
                        </div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <strong>Pilihan Jawaban:</strong>
                        <div class="mt-2">
                            <div class="d-flex mb-2"><span class="badge bg-primary me-2">A</span> <?= $soal['pilihan_a'] ?></div>
                            <div class="d-flex mb-2"><span class="badge bg-primary me-2">B</span> <?= $soal['pilihan_b'] ?></div>
                            <div class="d-flex mb-2"><span class="badge bg-primary me-2">C</span> <?= $soal['pilihan_c'] ?></div>
                            <div class="d-flex mb-2"><span class="badge bg-primary me-2">D</span> <?= $soal['pilihan_d'] ?></div>
                            <?php if (!empty($soal['pilihan_e'])): ?>
                                <div class="d-flex mb-2"><span class="badge bg-primary me-2">E</span> <?= $soal['pilihan_e'] ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if (!empty($soal['pembahasan'])): ?>
                        <div class="card bg-light">
                            <div class="card-header"><strong>Pembahasan</strong></div>
                            <div class="card-body"><?= $soal['pembahasan'] ?></div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<!-- Modal Edit Soal -->
<?php foreach ($soalList as $soal): ?>
    <div class="modal fade" id="editModal<?= $soal['soal_id'] ?>" tabindex="-1" data-bs-focus="false" data-bs-keyboard="false">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark border-0">
                    <h5 class="modal-title fw-bold"><i class="fas fa-edit me-2"></i>Edit Soal #<?= $soal['soal_id'] ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="<?= base_url('admin/bank-soal/edit-soal/' . $soal['soal_id']) ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="modal-body">
                        <!-- Kode Soal Section -->
                        <div class="card mb-3 shadow-sm">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-code text-warning me-2"></i>Kode Soal</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Kode Soal <span class="text-danger">*</span></label>
                                        <input type="text" name="kode_soal" class="form-control"
                                            value="<?= esc($soal['kode_soal']) ?>" required>
                                        <small class="text-muted">Kode unik untuk soal ini</small>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mt-4">
                                            <div class="alert alert-warning py-2 mb-0">
                                                <small>
                                                    <strong>Format Kode:</strong><br>
                                                    • 3-50 karakter<br>
                                                    • Boleh huruf, angka, dan simbol<br>
                                                    • Harus unik (tidak boleh sama)
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pertanyaan Section -->
                        <div class="card mb-3 shadow-sm">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-question-circle text-primary me-2"></i>Pertanyaan Soal</h6>
                            </div>
                            <div class="card-body">
                                <textarea name="pertanyaan" id="pertanyaan_edit_<?= $soal['soal_id'] ?>" class="form-control summernote" required><?= esc($soal['pertanyaan']) ?></textarea>

                                <div class="mt-3">
                                    <label class="form-label"><i class="fas fa-image text-secondary me-1"></i>Foto Soal</label>
                                    <?php if (!empty($soal['foto'])): ?>
                                        <div class="mb-2">
                                            <img src="<?= base_url('uploads/soal/' . $soal['foto']) ?>" alt="Foto Soal" class="img-thumbnail" style="max-height: 200px;">
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="hapus_foto" id="hapusFoto<?= $soal['soal_id'] ?>" value="1">
                                            <label class="form-check-label" for="hapusFoto<?= $soal['soal_id'] ?>">
                                                Hapus foto yang ada
                                            </label>
                                        </div>
                                    <?php endif; ?>
                                    <input type="file" name="foto" class="form-control" accept=".jpg,.jpeg,.png,.gif">
                                    <small class="text-muted">
                                        Upload gambar baru dengan format JPG, JPEG, PNG, atau GIF (maks. 2MB).
                                        <br><strong>Tips:</strong> Anda juga bisa langsung insert gambar di editor dengan klik tombol <i class="fas fa-image"></i> pada toolbar.
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Pilihan Jawaban Section -->
                        <div class="card mb-3 shadow-sm">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-list text-success me-2"></i>Pilihan Jawaban</h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-primary">A.</label>
                                        <textarea name="pilihan_a" id="pilihan_a_edit_<?= $soal['soal_id'] ?>" class="form-control summernote-small" required><?= esc($soal['pilihan_a']) ?></textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-primary">B.</label>
                                        <textarea name="pilihan_b" id="pilihan_b_edit_<?= $soal['soal_id'] ?>" class="form-control summernote-small" required><?= esc($soal['pilihan_b']) ?></textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-primary">C.</label>
                                        <textarea name="pilihan_c" id="pilihan_c_edit_<?= $soal['soal_id'] ?>" class="form-control summernote-small" required><?= esc($soal['pilihan_c']) ?></textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-primary">D.</label>
                                        <textarea name="pilihan_d" id="pilihan_d_edit_<?= $soal['soal_id'] ?>" class="form-control summernote-small" required><?= esc($soal['pilihan_d']) ?></textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-warning">E. (Opsional)</label>
                                        <textarea name="pilihan_e" id="pilihan_e_edit_<?= $soal['soal_id'] ?>" class="form-control summernote-small"><?= isset($soal['pilihan_e']) ? esc($soal['pilihan_e']) : '' ?></textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold"><i class="fas fa-check-circle text-success me-1"></i>Jawaban Benar</label>
                                        <select name="jawaban_benar" class="form-select form-select-lg" required>
                                            <option value="">Pilih Jawaban Benar</option>
                                            <option value="A" <?= $soal['jawaban_benar'] == 'A' ? 'selected' : '' ?>>A</option>
                                            <option value="B" <?= $soal['jawaban_benar'] == 'B' ? 'selected' : '' ?>>B</option>
                                            <option value="C" <?= $soal['jawaban_benar'] == 'C' ? 'selected' : '' ?>>C</option>
                                            <option value="D" <?= $soal['jawaban_benar'] == 'D' ? 'selected' : '' ?>>D</option>
                                            <option value="E" <?= $soal['jawaban_benar'] == 'E' ? 'selected' : '' ?>>E</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pengaturan Soal Section -->
                        <div class="card mb-3 shadow-sm">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-cogs text-warning me-2"></i>Pengaturan Soal</h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold"><i class="fas fa-balance-scale text-info me-1"></i>Tingkat Kesulitan</label>
                                        <div class="input-group">
                                            <input type="number" name="tingkat_kesulitan" class="form-control" step="0.001" value="<?= $soal['tingkat_kesulitan'] ?>" min="-3" max="3" required>
                                            <span class="input-group-text">(-3 hingga +3)</span>
                                        </div>
                                        <small class="text-muted">Negatif = mudah, Positif = sulit, 0 = sedang</small>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mt-4">
                                            <div class="alert alert-info py-2 mb-0">
                                                <small>
                                                    <strong>Panduan Tingkat Kesulitan:</strong><br>
                                                    -3.000 hingga -1.000 = Mudah<br>
                                                    -0.999 hingga +0.999 = Sedang<br>
                                                    +1.000 hingga +3.000 = Sulit
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pembahasan Section -->
                        <div class="card shadow-sm">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-lightbulb text-info me-2"></i>Pembahasan (Opsional)</h6>
                            </div>
                            <div class="card-body">
                                <textarea name="pembahasan" id="pembahasan_edit_<?= $soal['soal_id'] ?>" class="form-control summernote"><?= isset($soal['pembahasan']) ? esc($soal['pembahasan']) : '' ?></textarea>
                                <small class="text-muted mt-2 d-block">Pembahasan akan ditampilkan kepada siswa setelah menyelesaikan ujian</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Batal
                        </button>
                        <button type="submit" class="btn btn-warning btn-lg">
                            <i class="fas fa-save me-1"></i>Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<!-- Load jQuery, Bootstrap 5, dan Summernote BS5 -->
<script src="//code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" />
<script src="//cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Include Summernote BS5 -->
<link href="//cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-bs5.min.css" rel="stylesheet">
<script src="//cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-bs5.min.js"></script>

<script>
    // Konfigurasi Summernote UMUM yang sederhana dan stabil
    const summernoteConfig = {
        height: 200,
        toolbar: [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough', 'superscript', 'subscript']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'picture']],
            ['view', ['fullscreen', 'codeview']]
        ],
        placeholder: 'Masukkan teks di sini...',
        dialogsInBody: true,
        dialogsFade: true,
        disableDragAndDrop: false,
        container: 'body',
        callbacks: {
            onImageUpload: function(files) {
                console.log('onImageUpload triggered with files:', files);
                // Pastikan hanya file pertama yang diproses dan tidak ada multiple upload
                if (files && files.length > 0 && !$(this).data('uploading')) {
                    uploadImageSimple(files[0], this);
                }
            },
            onInit: function() {
                const $editor = $(this);
                const editorId = $editor.attr('id');
                console.log('Summernote initialized for:', editorId);

                // Reset upload flag
                $editor.data('uploading', false);

                // HANYA handle event fullscreen yang benar
                $editor.on('summernote.fullscreen', function(e, isFullscreen) {
                    if (isFullscreen) {
                        console.log('Entering fullscreen for:', editorId);
                        handleEnterFullscreen($editor);
                    } else {
                        console.log('Exiting fullscreen for:', editorId);
                        handleExitFullscreen($editor);
                    }
                });

                // Fix dropdown events dengan delay
                setTimeout(() => {
                    fixDropdownEvents($editor);
                }, 500);
            },
            onBlur: function() {
                const $editor = $(this);
                const $toolbar = $editor.siblings('.note-toolbar');
                setTimeout(() => {
                    $toolbar.find('.dropdown-menu').removeClass('show').hide();
                }, 150);
            }
        }
    };

    // Konfigurasi Summernote untuk pilihan (lebih kecil)
    const summernoteConfigSmall = {
        height: 120,
        toolbar: [
            ['style', ['bold', 'italic', 'underline']],
            ['font', ['superscript', 'subscript']],
            ['color', ['color']],
            ['insert', ['picture']]
        ],
        placeholder: 'Masukkan pilihan...',
        dialogsInBody: true,
        dialogsFade: true,
        container: 'body',
        callbacks: {
            onImageUpload: function(files) {
                console.log('onImageUpload triggered (small editor) with files:', files);
                // Pastikan hanya file pertama yang diproses dan tidak ada multiple upload
                if (files && files.length > 0 && !$(this).data('uploading')) {
                    uploadImageSimple(files[0], this);
                }
            },
            onInit: function() {
                const $editor = $(this);
                const editorId = $editor.attr('id');
                console.log('Summernote (small) initialized for:', editorId);

                // Reset upload flag
                $editor.data('uploading', false);

                // Fix dropdown events dengan delay
                setTimeout(() => {
                    fixDropdownEvents($editor);
                }, 500);
            },
            onBlur: function() {
                setTimeout(cleanupModalBackdrop, 10);
            }
        }
    };

    // Fungsi untuk handle masuk fullscreen
    function handleEnterFullscreen($editor) {
        // Hide semua elemen yang bisa mengganggu
        $('.modal').not('.note-modal').addClass('summernote-hidden');
        $('.note-editor').not($editor.siblings('.note-editor')).addClass('summernote-hidden');
        $('body').addClass('summernote-fullscreen-active');

        // Set z-index yang sangat tinggi
        $editor.siblings('.note-editor').css({
            'position': 'fixed',
            'top': '0',
            'left': '0',
            'right': '0',
            'bottom': '0',
            'z-index': '99999',
            'background': '#fff'
        });
    }

    // Fungsi untuk handle keluar fullscreen
    function handleExitFullscreen($editor) {
        // Restore semua elemen
        $('.summernote-hidden').removeClass('summernote-hidden');
        $('body').removeClass('summernote-fullscreen-active');

        // Reset styling
        $editor.siblings('.note-editor').css({
            'position': 'relative',
            'top': 'auto',
            'left': 'auto',
            'right': 'auto',
            'bottom': 'auto',
            'z-index': 'auto',
            'background': 'transparent'
        });

        // Clean up any remaining backdrops
        setTimeout(() => {
            cleanupModalBackdrop();
        }, 100);
    }

    // Fungsi untuk fix dropdown events
    function fixDropdownEvents($editor) {
        const $toolbar = $editor.siblings('.note-toolbar');

        // Re-attach dropdown events
        $toolbar.find('.dropdown-toggle').off('click.bs.dropdown').on('click.bs.dropdown', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const $this = $(this);
            const $menu = $this.siblings('.dropdown-menu');

            // Close other dropdowns
            $toolbar.find('.dropdown-menu').not($menu).removeClass('show').hide();

            // Toggle current dropdown
            if ($menu.hasClass('show')) {
                $menu.removeClass('show').hide();
            } else {
                $menu.addClass('show').show();

                // Ensure proper positioning
                const buttonOffset = $this.offset();
                const buttonHeight = $this.outerHeight();

                $menu.css({
                    'position': 'absolute',
                    'top': buttonHeight + 'px',
                    'left': '0px',
                    'z-index': '99999',
                    'display': 'block'
                });
            }
        });

        // Close dropdowns when clicking outside
        $(document).off('click.summernote-dropdown').on('click.summernote-dropdown', function(e) {
            if (!$(e.target).closest('.note-toolbar').length) {
                $toolbar.find('.dropdown-menu').removeClass('show').hide();
            }
        });

        // Special handling untuk berbagai jenis dropdown
        handleSpecialDropdowns($toolbar);
    }

    // Handle dropdown khusus (color, table, paragraph)
    function handleSpecialDropdowns($toolbar) {
        // Color picker
        $toolbar.find('.note-color').each(function() {
            const $colorBtn = $(this);
            $colorBtn.off('click').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const $menu = $colorBtn.find('.dropdown-menu');
                $('.note-toolbar .dropdown-menu').not($menu).removeClass('show').hide();

                if ($menu.hasClass('show')) {
                    $menu.removeClass('show').hide();
                } else {
                    $menu.addClass('show').show().css({
                        'position': 'absolute',
                        'z-index': '99999',
                        'display': 'block'
                    });
                }
            });
        });

        // Table
        $toolbar.find('.note-table').each(function() {
            const $tableBtn = $(this);
            $tableBtn.find('button').off('click').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const $menu = $tableBtn.find('.dropdown-menu');
                $('.note-toolbar .dropdown-menu').not($menu).removeClass('show').hide();

                if ($menu.hasClass('show')) {
                    $menu.removeClass('show').hide();
                } else {
                    $menu.addClass('show').show().css({
                        'position': 'absolute',
                        'z-index': '99999',
                        'display': 'block'
                    });
                }
            });
        });

        // Paragraph/style
        $toolbar.find('.note-para').each(function() {
            const $paraBtn = $(this);
            $paraBtn.find('button').off('click').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const $menu = $paraBtn.find('.dropdown-menu');
                $('.note-toolbar .dropdown-menu').not($menu).removeClass('show').hide();

                if ($menu.hasClass('show')) {
                    $menu.removeClass('show').hide();
                } else {
                    $menu.addClass('show').show().css({
                        'position': 'absolute',
                        'z-index': '99999',
                        'display': 'block'
                    });
                }
            });
        });
    }

    // Fungsi untuk membersihkan semua modal backdrop yang mungkin tertinggal
    function cleanupModalBackdrop() {
        // Remove semua modal backdrop yang tertinggal
        $('.modal-backdrop').remove();
        $('.note-modal-backdrop').remove();

        // Reset body classes dan styles
        if ($('.modal.show').length === 0) {
            $('body').removeClass('modal-open summernote-fullscreen-active');
            $('body').css({
                'padding-right': '',
                'overflow': ''
            });
        }
    }

    // Function upload gambar yang robust dan sederhana
    function uploadImageSimple(file, editor) {
        if (!file.type.startsWith('image/')) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Upload',
                    text: 'File yang dipilih bukan gambar!',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });
            } else {
                alert('Pilih file gambar!');
            }
            return;
        }

        if (file.size > 2 * 1024 * 1024) { // 2MB
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Upload',
                    text: 'Ukuran file terlalu besar! Maksimal 2MB.',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });
            } else {
                alert('File terlalu besar! Maksimal 2MB.');
            }
            return;
        }

        const formData = new FormData();
        formData.append('upload', file);

        const $editor = $(editor);

        // Pastikan editor masih ada dan aktif
        if (!$editor.length || !$editor.data('summernote')) {
            console.error('Editor tidak tersedia untuk upload gambar');
            return;
        }

        // Cegah multiple upload
        if ($editor.data('uploading')) {
            return;
        }

        $editor.data('uploading', true);

        // Show loading indicator sederhana
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Uploading...',
                text: 'Sedang mengupload gambar',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        }

        $.ajax({
            url: '<?= base_url('admin/upload-summernote-image') ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            timeout: 30000,
            success: function(response) {
                // Reset flag
                $editor.data('uploading', false);

                // Close loading
                if (typeof Swal !== 'undefined') {
                    Swal.close();
                }

                if (response.success && response.url) {
                    // Metode sederhana: insert gambar di akhir content
                    insertImageToEditor($editor, response.url);

                    // Show success notification
                    if (typeof Swal !== 'undefined') {
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true
                        });
                        Toast.fire({
                            icon: 'success',
                            title: 'Gambar berhasil diupload!'
                        });
                    }

                } else {
                    console.error('Upload response error:', response);

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Upload Gagal',
                            text: response.error || 'Response tidak valid dari server'
                        });
                    } else {
                        alert('Upload gagal: ' + (response.error || 'Response tidak valid'));
                    }
                }
            },
            error: function(xhr, status, error) {
                // Reset flag
                $editor.data('uploading', false);

                // Close loading
                if (typeof Swal !== 'undefined') {
                    Swal.close();
                }

                console.error('Upload error:', {
                    xhr,
                    status,
                    error
                });

                let errorMessage = 'Terjadi kesalahan saat upload gambar.';
                if (status === 'timeout') {
                    errorMessage = 'Upload timeout. Silakan coba gambar yang lebih kecil.';
                } else if (xhr.status === 413) {
                    errorMessage = 'File terlalu besar. Maksimal 2MB.';
                } else if (xhr.status === 0) {
                    errorMessage = 'Koneksi terputus. Periksa koneksi internet Anda.';
                }

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Upload Gagal',
                        text: errorMessage
                    });
                } else {
                    alert('Gagal upload gambar: ' + errorMessage);
                }
            }
        });
    }

    // Fungsi helper untuk insert gambar ke editor dengan berbagai fallback
    function insertImageToEditor($editor, imageUrl) {
        try {
            // Method 1: Coba insert menggunakan insertImage API
            $editor.summernote('focus');
            $editor.summernote('insertImage', imageUrl, function($img) {
                $img.css({
                    'max-width': '100%',
                    'height': 'auto',
                    'display': 'block',
                    'margin': '10px 0'
                });
                $img.addClass('img-fluid');
            });

            console.log('Gambar berhasil diinsert dengan method 1');
            return;

        } catch (e1) {
            console.warn('Method 1 gagal, coba method 2:', e1);

            try {
                // Method 2: Insert HTML langsung
                const imageHtml = `<div style="margin: 10px 0;"><img src="${imageUrl}" class="img-fluid" style="max-width: 100%; height: auto; display: block;"></div>`;
                $editor.summernote('pasteHTML', imageHtml);

                console.log('Gambar berhasil diinsert dengan method 2');
                return;

            } catch (e2) {
                console.warn('Method 2 gagal, coba method 3:', e2);

                try {
                    // Method 3: Append ke existing content
                    const currentContent = $editor.summernote('code') || '';
                    const imageHtml = `<div style="margin: 10px 0;"><img src="${imageUrl}" class="img-fluid" style="max-width: 100%; height: auto; display: block;"></div>`;
                    const newContent = currentContent + imageHtml;
                    $editor.summernote('code', newContent);

                    console.log('Gambar berhasil diinsert dengan method 3');
                    return;

                } catch (e3) {
                    console.warn('Method 3 gagal, coba method 4:', e3);

                    try {
                        // Method 4: Direct manipulation textarea value
                        const $textarea = $editor;
                        const currentValue = $textarea.val() || '';
                        const imageHtml = `<div style="margin: 10px 0;"><img src="${imageUrl}" class="img-fluid" style="max-width: 100%; height: auto; display: block;"></div>`;
                        $textarea.val(currentValue + imageHtml);

                        // Trigger summernote to refresh
                        if ($editor.data('summernote')) {
                            $editor.summernote('code', $textarea.val());
                        }

                        console.log('Gambar berhasil diinsert dengan method 4');
                        return;

                    } catch (e4) {
                        console.error('Semua method gagal:', e4);

                        // Method 5: Show URL untuk copy manual
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                title: 'Upload Berhasil',
                                html: `Gambar berhasil diupload. Silakan copy URL ini dan paste manual ke editor:<br><br><strong>${imageUrl}</strong>`,
                                icon: 'info',
                                confirmButtonText: 'OK'
                            });
                        } else {
                            alert('Gambar berhasil diupload. URL: ' + imageUrl + '\n\nSilakan copy dan paste manual ke editor.');
                        }
                    }
                }
            }
        }
    }

    // Initialize Summernote untuk modal tambah
    function initializeSummernoteAdd() {
        // Destroy existing instances terlebih dahulu
        destroySummernoteInstances([
            '#pertanyaan_tambah',
            '#pilihan_a_tambah',
            '#pilihan_b_tambah',
            '#pilihan_c_tambah',
            '#pilihan_d_tambah',
            '#pilihan_e_tambah',
            '#pembahasan_tambah'
        ]);

        // Delay untuk memastikan modal sudah fully rendered
        setTimeout(() => {
            // Initialize dengan config yang sudah diperbaiki
            $('#pertanyaan_tambah').summernote(summernoteConfig);
            $('#pilihan_a_tambah').summernote(summernoteConfigSmall);
            $('#pilihan_b_tambah').summernote(summernoteConfigSmall);
            $('#pilihan_c_tambah').summernote(summernoteConfigSmall);
            $('#pilihan_d_tambah').summernote(summernoteConfigSmall);
            $('#pilihan_e_tambah').summernote(summernoteConfigSmall);
            $('#pembahasan_tambah').summernote(summernoteConfig);

        }, 200);
    }

    // Initialize Summernote untuk modal edit
    function initializeSummernoteEdit(soalId) {
        const editorIds = [
            '#pertanyaan_edit_' + soalId,
            '#pilihan_a_edit_' + soalId,
            '#pilihan_b_edit_' + soalId,
            '#pilihan_c_edit_' + soalId,
            '#pilihan_d_edit_' + soalId,
            '#pilihan_e_edit_' + soalId,
            '#pembahasan_edit_' + soalId
        ];

        destroySummernoteInstances(editorIds);

        setTimeout(() => {
            $('#pertanyaan_edit_' + soalId).summernote(summernoteConfig);
            $('#pilihan_a_edit_' + soalId).summernote(summernoteConfigSmall);
            $('#pilihan_b_edit_' + soalId).summernote(summernoteConfigSmall);
            $('#pilihan_c_edit_' + soalId).summernote(summernoteConfigSmall);
            $('#pilihan_d_edit_' + soalId).summernote(summernoteConfigSmall);
            $('#pilihan_e_edit_' + soalId).summernote(summernoteConfigSmall);
            $('#pembahasan_edit_' + soalId).summernote(summernoteConfig);

        }, 200);
    }

    // Destroy Summernote instances
    function destroySummernoteInstances(editorIds) {
        editorIds.forEach(id => {
            const $editor = $(id);
            if ($editor.length && $editor.data('summernote')) {
                try {
                    // Close any open dropdowns first
                    $editor.siblings('.note-toolbar').find('.dropdown-menu').removeClass('show').hide();

                    // Destroy summernote
                    $editor.summernote('destroy');

                    // Clean up any remaining summernote elements
                    $editor.siblings('.note-editor').remove();
                    $editor.show(); // Make sure original textarea is visible

                } catch (e) {
                    console.warn('Error destroying summernote instance:', id, e);
                    // Force cleanup
                    $editor.siblings('.note-editor').remove();
                    $editor.show();
                }
            }
        });

        // Final cleanup
        cleanupModalBackdrop();
    }

    // Document ready
    $(document).ready(function() {
        console.log('Document ready - initializing Summernote handlers');

        // Tambahkan CSS untuk fix fullscreen
        $('<style>')
            .prop('type', 'text/css')
            .html(`
            .summernote-hidden {
                display: none !important;
            }
            
            body.summernote-fullscreen-active {
                overflow: hidden !important;
            }
            
            body.summernote-fullscreen-active .main-sidebar,
            body.summernote-fullscreen-active .main-header,
            body.summernote-fullscreen-active .main-footer {
                display: none !important;
            }
            
            .note-toolbar .dropdown-menu {
                z-index: 99999 !important;
                position: absolute !important;
            }
            
            .note-toolbar .dropdown-menu.show {
                display: block !important;
            }
            
            .note-color .dropdown-menu,
            .note-table .dropdown-menu,
            .note-para .dropdown-menu,
            .note-fontsize .dropdown-menu {
                min-width: 200px;
                max-height: 300px;
                overflow-y: auto;
            }
            
            /* Fix untuk table dimension picker */
            .note-table .note-dimension-picker {
                position: relative !important;
                z-index: 99999 !important;
            }
            
            .note-table .note-dimension-picker .note-dimension-picker-mousecatcher {
                position: absolute !important;
                z-index: 99999 !important;
                cursor: pointer !important;
            }
        `)
            .appendTo('head');

        // === EVENT HANDLERS UNTUK SUMMERNOTE ===

        // Event listener untuk modal tambah soal
        $('#modalTambahSoal').on('shown.bs.modal', function() {
            console.log('Modal tambah soal dibuka');
            initializeSummernoteAdd();
        });

        $('#modalTambahSoal').on('hidden.bs.modal', function() {
            console.log('Modal tambah soal ditutup');
            destroySummernoteInstances([
                '#pertanyaan_tambah',
                '#pilihan_a_tambah',
                '#pilihan_b_tambah',
                '#pilihan_c_tambah',
                '#pilihan_d_tambah',
                '#pilihan_e_tambah',
                '#pembahasan_tambah'
            ]);
            cleanupModalBackdrop();
        });

        // Event listener untuk modal edit (gunakan loop untuk setiap soal)
        <?php foreach ($soalList as $s): ?>
            $('#editModal<?= $s['soal_id'] ?>').on('shown.bs.modal', function() {
                initializeSummernoteEdit(<?= $s['soal_id'] ?>);
            });

            $('#editModal<?= $s['soal_id'] ?>').on('hidden.bs.modal', function() {
                destroySummernoteInstances([
                    '#pertanyaan_edit_<?= $s['soal_id'] ?>',
                    '#pilihan_a_edit_<?= $s['soal_id'] ?>',
                    '#pilihan_b_edit_<?= $s['soal_id'] ?>',
                    '#pilihan_c_edit_<?= $s['soal_id'] ?>',
                    '#pilihan_d_edit_<?= $s['soal_id'] ?>',
                    '#pilihan_e_edit_<?= $s['soal_id'] ?>',
                    '#pembahasan_edit_<?= $s['soal_id'] ?>'
                ]);
                cleanupModalBackdrop();
            });
        <?php endforeach; ?>

        // Handle cleanup saat modal ditutup tanpa save
        $('#modalTambahSoal').on('hidden.bs.modal', function() {
            if (!$(this).data('form-submitted')) {
                $.ajax({
                    url: '<?= base_url('admin/cleanup-temp-images') ?>',
                    type: 'POST',
                    silent: true,
                    error: function() {}
                });
            }
            $(this).data('form-submitted', false);
        });

        // Handle cleanup untuk modal edit
        <?php foreach ($soalList as $s): ?>
            $('#editModal<?= $s['soal_id'] ?>').on('hidden.bs.modal', function() {
                if (!$(this).data('form-submitted')) {
                    $.ajax({
                        url: '<?= base_url('admin/cleanup-temp-images') ?>',
                        type: 'POST',
                        silent: true,
                        error: function() {}
                    });
                }
                $(this).data('form-submitted', false);
            });
        <?php endforeach; ?>

        // Set flag ketika form di-submit
        $('form').on('submit', function() {
            const modalId = $(this).closest('.modal').attr('id');
            if (modalId) {
                $('#' + modalId).data('form-submitted', true);
            }

            // Event listener untuk memastikan Summernote menyimpan data terbaru ke textarea
            $('.summernote, .summernote-small').each(function() {
                if ($(this).data('summernote')) {
                    const content = $(this).summernote('code');
                    $(this).val(content);
                }
            });
        });

        // Prevent modal from auto-focusing yang bisa mengganggu Summernote
        $('.modal').on('shown.bs.modal', function() {
            $(this).removeAttr('tabindex');
        });
    });

    // Function helper untuk extract images dari HTML content
    function extractImagesFromContent(htmlContent) {
        const images = [];
        if (!htmlContent) return images;

        const tempDiv = $('<div>').html(htmlContent);
        tempDiv.find('img').each(function() {
            const src = $(this).attr('src');
            if (src && src.includes('uploads/editor-images/')) {
                const filename = src.split('/').pop();
                if (filename) {
                    images.push(filename);
                }
            }
        });
        return images;
    }

    // Function untuk hapus soal
    function hapusSoal(soalId) {
        if (confirm('Apakah Anda yakin ingin menghapus soal ini? Tindakan ini tidak dapat dibatalkan.')) {
            window.location.href = '<?= base_url('admin/bank-soal/hapus-soal/') ?>' + soalId;
        }
    }
</script>

<style>
    /* CSS FIX UNTUK MASALAH SUMMERNOTE - VERSI OPTIMIZED */

    /* Basic z-index for Bootstrap modals and their backdrops */
    .modal-backdrop {
        z-index: 1040;
    }

    .modal {
        z-index: 1050;
    }

    .note-popover {
        z-index: 2060;
        /* Popovers (like image/table tools) above Summernote modals */
    }

    .note-toolbar {
        z-index: 1050;
        /* Toolbar can be at modal level or slightly above if needed */
        position: relative;
        /* Ensure z-index works */
    }

    /* Ensure dropdown menus open correctly by giving them a very high z-index */
    .note-dropdown-menu {
        z-index: 2080;
        /* Dropdowns should be highest */
        position: absolute;
        /* Ensures z-index works */
    }

    /* Fix for fullscreen: Make the entire Summernote editor cover everything */
    .note-editor.fullscreen {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        z-index: 99999 !important;
        background-color: #fff !important;
        overflow-y: auto !important;
    }

    /* When Summernote is in fullscreen, hide main layout elements to avoid overlap */
    body.summernote-fullscreen-active {
        overflow: hidden !important;
    }

    /* Fix for button pointer events and hover */
    .note-btn {
        pointer-events: auto;
        /* Ensure buttons are clickable */
        cursor: pointer;
        border: 1px solid transparent;
        background-color: transparent;
        padding: 0.375rem 0.75rem;
        margin: 0;
        line-height: 1.5;
        border-radius: 0.25rem;
        transition: all 0.15s ease-in-out;
    }

    .note-btn:hover,
    .note-btn:focus {
        background-color: #e9ecef;
        border-color: #adb5bd;
        outline: 0;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    /* Specific fixes for table buttons in Summernote */
    /* These specific styles are often for the grid picker inside the table dropdown */
    .note-table .note-dimension-picker .note-dimension-picker-mousecatcher {
        position: absolute;
        z-index: 3;
        width: 10em;
        /* default is 5em, extend to give more clickable area */
        height: 10em;
        cursor: pointer;
    }

    .note-table .note-dimension-picker .note-dimension-picker-unhighlighted {
        position: absolute;
        z-index: 1;
        width: 5em;
        height: 5em;
        background: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAIAQMAAAD+wSzIAAAABlBMVEX///+/v7+jQ3Y5AAAADklEQVQI12P4AIX8EAgAw/AKAHlFae4AAAAASUVORK5CYII=') repeat;
    }

    .note-table .note-dimension-picker .note-dimension-picker-highlighted {
        position: absolute;
        z-index: 2;
        width: 1em;
        height: 1em;
        background: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAIAQMAAAD+wSzIAAAABlBMVEUAAABBQUE6faMoAAAADklEQVQI12P4AIX8EAgAw/AKAHlFae4AAAAASUVORK5CYII=') repeat;
    }

    /* General Summernote Editor Styling */
    .note-editor {
        z-index: 1055 !important;
        position: relative;
        margin-bottom: 10px;
        border-radius: 4px;
    }

    .note-popover {
        z-index: 99998 !important;
    }

    .note-modal {
        z-index: 99997 !important;
    }

    .note-toolbar {
        border-radius: 4px 4px 0 0;
    }

    .note-editing-area {
        border-radius: 0 0 4px 4px;
    }

    /* Image styling in editor */
    .note-editable img {
        max-width: 100%;
        height: auto;
        border-radius: 4px;
        margin: 5px 0;
    }

    /* Enhanced Modal Styling */
    .modal-header.bg-primary {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    }

    .modal-footer.bg-light {
        background: linear-gradient(to right, #f8f9fa 0%, #e9ecef 100%);
    }

    /* Card Styling for Modal Sections */
    .modal-body .card {
        border: 1px solid #dee2e6;
        transition: all 0.2s ease;
    }

    .modal-body .card:hover {
        border-color: #adb5bd;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .modal-body .card-header {
        background: linear-gradient(to right, #f8f9fa 0%, #e9ecef 100%);
        border-bottom: 1px solid #dee2e6;
    }

    .modal-body .card-header h6 {
        color: #495057;
        font-weight: 600;
    }

    /* Input Group Styling */
    .input-group-text {
        background-color: #e9ecef;
        border-color: #ced4da;
        color: #6c757d;
        font-size: 0.875rem;
        font-weight: 500;
    }

    /* Form Control Enhancement */
    .form-control-lg,
    .form-select-lg {
        padding: 0.75rem 1rem;
        font-size: 1.1rem;
        border-radius: 0.5rem;
    }

    /* Alert in Modal */
    .modal-body .alert-info {
        background-color: #d1ecf1;
        border-color: #bee5eb;
        color: #0c5460;
        border-radius: 0.5rem;
    }

    /* Label with Icons */
    .form-label i {
        width: 16px;
        text-align: center;
    }

    /* Button Enhancement */
    .modal-footer .btn {
        padding: 0.75rem 1.5rem;
        font-weight: 500;
        border-radius: 0.5rem;
        transition: all 0.2s ease;
    }

    .modal-footer .btn-primary {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        border: none;
    }

    .modal-footer .btn-primary:hover {
        background: linear-gradient(135deg, #0056b3 0%, #003d82 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .table-responsive {
            font-size: 0.8rem;
        }

        .modal-xl {
            max-width: 95%;
        }

        .note-toolbar {
            white-space: normal;
        }
    }

    /* Hide elements saat fullscreen */
    body.summernote-fullscreen-active {
        overflow: hidden !important;
    }

    body.summernote-fullscreen-active .main-sidebar,
    body.summernote-fullscreen-active .main-header,
    body.summernote-fullscreen-active .main-footer,
    body.summernote-fullscreen-active .navbar,
    body.summernote-fullscreen-active .breadcrumb {
        display: none !important;
    }

    .summernote-hidden {
        display: none !important;
    }

    /* Fix Dropdown Display Issues */
    .note-toolbar .dropdown-menu {
        display: none;
        min-width: 200px;
        max-height: 300px;
        overflow-y: auto;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.175);
        background-color: #fff;
        background-clip: padding-box;
    }

    .note-toolbar .dropdown-menu.show {
        display: block !important;
        animation: dropdownFadeIn 0.15s ease-in-out;
    }

    @keyframes dropdownFadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Enhanced Modal Styling */
    .modal-header.bg-primary {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%) !important;
    }

    .modal-header.bg-warning {
        background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%) !important;
    }

    .modal-footer.bg-light {
        background: linear-gradient(to right, #f8f9fa 0%, #e9ecef 100%) !important;
    }

    /* Card Styling for Modal Sections */
    .modal-body .card {
        border: 1px solid #dee2e6;
        transition: all 0.2s ease;
    }

    .modal-body .card:hover {
        border-color: #adb5bd;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .modal-body .card-header {
        background: linear-gradient(to right, #f8f9fa 0%, #e9ecef 100%) !important;
        border-bottom: 1px solid #dee2e6;
    }

    .modal-body .card-header h6 {
        color: #495057;
        font-weight: 600;
    }

    /* Button Enhancement */
    .modal-footer .btn-warning {
        background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
        border: none;
        color: #212529;
    }

    .modal-footer .btn-warning:hover {
        background: linear-gradient(135deg, #e0a800 0%, #c7950b 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(255, 193, 7, 0.3);
    }

    /* Table and Card Styling */
    .card {
        border: none;
        transition: all 0.3s ease;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.02);
    }

    .btn-group-sm .btn {
        --bs-btn-padding-y: 0.25rem;
        --bs-btn-padding-x: 0.75rem;
        --bs-btn-font-size: 0.875rem;
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

    .modal-content {
        border: none;
        border-radius: 1rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }

    /* Alert styling for tips */
    .alert-info .text-muted {
        color: #0c5460 !important;
    }
</style>

<?= $this->endSection() ?>