<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Hasil Ujian - <?= esc($hasil['nama_lengkap']) ?></title>
  <style>
    body { font-family: Arial, sans-serif; font-size: 11px; }
    h1, h2 { margin: 0 0 12px; }
    h1 { font-size: 18px; text-align: center; }
    h2 { font-size: 14px; margin-top: 20px; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
    td, th { border: 1px solid #ccc; padding: 6px; vertical-align: top; }
    th { background: #f2f2f2; text-align: center; }
    .label { width: 180px; background: #fafafa; font-weight: bold; }
    .text-center { text-align: center; }
    .text-success { color: #198754; }
    .text-danger { color: #dc3545; }
    .highlight { font-weight: bold; color: #0d6efd; }
  </style>
</head>
<body>
  <h1>LAPORAN HASIL UJIAN</h1>
  <div style="text-align:center; margin-bottom: 12px;">
    <div><?= esc($hasil['nama_ujian']) ?> - <?= esc($hasil['nama_jenis']) ?></div>
    <div>Kode Ujian: <?= esc($hasil['kode_ujian'] ?? $hasil['kode_akses']) ?></div>
  </div>

  <h2>Informasi Siswa</h2>
  <table>
    <tr>
      <td class="label">Nama Siswa</td>
      <td><?= esc($hasil['nama_lengkap']) ?></td>
      <td class="label">Nomor Peserta</td>
      <td><?= esc($hasil['nomor_peserta']) ?></td>
    </tr>
    <tr>
      <td class="label">Kelas</td>
      <td><?= esc($hasil['nama_kelas']) ?></td>
      <td class="label">Sekolah</td>
      <td><?= esc($hasil['nama_sekolah']) ?></td>
    </tr>
    <tr>
      <td class="label">Waktu Mulai</td>
      <td><?= esc($hasil['waktu_mulai_format']) ?></td>
      <td class="label">Waktu Selesai</td>
      <td><?= esc($hasil['waktu_selesai_format']) ?></td>
    </tr>
    <tr>
      <td class="label">Total Durasi</td>
      <td><?= esc($hasil['durasi_total_format']) ?></td>
      <td class="label">Rata-rata per Soal</td>
      <td><?= esc($rataRataWaktuFormat) ?></td>
    </tr>
  </table>

  <h2>Hasil Akhir</h2>
  <table>
    <tr>
      <td class="label">Total Soal</td>
      <td><?= count($detailJawaban) ?> soal</td>
      <td class="label">Jawaban Benar</td>
      <td><?= $jawabanBenar ?> soal</td>
    </tr>
    <?php if (!empty($isCatMode)): ?>
      <tr>
        <td class="label">Theta Akhir</td>
        <td><?= number_format((float) $lastTheta, 3) ?></td>
        <td class="label">SE Akhir</td>
        <td><?= number_format((float) $seAkhir, 3) ?></td>
      </tr>
    <?php else: ?>
      <tr>
        <td class="label">&#952;_EAP (Theta Final)</td>
        <td><?= number_format((float) ($lastTheta ?? 0), 4) ?></td>
        <td class="label">SEM (Std. Error)</td>
        <td><?= number_format((float) ($seAkhir ?? 0), 4) ?></td>
      </tr>
    <?php endif; ?>
    <tr>
      <td class="label"><?= !empty($isCatMode) ? 'Skor Kognitif' : 'Nilai EAP' ?></td>
      <td class="highlight"><?= number_format((float) $finalScore, 2) ?></td>
      <td class="label">Kategori</td>
      <td><?= esc($klasifikasiKognitif['kategori']) ?></td>
    </tr>
  </table>

  <?php if (!empty($isCatMode)): ?>
    <h2>Analisis Kognitif</h2>
    <table>
      <tr>
        <td class="label">Skor Kognitif</td>
        <td><?= $kemampuanKognitif['skor'] ?></td>
        <td class="label">Kategori</td>
        <td><?= esc($klasifikasiKognitif['kategori']) ?></td>
      </tr>
    </table>
  <?php else: ?>
    <h2>Analisis EAP (IRT 3PL)</h2>
    <table>
      <tr>
        <td class="label">Nilai EAP</td>
        <td class="highlight"><?= number_format((float) $finalScore, 2) ?></td>
        <td class="label">Kategori</td>
        <td><?= esc($klasifikasiKognitif['kategori']) ?></td>
      </tr>
      <tr>
        <td class="label">&#952;_EAP</td>
        <td><?= number_format((float) ($lastTheta ?? 0), 4) ?></td>
        <td class="label">Rumus</td>
        <td>NA = 50 + (10 &#215; &#952;_EAP)</td>
      </tr>
    </table>
  <?php endif; ?>

  <h2>Detail Jawaban</h2>
  <table>
    <thead>
      <tr>
        <th>No</th>
        <th>ID Soal</th>
        <th>Pertanyaan</th>
        <th>Tingkat Kesulitan</th>
        <th>Jawaban Siswa</th>
        <th>Jawaban Benar</th>
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
          <th>Theta</th>
          <th>SE</th>
        <?php endif; ?>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($detailJawaban as $jawaban): ?>
        <tr>
          <td class="text-center"><?= $jawaban['nomor_soal'] ?></td>
          <td class="text-center"><?= $jawaban['soal_id'] ?></td>
          <td><?= esc(strlen($jawaban['pertanyaan']) > 100 ? substr($jawaban['pertanyaan'], 0, 100) . '...' : $jawaban['pertanyaan']) ?></td>
          <td class="text-center"><?= number_format((float) $jawaban['tingkat_kesulitan'], 3) ?></td>
          <td class="text-center"><?= esc($jawaban['jawaban_siswa']) ?></td>
          <td class="text-center"><?= esc($jawaban['jawaban_benar']) ?></td>
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
            <td class="text-center"><?= isset($jawaban['theta_saat_ini']) ? number_format((float) $jawaban['theta_saat_ini'], 3) : '-' ?></td>
            <td class="text-center"><?= isset($jawaban['se_saat_ini']) ? number_format((float) $jawaban['se_saat_ini'], 3) : '-' ?></td>
          <?php endif; ?>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</body>
</html>
