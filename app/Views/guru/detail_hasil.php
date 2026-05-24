<?= $this->extend('templates/guru/guru_template') ?>

<?= $this->section('content') ?>

<?php
/**
 * Variabel dari controller Guru::detailHasil():
 *  $hasil          — row peserta_ujian + join (termasuk tipe_ujian, jadwal_id, dll.)
 *  $isCatMode      — true = CAT (berbasis IRT/theta), false = CBT (benar/salah)
 *  $thetaAkhir     — estimasi theta akhir (hanya relevan untuk CAT)
 *  $seAkhir        — standard error akhir (hanya relevan untuk CAT)
 *  $finalScore     — skor akhir: kognitif (CAT) atau benar/total×100 (CBT)
 *  $finalGrade     — nilai akhir (sama dengan finalScore, sudah dibulatkan)
 *  $klasifikasiKognitif — ['kategori', 'class', 'bg_class'] (hanya dipakai untuk CAT)
 *  $kemampuanKognitif   — ['skor', 'total_benar', 'total_salah'] (hanya dipakai untuk CAT)
 *  $detailJawaban  — array detail jawaban per soal
 *  $totalSoal      — jumlah soal
 *  $jawabanBenar   — jumlah jawaban benar
 *  $rataRataWaktuFormat — rata-rata waktu per soal
 *  $backUrl        — URL tombol Kembali (CAT → daftar siswa, CBT → daftar percobaan)
 */
?>

<div class="container-fluid py-4">

    <!-- ===== Header ===== -->
    <div class="d-flex justify-content-between align-items-start mb-4 pt-4">
        <div>
            <h2 class="mb-1">Detail Hasil Ujian</h2>
            <p class="text-muted mb-0"><?= esc($hasil['nama_ujian']) ?> &mdash; <?= esc($hasil['nama_jenis']) ?></p>
            <p class="text-muted mb-0">
                Kode Ujian: <code><?= esc($hasil['kode_ujian'] ?? '-') ?></code>
                <?php if (!$isCatMode): ?>
                    &nbsp;<span class="badge bg-secondary">CBT</span>
                <?php else: ?>
                    &nbsp;<span class="badge bg-primary">CAT</span>
                <?php endif; ?>
            </p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <div class="btn-group">
                <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="bi bi-download"></i> Download
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a class="dropdown-item"
                           href="<?= base_url('guru/hasil-ujian/download-excel-html/' . $hasil['peserta_ujian_id']) . (!empty($hasil['attempt_id']) ? '?attempt_id=' . $hasil['attempt_id'] : '') ?>">
                            Excel
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item"
                           href="<?= base_url('guru/hasil-ujian/download-pdf-html/' . $hasil['peserta_ujian_id']) . (!empty($hasil['attempt_id']) ? '?attempt_id=' . $hasil['attempt_id'] : '') ?>">
                            PDF
                        </a>
                    </li>
                </ul>
            </div>
            <a href="<?= esc($backUrl ?? base_url('guru/hasil-ujian/siswa/' . $hasil['jadwal_id'])) ?>"
               class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <!-- ===== Info Siswa & Waktu ===== -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless mb-0 small">
                        <tr>
                            <td style="width:160px" class="text-muted">Nama Siswa</td>
                            <td>: <strong><?= esc($hasil['nama_lengkap']) ?></strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Nomor Peserta</td>
                            <td>: <?= esc($hasil['nomor_peserta']) ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Kelas</td>
                            <td>: <?= esc($hasil['nama_kelas']) ?></td>
                        </tr>
                        <?php if (!empty($hasil['nomor_attempt'])): ?>
                            <tr>
                                <td class="text-muted">Percobaan</td>
                                <td>: <span class="badge bg-info">ke-<?= (int) $hasil['nomor_attempt'] ?></span></td>
                            </tr>
                        <?php endif; ?>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless mb-0 small">
                        <tr>
                            <td style="width:150px" class="text-muted">Waktu Mulai</td>
                            <td>: <?= esc($hasil['waktu_mulai_format'] ?? '-') ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Waktu Selesai</td>
                            <td>: <?= esc($hasil['waktu_selesai_format'] ?? '-') ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Total Durasi</td>
                            <td>: <strong><?= esc($hasil['durasi_total_format'] ?? '-') ?></strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Rata-rata/Soal</td>
                            <td>: <?= esc($rataRataWaktuFormat) ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== Ringkasan Hasil ===== -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent">
            <h5 class="card-title mb-0">
                <?= $isCatMode ? 'Ringkasan Hasil CAT' : 'Ringkasan Hasil CBT' ?>
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <!-- Kolom kiri: statistik soal -->
                <div class="col-md-6">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td width="180">Total Soal</td>
                            <td>: <strong><?= $totalSoal ?></strong></td>
                        </tr>
                        <tr>
                            <td>Jawaban Benar</td>
                            <td>: <strong class="text-success"><?= $jawabanBenar ?></strong></td>
                        </tr>
                        <tr>
                            <td>Jawaban Salah</td>
                            <td>: <strong class="text-danger"><?= $totalSoal - $jawabanBenar ?></strong></td>
                        </tr>
                        <?php if ($isCatMode): ?>
                            <!-- CAT: parameter IRT -->
                            <tr>
                                <td>Theta Akhir (θ)</td>
                                <td>: <strong><?= number_format((float) $thetaAkhir, 3) ?></strong></td>
                            </tr>
                            <tr>
                                <td>Standard Error (SE)</td>
                                <td>: <strong><?= number_format((float) $seAkhir, 3) ?></strong></td>
                            </tr>
                        <?php endif; ?>
                    </table>
                </div>
                <!-- Kolom kanan: skor akhir -->
                <div class="col-md-6">
                    <?php if ($isCatMode): ?>
                        <!-- CAT: skor kognitif + klasifikasi -->
                        <div class="text-center">
                            <h2 class="<?= $klasifikasiKognitif['class'] ?> mb-1">
                                <?= $kemampuanKognitif['skor'] ?>
                            </h2>
                            <span class="badge <?= $klasifikasiKognitif['bg_class'] ?> mb-2">
                                <?= $klasifikasiKognitif['kategori'] ?>
                            </span>
                            <div class="text-muted small">Skor Kognitif CAT</div>
                        </div>
                    <?php else: ?>
                        <!-- CBT: nilai benar/salah saja, tanpa skor kognitif -->
                        <div class="d-flex flex-column align-items-center justify-content-center h-100">
                            <div class="display-4 fw-bold text-primary mb-1">
                                <?= number_format((float) $finalScore, 2) ?>
                            </div>
                            <div class="text-muted small">
                                Nilai Akhir (<?= $jawabanBenar ?>/<?= $totalSoal ?> benar)
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($isCatMode): ?>
                <!-- CAT: tombol info perhitungan IRT -->
                <div class="collapse mt-3" id="cognitiveHelp">
                    <div class="alert alert-light border mb-0">
                        <p class="mb-1 small"><strong>Rumus:</strong> Skor = 50 + (16.67 × θ)</p>
                        <p class="mb-0 small text-muted">Skor kognitif dikonversi dari estimasi theta IRT ke skala 0–100.</p>
                    </div>
                </div>
                <div class="mt-2">
                    <button class="btn btn-sm btn-outline-secondary" type="button"
                            data-bs-toggle="collapse" data-bs-target="#cognitiveHelp">
                        <i class="bi bi-info-circle me-1"></i>Info Perhitungan CAT
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- ===== Detail Jawaban ===== -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Detail Jawaban</h5>
            <button class="btn btn-sm btn-outline-secondary" type="button"
                    data-bs-toggle="collapse" data-bs-target="#infoKolom">
                <i class="bi bi-info-circle"></i> Info Kolom
            </button>
        </div>

        <div class="collapse" id="infoKolom">
            <div class="card-body bg-light py-2">
                <?php if ($isCatMode): ?>
                    <!-- CAT: penjelasan kolom IRT -->
                    <ul class="small mb-0">
                        <li><strong>Pi</strong>: Probabilitas menjawab benar (model 3PL IRT)</li>
                        <li><strong>Qi</strong>: 1 &minus; Pi (probabilitas menjawab salah)</li>
                        <li><strong>Ii</strong>: Fungsi informasi butir soal</li>
                        <li><strong>SE</strong>: Standard Error estimasi theta</li>
                        <li><strong>ΔSE</strong>: Perubahan Standard Error antar soal</li>
                        <li><strong>θ</strong>: Estimasi kemampuan siswa setelah menjawab soal ini</li>
                    </ul>
                <?php else: ?>
                    <!-- CBT: kolom sederhana -->
                    <ul class="small mb-0">
                        <li><strong>Status</strong>: Benar atau Salah berdasarkan kunci jawaban</li>
                        <li><strong>Waktu Jawab</strong>: Waktu saat soal dijawab</li>
                        <li><strong>Durasi</strong>: Lama mengerjakan soal tersebut</li>
                    </ul>
                <?php endif; ?>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Kode Soal</th>
                        <th>ID Soal</th>
                        <th>Tingkat Kesulitan</th>
                        <th>Jawaban</th>
                        <th>Status</th>
                        <th>Waktu Jawab</th>
                        <th>Durasi</th>
                        <?php if ($isCatMode): ?>
                            <!-- CAT: kolom parameter IRT -->
                            <th>Pi</th>
                            <th>Qi</th>
                            <th>Ii</th>
                            <th>SE</th>
                            <th>ΔSE</th>
                            <th>θ</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($detailJawaban as $jawaban): ?>
                        <tr>
                            <td><?= $jawaban['nomor_soal'] ?></td>
                            <td class="fw-bold text-primary small"><?= esc($jawaban['kode_soal']) ?></td>
                            <td class="text-muted small"><?= $jawaban['soal_id'] ?></td>
                            <td><?= number_format((float) $jawaban['tingkat_kesulitan'], 3) ?></td>
                            <td><?= esc($jawaban['jawaban_siswa']) ?></td>
                            <td>
                                <?php if ($jawaban['is_correct']): ?>
                                    <span class="badge bg-success">Benar</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Salah</span>
                                <?php endif; ?>
                            </td>
                            <td><small class="text-muted"><?= esc($jawaban['waktu_menjawab_format']) ?></small></td>
                            <td><small class="fw-bold text-info"><?= esc($jawaban['durasi_pengerjaan_format']) ?></small></td>
                            <?php if ($isCatMode): ?>
                                <!-- CAT: nilai IRT per soal -->
                                <td><?= isset($jawaban['pi_saat_ini'])       ? number_format((float) $jawaban['pi_saat_ini'], 3)           : '-' ?></td>
                                <td><?= isset($jawaban['qi_saat_ini'])       ? number_format((float) $jawaban['qi_saat_ini'], 3)           : '-' ?></td>
                                <td><?= isset($jawaban['ii_saat_ini'])       ? number_format((float) $jawaban['ii_saat_ini'], 3)           : '-' ?></td>
                                <td><?= isset($jawaban['se_saat_ini'])       ? number_format((float) $jawaban['se_saat_ini'], 3)           : '-' ?></td>
                                <td><?= isset($jawaban['delta_se_saat_ini']) ? number_format(abs((float) $jawaban['delta_se_saat_ini']), 3) : '-' ?></td>
                                <td><?= isset($jawaban['theta_saat_ini'])    ? number_format((float) $jawaban['theta_saat_ini'], 3)        : '-' ?></td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if ($isCatMode): ?>
        <!-- ===== Grafik CAT (theta & SE) — hanya untuk CAT ===== -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent">
                        <h5 class="card-title mb-0">Grafik Theta (θ)</h5>
                    </div>
                    <div class="card-body"><canvas id="thetaChart" height="300"></canvas></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent">
                        <h5 class="card-title mb-0">Grafik Standard Error (SE)</h5>
                    </div>
                    <div class="card-body"><canvas id="seChart" height="300"></canvas></div>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const thetaData = <?= json_encode(array_map(static fn($i) => (float) ($i['theta_saat_ini'] ?? 0), $detailJawaban)) ?>;
            const seData    = <?= json_encode(array_map(static fn($i) => (float) ($i['se_saat_ini'] ?? 0), $detailJawaban)) ?>;
            const labels    = <?= json_encode(array_map(static fn($i) => 'Soal ' . $i['nomor_soal'], $detailJawaban)) ?>;

            new Chart(document.getElementById('thetaChart'), {
                type: 'line',
                data: { labels, datasets: [{ label: 'Theta (θ)', data: thetaData,
                    borderColor: '#4e73df', backgroundColor: 'rgba(78,115,223,0.1)',
                    tension: 0.2, fill: true }] },
                options: { responsive: true, plugins: { legend: { display: false } } }
            });

            new Chart(document.getElementById('seChart'), {
                type: 'line',
                data: { labels, datasets: [{ label: 'SE', data: seData,
                    borderColor: '#1cc88a', backgroundColor: 'rgba(28,200,138,0.1)',
                    tension: 0.2, fill: true }] },
                options: { responsive: true, plugins: { legend: { display: false } } }
            });
        </script>
    <?php endif; ?>

</div>
<?= $this->endSection() ?>
