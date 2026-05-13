<?= $this->extend('templates/guru/guru_template') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-5">
    <div class="row mb-4 align-items-center py-5">
        <div class="col">
            <h2 class="fw-bold text-primary"><?= $ujian['nama_ujian'] ?></h2>
            <p class="text-muted">Kelola Soal Ujian</p>
        </div>
        <div class="col-auto">
            <button type="button" class="btn btn-outline-primary shadow-sm me-2" data-bs-toggle="modal" data-bs-target="#importBankSoalModal">
                <i class="fas fa-download me-2"></i>Import dari Bank Soal
            </button>
        </div>
        <div class="col-auto">
            <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#tambahSoalModal">
                <i class="fas fa-plus me-2"></i>Tambah Soal
            </button>
        </div>
    </div>

    <!-- Info Box untuk Panduan CKEditor -->
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Panduan Menulis Soal</h6>
        <p class="mb-2">Gunakan editor yang tersedia untuk format teks yang lebih kaya:</p>
        <ul class="mb-2">
            <li><strong>Format teks:</strong> Bold, italic, underline, warna</li>
            <li><strong>Rumus matematika:</strong> Gunakan superscript (x²) dan subscript (H₂O), atau tombol <kbd>Special Characters</kbd></li>
            <li><strong>Simbol matematika/fisika:</strong> Klik <kbd>Special Characters</kbd> untuk simbol seperti ∫, ∑, π, α, β, γ, δ, ≤, ≥, ±, °, dll</li>
            <li><strong>List:</strong> Bullet points dan numbering</li>
            <li><strong>Tabel:</strong> Untuk data terstruktur</li>
            <li><strong>Upload gambar:</strong> Gunakan field "Foto Soal" di bawah editor</li>
        </ul>
        <div class="mt-2">
            <small class="text-muted"><strong>Tips:</strong> Untuk rumus kompleks, gunakan kombinasi superscript/subscript + special characters. Contoh: E=mc² bisa dibuat dengan mengetik "E=mc" lalu pilih superscript untuk "2"</small>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>

    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4" width="5%">No</th>
                            <th width="10%">Kode Soal</th>
                            <th width="20%">Pertanyaan</th>
                            <th width="10%">Foto</th>
                            <th width="20%">Pilihan</th>
                            <th width="10%">Jawaban</th>
                            <th width="10%">Kesulitan</th>
                            <th width="10%">Pembahasan</th>
                            <th width="15%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1;
                        foreach ($soal as $s): ?>
                            <tr>
                                <td class="px-4"><?= $i++ ?></td>
                                <td class="fw-bold text-primary"><?= $s['kode_soal'] ?></td>
                                <td><?= $s['pertanyaan'] ?></td>
                                <td>
                                    <?php if (!empty($s['foto'])): ?>
                                        <img src="<?= base_url('uploads/soal/' . $s['foto']) ?>" alt="Foto Soal" class="img-thumbnail" style="max-height: 80px;">
                                    <?php else: ?>
                                        <span class="text-muted small">Tidak ada foto</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-1">
                                        <small><span class="fw-bold">A.</span> <?= $s['pilihan_a'] ?></small>
                                        <small><span class="fw-bold">B.</span> <?= $s['pilihan_b'] ?></small>
                                        <small><span class="fw-bold">C.</span> <?= $s['pilihan_c'] ?></small>
                                        <small><span class="fw-bold">D.</span> <?= $s['pilihan_d'] ?></small>
                                        <?php if (!empty($s['pilihan_e'])): ?>
                                            <small><span class="fw-bold">E.</span> <?= $s['pilihan_e'] ?></small>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="text-center fw-bold"><?= $s['jawaban_benar'] ?></td>
                                <td><?= $s['tingkat_kesulitan'] ?></td>
                                <td>
                                    <?php if (!empty($s['pembahasan'])): ?>
                                        <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#pembahasanModal<?= $s['soal_id'] ?>">
                                            <i class="fas fa-eye"></i> Lihat
                                        </button>
                                    <?php else: ?>
                                        <span class="text-muted small">Tidak ada</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editSoalModal<?= $s['soal_id'] ?>">
                                            <i class="fas fa-edit"></i>
                                            Edit
                                        </button>
                                        <a href="<?= base_url('guru/soal/hapus/' . $s['soal_id'] . '/' . $ujian['id_ujian']) ?>"
                                            class="btn btn-danger btn-sm"
                                            onclick="return confirm('Apakah anda yakin?')">
                                            <i class="fas fa-trash"></i>
                                            Hapus
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Pembahasan -->
<?php foreach ($soal as $s):
    if (!empty($s['pembahasan'])): ?>
        <div class="modal fade" id="pembahasanModal<?= $s['soal_id'] ?>" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <h5 class="modal-title fw-bold">Pembahasan Soal</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="fw-bold mb-2">Kode Soal: <span class="text-primary"><?= $s['kode_soal'] ?></span></p>

                        <p class="fw-bold mb-2">Pertanyaan:</p>
                        <div class="border p-3 mb-3"><?= $s['pertanyaan'] ?></div>

                        <?php if (!empty($s['foto'])): ?>
                            <div class="text-center mb-3">
                                <img src="<?= base_url('uploads/soal/' . $s['foto']) ?>" alt="Foto Soal" class="img-fluid" style="max-height: 200px;">
                            </div>
                        <?php endif; ?>

                        <p class="fw-bold mb-2">Jawaban Benar: <?= $s['jawaban_benar'] ?></p>

                        <div class="card bg-light">
                            <div class="card-header fw-bold">Pembahasan</div>
                            <div class="card-body">
                                <?= $s['pembahasan'] ?>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
<?php endif;
endforeach; ?>

<!-- Modal Tambah Soal -->
<div class="modal fade" id="tambahSoalModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title fw-bold"><i class="fas fa-plus-circle me-2"></i>Tambah Soal Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('guru/soal/tambah') ?>" method="post" enctype="multipart/form-data">
                <input type="hidden" name="ujian_id" value="<?= $ujian['id_ujian'] ?>">
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
                                    <input type="text" name="kode_soal" class="form-control form-control-lg" placeholder="Contoh: MAT001, FIS002" required>
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
                            <div class="mb-2">
                                <small class="text-muted">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Gunakan toolbar simbol cepat di atas editor atau klik <strong>Special Characters</strong> untuk simbol matematika lengkap
                                </small>
                            </div>
                            <textarea name="pertanyaan" id="pertanyaan_tambah" class="form-control" rows="4" required placeholder="Masukkan pertanyaan soal..."></textarea>

                            <div class="mt-3">
                                <label class="form-label"><i class="fas fa-image text-secondary me-1"></i>Foto Soal (Opsional)</label>
                                <input type="file" name="foto" class="form-control" accept=".jpg,.jpeg,.png">
                                <small class="text-muted">Upload gambar dengan format JPG, JPEG, atau PNG (maks. 2MB). Gunakan toolbar di atas untuk menambah simbol matematika dengan cepat.</small>
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
                                    <textarea name="pilihan_a" id="pilihan_a_tambah" class="form-control" rows="2" required></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-primary">B.</label>
                                    <textarea name="pilihan_b" id="pilihan_b_tambah" class="form-control" rows="2" required></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-primary">C.</label>
                                    <textarea name="pilihan_c" id="pilihan_c_tambah" class="form-control" rows="2" required></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-primary">D.</label>
                                    <textarea name="pilihan_d" id="pilihan_d_tambah" class="form-control" rows="2" required></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-warning">E. (Opsional)</label>
                                    <textarea name="pilihan_e" id="pilihan_e_tambah" class="form-control" rows="2"></textarea>
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
                            <div class="mb-2">
                                <small class="text-muted">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Gunakan toolbar simbol cepat atau Special Characters untuk rumus matematika
                                </small>
                            </div>
                            <textarea name="pembahasan" id="pembahasan_tambah" class="form-control" rows="4" placeholder="Masukkan pembahasan soal..."></textarea>
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

<!-- Modal Edit Soal -->
<?php foreach ($soal as $s): ?>
    <div class="modal fade" id="editSoalModal<?= $s['soal_id'] ?>" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold">Edit Soal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="<?= base_url('guru/soal/edit/' . $s['soal_id']) ?>" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="ujian_id" value="<?= $ujian['id_ujian'] ?>">
                    <div class="modal-body">
                        <div class="row g-3">
                            <!-- Tambah field Kode Soal di Edit Modal -->
                            <div class="col-12">
                                <label class="form-label fw-semibold">Kode Soal <span class="text-danger">*</span></label>
                                <input type="text" name="kode_soal" class="form-control" value="<?= esc($s['kode_soal']) ?>" required>
                                <small class="text-muted">Kode unik untuk soal ini</small>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Pertanyaan</label>
                                <div class="mb-2">
                                    <small class="text-muted">
                                        <i class="fas fa-lightbulb me-1"></i>
                                        Gunakan toolbar simbol cepat di atas editor atau klik <strong>Special Characters</strong> untuk simbol matematika lengkap
                                    </small>
                                </div>
                                <textarea name="pertanyaan" id="pertanyaan_edit_<?= $s['soal_id'] ?>" class="form-control" rows="4" required><?= esc($s['pertanyaan']) ?></textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Foto Soal (Opsional)</label>
                                <?php if (!empty($s['foto'])): ?>
                                    <div class="mb-2">
                                        <img src="<?= base_url('uploads/soal/' . $s['foto']) ?>" alt="Foto Soal" class="img-thumbnail" style="max-height: 200px;">
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="hapus_foto" id="hapusFoto<?= $s['soal_id'] ?>" value="1">
                                        <label class="form-check-label" for="hapusFoto<?= $s['soal_id'] ?>">
                                            Hapus foto
                                        </label>
                                    </div>
                                <?php endif; ?>
                                <input type="file" name="foto" class="form-control" accept=".jpg,.jpeg,.png">
                                <small class="text-muted">Upload gambar baru dengan format JPG, JPEG, atau PNG (maks. 2MB)</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Pilihan A</label>
                                <textarea name="pilihan_a" id="pilihan_a_edit_<?= $s['soal_id'] ?>" class="form-control" rows="2" required><?= esc($s['pilihan_a']) ?></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Pilihan B</label>
                                <textarea name="pilihan_b" id="pilihan_b_edit_<?= $s['soal_id'] ?>" class="form-control" rows="2" required><?= esc($s['pilihan_b']) ?></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Pilihan C</label>
                                <textarea name="pilihan_c" id="pilihan_c_edit_<?= $s['soal_id'] ?>" class="form-control" rows="2" required><?= esc($s['pilihan_c']) ?></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Pilihan D</label>
                                <textarea name="pilihan_d" id="pilihan_d_edit_<?= $s['soal_id'] ?>" class="form-control" rows="2" required><?= esc($s['pilihan_d']) ?></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Pilihan E (Opsional)</label>
                                <textarea name="pilihan_e" id="pilihan_e_edit_<?= $s['soal_id'] ?>" class="form-control" rows="2"><?= isset($s['pilihan_e']) ? esc($s['pilihan_e']) : '' ?></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Jawaban Benar</label>
                                <select name="jawaban_benar" class="form-select" required>
                                    <option value="">Pilih Jawaban Benar</option>
                                    <option value="A" <?= $s['jawaban_benar'] == 'A' ? 'selected' : '' ?>>A</option>
                                    <option value="B" <?= $s['jawaban_benar'] == 'B' ? 'selected' : '' ?>>B</option>
                                    <option value="C" <?= $s['jawaban_benar'] == 'C' ? 'selected' : '' ?>>C</option>
                                    <option value="D" <?= $s['jawaban_benar'] == 'D' ? 'selected' : '' ?>>D</option>
                                    <option value="E" <?= $s['jawaban_benar'] == 'E' ? 'selected' : '' ?>>E</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold"><i class="fas fa-balance-scale text-info me-1"></i>Tingkat Kesulitan</label>
                                <div class="input-group">
                                    <input type="number" name="tingkat_kesulitan" class="form-control" step="0.001" value="<?= $s['tingkat_kesulitan'] ?>" min="-3" max="3" required>
                                    <span class="input-group-text">(-3 hingga +3)</span>
                                </div>
                                <small class="text-muted">Negatif = mudah, Positif = sulit, 0 = sedang</small>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Pembahasan (Opsional)</label>
                                <div class="mb-2">
                                    <small class="text-muted">
                                        <i class="fas fa-lightbulb me-1"></i>
                                        Gunakan toolbar simbol cepat atau Special Characters untuk rumus matematika
                                    </small>
                                </div>
                                <textarea name="pembahasan" id="pembahasan_edit_<?= $s['soal_id'] ?>" class="form-control" rows="4"><?= isset($s['pembahasan']) ? esc($s['pembahasan']) : '' ?></textarea>
                                <small class="text-muted">Pembahasan akan ditampilkan kepada siswa setelah menyelesaikan ujian</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<!-- Modal Import Bank Soal -->
<div class="modal fade" id="importBankSoalModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Import Soal dari Bank Soal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Filter bertingkat untuk Bank Soal -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <label class="form-label">1. Pilih Kategori:</label>
                        <select id="filterKategoriImport" class="form-select">
                            <option value="">Pilih Kategori</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">2. Pilih Mata Pelajaran:</label>
                        <select id="filterJenisUjianImport" class="form-select" disabled>
                            <option value="">Pilih Mata Pelajaran</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">3. Pilih Bank Ujian:</label>
                        <select id="filterBankUjianImport" class="form-select" disabled>
                            <option value="">Pilih Bank Ujian</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">4. Cari Soal:</label>
                        <input type="text" id="searchBankSoal" class="form-control" placeholder="Cari dalam pertanyaan..." disabled>
                    </div>
                </div>

                <!-- Info Bank Ujian yang dipilih -->
                <div id="bankUjianInfo" class="alert alert-info" style="display: none;">
                    <h6 class="alert-heading">Informasi Bank Ujian</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Nama:</strong> <span id="infoBankNama"></span><br>
                            <strong>Kategori:</strong> <span id="infoBankKategori"></span><br>
                        </div>
                        <div class="col-md-6">
                            <strong>Pembuat:</strong> <span id="infoBankPembuat"></span><br>
                            <strong>Jumlah Soal:</strong> <span id="infoBankJumlahSoal"></span> soal<br>
                        </div>
                    </div>
                    <div class="mt-2">
                        <strong>Deskripsi:</strong> <span id="infoBankDeskripsi"></span>
                    </div>
                </div>

                <!-- Loading indicator -->
                <div id="loadingBankSoal" class="text-center p-4" style="display: none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Memuat soal...</p>
                </div>

                <!-- Tabel Bank Soal -->
                <div id="bankSoalContainer" style="display: none;">
                    <form id="formImportSoal" action="<?= base_url('guru/soal/import-bank') ?>" method="post">
                        <input type="hidden" name="ujian_id" value="<?= $ujian['id_ujian'] ?>">

                        <div class="table-responsive">
                            <table class="table table-hover align-middle" id="tableBankSoalImport">
                                <thead class="bg-light">
                                    <tr>
                                        <th width="5%">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="selectAllSoal">
                                                <label class="form-check-label" for="selectAllSoal">Semua</label>
                                            </div>
                                        </th>
                                        <th width="5%">No</th>
                                        <th width="12%">Kode Soal</th>
                                        <th width="25%">Pertanyaan</th>
                                        <th width="8%">Foto</th>
                                        <th width="20%">Pilihan</th>
                                        <th width="8%">Jawaban</th>
                                        <th width="8%">Kesulitan</th>
                                        <th width="9%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="bankSoalTableBody">
                                    <!-- Data akan diload via AJAX -->
                                </tbody>
                            </table>
                        </div>

                        <div class="text-center mt-3" id="noBankSoalMessage" style="display: none;">
                            <p class="text-muted">Pilih kategori, Mata Pelajaran, dan bank ujian untuk melihat soal yang tersedia</p>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnImportSoal" disabled>
                    <i class="fas fa-download me-2"></i>Import Soal Terpilih
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Load CKEditor 4 -->
<script src="<?= base_url('ckeditor/ckeditor.js') ?>"></script>

<script>
    // Konfigurasi CKEditor
    const ckEditorConfig = {
        height: 200,
        toolbar: [{
                name: 'document',
                items: ['Source']
            },
            {
                name: 'clipboard',
                items: ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo']
            },
            {
                name: 'editing',
                items: ['Find', 'Replace', '-', 'SelectAll']
            },
            '/',
            {
                name: 'basicstyles',
                items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat']
            },
            {
                name: 'paragraph',
                items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote']
            },
            {
                name: 'links',
                items: ['Link', 'Unlink']
            },
            '/',
            {
                name: 'styles',
                items: ['Styles', 'Format', 'Font', 'FontSize']
            },
            {
                name: 'colors',
                items: ['TextColor', 'BGColor']
            },
            {
                name: 'tools',
                items: ['Maximize', 'ShowBlocks']
            },
            {
                name: 'insert',
                items: ['Table', 'HorizontalRule', 'SpecialChar', 'MathSymbols']
            }
        ],
        // Disable file upload, image upload, dan exportpdf
        removePlugins: 'image,uploadimage,uploadwidget,uploadfile,filetools,filebrowser,exportpdf',
        // Disable version check warning
        versionCheck: false,
        // Extra plugins untuk simbol matematika
        extraPlugins: 'specialchar',
        // Disable drag and drop file upload
        disallowedContent: 'img[src]',
        // Allow basic formatting
        allowedContent: {
            'h1 h2 h3 h4 h5 h6 p blockquote li ul ol': true,
            'strong em u s sub sup': true,
            'table thead tbody tr th td': true,
            'a[href]': true,
            'span{color,background-color,font-size,font-family}': true,
            'div{text-align}': true
        },
        // Custom special characters untuk matematika/fisika
        specialChars: [
            // Greek letters (sering digunakan dalam fisika)
            ['α', 'Alpha'],
            ['β', 'Beta'],
            ['γ', 'Gamma'],
            ['δ', 'Delta'],
            ['ε', 'Epsilon'],
            ['ζ', 'Zeta'],
            ['η', 'Eta'],
            ['θ', 'Theta'],
            ['ι', 'Iota'],
            ['κ', 'Kappa'],
            ['λ', 'Lambda'],
            ['μ', 'Mu'],
            ['ν', 'Nu'],
            ['ξ', 'Xi'],
            ['ο', 'Omicron'],
            ['π', 'Pi'],
            ['ρ', 'Rho'],
            ['σ', 'Sigma'],
            ['τ', 'Tau'],
            ['υ', 'Upsilon'],
            ['φ', 'Phi'],
            ['χ', 'Chi'],
            ['ψ', 'Psi'],
            ['ω', 'Omega'],
            // Capital Greek letters
            ['Α', 'Alpha (capital)'],
            ['Β', 'Beta (capital)'],
            ['Γ', 'Gamma (capital)'],
            ['Δ', 'Delta (capital)'],
            ['Ε', 'Epsilon (capital)'],
            ['Ζ', 'Zeta (capital)'],
            ['Η', 'Eta (capital)'],
            ['Θ', 'Theta (capital)'],
            ['Ι', 'Iota (capital)'],
            ['Κ', 'Kappa (capital)'],
            ['Λ', 'Lambda (capital)'],
            ['Μ', 'Mu (capital)'],
            ['Ν', 'Nu (capital)'],
            ['Ξ', 'Xi (capital)'],
            ['Ο', 'Omicron (capital)'],
            ['Π', 'Pi (capital)'],
            ['Ρ', 'Rho (capital)'],
            ['Σ', 'Sigma (capital)'],
            ['Τ', 'Tau (capital)'],
            ['Υ', 'Upsilon (capital)'],
            ['Φ', 'Phi (capital)'],
            ['Χ', 'Chi (capital)'],
            ['Ψ', 'Psi (capital)'],
            ['Ω', 'Omega (capital)'],
            // Mathematical operators
            ['±', 'Plus-minus'],
            ['∓', 'Minus-plus'],
            ['×', 'Multiplication'],
            ['÷', 'Division'],
            ['∝', 'Proportional'],
            ['∞', 'Infinity'],
            ['∂', 'Partial derivative'],
            ['∇', 'Nabla (gradient)'],
            ['∆', 'Delta (change)'],
            ['∑', 'Summation'],
            ['∏', 'Product'],
            ['∫', 'Integral'],
            ['∮', 'Contour integral'],
            ['∬', 'Double integral'],
            ['∭', 'Triple integral'],
            // Relations and logic
            ['≈', 'Approximately equal'],
            ['≠', 'Not equal'],
            ['≡', 'Identical'],
            ['≤', 'Less than or equal'],
            ['≥', 'Greater than or equal'],
            ['«', 'Much less than'],
            ['»', 'Much greater than'],
            ['∈', 'Element of'],
            ['∉', 'Not element of'],
            ['⊂', 'Subset'],
            ['⊃', 'Superset'],
            ['∪', 'Union'],
            ['∩', 'Intersection'],
            ['∀', 'For all'],
            ['∃', 'There exists'],
            ['∄', 'There does not exist'],
            ['∧', 'Logical and'],
            ['∨', 'Logical or'],
            ['¬', 'Not'],
            // Arrows
            ['→', 'Right arrow'],
            ['←', 'Left arrow'],
            ['↑', 'Up arrow'],
            ['↓', 'Down arrow'],
            ['↔', 'Left-right arrow'],
            ['⇒', 'Right double arrow'],
            ['⇐', 'Left double arrow'],
            ['⇔', 'Left-right double arrow'],
            ['↗', 'Northeast arrow'],
            ['↖', 'Northwest arrow'],
            ['↘', 'Southeast arrow'],
            ['↙', 'Southwest arrow'],
            // Units and constants
            ['°', 'Degree'],
            ['′', 'Prime (minutes/feet)'],
            ['″', 'Double prime (seconds/inches)'],
            ['℃', 'Celsius'],
            ['℉', 'Fahrenheit'],
            ['Å', 'Angstrom'],
            ['ℏ', 'Reduced Planck constant'],
            ['ħ', 'H-bar'],
            // Fractions
            ['½', 'One half'],
            ['⅓', 'One third'],
            ['⅔', 'Two thirds'],
            ['¼', 'One quarter'],
            ['¾', 'Three quarters'],
            ['⅕', 'One fifth'],
            ['⅖', 'Two fifths'],
            ['⅗', 'Three fifths'],
            ['⅘', 'Four fifths'],
            ['⅙', 'One sixth'],
            ['⅚', 'Five sixths'],
            ['⅛', 'One eighth'],
            ['⅜', 'Three eighths'],
            ['⅝', 'Five eighths'],
            ['⅞', 'Seven eighths'],
            // Superscript numbers
            ['⁰', 'Superscript 0'],
            ['¹', 'Superscript 1'],
            ['²', 'Superscript 2'],
            ['³', 'Superscript 3'],
            ['⁴', 'Superscript 4'],
            ['⁵', 'Superscript 5'],
            ['⁶', 'Superscript 6'],
            ['⁷', 'Superscript 7'],
            ['⁸', 'Superscript 8'],
            ['⁹', 'Superscript 9'],
            ['⁺', 'Superscript plus'],
            ['⁻', 'Superscript minus'],
            // Subscript numbers
            ['₀', 'Subscript 0'],
            ['₁', 'Subscript 1'],
            ['₂', 'Subscript 2'],
            ['₃', 'Subscript 3'],
            ['₄', 'Subscript 4'],
            ['₅', 'Subscript 5'],
            ['₆', 'Subscript 6'],
            ['₇', 'Subscript 7'],
            ['₈', 'Subscript 8'],
            ['₉', 'Subscript 9'],
            ['₊', 'Subscript plus'],
            ['₋', 'Subscript minus'],
            // Root symbols
            ['√', 'Square root'],
            ['∛', 'Cube root'],
            ['∜', 'Fourth root']
        ],
        // Disable file dialog
        filebrowserBrowseUrl: '',
        filebrowserUploadUrl: '',
        filebrowserImageBrowseUrl: '',
        filebrowserImageUploadUrl: '',
        // Content styling
        contentsCss: [
            'body { font-family: Arial, sans-serif; font-size: 14px; line-height: 1.6; margin: 20px; }',
            'table { border-collapse: collapse; width: 100%; }',
            'table, th, td { border: 1px solid #ddd; padding: 8px; }'
        ],
        // Entermode
        enterMode: CKEDITOR.ENTER_P,
        shiftEnterMode: CKEDITOR.ENTER_BR
    };

    // Konfigurasi khusus untuk pilihan (lebih kecil) - didefinisikan setelah config utama
    const ckEditorConfigPilihan = {
        height: 120,
        toolbar: [{
                name: 'basicstyles',
                items: ['Bold', 'Italic', 'Underline', 'Subscript', 'Superscript']
            },
            {
                name: 'colors',
                items: ['TextColor']
            },
            {
                name: 'insert',
                items: ['SpecialChar']
            },
            {
                name: 'tools',
                items: ['Source']
            }
        ],
        // Disable file upload, image upload, dan exportpdf
        removePlugins: 'image,uploadimage,uploadwidget,uploadfile,filetools,filebrowser,exportpdf',
        // Disable version check warning
        versionCheck: false,
        // Extra plugins untuk simbol matematika
        extraPlugins: 'specialchar',
        // Disable drag and drop file upload
        disallowedContent: 'img[src]',
        // Allow basic formatting
        allowedContent: {
            'strong em u s sub sup': true,
            'span{color,background-color,font-size,font-family}': true
        },
        // Custom special characters untuk matematika/fisika (sama dengan config utama)
        specialChars: [
            // Greek letters (sering digunakan dalam fisika)
            ['α', 'Alpha'],
            ['β', 'Beta'],
            ['γ', 'Gamma'],
            ['δ', 'Delta'],
            ['ε', 'Epsilon'],
            ['ζ', 'Zeta'],
            ['η', 'Eta'],
            ['θ', 'Theta'],
            ['ι', 'Iota'],
            ['κ', 'Kappa'],
            ['λ', 'Lambda'],
            ['μ', 'Mu'],
            ['ν', 'Nu'],
            ['ξ', 'Xi'],
            ['ο', 'Omicron'],
            ['π', 'Pi'],
            ['ρ', 'Rho'],
            ['σ', 'Sigma'],
            ['τ', 'Tau'],
            ['υ', 'Upsilon'],
            ['φ', 'Phi'],
            ['χ', 'Chi'],
            ['ψ', 'Psi'],
            ['ω', 'Omega'],
            // Capital Greek letters
            ['Α', 'Alpha (capital)'],
            ['Β', 'Beta (capital)'],
            ['Γ', 'Gamma (capital)'],
            ['Δ', 'Delta (capital)'],
            ['Ε', 'Epsilon (capital)'],
            ['Ζ', 'Zeta (capital)'],
            ['Η', 'Eta (capital)'],
            ['Θ', 'Theta (capital)'],
            ['Ι', 'Iota (capital)'],
            ['Κ', 'Kappa (capital)'],
            ['Λ', 'Lambda (capital)'],
            ['Μ', 'Mu (capital)'],
            ['Ν', 'Nu (capital)'],
            ['Ξ', 'Xi (capital)'],
            ['Ο', 'Omicron (capital)'],
            ['Π', 'Pi (capital)'],
            ['Ρ', 'Rho (capital)'],
            ['Σ', 'Sigma (capital)'],
            ['Τ', 'Tau (capital)'],
            ['Υ', 'Upsilon (capital)'],
            ['Φ', 'Phi (capital)'],
            ['Χ', 'Chi (capital)'],
            ['Ψ', 'Psi (capital)'],
            ['Ω', 'Omega (capital)'],
            // Mathematical operators
            ['±', 'Plus-minus'],
            ['∓', 'Minus-plus'],
            ['×', 'Multiplication'],
            ['÷', 'Division'],
            ['∝', 'Proportional'],
            ['∞', 'Infinity'],
            ['∂', 'Partial derivative'],
            ['∇', 'Nabla (gradient)'],
            ['∆', 'Delta (change)'],
            ['∑', 'Summation'],
            ['∏', 'Product'],
            ['∫', 'Integral'],
            ['∮', 'Contour integral'],
            ['∬', 'Double integral'],
            ['∭', 'Triple integral'],
            // Relations and logic
            ['≈', 'Approximately equal'],
            ['≠', 'Not equal'],
            ['≡', 'Identical'],
            ['≤', 'Less than or equal'],
            ['≥', 'Greater than or equal'],
            ['«', 'Much less than'],
            ['»', 'Much greater than'],
            ['∈', 'Element of'],
            ['∉', 'Not element of'],
            ['⊂', 'Subset'],
            ['⊃', 'Superset'],
            ['∪', 'Union'],
            ['∩', 'Intersection'],
            ['∀', 'For all'],
            ['∃', 'There exists'],
            ['∄', 'There does not exist'],
            ['∧', 'Logical and'],
            ['∨', 'Logical or'],
            ['¬', 'Not'],
            // Arrows
            ['→', 'Right arrow'],
            ['←', 'Left arrow'],
            ['↑', 'Up arrow'],
            ['↓', 'Down arrow'],
            ['↔', 'Left-right arrow'],
            ['⇒', 'Right double arrow'],
            ['⇐', 'Left double arrow'],
            ['⇔', 'Left-right double arrow'],
            ['↗', 'Northeast arrow'],
            ['↖', 'Northwest arrow'],
            ['↘', 'Southeast arrow'],
            ['↙', 'Southwest arrow'],
            // Units and constants
            ['°', 'Degree'],
            ['′', 'Prime (minutes/feet)'],
            ['″', 'Double prime (seconds/inches)'],
            ['℃', 'Celsius'],
            ['℉', 'Fahrenheit'],
            ['Å', 'Angstrom'],
            ['ℏ', 'Reduced Planck constant'],
            ['ħ', 'H-bar'],
            // Fractions
            ['½', 'One half'],
            ['⅓', 'One third'],
            ['⅔', 'Two thirds'],
            ['¼', 'One quarter'],
            ['¾', 'Three quarters'],
            ['⅕', 'One fifth'],
            ['⅖', 'Two fifths'],
            ['⅗', 'Three fifths'],
            ['⅘', 'Four fifths'],
            ['⅙', 'One sixth'],
            ['⅚', 'Five sixths'],
            ['⅛', 'One eighth'],
            ['⅜', 'Three eighths'],
            ['⅝', 'Five eighths'],
            ['⅞', 'Seven eighths'],
            // Superscript numbers
            ['⁰', 'Superscript 0'],
            ['¹', 'Superscript 1'],
            ['²', 'Superscript 2'],
            ['³', 'Superscript 3'],
            ['⁴', 'Superscript 4'],
            ['⁵', 'Superscript 5'],
            ['⁶', 'Superscript 6'],
            ['⁷', 'Superscript 7'],
            ['⁸', 'Superscript 8'],
            ['⁹', 'Superscript 9'],
            ['⁺', 'Superscript plus'],
            ['⁻', 'Superscript minus'],
            // Subscript numbers
            ['₀', 'Subscript 0'],
            ['₁', 'Subscript 1'],
            ['₂', 'Subscript 2'],
            ['₃', 'Subscript 3'],
            ['₄', 'Subscript 4'],
            ['₅', 'Subscript 5'],
            ['₆', 'Subscript 6'],
            ['₇', 'Subscript 7'],
            ['₈', 'Subscript 8'],
            ['₉', 'Subscript 9'],
            ['₊', 'Subscript plus'],
            ['₋', 'Subscript minus'],
            // Root symbols
            ['√', 'Square root'],
            ['∛', 'Cube root'],
            ['∜', 'Fourth root']
        ],
        // Disable file dialog
        filebrowserBrowseUrl: '',
        filebrowserUploadUrl: '',
        filebrowserImageBrowseUrl: '',
        filebrowserImageUploadUrl: '',
        // Content styling
        contentsCss: [
            'body { font-family: Arial, sans-serif; font-size: 14px; line-height: 1.6; margin: 20px; }',
            'sub, sup { font-size: 0.75em; }'
        ],
        // Entermode
        enterMode: CKEDITOR.ENTER_P,
        shiftEnterMode: CKEDITOR.ENTER_BR
    };

    // Initialize CKEditor untuk modal tambah
    function initializeCKEditorTambah() {
        // Destroy existing instances if any
        destroyCKEditorInstances([
            'pertanyaan_tambah',
            'pilihan_a_tambah',
            'pilihan_b_tambah',
            'pilihan_c_tambah',
            'pilihan_d_tambah',
            'pilihan_e_tambah',
            'pembahasan_tambah'
        ]);

        // Initialize new instances
        CKEDITOR.replace('pertanyaan_tambah', ckEditorConfig);
        CKEDITOR.replace('pilihan_a_tambah', ckEditorConfigPilihan);
        CKEDITOR.replace('pilihan_b_tambah', ckEditorConfigPilihan);
        CKEDITOR.replace('pilihan_c_tambah', ckEditorConfigPilihan);
        CKEDITOR.replace('pilihan_d_tambah', ckEditorConfigPilihan);
        CKEDITOR.replace('pilihan_e_tambah', ckEditorConfigPilihan);
        CKEDITOR.replace('pembahasan_tambah', ckEditorConfig);
    }

    // Initialize CKEditor untuk modal edit
    function initializeCKEditorEdit(soalId) {
        const editorIds = [
            'pertanyaan_edit_' + soalId,
            'pilihan_a_edit_' + soalId,
            'pilihan_b_edit_' + soalId,
            'pilihan_c_edit_' + soalId,
            'pilihan_d_edit_' + soalId,
            'pilihan_e_edit_' + soalId,
            'pembahasan_edit_' + soalId
        ];

        // Destroy existing instances if any
        destroyCKEditorInstances(editorIds);

        // Initialize new instances
        CKEDITOR.replace('pertanyaan_edit_' + soalId, ckEditorConfig);
        CKEDITOR.replace('pilihan_a_edit_' + soalId, ckEditorConfigPilihan);
        CKEDITOR.replace('pilihan_b_edit_' + soalId, ckEditorConfigPilihan);
        CKEDITOR.replace('pilihan_c_edit_' + soalId, ckEditorConfigPilihan);
        CKEDITOR.replace('pilihan_d_edit_' + soalId, ckEditorConfigPilihan);
        CKEDITOR.replace('pilihan_e_edit_' + soalId, ckEditorConfigPilihan);
        CKEDITOR.replace('pembahasan_edit_' + soalId, ckEditorConfig);
    }

    // Destroy CKEditor instances
    function destroyCKEditorInstances(editorIds) {
        editorIds.forEach(id => {
            if (CKEDITOR.instances[id]) {
                CKEDITOR.instances[id].destroy();
            }
        });
    }

    // Event listeners untuk modal
    document.addEventListener('DOMContentLoaded', function() {
        // Modal tambah soal
        document.getElementById('tambahSoalModal').addEventListener('shown.bs.modal', function() {
            setTimeout(() => {
                initializeCKEditorTambah();
            }, 100);
        });

        document.getElementById('tambahSoalModal').addEventListener('hidden.bs.modal', function() {
            destroyCKEditorInstances([
                'pertanyaan_tambah',
                'pilihan_a_tambah',
                'pilihan_b_tambah',
                'pilihan_c_tambah',
                'pilihan_d_tambah',
                'pilihan_e_tambah',
                'pembahasan_tambah'
            ]);
        });

        // Modal edit soal
        <?php foreach ($soal as $s): ?>
            document.getElementById('editSoalModal<?= $s['soal_id'] ?>').addEventListener('shown.bs.modal', function() {
                setTimeout(() => {
                    initializeCKEditorEdit(<?= $s['soal_id'] ?>);
                }, 100);
            });

            document.getElementById('editSoalModal<?= $s['soal_id'] ?>').addEventListener('hidden.bs.modal', function() {
                destroyCKEditorInstances([
                    'pertanyaan_edit_<?= $s['soal_id'] ?>',
                    'pilihan_a_edit_<?= $s['soal_id'] ?>',
                    'pilihan_b_edit_<?= $s['soal_id'] ?>',
                    'pilihan_c_edit_<?= $s['soal_id'] ?>',
                    'pilihan_d_edit_<?= $s['soal_id'] ?>',
                    'pilihan_e_edit_<?= $s['soal_id'] ?>',
                    'pembahasan_edit_<?= $s['soal_id'] ?>'
                ]);
            });
        <?php endforeach; ?>
    });

    // Update form data before submit
    function updateCKEditorData() {
        for (let instance in CKEDITOR.instances) {
            CKEDITOR.instances[instance].updateElement();
        }
    }

    // Add event listeners to forms
    document.addEventListener('submit', function(e) {
        if (e.target.tagName === 'FORM') {
            updateCKEditorData();
        }
    });

    // Add quick math symbols toolbar
    function addQuickMathSymbols() {
        // Create quick math symbols bar
        const quickMathHtml = `
        <div class="quick-math-symbols p-2 bg-light border-bottom">
            <small class="text-muted me-2">Simbol Cepat:</small>
            <button type="button" class="btn btn-sm btn-outline-secondary me-1" onclick="insertSymbol('²')" title="Kuadrat">x²</button>
            <button type="button" class="btn btn-sm btn-outline-secondary me-1" onclick="insertSymbol('³')" title="Kubik">x³</button>
            <button type="button" class="btn btn-sm btn-outline-secondary me-1" onclick="insertSymbol('½')" title="Setengah">½</button>
            <button type="button" class="btn btn-sm btn-outline-secondary me-1" onclick="insertSymbol('π')" title="Pi">π</button>
            <button type="button" class="btn btn-sm btn-outline-secondary me-1" onclick="insertSymbol('α')" title="Alpha">α</button>
            <button type="button" class="btn btn-sm btn-outline-secondary me-1" onclick="insertSymbol('β')" title="Beta">β</button>
            <button type="button" class="btn btn-sm btn-outline-secondary me-1" onclick="insertSymbol('γ')" title="Gamma">γ</button>
            <button type="button" class="btn btn-sm btn-outline-secondary me-1" onclick="insertSymbol('θ')" title="Theta">θ</button>
            <button type="button" class="btn btn-sm btn-outline-secondary me-1" onclick="insertSymbol('±')" title="Plus minus">±</button>
            <button type="button" class="btn btn-sm btn-outline-secondary me-1" onclick="insertSymbol('≤')" title="Kurang sama dengan">≤</button>
            <button type="button" class="btn btn-sm btn-outline-secondary me-1" onclick="insertSymbol('≥')" title="Lebih sama dengan">≥</button>
            <button type="button" class="btn btn-sm btn-outline-secondary me-1" onclick="insertSymbol('∫')" title="Integral">∫</button>
            <button type="button" class="btn btn-sm btn-outline-secondary me-1" onclick="insertSymbol('∑')" title="Sigma">∑</button>
            <button type="button" class="btn btn-sm btn-outline-secondary me-1" onclick="insertSymbol('√')" title="Akar">√</button>
            <button type="button" class="btn btn-sm btn-outline-secondary me-1" onclick="insertSymbol('∞')" title="Infinity">∞</button>
            <button type="button" class="btn btn-sm btn-outline-secondary me-1" onclick="insertSymbol('°')" title="Derajat">°</button>
        </div>
    `;

        // Add to modals when they're shown
        document.addEventListener('shown.bs.modal', function(e) {
            // Hanya tambahkan quick math symbols untuk modal tambah dan edit soal
            if (e.target.id === 'tambahSoalModal' || e.target.id.startsWith('editSoalModal')) {
                const modalBody = e.target.querySelector('.modal-body');
                // Cari card dengan header "Pertanyaan Soal"
                const pertanyaanCard = modalBody.querySelector('.card .card-header h6 i.fa-question-circle')?.closest('.card');

                if (pertanyaanCard && !modalBody.querySelector('.quick-math-symbols')) {
                    // Insert sebelum card pertanyaan
                    pertanyaanCard.insertAdjacentHTML('beforebegin', quickMathHtml);
                }
            }
        });
    }

    // Function to insert symbol into active CKEditor
    function insertSymbol(symbol) {
        // Cari editor yang sedang aktif/focus
        for (let instanceName in CKEDITOR.instances) {
            const editor = CKEDITOR.instances[instanceName];
            if (editor.focusManager.hasFocus) {
                editor.insertText(symbol);
                editor.focus(); // Pastikan tetap focus
                return;
            }
        }

        // Jika tidak ada yang focus, cari editor yang terakhir di-click atau yang visible
        const visibleEditors = [];
        for (let instanceName in CKEDITOR.instances) {
            const editor = CKEDITOR.instances[instanceName];
            if (editor.container && editor.container.isVisible()) {
                visibleEditors.push(editor);
            }
        }

        // Gunakan editor visible pertama
        if (visibleEditors.length > 0) {
            visibleEditors[0].insertText(symbol);
            visibleEditors[0].focus();
            return;
        }

        // Fallback: gunakan editor pertama yang tersedia
        const firstEditor = Object.keys(CKEDITOR.instances)[0];
        if (firstEditor && CKEDITOR.instances[firstEditor]) {
            CKEDITOR.instances[firstEditor].insertText(symbol);
            CKEDITOR.instances[firstEditor].focus();
        }
    }

    // Initialize quick math symbols
    document.addEventListener('DOMContentLoaded', function() {
        addQuickMathSymbols();
    });
</script>

<!-- Script untuk Import Bank Soal -->
<script>
    let bankSoalData = [];
    let selectedBankUjian = null;

    document.addEventListener('DOMContentLoaded', function() {
        // Load kategori ketika modal dibuka
        document.getElementById('importBankSoalModal').addEventListener('shown.bs.modal', function() {
            loadKategori();
            resetImportModal();
        });

        // Setup event listeners untuk filter bertingkat
        document.getElementById('filterKategoriImport').addEventListener('change', onKategoriChange);
        document.getElementById('filterJenisUjianImport').addEventListener('change', onJenisUjianChange);
        document.getElementById('filterBankUjianImport').addEventListener('change', onBankUjianChange);
        document.getElementById('searchBankSoal').addEventListener('input', filterSoalBySearch);

        // Select all checkbox
        document.getElementById('selectAllSoal').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('input[name="soal_ids[]"]:not(:disabled)');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateImportButton();
        });

        // Import button
        document.getElementById('btnImportSoal').addEventListener('click', function() {
            if (confirm('Apakah Anda yakin ingin mengimport soal yang dipilih?')) {
                document.getElementById('formImportSoal').submit();
            }
        });
    });

    function resetImportModal() {
        document.getElementById('filterJenisUjianImport').disabled = true;
        document.getElementById('filterBankUjianImport').disabled = true;
        document.getElementById('searchBankSoal').disabled = true;
        document.getElementById('bankUjianInfo').style.display = 'none';
        document.getElementById('bankSoalContainer').style.display = 'none';
        document.getElementById('noBankSoalMessage').style.display = 'block';
        bankSoalData = [];
        selectedBankUjian = null;
    }

    function loadKategori() {
        fetch('<?= base_url('guru/bank-soal/api/kategori') ?>')
            .then(response => response.json())
            .then(data => {
                const select = document.getElementById('filterKategoriImport');
                select.innerHTML = '<option value="">Pilih Kategori</option>';

                if (data.status === 'success') {
                    data.data.forEach(kategori => {
                        const option = document.createElement('option');
                        option.value = kategori;
                        option.textContent = kategori.charAt(0).toUpperCase() + kategori.slice(1);
                        select.appendChild(option);
                    });
                }
            })
            .catch(error => {
                console.error('Error loading kategori:', error);
            });
    }

    function onKategoriChange() {
        const kategori = this.value;
        const jenisUjianSelect = document.getElementById('filterJenisUjianImport');
        const bankUjianSelect = document.getElementById('filterBankUjianImport');

        // Reset subsequent dropdowns
        jenisUjianSelect.innerHTML = '<option value="">Pilih Mata Pelajaran</option>';
        bankUjianSelect.innerHTML = '<option value="">Pilih Bank Ujian</option>';
        jenisUjianSelect.disabled = !kategori;
        bankUjianSelect.disabled = true;
        document.getElementById('searchBankSoal').disabled = true;

        // Hide content
        document.getElementById('bankUjianInfo').style.display = 'none';
        document.getElementById('bankSoalContainer').style.display = 'none';

        if (!kategori) return;

        // Load Mata Pelajaran untuk kategori ini
        fetch(`<?= base_url('guru/bank-soal/api/jenis-ujian') ?>?kategori=${encodeURIComponent(kategori)}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    data.data.forEach(jenisUjian => {
                        const option = document.createElement('option');
                        option.value = jenisUjian.jenis_ujian_id;
                        option.textContent = `${jenisUjian.nama_jenis} (${jenisUjian.jumlah_bank} bank ujian)`;
                        jenisUjianSelect.appendChild(option);
                    });
                }
            })
            .catch(error => {
                console.error('Error loading Mata Pelajaran:', error);
            });
    }

    function onJenisUjianChange() {
        const kategori = document.getElementById('filterKategoriImport').value;
        const jenisUjianId = this.value;
        const bankUjianSelect = document.getElementById('filterBankUjianImport');

        // Reset bank ujian dropdown
        bankUjianSelect.innerHTML = '<option value="">Pilih Bank Ujian</option>';
        bankUjianSelect.disabled = !jenisUjianId;
        document.getElementById('searchBankSoal').disabled = true;

        // Hide content
        document.getElementById('bankUjianInfo').style.display = 'none';
        document.getElementById('bankSoalContainer').style.display = 'none';

        if (!jenisUjianId) return;

        // Load bank ujian
        fetch(`<?= base_url('guru/bank-soal/api/bank-ujian') ?>?kategori=${encodeURIComponent(kategori)}&jenis_ujian_id=${jenisUjianId}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    data.data.forEach(bankUjian => {
                        const option = document.createElement('option');
                        option.value = bankUjian.bank_ujian_id;
                        option.textContent = `${bankUjian.nama_ujian} (${bankUjian.jumlah_soal} soal) - ${bankUjian.creator_name}`;
                        bankUjianSelect.appendChild(option);
                    });
                }
            })
            .catch(error => {
                console.error('Error loading bank ujian:', error);
            });
    }

    function onBankUjianChange() {
        const bankUjianId = this.value;
        document.getElementById('searchBankSoal').disabled = !bankUjianId;

        if (!bankUjianId) {
            document.getElementById('bankUjianInfo').style.display = 'none';
            document.getElementById('bankSoalContainer').style.display = 'none';
            return;
        }

        // Show loading
        document.getElementById('loadingBankSoal').style.display = 'block';
        document.getElementById('bankSoalContainer').style.display = 'none';

        // Load soal dari bank ujian
        fetch(`<?= base_url('guru/bank-soal/api/soal') ?>?bank_ujian_id=${bankUjianId}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    bankSoalData = data.data;
                    selectedBankUjian = data.bank_ujian;

                    // Show bank ujian info
                    showBankUjianInfo(selectedBankUjian, bankSoalData.length);

                    // Render soal
                    renderBankSoal(bankSoalData);

                    document.getElementById('bankSoalContainer').style.display = 'block';
                } else {
                    alert(data.message || 'Error loading soal');
                }
            })
            .catch(error => {
                console.error('Error loading soal:', error);
                alert('Terjadi kesalahan saat memuat soal');
            })
            .finally(() => {
                document.getElementById('loadingBankSoal').style.display = 'none';
            });
    }

    function showBankUjianInfo(bankUjian, jumlahSoal) {
        document.getElementById('infoBankNama').textContent = bankUjian.nama_ujian;
        document.getElementById('infoBankKategori').textContent = bankUjian.kategori.charAt(0).toUpperCase() + bankUjian.kategori.slice(1);
        document.getElementById('infoBankPembuat').textContent = bankUjian.creator_name || 'System';
        document.getElementById('infoBankJumlahSoal').textContent = jumlahSoal;
        document.getElementById('infoBankDeskripsi').textContent = bankUjian.deskripsi || 'Tidak ada deskripsi';

        document.getElementById('bankUjianInfo').style.display = 'block';
    }

    function renderBankSoal(soalList) {
        const tbody = document.getElementById('bankSoalTableBody');
        const noDataMessage = document.getElementById('noBankSoalMessage');

        if (soalList.length === 0) {
            tbody.innerHTML = '';
            noDataMessage.style.display = 'block';
            noDataMessage.innerHTML = '<p class="text-muted">Tidak ada soal dalam bank ujian ini</p>';
            return;
        }

        noDataMessage.style.display = 'none';

        tbody.innerHTML = soalList.map((soal, index) => `
        <tr data-soal-id="${soal.soal_id}">
            <td>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="soal_ids[]" 
                        value="${soal.soal_id}" onchange="updateImportButton()">
                </div>
            </td>
            <td>${index + 1}</td>
            <td class="fw-bold text-primary">${soal.kode_soal}</td>
            <td>${truncateText(stripHtml(soal.pertanyaan), 100)}</td>
            <td>
                ${soal.foto ? 
                    `<img src="<?= base_url('uploads/soal/') ?>${soal.foto}" class="img-thumbnail" style="max-height: 40px;">` : 
                    '<span class="text-muted small">-</span>'
                }
            </td>
            <td>
                <div class="d-flex flex-column gap-1">
                    <small><strong>A.</strong> ${truncateText(stripHtml(soal.pilihan_a), 30)}</small>
                    <small><strong>B.</strong> ${truncateText(stripHtml(soal.pilihan_b), 30)}</small>
                    <small><strong>C.</strong> ${truncateText(stripHtml(soal.pilihan_c), 30)}</small>
                    <small><strong>D.</strong> ${truncateText(stripHtml(soal.pilihan_d), 30)}</small>
                    ${soal.pilihan_e ? `<small><strong>E.</strong> ${truncateText(stripHtml(soal.pilihan_e), 30)}</small>` : ''}
                </div>
            </td>
            <td class="text-center fw-bold">${soal.jawaban_benar}</td>
            <td class="text-center">${soal.tingkat_kesulitan}</td>
            <td>
                <button type="button" class="btn btn-sm btn-outline-info" 
                        onclick="previewSoal(${soal.soal_id})" title="Preview">
                    <i class="fas fa-eye"></i>
                </button>
            </td>
        </tr>
    `).join('');

        // Reset checkbox
        document.getElementById('selectAllSoal').checked = false;
        updateImportButton();
    }

    function filterSoalBySearch() {
        const search = this.value.toLowerCase();

        if (!bankSoalData || bankSoalData.length === 0) return;

        const filteredData = bankSoalData.filter(soal => {
            return stripHtml(soal.pertanyaan).toLowerCase().includes(search) ||
                stripHtml(soal.pilihan_a).toLowerCase().includes(search) ||
                stripHtml(soal.pilihan_b).toLowerCase().includes(search) ||
                stripHtml(soal.pilihan_c).toLowerCase().includes(search) ||
                stripHtml(soal.pilihan_d).toLowerCase().includes(search) ||
                (soal.pilihan_e && stripHtml(soal.pilihan_e).toLowerCase().includes(search));
        });

        renderBankSoal(filteredData);
    }

    function updateImportButton() {
        const checked = document.querySelectorAll('input[name="soal_ids[]"]:checked').length;
        const btn = document.getElementById('btnImportSoal');

        btn.disabled = checked === 0;
        btn.innerHTML = checked > 0 ?
            `<i class="fas fa-download me-2"></i>Import ${checked} Soal Terpilih` :
            '<i class="fas fa-download me-2"></i>Import Soal Terpilih';
    }

    function previewSoal(soalId) {
        const soal = bankSoalData.find(s => s.soal_id == soalId);
        if (!soal) return;

        // Buat modal preview
        const modalHtml = `
        <div class="modal fade" id="previewSoalModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Preview Soal</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="fw-bold">Kode Soal: <span class="text-primary">${soal.kode_soal}</span></p>
                        <p class="fw-bold">Pertanyaan:</p>
                        <div class="border p-3 mb-3">${soal.pertanyaan}</div>
                        ${soal.foto ? `<div class="text-center mb-3"><img src="<?= base_url('uploads/soal/') ?>${soal.foto}" class="img-fluid" style="max-height: 300px;"></div>` : ''}
                        <p class="fw-bold">Pilihan:</p>
                        <div>
                            <p><strong>A.</strong> ${soal.pilihan_a}</p>
                            <p><strong>B.</strong> ${soal.pilihan_b}</p>
                            <p><strong>C.</strong> ${soal.pilihan_c}</p>
                            <p><strong>D.</strong> ${soal.pilihan_d}</p>
                            ${soal.pilihan_e ? `<p><strong>E.</strong> ${soal.pilihan_e}</p>` : ''}
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <p class="fw-bold">Jawaban Benar: <span class="text-success">${soal.jawaban_benar}</span></p>
                            </div>
                            <div class="col-md-6">
                                <p class="fw-bold">Tingkat Kesulitan: ${soal.tingkat_kesulitan}</p>
                            </div>
                        </div>
                        ${soal.pembahasan ? `
                            <div class="mt-3">
                                <p class="fw-bold">Pembahasan:</p>
                                <div class="card bg-light">
                                    <div class="card-body">${soal.pembahasan}</div>
                                </div>
                            </div>
                        ` : ''}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    `;

        // Remove existing preview modal
        const existingModal = document.getElementById('previewSoalModal');
        if (existingModal) {
            existingModal.remove();
        }

        // Add new modal
        document.body.insertAdjacentHTML('beforeend', modalHtml);

        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('previewSoalModal'));
        modal.show();
    }

    function truncateText(text, maxLength) {
        if (!text) return '';
        if (text.length <= maxLength) return text;
        return text.substring(0, maxLength) + '...';
    }

    function stripHtml(html) {
        const tmp = document.createElement('div');
        tmp.innerHTML = html;
        return tmp.textContent || tmp.innerText || '';
    }
</script>

<style>
    /* Custom styles untuk CKEditor */
    .cke_editor {
        margin-bottom: 10px;
    }

    .cke_contents {
        border-radius: 0 0 4px 4px;
    }

    .cke_top {
        border-radius: 4px 4px 0 0;
    }

    /* Ensure mathematical symbols display properly */
    .cke_editable {
        font-family: 'Times New Roman', Times, serif;
        line-height: 1.5;
    }

    /* Special characters button styling */
    .cke_button__specialchar {
        background-color: #e3f2fd !important;
    }

    .cke_button__specialchar:hover {
        background-color: #bbdefb !important;
    }

    /* Mathematical symbols in content */
    .math-symbols {
        font-family: 'Times New Roman', 'Symbol', 'Arial Unicode MS', serif;
        font-size: 1.1em;
    }

    /* Responsive table for better mobile view */
    @media (max-width: 768px) {
        .table-responsive {
            font-size: 0.8rem;
        }

        .modal-xl {
            max-width: 95%;
        }

        .cke_toolbar {
            white-space: normal !important;
        }
    }

    /* Quick math symbols bar */
    .quick-math-symbols {
        border-radius: 4px 4px 0 0;
        border-left: 1px solid #ddd;
        border-right: 1px solid #ddd;
        border-top: 1px solid #ddd;
    }

    .quick-math-symbols .btn {
        font-family: 'Times New Roman', Times, serif;
        font-size: 14px;
        padding: 2px 6px;
        line-height: 1.2;
    }

    .quick-math-symbols .btn:hover {
        background-color: #007bff;
        color: white;
        border-color: #007bff;
    }

    /* Enhanced Modal Styling */
    .modal-header.bg-primary {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%) !important;
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

    /* Alert styling for tips */
    .alert-info .text-muted {
        color: #0c5460 !important;
    }

    /* Keyboard key styling */
    kbd {
        padding: 2px 4px;
        font-size: 87.5%;
        color: #fff;
        background-color: #212529;
        border-radius: 3px;
        box-shadow: inset 0 -1px 0 rgba(0, 0, 0, .25);
    }
</style>

<?= $this->endSection() ?>