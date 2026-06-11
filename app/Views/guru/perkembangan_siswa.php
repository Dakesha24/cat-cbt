<?= $this->extend('templates/guru/guru_template') ?>

<?= $this->section('title') ?>Analisis Perkembangan Siswa<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
    $selectedKelas     = $filters['kelas_id']     ?? null;
    $selectedTipe      = $filters['tipe_ujian']   ?? '';
    $selectedJadwal    = $filters['jadwal_id']    ?? null;
    $selectedVariabel  = $filters['variabel_id']  ?? null;
    $selectedIndikator = $filters['indikator_id'] ?? null;
    $selectedMateri    = $filters['materi_id']    ?? null;
    $selectedGender    = $filters['jenis_kelamin'] ?? '';

    // Hitung jumlah filter aktif (sama seperti Analisis Hasil Ujian)
    $activeCount = count(array_filter(
        array_merge(array_diff_key($filters, ['biodata' => true]), $biodataFilters),
        fn($v) => !empty($v) && !is_array($v)
    ));

    $totalSiswa = count($pivotData);
?>

<style>
.an-wrap { background: #f4f6f9; min-height: 100vh; padding: 28px 0 60px; }

.an-filter         { background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; margin-bottom: 24px; }
.an-filter-head    { padding: 14px 20px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; cursor: pointer; user-select: none; }
.an-filter-head h6 { margin: 0; font-size: .8125rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #64748b; }
.an-filter-body    { padding: 20px; }
.an-filter-grid    { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 14px; }
.an-filter-label   { font-size: .78rem; font-weight: 600; color: #374151; margin-bottom: 5px; display: block; }
.an-filter-body .form-select,
.an-filter-body .form-control { font-size: .85rem; border-color: #e2e8f0; border-radius: 7px; }
.an-filter-body .form-select:focus { border-color: #93c5fd; box-shadow: 0 0 0 3px rgba(59,130,246,.1); }

.an-stats    { display: grid; grid-template-columns: repeat(3,1fr); gap: 14px; margin-bottom: 24px; }
.an-stat     { background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 16px 20px; }
.an-stat-num { font-size: 1.75rem; font-weight: 700; color: #0f172a; line-height: 1; }
.an-stat-lbl { font-size: .75rem; color: #64748b; margin-top: 4px; text-transform: uppercase; letter-spacing: .05em; font-weight: 600; }

.an-chart-full{ margin-bottom: 16px; }
.an-chart-card{ background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; }
.an-chart-head{ padding: 14px 20px; border-bottom: 1px solid #f1f5f9; }
.an-chart-head h6 { margin: 0; font-size: .875rem; font-weight: 600; color: #0f172a; }
.an-chart-head p  { margin: 3px 0 0; font-size: .75rem; color: #64748b; }
.an-chart-body    { padding: 20px; }
.an-chart-canvas-wrap { position: relative; width: 100%; }

.an-empty   { text-align: center; padding: 40px 20px; color: #94a3b8; font-size: .875rem; }
.an-empty i { font-size: 2rem; opacity: .4; display: block; margin-bottom: 8px; }

/* ═══════════════════════════════════════════════
   KARTU RATA-RATA PER PENGULANGAN
═══════════════════════════════════════════════ */
.an-attempt-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 14px; }
.an-attempt-card {
    background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px;
    padding: 16px; text-align: center; position: relative; transition: border-color .15s, box-shadow .15s;
}
.an-attempt-card:hover { border-color: #c7d2fe; box-shadow: 0 2px 8px rgba(79,70,229,.08); }
.an-attempt-badge {
    display: inline-block; font-size: .68rem; font-weight: 700; color: #4f46e5;
    background: #e0e7ff; padding: 2px 10px; border-radius: 100px; letter-spacing: .04em;
    text-transform: uppercase; margin-bottom: 10px;
}
.an-attempt-val { font-size: 1.6rem; font-weight: 700; color: #0f172a; line-height: 1; }
.an-attempt-trend { font-size: .75rem; font-weight: 600; margin-top: 6px; display: flex; align-items: center; justify-content: center; gap: 4px; }
.an-attempt-trend.up   { color: #16a34a; }
.an-attempt-trend.down { color: #dc2626; }
.an-attempt-trend.flat { color: #94a3b8; }

.an-tbl              { width: 100%; border-collapse: collapse; font-size: .8125rem; }
.an-tbl thead th     { background: #f8fafc; color: #64748b; font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; padding: 10px 14px; border-bottom: 2px solid #e2e8f0; white-space: nowrap; text-align: center; }
.an-tbl tbody td     { padding: 10px 14px; border-bottom: 1px solid #f1f5f9; color: #374151; vertical-align: middle; }
.an-tbl tbody tr:last-child td { border-bottom: none; }
.an-tbl tbody tr:hover td      { background: #fafafa; }

@media (max-width: 768px) {
    .an-stats      { grid-template-columns: 1fr 1fr; }
    .an-filter-grid{ grid-template-columns: 1fr 1fr; }
}
</style>

<div class="an-wrap">
<div class="container-fluid px-3 px-md-4">

    <!-- ══════════════════════════════════════════
         PAGE HEADER
    ══════════════════════════════════════════ -->
    <div class="mb-4">
        <h2 class="fw-bold text-dark mb-1">Analisis Perkembangan Siswa</h2>
        <p class="text-muted mb-0">Pantau tren capaian rata-rata global dan rekap nilai per pengulangan ujian berdasarkan filter aktif</p>
    </div>

    <!-- ══════════════════════════════════════════
         FILTER PANEL (sama dengan halaman Analisis Hasil Ujian)
    ══════════════════════════════════════════ -->
    <div class="an-filter">
        <div class="an-filter-head" onclick="toggleFilter()">
            <h6><i class="bi bi-sliders2 me-2"></i>Filter Data</h6>
            <div class="d-flex align-items-center gap-2">
                <?php if ($activeCount > 0): ?>
                    <span style="font-size:.72rem;font-weight:600;background:#dbeafe;color:#1d4ed8;padding:2px 8px;border-radius:100px">
                        <?= $activeCount ?> aktif
                    </span>
                <?php endif; ?>
                <i class="bi bi-chevron-down" id="filterChevron" style="transition:transform .2s;color:#94a3b8"></i>
            </div>
        </div>

        <div id="filterBody" style="<?= $activeCount > 0 ? '' : 'display:none' ?>">
            <form method="get" action="" class="an-filter-body">
                <div class="an-filter-grid">

                    <div>
                        <label class="an-filter-label">Kelas</label>
                        <select name="kelas_id" id="fKelas" class="form-select form-select-sm">
                            <option value="">Semua Kelas</option>
                            <?php foreach ($filterOptions['kelas'] as $k): ?>
                                <option value="<?= $k['kelas_id'] ?>" <?= (int)$selectedKelas === (int)$k['kelas_id'] ? 'selected' : '' ?>>
                                    <?= esc($k['nama_kelas']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="an-filter-label">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-select form-select-sm">
                            <option value="">Semua</option>
                            <option value="Laki-laki" <?= $selectedGender === 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                            <option value="Perempuan" <?= $selectedGender === 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
                        </select>
                    </div>

                    <div>
                        <label class="an-filter-label">Tipe Ujian</label>
                        <!-- Auto-submit saat berubah agar matriks & tren langsung disesuaikan -->
                        <select name="tipe_ujian" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">CAT & CBT</option>
                            <?php foreach ($filterOptions['jenis_ujian'] as $j): ?>
                                <option value="<?= $j['value'] ?>" <?= $selectedTipe === $j['value'] ? 'selected' : '' ?>>
                                    <?= $j['label'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="an-filter-label">Ujian</label>
                        <select name="jadwal_id" id="fJadwal" class="form-select form-select-sm">
                            <option value="">Semua Ujian</option>
                            <?php foreach ($filterOptions['ujian'] as $j): ?>
                                <option value="<?= $j['jadwal_id'] ?>"
                                    data-tipe="<?= $j['tipe_ujian'] ?>"
                                    data-kelas="<?= $j['kelas_id'] ?? '' ?>"
                                    <?= (int)$selectedJadwal === (int)$j['jadwal_id'] ? 'selected' : '' ?>>
                                    <?= esc($j['nama_ujian']) . ($j['kode_ujian'] ? ' (' . $j['kode_ujian'] . ')' : '') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="an-filter-label">Variabel</label>
                        <select name="variabel_id" id="fVariabel" class="form-select form-select-sm" onchange="cascadeIndikator()">
                            <option value="">Semua Variabel</option>
                            <?php foreach ($filterOptions['variabel'] as $v): ?>
                                <option value="<?= $v['variabel_id'] ?>" <?= (int)$selectedVariabel === (int)$v['variabel_id'] ? 'selected' : '' ?>>
                                    <?= esc($v['nama_variabel']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="an-filter-label">Indikator</label>
                        <select name="indikator_id" id="fIndikator" class="form-select form-select-sm">
                            <option value="">Semua Indikator</option>
                            <?php foreach ($filterOptions['indikator'] as $i): ?>
                                <option value="<?= $i['indikator_id'] ?>"
                                    data-variabel="<?= $i['variabel_id'] ?? '' ?>"
                                    <?= (int)$selectedIndikator === (int)$i['indikator_id'] ? 'selected' : '' ?>>
                                    <?= esc($i['nama_indikator']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="an-filter-label">Materi</label>
                        <select name="materi_id" class="form-select form-select-sm">
                            <option value="">Semua Materi</option>
                            <?php foreach ($filterOptions['materi'] as $m): ?>
                                <option value="<?= $m['materi_id'] ?>" <?= (int)$selectedMateri === (int)$m['materi_id'] ? 'selected' : '' ?>>
                                    <?= esc($m['nama_materi']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Filter Biodata Tambahan (dinamis dari form builder, tipe select saja) -->
                    <?php foreach ($selectFields as $field): ?>
                    <div>
                        <label class="an-filter-label"><?= esc($field['label']) ?></label>
                        <select name="biodata_<?= (int)$field['field_id'] ?>" class="form-select form-select-sm">
                            <option value="">Semua</option>
                            <?php foreach ($field['options'] as $opt): ?>
                                <option value="<?= esc($opt['label']) ?>" <?= ($biodataFilters[$field['field_id']] ?? '') === $opt['label'] ? 'selected' : '' ?>>
                                    <?= esc($opt['label']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endforeach; ?>

                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary btn-sm px-4">
                        <i class="bi bi-search me-1"></i>Terapkan Filter
                    </button>
                    <a href="<?= base_url('guru/perkembangan-siswa') ?>" class="btn btn-outline-secondary btn-sm">
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- ══════════════════════════════════════════
         STAT CARDS
    ══════════════════════════════════════════ -->
    <div class="an-stats">
        <div class="an-stat">
            <div class="an-stat-num"><?= $totalSiswa ?></div>
            <div class="an-stat-lbl">Total Siswa</div>
        </div>
        <div class="an-stat">
            <div class="an-stat-num"><?= $maxAttempt ?></div>
            <div class="an-stat-lbl">Jumlah Pengulangan</div>
        </div>
        <div class="an-stat">
            <div class="an-stat-num"><?= $maxAttempt > 0 ? number_format($globalAverages[$maxAttempt], 2) : '-' ?></div>
            <div class="an-stat-lbl">Rata-rata Pengulangan Terakhir</div>
        </div>
    </div>

    <?php if ($totalSiswa === 0): ?>
        <div class="an-empty" style="background:#fff;border:1px solid #e2e8f0;border-radius:10px;padding:60px 20px">
            <i class="bi bi-bar-chart-line"></i>
            <strong>Belum ada data</strong><br>
            Pilih filter di atas lalu klik <strong>Terapkan Filter</strong>.
        </div>
    <?php else: ?>

    <!-- ══════════════════════════════════════════
         RATA-RATA CAPAIAN GLOBAL PER PENGULANGAN
    ══════════════════════════════════════════ -->
    <div class="an-chart-card an-chart-full">
        <div class="an-chart-head">
            <h6><i class="bi bi-globe me-2 text-primary"></i>Rata-Rata Capaian Keseluruhan (Makro Global)</h6>
            <p>Rata-rata skor seluruh siswa pada setiap pengulangan ujian</p>
        </div>
        <div class="an-chart-body">
            <div class="an-attempt-grid">
                <?php for ($i = 1; $i <= $maxAttempt; $i++): ?>
                    <?php
                        $current  = $globalAverages[$i];
                        $previous = $i > 1 ? $globalAverages[$i - 1] : null;
                        $diff     = $previous !== null ? round($current - $previous, 2) : null;
                        $trendClass = $diff === null ? '' : ($diff > 0 ? 'up' : ($diff < 0 ? 'down' : 'flat'));
                        $trendIcon  = $diff === null ? '' : ($diff > 0 ? 'bi-arrow-up-short' : ($diff < 0 ? 'bi-arrow-down-short' : 'bi-dash'));
                    ?>
                    <div class="an-attempt-card">
                        <span class="an-attempt-badge">Pengulangan <?= $i ?></span>
                        <div class="an-attempt-val"><?= number_format($current, 2) ?></div>
                        <?php if ($diff !== null): ?>
                            <div class="an-attempt-trend <?= $trendClass ?>">
                                <i class="bi <?= $trendIcon ?>"></i>
                                <?= number_format(abs($diff), 2) ?> dari sebelumnya
                            </div>
                        <?php else: ?>
                            <div class="an-attempt-trend flat">&nbsp;</div>
                        <?php endif; ?>
                    </div>
                <?php endfor; ?>
            </div>
        </div>
    </div>

    <!-- ══════════════════════════════════════════
         TREN GLOBAL (RATA-RATA SELURUH SISWA)
    ══════════════════════════════════════════ -->
    <div class="an-chart-card an-chart-full">
        <div class="an-chart-head">
            <h6><i class="bi bi-bar-chart-fill me-2 text-success"></i>Tren Global (Rata-rata Seluruh Siswa)</h6>
            <p>Perkembangan rata-rata skor seluruh siswa antar pengulangan ujian</p>
        </div>
        <div class="an-chart-body">
            <div class="an-chart-canvas-wrap" style="height:320px">
                <canvas id="canvasGlobal"></canvas>
            </div>
        </div>
    </div>

    <!-- ══════════════════════════════════════════
         MATRIKS NILAI SISWA PER PENGULANGAN
    ══════════════════════════════════════════ -->
    <div class="an-chart-card">
        <div class="an-chart-head d-flex justify-content-between align-items-center">
            <div>
                <h6><i class="bi bi-grid-3x3-gap-fill me-2 text-info"></i>Matriks Nilai Siswa per Pengulangan</h6>
                <p>Klik "Detail" untuk melihat grafik perkembangan spesifik per siswa</p>
            </div>
            <button class="btn btn-sm btn-outline-secondary" onclick="exportCSV()" style="font-size:.78rem">
                <i class="bi bi-download me-1"></i>Export CSV
            </button>
        </div>
        <div class="an-chart-body">
            <div class="table-responsive">
                <table class="an-tbl" id="tblMatriks">
                    <thead>
                        <tr>
                            <th width="5%" rowspan="2" class="align-middle">No</th>
                            <th rowspan="2" class="align-middle text-start">Nama Siswa</th>
                            <th rowspan="2" class="align-middle text-start">Sekolah & Kelas</th>
                            <?php if ($maxAttempt > 0): ?>
                                <th colspan="<?= $maxAttempt ?>">Skor Hasil Pengulangan (Attempt)</th>
                            <?php else: ?>
                                <th rowspan="2" class="align-middle">Skor Pengulangan</th>
                            <?php endif; ?>
                            <th rowspan="2" class="align-middle an-tbl-aksi" width="14%">Aksi</th>
                        </tr>
                        <?php if ($maxAttempt > 0): ?>
                        <tr>
                            <?php for ($i = 1; $i <= $maxAttempt; $i++): ?>
                                <th style="width: 100px;">P-<?= $i ?></th>
                            <?php endfor; ?>
                        </tr>
                        <?php endif; ?>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($pivotData as $sId => $s): ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td class="fw-bold text-dark text-start"><?= esc($s['nama']) ?></td>
                            <td class="text-start">
                                <span class="d-block small text-muted"><?= esc($s['sekolah'] ?? '-') ?></span>
                                <span class="badge bg-light text-secondary border"><?= esc($s['kelas'] ?? '-') ?></span>
                            </td>

                            <?php for ($i = 1; $i <= $maxAttempt; $i++): ?>
                                <td class="text-center fw-bold <?= isset($s['skor'][$i]) ? 'text-primary' : 'text-muted' ?>">
                                    <?= isset($s['skor'][$i]) ? number_format($s['skor'][$i], 2) : '-' ?>
                                </td>
                            <?php endfor; ?>

                            <td class="text-center an-tbl-aksi">
                                <button type="button" class="btn btn-sm btn-outline-primary fw-bold"
                                    onclick="bukaModalDetail('<?= $sId ?>', '<?= esc($s['nama'], 'js') ?>')">
                                    <i class="bi bi-graph-up me-1"></i>Detail
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php endif; ?>

</div>
</div>

<div class="modal fade" id="modalDetailSiswa" tabindex="-1" aria-labelledby="modalDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px; border: none;">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold" id="modalDetailLabel">
                    <i class="bi bi-person-badge text-primary me-2"></i>Perkembangan: <span id="namaSiswaLabel" class="text-primary"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-muted small mb-3">Grafik perkembangan ini mengikuti filter yang sedang aktif pada halaman ini.</p>
                <div style="position: relative; height: 350px; width: 100%;">
                    <canvas id="canvasSiswa"></canvas>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let chartGlobalInstance = null;
let chartSiswaInstance = null;

/** Export tabel matriks nilai siswa ke file CSV */
function exportCSV() {
    const tbl = document.getElementById('tblMatriks');
    if (!tbl) return;

    const rows = [];

    // Header dibangun manual karena thead memakai rowspan/colspan
    const header = ['No', 'Nama Siswa', 'Sekolah & Kelas'];
    const subHeaders = tbl.querySelectorAll('thead tr:nth-child(2) th');
    if (subHeaders.length > 0) {
        subHeaders.forEach(th => header.push(th.innerText.trim()));
    } else {
        header.push('Skor Pengulangan');
    }
    rows.push(header);

    // Baris data
    tbl.querySelectorAll('tbody tr').forEach(tr => {
        const cells = Array.from(tr.querySelectorAll('td')).filter(td => !td.classList.contains('an-tbl-aksi'));
        const row = cells.map((td, idx) => {
            if (idx === 2) {
                // Kolom "Sekolah & Kelas" terdiri dari 2 baris terpisah, gabungkan jadi satu
                return Array.from(td.querySelectorAll('span')).map(s => s.innerText.trim()).join(' - ');
            }
            return td.innerText.trim();
        });
        rows.push(row);
    });

    const csv = rows.map(r => r.map(c => '"' + String(c).replace(/"/g, '""') + '"').join(',')).join('\n');

    const blob = new Blob(['﻿' + csv], { type: 'text/csv;charset=utf-8' });
    const a    = document.createElement('a');
    a.href     = URL.createObjectURL(blob);
    a.download = 'perkembangan_siswa.csv';
    a.click();
}

/** Toggle buka/tutup filter panel */
function toggleFilter() {
    const body = document.getElementById('filterBody');
    const icon = document.getElementById('filterChevron');
    const open = body.style.display !== 'none';
    body.style.display  = open ? 'none' : 'block';
    icon.style.transform = open ? '' : 'rotate(180deg)';
}

function cascadeIndikator() {
    const varId = document.getElementById('fVariabel')?.value;
    document.querySelectorAll('#fIndikator option').forEach(o => {
        if (o.value !== '') o.style.display = (!varId || o.dataset.variabel === varId) ? '' : 'none';
    });
}

// Filter aktif saat ini (hasil submit form GET) — dipakai untuk panggilan AJAX grafik
const activeFilters = <?= json_encode(array_merge([
    'kelas_id'      => $selectedKelas,
    'jadwal_id'     => $selectedJadwal,
    'tipe_ujian'    => $selectedTipe,
    'jenis_kelamin' => $selectedGender,
    'variabel_id'   => $selectedVariabel,
    'indikator_id'  => $selectedIndikator,
    'materi_id'     => $selectedMateri,
], array_combine(
    array_map(fn($id) => 'biodata_' . $id, array_keys($biodataFilters)),
    array_values($biodataFilters)
))) ?>;

function buildParams(extra = {}) {
    const params = new URLSearchParams();
    Object.entries({ ...activeFilters, ...extra }).forEach(([key, val]) => {
        if (val !== null && val !== '') params.append(key, val);
    });
    return params;
}

function fetchGrafikGlobal() {
    const canvas = document.getElementById('canvasGlobal');
    if (!canvas) return;
    const params = buildParams();
    fetch('<?= base_url('guru/grafik-perkembangan-ajax') ?>?' + params.toString())
        .then(res => res.json())
        .then(resData => {
            renderChart('canvasGlobal', resData.labels, resData.data, 'global', 'Rata-Rata Global Seluruh Siswa', '#0d6efd', 'rgba(13, 110, 253, 0.05)');
        });
}

function bukaModalDetail(siswaId, namaSiswa) {
    document.getElementById('namaSiswaLabel').innerText = namaSiswa;
    const modalDetail = new bootstrap.Modal(document.getElementById('modalDetailSiswa'));
    modalDetail.show();

    const params = buildParams({ siswa_id: siswaId });

    fetch('<?= base_url('guru/grafik-perkembangan-ajax') ?>?' + params.toString())
        .then(res => res.json())
        .then(resData => {
            renderChart('canvasSiswa', resData.labels, resData.data, 'siswa', 'Skor ' + namaSiswa, '#198754', 'rgba(25, 135, 84, 0.05)');
        });
}

function renderChart(canvasId, labels, points, type, labelName, lineColor, bgColor) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return;
    const ctx = canvas.getContext('2d');

    if (type === 'global' && chartGlobalInstance) chartGlobalInstance.destroy();
    if (type === 'siswa' && chartSiswaInstance) chartSiswaInstance.destroy();

    const newChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: labelName,
                data: points,
                borderColor: lineColor,
                backgroundColor: bgColor,
                borderWidth: 3,
                tension: 0.3,
                fill: true,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: lineColor,
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true, max: 100 } }
        }
    });

    if (type === 'global') chartGlobalInstance = newChart;
    if (type === 'siswa') chartSiswaInstance = newChart;
}

document.addEventListener("DOMContentLoaded", function() {
    cascadeIndikator();
    fetchGrafikGlobal();
});
</script>
<?= $this->endSection() ?>
