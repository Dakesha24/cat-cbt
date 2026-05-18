<?= $this->extend('templates/guru/guru_template') ?>

<?= $this->section('content') ?>
<br><br>
<div class="container-fluid py-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h2 class="fw-bold text-primary">Generate Paket</h2>
      <p class="text-muted mb-0">Ujian: <strong><?= esc($ujian['nama_ujian']) ?></strong></p>
    </div>
    <a href="<?= base_url('guru/ujian/' . $ujian['id_ujian'] . '/bank') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
  </div>

  <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i><?= session()->getFlashdata('success') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
  <?php endif; ?>
  <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-exclamation-circle me-2"></i><?= session()->getFlashdata('error') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
  <?php endif; ?>

  <div class="row g-4 mb-4">
    <div class="col-md-4">
      <div class="card border-info"><div class="card-body text-center"><h3 class="text-info mb-0"><?= count($assignedBanks) ?></h3><small>Bank Ter-assign</small></div></div>
    </div>
    <div class="col-md-4">
      <div class="card border-success"><div class="card-body text-center"><h3 class="text-success mb-0"><?= $totalSoal ?></h3><small>Total Soal Tersedia</small></div></div>
    </div>
    <div class="col-md-4">
      <div class="card border-warning"><div class="card-body text-center"><h3 class="text-warning mb-0"><?= count($paketList) ?></h3><small>Paket Sudah Ada</small></div></div>
    </div>
  </div>

  <div class="card shadow-sm">
    <div class="card-header bg-light"><h6 class="mb-0"><i class="bi bi-dice-5 me-2"></i>Generate Paket Baru</h6></div>
    <div class="card-body">
      <?php if (empty($assignedBanks)): ?>
        <div class="alert alert-warning"><i class="bi bi-exclamation-triangle me-2"></i>Belum ada bank yang di-assign. <a href="<?= base_url('guru/ujian/' . $ujian['id_ujian'] . '/bank') ?>">Assign bank dulu</a>.</div>
      <?php else: ?>
        <form method="post" action="<?= base_url('guru/ujian/' . $ujian['id_ujian'] . '/generate-paket/proses') ?>">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label fw-semibold">Jumlah Paket</label>
              <input type="number" name="jumlah_paket" class="form-control" value="3" min="1" max="20" required>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Soal per Paket</label>
              <input type="number" name="soal_per_paket" class="form-control" value="25" min="1" max="100" required>
            </div>
            <div class="col-md-4 d-flex align-items-end">
              <div class="w-100">
                <div class="alert alert-info py-2 mb-2"><small>Dibutuhkan: <strong id="totalDibutuhkanG">75</strong> soal</small></div>
                <button type="submit" class="btn btn-primary w-100" onclick="return confirm('Generate paket? Paket lama akan dihapus.')"><i class="bi bi-lightning me-1"></i>Generate Paket</button>
              </div>
            </div>
          </div>
        </form>
        <script>
        document.querySelectorAll('[name="jumlah_paket"], [name="soal_per_paket"]').forEach(el => {
          el.addEventListener('input', function() {
            const jp = parseInt(document.querySelector('[name="jumlah_paket"]').value) || 0;
            const sp = parseInt(document.querySelector('[name="soal_per_paket"]').value) || 0;
            document.getElementById('totalDibutuhkanG').textContent = jp * sp;
          });
        });
        </script>
      <?php endif; ?>
    </div>
  </div>
</div>
<?= $this->endSection() ?>
