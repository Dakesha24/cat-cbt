<?= $this->extend('templates/guru/guru_template') ?>

<?= $this->section('content') ?>
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-4 py-5">
    <div>
      <h2 class="mb-1">Detail Hasil Ujian</h2>
      <p class="text-muted mb-0"><?= esc($hasil['nama_ujian']) ?> - <?= esc($hasil['nama_jenis']) ?></p>
      <p class="text-muted mb-0">Kode Ujian: <code><?= esc($hasil['kode_ujian']) ?></code></p>
    </div>
    <div>
        <div class="btn-group me-2">
        <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="bi bi-download"></i> Download Hasil
        </button>
        <ul class="dropdown-menu">
          <li><a class="dropdown-item" href="<?= base_url('guru/hasil-ujian/download-excel-html/' . $hasil['peserta_ujian_id']) . (!empty($hasil['attempt_id']) ? '?attempt_id=' . $hasil['attempt_id'] : '') ?>">Excel</a></li>
          <li><a class="dropdown-item" href="<?= base_url('guru/hasil-ujian/download-pdf-html/' . $hasil['peserta_ujian_id']) . (!empty($hasil['attempt_id']) ? '?attempt_id=' . $hasil['attempt_id'] : '') ?>">PDF</a></li>
        </ul>
      </div>
      <a href="<?= esc($backUrl ?? base_url('guru/hasil-ujian/siswa/' . $hasil['jadwal_id'])) ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
      </a>
    </div>
  </div>

  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
      <div class="row">
        <div class="col-md-6">
          <table class="table table-borderless mb-0">
            <tr><td width="180">Nama Siswa</td><td>: <?= esc($hasil['nama_lengkap']) ?></td></tr>
            <tr><td>Nomor Peserta</td><td>: <?= esc($hasil['nomor_peserta']) ?></td></tr>
            <tr><td>Kelas</td><td>: <?= esc($hasil['nama_kelas']) ?></td></tr>
            <tr><td><?= $isCatMode ? 'Theta Akhir (θ)' : 'Nilai Akhir' ?></td><td>: <strong><?= $isCatMode ? number_format((float) $thetaAkhir, 3) : number_format((float) $finalGrade, 2) ?></strong></td></tr>
          </table>
        </div>
        <div class="col-md-6">
          <table class="table table-borderless mb-0">
            <tr><td width="150">Waktu Mulai</td><td>: <?= $hasil['waktu_mulai_format'] ?></td></tr>
            <tr><td>Waktu Selesai</td><td>: <?= $hasil['waktu_selesai_format'] ?></td></tr>
            <tr><td>Total Durasi</td><td>: <?= $hasil['durasi_total_format'] ?></td></tr>
            <tr><td>Rata-rata/Soal</td><td>: <?= $rataRataWaktuFormat ?></td></tr>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div class="row mb-4">
    <div class="col-md-8">
      <div class="card border-0 shadow-sm">
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
    </div>

    <div class="col-md-4">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-header bg-transparent">
          <h5 class="card-title mb-0"><?= $isCatMode ? 'Kemampuan Kognitif' : 'Ringkasan Nilai CBT' ?></h5>
        </div>
        <div class="card-body text-center">
          <h2 class="<?= $isCatMode ? $klasifikasiKognitif['class'] : 'text-primary' ?> mb-2"><?= $isCatMode ? $kemampuanKognitif['skor'] : number_format((float) $finalGrade, 2) ?></h2>
          <?php if ($isCatMode): ?>
            <span class="badge <?= $klasifikasiKognitif['bg_class'] ?> text-white mb-3"><?= $klasifikasiKognitif['kategori'] ?></span>
          <?php endif; ?>
          <div class="text-start small text-muted">
            <div>Benar: <?= $jawabanBenar ?></div>
            <div>Salah: <?= $totalSoal - $jawabanBenar ?></div>
            <div>Total Soal: <?= $totalSoal ?></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-transparent border-0">
      <h5 class="mb-0"><i class="bi bi-lightbulb"></i> Interpretasi</h5>
    </div>
    <div class="card-body">
      <?php if ($isCatMode): ?>
        <p class="text-muted mb-0">
          Siswa <strong><?= esc($hasil['nama_lengkap']) ?></strong> memiliki kemampuan kognitif
          <strong class="<?= $klasifikasiKognitif['class'] ?>"><?= $klasifikasiKognitif['kategori'] ?></strong>
          dengan skor <strong><?= $kemampuanKognitif['skor'] ?></strong>.
        </p>
      <?php else: ?>
        <p class="text-muted mb-0">
          Siswa <strong><?= esc($hasil['nama_lengkap']) ?></strong> menyelesaikan ujian CBT dengan
          nilai akhir <strong><?= number_format((float) $finalGrade, 2) ?></strong> dan
          jawaban benar <strong><?= $jawabanBenar ?></strong> dari <strong><?= $totalSoal ?></strong> soal.
        </p>
      <?php endif; ?>
    </div>
  </div>

  <div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
      <h5 class="card-title mb-0">Detail Jawaban</h5>
      <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#additionalInfoHelp" aria-expanded="false">
        <i class="bi bi-info-circle"></i> Info Kolom
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
            <th>Kode Soal</th>
            <th>ID Soal</th>
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
              <td class="fw-bold text-primary"><?= esc($jawaban['kode_soal']) ?></td>
              <td><?= $jawaban['soal_id'] ?></td>
              <td><?= number_format((float) $jawaban['tingkat_kesulitan'], 3) ?></td>
              <td><?= esc($jawaban['jawaban_siswa']) ?></td>
              <td>
                <?php if ($jawaban['is_correct']): ?>
                  <span class="badge bg-success">Benar</span>
                <?php else: ?>
                  <span class="badge bg-danger">Salah</span>
                <?php endif; ?>
              </td>
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
      const thetaData = <?= json_encode(array_map(static fn($item) => (float) ($item['theta_saat_ini'] ?? 0), $detailJawaban)) ?>;
      const seData = <?= json_encode(array_map(static fn($item) => (float) ($item['se_saat_ini'] ?? 0), $detailJawaban)) ?>;
      const labels = <?= json_encode(array_map(static fn($item) => 'Soal ' . $item['nomor_soal'], $detailJawaban)) ?>;

      new Chart(document.getElementById('thetaChart'), {
        type: 'line',
        data: { labels, datasets: [{ label: 'Theta', data: thetaData, borderColor: '#4e73df', tension: 0.1, fill: false }] },
        options: { responsive: true }
      });

      new Chart(document.getElementById('seChart'), {
        type: 'line',
        data: { labels, datasets: [{ label: 'SE', data: seData, borderColor: '#1cc88a', tension: 0.1, fill: false }] },
        options: { responsive: true }
      });
    </script>
  <?php endif; ?>

  <style>
    .bg-orange { background-color: #fd7e14 !important; }
    .text-orange { color: #fd7e14 !important; }
  </style>
</div>
<?= $this->endSection() ?>
