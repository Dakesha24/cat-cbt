<?= $this->extend('templates/admin/admin_template') ?>

<?= $this->section('content') ?>
<br><br>
<div class="container">
  <div class="row mb-4 py-4">
    <div class="col">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Kelola Guru</h2>
        <a href="<?= base_url('admin/guru/tambah') ?>" class="btn btn-primary">
          <i class="bi bi-plus-circle me-2"></i>Tambah Guru
        </a>
      </div>

      <!-- Flash Messages -->
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

      <!-- Filter -->
      <div class="card mb-4">
        <div class="card-body">
          <div class="row">
            <div class="col-md-2">
              <select class="form-select" id="filterStatus">
                <option value="">Semua Status</option>
                <option value="active">Aktif</option>
                <option value="inactive">Nonaktif</option>
              </select>
            </div>
            <div class="col-md-3">
              <select class="form-select" id="filterSekolah">
                <option value="">Semua Sekolah</option>
                <?php 
                $sekolahUnique = array_unique(array_column($guru, 'nama_sekolah'));
                foreach ($sekolahUnique as $sekolah): 
                    if ($sekolah): ?>
                        <option value="<?= $sekolah ?>"><?= $sekolah ?></option>
                    <?php endif;
                endforeach; ?>
              </select>
            </div>
            <div class="col-md-3">
              <select class="form-select" id="filterMataPelajaran">
                <option value="">Semua Mata Pelajaran</option>
                <?php 
                $mapelUnique = array_unique(array_column($guru, 'mata_pelajaran'));
                foreach ($mapelUnique as $mapel): 
                    if ($mapel): ?>
                        <option value="<?= $mapel ?>"><?= $mapel ?></option>
                    <?php endif;
                endforeach; ?>
              </select>
            </div>
            <div class="col-md-3">
              <input type="text" class="form-control" id="searchGuru" placeholder="Cari nama atau email...">
            </div>
            <div class="col-md-1">
              <button class="btn btn-outline-secondary w-100" onclick="resetFilter()">
                <i class="bi bi-arrow-clockwise"></i> Reset
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Tabel Guru -->
      <div class="card">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-striped" id="tableGuru">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Username</th>
                  <th>Email</th>
                  <th>Nama Lengkap</th>
                  <th>NIP</th>
                  <th>Mata Pelajaran</th>
                  <th>Sekolah</th>
                  <th>Total Kelas</th>
                  <th>Status</th>
                  <th>Terdaftar</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($guru)): ?>
                  <tr>
                    <td colspan="11" class="text-center">Tidak ada data guru</td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($guru as $index => $g): ?>
                    <tr data-status="<?= $g['status'] ?>" 
                        data-sekolah="<?= $g['nama_sekolah'] ?>" 
                        data-mapel="<?= $g['mata_pelajaran'] ?>">
                      <td><?= $index + 1 ?></td>
                      <td><?= esc($g['username']) ?></td>
                      <td><?= esc($g['email']) ?></td>
                      <td><?= esc($g['nama_lengkap'] ?? '-') ?></td>
                      <td><?= esc($g['nip'] ?? '-') ?></td>
                      <td><?= esc($g['mata_pelajaran'] ?? '-') ?></td>
                      <td>
                        <small class="text-muted"><?= esc($g['nama_sekolah'] ?? '-') ?></small>
                      </td>
                      <td>
                        <?php if (isset($g['total_kelas']) && $g['total_kelas'] > 0): ?>
                          <span class="badge bg-info"><?= $g['total_kelas'] ?> Kelas</span>
                        <?php else: ?>
                          <span class="badge bg-secondary">0 Kelas</span>
                        <?php endif; ?>
                      </td>
                      <td>
                        <?php if ($g['status'] == 'active'): ?>
                          <span class="badge bg-success">Aktif</span>
                        <?php else: ?>
                          <span class="badge bg-danger">Nonaktif</span>
                        <?php endif; ?>
                      </td>
                      <td><?= date('d/m/Y', strtotime($g['created_at'])) ?></td>
                      <td>
                        <div class="btn-group" role="group">
                          <a href="<?= base_url('admin/guru/edit/' . $g['user_id']) ?>"
                            class="btn btn-sm btn-outline-primary" title="Edit">
                            <i class="bi bi-pencil"></i>
                          </a>

                          <?php if ($g['status'] == 'active'): ?>
                            <a href="<?= base_url('admin/guru/hapus/' . $g['user_id']) ?>"
                              class="btn btn-sm btn-outline-danger"
                              title="Nonaktifkan"
                              onclick="return confirm('Yakin ingin menonaktifkan guru ini?')">
                              <i class="bi bi-person-x"></i>
                            </a>
                          <?php else: ?>
                            <a href="<?= base_url('admin/guru/restore/' . $g['user_id']) ?>"
                              class="btn btn-sm btn-outline-success"
                              title="Aktifkan"
                              onclick="return confirm('Yakin ingin mengaktifkan guru ini?')">
                              <i class="bi bi-person-check"></i>
                            </a>
                          <?php endif; ?>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  // Filter dan Search
  document.getElementById('filterStatus').addEventListener('change', filterTable);
  document.getElementById('filterSekolah').addEventListener('change', filterTable);
  document.getElementById('filterMataPelajaran').addEventListener('change', filterTable);
  document.getElementById('searchGuru').addEventListener('keyup', filterTable);

  function filterTable() {
    const statusFilter = document.getElementById('filterStatus').value;
    const sekolahFilter = document.getElementById('filterSekolah').value;
    const mapelFilter = document.getElementById('filterMataPelajaran').value;
    const searchText = document.getElementById('searchGuru').value.toLowerCase();
    const rows = document.querySelectorAll('#tableGuru tbody tr');

    rows.forEach(row => {
      if (row.cells.length === 1) return; // Skip "no data" row

      const status = row.getAttribute('data-status');
      const sekolah = row.getAttribute('data-sekolah') || '';
      const mapel = row.getAttribute('data-mapel') || '';
      const username = row.cells[1].textContent.toLowerCase();
      const email = row.cells[2].textContent.toLowerCase();
      const nama = row.cells[3].textContent.toLowerCase();
      const nip = row.cells[4].textContent.toLowerCase();

      const statusMatch = !statusFilter || status === statusFilter;
      const sekolahMatch = !sekolahFilter || sekolah === sekolahFilter;
      const mapelMatch = !mapelFilter || mapel === mapelFilter;
      const textMatch = !searchText ||
        username.includes(searchText) ||
        email.includes(searchText) ||
        nama.includes(searchText) ||
        nip.includes(searchText);

      row.style.display = (statusMatch && sekolahMatch && mapelMatch && textMatch) ? '' : 'none';
    });
  }

  function resetFilter() {
    document.getElementById('filterStatus').value = '';
    document.getElementById('filterSekolah').value = '';
    document.getElementById('filterMataPelajaran').value = '';
    document.getElementById('searchGuru').value = '';
    filterTable();
  }
</script>

<?= $this->endSection() ?>