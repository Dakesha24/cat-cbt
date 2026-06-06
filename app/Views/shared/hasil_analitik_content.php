<?php
$summary = $summary ?? [];
$durationBars = $durationBars ?? [];
$studentRows = $studentRows ?? [];
$filters = $filters ?? [];
$filterOptions = $filterOptions ?? [];
$lockSchoolFilter = $lockSchoolFilter ?? false;
$biodataFilters = $biodataFilters ?? [];
$selectFields = $selectFields ?? [];
$role = $pageRole ?? 'admin';

$formatDuration = static function (int $seconds): string {
    if ($seconds <= 0) {
        return '-';
    }

    $hours = floor($seconds / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    $secs = $seconds % 60;

    return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
};

$selectedSchoolId  = $filters['sekolah_id']    ?? null;
$selectedKelasId   = $filters['kelas_id']       ?? null;
$selectedJenisUjian= $filters['tipe_ujian']     ?? null;
$selectedJadwalId  = $filters['jadwal_id']      ?? null;
$selectedVariabelId= $filters['variabel_id']    ?? null;
$selectedIndikatorId=$filters['indikator_id']   ?? null;
$selectedMateriId  = $filters['materi_id']      ?? null;
?>

<style>
.analytics-shell { background: linear-gradient(180deg, #f8fbff 0%, #ffffff 100%); border: 1px solid #e6edf7; border-radius: 18px; }
.analytics-card { border: 1px solid #e9eef5; border-radius: 16px; box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06); overflow: hidden; }
.analytics-card .card-header { background: #fff; border-bottom: 1px solid #edf2f7; padding: 1rem 1.25rem; }
.analytics-card .card-body { padding: 1.25rem; }
.an-filter         { background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; margin-bottom: 1.5rem; }
.an-filter-head    { padding: 14px 20px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; cursor: pointer; user-select: none; }
.an-filter-head h6 { margin: 0; font-size: .8125rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #64748b; }
.an-filter-body    { padding: 20px; }
.an-filter-grid    { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 14px; }
.an-filter-label   { font-size: .78rem; font-weight: 600; color: #374151; margin-bottom: 5px; display: block; }
.an-filter-body .form-select { font-size: .85rem; border-color: #e2e8f0; border-radius: 7px; }
.an-filter-body .form-select:focus { border-color: #93c5fd; box-shadow: 0 0 0 3px rgba(59,130,246,.1); }
.an-filter-body .form-select:disabled { background: #eef3f8; color: #7b8794; border-color: #dde6f1; cursor: not-allowed; }
.chart-wrap { position: relative; width: 100%; height: 320px; }
.chart-card .card-body { padding-top: 1rem; }
.duration-overview { display: flex; flex-wrap: wrap; gap: 0.75rem; margin-bottom: 1rem; }
.duration-chip { min-width: 170px; padding: 0.75rem 0.85rem; border: 1px solid #e5edf8; background: #f8fbff; }
.duration-chip .label { font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.2rem; }
.duration-chip .value { font-size: 1.05rem; font-weight: 700; color: #0f172a; }
.recap-table-wrap { border: 1px solid #e8edf4; }
.recap-table { margin-bottom: 0; }
.recap-table col.col-no { width: 56px; }
.recap-table col.col-name { width: 220px; }
.recap-table col.col-school { width: 180px; }
.recap-table col.col-class { width: 120px; }
.recap-table col.col-type { width: 110px; }
.recap-table col.col-exam { width: 240px; }
.recap-table col.col-meta { width: 240px; }
.recap-table col.col-duration { width: 130px; }
.recap-table col.col-interpretation { width: 120px; }
.recap-table thead th {
    white-space: nowrap;
    background: #f8fafc;
    color: #475569;
    font-size: 0.76rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    border-bottom: 1px solid #e2e8f0;
    padding: 0.9rem 0.85rem;
}
.recap-table td,
.recap-table th { vertical-align: middle; }
.recap-table tbody td {
    padding: 0.95rem 0.85rem;
    border-color: #edf2f7;
    color: #1f2937;
    font-size: 0.88rem;
    background: #fff;
}
.recap-table tbody tr:nth-child(even) td { background: #fbfcfe; }
.recap-table tbody tr:hover td { background: #f6f9fc; }
.recap-table .col-no { text-align: center; color: #64748b; }
.recap-table .col-duration { white-space: nowrap; font-variant-numeric: tabular-nums; }
.recap-table .col-jenis { white-space: nowrap; }
.student-name { font-weight: 600; color: #0f172a; }
.exam-name { color: #334155; }
.table-note { color: #64748b; font-size: 0.82rem; }
.interpretation-note {
    margin-top: 0.75rem;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.55rem;
    color: #475569;
    font-size: 0.79rem;
    line-height: 1.4;
}
.interpretation-note-text { color: #64748b; }
.interpretation-rule {
    display: inline-flex;
    align-items: center;
    padding: 0.38rem 0.7rem;
    border: 1px solid #e5eaf1;
    background: #f8fafc;
    color: #334155;
    font-size: 0.74rem;
    font-weight: 700;
    white-space: nowrap;
}
.analytics-empty { display: flex; align-items: center; justify-content: center; height: 100%; min-height: 240px; color: #94a3b8; font-size: 0.92rem; border: 1px dashed #dbe7ff; border-radius: 14px; background: #f8fbff; }
.meta-cell { max-width: 230px; white-space: normal; line-height: 1.35; color: #334155; }
.badge-interpretasi {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 92px;
    padding: 0.45rem 0.7rem;
    font-size: 0.74rem;
    font-weight: 700;
    border: 1px solid #d7dee8;
    background: #f8fafc;
    color: #475569;
    border-radius: 999px;
}
.badge-interpretasi.cepat { border-color: #d9e3ee; background: #f8fafc; color: #334155; }
.badge-interpretasi.rata-rata { border-color: #cfd8e3; background: #f1f5f9; color: #1e293b; }
.badge-interpretasi.lambat { border-color: #d9e3ee; background: #f8fafc; color: #334155; }
@media (max-width: 768px) {
    .an-filter-grid { grid-template-columns: 1fr 1fr; }
    .chart-wrap { height: 280px; }
    .duration-chip { min-width: calc(50% - 0.4rem); }
}
</style>

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">Rekap Data Ujian</h2>
            <p class="text-muted mb-0">Ringkasan durasi pengerjaan dan daftar siswa berdasarkan filter ujian dan metadata soal.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url($basePath) ?>" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-list-ul me-1"></i>Daftar Hasil
            </a>
        </div>
    </div>

    <div class="analytics-shell shadow-sm px-3 px-md-4 py-4">

        <?php
        $activeFilters = array_filter([
            $selectedSchoolId, $selectedKelasId, $selectedJenisUjian,
            $selectedJadwalId, $filters['jenis_kelamin'] ?? null,
            $selectedVariabelId, $selectedIndikatorId, $selectedMateriId,
            ...array_values($biodataFilters),
        ]);
        $activeCount = count($activeFilters);
        ?>
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

                        <?php if ($role === 'admin'): ?>
                        <div>
                            <label class="an-filter-label">Sekolah</label>
                            <select name="sekolah_id" id="fSekolah" class="form-select form-select-sm" onchange="cascadeKelas()">
                                <option value="">Semua Sekolah</option>
                                <?php foreach (($filterOptions['sekolah'] ?? []) as $sekolah): ?>
                                    <option value="<?= esc($sekolah['sekolah_id']) ?>"
                                        <?= (int)$selectedSchoolId === (int)$sekolah['sekolah_id'] ? 'selected' : '' ?>>
                                        <?= esc($sekolah['nama_sekolah']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if ($lockSchoolFilter): ?>
                                <input type="hidden" name="sekolah_id" value="<?= esc((string)$selectedSchoolId) ?>">
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>

                        <div>
                            <label class="an-filter-label">Kelas</label>
                            <select name="kelas_id" id="fKelas" class="form-select form-select-sm">
                                <option value="">Semua Kelas</option>
                                <?php foreach (($filterOptions['kelas'] ?? []) as $kelas): ?>
                                    <option value="<?= esc($kelas['kelas_id']) ?>"
                                        data-sekolah="<?= esc((string)($kelas['sekolah_id'] ?? '')) ?>"
                                        <?= (int)$selectedKelasId === (int)$kelas['kelas_id'] ? 'selected' : '' ?>>
                                        <?= esc($kelas['nama_kelas']) ?><?= !empty($kelas['tahun_ajaran']) ? ' - ' . esc($kelas['tahun_ajaran']) : '' ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="an-filter-label">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select form-select-sm">
                                <option value="">Semua</option>
                                <option value="Laki-laki" <?= ($filters['jenis_kelamin'] ?? '') === 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                                <option value="Perempuan" <?= ($filters['jenis_kelamin'] ?? '') === 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
                            </select>
                        </div>

                        <div>
                            <label class="an-filter-label">Tipe Ujian</label>
                            <select name="tipe_ujian" class="form-select form-select-sm">
                                <option value="">CAT & CBT</option>
                                <?php foreach (($filterOptions['jenis_ujian'] ?? []) as $jenis): ?>
                                    <option value="<?= esc($jenis['value']) ?>" <?= $selectedJenisUjian === $jenis['value'] ? 'selected' : '' ?>>
                                        <?= esc($jenis['label']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="an-filter-label">Ujian</label>
                            <select name="jadwal_id" id="fUjian" class="form-select form-select-sm">
                                <option value="">Semua Ujian</option>
                                <?php foreach (($filterOptions['ujian'] ?? []) as $ujian): ?>
                                    <option value="<?= esc($ujian['jadwal_id']) ?>"
                                        data-tipe="<?= esc($ujian['tipe_ujian'] ?? '') ?>"
                                        data-kelas="<?= esc((string)($ujian['kelas_id'] ?? '')) ?>"
                                        <?= (int)$selectedJadwalId === (int)$ujian['jadwal_id'] ? 'selected' : '' ?>>
                                        <?= esc($ujian['nama_ujian']) ?><?= !empty($ujian['kode_ujian']) ? ' (' . esc($ujian['kode_ujian']) . ')' : '' ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="an-filter-label">Variabel</label>
                            <select name="variabel_id" id="fVariabel" class="form-select form-select-sm" onchange="cascadeIndikator()">
                                <option value="">Semua Variabel</option>
                                <?php foreach (($filterOptions['variabel'] ?? []) as $variabel): ?>
                                    <option value="<?= esc($variabel['variabel_id']) ?>" <?= (int)$selectedVariabelId === (int)$variabel['variabel_id'] ? 'selected' : '' ?>>
                                        <?= esc($variabel['nama_variabel']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="an-filter-label">Indikator</label>
                            <select name="indikator_id" id="fIndikator" class="form-select form-select-sm">
                                <option value="">Semua Indikator</option>
                                <?php foreach (($filterOptions['indikator'] ?? []) as $indikator): ?>
                                    <option value="<?= esc($indikator['indikator_id']) ?>"
                                        data-variabel="<?= esc((string)($indikator['variabel_id'] ?? '')) ?>"
                                        <?= (int)$selectedIndikatorId === (int)$indikator['indikator_id'] ? 'selected' : '' ?>>
                                        <?= esc($indikator['nama_indikator']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="an-filter-label">Materi</label>
                            <select name="materi_id" class="form-select form-select-sm">
                                <option value="">Semua Materi</option>
                                <?php foreach (($filterOptions['materi'] ?? []) as $materi): ?>
                                    <option value="<?= esc($materi['materi_id']) ?>" <?= (int)$selectedMateriId === (int)$materi['materi_id'] ? 'selected' : '' ?>>
                                        <?= esc($materi['nama_materi']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <?php foreach ($selectFields as $field): ?>
                        <div>
                            <label class="an-filter-label"><?= esc($field['label']) ?></label>
                            <select name="biodata_<?= (int)$field['field_id'] ?>" class="form-select form-select-sm">
                                <option value="">Semua</option>
                                <?php foreach ($field['options'] as $opt): ?>
                                    <option value="<?= esc($opt['label']) ?>"
                                        <?= ($biodataFilters[$field['field_id']] ?? '') === $opt['label'] ? 'selected' : '' ?>>
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
                        <a href="<?= base_url($basePath . '/analitik') ?>" class="btn btn-outline-secondary btn-sm">
                            Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <?php
        $distribusi = ['Cepat' => 0, 'Rata-rata' => 0, 'Lambat' => 0];
        $totalDurasi = 0;
        $countDurasi = 0;
        foreach ($studentRows as $row) {
            $k = $row['interpretasi'] ?? 'Rata-rata';
            if (array_key_exists($k, $distribusi)) $distribusi[$k]++;
            if (!empty($row['durasi_detik'])) { $totalDurasi += (int)$row['durasi_detik']; $countDurasi++; }
        }
        $rataRataDurasi = $countDurasi > 0 ? (int)round($totalDurasi / $countDurasi) : 0;
        ?>
        <div class="card analytics-card chart-card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Distribusi Waktu Pengerjaan Siswa</h5>
                <div class="table-note mt-1">Jumlah siswa berdasarkan kecepatan pengerjaan relatif terhadap rata-rata (±10%).</div>
            </div>
            <div class="card-body">
                <div class="duration-overview">
                    <div class="duration-chip">
                        <div class="label">Rata-rata Durasi</div>
                        <div class="value"><?= esc($formatDuration($rataRataDurasi)) ?></div>
                    </div>
                    <div class="duration-chip">
                        <div class="label">Cepat (&le;90%)</div>
                        <div class="value"><?= $distribusi['Cepat'] ?> siswa</div>
                    </div>
                    <div class="duration-chip">
                        <div class="label">Rata-rata (91–109%)</div>
                        <div class="value"><?= $distribusi['Rata-rata'] ?> siswa</div>
                    </div>
                    <div class="duration-chip">
                        <div class="label">Lambat (&ge;110%)</div>
                        <div class="value"><?= $distribusi['Lambat'] ?> siswa</div>
                    </div>
                </div>
                <div class="chart-wrap">
                    <canvas id="durationChart"></canvas>
                </div>
            </div>
        </div>

        <div class="card analytics-card">
            <div class="card-header">
                <div>
                    <h5 class="mb-0">Tabel Rekap Siswa</h5>
                    <div class="table-note">Menampilkan daftar siswa sesuai filter yang dipilih beserta dimensi metadata dan interpretasi waktu pengerjaan.</div>
                    <div class="interpretation-note">
                        <span class="interpretation-note-text">Interpretasi dibandingkan terhadap rata-rata durasi data terfilter.</span>
                        <span class="interpretation-rule">Cepat: &le; 90%</span>
                        <span class="interpretation-rule">Rata-rata: 91% - 109%</span>
                        <span class="interpretation-rule">Lambat: &ge; 110%</span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive recap-table-wrap">
                    <table class="table recap-table align-middle">
                        <colgroup>
                            <col class="col-no">
                            <col class="col-name">
                            <col class="col-school">
                            <col class="col-class">
                            <col class="col-type">
                            <col class="col-exam">
                            <col class="col-meta">
                            <col class="col-meta">
                            <col class="col-meta">
                            <col class="col-duration">
                            <col class="col-interpretation">
                        </colgroup>
                        <thead>
                            <tr>
                                <th class="col-no">No</th>
                                <th>Nama</th>
                                <th>Sekolah</th>
                                <th>Kelas</th>
                                <th class="col-jenis">Jenis Ujian</th>
                                <th>Ujian</th>
                                <th>Variabel</th>
                                <th>Indikator</th>
                                <th>Materi</th>
                                <th class="col-duration">Waktu Pengerjaan</th>
                                <th>Interpretasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($studentRows)): ?>
                                <tr>
                                    <td colspan="10" class="text-center text-muted py-4">Belum ada data siswa untuk filter yang dipilih.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($studentRows as $index => $row): ?>
                                    <?php
                                    $interpretasiClass = strtolower(str_replace(' ', '-', (string) ($row['interpretasi'] ?? 'rata-rata')));
                                    ?>
                                    <tr>
                                        <td class="col-no"><?= $index + 1 ?></td>
                                        <td class="student-name"><?= esc($row['nama_lengkap'] ?? '-') ?></td>
                                        <td><?= esc($row['nama_sekolah'] ?? '-') ?></td>
                                        <td><?= esc($row['nama_kelas'] ?? '-') ?></td>
                                        <td class="col-jenis"><?= esc($row['tipe_ujian'] ?? '-') ?></td>
                                        <td class="exam-name"><?= esc($row['nama_ujian'] ?? '-') ?></td>
                                        <td class="meta-cell"><?= esc($row['daftar_variabel'] ?? '-') ?></td>
                                        <td class="meta-cell"><?= esc($row['daftar_indikator'] ?? '-') ?></td>
                                        <td class="meta-cell"><?= esc($row['daftar_materi'] ?? '-') ?></td>
                                        <td class="fw-semibold col-duration"><?= esc($formatDuration((int) ($row['durasi_detik'] ?? 0))) ?></td>
                                        <td><span class="badge-interpretasi <?= esc($interpretasiClass) ?>"><?= esc($row['interpretasi'] ?? 'Rata-rata') ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?= base_url('js/chart.umd.min.js') ?>"></script>
<script>
const distribusiData   = <?= json_encode(array_values($distribusi), JSON_UNESCAPED_UNICODE) ?>;
const distribusiLabels = <?= json_encode(array_keys($distribusi), JSON_UNESCAPED_UNICODE) ?>;
const distribusiColors = ['#16a34a', '#2563eb', '#dc2626'];

const durationChartContext = document.getElementById('durationChart');
function toggleFilter() {
    const body = document.getElementById('filterBody');
    const icon = document.getElementById('filterChevron');
    const isOpen = body && body.style.display !== 'none';
    if (body) body.style.display = isOpen ? 'none' : '';
    if (icon) icon.style.transform = isOpen ? '' : 'rotate(180deg)';
}

function cascadeKelas() {
    const sekolahId = document.getElementById('fSekolah')?.value || '';
    const kelasEl   = document.getElementById('fKelas');
    if (!kelasEl) return;
    Array.from(kelasEl.options).forEach((opt, i) => {
        if (i === 0) return;
        const match = !sekolahId || opt.dataset.sekolah === sekolahId;
        opt.hidden = !match; opt.disabled = !match;
    });
    if (kelasEl.options[kelasEl.selectedIndex]?.disabled) kelasEl.value = '';
}

function cascadeIndikator() {
    const variabelId = document.getElementById('fVariabel')?.value || '';
    const indEl      = document.getElementById('fIndikator');
    if (!indEl) return;
    Array.from(indEl.options).forEach((opt, i) => {
        if (i === 0) return;
        const match = !variabelId || opt.dataset.variabel === variabelId;
        opt.hidden = !match; opt.disabled = !match;
    });
    if (indEl.options[indEl.selectedIndex]?.disabled) indEl.value = '';
}

cascadeKelas();
cascadeIndikator();

function formatDuration(seconds) {
    const total = Number(seconds || 0);
    if (!total) {
        return '00:00:00';
    }

    const hours = Math.floor(total / 3600);
    const minutes = Math.floor((total % 3600) / 60);
    const secs = total % 60;

    return [hours, minutes, secs].map(item => String(item).padStart(2, '0')).join(':');
}

function buildChartOptions(extraOptions = {}) {
    return {
        responsive: true,
        maintainAspectRatio: false,
        resizeDelay: 200,
        animation: false,
        ...extraOptions
    };
}

if (durationChartContext) {
    const maxVal = Math.max(...distribusiData, 1);
    const suggestedMax = Math.ceil(maxVal * 1.2);

    new Chart(durationChartContext, {
        type: 'bar',
        data: {
            labels: distribusiLabels,
            datasets: [{
                label: 'Jumlah Siswa',
                data: distribusiData,
                backgroundColor: distribusiColors,
                borderRadius: 4,
                maxBarThickness: 80
            }]
        },
        options: buildChartOptions({
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: (ctx) => ` ${ctx.raw} siswa`
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    suggestedMax,
                    ticks: {
                        stepSize: 1,
                        precision: 0,
                        callback: (v) => Number.isInteger(v) ? v : ''
                    }
                }
            }
        })
    });
}
</script>
