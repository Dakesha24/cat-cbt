<?= $this->extend('templates/admin/admin_template') ?>

<?= $this->section('title') ?>Tambah Kelas - <?= esc($sekolah['nama_sekolah']) ?><?= $this->endSection() ?>

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
          <li class="breadcrumb-item active" aria-current="page">Tambah Kelas</li>
        </ol>
      </nav>

      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <div>
            <h4 class="card-title mb-0">Tambah Kelas Baru</h4>
            <small class="text-muted">di <?= esc($sekolah['nama_sekolah']) ?></small>
          </div>
          <a href="<?= base_url('admin/sekolah/' . $sekolah['sekolah_id'] . '/kelas') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
          </a>
        </div>
        <div class="card-body">
          <!-- Informasi Sekolah -->
          <div class="alert alert-info mb-4">
            <div class="row">
              <div class="col-md-8">
                <h6 class="alert-heading mb-2">
                  <i class="bi bi-building me-2"></i>Informasi Sekolah
                </h6>
                <p class="mb-1"><strong>Nama:</strong> <?= esc($sekolah['nama_sekolah']) ?></p>
                <p class="mb-1"><strong>Alamat:</strong> <?= esc($sekolah['alamat'] ?: '-') ?></p>
                <p class="mb-0"><strong>Telepon:</strong> <?= esc($sekolah['telepon'] ?: '-') ?></p>
              </div>
              <div class="col-md-4 text-end">
                <small class="text-muted">Kelas akan ditambahkan ke sekolah ini</small>
              </div>
            </div>
          </div>

          <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <?= session()->getFlashdata('error') ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          <?php endif; ?>

          <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <ul class="mb-0">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                  <li><?= $error ?></li>
                <?php endforeach; ?>
              </ul>
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          <?php endif; ?>

          <form action="<?= base_url('admin/sekolah/' . $sekolah['sekolah_id'] . '/kelas/tambah') ?>" method="POST">
            <?= csrf_field() ?>

            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="nama_kelas" class="form-label">Nama Kelas <span class="text-danger">*</span></label>
                  <input type="text"
                    class="form-control form-control-lg"
                    id="nama_kelas"
                    name="nama_kelas"
                    value="<?= old('nama_kelas') ?>"
                    placeholder="Contoh: XII IPA 1, XI IPS 2, X Multimedia"
                    required>
                  <div class="form-text">Format umum: [Tingkat] [Jurusan] [Nomor] atau sesuai kebijakan sekolah</div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="mb-3">
                  <label for="tahun_ajaran" class="form-label">Tahun Ajaran <span class="text-danger">*</span></label>
                  <input type="text"
                    class="form-control form-control-lg"
                    id="tahun_ajaran"
                    name="tahun_ajaran"
                    value="<?= old('tahun_ajaran', date('Y') . '/' . (date('Y') + 1)) ?>"
                    placeholder="Contoh: 2024/2025"
                    pattern="^\d{4}\/\d{4}$"
                    required>
                  <div class="form-text">Format: YYYY/YYYY (contoh: 2024/2025)</div>
                </div>
              </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
              <a href="<?= base_url('admin/sekolah/' . $sekolah['sekolah_id'] . '/kelas') ?>" class="btn btn-secondary">
                <i class="fas fa-times"></i> Batal
              </a>
              <button type="reset" class="btn btn-outline-warning">
                <i class="fas fa-undo"></i> Reset
              </button>
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan Kelas
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  // Auto-format tahun ajaran input
  document.getElementById('tahun_ajaran').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, ''); // Remove non-digits
    if (value.length >= 4) {
      value = value.slice(0, 4) + '/' + value.slice(4, 8);
    }
    e.target.value = value;
  });

  // Function untuk set nama kelas dari template
  function setNamaKelas(nama) {
    document.getElementById('nama_kelas').value = nama;
    document.getElementById('nama_kelas').focus();
  }
</script>

<?= $this->endSection() ?>