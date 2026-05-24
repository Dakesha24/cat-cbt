<?php
$summary = $summary ?? [];
$durationBars = $durationBars ?? [];
$studentRows = $studentRows ?? [];
$filters = $filters ?? [];
$filterOptions = $filterOptions ?? [];
$lockSchoolFilter = $lockSchoolFilter ?? false;

$formatDuration = static function (int $seconds): string {
    if ($seconds <= 0) {
        return '-';
    }

    $hours = floor($seconds / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    $secs = $seconds % 60;

    return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
};

$selectedSchoolId = $filters['sekolah_id'] ?? null;
$selectedKelasId = $filters['kelas_id'] ?? null;
$selectedJenisUjian = $filters['tipe_ujian'] ?? null;
$selectedJadwalId = $filters['jadwal_id'] ?? null;
$selectedNomorAttempt = $filters['nomor_attempt'] ?? null;
$selectedVariabelId = $filters['variabel_id'] ?? null;
$selectedIndikatorId = $filters['indikator_id'] ?? null;
$selectedMateriId = $filters['materi_id'] ?? null;
?>

<style>
.analytics-shell { background: linear-gradient(180deg, #f8fbff 0%, #ffffff 100%); border: 1px solid #e6edf7; border-radius: 18px; }
.analytics-header { background: linear-gradient(135deg, #ffffff 0%, #f2f7ff 100%); border: 1px solid #dbe7ff; border-radius: 16px; }
.header-actions { display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap; }
.header-btn {
    height: 44px;
    min-height: 44px;
    padding: 0 1rem;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    border: 1px solid #d8e2ee;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
}
.header-btn.btn-outline-primary {
    color: #334155;
    border-color: #d8e2ee;
    background: #ffffff;
}
.header-btn.btn-outline-primary:hover,
.header-btn.btn-outline-primary:focus {
    color: #0f172a;
    border-color: #c8d5e6;
    background: #f8fafc;
}
.analytics-card { border: 1px solid #e9eef5; border-radius: 16px; box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06); overflow: hidden; }
.analytics-card .card-header { background: #fff; border-bottom: 1px solid #edf2f7; padding: 1rem 1.25rem; }
.analytics-card .card-body { padding: 1.25rem; }
.summary-tile { background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%); border: 1px solid #e9eef5; border-radius: 16px; padding: 1rem; height: 100%; }
.summary-tile .label { font-size: 0.75rem; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 0.35rem; }
.summary-tile .value { font-size: 1.65rem; font-weight: 700; color: #0f172a; line-height: 1.1; margin-bottom: 0.35rem; }
.summary-tile .subvalue { font-size: 0.82rem; color: #64748b; }
.filter-card { border: 1px solid #dbe7ff; background: #fff; border-radius: 16px; }
.filter-card .card-body { padding: 1.35rem; }
.filter-toggle-btn {
    height: 44px;
    min-height: 44px;
    border-radius: 10px;
    padding: 0.55rem 1rem 0.55rem 0.9rem;
    display: inline-flex;
    align-items: center;
    gap: 0.65rem;
    font-weight: 700;
    color: #ffffff;
    border: 1px solid #2563eb;
    background: #2563eb;
    box-shadow: none;
}
.filter-toggle-btn:hover,
.filter-toggle-btn:focus {
    color: #ffffff;
    border-color: #1d4ed8;
    background: #1d4ed8;
}
.filter-toggle-btn:focus {
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.12);
}
.filter-toggle-badge {
    width: 28px;
    height: 28px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #ffffff;
    background: rgba(255, 255, 255, 0.16);
    border: 1px solid rgba(255, 255, 255, 0.2);
    flex: 0 0 auto;
}
.filter-toggle-btn .toggle-icon { transition: transform 0.18s ease; }
.filter-toggle-btn[aria-expanded="true"] .toggle-icon { transform: rotate(180deg); }
.filter-panel { display: none; }
.filter-panel.is-open { display: block; }
.filter-form-grid { display: grid; grid-template-columns: repeat(12, minmax(0, 1fr)); gap: 1rem; align-items: start; }
.filter-section-title { grid-column: 1 / -1; margin: 0; font-size: 0.74rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.08em; }
.filter-item { grid-column: span 3; min-width: 0; }
.filter-item-wide { grid-column: 1 / -1; min-width: 0; }
.filter-item-narrow { grid-column: span 3; min-width: 0; }
.filter-item,
.filter-item-wide,
.filter-item-narrow,
.filter-item-actions { display: flex; flex-direction: column; }
.filter-item-actions { grid-column: 1 / -1; min-width: 0; justify-content: flex-start; }
.filter-actions { display: flex; justify-content: flex-end; gap: 0.75rem; align-items: start; margin-top: 0.35rem; }
.filter-card .form-label { display: block; font-size: 0.79rem; font-weight: 700; color: #334155; margin-bottom: 0.42rem; }
.filter-card .form-select { height: 46px; min-height: 46px; border-radius: 0; border-color: #d6e2f3; box-shadow: none; }
.filter-card .form-select:focus { border-color: #7cb0ff; box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.12); }
.filter-card .form-select:disabled { background: #eef3f8; color: #7b8794; border-color: #dde6f1; cursor: not-allowed; }
.filter-actions .btn { height: 46px; min-height: 46px; border-radius: 0; padding-inline: 1rem; display: inline-flex; align-items: center; justify-content: center; }
.filter-actions .btn-primary { min-width: 180px; }
.filter-actions .btn-outline-secondary { width: 52px; min-width: 52px; padding-inline: 0; }
.filter-hint { min-height: 1.05rem; font-size: 0.75rem; color: #64748b; margin-top: 0.3rem; line-height: 1.35; }
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
@media (max-width: 991.98px) {
    .filter-form-grid { grid-template-columns: repeat(6, minmax(0, 1fr)); }
    .filter-item,
    .filter-item-wide,
    .filter-item-narrow { grid-column: span 3; }
    .filter-item-wide,
    .filter-item-actions { grid-column: 1 / -1; }
}
@media (max-width: 767.98px) {
    .filter-card .card-body { padding: 1rem; }
    .header-actions { width: 100%; }
    .header-btn,
    .filter-toggle-btn { width: 100%; justify-content: center; }
    .filter-form-grid { grid-template-columns: 1fr; gap: 0.85rem; }
    .filter-item,
    .filter-item-wide,
    .filter-item-narrow,
    .filter-item-actions { grid-column: 1 / -1; }
    .filter-actions { display: grid; grid-template-columns: 1fr 52px; }
    .filter-actions .btn,
    .filter-actions .btn-primary,
    .filter-actions .btn-outline-secondary { width: 100%; min-width: 0; }
    .filter-hint { min-height: 0; }
    .chart-wrap { height: 280px; }
    .duration-chip { min-width: calc(50% - 0.4rem); }
}
</style>

<br><br><br>
<div class="container-fluid py-4">
    <div class="analytics-shell shadow-sm px-3 px-md-4 py-4">
        <div class="analytics-header d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 px-3 px-md-4 py-3 shadow-sm">
            <div>
                <h2 class="fw-bold text-dark mb-1">Rekap Data Ujian</h2>
                <p class="text-muted mb-0">Ringkasan durasi pengerjaan dan daftar siswa berdasarkan filter ujian dan metadata soal.</p>
            </div>
            <div class="header-actions">
                <a href="<?= base_url($basePath) ?>" class="btn btn-outline-primary header-btn">
                    <i class="bi bi-list-ul me-2"></i>Daftar Hasil
                </a>
                <button
                    type="button"
                    class="btn filter-toggle-btn header-btn"
                    id="analyticsFilterToggle"
                    aria-expanded="false"
                    aria-controls="analyticsFilterPanel">
                    <span class="filter-toggle-badge">
                        <i class="bi bi-sliders2"></i>
                    </span>
                    <span>Filter</span>
                    <i class="bi bi-chevron-down toggle-icon"></i>
                </button>
            </div>
        </div>

        <div class="card analytics-card filter-card mb-4 filter-panel" id="analyticsFilterPanel">
            <div class="card-body">
                <form method="get" action="<?= base_url($basePath . '/analitik') ?>">
                    <div class="filter-form-grid">
                        <div class="filter-section-title">Filter Data Ujian</div>
                        <div class="filter-item">
                            <label class="form-label">Sekolah</label>
                            <select name="sekolah_id" id="filterSekolah" class="form-select" <?= $lockSchoolFilter ? 'disabled' : '' ?>>
                                <option value="">Tampilkan Semua</option>
                                <?php foreach (($filterOptions['sekolah'] ?? []) as $sekolah): ?>
                                    <option value="<?= esc($sekolah['sekolah_id']) ?>" <?= (int) $selectedSchoolId === (int) $sekolah['sekolah_id'] ? 'selected' : '' ?>>
                                        <?= esc($sekolah['nama_sekolah']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if ($lockSchoolFilter): ?>
                                <input type="hidden" name="sekolah_id" value="<?= esc((string) $selectedSchoolId) ?>">
                            <?php endif; ?>
                        </div>
                        <div class="filter-item">
                            <label class="form-label">Kelas</label>
                            <select name="kelas_id" id="filterKelas" class="form-select">
                                <option value="">Tampilkan Semua</option>
                                <?php foreach (($filterOptions['kelas'] ?? []) as $kelas): ?>
                                    <option
                                        value="<?= esc($kelas['kelas_id']) ?>"
                                        data-sekolah-id="<?= esc((string) ($kelas['sekolah_id'] ?? '')) ?>"
                                        <?= (int) $selectedKelasId === (int) $kelas['kelas_id'] ? 'selected' : '' ?>>
                                        <?= esc($kelas['nama_kelas']) ?><?= !empty($kelas['tahun_ajaran']) ? ' - ' . esc($kelas['tahun_ajaran']) : '' ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="filter-hint">Aktif setelah sekolah spesifik dipilih.</div>
                        </div>
                        <div class="filter-item">
                            <label class="form-label">Jenis Ujian</label>
                            <select name="tipe_ujian" id="filterJenisUjian" class="form-select">
                                <option value="">Tampilkan Semua</option>
                                <?php foreach (($filterOptions['jenis_ujian'] ?? []) as $jenis): ?>
                                    <option value="<?= esc($jenis['value']) ?>" <?= $selectedJenisUjian === $jenis['value'] ? 'selected' : '' ?>>
                                        <?= esc($jenis['label']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="filter-item filter-item-wide">
                            <label class="form-label">Ujian</label>
                            <select name="jadwal_id" id="filterUjian" class="form-select">
                                <option value="">Tampilkan Semua</option>
                                <?php foreach (($filterOptions['ujian'] ?? []) as $ujian): ?>
                                    <?php
                                    $ujianLabel = trim((string) ($ujian['nama_ujian'] ?? 'Ujian'));
                                    if (!empty($ujian['nama_kelas'])) {
                                        $ujianLabel .= ' - ' . $ujian['nama_kelas'];
                                    }
                                    if (!empty($ujian['tanggal_mulai'])) {
                                        $ujianLabel .= ' - ' . date('d/m/Y H:i', strtotime($ujian['tanggal_mulai']));
                                    }
                                    ?>
                                    <option
                                        value="<?= esc($ujian['jadwal_id']) ?>"
                                        data-sekolah-id="<?= esc((string) ($ujian['sekolah_id'] ?? '')) ?>"
                                        data-kelas-id="<?= esc((string) ($ujian['kelas_id'] ?? '')) ?>"
                                        data-tipe-ujian="<?= esc((string) ($ujian['tipe_ujian'] ?? '')) ?>"
                                        <?= (int) $selectedJadwalId === (int) $ujian['jadwal_id'] ? 'selected' : '' ?>>
                                        <?= esc($ujianLabel) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="filter-hint">Daftar ujian mengikuti pilihan sekolah, kelas, dan jenis ujian.</div>
                        </div>
                        <div class="filter-item filter-item-narrow">
                            <label class="form-label">Percobaan</label>
                            <select name="nomor_attempt" id="filterPercobaan" class="form-select">
                                <option value="">Tampilkan Semua</option>
                                <?php foreach (($filterOptions['percobaan'] ?? []) as $percobaan): ?>
                                    <option value="<?= esc($percobaan['nomor_attempt']) ?>" <?= (int) $selectedNomorAttempt === (int) $percobaan['nomor_attempt'] ? 'selected' : '' ?>>
                                        Ke-<?= esc($percobaan['nomor_attempt']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="filter-hint">Muncul untuk CBT setelah ujian dipilih.</div>
                        </div>

                        <div class="filter-section-title">Filter Analisis</div>
                        <div class="filter-item">
                            <label class="form-label">Variabel</label>
                            <select name="variabel_id" id="filterVariabel" class="form-select">
                                <option value="">Tampilkan Semua</option>
                                <?php foreach (($filterOptions['variabel'] ?? []) as $variabel): ?>
                                    <option value="<?= esc($variabel['variabel_id']) ?>" <?= (int) $selectedVariabelId === (int) $variabel['variabel_id'] ? 'selected' : '' ?>>
                                        <?= esc($variabel['nama_variabel']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="filter-item">
                            <label class="form-label">Indikator</label>
                            <select name="indikator_id" id="filterIndikator" class="form-select">
                                <option value="">Tampilkan Semua</option>
                                <?php foreach (($filterOptions['indikator'] ?? []) as $indikator): ?>
                                    <option
                                        value="<?= esc($indikator['indikator_id']) ?>"
                                        data-variabel-id="<?= esc((string) ($indikator['variabel_id'] ?? '')) ?>"
                                        <?= (int) $selectedIndikatorId === (int) $indikator['indikator_id'] ? 'selected' : '' ?>>
                                        <?= esc($indikator['nama_indikator']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="filter-hint">Aktif setelah variabel spesifik dipilih.</div>
                        </div>
                        <div class="filter-item">
                            <label class="form-label">Materi</label>
                            <select name="materi_id" id="filterMateri" class="form-select">
                                <option value="">Tampilkan Semua</option>
                                <?php foreach (($filterOptions['materi'] ?? []) as $materi): ?>
                                    <option value="<?= esc($materi['materi_id']) ?>" <?= (int) $selectedMateriId === (int) $materi['materi_id'] ? 'selected' : '' ?>>
                                        <?= esc($materi['nama_materi']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="filter-item-actions">
                            <div class="filter-actions">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-funnel me-2"></i>Terapkan
                                </button>
                                <a href="<?= base_url($basePath . '/analitik') ?>" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card analytics-card chart-card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Grafik Waktu Pengerjaan Siswa</h5>
                <div class="table-note mt-1">Perbandingan rata-rata durasi pengerjaan berdasarkan fokus analisis variabel, indikator, dan materi.</div>
            </div>
            <div class="card-body">
                <div class="duration-overview">
                    <?php foreach ($durationBars as $item): ?>
                        <div class="duration-chip">
                            <div class="label"><?= esc($item['label'] ?? '-') ?></div>
                            <div class="value"><?= esc($formatDuration((int) ($item['seconds'] ?? 0))) ?></div>
                        </div>
                    <?php endforeach; ?>
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

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const durationBars = <?= json_encode($durationBars, JSON_UNESCAPED_UNICODE) ?>;

const durationChartContext = document.getElementById('durationChart');
const analyticsFilterToggle = document.getElementById('analyticsFilterToggle');
const analyticsFilterPanel = document.getElementById('analyticsFilterPanel');
const filterSekolah = document.getElementById('filterSekolah');
const filterKelas = document.getElementById('filterKelas');
const filterJenisUjian = document.getElementById('filterJenisUjian');
const filterUjian = document.getElementById('filterUjian');
const filterPercobaan = document.getElementById('filterPercobaan');
const filterVariabel = document.getElementById('filterVariabel');
const filterIndikator = document.getElementById('filterIndikator');
const filterMateri = document.getElementById('filterMateri');

function hasActiveFilters() {
    return [
        filterSekolah,
        filterKelas,
        filterJenisUjian,
        filterUjian,
        filterPercobaan,
        filterVariabel,
        filterIndikator,
        filterMateri
    ].some(select => select && select.value !== '');
}

function setFilterPanelOpen(isOpen) {
    if (!analyticsFilterPanel || !analyticsFilterToggle) {
        return;
    }

    analyticsFilterPanel.classList.toggle('is-open', isOpen);
    analyticsFilterToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
}

function resetSelect(select) {
    if (!select) {
        return;
    }

    select.value = '';
}

function toggleOptionVisibility(select, predicate) {
    if (!select) {
        return;
    }

    Array.from(select.options).forEach((option, index) => {
        if (index === 0) {
            option.hidden = false;
            option.disabled = false;
            return;
        }

        const isVisible = predicate(option);
        option.hidden = !isVisible;
        option.disabled = !isVisible;
    });

    const selectedOption = select.options[select.selectedIndex];
    if (selectedOption && selectedOption.disabled) {
        select.value = '';
    }
}

function syncFilterState() {
    const sekolahId = filterSekolah ? filterSekolah.value : '';
    const kelasId = filterKelas ? filterKelas.value : '';
    const tipeUjian = filterJenisUjian ? filterJenisUjian.value : '';
    const variabelId = filterVariabel ? filterVariabel.value : '';

    if (filterKelas) {
        toggleOptionVisibility(filterKelas, option => !sekolahId || option.dataset.sekolahId === sekolahId);
        const shouldDisableKelas = !sekolahId;
        if (shouldDisableKelas) {
            resetSelect(filterKelas);
        }
        filterKelas.disabled = shouldDisableKelas;
    }

    if (filterUjian) {
        toggleOptionVisibility(filterUjian, option => {
            const sameSchool = !sekolahId || option.dataset.sekolahId === sekolahId;
            const sameKelas = !kelasId || option.dataset.kelasId === kelasId;
            const sameTipe = !tipeUjian || option.dataset.tipeUjian === tipeUjian;
            return sameSchool && sameKelas && sameTipe;
        });
    }

    if (filterPercobaan) {
        const shouldDisablePercobaan = tipeUjian !== 'CBT' || !filterUjian || !filterUjian.value;
        if (shouldDisablePercobaan) {
            resetSelect(filterPercobaan);
        }
        filterPercobaan.disabled = shouldDisablePercobaan;
    }

    if (filterIndikator) {
        toggleOptionVisibility(filterIndikator, option => !variabelId || option.dataset.variabelId === variabelId);
        const shouldDisableIndikator = !variabelId;
        if (shouldDisableIndikator) {
            resetSelect(filterIndikator);
        }
        filterIndikator.disabled = shouldDisableIndikator;
    }
}

if (filterSekolah) {
    filterSekolah.addEventListener('change', () => {
        resetSelect(filterKelas);
        resetSelect(filterUjian);
        resetSelect(filterPercobaan);
        syncFilterState();
    });
}

if (filterKelas) {
    filterKelas.addEventListener('change', () => {
        resetSelect(filterUjian);
        resetSelect(filterPercobaan);
        syncFilterState();
    });
}

if (filterJenisUjian) {
    filterJenisUjian.addEventListener('change', () => {
        resetSelect(filterUjian);
        resetSelect(filterPercobaan);
        syncFilterState();
    });
}

if (filterUjian) {
    filterUjian.addEventListener('change', () => {
        resetSelect(filterPercobaan);
        syncFilterState();
    });
}

if (filterVariabel) {
    filterVariabel.addEventListener('change', () => {
        resetSelect(filterIndikator);
        syncFilterState();
    });
}

if (analyticsFilterToggle) {
    analyticsFilterToggle.addEventListener('click', () => {
        const isOpen = analyticsFilterToggle.getAttribute('aria-expanded') === 'true';
        setFilterPanelOpen(!isOpen);
    });
}

setFilterPanelOpen(hasActiveFilters());
syncFilterState();

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

function formatDurationCompact(seconds) {
    const total = Number(seconds || 0);

    if (total < 60) {
        return `${Math.round(total)} dtk`;
    }

    if (total < 3600) {
        const minutes = Math.floor(total / 60);
        const secs = Math.round(total % 60);
        return secs > 0 ? `${minutes}m ${secs}d` : `${minutes}m`;
    }

    const hours = Math.floor(total / 3600);
    const minutes = Math.floor((total % 3600) / 60);
    return minutes > 0 ? `${hours}j ${minutes}m` : `${hours}j`;
}

function getDurationAxisConfig(values) {
    const maxValue = Math.max(...values, 60);
    let stepSize = 30;

    if (maxValue > 300 && maxValue <= 900) {
        stepSize = 60;
    } else if (maxValue > 900 && maxValue <= 1800) {
        stepSize = 300;
    } else if (maxValue > 1800 && maxValue <= 3600) {
        stepSize = 600;
    } else if (maxValue > 3600) {
        stepSize = 900;
    }

    return {
        maxValue,
        stepSize,
        suggestedMax: Math.ceil(maxValue / stepSize) * stepSize
    };
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
    const durationValues = durationBars.map(item => Number(item.seconds || 0));
    const axisConfig = getDurationAxisConfig(durationValues);

    new Chart(durationChartContext, {
        type: 'bar',
        data: {
            labels: durationBars.map(item => item.label),
            datasets: [{
                label: 'Rata-rata Waktu Pengerjaan',
                data: durationValues,
                backgroundColor: durationBars.map(item => item.color || '#2563eb'),
                borderRadius: 0,
                maxBarThickness: 72
            }]
        },
        options: buildChartOptions({
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: (context) => ` ${formatDuration(Number(durationBars[context.dataIndex]?.seconds || 0))}`
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    suggestedMax: axisConfig.suggestedMax,
                    ticks: {
                        stepSize: axisConfig.stepSize,
                        callback: (value) => formatDurationCompact(value)
                    }
                }
            }
        })
    });
}
</script>
