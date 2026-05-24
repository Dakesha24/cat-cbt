<?= $this->extend('templates/admin/admin_template') ?>
<?= $this->section('content') ?>

<style>
/* Page layout */
.breadcrumb { background: none; padding: 0; margin: 0; }
.breadcrumb-item + .breadcrumb-item::before { color: #adb5bd; }
.breadcrumb-item a { color: #6c757d; text-decoration: none; font-size: 0.875rem; }
.breadcrumb-item a:hover { color: #0d6efd; }
.breadcrumb-item.active { font-size: 0.875rem; color: #495057; }

/* Soal table card */
.soal-card { border-radius: 0; border: none; }
.soal-card .card-header { background: #fff; border-bottom: 2px solid #e9ecef; padding: 0.75rem 1.25rem; }

/* Table base */
.soal-table { width: 100%; table-layout: fixed; border-collapse: collapse; }
.soal-table thead th { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #9ca3af; background: #f9fafb; border-bottom: 1px solid #e9ecef; padding: 0.55rem 0.875rem; white-space: nowrap; }
.soal-table tbody td { padding: 0.75rem 0.875rem; vertical-align: middle; border-bottom: 1px solid #f3f4f6; }
.soal-table tbody tr:last-child td { border-bottom: 0; }
.soal-table tbody tr:hover td { background: #f8f9ff; }

/* Number circle */
.soal-no { display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; border-radius: 50%; background: #eff6ff; color: #3b82f6; font-size: 0.78rem; font-weight: 700; }

/* Kode soal */
.soal-kode { font-size: 0.72rem; font-weight: 700; color: #6366f1; letter-spacing: 0.4px; font-family: monospace; }

/* Question preview — 2-line clamp */
.q-text { font-size: 0.875rem; color: #1e293b; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }

/* Badges */
.badge-pill { display: inline-block; padding: 0.25em 0.6em; font-size: 0.72rem; font-weight: 600; border-radius: 999px; }
.bp-green  { background: #dcfce7; color: #166534; }
.bp-blue   { background: #dbeafe; color: #1e40af; }
.bp-yellow { background: #fef9c3; color: #854d0e; }
.bp-red    { background: #fee2e2; color: #991b1b; }

/* Foto dot */
.foto-dot { display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: #10b981; }

/* Empty state */
.empty-state { padding: 3rem 1rem; text-align: center; }
.empty-state i { font-size: 2.5rem; color: #adb5bd; display: block; margin-bottom: 0.75rem; }
.empty-state h6 { color: #6c757d; margin-bottom: 0.25rem; }
.empty-state p { font-size: 0.85rem; color: #adb5bd; }

/* Modal scroll fix — allow full modal to scroll, not just body */
.modal { overflow-y: auto !important; }
.modal-content { border: none; border-radius: 0; box-shadow: 0 8px 32px rgba(0,0,0,0.15); }

/* Summernote inside modal */
.note-editor.note-frame { border: 1px solid #dee2e6; border-radius: 0.25rem; }
.modal .note-popover { z-index: 1200 !important; }
.modal .note-dropdown-menu { z-index: 1300 !important; }

/* Detail modal */
.detail-section { background: #f8f9fa; border-radius: 0.375rem; padding: 0.875rem 1rem; }
.detail-label { font-size: 0.72rem; font-weight: 700; color: #6c757d; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.3rem; }
.pilihan-item { display: flex; gap: 0.625rem; align-items: flex-start; padding: 0.5rem 0.75rem; border-radius: 0.25rem; border: 1px solid #e9ecef; background: #fff; margin-bottom: 0.375rem; font-size: 0.875rem; }
.pilihan-item.correct { border-color: #10b981; background: #ecfdf5; }
.pilihan-badge { flex-shrink: 0; width: 22px; height: 22px; border-radius: 50%; background: #e7f0ff; color: #0d6efd; font-size: 0.7rem; font-weight: 700; display: flex; align-items: center; justify-content: center; }
.pilihan-item.correct .pilihan-badge { background: #10b981; color: #fff; }
</style>

<div class="container-fluid py-4">

  <div class="d-flex flex-wrap justify-content-between align-items-start mb-4">
    <div>
      <nav aria-label="breadcrumb" class="mb-1">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?= base_url('admin/bank-soal') ?>"><i class="bi bi-journals me-1"></i>Bank Soal</a></li>
          <li class="breadcrumb-item"><a href="<?= base_url('admin/bank-soal/kategori/' . urlencode($kategori)) ?>"><?= $kategori === 'umum' ? 'Umum' : 'Kelas ' . esc($kategori) ?></a></li>
          <li class="breadcrumb-item"><a href="<?= base_url('admin/bank-soal/kategori/' . urlencode($kategori) . '/jenis-ujian/' . $bankUjian['jenis_ujian_id']) ?>"><?= esc($bankUjian['nama_jenis']) ?></a></li>
          <li class="breadcrumb-item active"><?= esc($bankUjian['nama_ujian']) ?></li>
        </ol>
      </nav>
      <h2 class="fw-bold text-dark mb-1"><?= esc($bankUjian['nama_ujian']) ?></h2>
      <p class="text-muted mb-0"><?= esc($bankUjian['nama_jenis']) ?> — <?= $kategori === 'umum' ? 'Umum' : 'Kelas ' . esc($kategori) ?></p>
    </div>
    <div class="d-flex gap-2 mt-1">
      <a href="<?= base_url('admin/bank-soal/kategori/' . urlencode($kategori) . '/jenis-ujian/' . $bankUjian['jenis_ujian_id']) ?>" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Kembali
      </a>
      <?php if ($canEdit): ?>
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahSoal">
          <i class="bi bi-plus-lg me-1"></i>Tambah Soal
        </button>
      <?php endif; ?>
    </div>
  </div>

  <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm"><i class="bi bi-check-circle-fill me-2"></i><?= session()->getFlashdata('success') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
  <?php endif; ?>
  <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm"><i class="bi bi-exclamation-triangle-fill me-2"></i><?= session()->getFlashdata('error') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
  <?php endif; ?>
  <?php if (!empty($bankUjian['deskripsi'])): ?>
    <div class="alert alert-info alert-dismissible fade show border-0 shadow-sm py-2 mb-3">
      <i class="bi bi-info-circle me-2"></i><small><?= esc($bankUjian['deskripsi']) ?></small>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <div class="card border-0 shadow-sm soal-card">
    <div class="card-header d-flex align-items-center justify-content-between">
      <span class="fw-semibold text-dark">Daftar Soal</span>
      <small class="text-muted"><?= count($soalList) ?> soal</small>
    </div>
    <div class="card-body p-0">
      <?php if (empty($soalList)): ?>
        <div class="empty-state">
          <i class="bi bi-inbox"></i>
          <h6>Belum ada soal</h6>
          <p>Belum ada soal dalam bank soal ini<?= $canEdit ? '. Klik "Tambah Soal" untuk mulai.' : '.' ?></p>
          <?php if ($canEdit): ?>
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahSoal">
              <i class="bi bi-plus-lg me-1"></i>Tambah Soal Pertama
            </button>
          <?php endif; ?>
        </div>
      <?php else: ?>
        <div class="table-responsive">
          <table class="soal-table">
            <colgroup>
              <col style="width:52px">
              <col style="width:110px">
              <col>
              <col style="width:72px">
              <col style="width:90px">
              <col style="width:85px">
              <?php if ($canEdit): ?><col style="width:110px"><?php endif; ?>
            </colgroup>
            <thead>
              <tr>
                <th class="text-center">#</th>
                <th>Kode</th>
                <th>Pertanyaan</th>
                <th class="text-center">Jawaban</th>
                <th class="text-center">Kesulitan</th>
                <th>Tanggal</th>
                <?php if ($canEdit): ?><th>Aksi</th><?php endif; ?>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($soalList as $i => $soal):
                $d = (float)$soal['tingkat_kesulitan'];
                $dBadge = $d <= -1 ? 'bp-green' : ($d <= 1 ? 'bp-yellow' : 'bp-red');
                $dLabel  = $d <= -1 ? 'Mudah'    : ($d <= 1 ? 'Sedang'   : 'Sulit');
              ?>
                <tr>
                  <td class="text-center"><span class="soal-no"><?= $i + 1 ?></span></td>
                  <td><span class="soal-kode"><?= esc($soal['kode_soal']) ?></span></td>
                  <td style="min-width:0"><div class="q-text"><?= esc(strip_tags($soal['pertanyaan'])) ?: '<em class="text-muted">(tidak ada teks)</em>' ?></div></td>
                  <td class="text-center"><span class="badge-pill bp-green"><?= esc($soal['jawaban_benar']) ?></span></td>
                  <td class="text-center"><span class="badge-pill <?= $dBadge ?>"><?= $dLabel ?></span></td>
                  <td style="font-size:0.78rem;color:#9ca3af"><?= date('d/m/Y', strtotime($soal['created_at'])) ?></td>
                  <?php if ($canEdit): ?>
                    <td>
                      <div class="d-flex gap-1">
                        <button class="btn btn-sm btn-light border" style="padding:0.2rem 0.45rem" data-bs-toggle="modal" data-bs-target="#detailModal<?= $soal['soal_id'] ?>" title="Detail"><i class="bi bi-eye" style="font-size:0.8rem"></i></button>
                        <button class="btn btn-sm btn-warning" style="padding:0.2rem 0.45rem" data-bs-toggle="modal" data-bs-target="#editModal<?= $soal['soal_id'] ?>" title="Edit"><i class="bi bi-pencil" style="font-size:0.8rem"></i></button>
                        <button class="btn btn-sm btn-danger" style="padding:0.2rem 0.45rem" onclick="hapusSoal(<?= $soal['soal_id'] ?>)" title="Hapus"><i class="bi bi-trash" style="font-size:0.8rem"></i></button>
                      </div>
                    </td>
                  <?php endif; ?>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </div>

</div>

<!-- Modal Tambah Soal -->
<?php if ($canEdit): ?>
  <div class="modal fade" id="modalTambahSoal" tabindex="-1" data-bs-focus="false">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white px-4 py-3">
          <h5 class="modal-title fw-semibold"><i class="bi bi-plus-circle me-2"></i>Tambah Soal</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <form action="<?= base_url('admin/bank-soal/tambah-soal') ?>" method="post" enctype="multipart/form-data">
          <?= csrf_field() ?>
          <input type="hidden" name="bank_ujian_id" value="<?= $bankUjian['bank_ujian_id'] ?>">
          <div class="modal-body px-4 py-3">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label small fw-semibold">Kode Soal <span class="text-danger">*</span></label>
                <input type="text" name="kode_soal" class="form-control" placeholder="Contoh: MAT001" required>
              </div>
              <div class="col-md-2">
                <label class="form-label small fw-semibold">Diskriminasi (a)</label>
                <input type="number" name="a" class="form-control" step="0.001" value="1.000">
              </div>
              <div class="col-md-2">
                <label class="form-label small fw-semibold">Kesulitan (b) <span class="text-danger">*</span></label>
                <input type="number" name="tingkat_kesulitan" class="form-control" step="0.001" value="0.000" min="-3" max="3" required>
                <div class="form-text">-3 s/d +3</div>
              </div>
              <div class="col-md-2">
                <label class="form-label small fw-semibold">Guessing (c)</label>
                <input type="number" name="c" class="form-control" step="0.001" value="0.000">
              </div>
              <div class="col-12">
                <label class="form-label small fw-semibold">Pertanyaan <span class="text-danger">*</span></label>
                <textarea name="pertanyaan" id="pertanyaan_tambah" class="form-control summernote" required placeholder="Masukkan pertanyaan soal..."></textarea>
              </div>
              <div class="col-12"><hr class="my-1"><p class="small fw-semibold text-muted mb-0">Pilihan Jawaban</p></div>
              <div class="col-md-6">
                <label class="form-label small fw-semibold text-primary">A. <span class="text-danger">*</span></label>
                <textarea name="pilihan_a" id="pilihan_a_tambah" class="form-control summernote-small" required></textarea>
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-semibold text-primary">B. <span class="text-danger">*</span></label>
                <textarea name="pilihan_b" id="pilihan_b_tambah" class="form-control summernote-small" required></textarea>
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-semibold text-primary">C. <span class="text-danger">*</span></label>
                <textarea name="pilihan_c" id="pilihan_c_tambah" class="form-control summernote-small" required></textarea>
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-semibold text-primary">D. <span class="text-danger">*</span></label>
                <textarea name="pilihan_d" id="pilihan_d_tambah" class="form-control summernote-small" required></textarea>
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-semibold text-warning">E. <span class="fw-normal text-muted">(opsional)</span></label>
                <textarea name="pilihan_e" id="pilihan_e_tambah" class="form-control summernote-small"></textarea>
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-semibold">Jawaban Benar <span class="text-danger">*</span></label>
                <select name="jawaban_benar" class="form-select" required>
                  <option value="">Pilih Jawaban Benar</option>
                  <option value="A">A</option>
                  <option value="B">B</option>
                  <option value="C">C</option>
                  <option value="D">D</option>
                  <option value="E">E</option>
                </select>
              </div>
              <div class="col-12">
                <label class="form-label small fw-semibold">Pembahasan <span class="text-muted fw-normal">(opsional)</span></label>
                <textarea name="pembahasan" id="pembahasan_tambah" class="form-control summernote" placeholder="Masukkan pembahasan soal..."></textarea>
                <div class="form-text">Ditampilkan kepada siswa setelah ujian selesai</div>
              </div>
            </div>
          </div>
          <div class="modal-footer border-top bg-light px-4 py-3">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-lg me-1"></i>Simpan Soal</button>
          </div>
        </form>
      </div>
    </div>
  </div>
<?php endif; ?>

<!-- Modal Detail Soal -->
<?php foreach ($soalList as $soal):
  $d = (float)$soal['tingkat_kesulitan'];
  $dLabel = $d <= -1 ? 'Mudah' : ($d <= 1 ? 'Sedang' : 'Sulit');
  $dColor = $d <= -1 ? 'success' : ($d <= 1 ? 'warning' : 'danger');
?>
  <div class="modal fade" id="detailModal<?= $soal['soal_id'] ?>" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header px-4 py-3 border-bottom">
          <div>
            <h5 class="modal-title fw-semibold mb-0">Detail Soal</h5>
            <div class="d-flex gap-2 mt-1">
              <span class="badge bg-primary"><?= esc($soal['kode_soal']) ?></span>
              <span class="badge bg-success">Jawaban: <?= esc($soal['jawaban_benar']) ?></span>
              <span class="badge bg-<?= $dColor ?> <?= $dColor === 'warning' ? 'text-dark' : '' ?>"><?= $dLabel ?> (<?= number_format($d, 3) ?>)</span>
            </div>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body px-4 py-3">
          <p class="detail-label">Pertanyaan</p>
          <div class="detail-section mb-3"><?= $soal['pertanyaan'] ?></div>

          <?php if (!empty($soal['foto'])): ?>
            <div class="text-center mb-3">
              <img src="<?= base_url('uploads/soal/' . $soal['foto']) ?>" alt="Foto Soal" class="img-fluid rounded shadow-sm" style="max-height:200px">
            </div>
          <?php endif; ?>

          <p class="detail-label">Pilihan Jawaban</p>
          <?php foreach (['A','B','C','D'] as $opt): ?>
            <div class="pilihan-item <?= $soal['jawaban_benar'] == $opt ? 'correct' : '' ?>">
              <span class="pilihan-badge"><?= $opt ?></span>
              <span><?= $soal['pilihan_' . strtolower($opt)] ?></span>
            </div>
          <?php endforeach; ?>
          <?php if (!empty($soal['pilihan_e'])): ?>
            <div class="pilihan-item <?= $soal['jawaban_benar'] == 'E' ? 'correct' : '' ?>">
              <span class="pilihan-badge">E</span>
              <span><?= $soal['pilihan_e'] ?></span>
            </div>
          <?php endif; ?>

          <?php if (!empty($soal['pembahasan'])): ?>
            <p class="detail-label mt-3">Pembahasan</p>
            <div class="detail-section"><?= $soal['pembahasan'] ?></div>
          <?php endif; ?>
        </div>
        <div class="modal-footer border-top px-4 py-3">
          <?php if ($canEdit): ?>
            <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#editModal<?= $soal['soal_id'] ?>">
              <i class="bi bi-pencil me-1"></i>Edit Soal
            </button>
          <?php endif; ?>
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
        </div>
      </div>
    </div>
  </div>
<?php endforeach; ?>

<!-- Modal Edit Soal -->
<?php if ($canEdit): ?>
  <?php foreach ($soalList as $soal): ?>
    <div class="modal fade" id="editModal<?= $soal['soal_id'] ?>" tabindex="-1" data-bs-focus="false">
      <div class="modal-dialog modal-xl">
        <div class="modal-content">
          <div class="modal-header bg-warning px-4 py-3">
            <h5 class="modal-title fw-semibold"><i class="bi bi-pencil me-2"></i>Edit Soal — <span class="fw-normal"><?= esc($soal['kode_soal']) ?></span></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form action="<?= base_url('admin/bank-soal/edit-soal/' . $soal['soal_id']) ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="modal-body px-4 py-3">
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label small fw-semibold">Kode Soal <span class="text-danger">*</span></label>
                  <input type="text" name="kode_soal" class="form-control" value="<?= esc($soal['kode_soal']) ?>" required>
                </div>
                <div class="col-md-2">
                  <label class="form-label small fw-semibold">Diskriminasi (a)</label>
                  <input type="number" name="a" class="form-control" step="0.001" value="<?= $soal['a'] ?? 1 ?>">
                </div>
                <div class="col-md-2">
                  <label class="form-label small fw-semibold">Kesulitan (b) <span class="text-danger">*</span></label>
                  <input type="number" name="tingkat_kesulitan" class="form-control" step="0.001" value="<?= $soal['tingkat_kesulitan'] ?>" min="-3" max="3" required>
                </div>
                <div class="col-md-2">
                  <label class="form-label small fw-semibold">Guessing (c)</label>
                  <input type="number" name="c" class="form-control" step="0.001" value="<?= $soal['c'] ?? 0 ?>">
                </div>
                <div class="col-12">
                  <label class="form-label small fw-semibold">Pertanyaan <span class="text-danger">*</span></label>
                  <textarea name="pertanyaan" id="pertanyaan_edit_<?= $soal['soal_id'] ?>" class="form-control summernote" required><?= esc($soal['pertanyaan']) ?></textarea>
                </div>
                <div class="col-12"><hr class="my-1"><p class="small fw-semibold text-muted mb-0">Pilihan Jawaban</p></div>
                <div class="col-md-6">
                  <label class="form-label small fw-semibold text-primary">A. <span class="text-danger">*</span></label>
                  <textarea name="pilihan_a" id="pilihan_a_edit_<?= $soal['soal_id'] ?>" class="form-control summernote-small" required><?= esc($soal['pilihan_a']) ?></textarea>
                </div>
                <div class="col-md-6">
                  <label class="form-label small fw-semibold text-primary">B. <span class="text-danger">*</span></label>
                  <textarea name="pilihan_b" id="pilihan_b_edit_<?= $soal['soal_id'] ?>" class="form-control summernote-small" required><?= esc($soal['pilihan_b']) ?></textarea>
                </div>
                <div class="col-md-6">
                  <label class="form-label small fw-semibold text-primary">C. <span class="text-danger">*</span></label>
                  <textarea name="pilihan_c" id="pilihan_c_edit_<?= $soal['soal_id'] ?>" class="form-control summernote-small" required><?= esc($soal['pilihan_c']) ?></textarea>
                </div>
                <div class="col-md-6">
                  <label class="form-label small fw-semibold text-primary">D. <span class="text-danger">*</span></label>
                  <textarea name="pilihan_d" id="pilihan_d_edit_<?= $soal['soal_id'] ?>" class="form-control summernote-small" required><?= esc($soal['pilihan_d']) ?></textarea>
                </div>
                <div class="col-md-6">
                  <label class="form-label small fw-semibold text-warning">E. <span class="fw-normal text-muted">(opsional)</span></label>
                  <textarea name="pilihan_e" id="pilihan_e_edit_<?= $soal['soal_id'] ?>" class="form-control summernote-small"><?= isset($soal['pilihan_e']) ? esc($soal['pilihan_e']) : '' ?></textarea>
                </div>
                <div class="col-md-6">
                  <label class="form-label small fw-semibold">Jawaban Benar <span class="text-danger">*</span></label>
                  <select name="jawaban_benar" class="form-select" required>
                    <option value="">Pilih Jawaban Benar</option>
                    <option value="A" <?= $soal['jawaban_benar'] == 'A' ? 'selected' : '' ?>>A</option>
                    <option value="B" <?= $soal['jawaban_benar'] == 'B' ? 'selected' : '' ?>>B</option>
                    <option value="C" <?= $soal['jawaban_benar'] == 'C' ? 'selected' : '' ?>>C</option>
                    <option value="D" <?= $soal['jawaban_benar'] == 'D' ? 'selected' : '' ?>>D</option>
                    <option value="E" <?= $soal['jawaban_benar'] == 'E' ? 'selected' : '' ?>>E</option>
                  </select>
                </div>
                <div class="col-12">
                  <label class="form-label small fw-semibold">Pembahasan <span class="text-muted fw-normal">(opsional)</span></label>
                  <textarea name="pembahasan" id="pembahasan_edit_<?= $soal['soal_id'] ?>" class="form-control summernote"><?= isset($soal['pembahasan']) ? esc($soal['pembahasan']) : '' ?></textarea>
                </div>
              </div>
            </div>
            <div class="modal-footer border-top bg-light px-4 py-3">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
              <button type="submit" class="btn btn-warning px-4"><i class="bi bi-check-lg me-1"></i>Simpan Perubahan</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
<?php endif; ?>

<script>
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
    dialogsFade: false,
    container: 'body',
    callbacks: {
      onImageUpload: function(files) {
        if (files && files.length > 0 && !$(this).data('uploading')) {
          uploadImageSimple(files[0], this);
        }
      }
    }
  };

  const summernoteConfigSmall = {
    height: 150,
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
    placeholder: 'Masukkan pilihan...',
    dialogsInBody: true,
    dialogsFade: false,
    container: 'body',
    callbacks: {
      onImageUpload: function(files) {
        if (files && files.length > 0 && !$(this).data('uploading')) {
          uploadImageSimple(files[0], this);
        }
      }
    }
  };

  function initSummernoteAdd() {
    destroySummernote(['#pertanyaan_tambah','#pilihan_a_tambah','#pilihan_b_tambah','#pilihan_c_tambah','#pilihan_d_tambah','#pilihan_e_tambah','#pembahasan_tambah']);
    setTimeout(function() {
      $('#pertanyaan_tambah').summernote(summernoteConfig);
      $('#pilihan_a_tambah,#pilihan_b_tambah,#pilihan_c_tambah,#pilihan_d_tambah,#pilihan_e_tambah').summernote(summernoteConfigSmall);
      $('#pembahasan_tambah').summernote(summernoteConfig);
    }, 150);
  }

  function initSummernoteEdit(soalId) {
    var ids = ['#pertanyaan_edit_'+soalId,'#pilihan_a_edit_'+soalId,'#pilihan_b_edit_'+soalId,'#pilihan_c_edit_'+soalId,'#pilihan_d_edit_'+soalId,'#pilihan_e_edit_'+soalId,'#pembahasan_edit_'+soalId];
    destroySummernote(ids);
    setTimeout(function() {
      $('#pertanyaan_edit_'+soalId).summernote(summernoteConfig);
      $('#pilihan_a_edit_'+soalId+',#pilihan_b_edit_'+soalId+',#pilihan_c_edit_'+soalId+',#pilihan_d_edit_'+soalId+',#pilihan_e_edit_'+soalId).summernote(summernoteConfigSmall);
      $('#pembahasan_edit_'+soalId).summernote(summernoteConfig);
    }, 150);
  }

  function destroySummernote(ids) {
    $.each(ids, function(_, id) {
      var $el = $(id);
      if ($el.length && $el.data('summernote')) {
        try { $el.summernote('destroy'); } catch(e) {}
        $el.siblings('.note-editor').remove();
        $el.show();
      }
    });
    if ($('.modal.show').length === 0) {
      $('body').removeClass('modal-open').css({'padding-right':'','overflow':''});
    }
    $('.note-modal-backdrop').remove();
  }

  function uploadImageSimple(file, editor) {
    if (!file.type.startsWith('image/')) { alert('Pilih file gambar!'); return; }
    if (file.size > 2 * 1024 * 1024) { alert('File terlalu besar! Maksimal 2MB.'); return; }
    var $editor = $(editor);
    if ($editor.data('uploading')) return;
    $editor.data('uploading', true);
    var formData = new FormData();
    formData.append('upload', file);
    $.ajax({
      url: '<?= base_url('admin/upload-summernote-image') ?>',
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      timeout: 30000,
      success: function(response) {
        $editor.data('uploading', false);
        if (response.success && response.url) {
          $editor.summernote('focus');
          $editor.summernote('insertImage', response.url, function($img) {
            $img.css({'max-width':'100%','height':'auto'}).addClass('img-fluid');
          });
        } else {
          alert('Upload gagal: ' + (response.error || 'Terjadi kesalahan'));
        }
      },
      error: function() {
        $editor.data('uploading', false);
        alert('Gagal upload gambar. Periksa koneksi Anda.');
      }
    });
  }

  function hapusSoal(soalId) {
    if (confirm('Yakin ingin menghapus soal ini? Tindakan tidak dapat dibatalkan.')) {
      window.location.href = '<?= base_url('admin/bank-soal/hapus-soal/') ?>' + soalId;
    }
  }

  $(document).ready(function() {
    $('#modalTambahSoal').on('shown.bs.modal', function() { initSummernoteAdd(); });
    $('#modalTambahSoal').on('hidden.bs.modal', function() {
      destroySummernote(['#pertanyaan_tambah','#pilihan_a_tambah','#pilihan_b_tambah','#pilihan_c_tambah','#pilihan_d_tambah','#pilihan_e_tambah','#pembahasan_tambah']);
    });

    <?php foreach ($soalList as $s): ?>
      $('#editModal<?= $s['soal_id'] ?>').on('shown.bs.modal', function() { initSummernoteEdit(<?= $s['soal_id'] ?>); });
      $('#editModal<?= $s['soal_id'] ?>').on('hidden.bs.modal', function() {
        destroySummernote(['#pertanyaan_edit_<?= $s['soal_id'] ?>','#pilihan_a_edit_<?= $s['soal_id'] ?>','#pilihan_b_edit_<?= $s['soal_id'] ?>','#pilihan_c_edit_<?= $s['soal_id'] ?>','#pilihan_d_edit_<?= $s['soal_id'] ?>','#pilihan_e_edit_<?= $s['soal_id'] ?>','#pembahasan_edit_<?= $s['soal_id'] ?>']);
      });
    <?php endforeach; ?>

    $('form').on('submit', function() {
      var $modal = $(this).closest('.modal');
      $modal.find('.summernote, .summernote-small').each(function() {
        if ($(this).data('summernote')) $(this).val($(this).summernote('code'));
      });
    });
  });
</script>

<?= $this->endSection() ?>
