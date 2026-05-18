<?= $this->extend('templates/guru/guru_template') ?>

<?= $this->section('content') ?>

<?php
$jadwalCAT = array_filter($jadwal ?? [], static fn($item) => ($item['tipe_ujian'] ?? 'CAT') === 'CAT');
$jadwalCBT = array_filter($jadwal ?? [], static fn($item) => ($item['tipe_ujian'] ?? '') === 'CBT');
$jadwalSections = [
  ['type' => 'CAT', 'title' => 'Computer Adaptive Test', 'badge' => 'bg-primary', 'text' => 'primary', 'cardClass' => 'cat-card', 'items' => $jadwalCAT, 'empty' => 'Belum ada jadwal CAT'],
  ['type' => 'CBT', 'title' => 'Computer Based Test', 'badge' => 'bg-success', 'text' => 'success', 'cardClass' => 'cbt-card', 'items' => $jadwalCBT, 'empty' => 'Belum ada jadwal CBT'],
];
?>

<div class="container-fluid py-4">
  <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div>
      <h2 class="fw-bold text-dark mb-1">Jadwal Ujian</h2>
      <p class="text-muted mb-0">Atur jadwal pelaksanaan ujian untuk kelas Anda</p>
    </div>
    <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahJadwal">
      <i class="bi bi-plus-lg me-2"></i>Tambah Jadwal
    </button>
  </div>

  <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
      <i class="bi bi-check-circle-fill me-2"></i><?= session()->getFlashdata('success') ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>
  <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
      <i class="bi bi-exclamation-triangle-fill me-2"></i><?= esc(session()->getFlashdata('error')) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <?php if (!empty($jadwal)): ?>
    <?php foreach ($jadwalSections as $section): ?>
      <div class="section-hd">
        <span class="s-badge <?=esc($section['badge'])?> text-white"><?=esc($section['type'])?></span>
        <span class="s-title"><?=esc($section['title'])?></span>
        <span class="s-count"><?=count($section['items'])?> jadwal</span>
        <div class="s-line"></div>
      </div>

      <?php if (!empty($section['items'])): ?>
        <div class="row g-4 mb-4">
          <?php foreach ($section['items'] as $j): ?>
            <?php $sc = ['belum_mulai'=>'secondary','sedang_berlangsung'=>'success','selesai'=>'dark'][$j['status']]??'secondary'; ?>
            <div class="col-lg-6 col-xl-4">
              <div class="card border-0 shadow-sm h-100 exam-card <?=esc($section['cardClass'])?>">
                <div class="card-body d-flex flex-column p-0">
                  <div class="d-flex align-items-center justify-content-between px-4 pt-4 pb-2">
                    <div class="d-flex flex-wrap gap-2">
                      <span class="badge bg-<?=$sc?> bg-opacity-10 text-<?=$sc?> px-3 py-2"><?=ucwords(str_replace('_',' ',$j['status']))?></span>
                      <span class="badge <?=esc($section['badge'])?> bg-opacity-10 text-<?=esc($section['text'])?> px-3 py-2"><?=esc($section['type'])?></span>
                    </div>
                    <div class="dropdown">
                      <button class="btn btn-icon btn-sm" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></button>
                      <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                        <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#modalEditJadwal<?=$j['jadwal_id']?>"><i class="bi bi-pencil me-2"></i>Edit</button></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="<?=base_url('guru/jadwal-ujian/hapus/'.$j['jadwal_id'])?>" onclick="return confirm('Hapus jadwal?')"><i class="bi bi-trash me-2"></i>Hapus</a></li>
                      </ul>
                    </div>
                  </div>
                  <div class="px-4 pb-3 flex-grow-1">
                    <h5 class="fw-bold mb-1"><?=esc($j['nama_ujian'])?></h5>
                    <div class="d-flex flex-wrap gap-2 mb-2">
                      <small class="text-muted"><i class="bi bi-key me-1"></i><?=esc($j['kode_ujian'])?></small>
                      <small class="text-muted"><i class="bi bi-person-check me-1"></i><?=esc($j['nama_lengkap'])?></small>
                    </div>
                    <p class="text-muted small mb-2">Kelas: <?=esc($j['nama_kelas'] ?? 'Kelas Umum')?></p>
                    <div class="row g-2 text-center mb-3">
                      <div class="col-6"><div class="stat-box"><small>Mulai</small><div class="stat-val"><?=date('d/m/Y H:i',strtotime($j['tanggal_mulai']))?></div></div></div>
                      <div class="col-6"><div class="stat-box"><small>Selesai</small><div class="stat-val"><?=date('d/m/Y H:i',strtotime($j['tanggal_selesai']))?></div></div></div>
                    </div>
                  </div>
                  <div class="px-4 pb-4">
                    <button class="btn btn-outline-primary btn-sm w-100" data-bs-toggle="modal" data-bs-target="#modalEditJadwal<?=$j['jadwal_id']?>"><i class="bi bi-pencil me-1"></i>Edit</button>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div class="empty-sec"><?=esc($section['empty'])?></div>
      <?php endif; ?>
    <?php endforeach; ?>
  <?php else: ?>
    <div class="text-center py-5"><i class="bi bi-calendar-x text-muted" style="font-size:4rem"></i><h5 class="text-muted mt-3">Belum ada jadwal</h5></div>
  <?php endif; ?>
</div>

<!-- ==================== MODAL TAMBAH JADWAL ==================== -->
<div class="modal fade" id="modalTambahJadwal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-primary text-white px-4 py-3">
        <h5 class="modal-title fw-semibold"><i class="bi bi-plus-circle me-2"></i>Tambah Jadwal Ujian</h5>
        <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form action="<?=base_url('guru/jadwal-ujian/tambah')?>" method="post">
        <div class="modal-body px-4 py-4">
          <h6 class="text-uppercase text-muted fw-semibold small mb-3">Informasi Jadwal</h6>
          <div class="row g-3 mb-4">
            <div class="col-md-6">
              <label class="form-label small fw-semibold">Ujian <span class="text-danger">*</span></label>
              <select name="ujian_id" id="gUjianTambah" class="form-select" required>
                <option value="">Pilih Ujian</option>
                <?php if(!empty($ujian_tambah)): foreach($ujian_tambah as $u): ?>
                  <option value="<?=$u['id_ujian']?>"><?=esc($u['nama_ujian'])?> (<?=esc($u['kode_ujian'])?>)</option>
                <?php endforeach; endif; ?>
              </select>
              <div class="form-text" id="gExamInfoTambah">Pilih ujian terlebih dahulu. Kelas jadwal akan mengikuti cakupan ujian.</div>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-semibold">Kelas <span class="text-danger">*</span></label>
              <select name="kelas_id" class="form-select" id="gKelasTambah" required disabled>
                <option value="">Pilih Ujian dulu</option>
              </select>
              <div class="form-text" id="gClassInfoTambah">Kelas akan menyesuaikan sekolah atau kelas yang sudah ditetapkan di ujian.</div>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-semibold">Guru Pengawas <span class="text-danger">*</span></label>
              <select name="guru_id" class="form-select" required>
                <option value="">Pilih Guru</option>
                <?php if(!empty($guru)): foreach($guru as $g): ?>
                  <option value="<?=$g['guru_id']?>"><?=esc($g['nama_lengkap'])?></option>
                <?php endforeach; endif; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-semibold">Mulai <span class="text-danger">*</span></label>
              <input type="datetime-local" name="tanggal_mulai" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-semibold">Selesai <span class="text-danger">*</span></label>
              <input type="datetime-local" name="tanggal_selesai" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-semibold">Kode Akses <span class="text-danger">*</span></label>
              <input type="text" name="kode_akses" class="form-control" required>
            </div>
          </div>

          <h6 class="text-uppercase text-muted fw-semibold small mb-3">Peserta</h6>
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label small fw-semibold">Tipe Peserta</label>
              <div class="d-flex gap-3">
                <div class="form-check">
                  <input class="form-check-input g-tipe" type="radio" name="tipe_penugasan" value="kelas" id="gTipeKls" checked onchange="toggleGPeserta()">
                  <label class="form-check-label" for="gTipeKls"><strong>Seluruh Siswa</strong> <small class="text-muted">(semua siswa)</small></label>
                </div>
                <div class="form-check">
                  <input class="form-check-input g-tipe" type="radio" name="tipe_penugasan" value="individu" id="gTipeInd" onchange="toggleGPeserta()">
                  <label class="form-check-label" for="gTipeInd"><strong>Siswa Tertentu</strong> <small class="text-muted">(pilih manual)</small></label>
                </div>
              </div>
            </div>
            <div class="col-12" id="gWrapSiswa" style="display:none">
              <label class="form-label small fw-semibold">Pilih Siswa</label>
              <div class="card border" style="max-height:200px;overflow-y:auto">
                <div class="card-body p-2" id="gListSiswa">
                  <p class="text-muted small text-center py-3">Pilih kelas terlebih dahulu.</p>
                </div>
              </div>
              <div class="form-text">Centang siswa yang akan mengikuti ujian.</div>
            </div>
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

<!-- ==================== MODAL EDIT JADWAL ==================== -->
<?php if(!empty($jadwal)): foreach($jadwal as $j): ?>
<div class="modal fade" id="modalEditJadwal<?=$j['jadwal_id']?>" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-warning text-dark px-4 py-3">
        <h5 class="modal-title fw-semibold"><i class="bi bi-pencil me-2"></i>Edit Jadwal</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="<?=base_url('guru/jadwal-ujian/edit/'.$j['jadwal_id'])?>" method="post">
        <div class="modal-body px-4 py-4">
          <h6 class="text-uppercase text-muted fw-semibold small mb-3">Informasi Jadwal</h6>
          <div class="row g-3 mb-4">
            <div class="col-md-6">
              <label class="form-label small fw-semibold">Ujian</label>
              <select name="ujian_id" class="form-select gUjianEdit" data-jid="<?=$j['jadwal_id']?>" required>
                <?php if(!empty($ujian_edit)): foreach($ujian_edit as $u): ?>
                  <option value="<?=$u['id_ujian']?>" <?=$u['id_ujian']==$j['ujian_id']?'selected':''?>><?=esc($u['nama_ujian'])?></option>
                <?php endforeach; endif; ?>
              </select>
              <div class="form-text" id="gExamInfoEdit<?=$j['jadwal_id']?>">Jadwal mengikuti sekolah dan kelas yang diizinkan oleh ujian.</div>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-semibold">Kelas</label>
              <select name="kelas_id" class="form-select gKelasEdit" id="gKelasEdit<?=$j['jadwal_id']?>" data-selected="<?=$j['kelas_id']?>" required>
                <option value="">Pilih Ujian dulu</option>
              </select>
              <div class="form-text" id="gClassInfoEdit<?=$j['jadwal_id']?>">Kelas yang tersedia mengikuti cakupan ujian.</div>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-semibold">Guru Pengawas</label>
              <select name="guru_id" class="form-select" required>
                <?php if(!empty($guru)): foreach($guru as $g): ?>
                  <option value="<?=$g['guru_id']?>" <?=$g['guru_id']==$j['guru_id']?'selected':''?>><?=esc($g['nama_lengkap'])?></option>
                <?php endforeach; endif; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-semibold">Mulai</label>
              <input type="datetime-local" name="tanggal_mulai" class="form-control" value="<?=date('Y-m-d\TH:i',strtotime($j['tanggal_mulai']))?>" required>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-semibold">Selesai</label>
              <input type="datetime-local" name="tanggal_selesai" class="form-control gSelesaiEdit" data-jid="<?=$j['jadwal_id']?>" value="<?=date('Y-m-d\TH:i',strtotime($j['tanggal_selesai']))?>" required>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-semibold">Kode Akses</label>
              <input type="text" name="kode_akses" class="form-control" value="<?=esc($j['kode_akses'])?>" required>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-semibold">Status</label>
              <select name="status" class="form-select gStatusEdit" data-jid="<?=$j['jadwal_id']?>">
                <option value="belum_mulai" <?=$j['status']=='belum_mulai'?'selected':''?>>Belum Mulai</option>
                <option value="sedang_berlangsung" <?=$j['status']=='sedang_berlangsung'?'selected':''?>>Sedang Berlangsung</option>
                <option value="selesai" <?=$j['status']=='selesai'?'selected':''?>>Selesai</option>
              </select>
              <div class="form-text text-danger d-none" id="gStatusInfo<?=$j['jadwal_id']?>">Waktu selesai sudah terlewat. Perpanjang waktu selesai dulu untuk membuka status sedang berlangsung.</div>
            </div>
          </div>

          <h6 class="text-uppercase text-muted fw-semibold small mb-3">Peserta</h6>
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label small fw-semibold">Tipe Peserta</label>
              <div class="d-flex gap-3">
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="tipe_penugasan" value="kelas" id="gTipeKlsE<?=$j['jadwal_id']?>" <?=($j['tipe_penugasan']??'kelas')!='individu'?'checked':''?> onchange="toggleGPesertaE('<?=$j['jadwal_id']?>')">
                  <label class="form-check-label" for="gTipeKlsE<?=$j['jadwal_id']?>"><strong>Seluruh Siswa</strong></label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="tipe_penugasan" value="individu" id="gTipeIndE<?=$j['jadwal_id']?>" <?=($j['tipe_penugasan']??'')=='individu'?'checked':''?> onchange="toggleGPesertaE('<?=$j['jadwal_id']?>')">
                  <label class="form-check-label" for="gTipeIndE<?=$j['jadwal_id']?>"><strong>Siswa Tertentu</strong></label>
                </div>
              </div>
            </div>
            <div class="col-12" id="gWrapSiswaE<?=$j['jadwal_id']?>" style="display:<?=($j['tipe_penugasan']??'')=='individu'?'':'none'?>">
              <label class="form-label small fw-semibold">Pilih Siswa</label>
              <?php $selSiswa = !empty($j['siswa_ids']) ? json_decode($j['siswa_ids'], true) : []; ?>
              <div class="card border" style="max-height:200px;overflow-y:auto">
                <div class="card-body p-2" id="gListSiswaE<?=$j['jadwal_id']?>" data-selected='<?= esc(json_encode(array_values($selSiswa)), 'attr') ?>'>
                  <p class="text-muted small text-center py-3">Pilih kelas terlebih dahulu.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer border-0 bg-light px-4 py-3">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-warning px-4">Update</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php endforeach; endif; ?>

<style>
.exam-card{border-radius:0;overflow:hidden;transition:box-shadow 0.2s;border-left:4px solid #dee2e6}
.exam-card.cat-card{border-left-color:#0d6efd}
.exam-card.cbt-card{border-left-color:#198754}
.exam-card:hover{box-shadow:0 4px 18px rgba(0,0,0,0.10)!important}
.section-hd{display:flex;align-items:center;gap:.6rem;margin-bottom:1rem;margin-top:1.25rem}
.section-hd .s-badge{font-size:.7rem;font-weight:700;padding:.2em .55em;border-radius:3px;letter-spacing:.04em}
.section-hd .s-title{font-size:.9rem;font-weight:600;color:#343a40}
.section-hd .s-count{font-size:.8rem;color:#9ca3af}
.section-hd .s-line{flex:1;border-bottom:1px solid #e9ecef;margin-left:.25rem}
.empty-sec{border:1px dashed #d9e1ea;background:#fbfcfe;color:#6b7280;padding:1rem;text-align:center;margin-bottom:1rem}
.btn-icon{width:34px;height:34px;display:inline-flex;align-items:center;justify-content:center;border:none;background:none;color:#9ca3af;border-radius:4px;font-size:1.1rem;transition:all 0.15s}
.btn-icon:hover{background:#f0f0f0;color:#333}
.modal-content{border-radius:0!important;border:none}
.stat-box{background:#f8f9fa;padding:0.45rem 0.5rem;text-align:center}
.stat-box small{display:block;font-size:0.68rem;color:#9ca3af}
.stat-box .stat-val{font-size:0.78rem;font-weight:600;color:#212529}
@media (max-width:767.98px){
  .section-hd{align-items:flex-start;flex-wrap:wrap}
  .section-hd .s-line{display:none}
}
</style>

<script>
const allSiswaGuru = <?= json_encode($siswa ?? []) ?>;
const guruExamMap = <?= json_encode(array_reduce($ujian_tambah ?? [], static function ($carry, $item) {
  $carry[$item['id_ujian']] = [
    'id' => (int) $item['id_ujian'],
    'nama_ujian' => $item['nama_ujian'],
    'kode_ujian' => $item['kode_ujian'],
    'sekolah_id' => isset($item['sekolah_id']) ? (int) $item['sekolah_id'] : null,
    'sekolah_nama' => $item['nama_sekolah'] ?? null,
    'kelas_id' => isset($item['kelas_id']) ? (int) $item['kelas_id'] : null,
    'kelas_nama' => $item['nama_kelas'] ?? null,
  ];
  return $carry;
}, [])) ?>;
const guruClasses = <?= json_encode(array_map(static function ($item) {
  return [
    'kelas_id' => (int) $item['kelas_id'],
    'sekolah_id' => (int) $item['sekolah_id'],
    'nama_kelas' => $item['nama_kelas'],
    'nama_sekolah' => $item['nama_sekolah'] ?? '',
    'tahun_ajaran' => $item['tahun_ajaran'] ?? '',
  ];
}, $kelas ?? [])) ?>;

function renderGSiswaChecklist(container, kelasId, selectedIds = []){
  if(!container){return;}
  if(!kelasId){
    container.innerHTML='<p class="text-muted small text-center py-3">Pilih kelas terlebih dahulu.</p>';
    return;
  }
  const selected = new Set((selectedIds || []).map(String));
  const filtered = allSiswaGuru.filter(s => String(s.kelas_id) === String(kelasId));
  if(!filtered.length){
    container.innerHTML='<p class="text-muted small text-center py-3">Tidak ada siswa.</p>';
    return;
  }
  container.innerHTML = filtered.map(s => {
    const checked = selected.has(String(s.siswa_id)) ? ' checked' : '';
    return '<label class="d-block py-1"><input type="checkbox" name="siswa_ids[]" value="'+s.siswa_id+'"'+checked+'> '+s.nama_lengkap+' <small class="text-muted">('+s.nomor_peserta+')</small></label>';
  }).join('');
}

function getGuruExam(examId){
  return guruExamMap[String(examId)] || null;
}

function getGuruAllowedClasses(exam){
  if(!exam){
    return [];
  }

  if(exam.kelas_id){
    return guruClasses.filter(item => String(item.kelas_id) === String(exam.kelas_id));
  }

  if(exam.sekolah_id){
    return guruClasses.filter(item => String(item.sekolah_id) === String(exam.sekolah_id));
  }

  return guruClasses.slice();
}

function buildGuruExamSummary(exam){
  if(!exam){
    return 'Pilih ujian terlebih dahulu. Kelas jadwal akan mengikuti cakupan ujian.';
  }
  if(exam.kelas_id){
    return 'Mengikuti ujian: ' + (exam.sekolah_nama || '-') + ' / ' + (exam.kelas_nama || '-') + '. Kelas jadwal terkunci ke kelas ujian.';
  }
  if(exam.sekolah_id){
    return 'Mengikuti ujian: ' + (exam.sekolah_nama || '-') + ' / kelas fleksibel di sekolah ini.';
  }
  return 'Ujian umum. Anda dapat memilih kelas yang Anda ajar.';
}

function setGuruClassOptions(selectEl, exam, selectedClassId = ''){
  if(!selectEl){
    return;
  }

  const classes = getGuruAllowedClasses(exam);
  if(!exam){
    selectEl.innerHTML = '<option value="">Pilih Ujian dulu</option>';
    selectEl.disabled = true;
    return;
  }

  if(!classes.length){
    selectEl.innerHTML = '<option value="">Kelas tidak tersedia</option>';
    selectEl.disabled = true;
    return;
  }

  let options = '<option value="">Pilih Kelas</option>';
  classes.forEach(item => {
    options += '<option value="' + item.kelas_id + '">' + item.nama_kelas + ' (' + item.tahun_ajaran + ')</option>';
  });
  selectEl.innerHTML = options;
  selectEl.disabled = false;

  if(exam.kelas_id){
    selectEl.value = String(exam.kelas_id);
    return;
  }

  if(selectedClassId){
    selectEl.value = String(selectedClassId);
  }
}

function applyGuruExamConstraint(config){
  const exam = getGuruExam(config.examSelect?.value);
  setGuruClassOptions(config.classSelect, exam, config.selectedClassId || '');

  if(config.examInfo){
    config.examInfo.textContent = buildGuruExamSummary(exam);
  }

  if(config.classInfo){
    if(!exam){
      config.classInfo.textContent = 'Kelas akan menyesuaikan sekolah atau kelas yang sudah ditetapkan di ujian.';
    } else if (exam.kelas_id) {
      config.classInfo.textContent = 'Kelas mengikuti ujian dan dikunci ke ' + (exam.kelas_nama || 'kelas ujian') + '.';
    } else if (exam.sekolah_id) {
      config.classInfo.textContent = 'Pilih salah satu kelas yang Anda ajar di ' + (exam.sekolah_nama || 'sekolah ujian') + '.';
    } else {
      config.classInfo.textContent = 'Ujian umum. Pilih salah satu kelas yang Anda ajar.';
    }
  }
}

const guruTambahConfig = {
  examSelect: document.getElementById('gUjianTambah'),
  classSelect: document.getElementById('gKelasTambah'),
  examInfo: document.getElementById('gExamInfoTambah'),
  classInfo: document.getElementById('gClassInfoTambah'),
  selectedClassId: ''
};

guruTambahConfig.examSelect?.addEventListener('change', function(){
  guruTambahConfig.selectedClassId = '';
  applyGuruExamConstraint(guruTambahConfig);
  if(document.getElementById('gTipeInd')?.checked) loadGSiswa();
});

document.querySelectorAll('.gUjianEdit').forEach(function(select){
  const jid = select.dataset.jid;
  const config = {
    examSelect: select,
    classSelect: document.getElementById('gKelasEdit' + jid),
    examInfo: document.getElementById('gExamInfoEdit' + jid),
    classInfo: document.getElementById('gClassInfoEdit' + jid),
    selectedClassId: document.getElementById('gKelasEdit' + jid)?.dataset.selected || ''
  };

  select.addEventListener('change', function(){
    config.selectedClassId = '';
    applyGuruExamConstraint(config);
    if(document.getElementById('gTipeIndE' + jid)?.checked) loadGSiswaEdit(jid);
  });

  applyGuruExamConstraint(config);
});

applyGuruExamConstraint(guruTambahConfig);

function toggleGPeserta(){
  document.getElementById('gWrapSiswa').style.display = document.getElementById('gTipeInd').checked ? '' : 'none';
  if(document.getElementById('gTipeInd').checked) loadGSiswa();
}
function toggleGPesertaE(jid){
  document.getElementById('gWrapSiswaE'+jid).style.display = document.getElementById('gTipeIndE'+jid).checked ? '' : 'none';
  if(document.getElementById('gTipeIndE'+jid).checked) loadGSiswaEdit(jid);
}
function loadGSiswa(){
  const k = document.getElementById('gKelasTambah').value;
  const c = document.getElementById('gListSiswa');
  renderGSiswaChecklist(c, k);
}
function loadGSiswaEdit(jid){
  const k = document.querySelector('#modalEditJadwal'+jid+' select[name="kelas_id"]')?.value;
  const c = document.getElementById('gListSiswaE'+jid);
  let selectedIds = [];
  try {
    selectedIds = JSON.parse(c?.dataset.selected || '[]');
  } catch (e) {
    selectedIds = [];
  }
  renderGSiswaChecklist(c, k, selectedIds);
}
document.getElementById('gKelasTambah')?.addEventListener('change',function(){
  guruTambahConfig.selectedClassId = this.value;
  if(document.getElementById('gTipeInd').checked) loadGSiswa();
});
document.querySelectorAll('.gKelasEdit').forEach(function(el){
  el.addEventListener('change', function(){
    const jid = this.id.replace('gKelasEdit','');
    this.dataset.selected = this.value;
    if(jid && document.getElementById('gTipeIndE'+jid)?.checked) loadGSiswaEdit(jid);
  });
});
document.querySelectorAll('[id^="gWrapSiswaE"]').forEach(function(el){
  const jid = el.id.replace('gWrapSiswaE','');
  if(document.getElementById('gTipeIndE'+jid)?.checked) loadGSiswaEdit(jid);
});

function syncGuruStatusByEndTime(jid){
  const endInput = document.querySelector('.gSelesaiEdit[data-jid="' + jid + '"]');
  const statusSelect = document.querySelector('.gStatusEdit[data-jid="' + jid + '"]');
  const statusInfo = document.getElementById('gStatusInfo' + jid);
  if(!endInput || !statusSelect){ return; }

  const runningOption = statusSelect.querySelector('option[value="sedang_berlangsung"]');
  const isExpired = endInput.value && new Date(endInput.value).getTime() < Date.now();
  if(runningOption){
    runningOption.disabled = isExpired;
  }
  if(statusInfo){
    statusInfo.classList.toggle('d-none', !isExpired);
  }
  if(isExpired && statusSelect.value === 'sedang_berlangsung'){
    statusSelect.value = 'belum_mulai';
  }
}

document.querySelectorAll('.gSelesaiEdit').forEach(function(el){
  const jid = el.dataset.jid;
  syncGuruStatusByEndTime(jid);
  el.addEventListener('change', function(){
    syncGuruStatusByEndTime(jid);
  });
});
</script>

<?= $this->endSection() ?>
