<?= $this->extend('templates/guru/guru_template') ?>
<?= $this->section('title') ?>Hasil Ujian Siswa<?= $this->endSection() ?>
<?= $this->section('content') ?>

<?php
$isCbt     = ($ujian['tipe_ujian'] ?? 'CAT') === 'CBT';
$selesai   = array_values(array_filter($hasilSiswa, fn($s) => $s['status'] === 'selesai'));
$mengerjakan = array_values(array_filter($hasilSiswa, fn($s) => $s['status'] === 'sedang_mengerjakan'));
$belumMulai  = array_values(array_filter($hasilSiswa, fn($s) => $s['status'] === 'belum_mulai'));
$skorArr   = array_filter($selesai, fn($s) => $s['skor'] !== null);
$rataRata  = count($skorArr) > 0 ? array_sum(array_column($skorArr, 'skor')) / count($skorArr) : null;
?>

<style>
.hs-wrap{padding:72px 0 48px;background:#f4f6f9;min-height:100vh}
.hs-head{display:flex;flex-wrap:wrap;justify-content:space-between;align-items:flex-start;gap:1rem;margin-bottom:1.25rem}
.hs-title{font-size:1.05rem;font-weight:700;color:#0f172a;margin:0 0 .2rem}
.hs-meta{font-size:.8rem;color:#6b7280}
.hs-type-badge{display:inline-block;font-size:.68rem;font-weight:700;letter-spacing:.06em;padding:.18rem .55rem;border-radius:3px;margin-right:.4rem;vertical-align:middle}
.hs-type-cbt{background:#d1fae5;color:#065f46}
.hs-type-cat{background:#dbeafe;color:#1e40af}

.hs-stats{display:grid;grid-template-columns:repeat(4,1fr);gap:0;border:1px solid #e2e8f0;margin-bottom:1.25rem}
.hs-stat{background:#fff;padding:.9rem 1.1rem;border-right:1px solid #e2e8f0}
.hs-stat:last-child{border-right:none}
.hs-stat-num{font-size:1.65rem;font-weight:700;line-height:1;letter-spacing:-.02em}
.hs-stat-lbl{font-size:.68rem;color:#9ca3af;text-transform:uppercase;letter-spacing:.05em;margin-top:.2rem}
.num-green{color:#16a34a}.num-blue{color:#2563eb}.num-amber{color:#d97706}.num-slate{color:#0f172a}
@media(max-width:575px){.hs-stats{grid-template-columns:repeat(2,1fr)}.hs-stat:nth-child(2){border-right:none}.hs-stat:nth-child(1),.hs-stat:nth-child(2){border-bottom:1px solid #e2e8f0}}

.hs-card{background:#fff;border:1px solid #e2e8f0}
.hs-card-hd{display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:.75rem;padding:.85rem 1.25rem;border-bottom:1px solid #f1f5f9}
.hs-card-title{font-size:.88rem;font-weight:600;color:#0f172a}
.hs-filter{display:flex;flex-wrap:wrap;gap:.5rem;align-items:center}
.hs-filter .form-control,.hs-filter .form-select{font-size:.8rem;border-radius:0;border-color:#d1d5db;padding:.3rem .6rem;height:auto}
.hs-filter .form-control{width:210px}
.hs-filter .form-select{width:160px}

.hs-table{width:100%;border-collapse:collapse;font-size:.83rem}
.hs-table thead th{background:#f8fafc;color:#475569;font-weight:600;font-size:.72rem;text-transform:uppercase;letter-spacing:.04em;padding:.6rem 1rem;border-bottom:2px solid #e2e8f0;white-space:nowrap}
.hs-table tbody tr{border-bottom:1px solid #f1f5f9}
.hs-table tbody tr:last-child{border-bottom:none}
.hs-table tbody tr:hover{background:#fafbfc}
.hs-table td{padding:.7rem 1rem;vertical-align:middle}
.hs-no{color:#9ca3af;font-size:.75rem;text-align:center;width:36px}

.hs-name{font-weight:600;color:#0f172a;font-size:.86rem}
.hs-badges{display:flex;align-items:center;gap:.35rem;margin-top:.25rem}
.hs-sbadge{font-size:.67rem;font-weight:600;padding:.15rem .45rem;border-radius:3px;line-height:1.4}
.sbg-done{background:#dcfce7;color:#166534}
.sbg-ongoing{background:#dbeafe;color:#1e40af}
.sbg-pending{background:#fef9c3;color:#92400e}
.hs-gender{font-size:.7rem;color:#9ca3af;font-weight:500}

.hs-nopeserta{font-family:monospace;font-size:.82rem;color:#374151;font-weight:600}

.hs-dur-list{display:flex;flex-direction:column;gap:.18rem}
.hs-dur-row{display:flex;align-items:center;gap:.4rem;font-size:.8rem}
.hs-dur-p{font-size:.68rem;font-weight:700;color:#9ca3af;letter-spacing:.03em;min-width:20px}
.hs-dur-val{font-family:monospace;font-weight:600;color:#0f172a}
.hs-dur-single{font-family:monospace;font-size:.82rem;font-weight:600;color:#0f172a}

.hs-benar{display:flex;align-items:baseline;gap:.25rem;line-height:1}
.hs-benar-n{font-size:1.15rem;font-weight:700;color:#0f172a}
.hs-benar-d{font-size:.78rem;color:#9ca3af}
.hs-nilai{font-size:.75rem;color:#6b7280;margin-top:.3rem}
.hs-nilai-v{font-weight:700;color:#0f172a}
.hs-attempt-note{font-size:.68rem;color:#9ca3af;margin-top:.2rem}
.hs-skor-big{font-size:1.15rem;font-weight:700;color:#0f172a;line-height:1}
.hs-skor-meta{font-size:.72rem;color:#6b7280;margin-top:.25rem;line-height:1.6}
.hs-kat{display:inline-block;font-size:.67rem;font-weight:700;padding:.15rem .5rem;border-radius:3px;margin-top:.3rem;letter-spacing:.03em}

.hs-act{display:flex;flex-direction:column;gap:.3rem}
.hs-btn{display:inline-flex;align-items:center;gap:.3rem;font-size:.76rem;font-weight:500;padding:.3rem .65rem;border-radius:0;border:1px solid;cursor:pointer;text-decoration:none;background:transparent;line-height:1.4;white-space:nowrap;transition:background .12s}
.hs-btn-view{border-color:#bfdbfe;color:#1d4ed8}.hs-btn-view:hover{background:#eff6ff;color:#1d4ed8}
.hs-btn-reset{border-color:#fde68a;color:#92400e}.hs-btn-reset:hover{background:#fffbeb;color:#92400e}
.hs-btn-del{border-color:#fca5a5;color:#dc2626}.hs-btn-del:hover{background:#fef2f2;color:#dc2626}

.hs-empty{color:#9ca3af;font-size:.83rem}
@media print{.hs-act,.hs-filter,.hs-head .d-flex{display:none!important}.hs-wrap{padding-top:0;background:#fff}}
</style>

<div class="hs-wrap">
<div class="container-fluid px-3 px-md-4">

  <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show border-0 mb-3" style="border-radius:0" role="alert">
      <?= session()->getFlashdata('success') ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>
  <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show border-0 mb-3" style="border-radius:0" role="alert">
      <?= session()->getFlashdata('error') ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <!-- Header -->
  <div class="hs-head">
    <div>
      <div class="hs-title">
        <span class="hs-type-badge <?= $isCbt ? 'hs-type-cbt' : 'hs-type-cat' ?>"><?= $isCbt ? 'CBT' : 'CAT' ?></span>
        <?= esc($ujian['nama_ujian']) ?>
      </div>
      <div class="hs-meta">
        <?= esc($ujian['nama_kelas']) ?> &middot; <?= esc($ujian['nama_sekolah']) ?> &middot; <?= esc($ujian['nama_jenis']) ?>
        &middot; Guru: <?= esc($ujian['nama_guru']) ?>
        &middot; <?= $ujian['tanggal_mulai_format'] ?> &ndash; <?= $ujian['tanggal_selesai_format'] ?>
        &middot; Kode: <code><?= esc($ujian['kode_akses']) ?></code>
      </div>
    </div>
    <div class="d-flex gap-2 flex-wrap">
      <button class="hs-btn hs-btn-view" onclick="exportHasil()" style="border-radius:0">
        <i class="fas fa-download"></i>Export CSV
      </button>
      <a href="<?= base_url('guru/hasil-ujian') ?>" class="hs-btn" style="border-color:#d1d5db;color:#374151;border-radius:0;text-decoration:none;display:inline-flex;align-items:center;gap:.3rem">
        <i class="fas fa-arrow-left"></i>Kembali
      </a>
    </div>
  </div>

  <!-- Stats -->
  <div class="hs-stats">
    <div class="hs-stat">
      <div class="hs-stat-num num-green"><?= count($selesai) ?></div>
      <div class="hs-stat-lbl">Selesai</div>
    </div>
    <div class="hs-stat">
      <div class="hs-stat-num num-blue"><?= count($mengerjakan) ?></div>
      <div class="hs-stat-lbl">Mengerjakan</div>
    </div>
    <div class="hs-stat">
      <div class="hs-stat-num num-amber"><?= count($belumMulai) ?></div>
      <div class="hs-stat-lbl">Belum Mulai</div>
    </div>
    <div class="hs-stat">
      <div class="hs-stat-num num-slate"><?= $rataRata !== null ? round($rataRata, 1) : '—' ?></div>
      <div class="hs-stat-lbl">Rata-rata <?= $isCbt ? 'Nilai' : 'Skor' ?></div>
    </div>
  </div>

  <!-- Table card -->
  <div class="hs-card">
    <div class="hs-card-hd">
      <span class="hs-card-title">Daftar Peserta <span style="color:#9ca3af;font-weight:400">(<?= count($hasilSiswa) ?>)</span></span>
      <div class="hs-filter">
        <input type="text" class="form-control" id="searchSiswa" placeholder="Cari nama / no. peserta&hellip;">
        <select class="form-select" id="filterStatus">
          <option value="">Semua Status</option>
          <option value="selesai">Selesai</option>
          <option value="sedang_mengerjakan">Mengerjakan</option>
          <option value="belum_mulai">Belum Mulai</option>
        </select>
        <button class="hs-btn" style="border-color:#d1d5db;color:#374151;border-radius:0" onclick="resetFilter()">Reset</button>
      </div>
    </div>

    <div class="table-responsive">
      <table class="hs-table" id="tableHasil">
        <thead>
          <tr>
            <th class="hs-no text-center">#</th>
            <th>Siswa</th>
            <th>No. Peserta</th>
            <th><?= $isCbt ? 'Durasi per Percobaan' : 'Durasi' ?></th>
            <th><?= $isCbt ? 'Hasil (percobaan terakhir)' : 'Hasil Akhir' ?></th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($hasilSiswa as $idx => $siswa): ?>
          <?php
            $stCls = match($siswa['status']) { 'selesai' => 'sbg-done', 'sedang_mengerjakan' => 'sbg-ongoing', default => 'sbg-pending' };
            $stLbl = match($siswa['status']) { 'selesai' => 'Selesai', 'sedang_mengerjakan' => 'Mengerjakan', default => 'Belum Mulai' };
            $genderShort = $siswa['jenis_kelamin'] === 'Laki-laki' ? 'L' : ($siswa['jenis_kelamin'] === 'Perempuan' ? 'P' : '—');
          ?>
          <tr data-status="<?= $siswa['status'] ?>"
              data-nama="<?= esc(strtolower($siswa['nama_lengkap'])) ?>"
              data-no="<?= esc(strtolower($siswa['nomor_peserta'] ?? '')) ?>">
            <td class="hs-no"><?= $idx + 1 ?></td>
            <td>
              <div class="hs-name"><?= esc($siswa['nama_lengkap']) ?></div>
              <div class="hs-badges">
                <span class="hs-sbadge <?= $stCls ?>"><?= $stLbl ?></span>
                <span class="hs-gender"><?= $genderShort ?></span>
              </div>
            </td>
            <td><span class="hs-nopeserta"><?= esc($siswa['nomor_peserta'] ?? '—') ?></span></td>
            <td>
              <?php if ($siswa['status'] === 'selesai'): ?>
                <?php if ($isCbt && !empty($siswa['attempts_durasi'])): ?>
                  <div class="hs-dur-list">
                    <?php foreach ($siswa['attempts_durasi'] as $att): ?>
                      <div class="hs-dur-row">
                        <span class="hs-dur-p">P<?= $att['nomor'] ?></span>
                        <span class="hs-dur-val"><?= esc($att['durasi']) ?></span>
                      </div>
                    <?php endforeach; ?>
                  </div>
                <?php else: ?>
                  <span class="hs-dur-single"><?= esc($siswa['durasi_format']) ?></span>
                <?php endif; ?>
              <?php else: ?>
                <span class="hs-empty">—</span>
              <?php endif; ?>
            </td>
            <td>
              <?php if ($siswa['status'] === 'selesai'): ?>
                <?php if ($isCbt): ?>
                  <?php if ($siswa['nilai'] !== null): ?>
                    <div class="hs-skor-big"><?= number_format((float)$siswa['nilai'], 2) ?></div>
                  <?php endif; ?>
                  <?php if (($siswa['jumlah_attempt'] ?? 0) > 1): ?>
                    <div class="hs-attempt-note"><?= $siswa['jumlah_attempt'] ?> percobaan</div>
                  <?php endif; ?>
                <?php else: ?>
                  <div class="hs-skor-big"><?= number_format((float)$siswa['skor'], 1) ?></div>
                  <span class="hs-kat <?= $siswa['klasifikasi_kognitif']['bg_class'] ?> text-white">
                    <?= esc($siswa['klasifikasi_kognitif']['kategori']) ?>
                  </span>
                <?php endif; ?>
              <?php else: ?>
                <span class="hs-empty">—</span>
              <?php endif; ?>
            </td>
            <td>
              <div class="hs-act">
                <?php if ($siswa['status'] === 'selesai'): ?>
                  <a href="<?= base_url($isCbt ? 'guru/hasil-ujian/percobaan/' : 'guru/hasil-ujian/detail/') . $siswa['peserta_ujian_id'] ?>" class="hs-btn hs-btn-view">
                    <i class="fas fa-<?= $isCbt ? 'layer-group' : 'eye' ?>"></i>
                    <?= $isCbt ? 'Percobaan' : 'Detail' ?>
                  </a>
                  <button class="hs-btn hs-btn-del" onclick="confirmDelete(<?= $siswa['peserta_ujian_id'] ?>, '<?= addslashes($siswa['nama_lengkap']) ?>', 'selesai')">
                    <i class="fas fa-trash"></i>Hapus
                  </button>
                <?php elseif ($siswa['status'] === 'sedang_mengerjakan'): ?>
                  <button class="hs-btn hs-btn-reset" onclick="confirmReset(<?= $siswa['peserta_ujian_id'] ?>, '<?= addslashes($siswa['nama_lengkap']) ?>', 'sedang_mengerjakan')">
                    <i class="fas fa-redo"></i>Reset
                  </button>
                  <button class="hs-btn hs-btn-del" onclick="confirmDelete(<?= $siswa['peserta_ujian_id'] ?>, '<?= addslashes($siswa['nama_lengkap']) ?>', 'sedang_mengerjakan')">
                    <i class="fas fa-trash"></i>Hapus
                  </button>
                <?php else: ?>
                  <button class="hs-btn hs-btn-del" onclick="confirmDelete(<?= $siswa['peserta_ujian_id'] ?>, '<?= addslashes($siswa['nama_lengkap']) ?>', 'belum_mulai')">
                    <i class="fas fa-trash"></i>Hapus
                  </button>
                <?php endif; ?>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>
</div>

<script>
document.getElementById('searchSiswa').addEventListener('input', filterTable);
document.getElementById('filterStatus').addEventListener('change', filterTable);

function filterTable() {
  const q  = document.getElementById('searchSiswa').value.toLowerCase();
  const st = document.getElementById('filterStatus').value;
  document.querySelectorAll('#tableHasil tbody tr').forEach(function(r) {
    const nameMatch = !q || r.dataset.nama.includes(q) || r.dataset.no.includes(q);
    const stMatch   = !st || r.dataset.status === st;
    r.style.display = (nameMatch && stMatch) ? '' : 'none';
  });
}
function resetFilter() {
  document.getElementById('searchSiswa').value = '';
  document.getElementById('filterStatus').value = '';
  filterTable();
}

function exportHasil() {
  const isCbt = <?= $isCbt ? 'true' : 'false' ?>;
  const headers = ['No','Nama','No. Peserta','Status','Durasi','Jawaban Benar','Total Soal',isCbt?'Nilai':'Skor'];
  if (!isCbt) headers.push('Theta','SE','Klasifikasi');
  let csv = headers.join(',') + '\n';
  <?php foreach ($hasilSiswa as $i => $s): ?>
  csv += [
    <?= $i+1 ?>,
    '"<?= addslashes($s['nama_lengkap']) ?>"',
    '"<?= addslashes($s['nomor_peserta'] ?? '') ?>"',
    '"<?= $s['status'] ?>"',
    <?php if ($isCbt && !empty($s['attempts_durasi'])): ?>
      '"<?= implode(' | ', array_map(fn($a) => 'P'.$a['nomor'].': '.$a['durasi'], $s['attempts_durasi'])) ?>"',
    <?php else: ?>
      '"<?= $s['durasi_format'] ?? '-' ?>"',
    <?php endif; ?>
    <?= (int)($s['jawaban_benar'] ?? 0) ?>,
    <?= (int)($s['total_soal'] ?? 0) ?>,
    <?= $s['skor'] !== null ? number_format((float)$s['skor'], 2) : 0 ?>
    <?php if (!$isCbt): ?>
    ,<?= isset($s['theta_akhir']) ? number_format((float)$s['theta_akhir'], 4) : 0 ?>
    ,<?= isset($s['se_akhir']) ? number_format((float)$s['se_akhir'], 4) : 0 ?>
    ,'"<?= addslashes($s['klasifikasi_kognitif']['kategori'] ?? '') ?>"'
    <?php endif; ?>
  ].join(',') + '\n';
  <?php endforeach; ?>
  const blob = new Blob(['﻿' + csv], {type:'text/csv;charset=utf-8'});
  const a = document.createElement('a');
  a.href = URL.createObjectURL(blob);
  a.download = 'hasil_<?= preg_replace('/[^a-zA-Z0-9]/', '_', $ujian['nama_ujian']) ?>_<?= preg_replace('/[^a-zA-Z0-9]/', '_', $ujian['nama_kelas'] ?? '') ?>.csv';
  a.click();
}

function confirmDelete(id, nama, status) {
  const msgs = {
    selesai: {title:'Hapus Hasil Ujian', warn:'Semua data jawaban dan hasil ujian dihapus permanen.'},
    sedang_mengerjakan: {title:'Hapus Peserta (Sedang Mengerjakan)', warn:'Progress yang sudah dikerjakan akan hilang.'},
    belum_mulai: {title:'Hapus Peserta Ujian', warn:'Peserta dihapus dari daftar ujian.'}
  };
  const m = msgs[status] || msgs.belum_mulai;
  _showModal('deleteModal',
    `<div class="modal-header border-0"><h6 class="modal-title fw-semibold"><i class="fas fa-exclamation-triangle text-warning me-2"></i>${m.title}</h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
     <div class="modal-body"><p class="mb-2 small text-muted">${m.warn}</p><div class="p-3 bg-light" style="border-left:3px solid #dc2626"><strong>${nama}</strong></div></div>
     <div class="modal-footer border-0"><button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button><button class="btn btn-danger btn-sm" onclick="doDelete(${id})"><i class="fas fa-trash me-1"></i>Ya, Hapus</button></div>`
  );
}
function confirmReset(id, nama, status) {
  _showModal('resetModal',
    `<div class="modal-header border-0"><h6 class="modal-title fw-semibold"><i class="fas fa-redo text-warning me-2"></i>Reset Status Ujian</h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
     <div class="modal-body"><p class="mb-2 small text-muted">Progress ujian akan hilang dan siswa dapat mengulang dari awal.</p><div class="p-3 bg-light" style="border-left:3px solid #d97706"><strong>${nama}</strong></div></div>
     <div class="modal-footer border-0"><button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button><button class="btn btn-warning btn-sm" onclick="doReset(${id})"><i class="fas fa-redo me-1"></i>Ya, Reset</button></div>`
  );
}
function _showModal(id, body) {
  let el = document.getElementById(id);
  if (el) el.remove();
  document.body.insertAdjacentHTML('beforeend',
    `<div class="modal fade" id="${id}" tabindex="-1"><div class="modal-dialog"><div class="modal-content border-0 shadow">${body}</div></div></div>`
  );
  new bootstrap.Modal(document.getElementById(id)).show();
}
function doDelete(id) { window.location.href = '<?= base_url('guru/hasil-ujian/hapus/') ?>' + id; }
function doReset(id)  { window.location.href = '<?= base_url('guru/hasil-ujian/reset/') ?>' + id; }
</script>

<?= $this->endSection() ?>
