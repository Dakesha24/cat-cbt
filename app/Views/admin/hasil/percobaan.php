<?= $this->extend('templates/admin/admin_template') ?>

<?= $this->section('title') ?>Daftar Percobaan<?= $this->endSection() ?>

<?= $this->section('content') ?>
<br><br><br>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1">Daftar Percobaan</h3>
            <p class="text-muted mb-0"><?= esc($peserta['nama_lengkap']) ?> - <?= esc($peserta['nama_ujian']) ?></p>
            <p class="text-muted mb-0">Kode Ujian: <code><?= esc($peserta['kode_ujian']) ?></code></p>
        </div>
        <a href="<?= base_url('admin/hasil-ujian/siswa/' . $peserta['jadwal_id']) ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Kembali
        </a>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 small">
                <div class="col-md-3"><strong>Siswa:</strong> <?= esc($peserta['nama_lengkap']) ?></div>
                <div class="col-md-3"><strong>No Peserta:</strong> <?= esc($peserta['nomor_peserta']) ?></div>
                <div class="col-md-3"><strong>Kelas:</strong> <?= esc($peserta['nama_kelas']) ?></div>
                <div class="col-md-3"><strong>Total Percobaan:</strong> <?= count($attempts) ?></div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent">
            <h5 class="mb-0">Percobaan Tersimpan</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Percobaan</th>
                            <th>Waktu Mulai</th>
                            <th>Waktu Selesai</th>
                            <th>Durasi</th>
                            <th>Hasil</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($attempts as $attempt): ?>
                            <tr>
                                <td>
                                    <strong>Percobaan <?= (int) $attempt['nomor_attempt'] ?></strong><br>
                                    <small class="text-muted"><?= $attempt['jawaban_benar'] ?>/<?= $attempt['total_soal'] ?> benar</small>
                                </td>
                                <td><?= esc($attempt['waktu_mulai_format']) ?></td>
                                <td><?= esc($attempt['waktu_selesai_format']) ?></td>
                                <td><?= esc($attempt['durasi_format']) ?></td>
                                <td>
                                    <?php if (!empty($attempt['is_cat_mode'])): ?>
                                        <div><strong>Skor:</strong> <?= number_format((float) $attempt['skor'], 1) ?></div>
                                        <small class="text-muted">Theta <?= number_format((float) $attempt['theta_akhir'], 3) ?> | SE <?= number_format((float) $attempt['se_akhir'], 3) ?></small>
                                    <?php else: ?>
                                        <div><strong>Nilai:</strong> <?= number_format((float) $attempt['nilai'], 2) ?></div>
                                        <small class="text-muted">Benar <?= $attempt['jawaban_benar'] ?>/<?= $attempt['total_soal'] ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= base_url('admin/hasil-ujian/detail/' . $peserta['peserta_ujian_id']) . '?attempt_id=' . $attempt['attempt_id'] ?>" class="btn btn-info">
                                            Detail
                                        </a>
                                        <a href="<?= base_url('admin/hasil-ujian/download-excel/' . $peserta['peserta_ujian_id']) . '?attempt_id=' . $attempt['attempt_id'] ?>" class="btn btn-success">
                                            Excel
                                        </a>
                                        <a href="<?= base_url('admin/hasil-ujian/download-pdf/' . $peserta['peserta_ujian_id']) . '?attempt_id=' . $attempt['attempt_id'] ?>" class="btn btn-danger" target="_blank">
                                            PDF
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
