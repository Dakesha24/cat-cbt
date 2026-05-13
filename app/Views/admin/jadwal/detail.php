<?= $this->extend('templates/admin/admin_template') ?>

<?= $this->section('title') ?>Detail Jadwal Ujian<?= $this->endSection() ?>

<?= $this->section('content') ?>

<br><br><br>
<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <!-- Header Jadwal -->
      <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h4 class="card-title mb-0">
            <i class="fas fa-calendar-check"></i> Detail Jadwal Ujian
          </h4>
          <div>
            <a href="<?= base_url('admin/jadwal') ?>" class="btn btn-secondary me-2">
              <i class="fas fa-arrow-left me-1"></i>Kembali
            </a>
            <?php if ($jadwal['status'] !== 'sedang_berlangsung'): ?>
              <a href="<?= base_url('admin/jadwal/hapus/' . $jadwal['jadwal_id']) ?>"
                class="btn btn-danger"
                onclick="return confirm('Apakah Anda yakin ingin menghapus jadwal ujian ini?\n\nSemua data peserta dan hasil ujian akan ikut terhapus.')">
                <i class="fas fa-trash me-1"></i>Hapus Jadwal
              </a>
            <?php endif; ?>
          </div>
        </div>
        <div class="card-body">
          <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              <?= session()->getFlashdata('success') ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          <?php endif; ?>

          <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <?= session()->getFlashdata('error') ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          <?php endif; ?>

          <!-- Status Badge -->
          <div class="mb-3">
            <?php
            $statusClass = '';
            $statusText = '';
            $statusIcon = '';
            switch ($jadwal['status']) {
              case 'belum_mulai':
                $statusClass = 'bg-warning text-dark';
                $statusText = 'Belum Mulai';
                $statusIcon = 'fas fa-clock';
                break;
              case 'sedang_berlangsung':
                $statusClass = 'bg-success';
                $statusText = 'Sedang Berlangsung';
                $statusIcon = 'fas fa-play';
                break;
              case 'selesai':
                $statusClass = 'bg-secondary';
                $statusText = 'Selesai';
                $statusIcon = 'fas fa-check';
                break;
            }
            ?>
            <span class="badge <?= $statusClass ?> fs-6 p-2">
              <i class="<?= $statusIcon ?> me-1"></i><?= $statusText ?>
            </span>
          </div>

          <div class="row">
            <div class="col-md-6">
              <h6 class="text-primary">Informasi Ujian</h6>
              <table class="table table-borderless">
                <tr>
                  <td><strong>Nama Ujian:</strong></td>
                  <td><?= esc($jadwal['nama_ujian']) ?></td>
                </tr>
                <tr>
                  <td><strong>Durasi:</strong></td>
                  <td><?= $jadwal['durasi'] ? date('H:i', strtotime($jadwal['durasi'])) . ' jam' : '-' ?></td>
                </tr>
                <tr>
                  <td><strong>Kode Akses:</strong></td>
                  <td><code class="bg-light p-1 rounded fs-6"><?= esc($jadwal['kode_akses']) ?></code></td>
                </tr>
              </table>
            </div>
          </div>

          <div class="row mt-3">
            <div class="col-12">
              <h6 class="text-info">Jadwal Pelaksanaan</h6>
              <div class="alert alert-info">
                <div class="row">
                  <div class="col-md-6">
                    <strong><i class="fas fa-play me-1"></i>Tanggal & Waktu Mulai:</strong><br>
                    <?= date('l, d F Y - H:i', strtotime($jadwal['tanggal_mulai'])) ?> WIB
                  </div>
                  <div class="col-md-6">
                    <strong><i class="fas fa-stop me-1"></i>Tanggal & Waktu Selesai:</strong><br>
                    <?= date('l, d F Y - H:i', strtotime($jadwal['tanggal_selesai'])) ?> WIB
                  </div>
                </div>
              </div>
            </div>
          </div>

          <?php if (!empty($jadwal['deskripsi'])): ?>
            <div class="mt-3">
              <h6 class="text-secondary">Deskripsi Ujian:</h6>
              <p class="bg-light p-3 rounded"><?= nl2br(esc($jadwal['deskripsi'])) ?></p>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <!-- Daftar Peserta -->
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">
            <i class="fas fa-users"></i> Daftar Peserta (<?= count($peserta) ?>)
          </h5>
          <div class="btn-group">
            <button type="button" class="btn btn-outline-primary btn-sm" onclick="exportPeserta()">
              <i class="fas fa-download me-1"></i>Export
            </button>
            <button type="button" class="btn btn-outline-info btn-sm" onclick="printPeserta()">
              <i class="fas fa-print me-1"></i>Print
            </button>
          </div>
        </div>
        <div class="card-body">
          <?php if (!empty($peserta)): ?>
            <!-- Filter Peserta -->
            <div class="row mb-3">
              <div class="col-md-4">
                <input type="text" class="form-control" id="searchPeserta" placeholder="Cari nama/nis...">
              </div>
              <div class="col-md-3">
                <select class="form-select" id="filterStatusPeserta">
                  <option value="">Semua Status</option>
                  <option value="belum_mulai">Belum Mulai</option>
                  <option value="sedang_mengerjakan">Sedang Mengerjakan</option>
                  <option value="selesai">Selesai</option>
                </select>
              </div>
              <div class="col-md-2">
                <button class="btn btn-outline-secondary" onclick="resetFilterPeserta()">
                  <i class="fas fa-redo me-1"></i>Reset
                </button>
              </div>
            </div>

            <div class="table-responsive">
              <table class="table table-striped table-hover" id="tablePeserta">
                <thead class="table-success">
                  <tr>
                    <th>No</th>
                    <th>NIS</th>
                    <th>Nama Lengkap</th>
                    <th>Username</th>
                    <th>Status</th>
                    <th>Waktu Mulai</th>
                    <th>Waktu Selesai</th>
                    <th>Durasi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($peserta as $index => $p): ?>
                    <tr data-status="<?= $p['status'] ?>">
                      <td><?= $index + 1 ?></td>
                      <td><strong><?= esc($p['nomor_peserta']) ?></strong></td>
                      <td><?= esc($p['nama_lengkap']) ?></td>
                      <td><code><?= esc($p['username']) ?></code></td>
                      <td>
                        <?php
                        $statusPesertaClass = '';
                        $statusPesertaText = '';
                        switch ($p['status']) {
                          case 'belum_mulai':
                            $statusPesertaClass = 'bg-warning text-dark';
                            $statusPesertaText = 'Belum Mulai';
                            break;
                          case 'sedang_mengerjakan':
                            $statusPesertaClass = 'bg-primary';
                            $statusPesertaText = 'Mengerjakan';
                            break;
                          case 'selesai':
                            $statusPesertaClass = 'bg-success';
                            $statusPesertaText = 'Selesai';
                            break;
                        }
                        ?>
                        <span class="badge <?= $statusPesertaClass ?>"><?= $statusPesertaText ?></span>
                      </td>
                      <td>
                        <?= $p['waktu_mulai'] ? date('d/m/Y H:i:s', strtotime($p['waktu_mulai'])) : '-' ?>
                      </td>
                      <td>
                        <?= $p['waktu_selesai'] ? date('d/m/Y H:i:s', strtotime($p['waktu_selesai'])) : '-' ?>
                      </td>
                      <td>
                        <?php if ($p['waktu_mulai'] && $p['waktu_selesai']): ?>
                          <?php
                          $durasi = strtotime($p['waktu_selesai']) - strtotime($p['waktu_mulai']);
                          $jam = floor($durasi / 3600);
                          $menit = floor(($durasi % 3600) / 60);
                          $detik = $durasi % 60;
                          ?>
                          <small class="text-muted">
                            <?= sprintf('%02d:%02d:%02d', $jam, $menit, $detik) ?>
                          </small>
                        <?php else: ?>
                          <span class="text-muted">-</span>
                        <?php endif; ?>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>

            <!-- Statistik Peserta -->
            <div class="row mt-4">
              <div class="col-md-3">
                <div class="card bg-warning text-dark">
                  <div class="card-body text-center">
                    <h4><?= count(array_filter($peserta, fn($p) => $p['status'] === 'belum_mulai')) ?></h4>
                    <p class="mb-0">Belum Mulai</p>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="card bg-primary text-white">
                  <div class="card-body text-center">
                    <h4><?= count(array_filter($peserta, fn($p) => $p['status'] === 'sedang_mengerjakan')) ?></h4>
                    <p class="mb-0">Sedang Mengerjakan</p>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="card bg-success text-white">
                  <div class="card-body text-center">
                    <h4><?= count(array_filter($peserta, fn($p) => $p['status'] === 'selesai')) ?></h4>
                    <p class="mb-0">Selesai</p>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="card bg-info text-white">
                  <div class="card-body text-center">
                    <h4><?= count($peserta) ?></h4>
                    <p class="mb-0">Total Peserta</p>
                  </div>
                </div>
              </div>
            </div>
          <?php else: ?>
            <div class="text-center py-5">
              <i class="fas fa-users fa-3x text-muted mb-3"></i>
              <h5 class="text-muted">Belum ada peserta terdaftar</h5>
              <p class="text-muted">Guru belum menambahkan peserta untuk jadwal ujian ini.</p>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  // Filter peserta
  document.getElementById('searchPeserta')?.addEventListener('keyup', filterPeserta);
  document.getElementById('filterStatusPeserta')?.addEventListener('change', filterPeserta);

  function filterPeserta() {
    const searchText = document.getElementById('searchPeserta').value.toLowerCase();
    const statusFilter = document.getElementById('filterStatusPeserta').value;
    const rows = document.querySelectorAll('#tablePeserta tbody tr');

    rows.forEach(row => {
      const nama = row.cells[2].textContent.toLowerCase();
      const nomor = row.cells[1].textContent.toLowerCase();
      const status = row.getAttribute('data-status');

      const textMatch = !searchText || nama.includes(searchText) || nomor.includes(searchText);
      const statusMatch = !statusFilter || status === statusFilter;

      row.style.display = (textMatch && statusMatch) ? '' : 'none';
    });
  }

  function resetFilterPeserta() {
    document.getElementById('searchPeserta').value = '';
    document.getElementById('filterStatusPeserta').value = '';
    filterPeserta();
  }

  function exportPeserta() {
    // Implementasi export (CSV/Excel)
    const jadwalId = <?= $jadwal['jadwal_id'] ?>;
    const namaUjian = '<?= addslashes($jadwal['nama_ujian']) ?>';

    // Buat CSV content
    let csvContent = "No,Nomor Peserta,Nama Lengkap,Username,Status,Waktu Mulai,Waktu Selesai,Durasi\n";

    const rows = document.querySelectorAll('#tablePeserta tbody tr');
    rows.forEach((row, index) => {
      if (row.style.display !== 'none') {
        const cells = row.querySelectorAll('td');
        const rowData = [];
        cells.forEach(cell => {
          rowData.push('"' + cell.textContent.trim().replace(/"/g, '""') + '"');
        });
        csvContent += rowData.join(',') + '\n';
      }
    });

    // Download CSV
    const blob = new Blob([csvContent], {
      type: 'text/csv;charset=utf-8;'
    });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = `peserta_${namaUjian.replace(/[^a-zA-Z0-9]/g, '_')}_${new Date().toISOString().slice(0,10)}.csv`;
    link.click();
  }

  function printPeserta() {
    // Implementasi print dengan style khusus
    const printWindow = window.open('', '_blank');
    const jadwalInfo = `
        <div style="text-align: center; margin-bottom: 20px;">
            <h2>Daftar Peserta Ujian</h2>
            <h3><?= esc($jadwal['nama_ujian']) ?></h3>
            <p>Kelas: <?= esc($jadwal['nama_kelas']) ?> - <?= esc($jadwal['nama_sekolah']) ?></p>
            <p>Guru: <?= esc($jadwal['nama_guru']) ?></p>
            <p>Jadwal: <?= date('d F Y, H:i', strtotime($jadwal['tanggal_mulai'])) ?> - <?= date('H:i', strtotime($jadwal['tanggal_selesai'])) ?> WIB</p>
        </div>
    `;

    const tableContent = document.getElementById('tablePeserta').outerHTML;

    printWindow.document.write(`
        <html>
            <head>
                <title>Daftar Peserta Ujian</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; }
                    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                    th { background-color: #f2f2f2; font-weight: bold; }
                    .badge { padding: 2px 6px; border-radius: 3px; font-size: 11px; }
                    .bg-warning { background-color: #fff3cd; color: #856404; }
                    .bg-primary { background-color: #cce7ff; color: #004085; }
                    .bg-success { background-color: #d4edda; color: #155724; }
                    code { background-color: #f8f9fa; padding: 2px 4px; border-radius: 3px; }
                </style>
            </head>
            <body>
                ${jadwalInfo}
                ${tableContent}
                <div style="margin-top: 30px; text-align: center; font-size: 12px; color: #666;">
                    Dicetak pada: ${new Date().toLocaleString('id-ID')}
                </div>
            </body>
        </html>
    `);

    printWindow.document.close();
    printWindow.print();
  }
</script>

<style>
  @media print {

    .btn,
    .card-header .btn-group,
    .alert {
      display: none !important;
    }

    .card {
      border: none !important;
      box-shadow: none !important;
    }
  }
</style>

<?= $this->endSection() ?>