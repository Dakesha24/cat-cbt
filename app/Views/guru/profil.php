<?= $this->extend('templates/guru/guru_template') ?>

<?= $this->section('content') ?>
<div class="container py-4">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
          <h5 class="card-title mb-0">Profil Guru</h5>
        </div>
        <div class="card-body">
          <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              <?= session()->getFlashdata('success') ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          <?php endif; ?>

          <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <?= session()->getFlashdata('error') ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          <?php endif; ?>

          <form action="<?= base_url('guru/profil/save') ?>" method="POST">
            <div class="row mb-3">
              <label class="col-sm-3 col-form-label">Username</label>
              <div class="col-sm-9">
                <input type="text" class="form-control" value="<?= esc($guru['username']) ?>" disabled>
                <small class="text-muted">Username tidak dapat diubah</small>
              </div>
            </div>

            <div class="row mb-3">
              <label class="col-sm-3 col-form-label">Sekolah</label>
              <div class="col-sm-9">
                <input type="text" class="form-control" value="<?= esc($guru['nama_sekolah']) ?>" disabled>
              </div>
            </div>

            <div class="row mb-3">
              <label class="col-sm-3 col-form-label">NIP</label>
              <div class="col-sm-9">
                <input type="text" name="nip" class="form-control <?= (session('errors.nip')) ? 'is-invalid' : '' ?>"
                  value="<?= old('nip', $guru['nip']) ?>">
                <?php if (session('errors.nip')): ?>
                  <div class="invalid-feedback"><?= session('errors.nip') ?></div>
                <?php endif; ?>
              </div>
            </div>

            <div class="row mb-3">
              <label class="col-sm-3 col-form-label">Nama Lengkap</label>
              <div class="col-sm-9">
                <input type="text" name="nama_lengkap" class="form-control <?= (session('errors.nama_lengkap')) ? 'is-invalid' : '' ?>"
                  value="<?= old('nama_lengkap', $guru['nama_lengkap']) ?>">
                <?php if (session('errors.nama_lengkap')): ?>
                  <div class="invalid-feedback"><?= session('errors.nama_lengkap') ?></div>
                <?php endif; ?>
              </div>
            </div>

            <div class="row mb-3">
              <label class="col-sm-3 col-form-label">Mata Pelajaran</label>
              <div class="col-sm-9">
                <input type="text" name="mata_pelajaran" class="form-control <?= (session('errors.mata_pelajaran')) ? 'is-invalid' : '' ?>"
                  value="<?= old('mata_pelajaran', $guru['mata_pelajaran']) ?>">
                <?php if (session('errors.mata_pelajaran')): ?>
                  <div class="invalid-feedback"><?= session('errors.mata_pelajaran') ?></div>
                <?php endif; ?>
              </div>
            </div>

            <div class="row mb-3">
              <label class="col-sm-3 col-form-label">Email</label>
              <div class="col-sm-9">
                <input type="email" name="email" class="form-control <?= (session('errors.email')) ? 'is-invalid' : '' ?>"
                  value="<?= old('email', $guru['email']) ?>">
                <?php if (session('errors.email')): ?>
                  <div class="invalid-feedback"><?= session('errors.email') ?></div>
                <?php endif; ?>
              </div>
            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
              <button type="submit" class="btn btn-primary px-4">
                <i class="bi bi-save me-2"></i>Simpan Perubahan
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>