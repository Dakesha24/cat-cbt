<?= $this->extend('templates/guru/guru_template') ?>
<?= $this->section('content') ?>

<style>
.data-card { border-radius: 12px; overflow: hidden; }
.data-card:hover { box-shadow: 0 8px 24px rgba(0,0,0,0.1) !important; }
.table thead th { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #6c757d; background: #f8f9fa; border-bottom: 2px solid #e9ecef; padding: 0.75rem 1rem; white-space: nowrap; }
.table tbody td { padding: 0.75rem 1rem; font-size: 0.9rem; color: #212529; vertical-align: middle; border-bottom: 1px solid #f3f4f6; }
.table tbody tr:last-child td { border-bottom: 0; }
.table tbody tr:hover td { background: #f8f9fa; }
.count-pill { display: inline-flex; align-items: center; justify-content: center; min-width: 32px; height: 24px; padding: 0 0.5rem; font-size: 0.8rem; font-weight: 600; color: #495057; background: #e9ecef; border-radius: 6px; }
.btn-act { width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center; font-size: 0.85rem; border-radius: 6px; border: 1px solid #dee2e6; background: #fff; color: #6c757d; transition: all 0.15s; }
.btn-act:hover { background: #f8f9fa; border-color: #adb5bd; color: #212529; }
.btn-act-del:hover { background: #fff5f5; border-color: #f5c6cb; color: #dc3545; }
.td-name { font-weight: 600; color: #212529; }
.td-desc { color: #6c757d; font-size: 0.875rem; }
.empty-state { padding: 3rem 1rem; text-align: center; }
.empty-state i { font-size: 2.5rem; color: #adb5bd; display: block; margin-bottom: 0.75rem; }
.empty-state h6 { color: #6c757d; margin-bottom: 0.25rem; }
.empty-state p { font-size: 0.875rem; color: #adb5bd; }
</style>

<div class="container-fluid py-4">

  <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div>
      <h2 class="fw-bold text-dark mb-1">Variabel</h2>
      <p class="text-muted mb-0">Kompetensi atau dimensi yang diukur dalam soal</p>
    </div>
    <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
      <i class="bi bi-plus-lg me-2"></i>Tambah Variabel
    </button>
  </div>

  <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm"><i class="bi bi-check-circle-fill me-2"></i><?= session()->getFlashdata('success') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
  <?php endif; ?>
  <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm"><i class="bi bi-exclamation-triangle-fill me-2"></i><?= session()->getFlashdata('error') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
  <?php endif; ?>
  <?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-warning alert-dismissible fade show border-0 shadow-sm"><ul class="mb-0 ps-3"><?php foreach (session()->getFlashdata('errors') as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?></ul><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
  <?php endif; ?>

  <div class="card border-0 shadow-sm data-card">
    <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between py-3 px-4">
      <span class="fw-semibold text-dark">Daftar Variabel</span>
      <small class="text-muted"><?= count($variabel) ?> data</small>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table mb-0">
          <thead>
            <tr>
              <th style="width:48px">#</th>
              <th>Nama Variabel</th>
              <th>Deskripsi</th>
              <th style="width:100px;text-align:center">Indikator</th>
              <th style="width:80px;text-align:center">Soal</th>
              <th style="width:90px">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($variabel)): ?>
              <tr><td colspan="6"><div class="empty-state"><i class="bi bi-inbox"></i><h6>Belum ada variabel</h6><p>Tambahkan variabel pertama untuk memulai</p></div></td></tr>
            <?php else: ?>
              <?php $no = 1; foreach ($variabel as $v): ?>
                <tr>
                  <td class="text-muted"><?= $no++ ?></td>
                  <td class="td-name"><?= esc($v['nama_variabel']) ?></td>
                  <td class="td-desc"><?= esc($v['deskripsi'] ?? '—') ?></td>
                  <td style="text-align:center"><span class="count-pill"><?= $v['jumlah_indikator'] ?? 0 ?></span></td>
                  <td style="text-align:center"><span class="count-pill"><?= $v['jumlah_soal'] ?? 0 ?></span></td>
                  <td>
                    <div class="d-flex gap-1">
                      <button class="btn-act" onclick="editVariabel('<?= $v['variabel_id'] ?>', '<?= esc($v['nama_variabel'], 'js') ?>', '<?= esc($v['deskripsi'] ?? '', 'js') ?>')" data-bs-toggle="modal" data-bs-target="#modalEdit" title="Edit"><i class="bi bi-pencil"></i></button>
                      <a href="<?= base_url('guru/variabel/hapus/' . $v['variabel_id']) ?>" class="btn-act btn-act-del" onclick="return confirm('Hapus variabel ini? Indikator terkait akan ikut terhapus.')" title="Hapus"><i class="bi bi-trash"></i></a>
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

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-primary text-white px-4 py-3">
        <h5 class="modal-title fw-semibold"><i class="bi bi-plus-circle me-2"></i>Tambah Variabel</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form method="post" action="<?= base_url('guru/variabel/tambah') ?>">
        <div class="modal-body px-4 py-4">
          <div class="mb-3"><label class="form-label small fw-semibold">Nama Variabel <span class="text-danger">*</span></label><input type="text" name="nama_variabel" class="form-control" required maxlength="100" placeholder="Contoh: Pemahaman Konsep"></div>
          <div class="mb-0"><label class="form-label small fw-semibold">Deskripsi</label><textarea name="deskripsi" class="form-control" rows="3" placeholder="Penjelasan singkat..."></textarea></div>
        </div>
        <div class="modal-footer border-0 bg-light px-4 py-3">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-lg me-1"></i>Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="modalEdit" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-warning text-dark px-4 py-3">
        <h5 class="modal-title fw-semibold"><i class="bi bi-pencil me-2"></i>Edit Variabel</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="post" id="formEdit">
        <div class="modal-body px-4 py-4">
          <div class="mb-3"><label class="form-label small fw-semibold">Nama Variabel <span class="text-danger">*</span></label><input type="text" name="nama_variabel" id="edit_nama" class="form-control" required maxlength="100"></div>
          <div class="mb-0"><label class="form-label small fw-semibold">Deskripsi</label><textarea name="deskripsi" id="edit_deskripsi" class="form-control" rows="3"></textarea></div>
        </div>
        <div class="modal-footer border-0 bg-light px-4 py-3">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-warning px-4"><i class="bi bi-check-lg me-1"></i>Update</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function editVariabel(id, nama, deskripsi) {
  document.getElementById('formEdit').action = '<?= base_url('guru/variabel/edit/') ?>' + id;
  document.getElementById('edit_nama').value = nama;
  document.getElementById('edit_deskripsi').value = deskripsi || '';
}
</script>
<?= $this->endSection() ?>
