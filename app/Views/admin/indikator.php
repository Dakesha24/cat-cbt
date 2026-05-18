<?= $this->extend('templates/admin/admin_template') ?>
<?= $this->section('content') ?>

<style>
.data-card { border-radius: 12px; overflow: hidden; }
.data-card:hover { box-shadow: 0 8px 24px rgba(0,0,0,0.1) !important; }
.table thead th { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #6c757d; background: #f8f9fa; border-bottom: 2px solid #e9ecef; padding: 0.75rem 1rem; white-space: nowrap; }
.table tbody td { padding: 0.75rem 1rem; font-size: 0.9rem; color: #212529; vertical-align: middle; border-bottom: 1px solid #f3f4f6; }
.table tbody tr:last-child td { border-bottom: 0; }
.table tbody tr:hover td { background: #f8f9fa; }
.var-tag { display: inline-block; padding: 0.2rem 0.6rem; font-size: 0.78rem; font-weight: 600; color: #0d6efd; background: rgba(13,110,253,0.08); border-radius: 6px; white-space: nowrap; }
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

  <!-- Header -->
  <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div>
      <h2 class="fw-bold text-dark mb-1">Indikator</h2>
      <p class="text-muted mb-0">Capaian spesifik dari suatu variabel kompetensi</p>
    </div>
    <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
      <i class="bi bi-plus-lg me-2"></i>Tambah Indikator
    </button>
  </div>

  <!-- Alerts -->
  <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm"><i class="bi bi-check-circle-fill me-2"></i><?= session()->getFlashdata('success') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
  <?php endif; ?>
  <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm"><i class="bi bi-exclamation-triangle-fill me-2"></i><?= session()->getFlashdata('error') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
  <?php endif; ?>
  <?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-warning alert-dismissible fade show border-0 shadow-sm"><ul class="mb-0 ps-3"><?php foreach (session()->getFlashdata('errors') as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?></ul><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
  <?php endif; ?>

  <!-- Table Card -->
  <div class="card border-0 shadow-sm data-card">
    <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between py-3 px-4">
      <span class="fw-semibold text-dark">Daftar Indikator</span>
      <small class="text-muted"><?= count($indikator) ?> data</small>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table mb-0">
          <thead>
            <tr>
              <th style="width:48px">#</th>
              <th style="width:190px">Variabel</th>
              <th>Nama Indikator</th>
              <th>Deskripsi</th>
              <th style="width:90px">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($indikator)): ?>
              <tr><td colspan="5">
                <div class="empty-state">
                  <i class="bi bi-inbox"></i>
                  <h6>Belum ada indikator</h6>
                  <p>Tambahkan indikator pertama untuk memulai</p>
                </div>
              </td></tr>
            <?php else: ?>
              <?php $no = 1; foreach ($indikator as $i): ?>
                <tr>
                  <td class="text-muted"><?= $no++ ?></td>
                  <td><span class="var-tag"><?= esc($i['nama_variabel']) ?></span></td>
                  <td class="td-name"><?= esc($i['nama_indikator']) ?></td>
                  <td class="td-desc"><?= esc($i['deskripsi'] ?? '—') ?></td>
                  <td>
                    <div class="d-flex gap-1">
                      <button class="btn-act"
                        onclick="editIndikator('<?= $i['indikator_id'] ?>', '<?= $i['variabel_id'] ?>', '<?= esc($i['nama_indikator'], 'js') ?>', '<?= esc($i['deskripsi'] ?? '', 'js') ?>')"
                        data-bs-toggle="modal" data-bs-target="#modalEdit" title="Edit">
                        <i class="bi bi-pencil"></i>
                      </button>
                      <a href="<?= base_url('admin/indikator/hapus/' . $i['indikator_id']) ?>"
                        class="btn-act btn-act-del"
                        onclick="return confirm('Hapus indikator ini?')" title="Hapus">
                        <i class="bi bi-trash"></i>
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

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-primary text-white px-4 py-3">
        <h5 class="modal-title fw-semibold"><i class="bi bi-plus-circle me-2"></i>Tambah Indikator</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form method="post" action="<?= base_url('admin/indikator/tambah') ?>">
        <div class="modal-body px-4 py-4">
          <div class="mb-3">
            <label class="form-label small fw-semibold">Variabel <span class="text-danger">*</span></label>
            <select name="variabel_id" class="form-select" required>
              <option value="">— Pilih Variabel —</option>
              <?php foreach ($variabel as $v): ?>
                <option value="<?= $v['variabel_id'] ?>"><?= esc($v['nama_variabel']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label small fw-semibold">Nama Indikator <span class="text-danger">*</span></label>
            <input type="text" name="nama_indikator" class="form-control" required maxlength="200" placeholder="Contoh: Mampu menyebutkan definisi">
          </div>
          <div class="mb-0">
            <label class="form-label small fw-semibold">Deskripsi</label>
            <textarea name="deskripsi" class="form-control" rows="2" placeholder="Penjelasan indikator..."></textarea>
          </div>
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
        <h5 class="modal-title fw-semibold"><i class="bi bi-pencil me-2"></i>Edit Indikator</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="post" id="formEdit">
        <div class="modal-body px-4 py-4">
          <div class="mb-3">
            <label class="form-label small fw-semibold">Variabel <span class="text-danger">*</span></label>
            <select name="variabel_id" id="edit_variabel_id" class="form-select" required>
              <option value="">— Pilih Variabel —</option>
              <?php foreach ($variabel as $v): ?>
                <option value="<?= $v['variabel_id'] ?>"><?= esc($v['nama_variabel']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label small fw-semibold">Nama Indikator <span class="text-danger">*</span></label>
            <input type="text" name="nama_indikator" id="edit_nama" class="form-control" required maxlength="200">
          </div>
          <div class="mb-0">
            <label class="form-label small fw-semibold">Deskripsi</label>
            <textarea name="deskripsi" id="edit_deskripsi" class="form-control" rows="2"></textarea>
          </div>
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
function editIndikator(id, variabelId, nama, deskripsi) {
  document.getElementById('formEdit').action = '<?= base_url('admin/indikator/edit/') ?>' + id;
  document.getElementById('edit_variabel_id').value = variabelId;
  document.getElementById('edit_nama').value = nama;
  document.getElementById('edit_deskripsi').value = deskripsi || '';
}
</script>
<?= $this->endSection() ?>
