<?= $this->extend('templates/siswa/siswa_template') ?>

<meta name="robots" content="noindex,nofollow">
<meta http-equiv="cache-control" content="no-cache">
<meta http-equiv="expires" content="0">
<meta http-equiv="pragma" content="no-cache">

<?= $this->section('content') ?>
<div class="container py-4">
  <div class="row">
    <div class="col-12">
      <!-- Info Ujian & Timer -->
      <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h4 class="mb-0"><?= esc($ujian['nama_ujian']) ?></h4>
              <small class="text-muted"><?= esc($ujian['nama_jenis']) ?></small>
              <!-- TAMBAHAN: Tampilkan kode ujian -->
              <div class="mt-1">
                <span class="badge bg-secondary"><?= esc($ujian['kode_ujian']) ?></span>
              </div>
            </div>
            <div class="text-center">
              <h5 class="mb-0">Sisa Waktu</h5>
              <div id="timer" class="h4 mb-0 text-danger">
                <?= floor($sisa_waktu / 3600) ?>:<?= floor(($sisa_waktu % 3600) / 60) ?>:<?= $sisa_waktu % 60 ?>
              </div>
            </div>
            <div class="text-end">
              <h5 class="mb-0">Soal</h5>
              <div class="h4 mb-0"><?= $soal_dijawab + 1  ?></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Soal -->
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="card-title mb-0">Pertanyaan:</h5>
            <!-- TAMBAHAN: Tampilkan kode soal -->
            <span class="badge bg-info"><?= esc($soal['kode_soal']) ?></span>
          </div>

          <!-- PERBAIKAN: Hilangkan esc() untuk pertanyaan agar HTML ditampilkan -->
          <div class="lead mb-4"><?= $soal['pertanyaan'] ?></div>

          <?php if (!empty($soal['foto'])): ?>
            <!-- Tampilkan foto soal jika ada -->
            <div class="text-center mb-4">
              <img src="<?= base_url('uploads/soal/' . $soal['foto']) ?>" alt="Gambar Soal" class="img-fluid" style="max-height: 300px;">
            </div>
          <?php endif; ?>

          <form action="<?= base_url('siswa/ujian/simpan-jawaban') ?>" method="POST">
            <input type="hidden" name="soal_id" value="<?= $soal['soal_id'] ?>">

            <div class="list-group">
              <?php
              $pilihan = [
                'A' => $soal['pilihan_a'],
                'B' => $soal['pilihan_b'],
                'C' => $soal['pilihan_c'],
                'D' => $soal['pilihan_d'],
                'E' => $soal['pilihan_e']
              ];
              foreach ($pilihan as $key => $value):
                // Pengecekan khusus untuk pilihan E
                if ($key === 'E') {
                  // Hapus tag <p> yang hanya berisi whitespace dan <br>
                  $cleanValue = preg_replace('/<p(\s[^>]*)?>(\s|&nbsp;|<br\s*\/?>)*<\/p>/i', '', $value);
                  $cleanValue = trim($cleanValue);

                  // Skip jika setelah dibersihkan, tidak ada konten
                  if (empty($cleanValue)) continue;
                } else {
                  // Untuk pilihan A-D, cek kosong biasa
                  if (empty($value)) continue;
                }
              ?>
                <label class="list-group-item list-group-item-action d-flex">
                  <input class="form-check-input me-2 flex-shrink-0"
                    type="radio"
                    name="jawaban"
                    value="<?= $key ?>"
                    required>
                  <span class="choice-label flex-shrink-0 me-2"><?= $key ?>.</span>
                  <div class="choice-content flex-grow-1"><?= $value ?></div>
                </label>
              <?php endforeach; ?>
            </div>

            <div class="text-end mt-4">
              <button type="submit" class="btn btn-primary btn-lg px-5">
                Jawab
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  // Timer countdown
  let timeLeft = <?= $sisa_waktu ?>;
  const timerElement = document.getElementById('timer');

  const countDown = setInterval(() => {
    timeLeft--;

    const hours = Math.floor(timeLeft / 3600);
    const minutes = Math.floor((timeLeft % 3600) / 60);
    const seconds = timeLeft % 60;

    timerElement.textContent = `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;

    if (timeLeft <= 0) {
      clearInterval(countDown);
      // Redirect ke halaman selesai
      window.location.href = '<?= base_url('siswa/ujian/selesai/' . $ujian['jadwal_id']) ?>';
    }
  }, 1000);

  window.onbeforeunload = function() {
    return "Apakah Anda yakin ingin meninggalkan halaman ini?";
  };

  // Nonaktifkan untuk form submit
  document.querySelector('form').onsubmit = function() {
    window.onbeforeunload = null;
  };
</script>

<!-- CSS tambahan untuk styling konten CKEditor -->
<style>
  /* Styling untuk konten yang dibuat dengan CKEditor */
  .lead {
    line-height: 1.6;
  }

  .lead p {
    margin-bottom: 1rem;
  }

  .lead strong,
  .lead b {
    font-weight: 600;
  }

  .lead em,
  .lead i {
    font-style: italic;
  }

  .lead sub {
    font-size: 0.8em;
    vertical-align: sub;
  }

  .lead sup {
    font-size: 0.8em;
    vertical-align: super;
  }

  .lead table {
    border-collapse: collapse;
    width: 100%;
    margin: 1rem 0;
  }

  .lead table,
  .lead th,
  .lead td {
    border: 1px solid #ddd;
    padding: 8px;
  }

  .lead th {
    background-color: #f2f2f2;
  }

  .lead ul,
  .lead ol {
    margin: 1rem 0;
    padding-left: 2rem;
  }

  /* Styling untuk pilihan jawaban */
  .list-group-item {
    line-height: 1.5;
  }

  .list-group-item strong,
  .list-group-item b {
    font-weight: 600;
  }

  .list-group-item em,
  .list-group-item i {
    font-style: italic;
  }

  .list-group-item sub {
    font-size: 0.9em;
    vertical-align: sub;
  }

  .list-group-item sup {
    font-size: 0.9em;
    vertical-align: super;
  }

  .choice-label {
    font-weight: 600;
    min-width: 20px;
    align-self: flex-start;
    /* Agar label tetap di atas meski konten panjang */
  }

  .choice-content {
    line-height: 1.5;
  }

  .choice-content p:first-child {
    margin-top: 0;
  }

  .choice-content p:last-child {
    margin-bottom: 0;
  }
</style>

<?= $this->endSection() ?>