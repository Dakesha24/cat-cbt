<?= $this->extend('templates/admin/admin_template') ?>

<?= $this->section('title') ?>Hasil Ujian<?= $this->endSection() ?>

<?= $this->section('content') ?>

<br><br><br>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-chart-bar"></i> Daftar Hasil Ujian
                    </h4>
                    <div>
                        <a href="<?= base_url('admin/ujian') ?>" class="btn btn-info me-2">
                            <i class="fas fa-file-alt me-1"></i>Kelola Ujian
                        </a>
                        <a href="<?= base_url('admin/jadwal') ?>" class="btn btn-secondary">
                            <i class="fas fa-calendar me-1"></i>Jadwal Ujian
                        </a>
                    </div>
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

                    <!-- Info Status Legend -->
                    <div class="alert alert-info mb-4">
                        <h6 class="alert-heading"><i class="fas fa-info-circle me-1"></i>Keterangan Status:</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <span class="badge bg-secondary me-1">Belum Mulai</span> - Ujian belum dimulai
                            </div>
                            <div class="col-md-4">
                                <span class="badge bg-warning me-1">Sedang Berlangsung</span> - Ujian sedang berlangsung
                            </div>
                            <div class="col-md-4">
                                <span class="badge bg-success me-1">Selesai</span> - Ujian telah selesai
                            </div>
                        </div>
                    </div>

                    <?php if (empty($daftarUjian)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum ada ujian terjadwal</h5>
                            <p class="text-muted">Daftar ujian akan muncul setelah ada jadwal ujian yang dibuat.</p>
                        </div>
                    <?php else: ?>
                        <!-- Filter -->
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <input type="text" class="form-control" id="searchUjian" placeholder="Cari nama ujian...">
                            </div>
                            <div class="col-md-2">
                                <select class="form-select" id="filterSekolah">
                                    <option value="">Semua Sekolah</option>
                                    <?php
                                    $sekolahUnique = array_unique(array_filter(array_column($daftarUjian, 'nama_sekolah')));
                                    foreach ($sekolahUnique as $sekolah): ?>
                                        <option value="<?= esc($sekolah) ?>"><?= esc($sekolah) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select class="form-select" id="filterKelas">
                                    <option value="">Semua Kelas</option>
                                    <?php
                                    $kelasUnique = array_unique(array_filter(array_column($daftarUjian, 'nama_kelas')));
                                    foreach ($kelasUnique as $kelas): ?>
                                        <option value="<?= esc($kelas) ?>"><?= esc($kelas) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select class="form-select" id="filterStatus">
                                    <option value="">Semua Status</option>
                                    <option value="belum_mulai">Belum Mulai</option>
                                    <option value="sedang_berlangsung">Sedang Berlangsung</option>
                                    <option value="selesai">Selesai</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select class="form-select" id="filterMatpel">
                                    <option value="">Semua Mata Pelajaran</option>
                                    <?php
                                    $matpelUnique = array_unique(array_filter(array_column($daftarUjian, 'nama_jenis')));
                                    foreach ($matpelUnique as $matpel): ?>
                                        <option value="<?= esc($matpel) ?>"><?= esc($matpel) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-1">
                                <button class="btn btn-outline-secondary" onclick="resetFilter()" title="Reset Filter">
                                    <i class="fas fa-redo"></i>
                                </button>
                            </div>
                        </div>

                        <div class="row">
                            <?php foreach ($daftarUjian as $ujian): ?>
                                <div class="col-md-6 mb-4"
                                    data-sekolah="<?= esc($ujian['nama_sekolah']) ?>"
                                    data-kelas="<?= esc($ujian['nama_kelas']) ?>"
                                    data-status="<?= esc($ujian['status_ujian']) ?>"
                                    data-matpel="<?= esc($ujian['nama_jenis']) ?>">
                                    <div class="card border-0 shadow-sm h-100">
                                        <!-- Header dengan informasi sekolah yang prominent -->
                                        <div class="card-header bg-light border-0 pb-0">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-school text-primary me-2"></i>
                                                    <div>
                                                        <h6 class="mb-0 text-primary fw-bold"><?= esc($ujian['nama_sekolah']) ?></h6>
                                                        <small class="text-muted">
                                                            <i class="fas fa-users me-1"></i><?= esc($ujian['nama_kelas']) ?>
                                                            <?php if ($ujian['tahun_ajaran']): ?>
                                                                - <?= esc($ujian['tahun_ajaran']) ?>
                                                            <?php endif; ?>
                                                        </small>
                                                    </div>
                                                </div>
                                                <span class="badge bg-<?= $ujian['status_class'] ?> fs-6"><?= $ujian['status_text'] ?></span>
                                            </div>
                                        </div>

                                        <div class="card-body pt-3">
                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                <div>
                                                    <h5 class="card-title text-dark mb-1"><?= esc($ujian['nama_ujian']) ?></h5>
                                                    <span class="badge bg-info"><?= esc($ujian['nama_jenis']) ?></span>
                                                </div>
                                            </div>

                                            <p class="card-text text-muted small">
                                                <?= strlen($ujian['deskripsi']) > 100 ? substr(esc($ujian['deskripsi']), 0, 100) . '...' : esc($ujian['deskripsi']) ?>
                                            </p>

                                            <!-- Informasi Waktu -->
                                            <div class="mb-3">
                                                <div class="text-muted small">
                                                    <div class="d-flex justify-content-between mb-1">
                                                        <span><i class="fas fa-calendar me-1"></i>Mulai:</span>
                                                        <span class="fw-medium"><?= $ujian['tanggal_mulai_format'] ?></span>
                                                    </div>
                                                    <div class="d-flex justify-content-between mb-2">
                                                        <span><i class="fas fa-calendar-check me-1"></i>Selesai:</span>
                                                        <span class="fw-medium"><?= $ujian['tanggal_selesai_format'] ?></span>
                                                    </div>
                                                    <?php if ($ujian['status_ujian'] !== 'selesai'): ?>
                                                        <div class="d-flex justify-content-between">
                                                            <span><i class="fas fa-key me-1"></i>Kode Akses:</span>
                                                            <span class="fw-bold text-primary"><?= esc($ujian['kode_akses']) ?></span>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>

                                            <!-- Statistik Peserta -->
                                            <div class="row text-center mb-3">
                                                <div class="col-3">
                                                    <div class="border-end">
                                                        <h6 class="text-success mb-0"><?= $ujian['peserta_selesai'] ?></h6>
                                                        <small class="text-muted">Selesai</small>
                                                    </div>
                                                </div>
                                                <div class="col-3">
                                                    <div class="border-end">
                                                        <h6 class="text-warning mb-0"><?= $ujian['peserta_sedang_mengerjakan'] ?></h6>
                                                        <small class="text-muted">Aktif</small>
                                                    </div>
                                                </div>
                                                <div class="col-3">
                                                    <div class="border-end">
                                                        <h6 class="text-secondary mb-0"><?= $ujian['peserta_belum_mulai'] ?></h6>
                                                        <small class="text-muted">Belum</small>
                                                    </div>
                                                </div>
                                                <div class="col-3">
                                                    <h6 class="text-primary mb-0"><?= $ujian['jumlah_peserta'] ?></h6>
                                                    <small class="text-muted">Total</small>
                                                </div>
                                            </div>

                                            <!-- Progress Bar -->
                                            <?php if ($ujian['jumlah_peserta'] > 0): ?>
                                                <?php
                                                $progressSelesai = round(($ujian['peserta_selesai'] / $ujian['jumlah_peserta']) * 100);
                                                $progressAktif = round(($ujian['peserta_sedang_mengerjakan'] / $ujian['jumlah_peserta']) * 100);
                                                ?>
                                                <div class="progress mb-3" style="height: 8px;">
                                                    <div class="progress-bar bg-success" role="progressbar" style="width: <?= $progressSelesai ?>%" title="<?= $progressSelesai ?>% Selesai"></div>
                                                    <div class="progress-bar bg-warning" role="progressbar" style="width: <?= $progressAktif ?>%" title="<?= $progressAktif ?>% Sedang Mengerjakan"></div>
                                                </div>
                                                <div class="text-center small text-muted mb-3">
                                                    Progress: <?= $progressSelesai ?>% selesai, <?= $progressAktif ?>% sedang mengerjakan
                                                </div>
                                            <?php endif; ?>

                                            <!-- Statistik Waktu (hanya tampil jika ada yang selesai) -->
                                            <?php if ($ujian['peserta_selesai'] > 0): ?>
                                                <div class="mb-3">
                                                    <h6 class="text-muted mb-2"><i class="fas fa-stopwatch me-1"></i>Statistik Waktu:</h6>
                                                    <div class="text-muted small">
                                                        <div class="d-flex justify-content-between">
                                                            <span><i class="fas fa-clock me-1"></i>Rata-rata:</span>
                                                            <span class="fw-bold text-info"><?= $ujian['rata_rata_durasi_format'] ?></span>
                                                        </div>
                                                        <div class="d-flex justify-content-between">
                                                            <span><i class="fas fa-bolt me-1"></i>Tercepat:</span>
                                                            <span class="text-success"><?= $ujian['durasi_tercepat_format'] ?></span>
                                                        </div>
                                                        <div class="d-flex justify-content-between">
                                                            <span><i class="fas fa-hourglass me-1"></i>Terlama:</span>
                                                            <span class="text-warning"><?= $ujian['durasi_terlama_format'] ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>

                                            <!-- Informasi Guru -->
                                            <div class="text-muted small mb-3">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-chalkboard-teacher me-2"></i>
                                                    <span><?= esc($ujian['nama_guru']) ?></span>
                                                </div>
                                            </div>

                                            <!-- Action Button -->
                                            <?php if ($ujian['jumlah_peserta'] > 0): ?>
                                                <a href="<?= base_url('admin/hasil-ujian/siswa/' . $ujian['jadwal_id']) ?>"
                                                    class="btn btn-primary w-100">
                                                    <i class="fas fa-eye me-1"></i>
                                                    <?php if ($ujian['status_ujian'] === 'selesai'): ?>
                                                        Lihat Hasil Ujian
                                                    <?php else: ?>
                                                        Pantau Progress Ujian
                                                    <?php endif; ?>
                                                    <span class="badge bg-light text-primary ms-2"><?= $ujian['peserta_selesai'] ?>/<?= $ujian['jumlah_peserta'] ?></span>
                                                </a>
                                            <?php else: ?>
                                                <button class="btn btn-secondary w-100" disabled>
                                                    <i class="fas fa-users me-1"></i>Belum Ada Peserta Terdaftar
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Filter functionality
    document.getElementById('searchUjian').addEventListener('keyup', filterCards);
    document.getElementById('filterSekolah').addEventListener('change', filterCards);
    document.getElementById('filterKelas').addEventListener('change', filterCards);
    document.getElementById('filterStatus').addEventListener('change', filterCards);
    document.getElementById('filterMatpel').addEventListener('change', filterCards);

    function filterCards() {
        const searchText = document.getElementById('searchUjian').value.toLowerCase();
        const sekolahFilter = document.getElementById('filterSekolah').value;
        const kelasFilter = document.getElementById('filterKelas').value;
        const statusFilter = document.getElementById('filterStatus').value;
        const matpelFilter = document.getElementById('filterMatpel').value;
        const cards = document.querySelectorAll('.col-md-6[data-sekolah][data-status]');

        cards.forEach(card => {
            const namaUjian = card.querySelector('.card-title').textContent.toLowerCase();
            const sekolah = card.getAttribute('data-sekolah');
            const kelas = card.getAttribute('data-kelas');
            const status = card.getAttribute('data-status');
            const matpel = card.getAttribute('data-matpel');

            const textMatch = !searchText || namaUjian.includes(searchText);
            const sekolahMatch = !sekolahFilter || sekolah === sekolahFilter;
            const kelasMatch = !kelasFilter || kelas === kelasFilter;
            const statusMatch = !statusFilter || status === statusFilter;
            const matpelMatch = !matpelFilter || matpel === matpelFilter;

            card.style.display = (textMatch && sekolahMatch && kelasMatch && statusMatch && matpelMatch) ? '' : 'none';
        });
    }

    function resetFilter() {
        document.getElementById('searchUjian').value = '';
        document.getElementById('filterSekolah').value = '';
        document.getElementById('filterKelas').value = '';
        document.getElementById('filterStatus').value = '';
        document.getElementById('filterMatpel').value = '';
        filterCards();
    }

    // Initialize tooltips for progress bars
    document.addEventListener('DOMContentLoaded', function() {
        const progressBars = document.querySelectorAll('.progress-bar');
        progressBars.forEach(bar => {
            new bootstrap.Tooltip(bar);
        });
    });
</script>

<style>
    .border-end {
        border-right: 1px solid #dee2e6 !important;
    }

    .progress-bar {
        transition: width 0.3s ease;
    }

    .card:hover {
        transform: translateY(-2px);
        transition: transform 0.2s ease;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1) !important;
    }

    .card-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }

    .fw-medium {
        font-weight: 500;
    }

    @media (max-width: 768px) {
        .border-end {
            border-right: none !important;
            border-bottom: 1px solid #dee2e6 !important;
            margin-bottom: 0.5rem;
            padding-bottom: 0.5rem;
        }

        .border-end:last-child {
            border-bottom: none !important;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .col-3 {
            font-size: 0.85rem;
        }

        .card-header {
            padding: 0.75rem;
        }

        .card-header h6 {
            font-size: 0.9rem;
        }
    }
</style>

<?= $this->endSection() ?>