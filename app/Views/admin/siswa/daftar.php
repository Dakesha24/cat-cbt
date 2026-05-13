<?= $this->extend('templates/admin/admin_template') ?>

<?= $this->section('content') ?>
<br><br>
<div class="container">
    <div class="row mb-4 py-4">
        <div class="col">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Kelola Siswa</h2>
                <a href="<?= base_url('admin/siswa/tambah') ?>" class="btn btn-success">
                    <i class="bi bi-plus-circle me-2"></i>Tambah Siswa
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
                        <div class="col-md-2">
                            <select class="form-select" id="filterJenisKelamin">
                                <option value="">Semua Jenis Kelamin</option>
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" id="filterSekolah">
                                <option value="">Semua Sekolah</option>
                                <?php
                                $sekolahUnique = array_unique(array_column($siswa, 'nama_sekolah'));
                                foreach ($sekolahUnique as $sekolah):
                                    if ($sekolah): ?>
                                        <option value="<?= $sekolah ?>"><?= $sekolah ?></option>
                                <?php endif;
                                endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" id="filterKelas">
                                <option value="">Semua Kelas</option>
                                <?php
                                $kelasUnique = array_unique(array_column($siswa, 'nama_kelas'));
                                foreach ($kelasUnique as $kelas):
                                    if ($kelas): ?>
                                        <option value="<?= $kelas ?>"><?= $kelas ?></option>
                                <?php endif;
                                endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="text" class="form-control" id="searchSiswa" placeholder="Cari nama, NIS, atau email...">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-2">
                            <button class="btn btn-outline-secondary w-100" onclick="resetFilter()">
                                <i class="bi bi-arrow-clockwise"></i> Reset
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabel Siswa -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="tableSiswa">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Nama Lengkap</th>
                                    <th>Jenis Kelamin</th>
                                    <th>No. Peserta</th>
                                    <th>Sekolah</th>
                                    <th>Kelas</th>
                                    <th>Tahun Ajaran</th>
                                    <th>Status</th>
                                    <th>Terdaftar</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($siswa)): ?>
                                    <tr>
                                        <td colspan="12" class="text-center">Tidak ada data siswa</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($siswa as $index => $s): ?>
                                        <tr data-status="<?= $s['status'] ?>"
                                            data-jenis-kelamin="<?= $s['jenis_kelamin'] ?? '' ?>"
                                            data-sekolah="<?= $s['nama_sekolah'] ?>"
                                            data-kelas="<?= $s['nama_kelas'] ?>">
                                            <td><?= $index + 1 ?></td>
                                            <td><?= esc($s['username']) ?></td>
                                            <td><?= esc($s['email']) ?></td>
                                            <td><?= esc($s['nama_lengkap'] ?? '-') ?></td>
                                            <td>
                                                <?php if (!empty($s['jenis_kelamin'])): ?>
                                                    <?php if ($s['jenis_kelamin'] == 'Laki-laki'): ?>
                                                        <span class="badge bg-primary">
                                                            <i class="bi bi-person-standing me-1"></i>L
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">
                                                            <i class="bi bi-person-standing-dress me-1"></i>P
                                                        </span>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= esc($s['nomor_peserta'] ?? '-') ?></td>
                                            <td>
                                                <small class="text-muted"><?= esc($s['nama_sekolah'] ?? '-') ?></small>
                                            </td>
                                            <td><?= esc($s['nama_kelas'] ?? '-') ?></td>
                                            <td><?= esc($s['tahun_ajaran'] ?? '-') ?></td>
                                            <td>
                                                <?php if ($s['status'] == 'active'): ?>
                                                    <span class="badge bg-success">Aktif</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Nonaktif</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= date('d/m/Y', strtotime($s['created_at'])) ?></td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="<?= base_url('admin/siswa/edit/' . $s['user_id']) ?>"
                                                        class="btn btn-sm btn-outline-primary" title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>

                                                    <?php if ($s['status'] == 'active'): ?>
                                                        <a href="<?= base_url('admin/siswa/hapus/' . $s['user_id']) ?>"
                                                            class="btn btn-sm btn-outline-danger"
                                                            title="Nonaktifkan"
                                                            onclick="return confirm('Yakin ingin menonaktifkan siswa ini?')">
                                                            <i class="bi bi-person-x"></i>
                                                        </a>
                                                    <?php else: ?>
                                                        <a href="<?= base_url('admin/siswa/restore/' . $s['user_id']) ?>"
                                                            class="btn btn-sm btn-outline-success"
                                                            title="Aktifkan"
                                                            onclick="return confirm('Yakin ingin mengaktifkan siswa ini?')">
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
    document.getElementById('filterJenisKelamin').addEventListener('change', filterTable);
    document.getElementById('filterSekolah').addEventListener('change', filterTable);
    document.getElementById('filterKelas').addEventListener('change', filterTable);
    document.getElementById('searchSiswa').addEventListener('keyup', filterTable);

    function filterTable() {
        const statusFilter = document.getElementById('filterStatus').value;
        const jenisKelaminFilter = document.getElementById('filterJenisKelamin').value;
        const sekolahFilter = document.getElementById('filterSekolah').value;
        const kelasFilter = document.getElementById('filterKelas').value;
        const searchText = document.getElementById('searchSiswa').value.toLowerCase();
        const rows = document.querySelectorAll('#tableSiswa tbody tr');

        rows.forEach(row => {
            if (row.cells.length === 1) return; // Skip "no data" row

            const status = row.getAttribute('data-status');
            const jenisKelamin = row.getAttribute('data-jenis-kelamin');
            const sekolah = row.getAttribute('data-sekolah') || '';
            const kelas = row.getAttribute('data-kelas') || '';
            const username = row.cells[1].textContent.toLowerCase();
            const email = row.cells[2].textContent.toLowerCase();
            const nama = row.cells[3].textContent.toLowerCase();
            const noPeserta = row.cells[5].textContent.toLowerCase();

            const statusMatch = !statusFilter || status === statusFilter;
            const jenisKelaminMatch = !jenisKelaminFilter || jenisKelamin === jenisKelaminFilter;
            const sekolahMatch = !sekolahFilter || sekolah === sekolahFilter;
            const kelasMatch = !kelasFilter || kelas === kelasFilter;
            const textMatch = !searchText ||
                username.includes(searchText) ||
                email.includes(searchText) ||
                nama.includes(searchText) ||
                noPeserta.includes(searchText);

            row.style.display = (statusMatch && jenisKelaminMatch && sekolahMatch && kelasMatch && textMatch) ? '' : 'none';
        });
    }

    function resetFilter() {
        document.getElementById('filterStatus').value = '';
        document.getElementById('filterJenisKelamin').value = '';
        document.getElementById('filterSekolah').value = '';
        document.getElementById('filterKelas').value = '';
        document.getElementById('searchSiswa').value = '';
        filterTable();
    }
</script>

<?= $this->endSection() ?>