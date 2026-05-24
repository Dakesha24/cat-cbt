<?= $this->extend('templates/siswa/siswa_template') ?>
<?= $this->section('content') ?>

<?php
/**
 * Variabel dari controller Siswa::detailHasil():
 *  $hasil          — row attempt_ujian + join (termasuk tipe_ujian, jadwal_ujian, dll.)
 *  $isCatMode      — true = CAT (berbasis IRT/theta), false = CBT (benar/salah)
 *  $skor           — skor akhir: kognitif (CAT) atau benar/total×100 (CBT)
 *  $kemampuanKognitif   — ['skor', 'total_benar', 'total_salah'] (hanya dipakai untuk CAT)
 *  $klasifikasiKognitif — ['kategori', 'class', 'bg_class'] (hanya dipakai untuk CAT)
 *  $detailJawaban  — array detail jawaban per soal
 *  $totalSoal      — jumlah soal
 *  $jawabanBenar   — jumlah jawaban benar
 *  $rataRataWaktuFormat — rata-rata waktu per soal
 */
?>

<div class="container py-4">

    <!-- ===== Breadcrumb ===== -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small mb-0">
            <li class="breadcrumb-item">
                <a href="<?= base_url('siswa/hasil') ?>">Riwayat Ujian</a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?= base_url('siswa/hasil/ujian/' . $hasil['peserta_ujian_id']) ?>">Daftar Percobaan</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Detail Percobaan</li>
        </ol>
    </nav>

    <!-- ===== Header ===== -->
    <div class="d-flex justify-content-between align-items-center mb-4 pt-4">
        <h2 class="mb-0">Detail Percobaan</h2>
        <a href="<?= base_url('siswa/hasil/ujian/' . $hasil['peserta_ujian_id']) ?>"
           class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- ===== Ringkasan Ujian ===== -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="text-primary mb-1"><?= esc($hasil['nama_ujian']) ?></h4>
                    <p class="text-muted mb-1"><?= esc($hasil['nama_jenis']) ?></p>
                    <p class="mb-1">
                        <span class="badge bg-primary">Percobaan <?= esc($hasil['nomor_attempt']) ?></span>
                        <?php if (!$isCatMode): ?>
                            <span class="badge bg-secondary ms-1">CBT</span>
                        <?php else: ?>
                            <span class="badge bg-info ms-1">CAT</span>
                        <?php endif; ?>
                    </p>
                    <p class="text-muted mb-3">
                        <small><i class="bi bi-hash"></i> <?= esc($hasil['kode_ujian']) ?></small>
                    </p>
                    <p class="mb-0"><?= esc($hasil['deskripsi']) ?></p>
                </div>
                <div class="col-md-4">
                    <div class="border-start ps-4">
                        <div class="mb-3">
                            <small class="text-muted d-block">Waktu Mulai</small>
                            <strong><?= esc($hasil['waktu_mulai_format']) ?></strong>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block">Waktu Selesai</small>
                            <strong><?= esc($hasil['waktu_selesai_format']) ?></strong>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block">Total Waktu Pengerjaan</small>
                            <strong><?= esc($hasil['durasi_total_format']) ?></strong>
                        </div>
                        <div>
                            <small class="text-muted d-block">Rata-rata per Soal</small>
                            <strong><?= esc($rataRataWaktuFormat) ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== Kartu Statistik ===== -->
    <div class="row mb-4 g-3">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body">
                    <h3 class="mb-1"><?= $totalSoal ?></h3>
                    <small class="text-muted">Total Soal</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body">
                    <h3 class="text-success mb-1"><?= $jawabanBenar ?></h3>
                    <small class="text-muted">Jawaban Benar</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body">
                    <h3 class="text-danger mb-1"><?= $totalSoal - $jawabanBenar ?></h3>
                    <small class="text-muted">Jawaban Salah</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <?php if ($isCatMode): ?>
                <!-- CAT: tampilkan skor kognitif + klasifikasi -->
                <div class="card border-0 shadow-sm text-center h-100">
                    <div class="card-body">
                        <h3 class="<?= $klasifikasiKognitif['class'] ?> mb-1"><?= $kemampuanKognitif['skor'] ?></h3>
                        <small class="text-muted d-block">Skor Kognitif</small>
                        <span class="badge <?= $klasifikasiKognitif['bg_class'] ?> mt-1">
                            <?= $klasifikasiKognitif['kategori'] ?>
                        </span>
                    </div>
                </div>
            <?php else: ?>
                <!-- CBT: tampilkan nilai akhir saja tanpa skor kognitif -->
                <div class="card border-0 shadow-sm text-center h-100">
                    <div class="card-body">
                        <h3 class="text-primary mb-1"><?= number_format((float) $skor, 2) ?></h3>
                        <small class="text-muted">Nilai Akhir</small>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($isCatMode): ?>
        <!-- ===== Interpretasi Kemampuan Kognitif — hanya untuk CAT ===== -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent border-0">
                <h5 class="mb-0">
                    <i class="bi bi-lightbulb"></i> Analisis Kemampuan Kognitif
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <p class="mb-2"><strong>Interpretasi Hasil:</strong></p>
                        <p class="text-muted mb-3">
                            Kemampuan kognitif Anda tergolong
                            <strong class="<?= $klasifikasiKognitif['class'] ?>">
                                <?= $klasifikasiKognitif['kategori'] ?>
                            </strong>
                            dengan skor <strong><?= $kemampuanKognitif['skor'] ?></strong>.
                        </p>
                        <?php if ($kemampuanKognitif['skor'] > 80): ?>
                            <div class="alert alert-success">
                                <i class="bi bi-trophy"></i>
                                <strong>Excellent!</strong> Anda menunjukkan pemahaman yang sangat baik.
                            </div>
                        <?php elseif ($kemampuanKognitif['skor'] > 60): ?>
                            <div class="alert alert-info">
                                <i class="bi bi-star"></i>
                                <strong>Good Job!</strong> Terus tingkatkan pemahaman Anda!
                            </div>
                        <?php elseif ($kemampuanKognitif['skor'] > 40): ?>
                            <div class="alert alert-warning">
                                <i class="bi bi-lightbulb"></i>
                                <strong>Keep Learning!</strong> Masih ada ruang untuk peningkatan.
                            </div>
                        <?php else: ?>
                            <div class="alert alert-danger">
                                <i class="bi bi-book"></i>
                                <strong>Need More Practice!</strong> Disarankan untuk mempelajari kembali materi ini.
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-4">
                        <div class="border-start ps-4">
                            <small class="text-muted d-block mb-2">Detail Perhitungan:</small>
                            <small class="text-muted">Jawaban Benar: <?= $kemampuanKognitif['total_benar'] ?></small><br>
                            <small class="text-muted">Jawaban Salah: <?= $kemampuanKognitif['total_salah'] ?></small><br>
                            <small class="text-muted">Total Soal: <?= $totalSoal ?></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- ===== Tombol Unduh ===== -->
    <div class="mb-4">
        <a href="<?= base_url('siswa/hasil/unduh/' . $hasil['attempt_id']) ?>"
           class="btn btn-primary" target="_blank">
            <i class="bi bi-download"></i> Unduh Laporan Hasil Ujian
        </a>
    </div>

    <!-- ===== Detail Jawaban ===== -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-0">
            <h5 class="mb-0">Detail Jawaban</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Kode Soal</th>
                        <th>Pertanyaan</th>
                        <th>Jawaban Anda</th>
                        <th>Status</th>
                        <th>Waktu Jawab</th>
                        <th>Durasi</th>
                        <th>Pembahasan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($detailJawaban as $i => $jawaban): ?>
                        <tr>
                            <td><?= $jawaban['nomor_soal'] ?></td>
                            <td><small class="text-muted"><?= esc($jawaban['kode_soal']) ?></small></td>
                            <td>
                                <div style="max-width:300px;overflow-x:auto">
                                    <?= $jawaban['pertanyaan'] ?>
                                </div>
                            </td>
                            <td><?= esc($jawaban['jawaban_siswa']) ?></td>
                            <td>
                                <?php if ($jawaban['is_correct']): ?>
                                    <span class="badge bg-success">Benar</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Salah</span>
                                <?php endif; ?>
                            </td>
                            <td><small class="text-muted"><?= esc($jawaban['waktu_menjawab_format']) ?></small></td>
                            <td><small class="fw-bold"><?= esc($jawaban['durasi_pengerjaan_format']) ?></small></td>
                            <td>
                                <?php if (!empty($hasil['tampilkan_pembahasan']) && !empty($jawaban['pembahasan'])): ?>
                                    <button type="button" class="btn btn-sm btn-outline-info"
                                            data-bs-toggle="modal"
                                            data-bs-target="#pembahasanModal<?= $i ?>">
                                        Lihat
                                    </button>

                                    <!-- Modal Pembahasan -->
                                    <div class="modal fade" id="pembahasanModal<?= $i ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">
                                                        Pembahasan Soal #<?= $jawaban['nomor_soal'] ?>
                                                        (<?= esc($jawaban['kode_soal']) ?>)
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <?= $jawaban['pembahasan'] ?>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted small">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<style>
    .bg-orange  { background-color: #fd7e14 !important; }
    .text-orange { color: #fd7e14 !important; }
</style>

<?= $this->endSection() ?>
