<?= $this->extend('templates/siswa/siswa_template') ?>

<?= $this->section('content') ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center p-5">
                    <div class="display-1 text-success mb-4">
                        <i class="bi bi-check-circle"></i>
                    </div>

                    <h2 class="mb-4">Ujian Selesai!</h2>

                    <h4 class="text-muted mb-4"><?= esc($ujian['nama_ujian']) ?></h4>

                    <div class="row mb-4">
                        <div class="col-6">
                            <div class="border-end">
                                <h5>Total Soal</h5>
                                <p class="h3"><?= $total_soal ?></p>
                            </div>
                        </div>
                        <div class="col-6">
                            <h5><?= (($ujian['tipe_ujian'] ?? 'CAT') === 'CBT') ? 'Nilai EAP' : 'Nilai Akhir' ?></h5>
                            <?php if (($ujian['tipe_ujian'] ?? 'CAT') === 'CBT'): ?>
                                <p class="h3"><?= number_format((float) ($nilai_akhir ?? 0), 2) ?></p>
                                <?php if (($theta_akhir ?? null) !== null || ($sem_akhir ?? null) !== null): ?>
                                    <div class="small text-muted">
                                        TF <?= number_format((float) ($theta_akhir ?? 0), 4) ?>
                                        | SEM <?= number_format((float) ($sem_akhir ?? 0), 4) ?>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <p class="h3"><?= number_format(max(0, min(100, 50 + (16.67 * (float) ($nilai_akhir ?? 0)))), 2) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="text-muted mb-4">
                        <p>Waktu selesai: <?= date('d M Y H:i', strtotime($peserta['waktu_selesai'])) ?></p>
                    </div>

                    <div>
                        <a href="<?= base_url('siswa/ujian') ?>" class="btn btn-primary btn-lg px-5">
                            Kembali ke Daftar Ujian
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
