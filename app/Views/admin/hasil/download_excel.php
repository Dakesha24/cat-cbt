<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <title>Hasil Ujian - <?= esc($hasil['nama_lengkap']) ?></title>
  <style>
    body {
      font-family: Arial, sans-serif;
      font-size: 11px;
    }

    .header {
      text-align: center;
      margin-bottom: 20px;
      border-bottom: 2px solid #000;
      padding-bottom: 10px;
    }

    .info-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 15px;
    }

    .info-table td {
      padding: 3px 8px;
      border: 1px solid #ccc;
    }

    .info-table .label {
      background-color: #f0f0f0;
      font-weight: bold;
      width: 150px;
    }

    .result-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
    }

    .result-table th,
    .result-table td {
      border: 1px solid #000;
      padding: 5px;
      text-align: center;
    }

    .result-table th {
      background-color: #4CAF50;
      color: white;
      font-weight: bold;
    }

    .correct {
      background-color: #d4edda;
      color: #155724;
    }

    .incorrect {
      background-color: #f8d7da;
      color: #721c24;
    }

    .summary-box {
      background-color: #e8f4fd;
      border: 2px solid #007bff;
      padding: 10px;
      margin: 15px 0;
    }

    .summary-box h3 {
      margin: 0 0 10px 0;
      color: #007bff;
    }

    .cognitive-box {
      background-color: #f8f9fa;
      border: 2px solid #6c757d;
      padding: 10px;
      margin: 15px 0;
    }

    .cognitive-box h3 {
      margin: 0 0 10px 0;
      color: #6c757d;
    }
  </style>
</head>

<body>
  <!-- Header -->
  <div class="header">
    <h1>LAPORAN HASIL UJIAN ADAPTIVE TEST</h1>
    <h2><?= esc($hasil['nama_ujian']) ?></h2>
    <p><strong><?= esc($hasil['nama_jenis']) ?></strong></p>
    <p>Tanggal: <?= date('d/m/Y H:i:s') ?></p>
  </div>

  <!-- Informasi Siswa -->
  <h3>INFORMASI SISWA</h3>
  <table class="info-table">
    <tr>
      <td class="label">Nama Lengkap</td>
      <td><?= esc($hasil['nama_lengkap']) ?></td>
      <td class="label">Waktu Mulai</td>
      <td><?= $hasil['waktu_mulai_format'] ?></td>
    </tr>
    <tr>
      <td class="label">Nomor Peserta</td>
      <td><?= esc($hasil['nomor_peserta']) ?></td>
      <td class="label">Waktu Selesai</td>
      <td><?= $hasil['waktu_selesai_format'] ?></td>
    </tr>
    <tr>
      <td class="label">Kelas</td>
      <td><?= esc($hasil['nama_kelas']) ?></td>
      <td class="label">Total Durasi</td>
      <td><?= $hasil['durasi_total_format'] ?></td>
    </tr>
    <tr>
      <td class="label">Sekolah</td>
      <td><?= esc($hasil['nama_sekolah']) ?></td>
      <td class="label">Rata-rata per Soal</td>
      <td><?= $rataRataWaktuFormat ?></td>
    </tr>
    <tr>
      <td class="label">Guru Pengawas</td>
      <td><?= esc($hasil['nama_guru']) ?></td>
      <td class="label">Kode Ujian</td>
      <td><?= esc($hasil['kode_akses']) ?></td>
    </tr>
  </table>

  <!-- Hasil Akhir -->
  <div class="summary-box">
    <h3>HASIL AKHIR</h3>
    <table class="info-table">
      <tr>
        <td class="label">Theta Akhir (θ)</td>
        <td><strong><?= number_format($lastTheta, 3) ?></strong></td>
        <td class="label">Total Soal</td>
        <td><strong><?= count($detailJawaban) ?> soal</strong></td>
      </tr>
      <tr>
        <td class="label">Skor</td>
        <td><strong style="font-size: 14px; color: #007bff;"><?= number_format($finalScore, 1) ?></strong></td>
        <td class="label">Jawaban Benar</td>
        <td><strong style="color: #28a745;"><?= $jawabanBenar ?> soal</strong></td>
      </tr>
      <tr>
        <td class="label">Nilai (Skala 0-100)</td>
        <td><strong style="font-size: 14px; color: #28a745;"><?= min(100, max(0, round(($finalScore / 100) * 100))) ?></strong></td>
        <td class="label">Persentase Benar</td>
        <td><strong><?= round(($jawabanBenar / count($detailJawaban)) * 100, 1) ?>%</strong></td>
      </tr>
    </table>
  </div>

  <!-- **BARU: Kemampuan Kognitif** -->
  <div class="cognitive-box">
    <h3>ANALISIS KEMAMPUAN KOGNITIF</h3>
    <table class="info-table">
      <tr>
        <td class="label">Skor Kemampuan Kognitif</td>
        <td><strong style="font-size: 14px; color: #6c757d;"><?= $kemampuanKognitif['skor'] ?></strong></td>
        <td class="label">Total Benar</td>
        <td><strong style="color: #28a745;"><?= $kemampuanKognitif['total_benar'] ?> soal</strong></td>
      </tr>
      <tr>
        <td class="label">Kategori</td>
        <td><strong><?= $klasifikasiKognitif['kategori'] ?></strong></td>
        <td class="label">Total Salah</td>
        <td><strong style="color: #dc3545;"><?= $kemampuanKognitif['total_salah'] ?> soal</strong></td>
      </tr>
      <tr>
        <td class="label">Rata-rata Pilihan/Soal</td>
        <td><strong><?= $kemampuanKognitif['rata_rata_pilihan'] ?></strong></td>
        <td class="label">Persentase Benar (Kognitif)</td>
        <td><strong><?= round(($kemampuanKognitif['total_benar'] / count($detailJawaban)) * 100, 1) ?>%</strong></td>
      </tr>
    </table>
    <p style="font-size: 10px; margin: 10px 0 0 0; color: #666;">
      <strong>Formula:</strong> Skor = (B - (S/(P-1))) / N × 100 
      <br>B=Benar, S=Salah, P=Rata-rata pilihan, N=Total soal
    </p>
  </div>

  <!-- Detail Jawaban -->
  <h3>DETAIL JAWABAN</h3>
  <table class="result-table">
    <thead>
      <tr>
        <th>No</th>
        <th>ID Soal</th>
        <th>Pertanyaan</th>
        <th>Tingkat Kesulitan</th>
        <th>Jawaban Siswa</th>
        <th>Jawaban Benar</th>
        <th>Status</th>
        <th>Waktu Jawab</th>
        <th>Durasi</th>
        <th>Theta (θ)</th>
        <th>SE</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($detailJawaban as $i => $jawaban): ?>
        <tr class="<?= $jawaban['is_correct'] ? 'correct' : 'incorrect' ?>">
          <td><?= $jawaban['nomor_soal'] ?></td>
          <td><?= $jawaban['soal_id'] ?></td>
          <td style="text-align: left; max-width: 300px;">
            <?= strlen($jawaban['pertanyaan']) > 100 ? substr(esc($jawaban['pertanyaan']), 0, 100) . '...' : esc($jawaban['pertanyaan']) ?>
            <?php if (!empty($jawaban['foto'])): ?>
              <br><em>[Soal dengan gambar]</em>
            <?php endif; ?>
          </td>
          <td><?= number_format($jawaban['tingkat_kesulitan'], 3) ?></td>
          <td><strong><?= $jawaban['jawaban_siswa'] ?></strong></td>
          <td><strong><?= $jawaban['jawaban_benar'] ?></strong></td>
          <td><?= $jawaban['is_correct'] ? 'BENAR' : 'SALAH' ?></td>
          <td><?= $jawaban['waktu_menjawab_format'] ?></td>
          <td><?= $jawaban['durasi_pengerjaan_format'] ?></td>
          <td><?= number_format($jawaban['theta_saat_ini'], 3) ?></td>
          <td><?= number_format($jawaban['se_saat_ini'], 3) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <!-- Footer -->
  <div style="margin-top: 30px; border-top: 1px solid #ccc; padding-top: 10px; text-align: center; color: #666;">
    <p>Laporan ini dibuat secara otomatis oleh Sistem CD-CAT</p>
    <p>Dicetak pada: <?= date('d/m/Y H:i:s') ?> WIB</p>
  </div>
</body>

</html>