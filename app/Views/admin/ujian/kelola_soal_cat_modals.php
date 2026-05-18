<!-- CAT modals for Admin: Tambah Soal + Edit Soal with Summernote -->
<!-- Global summernote listeners handle init/destroy automatically -->

<!-- ===== MODAL TAMBAH SOAL (CAT) ===== -->
<div class="modal fade" id="tambahSoalModal" tabindex="-1" data-bs-focus="false">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-primary text-white px-4 py-3">
        <h5 class="modal-title fw-semibold"><i class="bi bi-plus-circle me-2"></i>Tambah Soal Baru</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form action="<?= base_url('admin/soal/tambah') ?>" method="post" enctype="multipart/form-data">
        <input type="hidden" name="ujian_id" value="<?= $ujian['id_ujian'] ?>">
        <div class="modal-body px-4 py-4">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label small fw-semibold">Kode Soal <span class="text-danger">*</span></label>
              <input type="text" name="kode_soal" class="form-control" required>
            </div>
            <div class="col-md-4">
              <label class="form-label small fw-semibold">Jawaban Benar <span class="text-danger">*</span></label>
              <select name="jawaban_benar" class="form-select" required>
                <option value="A">A</option><option value="B">B</option><option value="C">C</option><option value="D">D</option><option value="E">E</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label small fw-semibold">Tingkat Kesulitan</label>
              <div class="input-group"><input type="number" name="tingkat_kesulitan" class="form-control" step="0.001" value="0.000" min="-3" max="3" required><span class="input-group-text">-3..+3</span></div>
            </div>
            <div class="col-12">
              <label class="form-label small fw-semibold">Pertanyaan <span class="text-danger">*</span></label>
              <textarea id="pertanyaan_cat_add" name="pertanyaan" class="form-control summernote" required></textarea>
            </div>
            <?php foreach(['a'=>'A','b'=>'B','c'=>'C','d'=>'D','e'=>'E (opsional)'] as $k=>$l): ?>
            <div class="col-md-6">
              <label class="form-label small fw-semibold">Pilihan <?= $l ?></label>
              <textarea id="pilihan_<?= $k ?>_cat_add" name="pilihan_<?= $k ?>" class="form-control summernote-sm" <?= $k!=='e'?'required':'' ?>></textarea>
            </div>
            <?php endforeach; ?>
            <div class="col-md-3">
              <label class="form-label small fw-semibold">Diskriminasi (a)</label>
              <input type="number" name="a" class="form-control" step="0.001" value="1.000">
            </div>
            <div class="col-md-3">
              <label class="form-label small fw-semibold">Guessing (c)</label>
              <input type="number" name="c" class="form-control" step="0.001" value="0.000">
            </div>
            <div class="col-md-3">
              <label class="form-label small fw-semibold">Variabel</label>
              <select name="variabel_id" class="form-select" onchange="loadIndikatorCat(this.value,'indikator_cat_add')">
                <option value="">-- Tidak ada --</option>
                <?php foreach ($variabel as $v): ?><option value="<?= $v['variabel_id'] ?>"><?= esc($v['nama_variabel']) ?></option><?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label small fw-semibold">Indikator</label>
              <select name="indikator_id" id="indikator_cat_add" class="form-select"><option value="">-- Pilih Variabel dulu --</option></select>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-semibold">Materi</label>
              <select name="materi_id" class="form-select">
                <option value="">-- Tidak ada --</option>
                <?php foreach ($materi as $m): ?><option value="<?= $m['materi_id'] ?>"><?= esc($m['nama_materi']) ?></option><?php endforeach; ?>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label small fw-semibold">Pembahasan</label>
              <textarea id="pembahasan_cat_add" name="pembahasan" class="form-control summernote"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer border-0 bg-light px-4 py-3">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary px-4">Simpan Soal</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ===== MODAL EDIT SOAL (CAT) ===== -->
<?php if (!empty($soal)): foreach ($soal as $s): ?>
<div class="modal fade" id="editSoalCatModal<?= $s['soal_id'] ?>" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-warning text-dark px-4 py-3">
        <h5 class="modal-title fw-semibold"><i class="bi bi-pencil me-2"></i>Edit Soal</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="<?= base_url('admin/soal/edit/' . $s['soal_id']) ?>" method="post" enctype="multipart/form-data">
        <input type="hidden" name="ujian_id" value="<?= $ujian['id_ujian'] ?>">
        <div class="modal-body px-4 py-4">
          <div class="row g-3">
            <div class="col-md-4"><label class="form-label small fw-semibold">Kode Soal</label><input type="text" name="kode_soal" class="form-control" value="<?= esc($s['kode_soal']) ?>" required></div>
            <div class="col-md-4"><label class="form-label small fw-semibold">Jawaban</label><select name="jawaban_benar" class="form-select"><?php foreach(['A','B','C','D','E'] as $j): ?><option value="<?= $j ?>" <?= $s['jawaban_benar']==$j?'selected':'' ?>><?= $j ?></option><?php endforeach; ?></select></div>
            <div class="col-md-4"><label class="form-label small fw-semibold">Kesulitan</label><div class="input-group"><input type="number" name="tingkat_kesulitan" class="form-control" step="0.001" value="<?= $s['tingkat_kesulitan'] ?>" required><span class="input-group-text">-3..+3</span></div></div>
            <div class="col-12"><label class="form-label small fw-semibold">Pertanyaan</label><textarea id="pertanyaan_cat_edit_<?= $s['soal_id'] ?>" name="pertanyaan" class="form-control summernote" required><?= esc($s['pertanyaan']) ?></textarea></div>
            <?php foreach(['a'=>'A','b'=>'B','c'=>'C','d'=>'D','e'=>'E'] as $k=>$l): ?>
            <div class="col-md-6"><label class="form-label small fw-semibold">Pilihan <?= $l ?></label><textarea id="pilihan_<?= $k ?>_cat_edit_<?= $s['soal_id'] ?>" name="pilihan_<?= $k ?>" class="form-control summernote-sm" <?= $k!=='e'?'required':'' ?>><?= esc($s['pilihan_'.$k] ?? '') ?></textarea></div>
            <?php endforeach; ?>
            <div class="col-md-3"><label class="form-label small">a</label><input type="number" name="a" class="form-control" step="0.001" value="<?= $s['a']??1 ?>"></div>
            <div class="col-md-3"><label class="form-label small">c</label><input type="number" name="c" class="form-control" step="0.001" value="<?= $s['c']??0 ?>"></div>
            <div class="col-md-3">
              <label class="form-label small">Variabel</label>
              <select name="variabel_id" class="form-select" onchange="loadIndikatorCat(this.value,'indikator_cat_edit_<?= $s['soal_id'] ?>')">
                <option value="">-- Tidak ada --</option>
                <?php foreach ($variabel as $v): ?><option value="<?= $v['variabel_id'] ?>" <?= ($s['variabel_id']??'')==$v['variabel_id']?'selected':'' ?>><?= esc($v['nama_variabel']) ?></option><?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label small">Indikator</label>
              <select name="indikator_id" id="indikator_cat_edit_<?= $s['soal_id'] ?>" class="form-select">
                <option value="">-- <?= empty($s['variabel_id'])?'Pilih Variabel dulu':'Tidak ada' ?> --</option>
                <?php if(!empty($s['variabel_id'])): foreach($indikator as $ik): if(($ik['variabel_id']??null)==$s['variabel_id']): ?>
                  <option value="<?= $ik['indikator_id'] ?>" <?= ($s['indikator_id']??'')==$ik['indikator_id']?'selected':'' ?>><?= esc($ik['nama_indikator']) ?></option>
                <?php endif; endforeach; endif; ?>
              </select>
            </div>
            <div class="col-md-6"><label class="form-label small">Materi</label><select name="materi_id" class="form-select"><option value="">-- Tidak ada --</option><?php foreach ($materi as $m): ?><option value="<?= $m['materi_id'] ?>" <?= ($s['materi_id']??'')==$m['materi_id']?'selected':'' ?>><?= esc($m['nama_materi']) ?></option><?php endforeach; ?></select></div>
            <div class="col-12"><label class="form-label small">Pembahasan</label><textarea id="pembahasan_cat_edit_<?= $s['soal_id'] ?>" name="pembahasan" class="form-control summernote"><?= esc($s['pembahasan']??'') ?></textarea></div>
          </div>
        </div>
        <div class="modal-footer border-0 bg-light px-4 py-3">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-warning px-4">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php endforeach; endif; ?>

<script>
function loadIndikatorCat(variabelId, targetId) {
  const sel = document.getElementById(targetId);
  if (!sel) return;
  sel.innerHTML = '<option value="">Loading...</option>';
  if (!variabelId) { sel.innerHTML = '<option value="">-- Pilih Variabel dulu --</option>'; return; }
  fetch('<?= base_url('admin/api/indikator-by-variabel/') ?>' + variabelId)
    .then(r => r.json())
    .then(d => {
      let opts = '<option value="">-- Tidak ada --</option>';
      d.forEach(i => { opts += '<option value="'+i.indikator_id+'">'+i.nama_indikator+'</option>'; });
      sel.innerHTML = opts;
    })
    .catch(() => sel.innerHTML = '<option value="">Gagal</option>');
}
</script>
