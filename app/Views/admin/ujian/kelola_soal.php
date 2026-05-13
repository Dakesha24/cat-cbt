<?= $this->extend('templates/admin/admin_template') ?>

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
                    <a href="<?= base_url('admin/soal/hapus/' . $s['soal_id'] . '/' . $ujian['id_ujian']) ?>"
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
<div class="modal fade" id="tambahSoalModal" tabindex="-1" data-bs-focus="false">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white border-0">
        <h5 class="modal-title fw-bold"><i class="fas fa-plus-circle me-2"></i>Tambah Soal Baru</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form action="<?= base_url('admin/soal/tambah') ?>" method="post" enctype="multipart/form-data">
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

<!-- Modal Edit Soal -->
<?php foreach ($soal as $s): ?>
  <div class="modal fade" id="editSoalModal<?= $s['soal_id'] ?>" tabindex="-1" data-bs-focus="false" data-bs-keyboard="false">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header border-0">
          <h5 class="modal-title fw-bold">Edit Soal</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form action="<?= base_url('admin/soal/edit/' . $s['soal_id']) ?>" method="post" enctype="multipart/form-data">
          <input type="hidden" name="ujian_id" value="<?= $ujian['id_ujian'] ?>">
          <input type="hidden" name="old_foto" value="<?= esc($s['foto']) ?>">
          <div class="modal-body">
            <div class="row g-3">
              <div class="col-12">
                <label class="form-label fw-semibold">Kode Soal <span class="text-danger">*</span></label>
                <input type="text" name="kode_soal" class="form-control" value="<?= esc($s['kode_soal']) ?>" required>
                <small class="text-muted">Kode unik untuk soal ini</small>
              </div>

              <div class="col-12">
                <label class="form-label fw-semibold">Pertanyaan</label>
                <textarea name="pertanyaan" id="pertanyaan_edit_<?= $s['soal_id'] ?>" class="form-control summernote" required><?= esc($s['pertanyaan']) ?></textarea>
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
                <input type="file" name="foto" class="form-control" accept=".jpg,.jpeg,.png,.gif">
                <small class="text-muted">
                  Upload gambar baru dengan format JPG, JPEG, PNG, atau GIF (maks. 2MB).
                  <br><strong>Tips:</strong> Anda juga bisa langsung insert gambar di editor dengan klik tombol <i class="fas fa-image"></i> pada toolbar.
                </small>
              </div>

              <div class="col-md-6">
                <label class="form-label">Pilihan A</label>
                <textarea name="pilihan_a" id="pilihan_a_edit_<?= $s['soal_id'] ?>" class="form-control summernote-small" required><?= esc($s['pilihan_a']) ?></textarea>
              </div>
              <div class="col-md-6">
                <label class="form-label">Pilihan B</label>
                <textarea name="pilihan_b" id="pilihan_b_edit_<?= $s['soal_id'] ?>" class="form-control summernote-small" required><?= esc($s['pilihan_b']) ?></textarea>
              </div>
              <div class="col-md-6">
                <label class="form-label">Pilihan C</label>
                <textarea name="pilihan_c" id="pilihan_c_edit_<?= $s['soal_id'] ?>" class="form-control summernote-small" required><?= esc($s['pilihan_c']) ?></textarea>
              </div>
              <div class="col-md-6">
                <label class="form-label">Pilihan D</label>
                <textarea name="pilihan_d" id="pilihan_d_edit_<?= $s['soal_id'] ?>" class="form-control summernote-small" required><?= esc($s['pilihan_d']) ?></textarea>
              </div>
              <div class="col-md-6">
                <label class="form-label">Pilihan E (Opsional)</label>
                <textarea name="pilihan_e" id="pilihan_e_edit_<?= $s['soal_id'] ?>" class="form-control summernote-small"><?= isset($s['pilihan_e']) ? esc($s['pilihan_e']) : '' ?></textarea>
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
                <textarea name="pembahasan" id="pembahasan_edit_<?= $s['soal_id'] ?>" class="form-control summernote"><?= isset($s['pembahasan']) ? esc($s['pembahasan']) : '' ?></textarea>
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
          <form id="formImportSoal" action="<?= base_url('admin/soal/import-bank') ?>" method="post">
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

<!-- Load jQuery, Bootstrap 5, dan Summernote BS5 -->
<script src="//code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" />
<script src="//cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Include Summernote BS5 -->
<link href="//cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-bs5.min.css" rel="stylesheet">
<script src="//cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-bs5.min.js"></script>

<script>
  // Variabel global untuk data bank soal
  let bankSoalData = [];
  let selectedBankUjian = null;

  // Konfigurasi Summernote UMUM yang sudah diperbaiki
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

  // Function upload gambar sederhana untuk Summernote
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

  // === FUNGSI-FUNGSI UNTUK IMPORT BANK SOAL ===

  function resetImportModal() {
    console.log('Reset import modal');
    $('#filterJenisUjianImport').prop('disabled', true).html('<option value="">Pilih Mata Pelajaran</option>');
    $('#filterBankUjianImport').prop('disabled', true).html('<option value="">Pilih Bank Ujian</option>');
    $('#searchBankSoal').prop('disabled', true).val('');
    $('#bankUjianInfo').hide();
    $('#bankSoalContainer').hide();
    $('#noBankSoalMessage').show().html('<p class="text-muted">Pilih kategori, Mata Pelajaran, dan bank ujian untuk melihat soal yang tersedia</p>');
    bankSoalData = [];
    selectedBankUjian = null;
    updateImportButton();
    $('#selectAllSoal').prop('checked', false);
  }

  function loadKategori() {
    console.log('Loading kategori...');

    $.ajax({
      url: '<?= base_url('admin/bank-soal/api/kategori') ?>',
      type: 'GET',
      dataType: 'json',
      timeout: 10000,
      success: function(data) {
        console.log('Kategori loaded:', data);
        const select = $('#filterKategoriImport');
        select.html('<option value="">Pilih Kategori</option>');

        if (data && data.status === 'success' && data.data) {
          data.data.forEach(kategori => {
            const option = $('<option></option>');
            option.val(kategori);
            option.text(kategori.charAt(0).toUpperCase() + kategori.slice(1));
            select.append(option);
          });
          console.log('Kategori berhasil dimuat:', data.data.length, 'kategori');
        } else {
          console.warn('Data kategori tidak valid:', data);
          select.append('<option value="" disabled>Tidak ada kategori</option>');
        }
      },
      error: function(xhr, status, error) {
        console.error('Error loading kategori:', error, xhr.responseText);
        const select = $('#filterKategoriImport');
        select.html('<option value="" disabled>Error loading kategori</option>');

        if (typeof Swal !== 'undefined') {
          Swal.fire({
            icon: 'error',
            title: 'Kesalahan',
            text: 'Gagal memuat kategori. Silakan refresh halaman.',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
          });
        }
      }
    });
  }

  function onKategoriChange() {
    const kategori = $(this).val();
    console.log('Kategori changed:', kategori);

    const jenisUjianSelect = $('#filterJenisUjianImport');
    const bankUjianSelect = $('#filterBankUjianImport');

    // Reset dependent dropdowns
    jenisUjianSelect.html('<option value="">Pilih Mata Pelajaran</option>');
    bankUjianSelect.html('<option value="">Pilih Bank Ujian</option>');
    jenisUjianSelect.prop('disabled', !kategori);
    bankUjianSelect.prop('disabled', true);
    $('#searchBankSoal').prop('disabled', true);

    // Hide info and containers
    $('#bankUjianInfo').hide();
    $('#bankSoalContainer').hide();
    $('#noBankSoalMessage').show().html('<p class="text-muted">Pilih kategori, Mata Pelajaran, dan bank ujian untuk melihat soal yang tersedia</p>');

    // Reset data
    bankSoalData = [];
    selectedBankUjian = null;
    updateImportButton();
    $('#selectAllSoal').prop('checked', false);

    if (!kategori) return;

    // Load mata pelajaran
    console.log('Loading mata pelajaran untuk kategori:', kategori);

    $.ajax({
      url: `<?= base_url('admin/bank-soal/api/jenis-ujian') ?>?kategori=${encodeURIComponent(kategori)}`,
      type: 'GET',
      dataType: 'json',
      timeout: 10000,
      success: function(data) {
        console.log('Mata pelajaran loaded:', data);

        if (data && data.status === 'success' && data.data) {
          data.data.forEach(jenisUjian => {
            const option = $('<option></option>');
            option.val(jenisUjian.jenis_ujian_id);
            option.text(`${jenisUjian.nama_jenis} (${jenisUjian.jumlah_bank} bank ujian)`);
            jenisUjianSelect.append(option);
          });
          console.log('Mata pelajaran berhasil dimuat:', data.data.length, 'mata pelajaran');
        } else {
          console.warn('Data mata pelajaran tidak valid:', data);
          jenisUjianSelect.append('<option value="" disabled>Tidak ada mata pelajaran</option>');
        }
      },
      error: function(xhr, status, error) {
        console.error('Error loading mata pelajaran:', error, xhr.responseText);
        jenisUjianSelect.html('<option value="" disabled>Error loading mata pelajaran</option>');

        if (typeof Swal !== 'undefined') {
          Swal.fire({
            icon: 'error',
            title: 'Kesalahan',
            text: 'Gagal memuat mata pelajaran.',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
          });
        }
      }
    });
  }

  function onJenisUjianChange() {
    const kategori = $('#filterKategoriImport').val();
    const jenisUjianId = $(this).val();
    console.log('Jenis ujian changed:', jenisUjianId);

    const bankUjianSelect = $('#filterBankUjianImport');

    // Reset dependent dropdown
    bankUjianSelect.html('<option value="">Pilih Bank Ujian</option>');
    bankUjianSelect.prop('disabled', !jenisUjianId);
    $('#searchBankSoal').prop('disabled', true);

    // Hide info and containers
    $('#bankUjianInfo').hide();
    $('#bankSoalContainer').hide();
    $('#noBankSoalMessage').show().html('<p class="text-muted">Pilih kategori, Mata Pelajaran, dan bank ujian untuk melihat soal yang tersedia</p>');

    // Reset data
    bankSoalData = [];
    selectedBankUjian = null;
    updateImportButton();
    $('#selectAllSoal').prop('checked', false);

    if (!jenisUjianId) return;

    // Load bank ujian
    console.log('Loading bank ujian untuk kategori:', kategori, 'jenis ujian:', jenisUjianId);

    $.ajax({
      url: `<?= base_url('admin/bank-soal/api/bank-ujian') ?>?kategori=${encodeURIComponent(kategori)}&jenis_ujian_id=${jenisUjianId}`,
      type: 'GET',
      dataType: 'json',
      timeout: 10000,
      success: function(data) {
        console.log('Bank ujian loaded:', data);

        if (data && data.status === 'success' && data.data) {
          data.data.forEach(bankUjian => {
            const option = $('<option></option>');
            option.val(bankUjian.bank_ujian_id);
            option.text(`${bankUjian.nama_ujian} (${bankUjian.jumlah_soal} soal) - ${bankUjian.creator_name}`);
            bankUjianSelect.append(option);
          });
          console.log('Bank ujian berhasil dimuat:', data.data.length, 'bank ujian');
        } else {
          console.warn('Data bank ujian tidak valid:', data);
          bankUjianSelect.append('<option value="" disabled>Tidak ada bank ujian</option>');
        }
      },
      error: function(xhr, status, error) {
        console.error('Error loading bank ujian:', error, xhr.responseText);
        bankUjianSelect.html('<option value="" disabled>Error loading bank ujian</option>');

        if (typeof Swal !== 'undefined') {
          Swal.fire({
            icon: 'error',
            title: 'Kesalahan',
            text: 'Gagal memuat bank ujian.',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
          });
        }
      }
    });
  }

  function onBankUjianChange() {
    const bankUjianId = $(this).val();
    console.log('Bank ujian changed:', bankUjianId);

    $('#searchBankSoal').prop('disabled', !bankUjianId);
    $('#selectAllSoal').prop('checked', false);
    updateImportButton();

    if (!bankUjianId) {
      $('#bankUjianInfo').hide();
      $('#bankSoalContainer').hide();
      $('#noBankSoalMessage').show().html('<p class="text-muted">Pilih kategori, Mata Pelajaran, dan bank ujian untuk melihat soal yang tersedia</p>');
      bankSoalData = [];
      selectedBankUjian = null;
      return;
    }

    // Show loading
    $('#loadingBankSoal').show();
    $('#bankSoalContainer').hide();
    $('#noBankSoalMessage').hide();

    console.log('Loading soal untuk bank ujian:', bankUjianId);

    $.ajax({
      url: `<?= base_url('admin/bank-soal/api/soal') ?>?bank_ujian_id=${bankUjianId}`,
      type: 'GET',
      dataType: 'json',
      timeout: 15000,
      success: function(data) {
        console.log('Soal loaded:', data);

        if (data && data.status === 'success') {
          bankSoalData = data.data || [];
          selectedBankUjian = data.bank_ujian;

          if (selectedBankUjian) {
            showBankUjianInfo(selectedBankUjian, bankSoalData.length);
          }

          renderBankSoal(bankSoalData);
          $('#bankSoalContainer').show();

          console.log('Soal berhasil dimuat:', bankSoalData.length, 'soal');
        } else {
          console.warn('Data soal tidak valid:', data);
          $('#noBankSoalMessage').show().html('<p class="text-danger">Gagal memuat soal: ' + (data.message || 'Data tidak valid') + '</p>');
          $('#bankSoalTableBody').empty();
        }
      },
      error: function(xhr, status, error) {
        console.error('Error loading soal:', error, xhr.responseText);
        $('#noBankSoalMessage').show().html('<p class="text-danger">Terjadi kesalahan saat memuat soal.</p>');
        $('#bankSoalTableBody').empty();

        if (typeof Swal !== 'undefined') {
          Swal.fire({
            icon: 'error',
            title: 'Kesalahan Jaringan',
            text: 'Terjadi kesalahan jaringan saat memuat soal. Silakan coba lagi.',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
          });
        }
      },
      complete: function() {
        $('#loadingBankSoal').hide();
      }
    });
  }

  function showBankUjianInfo(bankUjian, jumlahSoal) {
    $('#infoBankNama').text(bankUjian.nama_ujian || 'N/A');
    $('#infoBankKategori').text(bankUjian.kategori ? bankUjian.kategori.charAt(0).toUpperCase() + bankUjian.kategori.slice(1) : 'N/A');
    $('#infoBankPembuat').text(bankUjian.creator_name || 'System');
    $('#infoBankJumlahSoal').text(jumlahSoal);
    $('#infoBankDeskripsi').text(bankUjian.deskripsi || 'Tidak ada deskripsi');

    $('#bankUjianInfo').show();
  }

  function renderBankSoal(soalList) {
    const tbody = $('#bankSoalTableBody');
    const noDataMessage = $('#noBankSoalMessage');

    tbody.empty(); // Clear existing rows

    if (soalList.length === 0) {
      noDataMessage.show().html('<p class="text-muted">Tidak ada soal yang ditemukan dalam bank ujian ini.</p>');
      return;
    }

    noDataMessage.hide();

    const rows = soalList.map((soal, index) => `
        <tr data-soal-id="${soal.soal_id}">
            <td>
                <div class="form-check">
                    <input class="form-check-input check-soal-import" type="checkbox" name="soal_ids[]"
                            value="${soal.soal_id}">
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
                <button type="button" class="btn btn-sm btn-outline-info preview-soal-btn"
                         data-soal-id="${soal.soal_id}" title="Preview">
                    <i class="fas fa-eye"></i>
                </button>
            </td>
        </tr>
    `).join('');

    tbody.html(rows);

    // Attach event listener for individual checkboxes using delegation
    tbody.off('change', '.check-soal-import').on('change', '.check-soal-import', updateImportButton);
    // Attach event listener for preview buttons using delegation
    tbody.off('click', '.preview-soal-btn').on('click', '.preview-soal-btn', function() {
      previewSoal($(this).data('soal-id'));
    });

    $('#selectAllSoal').prop('checked', false);
    updateImportButton(); // Initial update
  }

  function filterSoalBySearch() {
    const search = $(this).val().toLowerCase();

    if (!bankSoalData || bankSoalData.length === 0) {
      renderBankSoal([]); // Clear table if no data to search
      return;
    }

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
    const checkedCount = $('input[name="soal_ids[]"]:checked').length;
    const btn = $('#btnImportSoal');

    btn.prop('disabled', checkedCount === 0);
    btn.html(checkedCount > 0 ?
      `<i class="fas fa-download me-2"></i>Import ${checkedCount} Soal Terpilih` :
      '<i class="fas fa-download me-2"></i>Import Soal Terpilih');

    // Update master checkbox if all or none are checked
    const allCheckboxes = $('input[name="soal_ids[]"]');
    if (allCheckboxes.length === checkedCount && allCheckboxes.length > 0) {
      $('#selectAllSoal').prop('checked', true);
    } else {
      $('#selectAllSoal').prop('checked', false);
    }
  }

  // Making previewSoal globally accessible
  window.previewSoal = function(soalId) {
    const soal = bankSoalData.find(s => s.soal_id == soalId);
    if (!soal) return;

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

    $('#previewSoalModal').remove(); // Ensure no duplicate modal
    $('body').append(modalHtml);
    const modal = new bootstrap.Modal(document.getElementById('previewSoalModal'));
    modal.show();
  }

  function truncateText(text, maxLength) {
    if (!text) return '';
    if (text.length <= maxLength) return text;
    return text.substring(0, maxLength) + '...';
  }

  function stripHtml(html) {
    const tmp = $('<div>');
    tmp.html(html);
    return tmp.text() || '';
  }

  // Document ready
  $(document).ready(function() {
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
    $('#tambahSoalModal').on('shown.bs.modal', function() {
      console.log('Modal tambah soal dibuka');
      initializeSummernoteAdd();
    });

    $('#tambahSoalModal').on('hidden.bs.modal', function() {
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

    // Event listener untuk modal edit (gunakan loop seperti di kode asli)
    <?php foreach ($soal as $s): ?>
      $('#editSoalModal<?= $s['soal_id'] ?>').on('shown.bs.modal', function() {
        initializeSummernoteEdit(<?= $s['soal_id'] ?>);
      });

      $('#editSoalModal<?= $s['soal_id'] ?>').on('hidden.bs.modal', function() {
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

    // === EVENT HANDLERS UNTUK IMPORT BANK SOAL ===

    // Event listener untuk import bank soal modal
    $('#importBankSoalModal').on('shown.bs.modal', function() {
      console.log('Import Bank Soal modal dibuka');
      loadKategori();
      resetImportModal();
    });

    // Event listeners untuk filter dropdown
    $('#filterKategoriImport').on('change', onKategoriChange);
    $('#filterJenisUjianImport').on('change', onJenisUjianChange);
    $('#filterBankUjianImport').on('change', onBankUjianChange);
    $('#searchBankSoal').on('input', filterSoalBySearch);

    // Event listener untuk select all checkbox
    $('#selectAllSoal').on('change', function() {
      const checkboxes = $('.check-soal-import:not(:disabled)');
      checkboxes.prop('checked', this.checked);
      updateImportButton();
    });

    // Event listener untuk import button
    $('#btnImportSoal').on('click', function() {
      const checkedCount = $('input[name="soal_ids[]"]:checked').length;

      if (checkedCount === 0) {
        if (typeof Swal !== 'undefined') {
          Swal.fire({
            icon: 'warning',
            title: 'Tidak Ada Soal',
            text: 'Pilih soal yang ingin diimpor terlebih dahulu!'
          });
        } else {
          alert('Pilih soal yang ingin diimpor terlebih dahulu!');
        }
        return;
      }

      // Konfirmasi impor
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          title: 'Konfirmasi Impor',
          text: `Apakah Anda yakin ingin mengimport ${checkedCount} soal yang dipilih?`,
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Ya, Impor!',
          cancelButtonText: 'Batal'
        }).then((result) => {
          if (result.isConfirmed) {
            $('#formImportSoal').submit();
          }
        });
      } else {
        if (confirm(`Apakah Anda yakin ingin mengimport ${checkedCount} soal yang dipilih?`)) {
          $('#formImportSoal').submit();
        }
      }
    });

    // Event listener untuk memastikan Summernote menyimpan data terbaru ke textarea
    $('form').on('submit', function(e) {
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

    $('#tambahSoalModal').on('hidden.bs.modal', function() {
      // Hanya cleanup jika modal ditutup tanpa submit form
      if (!$(this).data('form-submitted')) {
        $.ajax({
          url: '<?= base_url('admin/cleanup-temp-images') ?>',
          type: 'POST',
          silent: true,
          error: function() {
            // Silent failure - tidak perlu tampilkan error
          }
        });
      }
      // Reset flag
      $(this).data('form-submitted', false);
    });

    // Handle cleanup untuk modal edit
    <?php foreach ($soal as $s): ?>
      $('#editSoalModal<?= $s['soal_id'] ?>').on('hidden.bs.modal', function() {
        if (!$(this).data('form-submitted')) {
          $.ajax({
            url: '<?= base_url('admin/cleanup-temp-images') ?>',
            type: 'POST',
            silent: true,
            error: function() {
              // Silent failure
            }
          });
        }
        $(this).data('form-submitted', false);
      });
    <?php endforeach; ?>
  });

  // Track gambar yang ada saat editor pertama kali load
  $('.summernote').each(function() {
    const editor = $(this);
    setTimeout(() => {
      if (editor.data('summernote')) {
        const originalContent = editor.summernote('code');
        editor.data('original-images', extractImagesFromContent(originalContent));
      }
    }, 1000); // Delay untuk memastikan editor sudah fully initialized
  });

  // Set flag ketika form di-submit (supaya tidak cleanup)
  $('form').on('submit', function() {
    const modalId = $(this).closest('.modal').attr('id');
    if (modalId) {
      $('#' + modalId).data('form-submitted', true);
    }
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

  // Function untuk detect perubahan gambar di editor
  function detectImageChanges(editorId) {
    const editor = $('#' + editorId);
    if (!editor.data('summernote')) return;

    const currentContent = editor.summernote('code');
    const currentImages = extractImagesFromContent(currentContent);
    const originalImages = editor.data('original-images') || [];

    // Update original images untuk tracking selanjutnya
    editor.data('original-images', currentImages);

    // Kirim info gambar yang dihapus ke server (opsional - untuk advanced tracking)
    const removedImages = originalImages.filter(img => !currentImages.includes(img));
    if (removedImages.length > 0) {
      console.log('Detected removed images:', removedImages);
      // Bisa kirim AJAX request untuk track removed images jika perlu
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


  /* Forcing display only when 'show' class is present is handled by Bootstrap/Summernote JS,
       so removing the !important rules here which might conflict with their internal logic.
       The z-index should be sufficient. */
  /* .note-dropdown-menu:not(.show),
    .note-table .dropdown-menu:not(.show) {
        display: none;
    }

    .note-dropdown-menu.show,
    .note-table .dropdown-menu.show {
        display: block;
    } */

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

  /* Image upload progress */
  .note-editable .uploading {
    background-color: #f8f9fa;
    border: 2px dashed #dee2e6;
    padding: 20px;
    text-align: center;
    color: #6c757d;
    border-radius: 8px;
    margin: 10px 0;
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

  /* 3. Fix Dropdown Display Issues */
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

  /* 4. Fix Color Picker */
  .note-color .dropdown-menu {
    padding: 0.5rem;
  }

  .note-color-palette {
    line-height: 1;
  }

  .note-color-palette .note-color-btn {
    width: 20px;
    height: 20px;
    border: 1px solid #dee2e6;
    margin: 1px;
    cursor: pointer;
    border-radius: 2px;
  }

  .note-color-palette .note-color-btn:hover {
    border-color: #007bff;
    transform: scale(1.1);
  }

  /* 5. Fix Table Dimension Picker */
  .note-table .dropdown-menu {
    padding: 1rem;
  }

  .note-table .note-dimension-picker {
    position: relative !important;
    z-index: 99999 !important;
  }

  .note-table .note-dimension-picker .note-dimension-picker-mousecatcher {
    position: absolute !important;
    z-index: 99999 !important;
    width: 10em !important;
    height: 10em !important;
    cursor: pointer !important;
    top: 0;
    left: 0;
  }

  .note-table .note-dimension-picker .note-dimension-picker-unhighlighted {
    position: absolute;
    z-index: 99998;
    width: 5em;
    height: 5em;
    background: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAIAQMAAAD+wSzIAAAABlBMVEX///+/v7+jQ3Y5AAAADklEQVQI12P4AIX8EAgAw/AKAHlFae4AAAAASUVORK5CYII=') repeat;
    border: 1px solid #dee2e6;
  }

  .note-table .note-dimension-picker .note-dimension-picker-highlighted {
    position: absolute;
    z-index: 99999;
    width: 1em;
    height: 1em;
    background: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAIAQMAAAD+wSzIAAAABlBMVEUAAABBQUE6faMoAAAADklEQVQI12P4AIX8EAgAw/AKAHlFae4AAAAASUVORK5CYII=') repeat;
    background-color: #007bff;
    border: 1px solid #0056b3;
  }

  /* 6. Fix Paragraph/Style Dropdown */
  .note-para .dropdown-menu {
    min-width: 180px;
  }

  .note-para .dropdown-menu .dropdown-item {
    padding: 0.375rem 1rem;
    transition: all 0.15s ease-in-out;
  }

  .note-para .dropdown-menu .dropdown-item:hover {
    background-color: #f8f9fa;
    color: #007bff;
  }

  /* 7. Fix Font Size Dropdown */
  .note-fontsize .dropdown-menu {
    min-width: 120px;
    max-height: 200px;
  }

  .note-fontsize .dropdown-menu .dropdown-item {
    text-align: center;
    padding: 0.25rem 0.5rem;
  }

  /* 8. Enhanced Button Styling */
  .note-btn {
    pointer-events: auto !important;
    cursor: pointer;
    border: 1px solid transparent;
    background-color: transparent;
    padding: 0.375rem 0.75rem;
    margin: 0;
    line-height: 1.5;
    border-radius: 0.25rem;
    transition: all 0.15s ease-in-out;
    position: relative;
  }

  .note-btn:hover,
  .note-btn:focus {
    background-color: #e9ecef;
    border-color: #adb5bd;
    outline: 0;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
  }

  .note-btn:active,
  .note-btn.active {
    background-color: #007bff;
    border-color: #007bff;
    color: #fff;
  }

  /* 9. Fix Dropdown Toggle Indicators */
  .note-toolbar .dropdown-toggle::after {
    content: "";
    border-top: 0.3em solid;
    border-right: 0.3em solid transparent;
    border-bottom: 0;
    border-left: 0.3em solid transparent;
    margin-left: 0.5em;
    vertical-align: 0.1em;
  }

  .note-toolbar .dropdown-toggle:empty::after {
    margin-left: 0;
  }

  /* 10. Modal Content Adjustments */
  .modal-xl .note-editor {
    margin-bottom: 1rem;
  }

  .modal-body .note-toolbar {
    border-radius: 0.25rem 0.25rem 0 0;
    border-bottom: 1px solid #dee2e6;
  }

  .modal-body .note-editing-area {
    border-radius: 0 0 0.25rem 0.25rem;
  }

  /* 11. Responsive Fixes */
  @media (max-width: 768px) {
    .note-toolbar {
      flex-wrap: wrap;
      white-space: normal;
    }

    .note-toolbar .note-btn {
      padding: 0.25rem 0.5rem;
      font-size: 0.875rem;
    }

    .note-dropdown-menu {
      max-width: 90vw;
      left: 0 !important;
      right: auto !important;
    }

    .modal-xl {
      max-width: 95%;
      margin: 0.5rem;
    }
  }

  /* 12. Fix untuk Image dalam Editor */
  .note-editable img {
    max-width: 100%;
    height: auto;
    border-radius: 0.25rem;
    margin: 0.25rem 0;
  }

  .note-editable img.img-fluid {
    max-width: 100%;
    height: auto;
  }

  /* 13. Link Dialog Fix */
  .note-link-dialog .modal-content,
  .note-image-dialog .modal-content {
    z-index: 99999 !important;
  }

  /* 14. Code View Fix */
  .note-codable {
    border: 1px solid #dee2e6;
    border-radius: 0 0 0.25rem 0.25rem;
    background-color: #f8f9fa;
    font-family: 'Courier New', monospace;
    font-size: 0.875rem;
  }

  /* 15. Additional Utility Classes */
  .note-toolbar-wrapper {
    position: relative;
    z-index: 1056;
  }

  .note-statusbar {
    border-top: 1px solid #dee2e6;
    background-color: #f8f9fa;
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
    color: #6c757d;
  }

  /* 16. Loading States */
  .note-editor.loading {
    opacity: 0.6;
    pointer-events: none;
  }

  .note-editor.loading::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.8);
    z-index: 9999;
  }

  .note-editor.loading::after {
    content: 'Loading...';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 10000;
    font-weight: bold;
    color: #007bff;
  }
</style>

<?= $this->endSection() ?>