<?= $this->extend('templates/admin/admin_template') ?>
<?= $this->section('content') ?>

<style>
.data-card { border-radius: 0; overflow: hidden; }
.table thead th { font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #6c757d; background: #f8f9fa; border-bottom: 2px solid #e9ecef; padding: 0.6rem 0.875rem; white-space: nowrap; }
.table tbody td { padding: 0.6rem 0.875rem; font-size: 0.875rem; color: #212529; vertical-align: middle; border-bottom: 1px solid #f3f4f6; }
.table tbody tr:last-child td { border-bottom: 0; }
.table tbody tr:hover td { background: #f8f9fa; }
.td-name { font-weight: 600; color: #212529; }
.td-desc { color: #6c757d; font-size: 0.8rem; margin-top: 2px; }
.td-meta { font-size: 0.8rem; color: #6c757d; }
.count-pill { display: inline-flex; align-items: center; justify-content: center; min-width: 28px; height: 22px; padding: 0 0.45rem; font-size: 0.78rem; font-weight: 600; color: #495057; background: #e9ecef; border-radius: 4px; }
.empty-state { padding: 2.5rem 1rem; text-align: center; }
.empty-state i { font-size: 2rem; color: #adb5bd; display: block; margin-bottom: 0.5rem; }
.empty-state h6 { color: #6c757d; margin-bottom: 0.2rem; }
.empty-state p { font-size: 0.85rem; color: #adb5bd; }
.breadcrumb { background: none; padding: 0; margin: 0; }
.breadcrumb-item + .breadcrumb-item::before { color: #adb5bd; }
.breadcrumb-item a { color: #6c757d; text-decoration: none; font-size: 0.875rem; }
.breadcrumb-item a:hover { color: #0d6efd; }
.breadcrumb-item.active { font-size: 0.875rem; color: #495057; }
</style>

<div class="container-fluid py-4">

  <div class="d-flex flex-wrap justify-content-between align-items-start mb-4">
    <div>
      <nav aria-label="breadcrumb" class="mb-1">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?= base_url('admin/bank-soal') ?>"><i class="bi bi-journals me-1"></i>Bank Soal</a></li>
          <li class="breadcrumb-item"><a href="<?= base_url('admin/bank-soal/kategori/' . urlencode($kategori)) ?>"><?= $kategori === 'umum' ? 'Umum' : 'Kelas ' . esc($kategori) ?></a></li>
          <li class="breadcrumb-item active"><?= esc($jenisUjian['nama_jenis']) ?></li>
        </ol>
      </nav>
      <h2 class="fw-bold text-dark mb-1"><?= esc($jenisUjian['nama_jenis']) ?></h2>
      <p class="text-muted mb-0">Daftar bank soal — <?= $kategori === 'umum' ? 'kategori umum' : 'Kelas ' . esc($kategori) ?></p>
      <?php if ($kategori !== 'umum' && !empty($kategoriSekolahList)): ?>
        <div class="small text-muted mt-1">Sekolah: <?= esc(implode(', ', array_column($kategoriSekolahList, 'nama_sekolah'))) ?></div>
      <?php endif; ?>
    </div>
    <a href="<?= base_url('admin/bank-soal/kategori/' . urlencode($kategori)) ?>" class="btn btn-outline-secondary btn-sm mt-1">
      <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
  </div>

  <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm"><i class="bi bi-check-circle-fill me-2"></i><?= session()->getFlashdata('success') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
  <?php endif; ?>
  <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm"><i class="bi bi-exclamation-triangle-fill me-2"></i><?= session()->getFlashdata('error') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
  <?php endif; ?>

  <div class="card border-0 shadow-sm data-card">
    <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between py-3 px-4">
      <span class="fw-semibold text-dark">Daftar Bank Soal</span>
      <small class="text-muted"><?= count($ujianList) ?> bank soal</small>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table mb-0">
          <colgroup>
            <col style="width:44px">
            <col style="width:30%">
            <col style="width:60px">
            <col style="width:130px">
            <col style="width:90px">
            <col style="width:170px">
          </colgroup>
          <thead>
            <tr>
              <th>#</th>
              <th>Nama Bank Soal</th>
              <th class="text-center">Soal</th>
              <th>Dibuat oleh</th>
              <th>Tanggal</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($ujianList)): ?>
              <tr><td colspan="6">
                <div class="empty-state">
                  <i class="bi bi-inbox"></i>
                  <h6>Belum ada bank soal</h6>
                  <p>Belum ada bank soal untuk mata pelajaran ini</p>
                </div>
              </td></tr>
            <?php else: ?>
              <?php $no = 1; foreach ($ujianList as $ujian): ?>
                <tr>
                  <td class="text-muted"><?= $no++ ?></td>
                  <td>
                    <div class="td-name"><?= esc($ujian['nama_ujian']) ?></div>
                    <?php if ($kategori !== 'umum' && !empty($ujian['nama_sekolah'])): ?>
                      <div class="td-desc">Sekolah: <?= esc($ujian['nama_sekolah']) ?></div>
                    <?php endif; ?>
                    <?php if (!empty($ujian['deskripsi'])): ?>
                      <div class="td-desc"><?= esc(mb_strimwidth($ujian['deskripsi'], 0, 80, '...')) ?></div>
                    <?php endif; ?>
                  </td>
                  <td class="text-center"><span class="count-pill"><?= $ujian['jumlah_soal'] ?? 0 ?></span></td>
                  <td class="td-meta"><?= esc($ujian['creator_name']) ?></td>
                  <td class="td-meta"><?= date('d/m/Y', strtotime($ujian['created_at'])) ?></td>
                  <td>
                    <div class="d-flex gap-1">
                      <a href="<?= base_url('admin/bank-soal/kategori/' . urlencode($kategori) . '/jenis-ujian/' . $jenisUjian['jenis_ujian_id'] . '/ujian/' . $ujian['bank_ujian_id']) ?>"
                        class="btn btn-primary btn-sm">
                        <i class="bi bi-list-ul me-1"></i>Kelola Soal
                      </a>
                      <a href="<?= base_url('admin/bank-soal/hapus/' . $ujian['bank_ujian_id']) ?>"
                        class="btn btn-danger btn-sm"
                        onclick="return confirm('Hapus bank soal ini?')">
                        <i class="bi bi-trash me-1"></i>Hapus
                      </a>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>

<?= $this->endSection() ?>
