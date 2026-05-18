<?= $this->extend('templates/admin/admin_template') ?>

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
      <p class="text-muted mb-0">Atur jadwal pelaksanaan ujian</p>
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
            <?php
              $statusColor = ['belum_mulai'=>'secondary','sedang_berlangsung'=>'success','selesai'=>'dark'][$j['status']] ?? 'secondary';
              $statusText = str_replace('_',' ', $j['status']);
              $schoolName = $j['nama_sekolah'] ?? 'Sekolah Umum';
              $className = $j['nama_kelas'] ?? 'Kelas Umum';
            ?>
            <div class="col-lg-6 col-xl-4">
              <div class="card border-0 shadow-sm h-100 exam-card <?=esc($section['cardClass'])?>">
                <div class="card-body d-flex flex-column p-0">
                  <div class="d-flex align-items-center justify-content-between px-4 pt-4 pb-2">
                    <div class="d-flex flex-wrap gap-2">
                      <span class="badge bg-<?=$statusColor?> bg-opacity-10 text-<?=$statusColor?> px-3 py-2"><?=ucwords($statusText)?></span>
                      <span class="badge <?=esc($section['badge'])?> bg-opacity-10 text-<?=esc($section['text'])?> px-3 py-2"><?=esc($section['type'])?></span>
                    </div>
                    <div class="dropdown">
                      <button class="btn btn-icon btn-sm" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></button>
                      <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                        <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#modalEditJadwal<?=$j['jadwal_id']?>"><i class="bi bi-pencil me-2"></i>Edit</button></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="<?=base_url('admin/jadwal-ujian/hapus/'.$j['jadwal_id'])?>" onclick="return confirm('Hapus jadwal?')"><i class="bi bi-trash me-2"></i>Hapus</a></li>
                      </ul>
                    </div>
                  </div>
                  <div class="px-4 pb-3 flex-grow-1">
                    <h5 class="fw-bold mb-1"><?=esc($j['nama_ujian'])?></h5>
                    <div class="d-flex flex-wrap gap-2 mb-2">
                      <small class="text-muted"><i class="bi bi-key me-1"></i><?=esc($j['kode_ujian'])?></small>
                      <small class="text-muted"><i class="bi bi-person-check me-1"></i><?=esc($j['nama_lengkap'])?></small>
                    </div>
                    <p class="text-muted small mb-2">
                      <i class="bi bi-building me-1"></i><?=esc($schoolName)?> - <?=esc($className)?>
                    </p>
                    <div class="row g-2 text-center mb-3">
                      <div class="col-6"><div class="stat-box"><small>Mulai</small><div class="stat-val"><?=date('d/m/Y H:i',strtotime($j['tanggal_mulai']))?></div></div></div>
                      <div class="col-6"><div class="stat-box"><small>Selesai</small><div class="stat-val"><?=date('d/m/Y H:i',strtotime($j['tanggal_selesai']))?></div></div></div>
                    </div>
                  </div>
                  <div class="px-4 pb-4">
                    <button class="btn btn-outline-primary btn-sm w-100" data-bs-toggle="modal" data-bs-target="#modalEditJadwal<?=$j['jadwal_id']?>"><i class="bi bi-pencil me-1"></i>Edit Jadwal</button>
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
<?php /*

                <i class="bi bi-building me-1"></i><?=esc($j['nama_sekolah'])?> — <?=esc($j['nama_kelas'])?>
*/ ?>

<!-- ==================== MODAL TAMBAH JADWAL ==================== -->
<div class="modal fade" id="modalTambahJadwal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-primary text-white px-4 py-3">
        <h5 class="modal-title fw-semibold"><i class="bi bi-plus-circle me-2"></i>Tambah Jadwal Ujian</h5>
        <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form action="<?=base_url('admin/jadwal-ujian/tambah')?>" method="post">
        <div class="modal-body px-4 py-4">
          <h6 class="text-uppercase text-muted fw-semibold small mb-3">Informasi Jadwal</h6>
          <div class="row g-3 mb-4">
            <div class="col-md-6">
              <label class="form-label small fw-semibold">Ujian <span class="text-danger">*</span></label>
              <select name="ujian_id" id="jUjianTambah" class="form-select" required>
                <option value="">Pilih Ujian</option>
                <?php if(!empty($ujian_tambah)): foreach($ujian_tambah as $u): ?>
                  <option value="<?=$u['id_ujian']?>"><?=esc($u['nama_ujian'])?> (<?=esc($u['kode_ujian'])?>)</option>
                <?php endforeach; endif; ?>
              </select>
              <div class="form-text" id="jExamInfoTambah">Pilih ujian terlebih dahulu. Sekolah dan kelas jadwal akan mengikuti pengaturan ujian.</div>
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
              <label class="form-label small fw-semibold">Sekolah <span class="text-danger">*</span></label>
              <select id="jSekolahTambah" class="form-select" required>
                <option value="">Pilih Sekolah</option>
                <option value="0">Sekolah Umum</option>
                <?php if(!empty($sekolah)): foreach($sekolah as $s): ?>
                  <option value="<?=$s['sekolah_id']?>"><?=esc($s['nama_sekolah'])?></option>
                <?php endforeach; endif; ?>
              </select>
              <div class="form-text" id="jSchoolInfoTambah">Untuk ujian umum, pilih sekolah dulu baru kelas.</div>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-semibold">Kelas <span class="text-danger">*</span></label>
              <select id="jKelasTambah" name="kelas_id" class="form-select" disabled>
                <option value="">Pilih Sekolah dulu</option>
              </select>
              <div class="form-text" id="jClassInfoTambah">Kelas aktif setelah sekolah atau batas ujian ditentukan.</div>
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
                  <input class="form-check-input j-tipe" type="radio" name="tipe_penugasan" value="kelas" id="jTipeKls" checked onchange="togglePeserta()">
                  <label class="form-check-label" for="jTipeKls"><strong>Seluruh Siswa</strong> <small class="text-muted">(semua siswa)</small></label>
                </div>
                <div class="form-check">
                  <input class="form-check-input j-tipe" type="radio" name="tipe_penugasan" value="individu" id="jTipeInd" onchange="togglePeserta()">
                  <label class="form-check-label" for="jTipeInd"><strong>Siswa Tertentu</strong> <small class="text-muted">(pilih manual)</small></label>
                </div>
              </div>
            </div>
            <div class="col-12" id="jWrapSiswa" style="display:none">
              <label class="form-label small fw-semibold">Pilih Siswa</label>
              <div class="card border" style="max-height:200px;overflow-y:auto">
                <div class="card-body p-2" id="jListSiswa">
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
      <form action="<?=base_url('admin/jadwal-ujian/edit/'.$j['jadwal_id'])?>" method="post">
        <div class="modal-body px-4 py-4">
          <h6 class="text-uppercase text-muted fw-semibold small mb-3">Informasi Jadwal</h6>
          <div class="row g-3 mb-4">
            <div class="col-md-6">
              <label class="form-label small fw-semibold">Ujian</label>
              <select name="ujian_id" class="form-select jUjianEdit" data-jid="<?=$j['jadwal_id']?>" required>
                <?php if(!empty($ujian_edit)): foreach($ujian_edit as $u): ?>
                  <option value="<?=$u['id_ujian']?>" <?=$u['id_ujian']==$j['ujian_id']?'selected':''?>><?=esc($u['nama_ujian'])?></option>
                <?php endforeach; endif; ?>
              </select>
              <div class="form-text" id="jExamInfoEdit<?=$j['jadwal_id']?>">Jadwal mengikuti pengaturan sekolah dan kelas dari ujian yang dipilih.</div>
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
              <label class="form-label small fw-semibold">Sekolah</label>
              <select class="form-select jSekolahEdit" data-jid="<?=$j['jadwal_id']?>" required>
                <option value="">Pilih Sekolah</option>
                <option value="0" <?=empty($j['kelas_id'])?'selected':''?>>Sekolah Umum</option>
                <?php if(!empty($sekolah)): foreach($sekolah as $s): ?>
                  <option value="<?=$s['sekolah_id']?>" <?=(isset($j['sekolah_id'])&&$j['sekolah_id']==$s['sekolah_id'])?'selected':''?>><?=esc($s['nama_sekolah'])?></option>
                <?php endforeach; endif; ?>
              </select>
              <div class="form-text" id="jSchoolInfoEdit<?=$j['jadwal_id']?>">Sekolah akan terkunci bila ujian sudah dibatasi ke sekolah tertentu.</div>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-semibold">Kelas</label>
              <select id="jKelasEdit<?=$j['jadwal_id']?>" name="kelas_id" class="form-select" data-selected="<?=$j['kelas_id'] ?? ''?>">
                <option value="">Pilih Sekolah dulu</option>
              </select>
              <div class="form-text" id="jClassInfoEdit<?=$j['jadwal_id']?>">Kelas akan menyesuaikan sekolah dan batas dari ujian.</div>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-semibold">Mulai</label>
              <input type="datetime-local" name="tanggal_mulai" class="form-control" value="<?=date('Y-m-d\TH:i',strtotime($j['tanggal_mulai']))?>" required>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-semibold">Selesai</label>
              <input type="datetime-local" name="tanggal_selesai" class="form-control jSelesaiEdit" data-jid="<?=$j['jadwal_id']?>" value="<?=date('Y-m-d\TH:i',strtotime($j['tanggal_selesai']))?>" required>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-semibold">Kode Akses</label>
              <input type="text" name="kode_akses" class="form-control" value="<?=esc($j['kode_akses'])?>" required>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-semibold">Status</label>
              <select name="status" class="form-select jStatusEdit" data-jid="<?=$j['jadwal_id']?>">
                <option value="belum_mulai" <?=$j['status']=='belum_mulai'?'selected':''?>>Belum Mulai</option>
                <option value="sedang_berlangsung" <?=$j['status']=='sedang_berlangsung'?'selected':''?>>Sedang Berlangsung</option>
                <option value="selesai" <?=$j['status']=='selesai'?'selected':''?>>Selesai</option>
              </select>
              <div class="form-text text-danger d-none" id="jStatusInfo<?=$j['jadwal_id']?>">Waktu selesai sudah terlewat. Perpanjang waktu selesai dulu untuk membuka status sedang berlangsung.</div>
            </div>
          </div>

          <h6 class="text-uppercase text-muted fw-semibold small mb-3">Peserta</h6>
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label small fw-semibold">Tipe Peserta</label>
              <div class="d-flex gap-3">
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="tipe_penugasan" value="kelas" id="jTipeKlsE<?=$j['jadwal_id']?>" <?=($j['tipe_penugasan']??'kelas')!='individu'?'checked':''?> onchange="togglePesertaE('<?=$j['jadwal_id']?>')">
                  <label class="form-check-label" for="jTipeKlsE<?=$j['jadwal_id']?>"><strong>Seluruh Siswa</strong></label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="tipe_penugasan" value="individu" id="jTipeIndE<?=$j['jadwal_id']?>" <?=($j['tipe_penugasan']??'')=='individu'?'checked':''?> onchange="togglePesertaE('<?=$j['jadwal_id']?>')">
                  <label class="form-check-label" for="jTipeIndE<?=$j['jadwal_id']?>"><strong>Siswa Tertentu</strong></label>
                </div>
              </div>
            </div>
            <div class="col-12" id="jWrapSiswaE<?=$j['jadwal_id']?>" style="display:<?=($j['tipe_penugasan']??'')=='individu'?'':'none'?>">
              <label class="form-label small fw-semibold">Pilih Siswa</label>
              <?php $selSiswa = !empty($j['siswa_ids']) ? json_decode($j['siswa_ids'], true) : []; ?>
              <div class="card border" style="max-height:200px;overflow-y:auto">
                <div class="card-body p-2" id="jListSiswaE<?=$j['jadwal_id']?>" data-selected='<?= esc(json_encode(array_values($selSiswa)), 'attr') ?>'>
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
.jadwal-student-option{display:flex;align-items:flex-start;gap:.55rem;padding:.55rem .65rem;border-bottom:1px solid #eef1f5;cursor:pointer}
.jadwal-student-option:last-child{border-bottom:0}
.jadwal-student-option:hover{background:#f8fafc}
.jadwal-student-option input{margin-top:.18rem;flex:0 0 auto}
.student-option-main{min-width:0;display:flex;flex-direction:column;gap:.12rem}
.student-option-name{font-size:.88rem;font-weight:600;color:#1f2937;line-height:1.25}
.student-option-meta{font-size:.74rem;color:#6b7280;line-height:1.25}
.jadwal-student-option.is-compact{align-items:center;padding:.42rem .55rem}
@media (max-width:767.98px){
  .section-hd{align-items:flex-start;flex-wrap:wrap}
  .section-hd .s-line{display:none}
}
</style>

<script>
const allSiswaAdmin = <?= json_encode($siswa ?? []) ?>;
const adminExamMap = <?= json_encode(array_reduce($ujian_tambah ?? [], static function ($carry, $item) {
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
const adminClasses = <?= json_encode(array_map(static function ($item) {
  return [
    'kelas_id' => (int) $item['kelas_id'],
    'sekolah_id' => (int) $item['sekolah_id'],
    'nama_kelas' => $item['nama_kelas'],
    'nama_sekolah' => $item['nama_sekolah'] ?? '',
    'tahun_ajaran' => $item['tahun_ajaran'] ?? '',
  ];
}, $kelas ?? [])) ?>;

function renderSiswaChecklist(container, kelasId, selectedIds = [], allowAll = false){
  if(!container){return;}
  if(!kelasId && !allowAll){
    container.innerHTML='<p class="text-muted small text-center py-3">Pilih kelas terlebih dahulu.</p>';
    return;
  }
  const selected = new Set((selectedIds || []).map(String));
  const filtered = allowAll && !kelasId
    ? allSiswaAdmin.slice()
    : allSiswaAdmin.filter(s => String(s.kelas_id) === String(kelasId));
  if(!filtered.length){
    container.innerHTML='<p class="text-muted small text-center py-3">Tidak ada siswa yang tersedia.</p>';
    return;
  }
  container.innerHTML = filtered.map(s => {
    const checked = selected.has(String(s.siswa_id)) ? ' checked' : '';
    if(allowAll && !kelasId){
      return '<label class="jadwal-student-option">'
        + '<input type="checkbox" name="siswa_ids[]" value="'+s.siswa_id+'"'+checked+'>'
        + '<span class="student-option-main">'
        + '<span class="student-option-name">'+s.nama_lengkap+' <small class="text-muted">('+s.nomor_peserta+')</small></span>'
        + '<span class="student-option-meta">'+(s.nama_sekolah || 'Sekolah tidak diketahui')+' / '+(s.nama_kelas || 'Kelas tidak diketahui')+'</span>'
        + '</span>'
        + '</label>';
    }

    return '<label class="jadwal-student-option is-compact">'
      + '<input type="checkbox" name="siswa_ids[]" value="'+s.siswa_id+'"'+checked+'>'
      + '<span class="student-option-name">'+s.nama_lengkap+' <small class="text-muted">('+s.nomor_peserta+')</small></span>'
      + '</label>';
  }).join('');
}

function getAdminExam(examId){
  return adminExamMap[String(examId)] || null;
}

function getAdminClassesBySchool(schoolId){
  return adminClasses.filter(item => String(item.sekolah_id) === String(schoolId));
}

function buildAdminExamSummary(exam){
  if(!exam){
    return 'Pilih ujian terlebih dahulu. Sekolah dan kelas jadwal akan mengikuti pengaturan ujian.';
  }
  if(exam.kelas_id){
    return 'Mengikuti ujian: ' + (exam.sekolah_nama || '-') + ' / ' + (exam.kelas_nama || '-') + '. Kelas jadwal terkunci ke kelas ujian.';
  }
  if(exam.sekolah_id){
    return 'Mengikuti ujian: ' + (exam.sekolah_nama || '-') + ' / kelas fleksibel di sekolah ini.';
  }
  return 'Ujian umum. Pilih Sekolah Umum untuk semua sekolah, atau pilih sekolah tertentu lalu kelas.';
}

function setAdminClassOptions(selectEl, schoolId, selectedClassId = '', lockedClassId = null){
  if(!selectEl){
    return;
  }

  if(lockedClassId){
    const lockedClass = adminClasses.find(item => String(item.kelas_id) === String(lockedClassId));
    if(!lockedClass){
      selectEl.innerHTML = '<option value="">Kelas ujian tidak ditemukan</option>';
      selectEl.disabled = true;
      return;
    }

    selectEl.innerHTML = '<option value="' + lockedClass.kelas_id + '">' + lockedClass.nama_kelas + ' (' + lockedClass.tahun_ajaran + ')</option>';
    selectEl.value = String(lockedClass.kelas_id);
    selectEl.disabled = false;
    return;
  }

  if(String(schoolId) === '0'){
    selectEl.innerHTML = '<option value="">Kelas Umum</option>';
    selectEl.value = '';
    selectEl.disabled = false;
    return;
  }

  if(!schoolId){
    selectEl.innerHTML = '<option value="">Pilih Sekolah dulu</option>';
    selectEl.disabled = true;
    return;
  }

  const classes = getAdminClassesBySchool(schoolId);
  let options = '<option value="">Pilih Kelas</option>';
  classes.forEach(item => {
    options += '<option value="' + item.kelas_id + '">' + item.nama_kelas + ' (' + item.tahun_ajaran + ')</option>';
  });

  selectEl.innerHTML = options;
  selectEl.disabled = false;

  if(selectedClassId){
    selectEl.value = String(selectedClassId);
  }
}

function applyAdminExamConstraint(config){
  const exam = getAdminExam(config.examSelect?.value);
  const selectedClassId = config.selectedClassId || '';

  if(config.examInfo){
    config.examInfo.textContent = buildAdminExamSummary(exam);
  }

  if(!exam){
    config.schoolSelect.value = '';
    config.schoolSelect.disabled = false;
    setAdminClassOptions(config.classSelect, '', '');
    if(config.schoolInfo){
      config.schoolInfo.textContent = 'Untuk ujian umum, pilih sekolah dulu baru kelas.';
    }
    if(config.classInfo){
      config.classInfo.textContent = 'Kelas aktif setelah sekolah atau batas ujian ditentukan.';
    }
    return;
  }

  if(exam.kelas_id){
    const schoolId = exam.sekolah_id || '';
    config.schoolSelect.value = schoolId ? String(schoolId) : '';
    config.schoolSelect.disabled = true;
    setAdminClassOptions(config.classSelect, schoolId, exam.kelas_id, exam.kelas_id);
    if(config.schoolInfo){
      config.schoolInfo.textContent = 'Sekolah mengikuti ujian dan tidak dapat diubah dari jadwal.';
    }
    if(config.classInfo){
      config.classInfo.textContent = 'Kelas mengikuti ujian dan dikunci ke ' + (exam.kelas_nama || 'kelas ujian') + '.';
    }
    return;
  }

  if(exam.sekolah_id){
    config.schoolSelect.value = String(exam.sekolah_id);
    config.schoolSelect.disabled = true;
    setAdminClassOptions(config.classSelect, exam.sekolah_id, selectedClassId);
    if(config.schoolInfo){
      config.schoolInfo.textContent = 'Sekolah mengikuti ujian dan tidak dapat diubah dari jadwal.';
    }
    if(config.classInfo){
      config.classInfo.textContent = 'Pilih salah satu kelas di ' + (exam.sekolah_nama || 'sekolah ujian') + '.';
    }
    return;
  }

  config.schoolSelect.disabled = false;
  if(!config.schoolSelect.value && config.initialSchoolId){
    config.schoolSelect.value = String(config.initialSchoolId);
  }
  setAdminClassOptions(config.classSelect, config.schoolSelect.value, selectedClassId);
  if(config.schoolInfo){
    config.schoolInfo.textContent = 'Ujian ini umum. Pilih Sekolah Umum atau sekolah tujuan jadwal.';
  }
  if(config.classInfo){
    config.classInfo.textContent = String(config.schoolSelect.value) === '0'
      ? 'Kelas otomatis menjadi Kelas Umum.'
      : config.schoolSelect.value
      ? 'Pilih kelas dari sekolah yang dipilih.'
      : 'Pilih sekolah dulu agar daftar kelas muncul.';
  }
}

const adminTambahConfig = {
  examSelect: document.getElementById('jUjianTambah'),
  schoolSelect: document.getElementById('jSekolahTambah'),
  classSelect: document.getElementById('jKelasTambah'),
  examInfo: document.getElementById('jExamInfoTambah'),
  schoolInfo: document.getElementById('jSchoolInfoTambah'),
  classInfo: document.getElementById('jClassInfoTambah'),
  initialSchoolId: '',
  selectedClassId: ''
};

adminTambahConfig.examSelect?.addEventListener('change', function(){
  adminTambahConfig.selectedClassId = '';
  applyAdminExamConstraint(adminTambahConfig);
  if(document.getElementById('jTipeInd')?.checked){
    loadSiswaTambah();
  }
});

adminTambahConfig.schoolSelect?.addEventListener('change', function(){
  const exam = getAdminExam(adminTambahConfig.examSelect?.value);
  if(exam && (exam.sekolah_id || exam.kelas_id)){
    return;
  }

  adminTambahConfig.selectedClassId = '';
  setAdminClassOptions(adminTambahConfig.classSelect, this.value, '');
  if(adminTambahConfig.classInfo){
    adminTambahConfig.classInfo.textContent = String(this.value) === '0'
      ? 'Kelas otomatis menjadi Kelas Umum.'
      : this.value
      ? 'Pilih kelas dari sekolah yang dipilih.'
      : 'Pilih sekolah dulu agar daftar kelas muncul.';
  }
  if(document.getElementById('jTipeInd')?.checked){
    loadSiswaTambah();
  }
});

document.querySelectorAll('.jUjianEdit').forEach(function(select){
  const jid = select.dataset.jid;
  const config = {
    examSelect: select,
    schoolSelect: document.querySelector('.jSekolahEdit[data-jid="' + jid + '"]'),
    classSelect: document.getElementById('jKelasEdit' + jid),
    examInfo: document.getElementById('jExamInfoEdit' + jid),
    schoolInfo: document.getElementById('jSchoolInfoEdit' + jid),
    classInfo: document.getElementById('jClassInfoEdit' + jid),
    initialSchoolId: document.querySelector('.jSekolahEdit[data-jid="' + jid + '"]')?.value || '',
    selectedClassId: document.getElementById('jKelasEdit' + jid)?.dataset.selected || ''
  };

  select.addEventListener('change', function(){
    config.selectedClassId = '';
    applyAdminExamConstraint(config);
    if(document.getElementById('jTipeIndE' + jid)?.checked){
      loadSiswaEdit(jid);
    }
  });

  config.schoolSelect?.addEventListener('change', function(){
    const exam = getAdminExam(config.examSelect?.value);
    if(exam && (exam.sekolah_id || exam.kelas_id)){
      return;
    }

    config.selectedClassId = '';
    setAdminClassOptions(config.classSelect, this.value, '');
    if(config.classInfo){
      config.classInfo.textContent = String(this.value) === '0'
        ? 'Kelas otomatis menjadi Kelas Umum.'
        : this.value
        ? 'Pilih kelas dari sekolah yang dipilih.'
        : 'Pilih sekolah dulu agar daftar kelas muncul.';
    }
    if(document.getElementById('jTipeIndE' + jid)?.checked){
      loadSiswaEdit(jid);
    }
  });

  applyAdminExamConstraint(config);
});

applyAdminExamConstraint(adminTambahConfig);

// Toggle peserta (tambah)
function togglePeserta(){
  document.getElementById('jWrapSiswa').style.display = document.getElementById('jTipeInd').checked ? '' : 'none';
  if(document.getElementById('jTipeInd').checked){
    loadSiswaTambah();
  }
}
function togglePesertaE(jid){
  document.getElementById('jWrapSiswaE'+jid).style.display = document.getElementById('jTipeIndE'+jid).checked ? '' : 'none';
  if(document.getElementById('jTipeIndE'+jid).checked){
    loadSiswaEdit(jid);
  }
}

// Load siswa checklist (tambah)
function loadSiswaTambah(){
  const kelasId = document.getElementById('jKelasTambah').value;
  const schoolId = document.getElementById('jSekolahTambah')?.value;
  const container = document.getElementById('jListSiswa');
  renderSiswaChecklist(container, kelasId, [], String(schoolId) === '0');
}

function loadSiswaEdit(jid){
  const kelasId = document.getElementById('jKelasEdit'+jid)?.value;
  const schoolId = document.querySelector('.jSekolahEdit[data-jid="' + jid + '"]')?.value;
  const container = document.getElementById('jListSiswaE'+jid);
  let selectedIds = [];
  try {
    selectedIds = JSON.parse(container?.dataset.selected || '[]');
  } catch (e) {
    selectedIds = [];
  }
  renderSiswaChecklist(container, kelasId, selectedIds, String(schoolId) === '0');
}
document.getElementById('jKelasTambah')?.addEventListener('change',function(){
  adminTambahConfig.selectedClassId = this.value;
  if(document.getElementById('jTipeInd').checked) loadSiswaTambah();
});
document.querySelectorAll('[id^="jKelasEdit"]').forEach(function(el){
  el.addEventListener('change', function(){
    const jid = this.id.replace('jKelasEdit','');
    this.dataset.selected = this.value;
    if(document.getElementById('jTipeIndE'+jid)?.checked) loadSiswaEdit(jid);
  });
});
document.querySelectorAll('[id^="jWrapSiswaE"]').forEach(function(el){
  const jid = el.id.replace('jWrapSiswaE','');
  if(document.getElementById('jTipeIndE'+jid)?.checked){
    loadSiswaEdit(jid);
  }
});

function syncAdminStatusByEndTime(jid){
  const endInput = document.querySelector('.jSelesaiEdit[data-jid="' + jid + '"]');
  const statusSelect = document.querySelector('.jStatusEdit[data-jid="' + jid + '"]');
  const statusInfo = document.getElementById('jStatusInfo' + jid);
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

document.querySelectorAll('.jSelesaiEdit').forEach(function(el){
  const jid = el.dataset.jid;
  syncAdminStatusByEndTime(jid);
  el.addEventListener('change', function(){
    syncAdminStatusByEndTime(jid);
  });
});
</script>

<?= $this->endSection() ?>
