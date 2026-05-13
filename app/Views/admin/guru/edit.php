<?= $this->extend('templates/admin/admin_template') ?>

<?= $this->section('content') ?>
<br><br>
<div class="container">
  <div class="row mb-4 py-4">
    <div class="col">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Edit Guru</h2>
        <a href="<?= base_url('admin/guru') ?>" class="btn btn-secondary">
          <i class="bi bi-arrow-left me-2"></i>Kembali
        </a>
      </div>

      <!-- Flash Messages -->
      <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <?= session()->getFlashdata('error') ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>

      <?php if (session()->get('errors')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <ul class="mb-0">
            <?php foreach (session()->get('errors') as $error): ?>
              <li><?= $error ?></li>
            <?php endforeach; ?>
          </ul>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>

      <!-- Form Edit Data Guru -->
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0">Data Guru</h5>
        </div>
        <div class="card-body">
          <form action="<?= base_url('admin/guru/edit/' . $guru['user_id']) ?>" method="post">
            <?= csrf_field() ?>

            <div class="row">
              <!-- Data Login -->
              <div class="col-md-6">
                <h5 class="mb-3">Data Login</h5>

                <div class="mb-3">
                  <label for="username" class="form-label">Username *</label>
                  <input type="text" class="form-control" id="username" name="username"
                    value="<?= old('username', $guru['username']) ?>" required>
                </div>

                <div class="mb-3">
                  <label for="email" class="form-label">Email *</label>
                  <input type="email" class="form-control" id="email" name="email"
                    value="<?= old('email', $guru['email']) ?>" required>
                </div>

                <div class="mb-3">
                  <label for="password" class="form-label">Password</label>
                  <input type="password" class="form-control" id="password" name="password"
                    placeholder="****  (Kosongkan jika tidak ingin mengubah)">
                  <div class="form-text">Kosongkan jika tidak ingin mengubah password</div>
                </div>

                <div class="mb-3">
                  <label class="form-label">Status</label>
                  <div>
                    <?php if ($guru['status'] == 'active'): ?>
                      <span class="badge bg-success fs-6">Aktif</span>
                    <?php else: ?>
                      <span class="badge bg-danger fs-6">Nonaktif</span>
                    <?php endif; ?>
                  </div>
                </div>
              </div>

              <!-- Data Guru -->
              <div class="col-md-6">
                <h5 class="mb-3">Data Guru</h5>

                <div class="mb-3">
                  <label for="nama_lengkap" class="form-label">Nama Lengkap *</label>
                  <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap"
                    value="<?= old('nama_lengkap', $guru['nama_lengkap']) ?>" required>
                </div>

                <div class="mb-3">
                  <label for="nip" class="form-label">NIP</label>
                  <input type="text" class="form-control" id="nip" name="nip"
                    value="<?= old('nip', $guru['nip']) ?>">
                  <div class="form-text">Opsional</div>
                </div>

                <div class="mb-3">
                  <label for="mata_pelajaran" class="form-label">Mata Pelajaran *</label>
                  <input type="text" class="form-control" id="mata_pelajaran" name="mata_pelajaran"
                    value="<?= old('mata_pelajaran', $guru['mata_pelajaran']) ?>" required>
                </div>

                <div class="mb-3">
                  <label for="sekolah_id" class="form-label">Sekolah *</label>
                  <select class="form-select" id="sekolah_id" name="sekolah_id" required onchange="updateKelasOptions()">
                    <option value="">Pilih Sekolah</option>
                    <?php foreach ($sekolah as $s): ?>
                      <option value="<?= $s['sekolah_id'] ?>"
                        <?= (old('sekolah_id', $guru['sekolah_id'] ?? '') == $s['sekolah_id']) ? 'selected' : '' ?>>
                        <?= esc($s['nama_sekolah']) ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>

                <div class="mb-3">
                  <label class="form-label">Informasi Tambahan</label>
                  <div class="bg-light p-3 rounded">
                    <small class="text-muted">
                      <strong>Terdaftar:</strong> <?= date('d F Y H:i', strtotime($guru['created_at'])) ?><br>
                      <strong>Sekolah Saat Ini:</strong> <?= esc($guru['nama_sekolah'] ?? 'Belum ditentukan') ?>
                    </small>
                  </div>
                </div>
              </div>
            </div>

            <hr>

            <div class="d-flex justify-content-end gap-2">
              <a href="<?= base_url('admin/guru') ?>" class="btn btn-secondary">Batal</a>
              <button type="submit" class="btn btn-primary">
                <i class="bi bi-save me-2"></i>Update Data
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Kelola Kelas yang Diajar -->
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">Kelola Kelas yang Diajar</h5>
        </div>
        <div class="card-body">
          
          <!-- Kelas yang Sudah Diajar -->
          <?php if (!empty($kelasGuru)): ?>
            <h6>Kelas yang Sedang Diajar:</h6>
            <div class="row mb-3">
              <?php foreach ($kelasGuru as $kg): ?>
                <div class="col-md-4 mb-2">
                  <div class="border rounded p-2 d-flex justify-content-between align-items-center">
                    <div>
                      <strong><?= esc($kg['nama_kelas']) ?></strong><br>
                      <small class="text-muted"><?= esc($kg['tahun_ajaran']) ?></small>
                    </div>
                    <a href="<?= base_url('admin/guru/remove-kelas/' . $guru['guru_id'] . '/' . $kg['kelas_id']) ?>" 
                       class="btn btn-sm btn-outline-danger"
                       onclick="return confirm('Yakin ingin mengeluarkan guru dari kelas ini?')"
                       title="Hapus dari kelas">
                      <i class="bi bi-x"></i>
                    </a>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
            <hr>
          <?php else: ?>
            <div class="alert alert-info">
              <i class="bi bi-info-circle me-2"></i>Guru ini belum mengajar di kelas manapun.
            </div>
          <?php endif; ?>

          <!-- Form Tambah Kelas -->
          <h6>Tambah Kelas:</h6>
          <form action="<?= base_url('admin/guru/assign-kelas') ?>" method="post" id="formAssignKelas">
            <?= csrf_field() ?>
            <input type="hidden" name="guru_id" value="<?= $guru['guru_id'] ?>">
            
            <div class="row">
              <div class="col-md-8">
                <select class="form-select" name="kelas_id" id="kelas_available" required>
                  <option value="">Pilih sekolah terlebih dahulu untuk melihat kelas yang tersedia</option>
                </select>
                <div class="form-text">Hanya menampilkan kelas yang belum diajar oleh guru ini</div>
              </div>
              <div class="col-md-4">
                <button type="submit" class="btn btn-success">
                  <i class="bi bi-plus-circle me-2"></i>Tambah Kelas
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
// Data kelas dari PHP
const kelasData = <?= json_encode($allKelas ?? []) ?>;
const kelasGuru = <?= json_encode(array_column($kelasGuru ?? [], 'kelas_id')) ?>;

function updateKelasOptions() {
    const sekolahId = document.getElementById('sekolah_id').value;
    const kelasSelect = document.getElementById('kelas_available');
    
    // Clear existing options
    kelasSelect.innerHTML = '<option value="">Pilih Kelas</option>';
    
    if (sekolahId) {
        // Filter kelas berdasarkan sekolah yang dipilih dan yang belum diajar
        const filteredKelas = kelasData.filter(k => 
            k.sekolah_id == sekolahId && !kelasGuru.includes(k.kelas_id.toString())
        );
        
        if (filteredKelas.length > 0) {
            filteredKelas.forEach(kelas => {
                const option = new Option(
                    `${kelas.nama_kelas} - ${kelas.tahun_ajaran}`,
                    kelas.kelas_id
                );
                kelasSelect.add(option);
            });
        } else {
            kelasSelect.innerHTML = '<option value="">Tidak ada kelas yang tersedia</option>';
        }
    } else {
        kelasSelect.innerHTML = '<option value="">Pilih sekolah terlebih dahulu</option>';
    }
}

// Initialize form when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Trigger update kelas options if sekolah is already selected
    const sekolahId = document.getElementById('sekolah_id').value;
    if (sekolahId) {
        updateKelasOptions();
    }
});

// Disable form submit if no kelas available
document.getElementById('formAssignKelas').addEventListener('submit', function(e) {
    const kelasSelect = document.getElementById('kelas_available');
    if (!kelasSelect.value) {
        e.preventDefault();
        alert('Pilih kelas yang akan ditambahkan');
    }
});
</script>

<?= $this->endSection() ?>