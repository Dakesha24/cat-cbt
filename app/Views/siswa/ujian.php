<?= $this->extend('templates/siswa/siswa_template') ?>

<?= $this->section('content') ?>
<div class="container py-4">
    <div class="row py-5">
        <div class="col-12">
            <h2 class="mb-4">Daftar Ujian Yang Tersedia</h2>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger">
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <?php if (empty($jadwalUjian)): ?>
                <div class="alert alert-info">
                    Belum ada jadwal ujian yang tersedia untuk kelas Anda.
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($jadwalUjian as $jadwal): ?>
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <h4 class="card-title text-primary mb-0"><?= esc($jadwal['nama_ujian']) ?></h4>
                                            <!-- TAMBAHAN: Tampilkan kode ujian -->
                                            <small class="text-muted"><?= esc($jadwal['kode_ujian']) ?></small>
                                        </div>
                                        <?php
                                        $statusClass = '';
                                        $statusText = '';

                                        if ($jadwal['status_peserta'] == 'sedang_mengerjakan') {
                                            $statusClass = 'bg-warning';
                                            $statusText = 'Sedang Mengerjakan';
                                        } elseif ($jadwal['status_peserta'] == 'selesai') {
                                            $statusClass = 'bg-success';
                                            $statusText = 'Selesai';
                                        } elseif ($jadwal['status_peserta'] == 'belum_mulai') {
                                            $statusClass = 'bg-info';
                                            $statusText = 'Belum Mulai';
                                        }

                                        if ($statusText): ?>
                                            <span class="badge <?= $statusClass ?>"><?= $statusText ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <p class="card-text"><?= esc($jadwal['deskripsi']) ?></p>

                                    <div class="mb-3">
                                        <small class="text-muted d-block mb-1">
                                            <i class="bi bi-calendar-event"></i>
                                            Mulai: <?= date('d M Y H:i', strtotime($jadwal['tanggal_mulai'])) ?>
                                        </small>
                                        <small class="text-muted d-block mb-1">
                                            <i class="bi bi-calendar-x"></i>
                                            Selesai: <?= date('d M Y H:i', strtotime($jadwal['tanggal_selesai'])) ?>
                                        </small>
                                        <small class="text-muted d-block">
                                            <i class="bi bi-clock"></i>
                                            Durasi: <?= $jadwal['durasi'] ?>
                                        </small>
                                    </div>

                                    <?php if ($jadwal['status'] == 'sedang_berlangsung'): ?>
                                        <?php if ($jadwal['status_peserta'] == 'sedang_mengerjakan'): ?>
                                            <a href="<?= base_url('siswa/ujian/soal/' . $jadwal['jadwal_id']) ?>"
                                                class="btn btn-warning w-100">
                                                Lanjutkan Ujian
                                            </a>
                                        <?php elseif ($jadwal['status_peserta'] == 'selesai'): ?>
                                            <button class="btn btn-success w-100" disabled>
                                                Ujian Selesai
                                            </button>
                                        <?php else: ?>
                                            <button type="button" class="btn btn-primary w-100"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalKodeAkses"
                                                data-jadwal-id="<?= $jadwal['jadwal_id'] ?>">
                                                Mulai Ujian
                                            </button>
                                        <?php endif; ?>
                                    <?php elseif ($jadwal['status'] == 'belum_mulai'): ?>
                                        <button class="btn btn-secondary w-100" disabled>
                                            Belum Dimulai
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

<!-- Modal Kode Akses -->
<div class="modal fade" id="modalKodeAkses" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Masukkan Kode Akses Ujian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('siswa/ujian/mulai') ?>" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="jadwal_id" id="jadwalId">
                    <div class="form-group">
                        <label for="kodeAkses" class="form-label">Kode Akses:</label>
                        <input type="text" class="form-control form-control-lg text-center"
                            id="kodeAkses" name="kode_akses"
                            maxlength="20" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Mulai Ujian</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('modalKodeAkses').addEventListener('show.bs.modal', function(event) {
        var button = event.relatedTarget;
        var jadwalId = button.getAttribute('data-jadwal-id');
        document.getElementById('jadwalId').value = jadwalId;
    });
</script>
<?= $this->endSection() ?>