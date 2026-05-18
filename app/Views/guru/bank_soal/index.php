<?= $this->extend('templates/guru/guru_template') ?>
<?= $this->section('content') ?>

<style>
.bs-card { border-radius: 0; overflow: hidden; transition: box-shadow 0.2s ease, transform 0.2s ease; border: 1px solid #e9ecef; }
.bs-card:hover { box-shadow: 0 8px 20px rgba(0,0,0,0.08) !important; transform: translateY(-2px); border-color: #d7dee7; }
.bs-icon-wrap { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; flex-shrink: 0; }
.modal-content { border-radius: 0 !important; }
.bs-card-title { font-size: 1rem; line-height: 1.3; }
.bs-card-subtitle { font-size: 0.76rem; letter-spacing: 0.02em; }
.bs-card-desc { font-size: 0.82rem; line-height: 1.55; color: #6c757d; }
.bs-meta-box { background: #fbfcfd; border: 1px solid #edf1f5; padding: 0.7rem 0.8rem; margin-bottom: 0.85rem; }
.bs-meta-label { font-size: 0.68rem; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; color: #8b95a1; margin-bottom: 0.28rem; }
.bs-meta-value { font-size: 0.8rem; color: #3f4954; line-height: 1.45; }
.empty-state { padding: 4rem 1rem; text-align: center; }
.empty-state i { font-size: 3.5rem; color: #adb5bd; display: block; margin-bottom: 1rem; }
</style>

<div class="container-fluid py-4">

  <!-- Header -->
  <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div>
      <h2 class="fw-bold text-dark mb-1">Bank Soal</h2>
      <p class="text-muted mb-0">Kelola koleksi soal untuk digunakan dalam ujian</p>
    </div>
    <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahBankSoal">
      <i class="bi bi-plus-lg me-2"></i>Tambah Bank Soal
    </button>
  </div>

  <!-- Alerts -->
  <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm"><i class="bi bi-check-circle-fill me-2"></i><?= session()->getFlashdata('success') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
  <?php endif; ?>
  <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm"><i class="bi bi-exclamation-triangle-fill me-2"></i><?= session()->getFlashdata('error') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
  <?php endif; ?>

  <!-- Category Cards -->
  <div class="row g-4">
    <!-- Umum -->
    <div class="col-xl-4 col-lg-6">
        <div class="card border-0 shadow-sm h-100 bs-card">
          <div class="card-body d-flex flex-column p-4">
            <div class="d-flex align-items-center mb-3">
              <div class="bs-icon-wrap me-3 bg-primary bg-opacity-10">
                <i class="bi bi-globe text-primary"></i>
              </div>
              <div>
                <h5 class="fw-bold mb-1 bs-card-title">Bank Soal Umum</h5>
                <div class="text-muted bs-card-subtitle">Dapat diakses semua guru</div>
              </div>
            </div>
          <p class="bs-card-desc flex-grow-1 mb-3">Soal umum yang dapat digunakan untuk semua kelas dan mata pelajaran.</p>
          <div class="bs-meta-box">
            <div class="bs-meta-label">Sekolah</div>
            <div class="bs-meta-value">Semua sekolah</div>
          </div>
          <a href="<?= base_url('guru/bank-soal/kategori/umum') ?>" class="btn btn-outline-primary w-100">
            <i class="bi bi-arrow-right me-2"></i>Pilih Mata Pelajaran
          </a>
        </div>
      </div>
    </div>

    <!-- Kelas yang diajar -->
    <?php if (!empty($kelasGuru)): ?>
      <?php foreach ($kelasGuru as $kelas): ?>
        <div class="col-xl-4 col-lg-6">
          <div class="card border-0 shadow-sm h-100 bs-card">
            <div class="card-body d-flex flex-column p-4">
              <div class="d-flex align-items-center mb-3">
                <div class="bs-icon-wrap me-3 bg-success bg-opacity-10">
                  <i class="bi bi-building text-success"></i>
                </div>
                <div>
                  <h5 class="fw-bold mb-1 bs-card-title">Kelas <?= esc($kelas['nama_kelas']) ?></h5>
                  <div class="text-muted bs-card-subtitle">Bank soal kelas Anda</div>
                </div>
              </div>
              <p class="bs-card-desc flex-grow-1 mb-3">Bank soal khusus untuk kelas yang Anda ampu.</p>
              <div class="bs-meta-box">
                <div class="bs-meta-label">Sekolah</div>
                <div class="bs-meta-value"><?= esc($kelas['nama_sekolah'] ?? 'Belum terdeteksi') ?></div>
              </div>
              <a href="<?= base_url('guru/bank-soal/kategori/' . urlencode($kelas['nama_kelas'])) ?>" class="btn btn-outline-success w-100">
                <i class="bi bi-arrow-right me-2"></i>Pilih Mata Pelajaran
              </a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="col-xl-4 col-lg-6">
        <div class="card border-0 shadow-sm h-100 bs-card">
          <div class="card-body d-flex flex-column p-4">
            <div class="d-flex align-items-center mb-3">
              <div class="bs-icon-wrap me-3 bg-warning bg-opacity-10">
                <i class="bi bi-exclamation-circle text-warning"></i>
              </div>
              <div>
                <h5 class="fw-bold mb-0">Belum Ada Kelas</h5>
                <small class="text-muted">Tidak ada kelas ditugaskan</small>
              </div>
            </div>
            <p class="text-muted small flex-grow-1 mb-3">Anda belum ditugaskan untuk mengajar kelas tertentu.</p>
            <button class="btn btn-outline-secondary w-100" disabled>Tidak Tersedia</button>
          </div>
        </div>
      </div>
    <?php endif; ?>
  </div>

</div>

<!-- Modal Tambah Bank Soal -->
<div class="modal fade" id="modalTambahBankSoal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-primary text-white px-4 py-3">
        <h5 class="modal-title fw-semibold"><i class="bi bi-plus-circle me-2"></i>Tambah Bank Soal Baru</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form action="<?= base_url('guru/bank-soal/tambah') ?>" method="post">
        <?= csrf_field() ?>
        <div class="modal-body px-4 py-4">
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label small fw-semibold">Kelas / Kategori <span class="text-danger">*</span></label>
              <div class="row g-2">
                <div class="col-md-6">
                  <select class="form-select" id="bankSekolah" onchange="loadBankKelas()">
                    <option value="">Pilih Sekolah</option>
                    <option value="umum">Sekolah Umum (semua guru)</option>
                    <?php if (!empty($sekolah)): foreach ($sekolah as $s): ?>
                    <option value="<?= $s['sekolah_id'] ?>"><?= esc($s['nama_sekolah']) ?></option>
                    <?php endforeach; endif; ?>
                  </select>
                </div>
                <div class="col-md-6">
                  <select class="form-select" id="bankKelas" name="kategori" disabled required>
                    <option value="">Pilih Sekolah dulu</option>
                  </select>
                </div>
              </div>
              <div class="form-text">Pilih "Umum" agar semua guru bisa menggunakan, atau pilih sekolah → kelas.</div>
            </div>
            <div class="col-12">
              <label class="form-label small fw-semibold">Mata Pelajaran <span class="text-danger">*</span></label>
              <select class="form-select" id="jenis_ujian_id" name="jenis_ujian_id" required disabled>
                <option value="">Pilih Sekolah & Kelas dulu</option>
              </select>
              <div class="form-text">Mata pelajaran disesuaikan dengan kelas yang dipilih.</div>
            </div>
            <div class="col-12">
              <label class="form-label small fw-semibold">Nama Bank Soal <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="nama_ujian" name="nama_ujian" placeholder="Contoh: Ujian Tengah Semester Ganjil 2024" required>
            </div>
            <div class="col-12">
              <label class="form-label small fw-semibold">Deskripsi</label>
              <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" placeholder="Deskripsi singkat bank soal..."></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer border-0 bg-light px-4 py-3">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-lg me-1"></i>Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  const bankSoalJenisUjianOptions = <?= json_encode(array_map(static function ($jenis) {
    return [
      'id'       => (string) $jenis['jenis_ujian_id'],
      'nama'     => $jenis['nama_jenis'],
      'kelas_id' => isset($jenis['kelas_id']) && $jenis['kelas_id'] !== null ? (string) $jenis['kelas_id'] : '0',
    ];
  }, $jenisUjianList), JSON_UNESCAPED_UNICODE) ?>;

  function renderBankSoalMapelOptions(kelasId) {
    const mp = document.getElementById('jenis_ujian_id');
    mp.innerHTML = '<option value="">Pilih Mata Pelajaran</option>';
    const filtered = bankSoalJenisUjianOptions.filter(item => kelasId === '0' ? item.kelas_id === '0' : item.kelas_id === kelasId || item.kelas_id === '0');
    if (filtered.length === 0) { mp.innerHTML = '<option value="">Tidak ada Mata Pelajaran tersedia</option>'; mp.disabled = true; return; }
    filtered.forEach(item => { const o = document.createElement('option'); o.value = item.id; o.textContent = item.nama; mp.appendChild(o); });
    mp.disabled = false; mp.value = '';
  }

  function resetBankSoalMapelSelect() {
    const mp = document.getElementById('jenis_ujian_id');
    mp.innerHTML = '<option value="">Pilih Sekolah & Kelas dulu</option>';
    mp.disabled = true;
  }

  function loadBankKelas() {
    const skl = document.getElementById('bankSekolah').value;
    const kls = document.getElementById('bankKelas');
    if (skl === 'umum') { kls.innerHTML = '<option value="umum">Kelas Umum</option>'; kls.value = 'umum'; kls.disabled = false; renderBankSoalMapelOptions('0'); return; }
    kls.innerHTML = '<option value="">Loading...</option>'; kls.disabled = !skl;
    if (!skl) { kls.innerHTML = '<option value="">Pilih Sekolah dulu</option>'; resetBankSoalMapelSelect(); return; }
    fetch('<?= base_url('guru/api/kelas-by-sekolah/') ?>' + skl).then(r => r.json()).then(d => {
      let o = '<option value="">Pilih Kelas</option>';
      if (d.status === 'success') d.data.forEach(k => { o += '<option value="' + k.nama_kelas + '" data-id="' + k.kelas_id + '">' + k.nama_kelas + ' (' + k.tahun_ajaran + ')</option>'; });
      kls.innerHTML = o; kls.disabled = false; resetBankSoalMapelSelect();
    });
  }

  document.getElementById('bankKelas').addEventListener('change', function () {
    const selOpt = this.selectedOptions[0];
    const kelasId = selOpt?.dataset?.id || (this.value === 'umum' ? '0' : null);
    if (!kelasId && this.value !== 'umum') { resetBankSoalMapelSelect(); return; }
    renderBankSoalMapelOptions(kelasId);
  });

  document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('modalTambahBankSoal');
    if (modal) {
      modal.addEventListener('hidden.bs.modal', function () {
        const form = modal.querySelector('form'); if (form) form.reset();
        document.getElementById('bankKelas').innerHTML = '<option value="">Pilih Sekolah dulu</option>';
        document.getElementById('bankKelas').disabled = true;
        resetBankSoalMapelSelect();
      });
    }
  });
</script>

<?= $this->endSection() ?>
