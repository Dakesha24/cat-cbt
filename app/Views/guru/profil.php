<?= $this->extend('templates/guru/guru_template') ?>
<?= $this->section('content') ?>

<style>
.profil-wrap { background: #f4f6f9; min-height: calc(100vh - 60px); padding: 28px 0 48px; }
.profil-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; margin-bottom: 20px; }
.profil-card-head { padding: 14px 24px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; gap: 10px; }
.profil-card-head h6 { margin: 0; font-size: .8125rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #64748b; }
.profil-card-body { padding: 24px; }
.info-row { display: grid; grid-template-columns: 160px 1fr; gap: 12px; align-items: center; padding: 10px 0; border-bottom: 1px solid #f1f5f9; font-size: .875rem; }
.info-row:last-child { border-bottom: none; }
.info-label { color: #64748b; font-weight: 500; font-size: .82rem; }
.info-value { color: #111827; font-weight: 500; }
.kelas-pill { display: inline-flex; align-items: center; gap: 6px; background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; border-radius: 6px; padding: 5px 10px; font-size: .8rem; font-weight: 600; }
.kelas-pill .tahun { font-size: .72rem; color: #6b7280; font-weight: 400; }
.kelas-pill .siswa-count { background: #dbeafe; color: #1d4ed8; border-radius: 4px; padding: 1px 6px; font-size: .72rem; font-weight: 700; }
.form-label { font-size: .875rem; font-weight: 600; color: #374151; margin-bottom: 5px; }
.form-control, .form-select { font-size: .9rem; border-color: #e2e8f0; border-radius: 7px; padding: .5rem .75rem; }
.form-control:focus { border-color: #93c5fd; box-shadow: 0 0 0 3px rgba(59,130,246,.1); }
.form-control:disabled { background: #f9fafb; color: #9ca3af; }
.empty-kelas { color: #9ca3af; font-size: .85rem; font-style: italic; }
</style>

<div class="profil-wrap">
<div class="container-fluid px-3 px-md-4" style="max-width:760px">

  <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4">
      <i class="bi bi-check-circle-fill me-2"></i><?= session()->getFlashdata('success') ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>
  <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4">
      <i class="bi bi-exclamation-triangle-fill me-2"></i><?= session()->getFlashdata('error') ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <!-- Info Singkat (read-only) -->
  <div class="profil-card">
    <div class="profil-card-head">
      <i class="bi bi-person-badge text-primary"></i>
      <h6>Informasi Akun</h6>
    </div>
    <div class="profil-card-body">
      <div class="info-row">
        <span class="info-label">Username</span>
        <span class="info-value"><code><?= esc($guru['username']) ?></code></span>
      </div>
      <div class="info-row">
        <span class="info-label">Sekolah</span>
        <span class="info-value"><?= esc($guru['nama_sekolah']) ?></span>
      </div>
      <div class="info-row">
        <span class="info-label">NIP</span>
        <span class="info-value"><?= esc($guru['nip'] ?: '—') ?></span>
      </div>
    </div>
  </div>

  <!-- Kelas yang Diajar -->
  <div class="profil-card">
    <div class="profil-card-head">
      <i class="bi bi-grid-3x3-gap text-success"></i>
      <h6>Kelas yang Diajar</h6>
      <span class="ms-auto" style="font-size:.78rem;color:#9ca3af"><?= count($kelasDiajar) ?> kelas</span>
    </div>
    <div class="profil-card-body">
      <?php if (empty($kelasDiajar)): ?>
        <p class="empty-kelas mb-0">Belum ada kelas yang ditugaskan. Hubungi admin untuk mengatur penugasan kelas.</p>
      <?php else: ?>
        <div class="d-flex flex-wrap gap-2">
          <?php foreach ($kelasDiajar as $k): ?>
            <div class="kelas-pill">
              <i class="bi bi-mortarboard" style="font-size:.85rem"></i>
              <span><?= esc($k['nama_kelas']) ?></span>
              <span class="tahun"><?= esc($k['tahun_ajaran']) ?></span>
              <span class="siswa-count"><?= (int)$k['jumlah_siswa'] ?> siswa</span>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Form Edit Profil -->
  <div class="profil-card">
    <div class="profil-card-head">
      <i class="bi bi-pencil-square text-warning"></i>
      <h6>Edit Profil</h6>
    </div>
    <div class="profil-card-body">
      <form action="<?= base_url('guru/profil/save') ?>" method="POST">

        <div class="mb-3">
          <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
          <input type="text" name="nama_lengkap" class="form-control <?= session('errors.nama_lengkap') ? 'is-invalid' : '' ?>"
            value="<?= old('nama_lengkap', esc($guru['nama_lengkap'])) ?>">
          <?php if (session('errors.nama_lengkap')): ?>
            <div class="invalid-feedback"><?= session('errors.nama_lengkap') ?></div>
          <?php endif; ?>
        </div>

        <div class="mb-3">
          <label class="form-label">NIP <span class="text-danger">*</span></label>
          <input type="text" name="nip" class="form-control <?= session('errors.nip') ? 'is-invalid' : '' ?>"
            value="<?= old('nip', esc($guru['nip'])) ?>">
          <?php if (session('errors.nip')): ?>
            <div class="invalid-feedback"><?= session('errors.nip') ?></div>
          <?php endif; ?>
        </div>

        <div class="mb-3">
          <label class="form-label">Mata Pelajaran <span class="text-danger">*</span></label>
          <input type="text" name="mata_pelajaran" class="form-control <?= session('errors.mata_pelajaran') ? 'is-invalid' : '' ?>"
            value="<?= old('mata_pelajaran', esc($guru['mata_pelajaran'])) ?>"
            placeholder="Contoh: Matematika, Fisika">
          <?php if (session('errors.mata_pelajaran')): ?>
            <div class="invalid-feedback"><?= session('errors.mata_pelajaran') ?></div>
          <?php endif; ?>
        </div>

        <div class="mb-4">
          <label class="form-label">Email <span class="text-danger">*</span></label>
          <input type="email" name="email" class="form-control <?= session('errors.email') ? 'is-invalid' : '' ?>"
            value="<?= old('email', esc($guru['email'])) ?>">
          <?php if (session('errors.email')): ?>
            <div class="invalid-feedback"><?= session('errors.email') ?></div>
          <?php endif; ?>
        </div>

        <div class="d-flex justify-content-end">
          <button type="submit" class="btn btn-primary px-4">
            <i class="bi bi-save me-2"></i>Simpan Perubahan
          </button>
        </div>

      </form>
    </div>
  </div>

</div>
</div>

<?= $this->endSection() ?>
