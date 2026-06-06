<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Ujian - <?= esc($hasil['nama_lengkap']) ?></title>
    <?php if (!empty($isCatMode)): ?>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
    <?php endif; ?>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.5; margin: 0; padding: 20px; color: #333; font-size: 12px; }
        .container { max-width: 900px; margin: 0 auto; }
        h1, h2, h3 { margin: 0 0 12px; }
        h1 { text-align: center; font-size: 20px; border-bottom: 1px solid #ddd; padding-bottom: 10px; }
        h2 { font-size: 16px; background: #f5f5f5; padding: 6px 10px; border-left: 4px solid #3498db; margin-top: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        td, th { border: 1px solid #ccc; padding: 6px; vertical-align: top; }
        th { background: #f2f2f2; text-align: center; }
        .info td:first-child { width: 160px; font-weight: bold; }
        .row { display: flex; gap: 20px; }
        .col { flex: 1; }
        .highlight { font-weight: bold; font-size: 18px; color: #2980b9; }
        .text-success { color: #27ae60; }
        .text-danger { color: #e74c3c; }
        .text-center { text-align: center; }
        .chart-row { display: flex; gap: 20px; }
        .chart-col { flex: 1; }
        .chart-container { width: 100%; height: 280px; }
        @media print {
            body { padding: 0; }
            .container { max-width: none; }
            .row, .chart-row { display: block; }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>LAPORAN HASIL UJIAN</h1>
        <div style="text-align:center; margin-bottom: 14px;">
            <div><?= esc($hasil['nama_ujian']) ?> - <?= esc($hasil['nama_jenis']) ?></div>
            <div>Kode Ujian: <?= esc($hasil['kode_ujian']) ?></div>
        </div>

        <div class="row">
            <div class="col">
                <h2>Informasi Ujian</h2>
                <table class="info">
                    <tr><td>Nama Ujian</td><td><?= esc($hasil['nama_ujian']) ?></td></tr>
                    <tr><td>Kode Ujian</td><td><?= esc($hasil['kode_ujian']) ?></td></tr>
                    <tr><td>Mata Pelajaran</td><td><?= esc($hasil['nama_jenis']) ?></td></tr>
                    <tr><td>Kelas</td><td><?= esc($hasil['nama_kelas']) ?></td></tr>
                </table>
            </div>
            <div class="col">
                <h2>Informasi Siswa</h2>
                <table class="info">
                    <tr><td>Nama Siswa</td><td><?= esc($hasil['nama_lengkap']) ?></td></tr>
                    <tr><td>Nomor Peserta</td><td><?= esc($hasil['nomor_peserta']) ?></td></tr>
                    <tr><td>Waktu Mulai</td><td><?= esc($hasil['waktu_mulai_format']) ?></td></tr>
                    <tr><td>Waktu Selesai</td><td><?= esc($hasil['waktu_selesai_format']) ?></td></tr>
                    <tr><td>Total Durasi</td><td><?= esc($hasil['durasi_total_format']) ?></td></tr>
                    <tr><td>Rata-rata/Soal</td><td><?= esc($rataRataWaktuFormat) ?></td></tr>
                </table>
            </div>
        </div>

        <?php
            $thetaPdf = $lastTheta ?? $thetaAkhir ?? null;
            $totalPdf = (int) ($totalSoal ?? count($detailJawaban));
            $benarPdf = (int) $jawabanBenar;
            $salahPdf = max(0, $totalPdf - $benarPdf);
            $akurasiRaw = $totalPdf > 0 ? round(($benarPdf / $totalPdf) * 100, 2) : 0;
            $akurasiPdf = min(100, max(0, $akurasiRaw));
        ?>

        <h2>Hasil Akhir</h2>
        <div class="row">
            <div class="col">
                <table class="info">
                    <tr><td>Total Soal</td><td><b><?= $totalPdf ?></b> soal</td></tr>
                    <tr><td>Jawaban Benar</td><td><b><?= $benarPdf ?></b> soal</td></tr>
                    <tr><td>Jawaban Salah</td><td><b><?= $salahPdf ?></b> soal</td></tr>
                    <tr><td>Akurasi</td><td><b><?= number_format((float) $akurasiPdf, 2) ?>%</b></td></tr>
                    <?php if (!empty($isCatMode)): ?>
                        <tr><td>Theta Akhir</td><td><b><?= number_format((float) ($thetaPdf ?? 0), 3) ?></b></td></tr>
                        <tr><td>SE Akhir</td><td><b><?= number_format((float) $seAkhir, 3) ?></b></td></tr>
                    <?php endif; ?>
                </table>
            </div>
            <div class="col">
                <table class="info">
                    <tr><td><?= !empty($isCatMode) ? 'Skor Kognitif' : 'Nilai Akhir CBT' ?></td><td><span class="highlight"><?= number_format((float) $finalScore, 2) ?></span></td></tr>
                    <tr><td>Kategori</td><td><?= esc($klasifikasiKognitif['kategori']) ?></td></tr>
                    <?php if (empty($isCatMode) && $thetaPdf !== null): ?>
                        <tr><td>Theta EAP</td><td><?= number_format((float) $thetaPdf, 4) ?></td></tr>
                        <tr><td>SEM</td><td><?= number_format((float) ($seAkhir ?? 0), 4) ?></td></tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>

        <?php if (!empty($isCatMode)): ?>
            <h2>Analisis Kognitif</h2>
            <table class="info">
                <tr><td>Skor Kognitif</td><td><?= $kemampuanKognitif['skor'] ?></td></tr>
                <tr><td>Kategori</td><td><?= esc($klasifikasiKognitif['kategori']) ?></td></tr>
            </table>
        <?php else: ?>
            <h2>Ringkasan CBT</h2>
            <table class="info">
                <tr><td>Nilai Akhir</td><td><b><?= number_format((float) $finalScore, 2) ?></b> - <?= esc($klasifikasiKognitif['kategori']) ?></td></tr>
                <tr><td>Komposisi Jawaban</td><td><?= $benarPdf ?> benar, <?= $salahPdf ?> salah dari <?= $totalPdf ?> soal</td></tr>
                <tr><td>Akurasi</td><td><?= number_format((float) $akurasiPdf, 2) ?>%</td></tr>
                <?php if ($thetaPdf !== null): ?>
                    <tr><td>Parameter Tambahan</td><td>Theta EAP <?= number_format((float) $thetaPdf, 4) ?>, SEM <?= number_format((float) ($seAkhir ?? 0), 4) ?></td></tr>
                <?php endif; ?>
            </table>
        <?php endif; ?>

        <?php if (!empty($isCatMode)): ?>
            <h2>Grafik Perkembangan</h2>
            <div class="chart-row">
                <div class="chart-col">
                    <h3>Grafik Theta</h3>
                    <div class="chart-container"><canvas id="thetaChart"></canvas></div>
                </div>
                <div class="chart-col">
                    <h3>Grafik Standard Error</h3>
                    <div class="chart-container"><canvas id="seChart"></canvas></div>
                </div>
            </div>
        <?php endif; ?>

        <h2>Detail Jawaban</h2>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Soal</th>
                    <th>ID Soal</th>
                    <th>Pertanyaan</th>
                    <th>Tingkat Kesulitan</th>
                    <th>Jawaban</th>
                    <th>Kunci</th>
                    <th>Status</th>
                    <?php if (empty($isCatMode)): ?>
                        <th>Kategori</th>
                        <th>P(&#952;)</th>
                        <th>z</th>
                        <th>Keterangan</th>
                    <?php endif; ?>
                    <th>Waktu Jawab</th>
                    <th>Durasi</th>
                    <?php if (!empty($isCatMode)): ?>
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
                        <td class="text-center"><?= $jawaban['nomor_soal'] ?></td>
                        <td class="text-center"><?= esc($jawaban['kode_soal']) ?></td>
                        <td class="text-center"><?= $jawaban['soal_id'] ?></td>
                        <td><?= $jawaban['pertanyaan'] ?></td>
                        <td class="text-center"><?= number_format((float) $jawaban['tingkat_kesulitan'], 3) ?></td>
                        <td class="text-center"><?= esc($jawaban['jawaban_siswa']) ?></td>
                        <td class="text-center"><?= esc($jawaban['jawaban_benar'] ?? '-') ?></td>
                        <td class="text-center <?= !empty($jawaban['is_correct']) ? 'text-success' : 'text-danger' ?>"><?= !empty($jawaban['is_correct']) ? 'Benar' : 'Salah' ?></td>
                        <?php if (empty($isCatMode)): ?>
                            <td class="text-center"><?= esc($jawaban['kategori_soal'] ?? '-') ?></td>
                            <td class="text-center"><?= isset($jawaban['p_residu']) ? number_format((float) $jawaban['p_residu'], 3) : '-' ?></td>
                            <td class="text-center"><?= isset($jawaban['z_score']) ? number_format((float) $jawaban['z_score'], 3) : '-' ?></td>
                            <td class="text-center"><?= esc($jawaban['keterangan_residu'] ?? '-') ?></td>
                        <?php endif; ?>
                        <td class="text-center"><?= esc($jawaban['waktu_menjawab_format']) ?></td>
                        <td class="text-center"><?= esc($jawaban['durasi_pengerjaan_format']) ?></td>
                        <?php if (!empty($isCatMode)): ?>
                            <td class="text-center"><?= isset($jawaban['pi_saat_ini']) ? number_format((float) $jawaban['pi_saat_ini'], 3) : '-' ?></td>
                            <td class="text-center"><?= isset($jawaban['qi_saat_ini']) ? number_format((float) $jawaban['qi_saat_ini'], 3) : '-' ?></td>
                            <td class="text-center"><?= isset($jawaban['ii_saat_ini']) ? number_format((float) $jawaban['ii_saat_ini'], 3) : '-' ?></td>
                            <td class="text-center"><?= isset($jawaban['se_saat_ini']) ? number_format((float) $jawaban['se_saat_ini'], 3) : '-' ?></td>
                            <td class="text-center"><?= isset($jawaban['delta_se_saat_ini']) ? number_format(abs((float) $jawaban['delta_se_saat_ini']), 3) : '-' ?></td>
                            <td class="text-center"><?= isset($jawaban['theta_saat_ini']) ? number_format((float) $jawaban['theta_saat_ini'], 3) : '-' ?></td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if (!empty($isCatMode)): ?>
        <script>
            const thetaData = <?= json_encode(array_map(static fn($item) => (float) ($item['theta_saat_ini'] ?? 0), $detailJawaban)) ?>;
            const seData = <?= json_encode(array_map(static fn($item) => (float) ($item['se_saat_ini'] ?? 0), $detailJawaban)) ?>;
            const labels = <?= json_encode(array_map(static fn($item) => 'Soal ' . $item['nomor_soal'], $detailJawaban)) ?>;

            new Chart(document.getElementById('thetaChart'), {
                type: 'line',
                data: { labels, datasets: [{ label: 'Theta', data: thetaData, borderColor: '#4e73df', tension: 0.1 }] },
                options: { responsive: true, maintainAspectRatio: false }
            });

            new Chart(document.getElementById('seChart'), {
                type: 'line',
                data: { labels, datasets: [{ label: 'SE', data: seData, borderColor: '#1cc88a', tension: 0.1 }] },
                options: { responsive: true, maintainAspectRatio: false }
            });
        </script>
    <?php endif; ?>
</body>
</html>
