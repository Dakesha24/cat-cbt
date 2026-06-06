<?= $this->extend('templates/admin/admin_template') ?>
<?= $this->section('content') ?>

<style>
.an-filter         { background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; margin-bottom: 1.25rem; }
.an-filter-head    { padding: 12px 18px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; cursor: pointer; user-select: none; }
.an-filter-head h6 { margin: 0; font-size: .8rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #64748b; }
.an-filter-body    { padding: 16px 18px; }
.an-filter-grid    { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 12px; }
.an-filter-label   { font-size: .78rem; font-weight: 600; color: #374151; margin-bottom: 4px; display: block; }
.an-filter-body .form-select,
.an-filter-body .form-control { font-size: .85rem; border-color: #e2e8f0; border-radius: 7px; }
.page-table { width: 100%; border-collapse: collapse; }
.page-table thead th { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #9ca3af; background: #f9fafb; border-bottom: 1px solid #e9ecef; padding: 0.6rem 1rem; white-space: nowrap; }
.page-table tbody td { padding: 0.85rem 1rem; vertical-align: middle; border-bottom: 1px solid #f3f4f6; font-size: 0.875rem; color: #374151; }
.page-table tbody tr:last-child td { border-bottom: 0; }
.page-table tbody tr:hover td { background: #f8f9ff; }
.stat-pill { display: inline-flex; align-items: center; gap: 0.3rem; padding: 0.22rem 0.6rem; border-radius: 999px; font-size: 0.72rem; font-weight: 600; }
.stat-pill.siswa   { background: #ecfdf5; color: #065f46; }
.stat-pill.tahun   { background: #eff6ff; color: #1e40af; }
.btn-icon { width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border: none; border-radius: 6px; font-size: 0.875rem; transition: all 0.15s; text-decoration: none; }
.btn-icon.primary { background: #eff6ff; color: #2563eb; } .btn-icon.primary:hover { background: #dbeafe; }
.btn-icon.danger  { background: #fef2f2; color: #dc2626; } .btn-icon.danger:hover  { background: #fee2e2; }
.btn-icon.muted   { background: #f3f4f6; color: #9ca3af; cursor: not-allowed; }
.class-name { font-weight: 600; color: #111827; }
.school-tag { font-size: 0.75rem; color: #6b7280; margin-top: 2px; }
</style>

<div class="container-fluid py-4">

  <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div>
      <h2 class="fw-bold text-dark mb-1">Kelola Kelas</h2>
      <p class="text-muted mb-0">Kelola data kelas dari seluruh sekolah</p>
    </div>
    <a href="<?= base_url('admin/kelas/tambah') ?>" class="btn btn-primary shadow-sm">
      <i class="bi bi-plus-lg me-2"></i>Tambah Kelas
    </a>
  </div>

  <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm">
      <i class="bi bi-check-circle-fill me-2"></i><?= session()->getFlashdata('success') ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>
  <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm">
      <i class="bi bi-exclamation-triangle-fill me-2"></i><?= session()->getFlashdata('error') ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <!-- Filter -->
  <div class="an-filter">
    <div class="an-filter-head" onclick="toggleFilterKelas()">
      <h6><i class="bi bi-sliders2 me-2"></i>Filter</h6>
      <i class="bi bi-chevron-down" id="filterChevronKelas" style="transition:transform .2s;color:#94a3b8"></i>
    </div>
    <div id="filterBodyKelas" style="display:none">
      <div class="an-filter-body">
        <div class="an-filter-grid">
          <div>
            <label class="an-filter-label">Tahun Ajaran</label>
            <select class="form-select form-select-sm" id="filterTahun">
              <option value="">Semua Tahun</option>
              <?php
              $tahunUnique = array_unique(array_column($kelas, 'tahun_ajaran'));
              rsort($tahunUnique);
              foreach ($tahunUnique as $tahun): ?>
                <option value="<?= esc($tahun) ?>"><?= esc($tahun) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label class="an-filter-label">Cari Kelas</label>
            <input type="text" class="form-control form-control-sm" id="searchKelas" placeholder="Nama kelas...">
          </div>
        </div>
        <div class="d-flex gap-2 mt-3">
          <button type="button" class="btn btn-outline-secondary btn-sm" onclick="resetFilterKelas()">
            <i class="bi bi-arrow-clockwise me-1"></i>Reset
          </button>
        </div>
      </div>
    </div>
  </div>

  <div class="card border-0 shadow-sm" style="border-radius:0">
    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center" style="padding:0.85rem 1.25rem">
      <span class="fw-semibold text-dark">Daftar Kelas</span>
      <small class="text-muted" id="jumlahKelas"><?= count($kelas) ?> kelas</small>
    </div>
    <div class="card-body p-0">
      <?php if (empty($kelas)): ?>
        <div class="text-center py-5">
          <i class="bi bi-grid-x text-muted d-block mb-2" style="font-size:3rem;opacity:.4"></i>
          <h6 class="text-muted">Belum ada data kelas</h6>
          <p class="text-muted small mb-3">Tambahkan kelas pertama untuk memulai</p>
          <a href="<?= base_url('admin/kelas/tambah') ?>" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i>Tambah Kelas
          </a>
        </div>
      <?php else: ?>
        <div class="table-responsive">
          <table class="page-table" id="tableKelas">
            <thead>
              <tr>
                <th style="width:44px">#</th>
                <th>Nama Kelas</th>
                <th>Tahun Ajaran</th>
                <th class="text-center">Siswa</th>
                <th style="width:90px">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($kelas as $i => $k): ?>
                <tr data-tahun="<?= esc($k['tahun_ajaran']) ?>" data-nama="<?= strtolower(esc($k['nama_kelas'])) ?>">
                  <td style="color:#9ca3af;font-size:0.8rem" class="row-no"><?= $i + 1 ?></td>
                  <td>
                    <div class="class-name"><?= esc($k['nama_kelas']) ?></div>
                    <?php if (!empty($k['nama_sekolah'])): ?>
                      <div class="school-tag"><i class="bi bi-building me-1"></i><?= esc($k['nama_sekolah']) ?></div>
                    <?php endif; ?>
                  </td>
                  <td>
                    <span class="stat-pill tahun"><?= esc($k['tahun_ajaran']) ?></span>
                  </td>
                  <td class="text-center">
                    <span class="stat-pill siswa"><i class="bi bi-people"></i><?= $k['total_siswa'] ?></span>
                  </td>
                  <td>
                    <div class="d-flex gap-1">
                      <a href="<?= base_url('admin/kelas/edit/' . $k['kelas_id']) ?>"
                         class="btn-icon primary" title="Edit">
                        <i class="bi bi-pencil"></i>
                      </a>
                      <?php if ($k['total_siswa'] == 0): ?>
                        <a href="<?= base_url('admin/kelas/hapus/' . $k['kelas_id']) ?>"
                           class="btn-icon danger" title="Hapus"
                           onclick="return confirm('Yakin ingin menghapus kelas ini?')">
                          <i class="bi bi-trash"></i>
                        </a>
                      <?php else: ?>
                        <span class="btn-icon muted" title="Tidak dapat dihapus — masih memiliki siswa">
                          <i class="bi bi-lock"></i>
                        </span>
                      <?php endif; ?>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <div id="emptyFilter" class="text-center py-4 text-muted d-none" style="font-size:0.875rem">
          <i class="bi bi-search d-block mb-1" style="font-size:1.5rem;opacity:.4"></i>
          Tidak ada kelas yang cocok dengan filter.
        </div>
      <?php endif; ?>
    </div>
  </div>

</div>

<script>
function toggleFilterKelas() {
    const body  = document.getElementById('filterBodyKelas');
    const icon  = document.getElementById('filterChevronKelas');
    const open  = body.style.display !== 'none';
    body.style.display  = open ? 'none' : '';
    icon.style.transform = open ? '' : 'rotate(180deg)';
}

function filterTable() {
    const tahunVal  = document.getElementById('filterTahun').value;
    const searchVal = document.getElementById('searchKelas').value.toLowerCase().trim();
    const rows      = document.querySelectorAll('#tableKelas tbody tr');
    let visible = 0;
    rows.forEach(row => {
        if (row.cells.length === 1) return;
        const tahunMatch  = !tahunVal  || row.dataset.tahun === tahunVal;
        const searchMatch = !searchVal || (row.dataset.nama || '').includes(searchVal);
        const show = tahunMatch && searchMatch;
        row.style.display = show ? '' : 'none';
        if (show) visible++;
    });
    const empty = document.getElementById('emptyFilter');
    if (empty) empty.classList.toggle('d-none', visible > 0);
    const jml = document.getElementById('jumlahKelas');
    if (jml) jml.textContent = visible + ' kelas';
}

function resetFilterKelas() {
    document.getElementById('filterTahun').value = '';
    document.getElementById('searchKelas').value = '';
    filterTable();
}

document.getElementById('filterTahun')?.addEventListener('change', filterTable);
document.getElementById('searchKelas')?.addEventListener('input', filterTable);
</script>

<?= $this->endSection() ?>
