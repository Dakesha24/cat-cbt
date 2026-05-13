<?= $this->extend('templates/admin/admin_template') ?>

<?= $this->section('content') ?>
<br><br>
<div class="container">
  <div class="row mb-4 py-4">
    <div class="col">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Tambah Siswa</h2>
        <a href="<?= base_url('admin/siswa') ?>" class="btn btn-secondary">
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

      <div class="card">
        <div class="card-body">
          <form action="<?= base_url('admin/siswa/tambah') ?>" method="post">
            <?= csrf_field() ?>

            <div class="row">
              <!-- Data Login -->
              <div class="col-md-6">
                <h5 class="mb-3">Data Login</h5>

                <div class="mb-3">
                  <label for="username" class="form-label">Username *</label>
                  <input type="text" class="form-control" id="username" name="username"
                    value="<?= old('username') ?>" required>
                </div>

                <div class="mb-3">
                  <label for="email" class="form-label">Email *</label>
                  <input type="email" class="form-control" id="email" name="email"
                    value="<?= old('email') ?>" required>
                </div>

                <div class="mb-3">
                  <label for="password" class="form-label">Password *</label>
                  <input type="password" class="form-control" id="password" name="password" required>
                  <div class="form-text">Minimal 6 karakter</div>
                </div>
              </div>

              <!-- Data Siswa -->
              <div class="col-md-6">
                <h5 class="mb-3">Data Siswa</h5>

                <div class="mb-3">
                  <label for="nama_lengkap" class="form-label">Nama Lengkap *</label>
                  <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap"
                    value="<?= old('nama_lengkap') ?>" required>
                </div>

                <div class="mb-3">
                  <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                  <select class="form-select" id="jenis_kelamin" name="jenis_kelamin">
                    <option value="">Pilih Jenis Kelamin</option>
                    <option value="Laki-laki" <?= (old('jenis_kelamin') == 'Laki-laki') ? 'selected' : '' ?>>
                      Laki-laki
                    </option>
                    <option value="Perempuan" <?= (old('jenis_kelamin') == 'Perempuan') ? 'selected' : '' ?>>
                      Perempuan
                    </option>
                  </select>
                </div>

                <div class="mb-3">
                  <label for="nomor_peserta" class="form-label">Nomor Peserta *</label>
                  <input type="text" class="form-control" id="nomor_peserta" name="nomor_peserta"
                    value="<?= old('nomor_peserta') ?>" required>
                  <div class="form-text">Nomor unik untuk setiap siswa</div>
                </div>

                <div class="mb-3">
                  <label for="sekolah_id" class="form-label">Sekolah *</label>
                  <select class="form-select" id="sekolah_id" name="sekolah_id" required onchange="filterKelas()">
                    <option value="">Pilih Sekolah</option>
                    <?php foreach ($sekolah as $s): ?>
                      <option value="<?= $s['sekolah_id'] ?>"
                        <?= (old('sekolah_id') == $s['sekolah_id']) ? 'selected' : '' ?>>
                        <?= esc($s['nama_sekolah']) ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>

                <div class="mb-3">
                  <label for="kelas_id" class="form-label">Kelas *</label>
                  <select class="form-select" id="kelas_id" name="kelas_id" required disabled>
                    <option value="">Pilih Sekolah Terlebih Dahulu</option>
                  </select>
                  <div class="form-text">Pilih sekolah terlebih dahulu untuk melihat kelas yang tersedia</div>
                </div>
              </div>
            </div>

            <hr>

            <div class="d-flex justify-content-end gap-2">
              <a href="<?= base_url('admin/siswa') ?>" class="btn btn-secondary">Batal</a>
              <button type="submit" class="btn btn-success">
                <i class="bi bi-save me-2"></i>Simpan
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Generate Batch Students -->
      <div class="card mt-4">
        <div class="card-header">
          <h5 class="mb-0">
            <i class="bi bi-people-fill me-2"></i>Generate Siswa Batch
          </h5>
        </div>
        <div class="card-body">
          <p class="text-muted">Buat beberapa siswa sekaligus dengan nomor peserta otomatis</p>

          <form id="batchForm">
            <div class="row">
              <div class="col-md-2">
                <label for="batch_sekolah" class="form-label">Sekolah</label>
                <select class="form-select" id="batch_sekolah" required onchange="filterKelasBatch()">
                  <option value="">Pilih Sekolah</option>
                  <?php foreach ($sekolah as $s): ?>
                    <option value="<?= $s['sekolah_id'] ?>">
                      <?= esc($s['nama_sekolah']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-md-2">
                <label for="batch_kelas" class="form-label">Kelas</label>
                <select class="form-select" id="batch_kelas" required disabled>
                  <option value="">Pilih Sekolah Dulu</option>
                </select>
              </div>
              <div class="col-md-2">
                <label for="batch_jenis_kelamin" class="form-label">Jenis Kelamin</label>
                <select class="form-select" id="batch_jenis_kelamin">
                  <option value="">Acak</option>
                  <option value="Laki-laki">Semua Laki-laki</option>
                  <option value="Perempuan">Semua Perempuan</option>
                </select>
              </div>
              <div class="col-md-2">
                <label for="batch_jumlah" class="form-label">Jumlah Siswa</label>
                <input type="number" class="form-control" id="batch_jumlah" min="1" max="50" value="10">
              </div>
              <div class="col-md-2">
                <label for="batch_prefix" class="form-label">Prefix No. Peserta</label>
                <input type="text" class="form-control" id="batch_prefix" value="SISWA" maxlength="10">
              </div>
              <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <button type="button" class="btn btn-info w-100" onclick="generateBatch()">
                  <i class="bi bi-magic me-2"></i>Generate Preview
                </button>
              </div>
            </div>
          </form>

          <div id="batchPreview" class="mt-4" style="display: none;">
            <h6>Preview Data yang Akan Dibuat:</h6>
            <div class="table-responsive">
              <table class="table table-sm">
                <thead>
                  <tr>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Nama Lengkap</th>
                    <th>Jenis Kelamin</th>
                    <th>No. Peserta</th>
                  </tr>
                </thead>
                <tbody id="batchTable">
                </tbody>
              </table>
            </div>
            <button type="button" class="btn btn-success" onclick="createBatch()">
              <i class="bi bi-check2-all me-2"></i>Buat Semua Siswa
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  // Data kelas dari PHP
  const kelasData = <?= json_encode($kelas) ?>;

  function filterKelas() {
    const sekolahId = document.getElementById('sekolah_id').value;
    const kelasSelect = document.getElementById('kelas_id');

    // Clear existing options
    kelasSelect.innerHTML = '<option value="">Pilih Kelas</option>';

    if (sekolahId) {
      kelasSelect.disabled = false;

      // Filter kelas berdasarkan sekolah yang dipilih
      const filteredKelas = kelasData.filter(k => k.sekolah_id == sekolahId);

      filteredKelas.forEach(kelas => {
        const option = new Option(
          `${kelas.nama_kelas} - ${kelas.tahun_ajaran}`,
          kelas.kelas_id
        );
        kelasSelect.add(option);
      });

      // Restore selected value if exists (for edit form)
      const oldKelasId = '<?= old('kelas_id') ?>';
      if (oldKelasId) {
        kelasSelect.value = oldKelasId;
      }
    } else {
      kelasSelect.disabled = true;
      kelasSelect.innerHTML = '<option value="">Pilih Sekolah Terlebih Dahulu</option>';
    }
  }

  function filterKelasBatch() {
    const sekolahId = document.getElementById('batch_sekolah').value;
    const kelasSelect = document.getElementById('batch_kelas');

    // Clear existing options
    kelasSelect.innerHTML = '<option value="">Pilih Kelas</option>';

    if (sekolahId) {
      kelasSelect.disabled = false;

      // Filter kelas berdasarkan sekolah yang dipilih
      const filteredKelas = kelasData.filter(k => k.sekolah_id == sekolahId);

      filteredKelas.forEach(kelas => {
        const option = new Option(
          `${kelas.nama_kelas} - ${kelas.tahun_ajaran}`,
          kelas.kelas_id
        );
        kelasSelect.add(option);
      });
    } else {
      kelasSelect.disabled = true;
      kelasSelect.innerHTML = '<option value="">Pilih Sekolah Dulu</option>';
    }
  }

  function generateBatch() {
    const kelas = document.getElementById('batch_kelas').value;
    const jenisKelamin = document.getElementById('batch_jenis_kelamin').value;
    const jumlah = parseInt(document.getElementById('batch_jumlah').value);
    const prefix = document.getElementById('batch_prefix').value;

    if (!kelas || !jumlah || !prefix) {
      alert('Harap lengkapi semua field');
      return;
    }

    const tableBody = document.getElementById('batchTable');
    tableBody.innerHTML = '';

    for (let i = 1; i <= jumlah; i++) {
      const num = i.toString().padStart(3, '0');
      const username = `${prefix.toLowerCase()}${num}`;
      const email = `${username}@sekolah.com`;
      const nama = `${prefix} ${num}`;
      const noPeserta = `${prefix}${num}`;

      // Tentukan jenis kelamin
      let gender = '';
      if (jenisKelamin) {
        gender = jenisKelamin;
      } else {
        // Jika acak, alternate antara Laki-laki dan Perempuan
        gender = (i % 2 === 1) ? 'Laki-laki' : 'Perempuan';
      }

      const row = `
            <tr>
                <td>${username}</td>
                <td>${email}</td>
                <td>${nama}</td>
                <td>
                    <span class="badge ${gender === 'Laki-laki' ? 'bg-primary' : 'bg-danger'}">
                        ${gender === 'Laki-laki' ? 'L' : 'P'}
                    </span>
                </td>
                <td>${noPeserta}</td>
            </tr>
        `;
      tableBody.innerHTML += row;
    }

    document.getElementById('batchPreview').style.display = 'block';
  }

  function createBatch() {
    const kelas = document.getElementById('batch_kelas').value;
    const jenisKelamin = document.getElementById('batch_jenis_kelamin').value;
    const jumlah = parseInt(document.getElementById('batch_jumlah').value);
    const prefix = document.getElementById('batch_prefix').value;

    if (confirm(`Yakin ingin membuat ${jumlah} siswa sekaligus?`)) {
      // Implementasi AJAX untuk create batch
      // Untuk sementara, redirect ke halaman dengan parameter
      let url = `<?= base_url('admin/siswa/batch') ?>?kelas=${kelas}&jumlah=${jumlah}&prefix=${prefix}`;
      if (jenisKelamin) {
        url += `&jenis_kelamin=${jenisKelamin}`;
      }
      window.location.href = url;
    }
  }

  // Trigger filter when page loads if sekolah is already selected
  document.addEventListener('DOMContentLoaded', function() {
    const sekolahId = document.getElementById('sekolah_id').value;
    if (sekolahId) {
      filterKelas();
    }
  });
</script>

<?= $this->endSection() ?>