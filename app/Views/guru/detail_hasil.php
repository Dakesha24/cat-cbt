<?= $this->extend('templates/guru/guru_template') ?>

<?= $this->section('content') ?>
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-4 py-5">
    <div>
      <h2 class="mb-1">Detail Hasil Ujian</h2>
      <p class="text-muted mb-0">
        <?= esc($hasil['nama_ujian']) ?> - <?= esc($hasil['nama_jenis']) ?>
      </p>
      <p class="text-muted mb-0">
        Kode Ujian: <code><?= esc($hasil['kode_ujian']) ?></code>
      </p>
    </div>
    <div>
      <!-- Tombol Download Hasil -->
      <div class="btn-group me-2">
        <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="bi bi-download"></i> Download Hasil
        </button>
        <ul class="dropdown-menu">
          <li><a class="dropdown-item" href="<?= base_url('guru/hasil-ujian/download-excel-html/' . $hasil['peserta_ujian_id']) ?>">Excel</a></li>
          <li><a class="dropdown-item" href="<?= base_url('guru/hasil-ujian/download-pdf-html/' . $hasil['peserta_ujian_id']) ?>">PDF</a></li>
        </ul>
      </div>

      <a href="<?= base_url('guru/hasil-ujian/siswa/' . $hasil['jadwal_id']) ?>"
        class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
      </a>
    </div>
  </div>

  <!-- Info Siswa -->
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
      <div class="row">
        <div class="col-md-6">
          <table class="table table-borderless">
            <tr>
              <td width="200">Theta Akhir (θ)</td>
              <td width="20">:</td>
              <?php $lastResult = end($detailJawaban); ?>
              <td><strong><?= $lastResult ? number_format($lastResult['theta_saat_ini'], 3) : 'N/A' ?></strong></td>
            </tr>
            <tr>
              <td>Skor Akhir</td>
              <td>:</td>
              <td><strong class="fs-4 text-primary"><?= $kemampuanKognitif['skor'] ?></strong></td>
            </tr>
            <tr>
              <td>Nilai (Skala 0-100)</td>
              <td>:</td>
              <td><strong class="fs-4 text-success"><?= min(100, max(0, round($kemampuanKognitif['skor']))) ?></strong></td>
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
  <?php
  // Ambil theta terakhir (dari jawaban terakhir)
  $lastTheta = end($detailJawaban)['theta_saat_ini'];
  // Hitung nilai akhir: 50 + 16.6 * theta
  $finalScore = 50 + (16.67 * $lastTheta);
  // Nilai dalam skala 0-100
  $finalGrade = min(100, max(0, round(($finalScore / 100) * 100)));
  ?>
  <div class="row mb-4">
    <div class="col-md-8">
      <div class="card border-0 shadow-sm">
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
                  <td><strong><?= number_format($lastTheta, 3) ?></strong></td>
                </tr>
              </table>
            </div>
            <div class="col-md-6">
              <table class="table table-borderless">
                <tr>
                  <td>Skor</td>
                  <td>:</td>
                  <td><strong class="fs-4 text-primary"><?= number_format($finalScore, 2) ?></strong></td>
                </tr>
                <tr>
                  <td>Nilai (Skala 0-100)</td>
                  <td>:</td>
                  <td><strong class="fs-4 text-success"><?= $finalGrade ?></strong></td>
                </tr>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- BARU: Kartu Kemampuan Kognitif -->
    <div class="col-md-4">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent">
          <h5 class="card-title mb-0">
            <i class="bi bi-brain"></i> Kemampuan Kognitif
          </h5>
        </div>
        <div class="card-body text-center">
          <h2 class="<?= $klasifikasiKognitif['class'] ?> mb-2"><?= $kemampuanKognitif['skor'] ?></h2>
          <span class="badge <?= $klasifikasiKognitif['bg_class'] ?> text-white mb-3">
            <?= $klasifikasiKognitif['kategori'] ?>
          </span>

          <div class="text-start">
            <small class="text-muted d-block">Detail Perhitungan:</small>
            <div class="mt-2">
              <small class="text-muted">• Benar: <?= $kemampuanKognitif['total_benar'] ?></small><br>
              <small class="text-muted">• Salah: <?= $kemampuanKognitif['total_salah'] ?></small><br>
              <small class="text-muted">• Rata-rata Pilihan: <?= $kemampuanKognitif['rata_rata_pilihan'] ?></small><br>
              <small class="text-muted">• Total Soal: <?= $totalSoal ?></small>
            </div>
          </div>

          <div class="mt-3">
            <!-- <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#rumusKognitifModal">
                <i class="bi bi-calculator"></i> Lihat Rumus
              </button> -->
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Rumus Kemampuan Kognitif -->
  <div class="modal fade" id="rumusKognitifModal" tabindex="-1" aria-labelledby="rumusKognitifModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="rumusKognitifModalLabel">Rumus Perhitungan Kemampuan Kognitif</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-4">
            <h6>Rumus Dasar:</h6>
            <div class="bg-light p-3 rounded">
              <code>Skor Kognitif = (B - (S/(P-1))) / N × 100</code>
            </div>
          </div>

          <div class="mb-4">
            <h6>Keterangan:</h6>
            <ul>
              <li><strong>B</strong> = Jumlah jawaban benar</li>
              <li><strong>S</strong> = Jumlah jawaban salah</li>
              <li><strong>P</strong> = Rata-rata jumlah pilihan jawaban per soal</li>
              <li><strong>N</strong> = Total jumlah soal</li>
            </ul>
          </div>

          <div class="mb-4">
            <h6>Klasifikasi Kemampuan:</h6>
            <ul>
              <li><span class="badge bg-success">Sangat Tinggi</span>: 80% - 100%</li>
              <li><span class="badge bg-info">Tinggi</span>: 60% - 80%</li>
              <li><span class="badge bg-warning">Rata-rata (Sedang)</span>: 40% - 60%</li>
              <li><span class="badge bg-orange text-white">Rendah</span>: 20% - 40%</li>
              <li><span class="badge bg-danger">Sangat Rendah</span>: 0% - 20%</li>
            </ul>
          </div>

          <div class="bg-info-subtle p-3 rounded">
            <small>
              <strong>Catatan:</strong> Rumus ini menggunakan koreksi untuk faktor menebak,
              sehingga memberikan estimasi yang lebih akurat tentang kemampuan kognitif siswa dalam memahami materi ujian.
            </small>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
        </div>
      </div>
    </div>
  </div>

  <!-- BARU: Card Interpretasi Kemampuan Kognitif untuk Guru -->
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-transparent border-0">
      <h5 class="mb-0">
        <i class="bi bi-lightbulb"></i> Interpretasi & Rekomendasi Pembelajaran
      </h5>
    </div>
    <div class="card-body">
      <div class="row">
        <div class="col-md-8">
          <p class="mb-2">
            <strong>Analisis Kemampuan Kognitif:</strong>
          </p>
          <p class="text-muted mb-3">
            Siswa <strong><?= esc($hasil['nama_lengkap']) ?></strong> memiliki kemampuan kognitif
            <strong class="<?= $klasifikasiKognitif['class'] ?>"><?= $klasifikasiKognitif['kategori'] ?></strong>
            dengan skor <strong><?= $kemampuanKognitif['skor'] ?>%</strong> dalam mata pelajaran
            <strong><?= esc($hasil['nama_jenis']) ?></strong>.
          </p>

          <?php if ($kemampuanKognitif['skor'] > 80): ?>
            <div class="alert alert-success">
              <i class="bi bi-trophy"></i>
              <strong>Rekomendasi:</strong> Siswa menunjukkan pemahaman yang sangat baik.
              Berikan tantangan lebih lanjut dengan soal-soal aplikatif dan analisis tingkat tinggi.
            </div>
          <?php elseif ($kemampuanKognitif['skor'] > 60): ?>
            <div class="alert alert-info">
              <i class="bi bi-star"></i>
              <strong>Rekomendasi:</strong> Kemampuan siswa sudah baik.
              Fokuskan pada pendalaman materi dan latihan soal dengan variasi yang lebih kompleks.
            </div>
          <?php elseif ($kemampuanKognitif['skor'] > 40): ?>
            <div class="alert alert-warning">
              <i class="bi bi-lightbulb"></i>
              <strong>Rekomendasi:</strong> Perlu perbaikan dalam pemahaman konsep dasar.
              Berikan penjelasan ulang materi dengan pendekatan yang berbeda dan latihan tambahan.
            </div>
          <?php else: ?>
            <div class="alert alert-danger">
              <i class="bi bi-book"></i>
              <strong>Rekomendasi:</strong> Kemampuan memerlukan perhatian khusus.
              Lakukan remedial pembelajaran dengan pendekatan individual dan evaluasi ulang metode pengajaran.
            </div>
          <?php endif; ?>
        </div>
        <div class="col-md-4">
          <div class="border-start ps-4">
            <h6 class="text-muted">Indikator Performa:</h6>
            <div class="mb-2">
              <small class="text-muted">Persentase Benar:</small>
              <div class="progress mb-1" style="height: 20px;">
                <div class="progress-bar bg-success" role="progressbar"
                  style="width: <?= round(($jawabanBenar / $totalSoal) * 100) ?>%">
                  <?= round(($jawabanBenar / $totalSoal) * 100) ?>%
                </div>
              </div>
            </div>
            <div class="mb-2">
              <small class="text-muted">Skor Kognitif:</small>
              <div class="progress mb-1" style="height: 20px;">
                <div class="progress-bar <?= $klasifikasiKognitif['bg_class'] ?>" role="progressbar"
                  style="width: <?= $kemampuanKognitif['skor'] ?>%">
                  <?= $kemampuanKognitif['skor'] ?>%
                </div>
              </div>
            </div>
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
        <i class="bi bi-info-circle"></i> Info Kolom
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
            <th>Kode Soal</th>
            <th>ID Soal</th>
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
              <td class="fw-bold text-primary"><?= esc($jawaban['kode_soal']) ?></td>
              <td><?= $jawaban['soal_id'] ?></td>
              <td><?= number_format($jawaban['tingkat_kesulitan'], 3) ?></td>
              <td><?= $jawaban['jawaban_siswa'] ?></td>
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

  <!-- Grafik Perkembangan (tetap sama seperti sebelumnya) -->
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

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    // Data untuk grafik theta dan SE
    const thetaData = <?= json_encode(array_map(function ($item) {
                        return $item['theta_saat_ini'];
                      }, $detailJawaban)) ?>;

    const seData = <?= json_encode(array_map(function ($item) {
                      return $item['se_saat_ini'];
                    }, $detailJawaban)) ?>;

    const labels = <?= json_encode(array_map(function ($i) {
                      return 'Soal ' . ($i + 1);
                    }, range(0, count($detailJawaban) - 1))) ?>;

    // Grafik Theta
    new Chart(document.getElementById('thetaChart'), {
      type: 'line',
      data: {
        labels: labels,
        datasets: [{
          label: 'Theta (θ)',
          data: thetaData,
          borderColor: '#4e73df',
          tension: 0.1,
          fill: false
        }]
      },
      options: {
        responsive: true,
        plugins: {
          title: {
            display: true,
            text: 'Perkembangan Estimasi Kemampuan (θ)'
          }
        },
        scales: {
          y: {
            beginAtZero: false,
            title: {
              display: true,
              text: 'Nilai Theta'
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
          tension: 0.1,
          fill: false
        }]
      },
      options: {
        responsive: true,
        plugins: {
          title: {
            display: true,
            text: 'Perkembangan Standard Error'
          }
        },
        scales: {
          y: {
            beginAtZero: false,
            title: {
              display: true,
              text: 'Nilai SE'
            }
          }
        }
      }
    });
  </script>

  <style>
    .bg-orange {
      background-color: #fd7e14 !important;
    }

    .text-orange {
      color: #fd7e14 !important;
    }
  </style>

  <?= $this->endSection() ?>