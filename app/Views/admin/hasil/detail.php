<?= $this->extend('templates/admin/admin_template') ?>

<?= $this->section('title') ?>Detail Hasil Ujian<?= $this->endSection() ?>

<?= $this->section('content') ?>

<br><br><br>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Detail Hasil Ujian</h2>
            <p class="text-muted mb-0">
                <?= esc($hasil['nama_ujian']) ?> - <?= esc($hasil['nama_jenis']) ?>
            </p>
            <?php
            // Generate kode soal dari tahun dan ID
            $tahunPembuatan = date('Y', strtotime($hasil['tanggal_mulai']));
            $kode_soal = $tahunPembuatan . str_pad($hasil['ujian_id'], 4, '0', STR_PAD_LEFT);
            ?>
            <p class="text-muted mb-0">
                Kode Soal: <strong><?= $kode_soal ?></strong>
            </p>
        </div>
        <div>
            <a href="<?= base_url('admin/hasil-ujian/siswa/' . $hasil['jadwal_id']) ?>"
                class="btn btn-secondary me-2">
                <i class="fas fa-arrow-left me-1"></i>Kembali
            </a>

            <!-- Download Buttons -->
            <div class="btn-group me-2" role="group">
                <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-download me-1"></i>Download
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a class="dropdown-item" href="<?= base_url('admin/hasil-ujian/download-excel/' . $hasil['peserta_ujian_id']) ?>" target="_blank">
                            <i class="fas fa-file-excel me-2 text-success"></i>Download Excel
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="<?= base_url('admin/hasil-ujian/download-pdf/' . $hasil['peserta_ujian_id']) ?>" target="_blank">
                            <i class="fas fa-file-pdf me-2 text-danger"></i>Download PDF
                        </a>
                    </li>
                </ul>
            </div>

            <a href="<?= base_url('admin/hasil-ujian/hapus/' . $hasil['peserta_ujian_id']) ?>"
                class="btn btn-danger"
                onclick="return confirm('Apakah Anda yakin ingin menghapus hasil ujian siswa ini?\n\nSiswa akan direset ke status belum mulai.')">
                <i class="fas fa-trash me-1"></i>Hapus Hasil
            </a>
        </div>
    </div>

    <!-- Info Siswa -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td style="width: 150px">Nama Siswa</td>
                            <td>: <?= esc($hasil['nama_lengkap']) ?></td>
                        </tr>
                        <tr>
                            <td>NIS</td>
                            <td>: <?= esc($hasil['nomor_peserta']) ?></td>
                        </tr>
                        <tr>
                            <td>Kelas</td>
                            <td>: <?= esc($hasil['nama_kelas']) ?> - <?= esc($hasil['nama_sekolah']) ?></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td style="width: 150px">Waktu Mulai</td>
                            <td>: <?= $hasil['waktu_mulai_format'] ?></td>
                        </tr>
                        <tr>
                            <td>Waktu Selesai</td>
                            <td>: <?= $hasil['waktu_selesai_format'] ?></td>
                        </tr>
                        <tr>
                            <td>Total Durasi</td>
                            <td>: <?= $hasil['durasi_total_format'] ?></td>
                        </tr>
                        <tr>
                            <td>Rata-rata/Soal</td>
                            <td>: <?= $rataRataWaktuFormat ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Hasil Akhir -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent">
            <h5 class="card-title mb-0">Hasil Akhir</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td width="200">Total Soal</td>
                            <td width="20">:</td>
                            <td><strong><?= $totalSoal ?></strong> soal</td>
                        </tr>
                        <tr>
                            <td>Jawaban Benar</td>
                            <td>:</td>
                            <td><strong><?= $jawabanBenar ?></strong> soal</td>
                        </tr>
                        <tr>
                            <td>Standard Error Akhir</td>
                            <td>:</td>
                            <td><strong><?= number_format(end($detailJawaban)['se_saat_ini'], 3) ?></strong></td>
                        </tr>
                        <tr>
                            <td width="200">Theta Akhir (θ)</td>
                            <td width="20">:</td>
                            <td><strong><?= number_format(end($detailJawaban)['theta_saat_ini'], 3) ?></strong></td>
                        </tr>

                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless">


                        <tr>
                            <td>Skor</td>
                            <td>:</td>
                            <td><strong class="fs-4 text-primary"><?= number_format(50 + (16.6 * end($detailJawaban)['theta_saat_ini']), 1) ?></strong></td>
                        </tr>
                        <tr>
                            <td>Nilai (Skala 0-100)</td>
                            <td>:</td>
                            <td><strong class="fs-4 text-success"><?= min(100, max(0, round(((50 + (16.6 * end($detailJawaban)['theta_saat_ini'])) / 100) * 100))) ?></strong></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- **BARU: Kemampuan Kognitif** -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Analisis Kemampuan Kognitif</h5>
            <button class="btn btn-sm btn-outline-info" type="button" data-bs-toggle="collapse" data-bs-target="#cognitiveHelp" aria-expanded="false">
                <i class="fas fa-info-circle me-1"></i>Info Perhitungan
            </button>
        </div>

        <div class="collapse" id="cognitiveHelp">
            <div class="card-body bg-light">
                <h6 class="fw-bold">Rumus Kemampuan Kognitif:</h6>
                <p class="mb-2"><strong>Skor = (B - (S/(P-1))) / N × 100</strong></p>
                <ul class="small mb-0">
                    <li><strong>B</strong>: Jumlah jawaban benar</li>
                    <li><strong>S</strong>: Jumlah jawaban salah</li>
                    <li><strong>P</strong>: Rata-rata jumlah pilihan jawaban per soal</li>
                    <li><strong>N</strong>: Total jumlah soal</li>
                </ul>
                <p class="small mt-2 mb-0 text-muted">
                    Formula ini menggunakan koreksi tebakan untuk mengurangi efek menebak secara acak pada penilaian kemampuan kognitif.
                </p>
            </div>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="180">Skor Kemampuan Kognitif</td>
                                    <td width="20">:</td>
                                    <td><strong class="fs-4 text-primary"><?= $kemampuanKognitif['skor'] ?></strong></td>
                                </tr>
                                <tr>
                                    <td>Kategori</td>
                                    <td>:</td>
                                    <td>
                                        <span class="badge <?= $klasifikasiKognitif['bg_class'] ?> fs-6">
                                            <?= $klasifikasiKognitif['kategori'] ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Rata-rata Pilihan/Soal</td>
                                    <td>:</td>
                                    <td><strong><?= $kemampuanKognitif['rata_rata_pilihan'] ?></strong></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="150">Total Benar</td>
                                    <td width="20">:</td>
                                    <td><strong class="text-success"><?= $kemampuanKognitif['total_benar'] ?></strong> soal</td>
                                </tr>
                                <tr>
                                    <td>Total Salah</td>
                                    <td>:</td>
                                    <td><strong class="text-danger"><?= $kemampuanKognitif['total_salah'] ?></strong> soal</td>
                                </tr>
                                <tr>
                                    <td>Persentase Benar</td>
                                    <td>:</td>
                                    <td><strong><?= round(($kemampuanKognitif['total_benar'] / $totalSoal) * 100, 1) ?>%</strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center">
                        <div class="circular-progress mx-auto" style="width: 120px; height: 120px; background: conic-gradient(<?= $klasifikasiKognitif['bg_class'] === 'bg-success' ? '#28a745' : ($klasifikasiKognitif['bg_class'] === 'bg-info' ? '#17a2b8' : ($klasifikasiKognitif['bg_class'] === 'bg-warning' ? '#ffc107' : '#dc3545')) ?> <?= ($kemampuanKognitif['skor'] / 100) * 360 ?>deg, #e9ecef 0deg); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <div style="width: 80px; height: 80px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                <span class="fw-bold fs-5"><?= $kemampuanKognitif['skor'] ?></span>
                            </div>
                        </div>
                        <p class="mt-2 mb-0 fw-bold <?= $klasifikasiKognitif['class'] ?>">
                            <?= $klasifikasiKognitif['kategori'] ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Jawaban -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Detail Jawaban</h5>
            <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#additionalInfoHelp" aria-expanded="false">
                <i class="fas fa-info-circle me-1"></i>Info Kolom
            </button>
        </div>

        <div class="collapse" id="additionalInfoHelp">
            <div class="card-body bg-light">
                <h6 class="fw-bold">Penjelasan Kolom:</h6>
                <ul class="small mb-0">
                    <li><strong>Pi</strong>: Probabilitas menjawab benar</li>
                    <li><strong>Qi</strong>: Probabilitas menjawab salah</li>
                    <li><strong>Ii</strong>: Fungsi informasi</li>
                    <li><strong>SE</strong>: Standard Error</li>
                    <li><strong>ΔSE</strong>: Perubahan Standard Error</li>
                    <li><strong>θ</strong>: Theta/Kemampuan setelah menjawab soal</li>
                    <li><strong>Waktu Jawab</strong>: Jam berapa soal dijawab</li>
                    <li><strong>Durasi</strong>: Lama mengerjakan soal tersebut</li>
                </ul>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>ID Soal</th>
                        <th>Pertanyaan</th>
                        <th>Tingkat Kesulitan</th>
                        <th>Jawaban</th>
                        <th>Status</th>
                        <th>Waktu Jawab</th>
                        <th>Durasi</th>
                        <th>Pi</th>
                        <th>Qi</th>
                        <th>Ii</th>
                        <th>SE</th>
                        <th>ΔSE</th>
                        <th>θ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($detailJawaban as $i => $jawaban): ?>
                        <tr>
                            <td><?= $jawaban['nomor_soal'] ?></td>
                            <td><?= $jawaban['soal_id'] ?></td>
                            <td>
                                <div style="max-width: 300px;">
                                    <?= strlen($jawaban['pertanyaan']) > 80 ? substr(esc($jawaban['pertanyaan']), 0, 80) . '...' : esc($jawaban['pertanyaan']) ?>
                                    <?php if (!empty($jawaban['foto'])): ?>
                                        <br><small class="text-info"><i class="fas fa-image me-1"></i>Ada gambar</small>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td><?= number_format($jawaban['tingkat_kesulitan'], 3) ?></td>
                            <td>
                                <span class="badge <?= $jawaban['is_correct'] ? 'bg-success' : 'bg-danger' ?>">
                                    <?= $jawaban['jawaban_siswa'] ?>
                                </span>
                                <br><small class="text-muted">Benar: <?= $jawaban['jawaban_benar'] ?></small>
                            </td>
                            <td>
                                <?php if ($jawaban['is_correct']): ?>
                                    <span class="badge bg-success">Benar</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Salah</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <small class="text-muted"><?= $jawaban['waktu_menjawab_format'] ?></small>
                            </td>
                            <td>
                                <small class="fw-bold text-info"><?= $jawaban['durasi_pengerjaan_format'] ?></small>
                            </td>
                            <td><?= isset($jawaban['pi_saat_ini']) ? number_format($jawaban['pi_saat_ini'], 3) : '-' ?></td>
                            <td><?= isset($jawaban['qi_saat_ini']) ? number_format($jawaban['qi_saat_ini'], 3) : '-' ?></td>
                            <td><?= isset($jawaban['ii_saat_ini']) ? number_format($jawaban['ii_saat_ini'], 3) : '-' ?></td>
                            <td><?= number_format($jawaban['se_saat_ini'], 3) ?></td>
                            <td><?= number_format(abs($jawaban['delta_se_saat_ini']), 3) ?></td>
                            <td><?= number_format($jawaban['theta_saat_ini'], 3) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Grafik Perkembangan (tetap sama, tanpa grafik informasi) -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <h5 class="card-title mb-0">Grafik Theta (θ)</h5>
                </div>
                <div class="card-body">
                    <canvas id="thetaChart" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <h5 class="card-title mb-0">Grafik Standard Error (SE)</h5>
                </div>
                <div class="card-body">
                    <canvas id="seChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Data untuk grafik theta dan SE
    const thetaData = <?= json_encode(array_map(function ($item) {
                            return $item['theta_saat_ini'];
                        }, $detailJawaban)) ?>;

    const seData = <?= json_encode(array_map(function ($item) {
                        return $item['se_saat_ini'];
                    }, $detailJawaban)) ?>;

    const labels = <?= json_encode(array_map(function ($item) {
                        return 'Soal ' . $item['nomor_soal'];
                    }, $detailJawaban)) ?>;

    // Grafik Theta
    new Chart(document.getElementById('thetaChart'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Theta (θ)',
                data: thetaData,
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                tension: 0.1,
                fill: false,
                pointBackgroundColor: '#4e73df'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Perkembangan Estimasi Kemampuan (θ)'
                },
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: false,
                    title: {
                        display: true,
                        text: 'Nilai Theta'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Urutan Soal'
                    }
                }
            }
        }
    });

    // Grafik SE
    new Chart(document.getElementById('seChart'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Standard Error',
                data: seData,
                borderColor: '#1cc88a',
                backgroundColor: 'rgba(28, 200, 138, 0.1)',
                tension: 0.1,
                fill: false,
                pointBackgroundColor: '#1cc88a'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Perkembangan Standard Error'
                },
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: false,
                    title: {
                        display: true,
                        text: 'Nilai SE'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Urutan Soal'
                    }
                }
            }
        }
    });
</script>
<?= $this->endSection() ?>