<?= $this->extend('templates/admin/admin_template') ?>

<?= $this->section('content') ?>

<?php
$ujianCAT = array_filter($ujian ?? [], fn($u) => ($u['tipe_ujian'] ?? 'CAT') === 'CAT');
$ujianCBT = array_filter($ujian ?? [], fn($u) => ($u['tipe_ujian'] ?? '') === 'CBT');
$kelasUmumLabel = 'Semua Guru';
?>

<style>
.exam-page { background: linear-gradient(180deg, #f8fbff 0%, #ffffff 100%); border: 1px solid #e9eef5; }
.page-header-card { background: linear-gradient(135deg, #ffffff 0%, #f2f7ff 100%); border: 1px solid #dbe7ff; }
.exam-card { border-radius: 0; overflow: hidden; border: 1px solid #e9ecef; border-left: 4px solid #dee2e6; transition: transform 0.18s ease, box-shadow 0.2s ease; }
.exam-card.cat-card { border-left-color: #0d6efd; }
.exam-card.cbt-card { border-left-color: #198754; }
.exam-card:hover { transform: translateY(-2px); box-shadow: 0 12px 28px rgba(15, 23, 42, 0.10) !important; }
.stat-box { background: linear-gradient(180deg, #f8f9fa 0%, #f1f3f5 100%); padding: 0.6rem 0.5rem; text-align: center; border: 1px solid #e9ecef; }
.stat-box .stat-val { font-size: 0.8rem; font-weight: 700; color: #212529; line-height: 1.25; min-height: 2.4em; display: flex; align-items: center; justify-content: center; text-wrap: balance; }
.stat-box .stat-lbl { font-size: 0.68rem; color: #9ca3af; }
.exam-meta-pill { display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.28rem 0.6rem; border-radius: 999px; font-size: 0.72rem; font-weight: 600; line-height: 1; border: 1px solid transparent; }
.exam-meta-pill.school { background: #eef6ff; border-color: #cfe2ff; color: #0b5ed7; }
.exam-meta-pill.classroom { background: #eefbf3; border-color: #ccebd7; color: #137547; }
.exam-meta-pill.access { background: #fff8e1; border-color: #f4d68c; color: #9a6700; }
.exam-meta-pill.subject { background: #f8f9fa; border-color: #e5e7eb; color: #495057; }
.exam-code { color: #6b7280; font-size: 0.74rem; }
.exam-code i { color: #9ca3af; }
.btn-icon { width: 34px; height: 34px; display: inline-flex; align-items: center; justify-content: center; border: none; background: none; color: #9ca3af; border-radius: 4px; font-size: 1.1rem; transition: all 0.15s; }
.btn-icon:hover { background: #f0f0f0; color: #333; }
.line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.section-hd { display: flex; align-items: center; gap: 0.6rem; margin-bottom: 1rem; margin-top: 1.25rem; }
.section-hd .s-badge { font-size: 0.7rem; font-weight: 700; padding: 0.2em 0.55em; border-radius: 3px; letter-spacing: 0.04em; }
.section-hd .s-title { font-size: 0.9rem; font-weight: 600; color: #343a40; }
.section-hd .s-count { font-size: 0.8rem; color: #9ca3af; }
.section-hd .s-line { flex: 1; border-bottom: 1px solid #e9ecef; margin-left: 0.25rem; }
.type-divider { display: flex; align-items: center; gap: 1rem; margin: 0.75rem 0; }
.type-divider hr { flex: 1; margin: 0; border-color: #dee2e6; }
.type-divider span { font-size: 0.75rem; font-weight: 600; color: #adb5bd; text-transform: uppercase; letter-spacing: 0.06em; white-space: nowrap; }
.empty-sec { background: #f8f9fa; padding: 1.5rem; text-align: center; color: #adb5bd; font-size: 0.85rem; margin-bottom: 1rem; }
/* Modal ukuran & corner fix */
.exam-modal-dialog { max-width: 880px !important; }
.modal-content { border-radius: 0 !important; overflow: hidden; }
.modal-header, .modal-footer { border-radius: 0 !important; }
.modal-dialog-scrollable .modal-content { max-height: calc(100vh - 2.5rem); }
.modal-dialog-scrollable .modal-body { max-height: calc(100vh - 215px); overflow-y: auto; background: #f4f7fb; padding: 1.35rem !important; }
/* Disabled field styling */
.exam-modal-section .form-select:disabled,
.exam-modal-section .form-control:disabled {
  background-color: #eef1f5 !important;
  color: #9ca3af !important;
  border-color: #dde3ec !important;
  cursor: not-allowed;
  opacity: 1;
}
/* Sections */
.exam-modal-section {
  background: #fff;
  border: 1px solid #dde5f0;
  padding: 1rem 1.1rem;
  margin-bottom: 0.85rem;
  box-shadow: 0 1px 4px rgba(15,23,42,0.04);
}
.exam-modal-section:last-child { margin-bottom: 0; }
.exam-modal-section-title {
  display: flex; align-items: center; gap: 0.5rem;
  padding-bottom: 0.65rem; margin-bottom: 0.85rem !important;
  border-bottom: 1px solid #eef2f7; letter-spacing: 0.06em;
}
.exam-modal-section-title .section-dot {
  width: 8px; height: 8px;
  background: #3b82f6; border-radius: 50%;
  box-shadow: 0 0 0 3px rgba(59,130,246,.15); flex-shrink: 0;
}
.exam-modal-section-title small { letter-spacing: 0.06em; font-size: 0.7rem !important; }
.exam-modal-grid { background: transparent !important; border: 0 !important; padding: 0 !important; margin-left: 0; margin-right: 0; }
.exam-modal-help { color: #94a3b8; font-size: 0.73rem; line-height: 1.45; }
.exam-modal-section .form-label { font-size: 0.78rem; font-weight: 600; color: #374151; margin-bottom: 0.28rem; }
.exam-modal-section .form-control,
.exam-modal-section .form-select {
  border-color: #cdd5e0; background: #f9fbfd; font-size: 0.875rem;
  border-radius: 6px; transition: border-color .15s, box-shadow .15s;
}
.exam-modal-section .form-control:focus,
.exam-modal-section .form-select:focus {
  border-color: #3b82f6; background: #fff;
  box-shadow: 0 0 0 3px rgba(59,130,246,0.12);
}
.exam-modal-section textarea.form-control { resize: vertical; min-height: 68px; }
/* Option cards */
.exam-option-stack { display: grid; gap: 0.5rem; }
.exam-option-card {
  display: flex; align-items: flex-start; gap: 0.7rem;
  padding: 0.75rem 0.9rem; background: #fff;
  border: 1px solid #e2e8f0; border-radius: 6px;
  cursor: pointer; transition: border-color .15s, background .15s;
}
.exam-option-card:has(.form-check-input:checked) { border-color: #93c5fd; background: #eff6ff; }
.exam-option-card .form-check-input { margin-top: 0.15rem; flex-shrink: 0; }
.exam-option-card .form-check-label { display: block; margin: 0; cursor: pointer; }
.exam-option-title { color: #1e293b; font-size: 0.8rem; font-weight: 700; line-height: 1.3; }
.exam-option-desc { color: #94a3b8; font-size: 0.71rem; line-height: 1.45; margin-top: 0.12rem; }
@media (max-width: 767.98px) {
  .exam-page { padding: 1rem !important; }
  .page-header-card { padding: 1rem !important; }
  .section-hd { align-items: flex-start; flex-wrap: wrap; }
  .section-hd .s-line { display: none; }
  .exam-modal-section { padding: 0.85rem; }
}
</style>

<div class="container-fluid py-4">

  <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div>
      <h2 class="fw-bold text-dark mb-1">Kelola Ujian</h2>
      <p class="text-muted mb-0">Konfigurasi dan manajemen ujian untuk seluruh kelas</p>
    </div>
    <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahUjian">
      <i class="bi bi-plus-lg me-2"></i>Tambah Ujian
    </button>
  </div>

  <div class="exam-page shadow-sm px-3 px-md-4 py-4">

  <!-- Alerts -->
  <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
      <i class="bi bi-check-circle-fill me-2"></i><?= session()->getFlashdata('success') ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>
  <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
      <i class="bi bi-exclamation-triangle-fill me-2"></i>
      <?php $errs = session()->getFlashdata('error'); ?>
      <?php if (is_array($errs)): ?>
        <ul class="mb-0"><?php foreach ($errs as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?></ul>
      <?php else: ?>
        <?= esc($errs) ?>
      <?php endif; ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <?php if (empty($ujian)): ?>
    <div class="text-center py-5">
      <div class="mb-3"><i class="bi bi-journal-x text-muted" style="font-size:4rem"></i></div>
      <h5 class="text-muted">Belum ada ujian</h5>
      <p class="text-muted mb-3">Tambahkan ujian pertama Anda</p>
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahUjian">
        <i class="bi bi-plus-lg me-2"></i>Tambah Ujian
      </button>
    </div>
  <?php else: ?>

    <!-- ── CAT Section ── -->
    <div class="section-hd">
      <span class="s-badge bg-primary text-white s-badge">CAT</span>
      <span class="s-title">Computer Adaptive Test</span>
      <span class="s-count"><?= count($ujianCAT) ?> ujian</span>
      <div class="s-line"></div>
    </div>

    <?php if (!empty($ujianCAT)): ?>
      <div class="row g-3 mb-4">
        <?php foreach ($ujianCAT as $u): ?>
          <div class="col-xl-4 col-lg-6">
            <div class="card border-0 shadow-sm h-100 exam-card cat-card">
              <div class="card-body d-flex flex-column p-0">
                <div class="d-flex align-items-center justify-content-between px-4 pt-3 pb-2">
                  <span class="badge bg-primary bg-opacity-10 text-primary px-2 py-1" style="font-size:0.72rem;font-weight:700;border-radius:3px">CAT Adaptif</span>
                  <div class="dropdown">
                    <button class="btn-icon" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                      <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#modalEditUjian<?= $u['id_ujian'] ?>"><i class="bi bi-pencil me-2"></i>Edit</button></li>
                      <li><a class="dropdown-item" href="<?= base_url('admin/soal/' . $u['id_ujian']) ?>"><i class="bi bi-list-task me-2"></i>Kelola Soal</a></li>
                      <li><hr class="dropdown-divider"></li>
                      <li><a class="dropdown-item text-danger" href="<?= base_url('admin/ujian/hapus/' . $u['id_ujian']) ?>" onclick="return confirm('Hapus ujian ini?')"><i class="bi bi-trash me-2"></i>Hapus</a></li>
                    </ul>
                  </div>
                </div>
                <div class="px-4 pb-3 flex-grow-1">
                  <h5 class="fw-bold mb-1" style="font-size:1rem"><?= esc($u['nama_ujian']) ?></h5>
                  <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    <span class="exam-meta-pill subject"><?= esc($u['nama_jenis'] ?? 'Mata Pelajaran') ?></span>
                    <span class="exam-meta-pill school"><i class="bi bi-buildings"></i><?= !empty($u['nama_sekolah']) ? esc($u['nama_sekolah']) : 'Sekolah Umum' ?></span>
                    <?php if (!empty($u['nama_kelas'])): ?>
                      <span class="exam-meta-pill classroom"><i class="bi bi-mortarboard"></i><?= esc($u['nama_kelas']) ?></span>
                    <?php else: ?>
                      <span class="exam-meta-pill access"><i class="bi bi-people"></i><?= esc($kelasUmumLabel) ?></span>
                    <?php endif; ?>
                    <?php if (!empty($u['kode_ujian'])): ?>
                      <small class="exam-code"><i class="bi bi-key me-1"></i><?= esc($u['kode_ujian']) ?></small>
                    <?php endif; ?>
                  </div>
                  <p class="text-muted small mb-3 line-clamp-2"><?= esc($u['deskripsi']) ?></p>
                  <div class="row g-2 text-center mb-3">
                    <div class="col-4"><div class="stat-box"><div class="stat-val"><?= !empty($u['durasi']) ? esc($u['durasi']) : '-' ?></div><div class="stat-lbl">Durasi</div></div></div>
                    <div class="col-4"><div class="stat-box"><div class="stat-val"><?= !empty($u['nama_kelas']) ? esc($u['nama_kelas']) : esc($kelasUmumLabel) ?></div><div class="stat-lbl">Akses</div></div></div>
                    <div class="col-4"><div class="stat-box"><div class="stat-val"><?= !empty($u['nama_sekolah']) ? esc($u['nama_sekolah']) : 'Sekolah Umum' ?></div><div class="stat-lbl">Sekolah</div></div></div>
                  </div>
                </div>
                <div class="px-4 pb-4 mt-auto">
                  <a href="<?= base_url('admin/soal/' . $u['id_ujian']) ?>" class="btn btn-outline-primary w-100" style="border-radius:0">
                    <i class="bi bi-list-task me-2"></i>Kelola Soal
                  </a>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="empty-sec mb-4"><i class="bi bi-inbox me-2"></i>Belum ada ujian CAT</div>
    <?php endif; ?>

    <!-- ── CBT Section ── -->
    <div class="section-hd">
      <span class="s-badge bg-success text-white s-badge">CBT</span>
      <span class="s-title">Computer Based Test</span>
      <span class="s-count"><?= count($ujianCBT) ?> ujian</span>
      <div class="s-line"></div>
    </div>

    <?php if (!empty($ujianCBT)): ?>
      <div class="row g-3">
        <?php foreach ($ujianCBT as $u): ?>
          <div class="col-xl-4 col-lg-6">
            <div class="card border-0 shadow-sm h-100 exam-card cbt-card">
              <div class="card-body d-flex flex-column p-0">
                <div class="d-flex align-items-center justify-content-between px-4 pt-3 pb-2">
                  <span class="badge bg-success bg-opacity-10 text-success px-2 py-1" style="font-size:0.72rem;font-weight:700;border-radius:3px">CBT Fixed</span>
                  <div class="dropdown">
                    <button class="btn-icon" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                      <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#modalEditUjian<?= $u['id_ujian'] ?>"><i class="bi bi-pencil me-2"></i>Edit</button></li>
                      <li><a class="dropdown-item" href="<?= base_url('admin/soal/' . $u['id_ujian']) ?>"><i class="bi bi-list-task me-2"></i>Kelola Soal</a></li>
                      <li><hr class="dropdown-divider"></li>
                      <li><a class="dropdown-item text-danger" href="<?= base_url('admin/ujian/hapus/' . $u['id_ujian']) ?>" onclick="return confirm('Hapus ujian ini?')"><i class="bi bi-trash me-2"></i>Hapus</a></li>
                    </ul>
                  </div>
                </div>
                <div class="px-4 pb-3 flex-grow-1">
                  <h5 class="fw-bold mb-1" style="font-size:1rem"><?= esc($u['nama_ujian']) ?></h5>
                  <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    <span class="exam-meta-pill subject"><?= esc($u['nama_jenis'] ?? 'Mata Pelajaran') ?></span>
                    <span class="exam-meta-pill school"><i class="bi bi-buildings"></i><?= !empty($u['nama_sekolah']) ? esc($u['nama_sekolah']) : 'Sekolah Umum' ?></span>
                    <?php if (!empty($u['nama_kelas'])): ?>
                      <span class="exam-meta-pill classroom"><i class="bi bi-mortarboard"></i><?= esc($u['nama_kelas']) ?></span>
                    <?php else: ?>
                      <span class="exam-meta-pill access"><i class="bi bi-people"></i><?= esc($kelasUmumLabel) ?></span>
                    <?php endif; ?>
                    <?php if (!empty($u['kode_ujian'])): ?>
                      <small class="exam-code"><i class="bi bi-key me-1"></i><?= esc($u['kode_ujian']) ?></small>
                    <?php endif; ?>
                  </div>
                  <p class="text-muted small mb-3 line-clamp-2"><?= esc($u['deskripsi']) ?></p>
                  <div class="row g-2 text-center mb-3">
                    <div class="col-4"><div class="stat-box"><div class="stat-val"><?= !empty($u['durasi']) ? esc($u['durasi']) : '-' ?></div><div class="stat-lbl">Durasi</div></div></div>
                    <div class="col-4"><div class="stat-box"><div class="stat-val"><?= !empty($u['nama_kelas']) ? esc($u['nama_kelas']) : esc($kelasUmumLabel) ?></div><div class="stat-lbl">Akses</div></div></div>
                    <div class="col-4"><div class="stat-box"><div class="stat-val"><?= !empty($u['nama_sekolah']) ? esc($u['nama_sekolah']) : 'Sekolah Umum' ?></div><div class="stat-lbl">Sekolah</div></div></div>
                  </div>
                </div>
                <div class="px-4 pb-4 mt-auto">
                  <a href="<?= base_url('admin/soal/' . $u['id_ujian']) ?>" class="btn btn-outline-success w-100" style="border-radius:0">
                    <i class="bi bi-list-task me-2"></i>Kelola Soal
                  </a>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="empty-sec"><i class="bi bi-inbox me-2"></i>Belum ada ujian CBT</div>
    <?php endif; ?>

  <?php endif; ?>
  </div>
</div>

<!-- ==================== MODAL TAMBAH UJIAN ==================== -->
<div class="modal fade" id="modalTambahUjian" tabindex="-1">
  <div class="modal-dialog exam-modal-dialog modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white px-4 py-3">
        <h5 class="modal-title fw-semibold"><i class="bi bi-plus-circle me-2"></i>Tambah Ujian Baru</h5>
        <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form id="formTambahUjian" action="<?= base_url('admin/ujian/tambah') ?>" method="post" novalidate>
        <div class="modal-body px-4 py-4">
          <div id="tambahUjianGeneralError" class="alert alert-danger py-2 small d-none"></div>
          <div class="exam-modal-section">
            <h6 class="exam-modal-section-title text-uppercase text-muted fw-semibold small">
              <span class="section-dot"></span>Informasi Dasar
            </h6>
            <div class="row g-3 exam-modal-grid">
            <div class="col-md-6">
              <label class="form-label small fw-semibold">Sekolah <span class="text-danger">*</span></label>
              <select id="sekolahTambah" name="sekolah_id" class="form-select" required>
                <option value="">Pilih Sekolah</option>
                <option value="0">Sekolah Umum</option>
                <?php if (!empty($sekolah)): foreach ($sekolah as $s): ?>
                  <option value="<?= $s['sekolah_id'] ?>"><?= esc($s['nama_sekolah']) ?></option>
                <?php endforeach; endif; ?>
              </select>
              <div class="invalid-feedback" id="err_sekolah_id"></div>
              <small class="exam-modal-help d-block mt-1">Sekolah menjadi cakupan utama ujian ini.</small>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-semibold">Akses Kelas</label>
              <select id="kelasTambah" name="kelas_id" class="form-select" disabled>
                <option value="">Pilih sekolah dulu</option>
              </select>
              <div class="invalid-feedback" id="err_kelas_id"></div>
              <div class="form-check mt-2 mb-0">
                <input class="form-check-input kelas-umum-toggle" type="checkbox" id="kelasUmumTambah" data-target="kelasTambah" checked>
                <label class="form-check-label small fw-semibold" for="kelasUmumTambah">Kelas Umum</label>
              </div>
              <small class="exam-modal-help d-block mt-1">Jika aktif, ujian dapat diakses semua guru di sekolah ini.</small>
            </div>
            <div class="col-md-8">
              <label class="form-label small fw-semibold">Nama Ujian <span class="text-danger">*</span></label>
              <input type="text" id="tambahNamaUjian" name="nama_ujian" class="form-control" placeholder="Contoh: UTS Matematika Semester 1" required>
              <div class="invalid-feedback" id="err_nama_ujian"></div>
            </div>
            <div class="col-md-4">
              <label class="form-label small fw-semibold">Kode Ujian <span class="text-danger">*</span></label>
              <input type="text" id="tambahKodeUjian" name="kode_ujian" class="form-control" placeholder="MTK-UTS-001" required>
              <div class="invalid-feedback" id="err_kode_ujian"></div>
            </div>
            <div class="col-12">
              <label class="form-label small fw-semibold">Mata Pelajaran <span class="text-danger">*</span></label>
              <select id="tambahJenisUjian" name="jenis_ujian_id" class="form-select" required disabled>
                <option value="">Pilih sekolah dulu</option>
              </select>
              <div class="invalid-feedback" id="err_jenis_ujian_id"></div>
            </div>
            <div class="col-12">
              <label class="form-label small fw-semibold">Deskripsi</label>
              <textarea id="tambahDeskripsi" name="deskripsi" class="form-control" rows="2" placeholder="Deskripsi singkat (min. 10 karakter)..." required></textarea>
              <div class="invalid-feedback" id="err_deskripsi"></div>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-semibold">Durasi <span class="text-danger">*</span></label>
              <div class="duration-picker" data-duration="01:30:00">
                <input type="hidden" name="durasi" value="01:30:00" required>
                <div class="row g-2">
                  <div class="col-4">
                    <input type="number" class="form-control duration-hour" min="0" max="23" step="1" placeholder="JJ" required>
                    <div class="form-text">Jam</div>
                  </div>
                  <div class="col-4">
                    <input type="number" class="form-control duration-minute" min="0" max="59" step="1" placeholder="MM" required>
                    <div class="form-text">Menit</div>
                  </div>
                  <div class="col-4">
                    <input type="number" class="form-control duration-second" min="0" max="59" step="1" placeholder="DD" required>
                    <div class="form-text">Detik</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          </div>

          <div class="exam-modal-section">
            <h6 class="exam-modal-section-title text-uppercase text-muted fw-semibold small">
              <span class="section-dot"></span>Konfigurasi Ujian
            </h6>
            <div class="row g-3 exam-modal-grid">
            <div class="col-12">
              <label class="form-label small fw-semibold">Tipe Ujian <span class="text-danger">*</span></label>
              <div class="d-flex gap-3">
                <div class="form-check">
                  <input class="form-check-input tipe-radio" type="radio" name="tipe_ujian" value="CAT" id="tCAT" checked>
                  <label class="form-check-label" for="tCAT"><strong>CAT</strong> <small class="text-muted">(Adaptif)</small></label>
                </div>
                <div class="form-check">
                  <input class="form-check-input tipe-radio" type="radio" name="tipe_ujian" value="CBT" id="tCBT">
                  <label class="form-check-label" for="tCBT"><strong>CBT</strong> <small class="text-muted">(Fixed-Form)</small></label>
                </div>
              </div>
            </div>
          </div>
          </div>

          <div class="exam-modal-section">
            <h6 class="exam-modal-section-title text-uppercase text-muted fw-semibold small">
              <span class="section-dot"></span>Opsi Tambahan
            </h6>
            <div class="row g-3 exam-modal-grid">
            <div class="col-md-6">
              <div class="exam-option-stack">
                <label class="exam-option-card">
                  <input class="form-check-input" type="checkbox" name="tampilkan_pembahasan" value="1" id="cbPem">
                  <span class="form-check-label" for="cbPem">
                    <span class="exam-option-title">Tampilkan Pembahasan</span>
                    <span class="exam-option-desc">Peserta dapat melihat pembahasan setelah ujian selesai.</span>
                  </span>
                </label>
                <label class="exam-option-card">
                  <input class="form-check-input" type="checkbox" name="acak_urutan_soal" value="1" id="cbAcU">
                  <span class="form-check-label" for="cbAcU">
                    <span class="exam-option-title">Acak Urutan Soal</span>
                    <span class="exam-option-desc">Susunan soal diacak untuk tiap peserta.</span>
                  </span>
                </label>
                <label class="exam-option-card">
                  <input class="form-check-input" type="checkbox" name="acak_pilihan_jawaban" value="1" id="cbAcP">
                  <span class="form-check-label" for="cbAcP">
                    <span class="exam-option-title">Acak Pilihan Jawaban</span>
                    <span class="exam-option-desc">Pilihan A-E diacak untuk mengurangi pola jawaban.</span>
                  </span>
                </label>
              </div>
            </div>
            <div class="col-md-6 cbt-field" style="display:none">
              <label class="exam-option-card mb-2">
                <input class="form-check-input" type="checkbox" name="pengulangan_aktif" value="1" id="cbUlang" onchange="document.getElementById('wrapAttempt').style.display=this.checked?'block':'none'">
                <span class="form-check-label" for="cbUlang">
                  <span class="exam-option-title">Pengulangan Aktif</span>
                  <span class="exam-option-desc">Izinkan peserta mengulang ujian sesuai batas attempt.</span>
                </span>
              </label>
              <div id="wrapAttempt" style="display:none">
                <label class="form-label small fw-semibold">Maksimal Attempt</label>
                <select name="maksimal_attempt" class="form-select form-select-sm w-auto">
                  <option value="1">1 kali</option><option value="2">2 kali</option><option value="3">3 kali</option>
                </select>
              </div>
            </div>
          </div>
          </div>

          <div id="sectionIRT" class="exam-modal-section">
            <h6 class="exam-modal-section-title text-uppercase text-muted fw-semibold small">
              <span class="section-dot"></span>Parameter IRT <small class="text-muted fw-normal">(khusus CAT)</small>
            </h6>
            <div class="row g-3 exam-modal-grid">
              <div class="col-md-4">
                <label class="form-label small fw-semibold">SE Awal <span class="text-danger">*</span></label>
                <input type="number" id="tambahSeAwal" name="se_awal" class="form-control" step="0.0001" value="1.0000" required>
                <div class="invalid-feedback" id="err_se_awal"></div>
              </div>
              <div class="col-md-4">
                <label class="form-label small fw-semibold">SE Minimum <span class="text-danger">*</span></label>
                <input type="number" id="tambahSeMinimum" name="se_minimum" class="form-control" step="0.0001" value="0.2500" required>
                <div class="invalid-feedback" id="err_se_minimum"></div>
              </div>
              <div class="col-md-4">
                <label class="form-label small fw-semibold">Delta SE <span class="text-danger">*</span></label>
                <input type="number" id="tambahDeltaSe" name="delta_se_minimum" class="form-control" step="0.0001" value="0.0100" required>
                <div class="invalid-feedback" id="err_delta_se_minimum"></div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer border-0 bg-light px-4 py-3">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" id="btnTambahUjianSubmit" class="btn btn-primary px-4"><i class="bi bi-check-lg me-1"></i>Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ==================== MODAL EDIT UJIAN (per ujian) ==================== -->
<?php if (!empty($ujian)): foreach ($ujian as $u): ?>
<div class="modal fade" id="modalEditUjian<?= $u['id_ujian'] ?>" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-warning text-dark px-4 py-3">
        <h5 class="modal-title fw-semibold"><i class="bi bi-pencil me-2"></i>Edit Ujian</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="<?= base_url('admin/ujian/edit/' . $u['id_ujian']) ?>" method="post">
        <div class="modal-body px-4 py-4">
          <div class="exam-modal-section">
            <h6 class="exam-modal-section-title text-uppercase text-muted fw-semibold small">
              <span class="section-dot"></span>Informasi Dasar
            </h6>
            <div class="row g-3 exam-modal-grid">
            <div class="col-md-6">
              <label class="form-label small fw-semibold">Sekolah <span class="text-danger">*</span></label>
              <select class="form-select sekolah-edit" name="sekolah_id" data-uid="<?= $u['id_ujian'] ?>" required>
                <option value="">Pilih Sekolah</option>
                <option value="0" <?= empty($u['sekolah_id']) ? 'selected' : '' ?>>Sekolah Umum</option>
                <?php if (!empty($sekolah)): foreach ($sekolah as $s): ?>
                  <option value="<?= $s['sekolah_id'] ?>" <?= (isset($u['sekolah_id']) && $u['sekolah_id'] == $s['sekolah_id']) ? 'selected' : '' ?>><?= esc($s['nama_sekolah']) ?></option>
                <?php endforeach; endif; ?>
              </select>
              <small class="exam-modal-help d-block mt-1">Sekolah menjadi cakupan utama ujian ini.</small>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-semibold">Akses Kelas</label>
              <select class="form-select kelas-edit" id="kelasEdit<?= $u['id_ujian'] ?>" name="kelas_id" data-uid="<?= $u['id_ujian'] ?>" data-selected="<?= esc($u['kelas_id'] ?? '') ?>">
                <option value="">Kosongkan untuk umum</option>
                <?php if (!empty($kelas_guru)): foreach ($kelas_guru as $kelas): ?>
                  <option value="<?= $kelas['kelas_id'] ?>" <?= (isset($u['kelas_id']) && $u['kelas_id'] == $kelas['kelas_id']) ? 'selected' : '' ?>><?= esc($kelas['nama_kelas']) ?></option>
                <?php endforeach; endif; ?>
              </select>
              <div class="form-check mt-2 mb-0">
                <input class="form-check-input kelas-umum-toggle" type="checkbox" id="kelasUmumEdit<?= $u['id_ujian'] ?>" data-target="kelasEdit<?= $u['id_ujian'] ?>" <?= empty($u['kelas_id']) ? 'checked' : '' ?>>
                <label class="form-check-label small fw-semibold" for="kelasUmumEdit<?= $u['id_ujian'] ?>">Kelas Umum</label>
              </div>
              <small class="exam-modal-help d-block mt-1">Jika aktif, ujian dapat diakses semua guru di sekolah ini.</small>
            </div>
            <div class="col-md-8">
              <label class="form-label small fw-semibold">Nama Ujian <span class="text-danger">*</span></label>
              <input type="text" name="nama_ujian" class="form-control" value="<?= esc($u['nama_ujian']) ?>" required>
            </div>
            <div class="col-md-4">
              <label class="form-label small fw-semibold">Kode Ujian <span class="text-danger">*</span></label>
              <input type="text" name="kode_ujian" class="form-control" value="<?= esc($u['kode_ujian']) ?>" required>
            </div>
            <div class="col-12">
              <label class="form-label small fw-semibold">Mata Pelajaran <span class="text-danger">*</span></label>
              <select name="jenis_ujian_id" class="form-select" required>
                <?php if (!empty($jenis_ujian)): foreach ($jenis_ujian as $ju): ?>
                  <option value="<?= $ju['jenis_ujian_id'] ?>" <?= $ju['jenis_ujian_id'] == $u['jenis_ujian_id'] ? 'selected' : '' ?>><?= esc($ju['nama_jenis']) ?></option>
                <?php endforeach; endif; ?>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label small fw-semibold">Deskripsi</label>
              <textarea name="deskripsi" class="form-control" rows="2" required><?= esc($u['deskripsi']) ?></textarea>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-semibold">Durasi <span class="text-danger">*</span></label>
              <div class="duration-picker" data-duration="<?= esc($u['durasi']) ?>">
                <input type="hidden" name="durasi" value="<?= esc($u['durasi']) ?>" required>
                <div class="row g-2">
                  <div class="col-4">
                    <input type="number" class="form-control duration-hour" min="0" max="23" step="1" placeholder="JJ" required>
                    <div class="form-text">Jam</div>
                  </div>
                  <div class="col-4">
                    <input type="number" class="form-control duration-minute" min="0" max="59" step="1" placeholder="MM" required>
                    <div class="form-text">Menit</div>
                  </div>
                  <div class="col-4">
                    <input type="number" class="form-control duration-second" min="0" max="59" step="1" placeholder="DD" required>
                    <div class="form-text">Detik</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          </div>

          <div class="exam-modal-section">
            <h6 class="exam-modal-section-title text-uppercase text-muted fw-semibold small">
              <span class="section-dot"></span>Konfigurasi Ujian
            </h6>
            <div class="row g-3 exam-modal-grid">
            <div class="col-12">
              <label class="form-label small fw-semibold">Tipe Ujian</label>
              <div class="d-flex gap-3">
                <div class="form-check">
                  <input class="form-check-input tipe-radio-e" type="radio" name="tipe_ujian" value="CAT" id="tCATE<?= $u['id_ujian'] ?>" <?= ($u['tipe_ujian'] ?? 'CAT') == 'CAT' ? 'checked' : '' ?>>
                  <label class="form-check-label" for="tCATE<?= $u['id_ujian'] ?>"><strong>CAT</strong> <small class="text-muted">(Adaptif)</small></label>
                </div>
                <div class="form-check">
                  <input class="form-check-input tipe-radio-e" type="radio" name="tipe_ujian" value="CBT" id="tCBTE<?= $u['id_ujian'] ?>" <?= ($u['tipe_ujian'] ?? '') == 'CBT' ? 'checked' : '' ?>>
                  <label class="form-check-label" for="tCBTE<?= $u['id_ujian'] ?>"><strong>CBT</strong> <small class="text-muted">(Fixed-Form)</small></label>
                </div>
              </div>
            </div>
          </div>
          </div>

          <div class="exam-modal-section">
            <h6 class="exam-modal-section-title text-uppercase text-muted fw-semibold small">
              <span class="section-dot"></span>Opsi Tambahan
            </h6>
            <div class="row g-3 exam-modal-grid">
            <div class="col-md-6">
              <div class="exam-option-stack">
                <label class="exam-option-card">
                  <input class="form-check-input" type="checkbox" name="tampilkan_pembahasan" value="1" <?= ($u['tampilkan_pembahasan'] ?? 0) ? 'checked' : '' ?>>
                  <span class="form-check-label">
                    <span class="exam-option-title">Tampilkan Pembahasan</span>
                    <span class="exam-option-desc">Peserta dapat melihat pembahasan setelah ujian selesai.</span>
                  </span>
                </label>
                <label class="exam-option-card">
                  <input class="form-check-input" type="checkbox" name="acak_urutan_soal" value="1" <?= ($u['acak_urutan_soal'] ?? 0) ? 'checked' : '' ?>>
                  <span class="form-check-label">
                    <span class="exam-option-title">Acak Urutan Soal</span>
                    <span class="exam-option-desc">Susunan soal diacak untuk tiap peserta.</span>
                  </span>
                </label>
                <label class="exam-option-card">
                  <input class="form-check-input" type="checkbox" name="acak_pilihan_jawaban" value="1" <?= ($u['acak_pilihan_jawaban'] ?? 0) ? 'checked' : '' ?>>
                  <span class="form-check-label">
                    <span class="exam-option-title">Acak Pilihan Jawaban</span>
                    <span class="exam-option-desc">Pilihan A-E diacak untuk mengurangi pola jawaban.</span>
                  </span>
                </label>
              </div>
            </div>
            <div class="col-md-6 cbt-field-e<?= $u['id_ujian'] ?>" style="display:<?= ($u['tipe_ujian'] ?? 'CAT') == 'CBT' ? '' : 'none' ?>">
              <label class="exam-option-card mb-2">
                <input class="form-check-input" type="checkbox" name="pengulangan_aktif" value="1" id="cbUlangE<?= $u['id_ujian'] ?>" onchange="document.getElementById('wrapAttE<?= $u['id_ujian'] ?>').style.display=this.checked?'block':'none'" <?= ($u['pengulangan_aktif'] ?? 0) ? 'checked' : '' ?>>
                <span class="form-check-label">
                  <span class="exam-option-title">Pengulangan Aktif</span>
                  <span class="exam-option-desc">Izinkan peserta mengulang ujian sesuai batas attempt.</span>
                </span>
              </label>
              <div id="wrapAttE<?= $u['id_ujian'] ?>" style="display:<?= ($u['pengulangan_aktif'] ?? 0) ? 'block' : 'none' ?>">
                <label class="form-label small fw-semibold">Maksimal Attempt</label>
                <select name="maksimal_attempt" class="form-select form-select-sm w-auto">
                  <option value="1" <?= ($u['maksimal_attempt'] ?? 1) == 1 ? 'selected' : '' ?>>1 kali</option>
                  <option value="2" <?= ($u['maksimal_attempt'] ?? 1) == 2 ? 'selected' : '' ?>>2 kali</option>
                  <option value="3" <?= ($u['maksimal_attempt'] ?? 1) == 3 ? 'selected' : '' ?>>3 kali</option>
                </select>
              </div>
            </div>
          </div>
          </div>

          <div id="sectionIRTE<?= $u['id_ujian'] ?>" class="exam-modal-section" style="display:<?= ($u['tipe_ujian'] ?? 'CAT') == 'CBT' ? 'none' : '' ?>">
            <h6 class="exam-modal-section-title text-uppercase text-muted fw-semibold small">
              <span class="section-dot"></span>Parameter IRT
            </h6>
            <div class="row g-3 exam-modal-grid">
              <div class="col-md-4">
                <label class="form-label small fw-semibold">SE Awal</label>
                <input type="number" name="se_awal" class="form-control" step="0.0001" value="<?= esc($u['se_awal'] ?? '1.0000') ?>" required>
              </div>
              <div class="col-md-4">
                <label class="form-label small fw-semibold">SE Minimum</label>
                <input type="number" name="se_minimum" class="form-control" step="0.0001" value="<?= esc($u['se_minimum'] ?? '0.2500') ?>" required>
              </div>
              <div class="col-md-4">
                <label class="form-label small fw-semibold">Delta SE</label>
                <input type="number" name="delta_se_minimum" class="form-control" step="0.0001" value="<?= esc($u['delta_se_minimum'] ?? '0.0100') ?>" required>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer border-0 bg-light px-4 py-3">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-warning px-4"><i class="bi bi-check-lg me-1"></i>Update</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php endforeach; endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function(){
  function initDurationPicker(root) {
    const hiddenInput = root.querySelector('input[type="hidden"][name="durasi"]');
    const hourInput = root.querySelector('.duration-hour');
    const minuteInput = root.querySelector('.duration-minute');
    const secondInput = root.querySelector('.duration-second');
    if (!hiddenInput || !hourInput || !minuteInput || !secondInput) return;

    const normalizePart = (value, max) => {
      const numeric = Number.parseInt(value, 10);
      const safeValue = Number.isNaN(numeric) ? 0 : Math.min(Math.max(numeric, 0), max);
      return String(safeValue).padStart(2, '0');
    };

    const syncHidden = () => {
      hourInput.value = normalizePart(hourInput.value, 23);
      minuteInput.value = normalizePart(minuteInput.value, 59);
      secondInput.value = normalizePart(secondInput.value, 59);
      hiddenInput.value = [hourInput.value, minuteInput.value, secondInput.value].join(':');
    };

    const parts = String(hiddenInput.value || root.dataset.duration || '00:00:00').split(':');
    hourInput.value = normalizePart(parts[0], 23);
    minuteInput.value = normalizePart(parts[1], 59);
    secondInput.value = normalizePart(parts[2], 59);
    syncHidden();

    [hourInput, minuteInput, secondInput].forEach(input => {
      input.addEventListener('input', syncHidden);
      input.addEventListener('change', syncHidden);
    });
  }

  function initDurationPickers(scope = document) {
    scope.querySelectorAll('.duration-picker').forEach(initDurationPicker);
  }

  initDurationPickers();

  function syncKelasUmum(toggle) {
    const targetId = toggle.dataset.target;
    const kelasSelect = targetId ? document.getElementById(targetId) : null;
    if (!kelasSelect) return;
    const sekolahSelect = kelasSelect.classList.contains('kelas-edit')
      ? document.querySelector('.sekolah-edit[data-uid="' + kelasSelect.dataset.uid + '"]')
      : document.getElementById('sekolahTambah');
    const sekolahUmum = sekolahSelect && sekolahSelect.value === '0';

    if (sekolahUmum) {
      toggle.checked = true;
      toggle.disabled = true;
      kelasSelect.innerHTML = '<option value="">Kelas Umum</option>';
      kelasSelect.value = '';
      kelasSelect.disabled = true;
      return;
    }

    toggle.disabled = false;
    if (toggle.checked) {
      kelasSelect.value = '';
      kelasSelect.disabled = true;
    } else {
      kelasSelect.disabled = !(sekolahSelect && sekolahSelect.value);
    }
  }

  // Toggle CBT/CAT fields for tambah modal
  const radioTambah = document.querySelectorAll('#modalTambahUjian .tipe-radio');
  radioTambah.forEach(r => r.addEventListener('change', function(){
    const isCBT = document.getElementById('tCBT').checked;
    document.querySelectorAll('#modalTambahUjian .cbt-field').forEach(el => el.style.display = isCBT ? '' : 'none');
    document.getElementById('sectionIRT').style.display = isCBT ? 'none' : '';
  }));

  // Cascade mata pelajaran untuk form tambah
  function refreshJenisUjianTambah(sekolahId, kelasId) {
    const sel = document.getElementById('tambahJenisUjian');
    if (!sel) return;
    if (!sekolahId) {
      sel.innerHTML = '<option value="">Pilih sekolah dulu</option>';
      sel.disabled = true;
      return;
    }
    sel.innerHTML = '<option value="">Memuat...</option>';
    sel.disabled = true;
    let url = '<?= base_url('admin/api/jenis-ujian') ?>?sekolah_id=' + sekolahId;
    if (kelasId > 0) url += '&kelas_id=' + kelasId;
    fetch(url)
      .then(r => r.json())
      .then(d => {
        let opts = '<option value="">Pilih Mata Pelajaran</option>';
        if (d.status === 'success' && Array.isArray(d.data) && d.data.length > 0) {
          d.data.forEach(j => {
            const label = j.nama_jenis + (j.nama_kelas ? ' - ' + j.nama_kelas : '');
            opts += '<option value="' + j.jenis_ujian_id + '">' + label + '</option>';
          });
          sel.disabled = false;
        } else {
          opts = '<option value="">Tidak ada mata pelajaran</option>';
        }
        sel.innerHTML = opts;
      })
      .catch(() => {
        sel.innerHTML = '<option value="">Gagal memuat</option>';
      });
  }

  // Sekolah -> Kelas cascade (tambah)
  const selSekolahTambah = document.getElementById('sekolahTambah');
  const selKelasTambah = document.getElementById('kelasTambah');
  if (selSekolahTambah && selKelasTambah) {
    selSekolahTambah.addEventListener('change', function(){
      const sekolahId = this.value;
      const umumToggle = document.getElementById('kelasUmumTambah');
      selKelasTambah.innerHTML = '<option value="">Loading...</option>';
      selKelasTambah.disabled = true;
      // Refresh mata pelajaran saat sekolah berubah
      refreshJenisUjianTambah(sekolahId, 0);
      if (sekolahId === '0') {
        selKelasTambah.innerHTML = '<option value="">Kelas Umum</option>';
        if (umumToggle) {
          umumToggle.checked = true;
          syncKelasUmum(umumToggle);
        }
        return;
      }
      if (umumToggle) {
        syncKelasUmum(umumToggle);
      }
      if (!sekolahId) {
        selKelasTambah.innerHTML = '<option value="">Pilih Sekolah dulu</option>';
        return;
      }
      fetch('<?= base_url('admin/api/kelas-by-sekolah/') ?>' + sekolahId)
        .then(r => r.json())
        .then(d => {
          let opts = '<option value="">Kosongkan untuk umum</option>';
          if (d.status === 'success' && Array.isArray(d.data)) {
            d.data.forEach(k => { opts += '<option value="'+k.kelas_id+'">'+k.nama_kelas+' ('+k.tahun_ajaran+')</option>'; });
          }
          selKelasTambah.innerHTML = opts;
          syncKelasUmum(document.getElementById('kelasUmumTambah'));
        })
        .catch(() => {
          selKelasTambah.innerHTML = '<option value="">Gagal memuat</option>';
          syncKelasUmum(document.getElementById('kelasUmumTambah'));
        });
    });

    // Ketika kelas berubah, refresh mata pelajaran
    selKelasTambah.addEventListener('change', function(){
      const sekolahId = selSekolahTambah.value;
      const kelasId = this.value;
      refreshJenisUjianTambah(sekolahId, kelasId || 0);
    });
  }

  // Sekolah -> Kelas cascade (edit)
  document.querySelectorAll('.sekolah-edit').forEach(sel => {
    sel.addEventListener('change', function(){
      const uid = this.dataset.uid;
      const kelasSel = document.querySelector('.kelas-edit[data-uid="'+uid+'"]');
      const umumToggle = document.getElementById('kelasUmumEdit' + uid);
      if (!kelasSel) return;
      const sekolahId = this.value;
      kelasSel.innerHTML = '<option value="">Loading...</option>';
      kelasSel.disabled = true;
      if (sekolahId === '0') {
        kelasSel.innerHTML = '<option value="">Kelas Umum</option>';
        kelasSel.dataset.selected = '';
        if (umumToggle) {
          umumToggle.checked = true;
          syncKelasUmum(umumToggle);
        }
        return;
      }
      if (umumToggle) {
        syncKelasUmum(umumToggle);
      }
      if (!sekolahId) {
        kelasSel.innerHTML = '<option value="">Pilih Sekolah dulu</option>';
        return;
      }
      fetch('<?= base_url('admin/api/kelas-by-sekolah/') ?>' + sekolahId)
        .then(r => r.json())
        .then(d => {
          let opts = '<option value="">Kosongkan untuk umum</option>';
          const selectedValue = kelasSel.dataset.selected || '';
          if (d.status === 'success' && Array.isArray(d.data)) {
            d.data.forEach(k => {
              const selected = String(k.kelas_id) === String(selectedValue) ? ' selected' : '';
              opts += '<option value="'+k.kelas_id+'"'+selected+'>'+k.nama_kelas+' ('+k.tahun_ajaran+')</option>';
            });
          }
          kelasSel.innerHTML = opts;
          syncKelasUmum(document.getElementById('kelasUmumEdit' + uid));
        })
        .catch(() => {
          kelasSel.innerHTML = '<option value="">Gagal memuat</option>';
          syncKelasUmum(document.getElementById('kelasUmumEdit' + uid));
        });
    });
  });

  document.querySelectorAll('.kelas-umum-toggle').forEach(toggle => {
    toggle.addEventListener('change', function() {
      syncKelasUmum(this);
      // Jika ini toggle tambah, refresh mata pelajaran
      if (this.id === 'kelasUmumTambah') {
        const sekolahId = selSekolahTambah ? selSekolahTambah.value : '';
        const kelasId = (this.checked || !selKelasTambah || !selKelasTambah.value) ? 0 : selKelasTambah.value;
        refreshJenisUjianTambah(sekolahId, kelasId);
      }
    });
    syncKelasUmum(toggle);
  });

  const modalTambahUjian = document.getElementById('modalTambahUjian');
  if (modalTambahUjian) {
    modalTambahUjian.addEventListener('hidden.bs.modal', function() {
      const form = modalTambahUjian.querySelector('form');
      if (form) {
        form.reset();
      }
      initDurationPickers(modalTambahUjian);

      if (selSekolahTambah) {
        selSekolahTambah.value = '';
      }
      if (selKelasTambah) {
        selKelasTambah.innerHTML = '<option value="">Pilih Sekolah dulu</option>';
        selKelasTambah.disabled = true;
      }

      const umumToggle = document.getElementById('kelasUmumTambah');
      if (umumToggle) {
        umumToggle.checked = true;
        umumToggle.disabled = false;
        syncKelasUmum(umumToggle);
      }

      // Reset mata pelajaran ke state awal
      const selJenisReset = document.getElementById('tambahJenisUjian');
      if (selJenisReset) {
        selJenisReset.innerHTML = '<option value="">Pilih sekolah dulu</option>';
        selJenisReset.disabled = true;
      }

      const isCBT = document.getElementById('tCBT')?.checked;
      document.querySelectorAll('#modalTambahUjian .cbt-field').forEach(el => el.style.display = isCBT ? '' : 'none');
      document.getElementById('sectionIRT').style.display = isCBT ? 'none' : '';
      // Bersihkan semua error inline saat modal ditutup
      clearTambahUjianErrors();
    });
  }

  // Peta nama field ke id elemen di form tambah
  const _tambahFieldMap = {
    sekolah_id: 'sekolahTambah',
    kelas_id: 'kelasTambah',
    nama_ujian: 'tambahNamaUjian',
    kode_ujian: 'tambahKodeUjian',
    jenis_ujian_id: 'tambahJenisUjian',
    deskripsi: 'tambahDeskripsi',
    se_awal: 'tambahSeAwal',
    se_minimum: 'tambahSeMinimum',
    delta_se_minimum: 'tambahDeltaSe',
  };

  function clearTambahUjianErrors() {
    document.querySelectorAll('#formTambahUjian .is-invalid').forEach(el => el.classList.remove('is-invalid'));
    document.querySelectorAll('#formTambahUjian .invalid-feedback').forEach(el => el.textContent = '');
    const gen = document.getElementById('tambahUjianGeneralError');
    if (gen) { gen.textContent = ''; gen.classList.add('d-none'); }
  }

  function showTambahUjianErrors(errors) {
    const gen = document.getElementById('tambahUjianGeneralError');
    Object.entries(errors).forEach(([field, msg]) => {
      if (field === 'general') {
        if (gen) { gen.textContent = msg; gen.classList.remove('d-none'); }
        return;
      }
      const inputId = _tambahFieldMap[field];
      if (inputId) {
        const input = document.getElementById(inputId);
        if (input) input.classList.add('is-invalid');
      }
      const errEl = document.getElementById('err_' + field);
      if (errEl) errEl.textContent = msg;
    });
    // Scroll ke error pertama
    const first = document.querySelector('#formTambahUjian .is-invalid');
    if (first) first.scrollIntoView({ behavior: 'smooth', block: 'center' });
  }

  const formTambahUjian = document.getElementById('formTambahUjian');
  if (formTambahUjian) {
    formTambahUjian.addEventListener('submit', function(e) {
      e.preventDefault();
      clearTambahUjianErrors();
      const btn = document.getElementById('btnTambahUjianSubmit');
      const origHtml = btn.innerHTML;
      btn.disabled = true;
      btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...';
      fetch(this.action, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: new FormData(this)
      })
      .then(r => r.json())
      .then(data => {
        if (data.success) {
          window.location.href = data.redirect || window.location.href;
        } else {
          showTambahUjianErrors(data.errors || { general: 'Terjadi kesalahan validasi.' });
          btn.disabled = false;
          btn.innerHTML = origHtml;
        }
      })
      .catch(() => {
        showTambahUjianErrors({ general: 'Gagal terhubung ke server. Silakan coba lagi.' });
        btn.disabled = false;
        btn.innerHTML = origHtml;
      });
    });
  }

  document.querySelectorAll('.sekolah-edit').forEach(sel => {
    if (sel.value) {
      sel.dispatchEvent(new Event('change'));
    }
  });

  document.querySelectorAll('[id^="modalEditUjian"]').forEach(modal => {
    modal.addEventListener('hidden.bs.modal', function() {
      const uid = this.id.replace('modalEditUjian', '');
      const sekolahSel = this.querySelector('.sekolah-edit[data-uid="' + uid + '"]');
      const kelasSel = document.getElementById('kelasEdit' + uid);
      const umumToggle = document.getElementById('kelasUmumEdit' + uid);

      if (kelasSel) {
        kelasSel.dataset.selected = kelasSel.getAttribute('data-selected') || '';
      }

      if (sekolahSel && sekolahSel.value) {
        sekolahSel.dispatchEvent(new Event('change'));
      } else if (kelasSel) {
        kelasSel.innerHTML = '<option value="">Pilih Sekolah dulu</option>';
        kelasSel.disabled = true;
      }

      if (umumToggle) {
        syncKelasUmum(umumToggle);
      }
      initDurationPickers(this);

      const isCBT = document.getElementById('tCBTE' + uid)?.checked;
      document.querySelectorAll('.cbt-field-e' + uid).forEach(el => el.style.display = isCBT ? '' : 'none');
      const irtSec = document.getElementById('sectionIRTE' + uid);
      if (irtSec) {
        irtSec.style.display = isCBT ? 'none' : '';
      }
    });
  });

  // Toggle CBT/CAT for edit modals
  document.querySelectorAll('.tipe-radio-e').forEach(r => r.addEventListener('change', function(){
    const uid = this.closest('.modal').id.replace('modalEditUjian','');
    const isCBT = document.getElementById('tCBTE'+uid)?.checked;
    document.querySelectorAll('.cbt-field-e'+uid).forEach(el => el.style.display = isCBT ? '' : 'none');
    const irtSec = document.getElementById('sectionIRTE'+uid);
    if (irtSec) irtSec.style.display = isCBT ? 'none' : '';
  }));
});
</script>

<?= $this->endSection() ?>
