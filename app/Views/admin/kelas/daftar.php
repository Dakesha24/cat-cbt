<?= $this->extend('templates/admin/admin_template') ?>

<?= $this->section('content') ?>
<br><br>
<div class="container">
    <div class="row mb-4 py-4">
        <div class="col">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Kelola Kelas</h2>
                <a href="<?= base_url('admin/kelas/tambah') ?>" class="btn btn-warning">
                    <i class="bi bi-plus-circle me-2"></i>Tambah Kelas
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
                        <div class="col-md-4">
                            <select class="form-select" id="filterTahun">
                                <option value="">Semua Tahun Ajaran</option>
                                <?php 
                                $tahunUnique = array_unique(array_column($kelas, 'tahun_ajaran'));
                                rsort($tahunUnique);
                                foreach ($tahunUnique as $tahun): ?>
                                    <option value="<?= $tahun ?>"><?= $tahun ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="searchKelas" placeholder="Cari nama kelas...">
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-outline-secondary" onclick="resetFilter()">
                                <i class="bi bi-arrow-clockwise"></i> Reset
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabel Kelas -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="tableKelas">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Kelas</th>
                                    <th>Tahun Ajaran</th>
                                    <th>Total Siswa</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($kelas)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center">Tidak ada data kelas</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($kelas as $index => $k): ?>
                                        <tr data-tahun="<?= $k['tahun_ajaran'] ?>">
                                            <td><?= $index + 1 ?></td>
                                            <td><strong><?= esc($k['nama_kelas']) ?></strong></td>
                                            <td>
                                                <span class="badge bg-info"><?= esc($k['tahun_ajaran']) ?></span>
                                            </td>
                                            <td>
                                                <span class="badge bg-success"><?= $k['total_siswa'] ?> Siswa</span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="<?= base_url('admin/kelas/edit/' . $k['kelas_id']) ?>" 
                                                       class="btn btn-sm btn-outline-primary" title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    
                                                    <?php if ($k['total_siswa'] == 0): ?>
                                                        <a href="<?= base_url('admin/kelas/hapus/' . $k['kelas_id']) ?>" 
                                                           class="btn btn-sm btn-outline-danger" 
                                                           title="Hapus"
                                                           onclick="return confirm('Yakin ingin menghapus kelas ini?')">
                                                            <i class="bi bi-trash"></i>
                                                        </a>
                                                    <?php else: ?>
                                                        <button class="btn btn-sm btn-outline-secondary" 
                                                                title="Tidak dapat dihapus karena masih memiliki siswa" disabled>
                                                            <i class="bi bi-lock"></i>
                                                        </button>
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
document.getElementById('filterTahun').addEventListener('change', filterTable);
document.getElementById('searchKelas').addEventListener('keyup', filterTable);

function filterTable() {
    const tahunFilter = document.getElementById('filterTahun').value;
    const searchText = document.getElementById('searchKelas').value.toLowerCase();
    const rows = document.querySelectorAll('#tableKelas tbody tr');

    rows.forEach(row => {
        if (row.cells.length === 1) return; // Skip "no data" row
        
        const tahun = row.getAttribute('data-tahun');
        const namaKelas = row.cells[1].textContent.toLowerCase();
        
        const tahunMatch = !tahunFilter || tahun === tahunFilter;
        const textMatch = !searchText || namaKelas.includes(searchText);
        
        row.style.display = (tahunMatch && textMatch) ? '' : 'none';
    });
}

function resetFilter() {
    document.getElementById('filterTahun').value = '';
    document.getElementById('searchKelas').value = '';
    filterTable();
}
</script>

<?= $this->endSection() ?>