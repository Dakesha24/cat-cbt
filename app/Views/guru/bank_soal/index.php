<?= $this->extend('templates/guru/guru_template') ?>

<?= $this->section('content') ?>
<br><br>
<div class="container-fluid py-4">
  <div class="row mb-4 align-items-center">
    <div class="col">
      <h2 class="fw-bold text-primary">Bank Soal</h2>
      <p class="text-muted">Kelola koleksi soal yang dapat digunakan untuk berbagai ujian</p>
    </div>
    <div class="col-auto">
      <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahBankSoal">
        <i class="fas fa-plus me-2"></i>Tambah Bank Soal Baru
      </button>
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

  <!-- Info Box -->
  <div class="alert alert-info alert-dismissible fade show" role="alert">
    <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Cara Menggunakan Bank Soal</h6>
    <p class="mb-2">Bank soal diorganisir dalam struktur hierarki untuk memudahkan pengelolaan:</p>
    <ol class="mb-2">
      <li><strong>Kategori:</strong> Pilih "Umum" (dapat diakses semua guru) atau "Kelas" tertentu</li>
      <li><strong>Mata Pelajaran:</strong> Tentukan Mata Pelajaran (UTS, UAS, Quiz, dll)</li>
      <li><strong>Nama Ujian:</strong> Beri nama spesifik untuk bank ujian</li>
      <li><strong>Kelola Soal:</strong> Tambah, edit, dan hapus soal dalam bank ujian</li>
    </ol>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>

  <!-- Kategori Bank Soal -->
  <div class="row g-4">
    <!-- Kategori Umum -->
    <div class="col-lg-4 col-md-6">
      <div class="card h-100 shadow-sm hover-shadow">
        <div class="card-body text-center p-4">
          <div class="mb-3">
            <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
              <i class="fas fa-globe fa-2x text-primary"></i>
            </div>
          </div>
          <h5 class="card-title fw-bold">Bank Soal Umum</h5>
          <p class="card-text text-muted mb-4">Soal yang dapat diakses oleh semua guru di sekolah</p>
          <a href="<?= base_url('guru/bank-soal/kategori/umum') ?>" class="btn btn-outline-primary">
            <i class="fas fa-arrow-right me-2"></i>Lihat Bank Soal
          </a>
        </div>
      </div>
    </div>

    <!-- Kategori Kelas yang Diajar -->
    <?php if (!empty($kelasGuru)): ?>
      <?php foreach ($kelasGuru as $kelas): ?>
        <div class="col-lg-4 col-md-6">
          <div class="card h-100 shadow-sm hover-shadow">
            <div class="card-body text-center p-4">
              <div class="mb-3">
                <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                  <i class="fas fa-chalkboard-teacher fa-2x text-success"></i>
                </div>
              </div>
              <h5 class="card-title fw-bold">Kelas <?= esc($kelas['nama_kelas']) ?></h5>
              <p class="card-text text-muted mb-4">Bank soal khusus untuk kelas yang Anda ajar</p>
              <a href="<?= base_url('guru/bank-soal/kategori/' . urlencode($kelas['nama_kelas'])) ?>" class="btn btn-outline-success">
                <i class="fas fa-arrow-right me-2"></i>Lihat Bank Soal
              </a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="col-lg-4 col-md-6">
        <div class="card h-100 shadow-sm">
          <div class="card-body text-center p-4">
            <div class="mb-3">
              <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                <i class="fas fa-exclamation-triangle fa-2x text-warning"></i>
              </div>
            </div>
            <h5 class="card-title fw-bold">Tidak Ada Kelas</h5>
            <p class="card-text text-muted mb-4">Anda belum ditugaskan untuk mengajar kelas tertentu</p>
            <button class="btn btn-outline-secondary" disabled>
              <i class="fas fa-times me-2"></i>Tidak Tersedia
            </button>
          </div>
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>

<!-- Modal Tambah Bank Soal -->
<div class="modal fade" id="modalTambahBankSoal" tabindex="-1" aria-labelledby="modalTambahBankSoalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header border-0">
        <h5 class="modal-title fw-bold" id="modalTambahBankSoalLabel">
          <i class="fas fa-plus-circle text-primary me-2"></i>Tambah Bank Soal Baru
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="<?= base_url('guru/bank-soal/tambah') ?>" method="post">
        <?= csrf_field() ?>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-12">
              <label for="kategori" class="form-label fw-semibold">Kategori Bank Soal</label>
              <select class="form-select" id="kategori" name="kategori" required>
                <option value="">Pilih Kategori</option>
                <option value="umum">🌍 Umum (Dapat diakses semua guru)</option>
                <?php if (!empty($kelasGuru)): ?>
                  <?php foreach ($kelasGuru as $kelas): ?>
                    <option value="<?= esc($kelas['nama_kelas']) ?>">
                      🏫 Kelas <?= esc($kelas['nama_kelas']) ?>
                    </option>
                  <?php endforeach; ?>
                <?php endif; ?>
              </select>
              <div class="form-text">Kategori menentukan siapa saja yang dapat mengakses bank soal ini</div>
            </div>

            <div class="col-12">
              <label for="jenis_ujian_id" class="form-label fw-semibold">Mata Pelajaran</label>
              <select class="form-select" id="jenis_ujian_id" name="jenis_ujian_id" required disabled>
                <option value="">Pilih kategori terlebih dahulu</option>
              </select>
              <div class="form-text">Pilih Mata Pelajaran yang sesuai dengan bank soal yang akan dibuat</div>
              <div id="loading-jenis-ujian" class="d-none">
                <div class="spinner-border spinner-border-sm text-primary" role="status">
                  <span class="visually-hidden">Loading...</span>
                </div>
                <span class="ms-2 text-muted">Memuat Mata Pelajaran...</span>
              </div>
            </div>

            <div class="col-12">
              <label for="nama_ujian" class="form-label fw-semibold">Nama Bank Ujian</label>
              <input type="text" class="form-control" id="nama_ujian" name="nama_ujian"
                placeholder="Contoh: Ujian Tengah Semester Ganjil 2024" required>
              <div class="form-text">Berikan nama yang spesifik dan mudah dikenali</div>
            </div>

            <div class="col-12">
              <label for="deskripsi" class="form-label fw-semibold">Deskripsi (Opsional)</label>
              <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"
                placeholder="Deskripsi singkat tentang bank soal ini..."></textarea>
              <div class="form-text">Deskripsi akan membantu guru lain memahami isi bank soal</div>
            </div>
          </div>
        </div>
        <div class="modal-footer border-0">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">
            <i class="fas fa-times me-2"></i>Batal
          </button>
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-2"></i>Simpan Bank Soal
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<style>
  .hover-shadow:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
    transition: all 0.3s ease;
  }

  .card {
    border: none;
    transition: all 0.3s ease;
  }

  .card-body {
    border-radius: 0.5rem;
  }

  .btn-outline-primary:hover,
  .btn-outline-success:hover {
    transform: scale(1.05);
    transition: transform 0.2s ease;
  }

  .form-select:focus,
  .form-control:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
  }

  .modal-content {
    border: none;
    border-radius: 1rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
  }

  .modal-header {
    padding: 1.5rem 1.5rem 0 1.5rem;
  }

  .modal-body {
    padding: 1.5rem;
  }

  .modal-footer {
    padding: 0 1.5rem 1.5rem 1.5rem;
  }
</style>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const kategoriSelect = document.getElementById('kategori');
    const jenisUjianSelect = document.getElementById('jenis_ujian_id');
    const loadingIndicator = document.getElementById('loading-jenis-ujian');
    const modal = document.getElementById('modalTambahBankSoal');

    // Handle kategori change
    kategoriSelect.addEventListener('change', function() {
      const selectedKategori = this.value;

      if (!selectedKategori) {
        // Reset Mata Pelajaran dropdown
        jenisUjianSelect.innerHTML = '<option value="">Pilih kategori terlebih dahulu</option>';
        jenisUjianSelect.disabled = true;
        return;
      }

      // Show loading
      loadingIndicator.classList.remove('d-none');
      jenisUjianSelect.disabled = true;
      jenisUjianSelect.innerHTML = '<option value="">Memuat...</option>';

      // Fetch Mata Pelajaran for selected kategori
      fetch(`<?= base_url('guru/bank-soal/api/jenis-ujian-kelas') ?>?kategori=${encodeURIComponent(selectedKategori)}`)
        .then(response => response.json())
        .then(data => {
          loadingIndicator.classList.add('d-none');

          if (data.status === 'success') {
            jenisUjianSelect.innerHTML = '<option value="">Pilih Mata Pelajaran</option>';

            if (data.data && data.data.length > 0) {
              data.data.forEach(jenis => {
                const option = document.createElement('option');
                option.value = jenis.jenis_ujian_id;
                option.textContent = jenis.nama_jenis;
                jenisUjianSelect.appendChild(option);
              });
              jenisUjianSelect.disabled = false;
            } else {
              jenisUjianSelect.innerHTML = '<option value="" disabled>Tidak ada Mata Pelajaran tersedia untuk kategori ini</option>';

              // Show info message
              const alert = document.createElement('div');
              alert.className = 'alert alert-info alert-dismissible fade show mt-2';
              alert.innerHTML = `
                            <i class="fas fa-info-circle me-2"></i>
                            Belum ada Mata Pelajaran untuk kategori "${selectedKategori}". 
                            Silakan buat Mata Pelajaran terlebih dahulu di menu <strong>Mata Pelajaran</strong>.
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        `;

              const container = jenisUjianSelect.parentNode;
              container.appendChild(alert);

              // Auto remove alert after 5 seconds
              setTimeout(() => {
                if (alert.parentNode) {
                  alert.remove();
                }
              }, 5000);
            }
          } else {
            jenisUjianSelect.innerHTML = '<option value="" disabled>Error memuat data</option>';
            console.error('Error:', data.message);

            // Show error message
            alert(`Error: ${data.message}`);
          }
        })
        .catch(error => {
          loadingIndicator.classList.add('d-none');
          jenisUjianSelect.innerHTML = '<option value="" disabled>Error memuat data</option>';
          console.error('Fetch error:', error);
          alert('Terjadi kesalahan saat memuat Mata Pelajaran. Silakan coba lagi.');
        });
    });

    // Handle modal cleanup
    if (modal) {
      modal.addEventListener('hidden.bs.modal', function() {
        // Reset form
        const form = modal.querySelector('form');
        if (form) {
          form.reset();
        }

        // Reset dropdowns
        jenisUjianSelect.innerHTML = '<option value="">Pilih kategori terlebih dahulu</option>';
        jenisUjianSelect.disabled = true;
        loadingIndicator.classList.add('d-none');

        // Remove any dynamic alerts
        const alerts = modal.querySelectorAll('.alert:not(.alert-info):not([role="alert"])');
        alerts.forEach(alert => alert.remove());

        // Force remove backdrop
        const backdrop = document.querySelector('.modal-backdrop');
        if (backdrop) {
          backdrop.remove();
        }

        // Reset body classes
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
      });
    }

    // Form validation before submit
    const form = modal?.querySelector('form');
    if (form) {
      form.addEventListener('submit', function(e) {
        const kategori = kategoriSelect.value;
        const jenisUjian = jenisUjianSelect.value;
        const namaUjian = document.getElementById('nama_ujian').value;

        if (!kategori || !jenisUjian || !namaUjian.trim()) {
          e.preventDefault();
          alert('Mohon lengkapi semua field yang wajib diisi!');
          return false;
        }
      });
    }
  });
</script>

<?= $this->endSection() ?>