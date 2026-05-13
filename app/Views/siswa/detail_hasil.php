<?= $this->extend('templates/siswa/siswa_template') ?>
<?= $this->section('content') ?>

<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-4 py-5">
    <h2 class="mb-0">Detail Hasil Ujian</h2>
    <a href="<?= base_url('siswa/hasil') ?>" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left"></i> Kembali
    </a>
  </div>

  <!-- Ringkasan Ujian -->
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
      <div class="row">
        <div class="col-md-8">
          <h4 class="text-primary mb-1"><?= esc($hasil['nama_ujian']) ?></h4>
          <p class="text-muted mb-1"><?= esc($hasil['nama_jenis']) ?></p>
          <!-- TAMBAHAN: Tampilkan kode ujian -->
          <p class="text-muted mb-3"><small><i class="bi bi-hash"></i> <?= esc($hasil['kode_ujian']) ?></small></p>
          <p class="mb-0"><?= esc($hasil['deskripsi']) ?></p>
        </div>
        <div class="col-md-4">
          <div class="border-start ps-4">
            <div class="mb-3">
              <small class="text-muted d-block">Waktu Mulai</small>
              <strong><?= $hasil['waktu_mulai_format'] ?></strong>
            </div>
            <div class="mb-3">
              <small class="text-muted d-block">Waktu Selesai</small>
              <strong><?= $hasil['waktu_selesai_format'] ?></strong>
            </div>
            <div class="mb-3">
              <small class="text-muted d-block">Total Waktu Pengerjaan</small>
              <strong><?= $hasil['durasi_total_format'] ?></strong>
            </div>
            <div>
              <small class="text-muted d-block">Rata-rata per Soal</small>
              <strong><?= $rataRataWaktuFormat ?></strong>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Statistik -->
  <div class="row mb-4">
    <div class="col-md-2">
      <div class="card border-0 shadow-sm">
        <div class="card-body text-center">
          <h3 class="mb-1"><?= $totalSoal ?></h3>
          <small class="text-muted">Total Soal</small>
        </div>
      </div>
    </div>
    <div class="col-md-2">
      <div class="card border-0 shadow-sm">
        <div class="card-body text-center">
          <h3 class="text-success mb-1"><?= $jawabanBenar ?></h3>
          <small class="text-muted">Jawaban Benar</small>
        </div>
      </div>
    </div>
    <div class="col-md-2">
      <div class="card border-0 shadow-sm">
        <div class="card-body text-center">
          <h3 class="text-danger mb-1"><?= $totalSoal - $jawabanBenar ?></h3>
          <small class="text-muted">Jawaban Salah</small>
        </div>
      </div>
    </div>
    <div class="col-md-2">
      <div class="card border-0 shadow-sm">
        <div class="card-body text-center">
          <h3 class="text-primary mb-1"><?= $skor ?></h3>
          <small class="text-muted">Skor Ujian</small>
        </div>
      </div>
    </div>
    <!-- BARU: Kartu Kemampuan Kognitif -->
    <div class="col-md-4">
      <div class="card border-0 shadow-sm">
        <div class="card-body text-center">
          <h3 class="<?= $klasifikasiKognitif['class'] ?> mb-1"><?= $kemampuanKognitif['skor'] ?>%</h3>
          <small class="text-muted d-block">Skor Kognitif</small>
          <span class="badge <?= $klasifikasiKognitif['bg_class'] ?> text-white mt-2">
            <?= $klasifikasiKognitif['kategori'] ?>
          </span>
        </div>
      </div>
    </div>
  </div>

  <!-- BARU: Card Penjelasan Kemampuan Kognitif -->
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-transparent border-0">
      <h5 class="mb-0">
        <i class="bi bi-brain"></i> Analisis Kemampuan Kognitif
      </h5>
    </div>
    <div class="card-body">
      <div class="row">
        <div class="col-md-8">
          <p class="mb-2">
            <strong>Interpretasi Hasil:</strong>
          </p>
          <p class="text-muted mb-3">
            Kemampuan kognitif Anda dalam mata pelajaran ini tergolong
            <strong class="<?= $klasifikasiKognitif['class'] ?>"><?= $klasifikasiKognitif['kategori'] ?></strong>
            dengan skor <strong><?= $kemampuanKognitif['skor'] ?></strong>.
          </p>

          <?php if ($kemampuanKognitif['skor'] > 80): ?>
            <div class="alert alert-success">
              <i class="bi bi-trophy"></i>
              <strong>Excellent!</strong> Anda menunjukkan pemahaman yang sangat baik terhadap materi ini.
            </div>
          <?php elseif ($kemampuanKognitif['skor'] > 60): ?>
            <div class="alert alert-info">
              <i class="bi bi-star"></i>
              <strong>Good Job!</strong> Anda memiliki pemahaman yang baik, terus tingkatkan!
            </div>
          <?php elseif ($kemampuanKognitif['skor'] > 40): ?>
            <div class="alert alert-warning">
              <i class="bi bi-lightbulb"></i>
              <strong>Keep Learning!</strong> Masih ada ruang untuk peningkatan pemahaman.
            </div>
          <?php else: ?>
            <div class="alert alert-danger">
              <i class="bi bi-book"></i>
              <strong>Need More Practice!</strong> Disarankan untuk mempelajari kembali materi ini.
            </div>
          <?php endif; ?>
        </div>
        <div class="col-md-4">
          <div class="border-start ps-4">
            <small class="text-muted d-block">Detail Perhitungan:</small>
            <div class="mt-2">
              <small class="text-muted">Jawaban Benar: <?= $kemampuanKognitif['total_benar'] ?></small><br>
              <small class="text-muted">Jawaban Salah: <?= $kemampuanKognitif['total_salah'] ?></small><br>
              <!-- <small class="text-muted">Rata-rata Pilihan: <?= $kemampuanKognitif['rata_rata_pilihan'] ?></small><br> -->
              <small class="text-muted">Total Soal: <?= $totalSoal ?></small>
            </div>
            <div class="mt-3">
              <!-- <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#rumusModal">
                <i class="bi bi-calculator"></i> Lihat Rumus
              </button> -->
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Rumus Perhitungan -->
  <div class="modal fade" id="rumusModal" tabindex="-1" aria-labelledby="rumusModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="rumusModalLabel">Rumus Perhitungan Skor Kemampuan Kognitif</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-4">
            <h6>Rumus Konversi Skor:</h6>
            <div class="bg-light p-3 rounded text-center">
              <p style="font-size: 1.2rem; margin-bottom: 0;">
                $$ \text{Skor Akhir} (x) = 50 + (16.67 \times \theta) $$
              </p>
            </div>
          </div>

          <div class="mb-4">
            <h6>Keterangan:</h6>
            <ul>
              <li><strong>$x$</strong> = Skor akhir siswa dalam skala 0-100 (kurang lebih).</li>
              <li><strong>$\theta$ (theta)</strong> = Estimasi tingkat kemampuan akhir siswa yang dihitung oleh sistem CAT (Computerized Adaptive Testing). Nilai ini merepresentasikan tingkat kesulitan soal di mana siswa memiliki peluang 50% untuk menjawab dengan benar.</li>
            </ul>
          </div>

          <div class="mb-4">
            <h6>Klasifikasi Kemampuan:</h6>
            <ul class="list-unstyled">
              <li><span class="badge bg-success" style="width: 120px;">Sangat Baik</span> : Skor &ge; 75</li>
              <li><span class="badge bg-info" style="width: 120px;">Baik</span> : 58 &le; Skor &lt; 75</li>
              <li><span class="badge bg-warning" style="width: 120px;">Cukup</span> : 42 &le; Skor &lt; 58</li>
              <li><span class="badge bg-orange text-white" style="width: 120px;">Rendah</span> : 25 &le; Skor &lt; 42</li>
              <li><span class="badge bg-danger" style="width: 120px;">Sangat Rendah</span> : Skor &lt; 25</li>
            </ul>
          </div>

          <div class="bg-info-subtle p-3 rounded">
            <small>
              <strong>Catatan:</strong> Skor ini dihasilkan dari model Teori Respons Butir (Item Response Theory) yang secara adaptif mengukur kemampuan siswa. Semakin tinggi nilai theta, semakin tinggi pula estimasi kemampuan kognitif siswa.
            </small>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Tombol Unduh Laporan -->
  <div class="mb-4">
    <a href="<?= base_url('siswa/hasil/unduh/' . $hasil['peserta_ujian_id']) ?>" class="btn btn-primary" target="_blank">
      <i class="bi bi-download"></i> Unduh Laporan Hasil Ujian
    </a>
  </div>

  <!-- Detail Jawaban -->
  <div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent border-0">
      <h5 class="mb-0">Detail Jawaban</h5>
    </div>
    <div class="table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th>No</th>
            <th>Kode Soal</th>
            <th>Pertanyaan</th>
            <th>Jawaban Anda</th>
            <!-- <th>Jawaban Benar</th> -->
            <th>Status</th>
            <th>Waktu Jawab</th>
            <th>Durasi</th>
            <th>Pembahasan</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($detailJawaban as $i => $jawaban): ?>
            <tr>
              <td><?= $jawaban['nomor_soal'] ?></td>
              <!-- TAMBAHAN: Tampilkan kode soal -->
              <td><small class="text-muted"><?= esc($jawaban['kode_soal']) ?></small></td>
              <td>
                <div style="max-width: 300px; overflow-x: auto;">
                  <?= $jawaban['pertanyaan'] ?>
                </div>
              </td>
              <td><?= $jawaban['jawaban_siswa'] ?></td>
              <!-- <td><?= $jawaban['jawaban_benar'] ?></td> -->
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
                <small class="fw-bold"><?= $jawaban['durasi_pengerjaan_format'] ?></small>
              </td>
              <td>
                <?php if (isset($jawaban['pembahasan']) && !empty($jawaban['pembahasan'])): ?>
                  <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#pembahasanModal<?= $i ?>">
                    Lihat Pembahasan
                  </button>

                  <!-- Modal Pembahasan -->
                  <div class="modal fade" id="pembahasanModal<?= $i ?>" tabindex="-1" aria-labelledby="pembahasanModalLabel<?= $i ?>" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title" id="pembahasanModalLabel<?= $i ?>">Pembahasan Soal #<?= $jawaban['nomor_soal'] ?> (<?= esc($jawaban['kode_soal']) ?>)</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                          <?= $jawaban['pembahasan'] ?>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        </div>
                      </div>
                    </div>
                  </div>
                <?php else: ?>
                  <span class="text-muted">Tidak tersedia</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<style>
  .bg-orange {
    background-color: #fd7e14 !important;
  }

  .text-orange {
    color: #fd7e14 !important;
  }
</style>

<?= $this->endSection() ?>