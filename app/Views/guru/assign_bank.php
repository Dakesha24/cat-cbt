<?= $this->extend('templates/guru/guru_template') ?>

<?= $this->section('content') ?>
<br><br>
<div class="container-fluid py-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h2 class="fw-bold text-primary">Assign Bank & Generate Paket</h2>
      <p class="text-muted mb-0">Ujian: <strong><?= esc($ujian['nama_ujian']) ?></strong> — Tipe: <span class="badge <?= ($ujian['tipe_ujian'] ?? 'CAT') == 'CAT' ? 'bg-info' : 'bg-success' ?>"><?= esc($ujian['tipe_ujian'] ?? 'CAT') ?></span></p>
    </div>
    <a href="<?= base_url('guru/soal/' . $ujian['id_ujian']) ?>" class="btn btn-outline-secondary"><i class="bi bi-list-task me-1"></i>Kelola Soal</a>
  </div>

  <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i><?= session()->getFlashdata('success') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
  <?php endif; ?>
  <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-exclamation-circle me-2"></i><?= session()->getFlashdata('error') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
  <?php endif; ?>

  <div class="row g-4 mb-4">
    <!-- Assign Bank -->
    <div class="col-lg-6">
      <div class="card shadow-sm h-100">
        <div class="card-header bg-light"><h6 class="mb-0"><i class="bi bi-database me-2"></i>Assign Bank Soal</h6></div>
        <div class="card-body">
          <form method="post" action="<?= base_url('guru/ujian/' . $ujian['id_ujian'] . '/bank/sync') ?>">
            <div class="mb-3" style="max-height:250px;overflow-y:auto">
              <?php if (empty($allBanks)): ?>
                <p class="text-muted">Belum ada bank soal.</p>
              <?php else: ?>
                <?php 
                  $assignedIds = array_column((array)$assignedBanks, 'bank_ujian_id');
                  foreach ($allBanks as $bank): 
                    $checked = in_array($bank['bank_ujian_id'], $assignedIds);
                ?>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="bank_ids[]" value="<?= $bank['bank_ujian_id'] ?>" id="bank_<?= $bank['bank_ujian_id'] ?>" <?= $checked ? 'checked' : '' ?>>
                  <label class="form-check-label" for="bank_<?= $bank['bank_ujian_id'] ?>">
                    <strong><?= esc($bank['nama_ujian']) ?></strong>
                    <small class="text-muted d-block"><?= esc($bank['kategori'] ?? '') ?></small>
                  </label>
                </div>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
            <button type="submit" class="btn btn-primary w-100"><i class="bi bi-save me-1"></i>Simpan Assignment</button>
          </form>
        </div>
      </div>
    </div>

    <!-- Generate Paket (INLINE) -->
    <div class="col-lg-6">
      <div class="card shadow-sm h-100">
        <div class="card-header bg-light"><h6 class="mb-0"><i class="bi bi-dice-5 me-2"></i>Generate Paket</h6></div>
        <div class="card-body">
          <?php if (empty($assignedBanks)): ?>
            <div class="alert alert-warning mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Assign bank dulu di sebelah kiri, lalu generate paket di sini.</div>
          <?php else: ?>
            <div class="row text-center mb-3">
              <div class="col-6"><div class="bg-light rounded p-2"><strong class="text-success"><?= $totalSoal ?? 0 ?></strong><br><small>Soal Tersedia</small></div></div>
              <div class="col-6"><div class="bg-light rounded p-2"><strong class="text-warning"><?= count($paketList) ?></strong><br><small>Paket Ada</small></div></div>
            </div>
            <form method="post" action="<?= base_url('guru/ujian/' . $ujian['id_ujian'] . '/generate-paket/proses') ?>">
              <div class="row g-2 mb-3">
                <div class="col-6">
                  <label class="form-label small fw-semibold">Jumlah Paket</label>
                  <input type="number" name="jumlah_paket" class="form-control" value="3" min="1" max="20" required>
                </div>
                <div class="col-6">
                  <label class="form-label small fw-semibold">Soal per Paket</label>
                  <input type="number" name="soal_per_paket" class="form-control" value="25" min="1" max="100" required>
                </div>
              </div>
              <div class="alert alert-info py-2 mb-2 small">Dibutuhkan: <strong id="totalDibutuhkanG">75</strong> soal</div>
              <button type="submit" class="btn btn-primary w-100" onclick="return confirm('Generate paket? Paket lama akan dihapus.')"><i class="bi bi-lightning me-1"></i>Generate Paket</button>
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
  </div>

  <!-- Daftar Paket -->
  <div class="card shadow-sm">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
      <h6 class="mb-0"><i class="bi bi-box-seam me-2"></i>Daftar Paket</h6>
      <?php if (!empty($paketList)): ?>
        <a href="<?= base_url('guru/ujian/' . $ujian['id_ujian'] . '/paket/hapus-semua') ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus SEMUA paket?')"><i class="bi bi-trash"></i> Hapus Semua</a>
      <?php endif; ?>
    </div>
    <div class="card-body p-0">
      <?php if (empty($paketList)): ?>
        <div class="text-center py-4 text-muted"><i class="bi bi-inbox display-4 d-block mb-2"></i>Belum ada paket. Assign bank lalu generate.</div>
      <?php else: ?>
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead class="table-light"><tr><th>#</th><th>Nama Paket</th><th>Jumlah Soal</th><th>Aksi</th></tr></thead>
            <tbody>
              <?php foreach ($paketList as $p): ?>
                <tr>
                  <td><?= $p['nomor_paket'] ?></td>
                  <td><strong><?= esc($p['nama_paket']) ?></strong></td>
                  <td><span class="badge bg-secondary"><?= $p['jumlah_soal'] ?? 0 ?> soal</span></td>
                  <td><a href="<?= base_url('guru/ujian/' . $ujian['id_ujian'] . '/paket/' . $p['paket_id'] . '/hapus') ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus paket ini?')"><i class="bi bi-trash"></i></a></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>
<?= $this->endSection() ?>
