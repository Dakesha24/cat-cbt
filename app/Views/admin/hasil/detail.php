<?= $this->extend('templates/admin/admin_template') ?>

<?= $this->section('title') ?>Detail Hasil Ujian<?= $this->endSection() ?>
<?= $this->section('content') ?>

<br><br><br>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Detail Hasil Ujian</h2>
            <p class="text-muted mb-0"><?= esc($hasil['nama_ujian']) ?> - <?= esc($hasil['nama_jenis']) ?></p>
            <p class="text-muted mb-0">Kode Ujian: <strong><?= esc($hasil['kode_ujian'] ?? $hasil['kode_akses']) ?></strong></p>
        </div>
        <div>
            <a href="<?= esc($backUrl ?? base_url('admin/hasil-ujian/siswa/' . $hasil['jadwal_id'])) ?>" class="btn btn-secondary me-2">
                <i class="fas fa-arrow-left me-1"></i>Kembali
            </a>
            <div class="btn-group me-2" role="group">
                <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-download me-1"></i>Download
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="<?= base_url('admin/hasil-ujian/download-excel/' . $hasil['peserta_ujian_id']) . (!empty($hasil['attempt_id']) ? '?attempt_id=' . $hasil['attempt_id'] : '') ?>" target="_blank"><i class="fas fa-file-excel me-2 text-success"></i>Download Excel</a></li>
                    <li><a class="dropdown-item" href="<?= base_url('admin/hasil-ujian/download-pdf/' . $hasil['peserta_ujian_id']) . (!empty($hasil['attempt_id']) ? '?attempt_id=' . $hasil['attempt_id'] : '') ?>" target="_blank"><i class="fas fa-file-pdf me-2 text-danger"></i>Download PDF</a></li>
                </ul>
            </div>
            <a href="<?= base_url('admin/hasil-ujian/hapus/' . $hasil['peserta_ujian_id']) ?>" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus hasil ujian siswa ini?\n\nSiswa akan direset ke status belum mulai.')">
                <i class="fas fa-trash me-1"></i>Hapus Hasil
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless mb-0">
                        <tr><td style="width: 150px">Nama Siswa</td><td>: <?= esc($hasil['nama_lengkap']) ?></td></tr>
                        <tr><td>NIS</td><td>: <?= esc($hasil['nomor_peserta']) ?></td></tr>
                        <tr><td>Kelas</td><td>: <?= esc($hasil['nama_kelas']) ?> - <?= esc($hasil['nama_sekolah']) ?></td></tr>
                        <tr><td><?= $isCatMode ? 'Theta Akhir (θ)' : 'Nilai Akhir' ?></td><td>: <strong><?= $isCatMode ? number_format((float) $thetaAkhir, 3) : number_format((float) $finalGrade, 2) ?></strong></td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless mb-0">
                        <tr><td style="width: 150px">Waktu Mulai</td><td>: <?= $hasil['waktu_mulai_format'] ?></td></tr>
                        <tr><td>Waktu Selesai</td><td>: <?= $hasil['waktu_selesai_format'] ?></td></tr>
                        <tr><td>Total Durasi</td><td>: <?= $hasil['durasi_total_format'] ?></td></tr>
                        <tr><td>Rata-rata/Soal</td><td>: <?= $rataRataWaktuFormat ?></td></tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent">
            <h5 class="card-title mb-0">Ringkasan Hasil</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless mb-0">
                        <tr><td width="180">Total Soal</td><td>: <strong><?= $totalSoal ?></strong> soal</td></tr>
                        <tr><td>Jawaban Benar</td><td>: <strong><?= $jawabanBenar ?></strong> soal</td></tr>
                        <?php if ($isCatMode): ?>
                            <tr><td>Standard Error Akhir</td><td>: <strong><?= number_format((float) $seAkhir, 3) ?></strong></td></tr>
                            <tr><td>Theta Akhir (θ)</td><td>: <strong><?= number_format((float) $thetaAkhir, 3) ?></strong></td></tr>
                        <?php endif; ?>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless mb-0">
                        <tr><td><?= $isCatMode ? 'Skor' : 'Nilai' ?></td><td>: <strong class="fs-4 text-primary"><?= number_format((float) $finalScore, 2) ?></strong></td></tr>
                        <tr><td>Nilai</td><td>: <strong class="fs-4 text-success"><?= number_format((float) $finalGrade, $isCatMode ? 0 : 2) ?></strong></td></tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0"><?= $isCatMode ? 'Analisis Kemampuan Kognitif' : 'Ringkasan Nilai CBT' ?></h5>
            <?php if ($isCatMode): ?>
                <button class="btn btn-sm btn-outline-info" type="button" data-bs-toggle="collapse" data-bs-target="#cognitiveHelp" aria-expanded="false">
                    <i class="fas fa-info-circle me-1"></i>Info Perhitungan
                </button>
            <?php endif; ?>
        </div>

        <?php if ($isCatMode): ?>
            <div class="collapse" id="cognitiveHelp">
                <div class="card-body bg-light">
                    <p class="mb-2"><strong>Skor = 50 + (16.67 × θ)</strong></p>
                    <p class="small mb-0 text-muted">Digunakan untuk mengubah estimasi theta menjadi skor kognitif.</p>
                </div>
            </div>
        <?php endif; ?>

        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <table class="table table-borderless mb-0">
                        <tr><td width="180"><?= $isCatMode ? 'Skor Kognitif' : 'Nilai CBT' ?></td><td width="20">:</td><td><strong class="fs-4 text-primary"><?= $isCatMode ? $kemampuanKognitif['skor'] : number_format((float) $finalGrade, 2) ?></strong></td></tr>
                        <?php if ($isCatMode): ?>
                            <tr><td>Kategori</td><td>:</td><td><span class="badge <?= $klasifikasiKognitif['bg_class'] ?> fs-6"><?= $klasifikasiKognitif['kategori'] ?></span></td></tr>
                        <?php endif; ?>
                        <tr><td>Total Benar</td><td>:</td><td><strong class="text-success"><?= $jawabanBenar ?></strong> soal</td></tr>
                        <tr><td>Total Salah</td><td>:</td><td><strong class="text-danger"><?= $totalSoal - $jawabanBenar ?></strong> soal</td></tr>
                    </table>
                </div>
                <div class="col-md-4">
                    <div class="text-center">
                        <div class="circular-progress mx-auto" style="width: 120px; height: 120px; background: conic-gradient(<?= $isCatMode ? ($klasifikasiKognitif['bg_class'] === 'bg-success' ? '#28a745' : ($klasifikasiKognitif['bg_class'] === 'bg-info' ? '#17a2b8' : ($klasifikasiKognitif['bg_class'] === 'bg-warning' ? '#ffc107' : '#dc3545'))) : '#0d6efd' ?> <?= (($isCatMode ? $kemampuanKognitif['skor'] : $finalGrade) / 100) * 360 ?>deg, #e9ecef 0deg); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <div style="width: 80px; height: 80px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                <span class="fw-bold fs-5"><?= $isCatMode ? $kemampuanKognitif['skor'] : number_format((float) $finalGrade, 2) ?></span>
                            </div>
                        </div>
                        <?php if ($isCatMode): ?>
                            <p class="mt-2 mb-0 fw-bold <?= $klasifikasiKognitif['class'] ?>"><?= $klasifikasiKognitif['kategori'] ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Detail Jawaban</h5>
            <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#additionalInfoHelp" aria-expanded="false">
                <i class="fas fa-info-circle me-1"></i>Info Kolom
            </button>
        </div>

        <div class="collapse" id="additionalInfoHelp">
            <div class="card-body bg-light">
                <?php if ($isCatMode): ?>
                    <ul class="small mb-0">
                        <li><strong>Pi</strong>: Probabilitas menjawab benar</li>
                        <li><strong>Qi</strong>: Probabilitas menjawab salah</li>
                        <li><strong>Ii</strong>: Fungsi informasi</li>
                        <li><strong>SE</strong>: Standard Error</li>
                        <li><strong>ΔSE</strong>: Perubahan Standard Error</li>
                        <li><strong>θ</strong>: Theta setelah menjawab soal</li>
                    </ul>
                <?php else: ?>
                    <ul class="small mb-0">
                        <li><strong>Status</strong>: Menunjukkan jawaban benar atau salah</li>
                        <li><strong>Waktu Jawab</strong>: Jam saat soal dijawab</li>
                        <li><strong>Durasi</strong>: Lama mengerjakan soal</li>
                    </ul>
                <?php endif; ?>
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
                        <?php if ($isCatMode): ?>
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
                            <td><?= $jawaban['soal_id'] ?></td>
                            <td>
                                <div style="max-width: 300px;">
                                    <?= strlen($jawaban['pertanyaan']) > 80 ? substr(esc($jawaban['pertanyaan']), 0, 80) . '...' : esc($jawaban['pertanyaan']) ?>
                                    <?php if (!empty($jawaban['foto'])): ?>
                                        <br><small class="text-info"><i class="fas fa-image me-1"></i>Ada gambar</small>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td><?= number_format((float) $jawaban['tingkat_kesulitan'], 3) ?></td>
                            <td>
                                <span class="badge <?= $jawaban['is_correct'] ? 'bg-success' : 'bg-danger' ?>"><?= $jawaban['jawaban_siswa'] ?></span>
                                <br><small class="text-muted">Benar: <?= $jawaban['jawaban_benar'] ?></small>
                            </td>
                            <td><?= $jawaban['is_correct'] ? 'BENAR' : 'SALAH' ?></td>
                            <td><small class="text-muted"><?= $jawaban['waktu_menjawab_format'] ?></small></td>
                            <td><small class="fw-bold text-info"><?= $jawaban['durasi_pengerjaan_format'] ?></small></td>
                            <?php if ($isCatMode): ?>
                                <td><?= isset($jawaban['pi_saat_ini']) ? number_format((float) $jawaban['pi_saat_ini'], 3) : '-' ?></td>
                                <td><?= isset($jawaban['qi_saat_ini']) ? number_format((float) $jawaban['qi_saat_ini'], 3) : '-' ?></td>
                                <td><?= isset($jawaban['ii_saat_ini']) ? number_format((float) $jawaban['ii_saat_ini'], 3) : '-' ?></td>
                                <td><?= isset($jawaban['se_saat_ini']) ? number_format((float) $jawaban['se_saat_ini'], 3) : '-' ?></td>
                                <td><?= isset($jawaban['delta_se_saat_ini']) ? number_format(abs((float) $jawaban['delta_se_saat_ini']), 3) : '-' ?></td>
                                <td><?= isset($jawaban['theta_saat_ini']) ? number_format((float) $jawaban['theta_saat_ini'], 3) : '-' ?></td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if ($isCatMode): ?>
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent"><h5 class="card-title mb-0">Grafik Theta (θ)</h5></div>
                    <div class="card-body"><canvas id="thetaChart" height="300"></canvas></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent"><h5 class="card-title mb-0">Grafik Standard Error (SE)</h5></div>
                    <div class="card-body"><canvas id="seChart" height="300"></canvas></div>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const thetaData = <?= json_encode(array_map(static fn($item) => (float) ($item['theta_saat_ini'] ?? 0), $detailJawaban)) ?>;
            const seData = <?= json_encode(array_map(static fn($item) => (float) ($item['se_saat_ini'] ?? 0), $detailJawaban)) ?>;
            const labels = <?= json_encode(array_map(static fn($item) => 'Soal ' . $item['nomor_soal'], $detailJawaban)) ?>;

            new Chart(document.getElementById('thetaChart'), {
                type: 'line',
                data: { labels, datasets: [{ label: 'Theta', data: thetaData, borderColor: '#4e73df', backgroundColor: 'rgba(78, 115, 223, 0.1)', tension: 0.1, fill: false, pointBackgroundColor: '#4e73df' }] },
                options: { responsive: true, plugins: { legend: { display: false } } }
            });

            new Chart(document.getElementById('seChart'), {
                type: 'line',
                data: { labels, datasets: [{ label: 'SE', data: seData, borderColor: '#1cc88a', backgroundColor: 'rgba(28, 200, 138, 0.1)', tension: 0.1, fill: false, pointBackgroundColor: '#1cc88a' }] },
                options: { responsive: true, plugins: { legend: { display: false } } }
            });
        </script>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
