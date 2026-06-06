<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Percobaan - <?= esc($hasil['nama_ujian']) ?> - Percobaan <?= esc($hasil['nomor_attempt']) ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background-color: #f8f9fa;
        }

        .back-button { margin-bottom: 20px; }

        .report-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,.1);
            padding: 30px;
            margin-bottom: 30px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #dee2e6;
        }

        .title    { font-size: 24px; font-weight: bold; margin-bottom: 10px; }
        .subtitle { font-size: 18px; color: #6c757d; }
        .kode-ujian { font-size: 14px; color: #6c757d; margin-top: 5px; }

        .info-box   { margin-bottom: 25px; }
        .info-label { font-weight: bold; color: #495057; }
        .info-value { margin-bottom: 10px; }
        .statistics { margin-bottom: 30px; }

        .correct  { color: #28a745 !important; font-weight: bold; }
        .incorrect { color: #dc3545 !important; font-weight: bold; }
        td.correct   { color: #28a745 !important; font-weight: bold; }
        td.incorrect { color: #dc3545 !important; font-weight: bold; }
        .score { color: #007bff; font-weight: bold; }

        .kognitif-sangat-tinggi { color: #28a745 !important; font-weight: bold; }
        .kognitif-tinggi        { color: #17a2b8 !important; font-weight: bold; }
        .kognitif-sedang        { color: #ffc107 !important; font-weight: bold; }
        .kognitif-rendah        { color: #fd7e14 !important; font-weight: bold; }
        .kognitif-sangat-rendah { color: #dc3545 !important; font-weight: bold; }

        .kognitif-box {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .param-table td { padding: 3px 8px 3px 0; font-size: 14px; }
        .param-table td:first-child { color: #6c757d; min-width: 160px; }
        .param-table td:last-child { font-weight: bold; }

        .badge-normal       { background:#198754; color:#fff; padding:2px 7px; border-radius:4px; font-size:12px; }
        .badge-lucky        { background:#ffc107; color:#212529; padding:2px 7px; border-radius:4px; font-size:12px; }
        .badge-ceroboh      { background:#dc3545; color:#fff; padding:2px 7px; border-radius:4px; font-size:12px; }
        .badge-mudah        { background:#198754; color:#fff; padding:2px 7px; border-radius:4px; font-size:12px; }
        .badge-sedang       { background:#ffc107; color:#212529; padding:2px 7px; border-radius:4px; font-size:12px; }
        .badge-sulit        { background:#dc3545; color:#fff; padding:2px 7px; border-radius:4px; font-size:12px; }

        .pembahasan-container {
            margin-top: 30px;
            border-top: 1px solid #dee2e6;
            padding-top: 20px;
        }
        .pembahasan-item {
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px dashed #dee2e6;
        }

        .print-button {
            position: fixed;
            bottom: 30px;
            right: 30px;
            display: block;
        }

        @media print {
            body { padding: 0; background-color: white; }
            .report-container { box-shadow: none; padding: 0; }
            .back-button, .print-button { display: none !important; }
            .header { margin-bottom: 20px; padding-bottom: 15px; }
            .title { font-size: 18px; }
            .subtitle { font-size: 16px; }
            table { font-size: 12px; }
            .page-break { page-break-after: always; }
            @page { margin: 2cm; }
        }
    </style>
</head>

<body>

<?php $isCbt = ($hasil['tipe_ujian'] ?? 'CAT') === 'CBT'; ?>

    <!-- Tombol kembali -->
    <div class="back-button">
        <a href="<?= base_url('siswa/hasil') ?>" class="btn btn-outline-secondary">
            &larr; Kembali
        </a>
    </div>

    <div class="report-container">

        <!-- ===== Header ===== -->
        <div class="header">
            <div class="title">LAPORAN HASIL UJIAN</div>
            <div class="subtitle"><?= esc($hasil['nama_ujian']) ?></div>
            <div class="kode-ujian">
                Kode Ujian: <?= esc($hasil['kode_ujian']) ?>
                &nbsp;|&nbsp;
                <?= $isCbt ? 'CBT (EAP IRT 3PL)' : 'CAT (IRT 1PL)' ?>
            </div>
        </div>

        <!-- ===== Info Siswa & Waktu ===== -->
        <div class="row">
            <div class="col-md-6">
                <div class="info-box">
                    <div class="info-label">Nama Siswa:</div>
                    <div class="info-value"><?= esc($siswa['nama_lengkap']) ?></div>

                    <div class="info-label">NIS:</div>
                    <div class="info-value"><?= esc($siswa['nomor_peserta']) ?></div>

                    <div class="info-label">Mata Pelajaran:</div>
                    <div class="info-value"><?= esc($hasil['nama_jenis']) ?></div>

                    <div class="info-label">Percobaan ke-:</div>
                    <div class="info-value"><?= esc($hasil['nomor_attempt']) ?></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="info-box">
                    <div class="info-label">Waktu Mulai:</div>
                    <div class="info-value"><?= $hasil['waktu_mulai_format'] ?></div>

                    <div class="info-label">Waktu Selesai:</div>
                    <div class="info-value"><?= $hasil['waktu_selesai_format'] ?></div>

                    <div class="info-label">Total Waktu Pengerjaan:</div>
                    <div class="info-value"><?= $hasil['durasi_total_format'] ?></div>

                    <div class="info-label">Rata-rata per Soal:</div>
                    <div class="info-value"><?= $rataRataWaktuFormat ?></div>
                </div>
            </div>
        </div>

        <!-- ===== Analisis Hasil ===== -->
        <div class="kognitif-box">

            <?php if ($isCbt): ?>
                <!-- CBT: Analisis Hasil EAP -->
                <h4 class="mb-3">Analisis Hasil EAP</h4>
                <div class="row">
                    <div class="col-md-5">
                        <div class="info-label">Nilai EAP:</div>
                        <div class="info-value">
                            <span class="<?= esc($klasifikasiKognitif['class'], 'attr') ?>">
                                <?= number_format((float) $skor, 2) ?> &mdash; <?= $klasifikasiKognitif['kategori'] ?>
                            </span>
                        </div>

                        <div class="info-label mt-2">Interpretasi:</div>
                        <div class="info-value">
                            <?php if ($skor > 80): ?>
                                Penguasaan materi sangat baik. Siswa menunjukkan pemahaman yang excellent dan mendalam.
                            <?php elseif ($skor > 60): ?>
                                Penguasaan materi baik. Siswa memiliki pemahaman yang solid terhadap materi.
                            <?php elseif ($skor > 40): ?>
                                Penguasaan materi cukup. Masih ada beberapa bagian yang perlu diperkuat.
                            <?php elseif ($skor > 20): ?>
                                Penguasaan materi rendah. Disarankan review ulang sebagian besar materi.
                            <?php else: ?>
                                Penguasaan materi sangat rendah. Sangat disarankan mempelajari seluruh materi kembali.
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-label">Parameter IRT 3PL:</div>
                        <table class="param-table mt-1">
                            <tr>
                                <td>θ_EAP (Theta Final)</td>
                                <td><?= number_format((float) ($hasil['theta_akhir'] ?? 0), 4) ?></td>
                            </tr>
                            <tr>
                                <td>SEM (Std. Error)</td>
                                <td><?= number_format((float) ($hasil['sem_akhir'] ?? 0), 4) ?></td>
                            </tr>
                            <tr>
                                <td>Nilai EAP (NA)</td>
                                <td><?= number_format((float) $skor, 2) ?></td>
                            </tr>
                        </table>
                        <small class="text-muted d-block mt-2">Rumus: NA = 50 + (10 × θ_EAP)</small>
                    </div>
                    <div class="col-md-3">
                        <div class="info-label">Statistik:</div>
                        <small>
                            &bull; Benar: <?= $kemampuanKognitif['total_benar'] ?><br>
                            &bull; Salah: <?= $kemampuanKognitif['total_salah'] ?><br>
                            &bull; Total: <?= $totalSoal ?>
                        </small>
                        <?php
                            $lgCount  = count(array_filter($detailJawaban, fn($j) => ($j['keterangan_residu'] ?? '') === 'Lucky Guess'));
                            $cbCount  = count(array_filter($detailJawaban, fn($j) => ($j['keterangan_residu'] ?? '') === 'Ceroboh'));
                        ?>
                        <?php if ($lgCount > 0 || $cbCount > 0): ?>
                            <small class="d-block mt-2">
                                &bull; Lucky Guess: <?= $lgCount ?><br>
                                &bull; Ceroboh: <?= $cbCount ?>
                            </small>
                        <?php endif; ?>
                    </div>
                </div>

            <?php else: ?>
                <!-- CAT: Analisis Kemampuan Kognitif -->
                <h4 class="mb-3">Analisis Kemampuan Kognitif</h4>
                <div class="row">
                    <div class="col-md-8">
                        <div class="info-label">Skor Kemampuan Kognitif:</div>
                        <div class="info-value">
                            <span class="<?= esc($klasifikasiKognitif['class'], 'attr') ?>">
                                <?= $kemampuanKognitif['skor'] ?> &mdash; <?= $klasifikasiKognitif['kategori'] ?>
                            </span>
                        </div>

                        <div class="info-label mt-3">Interpretasi:</div>
                        <div class="info-value">
                            <?php if ($kemampuanKognitif['skor'] >= 75): ?>
                                Kemampuan kognitif sangat baik. Siswa menunjukkan pemahaman yang excellent dan mendalam.
                            <?php elseif ($kemampuanKognitif['skor'] >= 58): ?>
                                Kemampuan kognitif baik. Siswa memiliki pemahaman yang solid terhadap materi.
                            <?php elseif ($kemampuanKognitif['skor'] >= 42): ?>
                                Kemampuan kognitif cukup. Masih ada beberapa bagian materi yang perlu diperkuat.
                            <?php elseif ($kemampuanKognitif['skor'] >= 25): ?>
                                Kemampuan kognitif rendah. Disarankan review ulang sebagian besar materi.
                            <?php else: ?>
                                Kemampuan kognitif sangat rendah. Sangat disarankan mempelajari seluruh materi kembali.
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-label">Detail Perhitungan:</div>
                        <small>
                            &bull; Jawaban Benar: <?= $kemampuanKognitif['total_benar'] ?><br>
                            &bull; Jawaban Salah: <?= $kemampuanKognitif['total_salah'] ?><br>
                            &bull; Total Soal: <?= $totalSoal ?>
                        </small>
                    </div>
                </div>
            <?php endif; ?>

        </div>

        <!-- ===== Statistik Ringkas ===== -->
        <div class="statistics">
            <h4 class="mb-3">Statistik Hasil</h4>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Total Soal</th>
                            <th>Jawaban Benar</th>
                            <th>Jawaban Salah</th>
                            <?php if ($isCbt): ?>
                                <th>θ_EAP</th>
                                <th>SEM</th>
                                <th>Nilai EAP</th>
                                <th>Kategori</th>
                            <?php else: ?>
                                <th>Skor Kognitif</th>
                                <th>Kategori</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= $totalSoal ?></td>
                            <td class="correct"><?= $jawabanBenar ?></td>
                            <td class="incorrect"><?= $totalSoal - $jawabanBenar ?></td>
                            <?php if ($isCbt): ?>
                                <td><?= number_format((float) ($hasil['theta_akhir'] ?? 0), 4) ?></td>
                                <td><?= number_format((float) ($hasil['sem_akhir'] ?? 0), 4) ?></td>
                                <td class="score"><?= number_format((float) $skor, 2) ?></td>
                                <td class="<?= esc($klasifikasiKognitif['class'], 'attr') ?>"><?= $klasifikasiKognitif['kategori'] ?></td>
                            <?php else: ?>
                                <td class="score"><?= $kemampuanKognitif['skor'] ?></td>
                                <td class="<?= esc($klasifikasiKognitif['class'], 'attr') ?>"><?= $klasifikasiKognitif['kategori'] ?></td>
                            <?php endif; ?>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ===== Detail Jawaban ===== -->
        <div class="detail-jawaban">
            <h4 class="mb-3">Detail Jawaban</h4>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th width="4%">No</th>
                            <th width="9%">Kode Soal</th>
                            <th>Pertanyaan</th>
                            <th width="8%">Jawaban</th>
                            <th width="7%">Status</th>
                            <?php if ($isCbt): ?>
                                <th width="7%">Kategori</th>
                                <th width="6%">P(θ)</th>
                                <th width="6%">z</th>
                                <th width="9%">Keterangan</th>
                            <?php endif; ?>
                            <th width="10%">Waktu Jawab</th>
                            <th width="8%">Durasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($detailJawaban as $jawaban): ?>
                            <tr>
                                <td><?= $jawaban['nomor_soal'] ?></td>
                                <td><small><?= esc($jawaban['kode_soal']) ?></small></td>
                                <td>
                                    <div style="max-width:280px;overflow-x:auto">
                                        <?= $jawaban['pertanyaan'] ?>
                                    </div>
                                </td>
                                <td><?= esc($jawaban['jawaban_siswa']) ?></td>
                                <td class="<?= $jawaban['is_correct'] ? 'correct' : 'incorrect' ?>">
                                    <?= $jawaban['is_correct'] ? 'Benar' : 'Salah' ?>
                                </td>
                                <?php if ($isCbt): ?>
                                    <!-- Kategori soal (berdasarkan parameter b) -->
                                    <td>
                                        <?php $katSoal = $jawaban['kategori_soal'] ?? null; ?>
                                        <?php if ($katSoal): ?>
                                            <?php
                                                $katBadge = match($katSoal) {
                                                    'Sulit'  => 'badge-sulit',
                                                    'Sedang' => 'badge-sedang',
                                                    default  => 'badge-mudah',
                                                };
                                            ?>
                                            <span class="<?= $katBadge ?>"><?= esc($katSoal) ?></span>
                                        <?php else: ?>
                                            &mdash;
                                        <?php endif; ?>
                                    </td>
                                    <!-- P(θ): probabilitas benar pada θ_EAP -->
                                    <td><?= isset($jawaban['p_residu']) ? number_format((float) $jawaban['p_residu'], 3) : '&mdash;' ?></td>
                                    <!-- z: standardized residual -->
                                    <td><?= isset($jawaban['z_score']) ? number_format((float) $jawaban['z_score'], 3) : '&mdash;' ?></td>
                                    <!-- Keterangan residu -->
                                    <td>
                                        <?php $ket = $jawaban['keterangan_residu'] ?? null; ?>
                                        <?php if ($ket): ?>
                                            <?php
                                                $ketBadge = match($ket) {
                                                    'Lucky Guess' => 'badge-lucky',
                                                    'Ceroboh'     => 'badge-ceroboh',
                                                    default       => 'badge-normal',
                                                };
                                            ?>
                                            <span class="<?= $ketBadge ?>"><?= esc($ket) ?></span>
                                        <?php else: ?>
                                            &mdash;
                                        <?php endif; ?>
                                    </td>
                                <?php endif; ?>
                                <td><small><?= esc($jawaban['waktu_menjawab_format']) ?></small></td>
                                <td><strong><?= esc($jawaban['durasi_pengerjaan_format']) ?></strong></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($isCbt): ?>
                <small class="text-muted">
                    Kategori: <span class="badge-mudah">Mudah</span> (b &lt; &minus;1) &nbsp;
                    <span class="badge-sedang">Sedang</span> (&minus;1 &le; b &lt; 1) &nbsp;
                    <span class="badge-sulit">Sulit</span> (b &ge; 1) &mdash;
                    Keterangan: <span class="badge-lucky">Lucky Guess</span> z &gt; 2 pada soal Sulit &nbsp;
                    <span class="badge-ceroboh">Ceroboh</span> z &lt; &minus;2 pada soal Mudah
                </small>
            <?php endif; ?>
        </div>

        <div class="page-break"></div>

        <!-- ===== Pembahasan ===== -->
        <?php $adaPembahasan = array_filter($detailJawaban, fn($j) => !empty($j['pembahasan'])); ?>
        <?php if (!empty($adaPembahasan)): ?>
            <div class="pembahasan-container">
                <h4 class="mb-4">Pembahasan Soal</h4>
                <?php foreach ($detailJawaban as $jawaban): ?>
                    <?php if (!empty($jawaban['pembahasan'])): ?>
                        <div class="pembahasan-item">
                            <div class="fw-bold mb-2">
                                Soal #<?= $jawaban['nomor_soal'] ?> (<?= esc($jawaban['kode_soal']) ?>):
                            </div>
                            <div class="mb-3"><?= $jawaban['pertanyaan'] ?></div>
                            <div class="fw-bold mb-2">Pembahasan:</div>
                            <div><?= $jawaban['pembahasan'] ?></div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="text-end mt-4 text-muted small">
            Dicetak pada: <?= date('d M Y H:i:s') ?>
        </div>
    </div>

    <!-- Tombol Print -->
    <button class="btn btn-primary btn-lg print-button" onclick="window.print()">
        &#128438; Cetak Laporan
    </button>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        if (new URLSearchParams(window.location.search).get('autoprint') === 'true') {
            window.onload = function () {
                setTimeout(function () { window.print(); }, 1000);
            };
        }
    </script>
</body>

</html>
