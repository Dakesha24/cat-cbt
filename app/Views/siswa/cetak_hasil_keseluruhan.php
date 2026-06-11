<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keseluruhan - <?= esc($ujian['nama_ujian']) ?></title>

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

        .correct   { color: #28a745 !important; font-weight: bold; }
        .incorrect { color: #dc3545 !important; font-weight: bold; }
        td.correct   { color: #28a745 !important; font-weight: bold; }
        td.incorrect { color: #dc3545 !important; font-weight: bold; }
        .score { color: #007bff; font-weight: bold; }

        .kognitif-sangat-tinggi { color: #28a745 !important; font-weight: bold; }
        .kognitif-tinggi        { color: #17a2b8 !important; font-weight: bold; }
        .kognitif-sedang        { color: #ffc107 !important; font-weight: bold; }
        .kognitif-rendah        { color: #fd7e14 !important; font-weight: bold; }
        .kognitif-sangat-rendah { color: #dc3545 !important; font-weight: bold; }

        .chart-container { position: relative; height: 320px; width: 100%; margin-bottom: 20px; }

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

<?php $isCbt = ($ujian['tipe_ujian'] ?? 'CAT') === 'CBT'; ?>

    <!-- Tombol kembali -->
    <div class="back-button">
        <a href="<?= base_url('siswa/hasil/ujian/' . ($attempts[0]['peserta_ujian_id'] ?? '')) ?>" class="btn btn-outline-secondary">
            &larr; Kembali
        </a>
    </div>

    <div class="report-container">

        <!-- ===== Header ===== -->
        <div class="header">
            <div class="title">LAPORAN KESELURUHAN PERCOBAAN</div>
            <div class="subtitle"><?= esc($ujian['nama_ujian']) ?></div>
            <div class="kode-ujian">
                Kode Ujian: <?= esc($ujian['kode_ujian']) ?>
                &nbsp;|&nbsp;
                <?= $isCbt ? 'CBT (EAP IRT 3PL)' : 'CAT (IRT 1PL)' ?>
            </div>
        </div>

        <!-- ===== Info Siswa ===== -->
        <div class="info-box">
            <div class="info-label">Nama Siswa:</div>
            <div class="info-value"><?= esc($siswa['nama_lengkap']) ?></div>

            <div class="info-label">NIS:</div>
            <div class="info-value"><?= esc($siswa['nomor_peserta']) ?></div>

            <div class="info-label">Mata Pelajaran:</div>
            <div class="info-value"><?= esc($ujian['nama_jenis']) ?></div>

            <div class="info-label">Jumlah Percobaan:</div>
            <div class="info-value"><?= count($attempts) ?></div>
        </div>

        <?php if (count($chartLabels) > 1): ?>
        <!-- ===== Grafik Perkembangan Nilai ===== -->
        <div class="mb-4">
            <h4 class="mb-3">Grafik Perkembangan Nilai</h4>
            <div class="chart-container">
                <canvas id="progressChart"></canvas>
            </div>
        </div>
        <?php endif; ?>

        <!-- ===== Rekap Semua Percobaan ===== -->
        <div class="mb-4">
            <h4 class="mb-3">Rekap Semua Percobaan</h4>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>Percobaan</th>
                            <th>Waktu Mulai</th>
                            <th>Waktu Selesai</th>
                            <th>Durasi</th>
                            <th>Total Soal</th>
                            <th>Benar</th>
                            <th>Salah</th>
                            <?php if ($isCbt): ?>
                                <th>θ_EAP</th>
                                <th>SEM</th>
                                <th>Nilai EAP</th>
                            <?php else: ?>
                                <th>Skor Kognitif</th>
                            <?php endif; ?>
                            <th>Kategori</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($attempts as $att): ?>
                        <tr>
                            <td>Percobaan <?= esc($att['nomor_attempt']) ?></td>
                            <td><?= $att['waktu_mulai_format'] ?></td>
                            <td><?= $att['waktu_selesai_format'] ?></td>
                            <td><?= $att['durasi_format'] ?></td>
                            <td><?= $att['jumlah_soal'] ?></td>
                            <td class="correct"><?= $att['jawaban_benar'] ?></td>
                            <td class="incorrect"><?= $att['jawaban_salah'] ?></td>
                            <?php if ($isCbt): ?>
                                <td><?= number_format((float) ($att['theta_akhir'] ?? 0), 4) ?></td>
                                <td><?= number_format((float) ($att['sem_akhir'] ?? 0), 4) ?></td>
                                <td class="score"><?= number_format((float) $att['nilai_tampil'], 2) ?></td>
                            <?php else: ?>
                                <td class="score"><?= number_format((float) $att['nilai_tampil'], 2) ?></td>
                            <?php endif; ?>
                            <td class="<?= esc($att['klasifikasi']['class'], 'attr') ?>"><?= $att['klasifikasi']['kategori'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="text-end mt-4 text-muted small">
            Dicetak pada: <?= date('d M Y H:i:s') ?>
        </div>
    </div>

    <!-- Tombol Print -->
    <button class="btn btn-primary btn-lg print-button" onclick="window.print()">
        &#128438; Cetak Laporan
    </button>

    <?php if (count($chartLabels) > 1): ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const ctx = document.getElementById('progressChart').getContext('2d');

            const labels = <?= json_encode($chartLabels) ?>;
            const dataPoints = <?= json_encode($chartData) ?>;
            const isCbt = <?= json_encode($isCbt) ?>;
            const labelName = isCbt ? 'Nilai EAP' : 'Skor Kognitif';

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: labelName,
                        data: dataPoints,
                        borderColor: '#1d4ed8',
                        backgroundColor: 'rgba(29, 78, 216, 0.1)',
                        borderWidth: 2,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#1d4ed8',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    return labelName + ': ' + context.parsed.y.toFixed(2);
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { borderDash: [4, 4], color: '#e2e8f0' }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });
        });
    </script>
    <?php endif; ?>

    <script>
        if (new URLSearchParams(window.location.search).get('autoprint') === 'true') {
            window.onload = function () {
                setTimeout(function () { window.print(); }, 1000);
            };
        }
    </script>
</body>
</html>
