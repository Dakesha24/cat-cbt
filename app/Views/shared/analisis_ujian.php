<?php
/**
 * shared/analisis_ujian.php
 * ─────────────────────────────────────────────────────────────
 * View bersama untuk halaman Analisis Hasil Ujian (admin & guru).
 * Di-include oleh:
 *   - admin/hasil/analisis_ujian.php  ($detailRole = 'admin')
 *   - guru/analisis_ujian.php         ($detailRole = 'guru')
 *
 * Variabel yang wajib dikirim dari controller:
 *   $pageRole       — 'admin' | 'guru'
 *   $basePath       — prefix URL, misal 'admin/hasil-ujian'
 *   $chartData      — array data 8 grafik (chart1–chart8)
 *   $filters        — array filter aktif (termasuk nilai_min/max, kategori, dll)
 *   $biodataFilters — array filter biodata [field_id => nilai]
 *   $selectFields   — field bertipe select dari form builder (untuk filter biodata)
 *   $filterOptions  — opsi dropdown filter (sekolah, kelas, ujian, dll)
 *   $isCbt          — bool, true jika ada data CBT dalam hasil
 *   $totalPeserta   — jumlah total peserta setelah filter
 *   $studentRows    — array data siswa untuk tabel rekap
 */

// ── Inisialisasi variabel dengan fallback ─────────────────────────────
$role             = $pageRole      ?? 'admin';
$basePath         = $basePath      ?? 'admin/hasil-ujian';
$chartData        = $chartData     ?? [];
$filters          = $filters       ?? [];
$isCbt            = $isCbt         ?? false;
$total            = $totalPeserta  ?? 0;
$studentRows      = $studentRows   ?? [];
$biodataFilters   = $biodataFilters ?? [];
$selectFields     = $selectFields  ?? [];

// Filter panel — nilai yang terpilih saat ini
$selectedJadwal   = $filters['jadwal_id']    ?? null;
$selectedTipe     = $filters['tipe_ujian']   ?? '';
$selectedKelas    = $filters['kelas_id']     ?? null;
$selectedSekolah  = $filters['sekolah_id']   ?? null;
$selectedVariabel = $filters['variabel_id']  ?? null;
$selectedIndikator= $filters['indikator_id'] ?? null;
$selectedMateri   = $filters['materi_id']    ?? null;

// Filter grafik (drill-down dari klik grafik)
$chartFilterKeys = ['nilai_min','nilai_max','kategori','theta_min','theta_max','keterangan_residu'];
$hasChartFilter  = array_reduce($chartFilterKeys, fn($carry, $k) => $carry || !empty($filters[$k]) || $filters[$k] === 0, false);
?>

<style>
/* ═══════════════════════════════════════════════
   LAYOUT UTAMA
═══════════════════════════════════════════════ */
.an-wrap { background: #f4f6f9; min-height: 100vh; padding: 28px 0 60px; }

/* ═══════════════════════════════════════════════
   FILTER PANEL
═══════════════════════════════════════════════ */
.an-filter         { background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; margin-bottom: 24px; }
.an-filter-head    { padding: 14px 20px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; cursor: pointer; user-select: none; }
.an-filter-head h6 { margin: 0; font-size: .8125rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #64748b; }
.an-filter-body    { padding: 20px; }
.an-filter-grid    { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 14px; }
.an-filter-label   { font-size: .78rem; font-weight: 600; color: #374151; margin-bottom: 5px; display: block; }
.an-filter-body .form-select,
.an-filter-body .form-control { font-size: .85rem; border-color: #e2e8f0; border-radius: 7px; }
.an-filter-body .form-select:focus { border-color: #93c5fd; box-shadow: 0 0 0 3px rgba(59,130,246,.1); }

/* ═══════════════════════════════════════════════
   STAT CARDS (ringkasan di atas grafik)
═══════════════════════════════════════════════ */
.an-stats    { display: grid; grid-template-columns: repeat(3,1fr); gap: 14px; margin-bottom: 24px; }
.an-stat     { background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 16px 20px; }
.an-stat-num { font-size: 1.75rem; font-weight: 700; color: #0f172a; line-height: 1; }
.an-stat-lbl { font-size: .75rem; color: #64748b; margin-top: 4px; text-transform: uppercase; letter-spacing: .05em; font-weight: 600; }

/* ═══════════════════════════════════════════════
   GRAFIK — grid & kartu
═══════════════════════════════════════════════ */
.an-charts-2  { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px; }
.an-chart-full{ margin-bottom: 16px; }
.an-chart-card{ background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; }
.an-chart-head{ padding: 14px 20px; border-bottom: 1px solid #f1f5f9; }
.an-chart-head h6 { margin: 0; font-size: .875rem; font-weight: 600; color: #0f172a; }
.an-chart-head p  { margin: 3px 0 0; font-size: .75rem; color: #64748b; }
.an-chart-body    { padding: 20px; }
.an-chart-canvas-wrap { position: relative; width: 100%; }

/* Badge CBT — muncul di judul grafik eksklusif CBT */
.an-cbt-badge {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: .68rem; font-weight: 700;
    background: #d1fae5; color: #065f46;
    padding: 2px 8px; border-radius: 4px; margin-left: 8px; vertical-align: middle;
}

/* Empty state — ketika belum ada data */
.an-empty   { text-align: center; padding: 40px 20px; color: #94a3b8; font-size: .875rem; }
.an-empty i { font-size: 2rem; opacity: .4; display: block; margin-bottom: 8px; }

/* ═══════════════════════════════════════════════
   TABEL REKAP SISWA
═══════════════════════════════════════════════ */
.an-tbl              { width: 100%; border-collapse: collapse; font-size: .8125rem; }
.an-tbl thead th     { background: #f8fafc; color: #64748b; font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; padding: 10px 14px; border-bottom: 2px solid #e2e8f0; white-space: nowrap; }
.an-tbl tbody td     { padding: 10px 14px; border-bottom: 1px solid #f1f5f9; color: #374151; vertical-align: middle; }
.an-tbl tbody tr:last-child td { border-bottom: none; }
.an-tbl tbody tr:hover td      { background: #fafafa; }

/* ═══════════════════════════════════════════════
   RESPONSIVE
═══════════════════════════════════════════════ */
@media (max-width: 768px) {
    .an-charts-2   { grid-template-columns: 1fr; }
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
        <h2 class="fw-bold text-dark mb-1">Analisis Hasil Ujian</h2>
        <p class="text-muted mb-0">Visualisasi dan analisis mendalam hasil ujian berdasarkan filter aktif</p>
    </div>

    <!-- ══════════════════════════════════════════
         FILTER PANEL
         - Filter utama: sekolah, kelas, tipe ujian, ujian, variabel, indikator, materi
         - Filter biodata: field select dari form builder (dinamis)
         - Tekan "Terapkan Filter" untuk submit
         - Tombol "Reset" hapus semua filter
    ══════════════════════════════════════════ -->
    <div class="an-filter">
        <div class="an-filter-head" onclick="toggleFilter()">
            <h6><i class="bi bi-sliders2 me-2"></i>Filter Data</h6>
            <div class="d-flex align-items-center gap-2">
                <?php
                    // Hitung jumlah filter aktif (kecuali array & chart filters)
                    $activeCount = count(array_filter(
                        array_merge($filters, $biodataFilters),
                        fn($v) => !empty($v) && !is_array($v) && !in_array($v, array_keys(array_flip($chartFilterKeys)))
                    ));
                ?>
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
                            <?php foreach ($filterOptions['sekolah'] ?? [] as $s): ?>
                                <option value="<?= $s['sekolah_id'] ?>"
                                    data-sekolah="<?= $s['sekolah_id'] ?>"
                                    <?= (int)$selectedSekolah === (int)$s['sekolah_id'] ? 'selected' : '' ?>>
                                    <?= esc($s['nama_sekolah']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>

                    <div>
                        <label class="an-filter-label">Kelas</label>
                        <select name="kelas_id" id="fKelas" class="form-select form-select-sm">
                            <option value="">Semua Kelas</option>
                            <?php foreach ($filterOptions['kelas'] ?? [] as $k): ?>
                                <option value="<?= $k['kelas_id'] ?>"
                                    data-sekolah="<?= $k['sekolah_id'] ?? '' ?>"
                                    <?= (int)$selectedKelas === (int)$k['kelas_id'] ? 'selected' : '' ?>>
                                    <?= esc($k['nama_kelas']) ?>
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
                        <!-- Auto-submit saat berubah agar isCbt langsung dideteksi -->
                        <select name="tipe_ujian" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">CAT & CBT</option>
                            <?php foreach ($filterOptions['jenis_ujian'] ?? [] as $j): ?>
                                <option value="<?= $j['value'] ?>" <?= $selectedTipe === $j['value'] ? 'selected' : '' ?>>
                                    <?= $j['label'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="an-filter-label">Ujian</label>
                        <select name="jadwal_id" id="fUjian" class="form-select form-select-sm">
                            <option value="">Semua Ujian</option>
                            <?php foreach ($filterOptions['ujian'] ?? [] as $u): ?>
                                <option value="<?= $u['jadwal_id'] ?>"
                                    data-tipe="<?= $u['tipe_ujian'] ?>"
                                    data-kelas="<?= $u['kelas_id'] ?? '' ?>"
                                    <?= (int)$selectedJadwal === (int)$u['jadwal_id'] ? 'selected' : '' ?>>
                                    <?= esc($u['nama_ujian']) . ($u['kode_ujian'] ? ' (' . $u['kode_ujian'] . ')' : '') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="an-filter-label">Variabel</label>
                        <select name="variabel_id" id="fVariabel" class="form-select form-select-sm" onchange="cascadeIndikator()">
                            <option value="">Semua Variabel</option>
                            <?php foreach ($filterOptions['variabel'] ?? [] as $v): ?>
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
                            <?php foreach ($filterOptions['indikator'] ?? [] as $ind): ?>
                                <option value="<?= $ind['indikator_id'] ?>"
                                    data-variabel="<?= $ind['variabel_id'] ?? '' ?>"
                                    <?= (int)$selectedIndikator === (int)$ind['indikator_id'] ? 'selected' : '' ?>>
                                    <?= esc($ind['nama_indikator']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="an-filter-label">Materi</label>
                        <select name="materi_id" class="form-select form-select-sm">
                            <option value="">Semua Materi</option>
                            <?php foreach ($filterOptions['materi'] ?? [] as $m): ?>
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
                    <a href="<?= base_url(($role === 'admin' ? 'admin' : 'guru') . '/analisis-ujian') ?>"
                       class="btn btn-outline-secondary btn-sm">
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- ══════════════════════════════════════════
         EMPTY STATE — tampil jika belum ada data
    ══════════════════════════════════════════ -->
    <?php if ($total === 0): ?>
        <div class="an-empty" style="background:#fff;border:1px solid #e2e8f0;border-radius:10px;padding:60px 20px">
            <i class="bi bi-bar-chart-line"></i>
            <strong>Belum ada data</strong><br>
            Pilih filter di atas lalu klik <strong>Terapkan Filter</strong>.
        </div>

    <?php else: ?>

    <!-- ══════════════════════════════════════════
         STAT CARDS — ringkasan cepat
    ══════════════════════════════════════════ -->
    <div class="an-stats">
        <div class="an-stat">
            <div class="an-stat-num"><?= $total ?></div>
            <div class="an-stat-lbl">Total Peserta</div>
        </div>
        <div class="an-stat">
            <!-- Tampilkan tipe dari filter panel, bukan dari $isCbt -->
            <div class="an-stat-num"><?= $selectedTipe ?: 'CAT & CBT' ?></div>
            <div class="an-stat-lbl">Tipe Ujian</div>
        </div>
        <div class="an-stat">
            <?php
                // Kategori terbanyak dari data distribusi kategori
                $katData   = $chartData['chart2']['data']   ?? [0,0,0,0,0];
                $katLabels = $chartData['chart2']['labels'] ?? [];
                $maxIdx    = array_search(max($katData), $katData);
                $dominan   = $katLabels[$maxIdx] ?? '-';
            ?>
            <div class="an-stat-num" style="font-size:1.1rem"><?= $dominan ?></div>
            <div class="an-stat-lbl">Kategori Dominan</div>
        </div>
    </div>

    <!-- ══════════════════════════════════════════
         ACTIVE CHART FILTER BADGE
         Muncul saat user mengklik salah satu grafik (drill-down).
         Menampilkan filter grafik yang sedang aktif + tombol hapus.
    ══════════════════════════════════════════ -->
    <?php if ($hasChartFilter): ?>
    <div class="d-flex align-items-center flex-wrap gap-2 mb-3 p-3"
         style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:8px">

        <i class="bi bi-funnel-fill" style="color:#3b82f6;flex-shrink:0"></i>
        <span style="font-size:.875rem;color:#1d4ed8;font-weight:500">Filter grafik aktif:</span>

        <?php if ($filters['nilai_min'] !== null || $filters['nilai_max'] !== null): ?>
            <span style="font-size:.8rem;background:#dbeafe;color:#1e40af;padding:3px 10px;border-radius:100px;font-weight:600">
                Nilai <?= $filters['nilai_min'] ?>–<?= $filters['nilai_max'] ?>
            </span>
        <?php endif; ?>

        <?php if (!empty($filters['kategori'])): ?>
            <span style="font-size:.8rem;background:#dbeafe;color:#1e40af;padding:3px 10px;border-radius:100px;font-weight:600">
                Kategori: <?= esc($filters['kategori']) ?>
            </span>
        <?php endif; ?>

        <?php if ($filters['theta_min'] !== null || $filters['theta_max'] !== null): ?>
            <span style="font-size:.8rem;background:#ede9fe;color:#5b21b6;padding:3px 10px;border-radius:100px;font-weight:600">
                θ: <?= $filters['theta_min'] ?> s.d. <?= $filters['theta_max'] ?>
            </span>
        <?php endif; ?>

        <?php if (!empty($filters['keterangan_residu'])): ?>
            <span style="font-size:.8rem;background:#fef3c7;color:#92400e;padding:3px 10px;border-radius:100px;font-weight:600">
                Residu: <?= esc($filters['keterangan_residu']) ?>
            </span>
        <?php endif; ?>

        <?php
            // URL untuk hapus semua chart filter, pertahankan filter panel
            $qClear = $_GET;
            foreach ($chartFilterKeys as $k) unset($qClear[$k]);
            $clearUrl = current_url() . ($qClear ? '?' . http_build_query($qClear) : '');
        ?>
        <a href="<?= esc($clearUrl, 'attr') ?>"
           style="margin-left:auto;font-size:.8rem;color:#64748b;text-decoration:none;display:flex;align-items:center;gap:4px;white-space:nowrap">
            <i class="bi bi-x-circle"></i> Hapus filter grafik
        </a>
    </div>
    <?php endif; ?>

    <!-- ══════════════════════════════════════════
         GRAFIK BARIS 1: Distribusi Nilai + Kategori
         Keduanya berlaku untuk CAT maupun CBT.
         Klik bar/slice → drill-down ke rentang/kategori tersebut.
    ══════════════════════════════════════════ -->
    <div class="an-charts-2">
        <div class="an-chart-card">
            <div class="an-chart-head">
                <h6>Distribusi Nilai</h6>
                <p>Sebaran skor peserta per rentang nilai · klik bar untuk drill-down</p>
            </div>
            <div class="an-chart-body"><div class="an-chart-canvas-wrap" style="height:260px"><canvas id="chartDistribusiNilai"></canvas></div></div>
        </div>
        <div class="an-chart-card">
            <div class="an-chart-head">
                <h6>Distribusi Kategori Kemampuan</h6>
                <p>Komposisi kategori peserta · klik slice untuk drill-down</p>
            </div>
            <div class="an-chart-body"><div class="an-chart-canvas-wrap" style="height:260px"><canvas id="chartKategori"></canvas></div></div>
        </div>
    </div>

    <!-- ══════════════════════════════════════════
         GRAFIK BARIS 2: Rata-rata per Kelompok + Durasi
         Rata-rata: klik bar sekolah/kelas → set filter sekolah/kelas.
         Durasi: informatif saja, tidak ada drill-down.
    ══════════════════════════════════════════ -->
    <div class="an-charts-2">
        <div class="an-chart-card">
            <div class="an-chart-head">
                <h6>Rata-rata Nilai per <?= !empty($filters['kelas_id']) ? 'Kelas' : ($role === 'guru' ? 'Kelas' : 'Sekolah') ?></h6>
                <p>Perbandingan rata-rata skor antar kelompok<?php if (!empty($biodataFilters)): ?> · filter biodata aktif<?php endif; ?></p>
            </div>
            <div class="an-chart-body"><div class="an-chart-canvas-wrap" style="height:220px"><canvas id="chartKelompok"></canvas></div></div>
        </div>
        <div class="an-chart-card">
            <div class="an-chart-head">
                <h6>Distribusi Durasi Pengerjaan</h6>
                <p>Sebaran waktu yang digunakan peserta untuk menyelesaikan ujian</p>
            </div>
            <div class="an-chart-body"><div class="an-chart-canvas-wrap" style="height:220px"><canvas id="chartDurasi"></canvas></div></div>
        </div>
    </div>

    <!-- ══════════════════════════════════════════
         GRAFIK BARIS 3: Rata-rata Nilai per Jenis Kelamin & Variabel
         Berlaku untuk CAT maupun CBT.
         Klik bar Variabel → set filter variabel_id.
    ══════════════════════════════════════════ -->
    <div class="an-charts-2">
        <div class="an-chart-card">
            <div class="an-chart-head">
                <h6>Rata-rata Nilai per Jenis Kelamin</h6>
                <p>Perbandingan rata-rata skor peserta laki-laki dan perempuan</p>
            </div>
            <div class="an-chart-body"><div class="an-chart-canvas-wrap" style="height:220px"><canvas id="chartGender"></canvas></div></div>
        </div>
        <div class="an-chart-card">
            <div class="an-chart-head">
                <h6>Rata-rata Nilai per Variabel</h6>
                <p>Persentase jawaban benar per variabel · klik bar untuk filter</p>
            </div>
            <div class="an-chart-body">
                <?php if (empty($chartData['chart10']['labels'])): ?>
                    <div class="an-empty"><i class="bi bi-bar-chart"></i>Belum ada data jawaban per variabel</div>
                <?php else: ?>
                    <div class="an-chart-canvas-wrap" style="height:220px"><canvas id="chartVariabel"></canvas></div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ══════════════════════════════════════════
         GRAFIK BARIS 4: Rata-rata Nilai per Indikator & Materi
         Berlaku untuk CAT maupun CBT.
         Klik bar → set filter indikator_id / materi_id.
    ══════════════════════════════════════════ -->
    <div class="an-charts-2">
        <div class="an-chart-card">
            <div class="an-chart-head">
                <h6>Rata-rata Nilai per Indikator</h6>
                <p>Persentase jawaban benar per indikator · klik bar untuk filter</p>
            </div>
            <div class="an-chart-body">
                <?php if (empty($chartData['chart11']['labels'])): ?>
                    <div class="an-empty"><i class="bi bi-bar-chart"></i>Belum ada data jawaban per indikator</div>
                <?php else: ?>
                    <div class="an-chart-canvas-wrap" style="height:260px"><canvas id="chartIndikator"></canvas></div>
                <?php endif; ?>
            </div>
        </div>
        <div class="an-chart-card">
            <div class="an-chart-head">
                <h6>Rata-rata Nilai per Materi</h6>
                <p>Persentase jawaban benar per materi · klik bar untuk filter</p>
            </div>
            <div class="an-chart-body">
                <?php if (empty($chartData['chart12']['labels'])): ?>
                    <div class="an-empty"><i class="bi bi-bar-chart"></i>Belum ada data jawaban per materi</div>
                <?php else: ?>
                    <div class="an-chart-canvas-wrap" style="height:260px"><canvas id="chartMateri"></canvas></div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ══════════════════════════════════════════
         GRAFIK CBT (hanya muncul jika ada data CBT)
         chart4: Distribusi θ_EAP  → klik untuk drill-down per rentang theta
         chart5: Scatter durasi vs nilai → informatif, tidak ada drill-down
         chart6: Analisis Residu   → klik Lucky Guess/Ceroboh untuk drill-down
         chart7: SEM vs Nilai      → informatif, tidak ada drill-down
    ══════════════════════════════════════════ -->
    <?php if ($isCbt): ?>
    <div class="an-charts-2">
        <div class="an-chart-card">
            <div class="an-chart-head">
                <h6>Distribusi θ_EAP <span class="an-cbt-badge">CBT</span></h6>
                <p>Sebaran kemampuan laten (skala IRT) · klik bar untuk drill-down</p>
            </div>
            <div class="an-chart-body"><div class="an-chart-canvas-wrap" style="height:260px"><canvas id="chartTheta"></canvas></div></div>
        </div>
        <div class="an-chart-card">
            <div class="an-chart-head">
                <h6>Durasi vs Nilai <span class="an-cbt-badge">CBT</span></h6>
                <p>Korelasi waktu pengerjaan dengan skor akhir</p>
            </div>
            <div class="an-chart-body"><div class="an-chart-canvas-wrap" style="height:260px"><canvas id="chartScatter"></canvas></div></div>
        </div>
    </div>

    <div class="an-charts-2">
        <div class="an-chart-card">
            <div class="an-chart-head">
                <h6>Analisis Residu <span class="an-cbt-badge">CBT</span></h6>
                <p>Lucky Guess, Ceroboh, Normal per rentang nilai · klik Lucky Guess/Ceroboh untuk drill-down</p>
            </div>
            <div class="an-chart-body"><div class="an-chart-canvas-wrap" style="height:260px"><canvas id="chartResidu"></canvas></div></div>
        </div>
        <div class="an-chart-card">
            <div class="an-chart-head">
                <h6>Standard Error (SEM) per Nilai <span class="an-cbt-badge">CBT</span></h6>
                <p>Rata-rata SEM estimasi kemampuan — indikator presisi pengukuran</p>
            </div>
            <div class="an-chart-body"><div class="an-chart-canvas-wrap" style="height:260px"><canvas id="chartSEM"></canvas></div></div>
        </div>
    </div>
    <?php endif; ?>

    <?php endif; // end $total > 0 ?>

    <!-- ══════════════════════════════════════════
         TABEL REKAP SISWA
         Tampil jika ada data, berisi semua peserta sesuai filter.
         Kolom θ_EAP dan SEM hanya muncul jika CBT.
         Tombol Export CSV di kanan atas.
    ══════════════════════════════════════════ -->
    <?php if ($total > 0): ?>
    <div class="an-chart-card mt-2">
        <div class="an-chart-head d-flex justify-content-between align-items-center" style="padding:14px 20px">
            <div>
                <h6 style="margin:0;font-size:.875rem;font-weight:600;color:#0f172a">Rekap Siswa</h6>
                <p style="margin:3px 0 0;font-size:.75rem;color:#64748b"><?= $total ?> peserta sesuai filter aktif</p>
            </div>
            <button class="btn btn-sm btn-outline-secondary" onclick="exportCSV()" style="font-size:.78rem">
                <i class="bi bi-download me-1"></i>Export CSV
            </button>
        </div>

        <div class="table-responsive">
            <table class="an-tbl" id="tblSiswa">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Siswa</th>
                        <th>No. Peserta</th>
                        <?php if ($role === 'admin'): ?><th>Sekolah</th><?php endif; ?>
                        <th>Kelas</th>
                        <th>Ujian</th>
                        <th>Nilai</th>
                        <th>Kategori</th>
                        <?php if ($isCbt): ?>
                            <th>θ_EAP</th>
                            <th>SEM</th>
                            <th>Lucky Guess</th>
                            <th>Ceroboh</th>
                        <?php endif; ?>
                        <th>Durasi</th>
                        <th>Detail</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($studentRows as $idx => $row):
                        // Hitung kategori dan warna pill
                        $s = $row['skor_akhir'] ?? 0;
                        if      ($s >= 75) { $katLabel = 'Sangat Baik';  $katColor = '#16a34a'; $katBg = '#dcfce7'; }
                        elseif  ($s >= 58) { $katLabel = 'Baik';         $katColor = '#0891b2'; $katBg = '#e0f2fe'; }
                        elseif  ($s >= 42) { $katLabel = 'Cukup';        $katColor = '#d97706'; $katBg = '#fef3c7'; }
                        elseif  ($s >= 25) { $katLabel = 'Rendah';       $katColor = '#ea580c'; $katBg = '#ffedd5'; }
                        else               { $katLabel = 'Sangat Rendah';$katColor = '#dc2626'; $katBg = '#fee2e2'; }

                        // Format durasi
                        $d = $row['durasi_menit'] ?? 0;
                        $durasiFmt = $d > 0
                            ? floor($d) . ' mnt ' . round(($d - floor($d)) * 60) . ' dtk'
                            : '—';
                    ?>
                    <tr>
                        <td><?= $idx + 1 ?></td>
                        <td><span style="font-weight:500;color:#0f172a"><?= esc($row['nama_lengkap'] ?? '—') ?></span></td>
                        <td><span style="font-family:monospace;font-size:.8rem;color:#64748b"><?= esc($row['nomor_peserta'] ?? '—') ?></span></td>
                        <?php if ($role === 'admin'): ?>
                            <td><?= esc($row['nama_sekolah'] ?? '—') ?></td>
                        <?php endif; ?>
                        <td><?= esc($row['nama_kelas'] ?? '—') ?></td>
                        <td style="max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"
                            title="<?= esc($row['nama_ujian'] ?? '') ?>">
                            <?= esc($row['nama_ujian'] ?? '—') ?>
                        </td>
                        <td><span style="font-weight:700;color:#0f172a"><?= number_format((float)($row['skor_akhir'] ?? 0), 2) ?></span></td>
                        <td>
                            <span style="font-size:.72rem;font-weight:700;padding:3px 8px;border-radius:100px;background:<?= $katBg ?>;color:<?= $katColor ?>">
                                <?= $katLabel ?>
                            </span>
                        </td>
                        <?php if ($isCbt): ?>
                            <td><span style="font-family:monospace;font-size:.8rem;color:#64748b"><?= $row['theta_akhir'] !== null ? number_format((float)$row['theta_akhir'], 4) : '—' ?></span></td>
                            <td><span style="font-family:monospace;font-size:.8rem;color:#64748b"><?= $row['sem_akhir']   !== null ? number_format((float)$row['sem_akhir'],   4) : '—' ?></span></td>
                            <td>
                                <?php $lg = (int)($row['lucky_guess_count'] ?? 0); ?>
                                <?php if ($lg > 0): ?>
                                    <span style="font-size:.75rem;font-weight:700;background:#fef3c7;color:#92400e;padding:2px 8px;border-radius:100px"><?= $lg ?> soal</span>
                                <?php else: ?>
                                    <span style="color:#cbd5e1;font-size:.8rem">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php $cb = (int)($row['ceroboh_count'] ?? 0); ?>
                                <?php if ($cb > 0): ?>
                                    <span style="font-size:.75rem;font-weight:700;background:#fee2e2;color:#b91c1c;padding:2px 8px;border-radius:100px"><?= $cb ?> soal</span>
                                <?php else: ?>
                                    <span style="color:#cbd5e1;font-size:.8rem">—</span>
                                <?php endif; ?>
                            </td>
                        <?php endif; ?>
                        <td><span style="font-size:.8rem;color:#64748b"><?= $durasiFmt ?></span></td>
                        <td>
                            <?php
                                // URL detail sesuai role — CBT ke percobaan, CAT ke detail langsung
                                $detailUrl = $role === 'guru'
                                    ? base_url('guru/hasil-ujian/' . (($row['tipe_ujian'] ?? '') === 'CBT' ? 'percobaan' : 'detail') . '/' . $row['peserta_ujian_id'])
                                    : base_url('admin/hasil-ujian/' . (($row['tipe_ujian'] ?? '') === 'CBT' ? 'percobaan' : 'detail') . '/' . $row['peserta_ujian_id']);
                            ?>
                            <a href="<?= $detailUrl ?>"
                               target="_blank"
                               style="display:inline-flex;align-items:center;gap:4px;font-size:.78rem;padding:4px 10px;border-radius:6px;border:1px solid #bfdbfe;color:#2563eb;text-decoration:none;white-space:nowrap;transition:background .1s"
                               onmouseover="this.style.background='#eff6ff'" onmouseout="this.style.background=''">
                                <i class="bi bi-arrow-up-right-square"></i> Detail
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

</div>
</div>

<!-- ══════════════════════════════════════════════════════════════════
     JAVASCRIPT
     Chart.js disimpan lokal di public/js/chart.umd.min.js
══════════════════════════════════════════════════════════════════ -->
<script src="<?= base_url('js/chart.umd.min.js') ?>"></script>
<script>
'use strict';

// ── Data dari controller (di-encode sebagai JSON) ─────────────────────
const chartData = <?= json_encode($chartData, JSON_UNESCAPED_UNICODE) ?>;
const isCbt     = <?= $isCbt ? 'true' : 'false' ?>;
const BASE_URL  = '<?= current_url() ?>';

// ── Filter grafik yang sedang aktif (dari URL) ────────────────────────
const CHART_FILTER_KEYS = ['nilai_min','nilai_max','kategori','theta_min','theta_max','keterangan_residu'];
const activeNilaiMin    = <?= $filters['nilai_min']  !== null ? (int)$filters['nilai_min']   : 'null' ?>;
const activeNilaiMax    = <?= $filters['nilai_max']  !== null ? (int)$filters['nilai_max']   : 'null' ?>;
const activeKategori    = <?= !empty($filters['kategori'])          ? json_encode($filters['kategori'])          : 'null' ?>;
const activeThetaMin    = <?= $filters['theta_min']  !== null ? (float)$filters['theta_min'] : 'null' ?>;
const activeThetaMax    = <?= $filters['theta_max']  !== null ? (float)$filters['theta_max'] : 'null' ?>;
const activeResidu      = <?= !empty($filters['keterangan_residu']) ? json_encode($filters['keterangan_residu']) : 'null' ?>;

// ── Palet warna konsisten untuk semua grafik ──────────────────────────
const COLORS = {
    blue:    '#3b82f6', blueA:   'rgba(59,130,246,.15)',
    green:   '#22c55e', greenA:  'rgba(34,197,94,.15)',
    amber:   '#f59e0b', amberA:  'rgba(245,158,11,.15)',
    red:     '#ef4444', redA:    'rgba(239,68,68,.15)',
    purple:  '#8b5cf6', purpleA: 'rgba(139,92,246,.15)',
    teal:    '#14b8a6',
};
// Warna per kategori: Sangat Rendah, Rendah, Cukup, Baik, Sangat Baik
const KAT_COLORS = ['#ef4444','#f97316','#eab308','#22c55e','#3b82f6'];

// ── Opsi dasar Chart.js (dipakai ulang di beberapa grafik) ────────────
const baseOpts = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: {
        x: { grid: { display: false }, border: { display: false } },
        y: { grid: { color: '#f1f5f9' }, border: { display: false } },
    },
};

// ── Fungsi drill-down: redirect dengan tambah chart filter ke URL ─────
// Hapus semua chart filter lama dulu, lalu set yang baru.
function drillDown(params) {
    const p = new URLSearchParams(window.location.search);
    CHART_FILTER_KEYS.forEach(k => p.delete(k));
    Object.entries(params).forEach(([k, v]) => p.set(k, v));
    window.location.href = BASE_URL + '?' + p.toString();
}

// ── Helper: label di atas bar ─────────────────────────────────────────
// Dipakai sebagai plugin inline pada chart bar.
function barLabelPlugin(getFn) {
    return {
        afterDatasetsDraw(chart) {
            const { ctx } = chart;
            chart.data.datasets.forEach((_, di) => {
                chart.getDatasetMeta(di).data.forEach((bar, i) => {
                    const val = chart.data.datasets[di].data[i];
                    if (!val) return;
                    const label = getFn ? getFn(val, i) : `${val}`;
                    ctx.save();
                    ctx.textAlign    = 'center';
                    ctx.textBaseline = 'bottom';
                    ctx.font         = '600 11px system-ui,sans-serif';
                    ctx.fillStyle    = '#374151';
                    ctx.fillText(label, bar.x, bar.y - 6);
                    ctx.restore();
                });
            });
        },
    };
}

/* ═══════════════════════════════════════════════════════════════════════
   GRAFIK 1 — Distribusi Nilai
   Bar chart dengan 5 rentang nilai (0-20, 21-40, 41-60, 61-80, 81-100).
   Warna gradasi: merah (rendah) → hijau (tinggi).
   Klik bar → drillDown ke rentang nilai tersebut.
   Bar aktif tetap terang; bar lain memudar.
═══════════════════════════════════════════════════════════════════════ */
if (chartData.chart1 && document.getElementById('chartDistribusiNilai')) {
    const c1data  = chartData.chart1.data;
    const c1total = c1data.reduce((a, b) => a + b, 0);
    const c1max   = Math.max(...c1data);

    // Definisi rentang dan warna per bar
    const c1Ranges = [[0,20],[21,40],[41,60],[61,80],[81,100]];
    const c1Colors = [
        { bg: 'rgba(239,68,68,.15)',  border: '#ef4444' },  // 0–20   merah
        { bg: 'rgba(249,115,22,.15)', border: '#f97316' },  // 21–40  oranye
        { bg: 'rgba(234,179,8,.15)',  border: '#eab308' },  // 41–60  kuning
        { bg: 'rgba(34,197,94,.15)',  border: '#22c55e' },  // 61–80  hijau
        { bg: 'rgba(16,185,129,.18)', border: '#10b981' },  // 81–100 teal
    ];

    // Temukan index bar yang aktif berdasarkan filter nilai_min
    const c1ActiveIdx = activeNilaiMin !== null
        ? c1Ranges.findIndex(r => r[0] === activeNilaiMin)
        : -1;

    // Redup-kan bar yang tidak aktif jika ada filter aktif
    const c1BgColors = c1Colors.map((c, i) =>
        c1ActiveIdx >= 0 && i !== c1ActiveIdx ? c.bg.replace(/[\d.]+\)$/, '.05)') : c.bg
    );
    const c1BdColors = c1Colors.map((c, i) =>
        c1ActiveIdx >= 0 && i !== c1ActiveIdx ? c.border + '55' : c.border
    );

    new Chart('chartDistribusiNilai', {
        type: 'bar',
        data: {
            labels: chartData.chart1.labels,
            datasets: [{
                data:            c1data,
                backgroundColor: c1BgColors,
                borderColor:     c1BdColors,
                borderWidth:     2,
                borderRadius:    0,
                borderSkipped:   false,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            layout:    { padding: { top: 20 } },
            animation: { duration: 600, easing: 'easeOutQuart' },
            onClick(e, els) {
                if (!els.length) return;
                const r = c1Ranges[els[0].index];
                if (r) drillDown({ nilai_min: r[0], nilai_max: r[1] });
            },
            onHover(e, els) {
                e.native.target.style.cursor = els.length ? 'pointer' : 'default';
            },
            plugins: {
                legend:  { display: false },
                tooltip: {
                    backgroundColor: '#1e293b',
                    padding:         10,
                    titleFont:       { size: 12, weight: '600' },
                    bodyFont:        { size: 12 },
                    callbacks: {
                        title: ctx => 'Nilai ' + ctx[0].label,
                        label: ctx => {
                            const pct = c1total > 0 ? ((ctx.raw / c1total) * 100).toFixed(1) : 0;
                            return `  ${ctx.raw} peserta  (${pct}%)`;
                        },
                    },
                },
            },
            scales: {
                x: {
                    grid:   { display: false },
                    border: { display: false },
                    ticks:  { color: '#64748b', font: { size: 12 } },
                    title:  { display: true, text: 'Rentang Nilai', color: '#94a3b8', font: { size: 11 } },
                },
                y: {
                    grid:   { color: '#f1f5f9' },
                    border: { display: false },
                    ticks:  { color: '#94a3b8', font: { size: 11 }, stepSize: 1, callback: v => Number.isInteger(v) ? v : '' },
                    title:  { display: true, text: 'Jumlah Peserta', color: '#94a3b8', font: { size: 11 } },
                    suggestedMax: c1max + Math.ceil(c1max * 0.15),
                },
            },
        },
        plugins: [barLabelPlugin()],
    });
}

/* ═══════════════════════════════════════════════════════════════════════
   GRAFIK 2 — Distribusi Kategori Kemampuan
   Donut chart dengan 5 kategori.
   Klik slice → drillDown ke kategori tersebut.
═══════════════════════════════════════════════════════════════════════ */
if (chartData.chart2 && document.getElementById('chartKategori')) {
    new Chart('chartKategori', {
        type: 'doughnut',
        data: {
            labels:   chartData.chart2.labels,
            datasets: [{
                data:            chartData.chart2.data,
                backgroundColor: KAT_COLORS,
                borderWidth:     0,
                hoverOffset:     6,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            onClick(e, els) {
                if (!els.length) return;
                drillDown({ kategori: chartData.chart2.labels[els[0].index] });
            },
            onHover(e, els) {
                e.native.target.style.cursor = els.length ? 'pointer' : 'default';
            },
            plugins: {
                legend:  { display: true, position: 'bottom', labels: { padding: 16, font: { size: 12 } } },
                tooltip: { callbacks: { label: ctx => ` ${ctx.label}: ${ctx.raw} peserta` } },
            },
        },
    });
}

/* ═══════════════════════════════════════════════════════════════════════
   GRAFIK 3 — Rata-rata Nilai per Kelompok
   Horizontal bar. Kelompok = sekolah (admin) atau kelas (guru/filter kelas).
   Klik bar → set filter sekolah_id atau kelas_id di URL.
   Warna solid biru seragam.
═══════════════════════════════════════════════════════════════════════ */
if (chartData.chart3 && document.getElementById('chartKelompok')) {
    const c3 = chartData.chart3;

    new Chart('chartKelompok', {
        type: 'bar',
        data: {
            labels:   c3.labels,
            datasets: [{
                data:            c3.data,
                backgroundColor: COLORS.blue,
                borderWidth:     0,
                borderRadius:    0,
                borderSkipped:   false,
            }],
        },
        options: {
            ...baseOpts,
            indexAxis: 'y',
            onClick(e, els) {
                if (!els.length) return;
                const id  = c3.ids ? c3.ids[els[0].index] : null;
                if (!id) return;
                const p   = new URLSearchParams(window.location.search);
                const key = c3.groupBy === 'kelas' ? 'kelas_id' : 'sekolah_id';
                p.set(key, id);
                window.location.href = BASE_URL + '?' + p.toString();
            },
            onHover(e, els) {
                e.native.target.style.cursor = (c3.ids && els.length) ? 'pointer' : 'default';
            },
            plugins: {
                legend:  { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ` Rata-rata: ${ctx.raw} | ${c3.counts[ctx.dataIndex]} peserta`,
                    },
                },
            },
            scales: {
                x: {
                    min:    0,
                    max:    100,
                    grid:   { color: '#f1f5f9' },
                    border: { display: false },
                    title:  { display: true, text: 'Rata-rata Nilai (0–100)', color: '#94a3b8', font: { size: 11 } },
                },
                y: { grid: { display: false }, border: { display: false } },
            },
        },
    });
}

/* ═══════════════════════════════════════════════════════════════════════
   GRAFIK 8 — Distribusi Durasi Pengerjaan
   Bar chart warna-warni (satu warna per rentang waktu).
   Tidak ada drill-down — hanya informatif.
   Label angka di atas bar; persentase muncul di tooltip.
═══════════════════════════════════════════════════════════════════════ */
if (chartData.chart8 && document.getElementById('chartDurasi')) {
    const c8      = chartData.chart8;
    const c8total = c8.data.reduce((a, b) => a + b, 0);
    const c8max   = Math.max(...c8.data);

    new Chart('chartDurasi', {
        type: 'bar',
        data: {
            labels:   c8.labels,
            datasets: [{
                data:            c8.data,
                // Warna berbeda per rentang untuk memudahkan pembacaan
                backgroundColor: ['#6366f1','#8b5cf6','#06b6d4','#10b981','#f59e0b','#ef4444'],
                borderWidth:     0,
                borderRadius:    0,
                borderSkipped:   false,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            layout:    { padding: { top: 18 } },
            animation: { duration: 500, easing: 'easeOutQuart' },
            plugins: {
                legend:  { display: false },
                tooltip: {
                    backgroundColor: '#1e293b',
                    padding:         10,
                    callbacks: {
                        title:     ctx  => ctx[0].label,
                        label:     ctx  => {
                            const pct = c8total > 0 ? ((ctx.raw / c8total) * 100).toFixed(1) : 0;
                            return `  ${ctx.raw} peserta  (${pct}%)`;
                        },
                        afterBody: () => `  Rata-rata: ${c8.avg} menit`,
                    },
                },
            },
            scales: {
                x: {
                    grid:   { display: false },
                    border: { display: false },
                    ticks:  { color: '#64748b', font: { size: 11 }, maxRotation: 0, minRotation: 0 },
                    title:  { display: true, text: 'Rentang Waktu', color: '#94a3b8', font: { size: 11 } },
                },
                y: {
                    grid:   { color: '#f1f5f9' },
                    border: { display: false },
                    ticks:  { color: '#94a3b8', font: { size: 11 }, stepSize: 1, callback: v => Number.isInteger(v) ? v : '' },
                    title:  { display: true, text: 'Jumlah Peserta', color: '#94a3b8', font: { size: 11 } },
                    suggestedMax: c8max + Math.ceil(c8max * 0.2),
                },
            },
        },
        plugins: [barLabelPlugin()],
    });
}

/* ═══════════════════════════════════════════════════════════════════════
   GRAFIK 9 — Rata-rata Nilai per Jenis Kelamin
   Bar chart 2 batang (Laki-laki / Perempuan). Berlaku CAT & CBT.
═══════════════════════════════════════════════════════════════════════ */
if (chartData.chart9 && document.getElementById('chartGender')) {
    const c9 = chartData.chart9;

    new Chart('chartGender', {
        type: 'bar',
        data: {
            labels:   c9.labels,
            datasets: [{
                data:            c9.data,
                backgroundColor: [COLORS.blueA, 'rgba(236,72,153,.15)'],
                borderColor:     [COLORS.blue, '#ec4899'],
                borderWidth:     2,
                borderRadius:    0,
                borderSkipped:   false,
            }],
        },
        options: {
            ...baseOpts,
            plugins: {
                legend:  { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ` Rata-rata: ${ctx.raw} | ${c9.counts[ctx.dataIndex]} peserta`,
                    },
                },
            },
            scales: {
                x: { grid: { display: false }, border: { display: false } },
                y: {
                    min: 0, max: 100,
                    grid:   { color: '#f1f5f9' },
                    border: { display: false },
                    title:  { display: true, text: 'Rata-rata Nilai (0–100)', color: '#94a3b8', font: { size: 11 } },
                },
            },
        },
        plugins: [barLabelPlugin()],
    });
}

/* ═══════════════════════════════════════════════════════════════════════
   GRAFIK 10–12 — Rata-rata Nilai per Variabel / Indikator / Materi
   Horizontal bar, skala 0–100 (% jawaban benar). Berlaku CAT & CBT.
   Klik bar → set filter variabel_id / indikator_id / materi_id.
═══════════════════════════════════════════════════════════════════════ */
function renderMetadataChart(canvasId, chartObj, filterKey) {
    if (!chartObj || !document.getElementById(canvasId) || !chartObj.labels.length) return;

    new Chart(canvasId, {
        type: 'bar',
        data: {
            labels:   chartObj.labels,
            datasets: [{
                data:            chartObj.data,
                backgroundColor: COLORS.purpleA,
                borderColor:     COLORS.purple,
                borderWidth:     2,
                borderRadius:    0,
                borderSkipped:   false,
            }],
        },
        options: {
            ...baseOpts,
            indexAxis: 'y',
            onClick(e, els) {
                if (!els.length) return;
                const id = chartObj.ids ? chartObj.ids[els[0].index] : null;
                if (!id) return;
                const p = new URLSearchParams(window.location.search);
                p.set(filterKey, id);
                window.location.href = BASE_URL + '?' + p.toString();
            },
            onHover(e, els) {
                e.native.target.style.cursor = (chartObj.ids && els.length) ? 'pointer' : 'default';
            },
            plugins: {
                legend:  { display: false },
                tooltip: { callbacks: { label: ctx => ` Rata-rata: ${ctx.raw}` } },
            },
            scales: {
                x: {
                    min:    0,
                    max:    100,
                    grid:   { color: '#f1f5f9' },
                    border: { display: false },
                    title:  { display: true, text: 'Rata-rata Nilai (0–100)', color: '#94a3b8', font: { size: 11 } },
                },
                y: { grid: { display: false }, border: { display: false } },
            },
        },
    });
}
renderMetadataChart('chartVariabel',  chartData.chart10, 'variabel_id');
renderMetadataChart('chartIndikator', chartData.chart11, 'indikator_id');
renderMetadataChart('chartMateri',    chartData.chart12, 'materi_id');

/* ═══════════════════════════════════════════════════════════════════════
   GRAFIK CBT (hanya di-render jika isCbt = true)
═══════════════════════════════════════════════════════════════════════ */
if (isCbt) {

    /* ─────────────────────────────────────────────────────────────────
       GRAFIK 4 — Distribusi θ_EAP
       Histogram 6 rentang theta. Klik bar → drillDown ke rentang theta.
    ───────────────────────────────────────────────────────────────── */
    if (chartData.chart4 && document.getElementById('chartTheta')) {
        // Rentang theta: batas kiri inklusif, batas kanan eksklusif
        const thetaRanges = [[-99,-2],[-2,-1],[-1,0],[0,1],[1,2],[2,99]];

        new Chart('chartTheta', {
            type: 'bar',
            data: {
                labels:   chartData.chart4.labels,
                datasets: [{
                    data:            chartData.chart4.data,
                    backgroundColor: COLORS.purpleA,
                    borderColor:     COLORS.purple,
                    borderWidth:     2,
                    borderRadius:    0,
                    borderSkipped:   false,
                }],
            },
            options: {
                ...baseOpts,
                onClick(e, els) {
                    if (!els.length) return;
                    const r = thetaRanges[els[0].index];
                    if (r) drillDown({ theta_min: r[0], theta_max: r[1] });
                },
                onHover(e, els) {
                    e.native.target.style.cursor = els.length ? 'pointer' : 'default';
                },
                plugins: {
                    legend:  { display: false },
                    tooltip: { callbacks: { label: ctx => ` ${ctx.raw} peserta` } },
                },
                scales: {
                    x: {
                        grid:   { display: false },
                        border: { display: false },
                        ticks:  { color: '#64748b', font: { size: 12 } },
                        title:  { display: true, text: 'Rentang θ_EAP', color: '#94a3b8', font: { size: 11 } },
                    },
                    y: {
                        grid:   { color: '#f1f5f9' },
                        border: { display: false },
                        ticks:  { color: '#94a3b8', font: { size: 11 }, stepSize: 1, callback: v => Number.isInteger(v) ? v : '' },
                        title:  { display: true, text: 'Jumlah Peserta', color: '#94a3b8', font: { size: 11 } },
                    },
                },
            },
        });
    }

    /* ─────────────────────────────────────────────────────────────────
       GRAFIK 5 — Scatter: Durasi vs Nilai
       Satu titik = satu siswa. Tidak ada drill-down (terlalu granular).
    ───────────────────────────────────────────────────────────────── */
    if (chartData.chart5 && document.getElementById('chartScatter')) {
        new Chart('chartScatter', {
            type: 'scatter',
            data: {
                datasets: [{
                    data:             chartData.chart5,
                    backgroundColor:  'rgba(59,130,246,.4)',
                    borderColor:      COLORS.blue,
                    pointRadius:      5,
                    pointHoverRadius: 7,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend:  { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => ` Durasi: ${ctx.parsed.x} mnt | Nilai: ${ctx.parsed.y}`,
                        },
                    },
                },
                scales: {
                    x: {
                        grid:   { color: '#f1f5f9' },
                        border: { display: false },
                        title:  { display: true, text: 'Durasi (menit)', color: '#64748b', font: { size: 11 } },
                    },
                    y: {
                        min:    0,
                        max:    100,
                        grid:   { color: '#f1f5f9' },
                        border: { display: false },
                        title:  { display: true, text: 'Nilai', color: '#64748b', font: { size: 11 } },
                    },
                },
            },
        });
    }

    /* ─────────────────────────────────────────────────────────────────
       GRAFIK 6 — Analisis Residu (Stacked Bar)
       3 dataset: Normal, Lucky Guess, Ceroboh per rentang nilai.
       Klik bar Lucky Guess / Ceroboh → drillDown ke keterangan_residu.
       Normal tidak bisa diklik (tidak informatif untuk drill-down).
    ───────────────────────────────────────────────────────────────── */
    if (chartData.chart6 && document.getElementById('chartResidu')) {
        const c6         = chartData.chart6;
        const c6DsLabels = ['Normal','Lucky Guess','Ceroboh'];

        new Chart('chartResidu', {
            type: 'bar',
            data: {
                labels:   c6.labels,
                datasets: [
                    { label: 'Normal',      data: c6.normal,     backgroundColor: COLORS.greenA, borderColor: COLORS.green, borderWidth: 2 },
                    { label: 'Lucky Guess', data: c6.luckyGuess, backgroundColor: COLORS.amberA, borderColor: COLORS.amber, borderWidth: 2 },
                    { label: 'Ceroboh',     data: c6.ceroboh,    backgroundColor: COLORS.redA,   borderColor: COLORS.red,   borderWidth: 2 },
                ],
            },
            options: {
                ...baseOpts,
                responsive: true,
                // intersect:true agar klik tepat pada segment yang dimaksud
                interaction: { mode: 'nearest', intersect: true, axis: 'xy' },
                onClick(e, els, chart) {
                    if (!els.length) return;
                    // Ambil label dari chart langsung agar tidak salah urutan
                    const dsLabel = chart.data.datasets[els[0].datasetIndex]?.label;
                    if (dsLabel && dsLabel !== 'Normal') drillDown({ keterangan_residu: dsLabel });
                },
                onHover(e, els, chart) {
                    const dsLabel = els.length ? chart.data.datasets[els[0].datasetIndex]?.label : null;
                    e.native.target.style.cursor = (dsLabel && dsLabel !== 'Normal') ? 'pointer' : 'default';
                },
                plugins: {
                    legend: { display: true, position: 'bottom', labels: { padding: 16, font: { size: 12 } } },
                },
                scales: {
                    x: {
                        stacked: true,
                        grid:    { display: false },
                        border:  { display: false },
                        title:   { display: true, text: 'Rentang Nilai', color: '#94a3b8', font: { size: 11 } },
                    },
                    y: {
                        stacked: true,
                        grid:    { color: '#f1f5f9' },
                        border:  { display: false },
                        title:   { display: true, text: 'Jumlah Soal', color: '#94a3b8', font: { size: 11 } },
                    },
                },
            },
        });
    }

    /* ─────────────────────────────────────────────────────────────────
       GRAFIK 7 — SEM vs Nilai (Line Chart)
       Rata-rata SEM per rentang nilai. Tidak ada drill-down.
       SEM rendah = pengukuran presisi; SEM tinggi = butuh lebih banyak soal.
    ───────────────────────────────────────────────────────────────── */
    if (chartData.chart7 && document.getElementById('chartSEM')) {
        const c7 = chartData.chart7;

        new Chart('chartSEM', {
            type: 'line',
            data: {
                labels:   c7.labels,
                datasets: [{
                    data:               c7.data,
                    borderColor:        COLORS.teal,
                    backgroundColor:    'rgba(20,184,166,.1)',
                    tension:            0.3,
                    fill:               true,
                    pointBackgroundColor: COLORS.teal,
                    pointRadius:        5,
                    borderWidth:        2.5,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend:  { display: false },
                    tooltip: { callbacks: { label: ctx => ` Rata-rata SEM: ${ctx.raw}` } },
                },
                scales: {
                    x: {
                        grid:   { display: false },
                        border: { display: false },
                        title:  { display: true, text: 'Rentang Nilai', color: '#94a3b8', font: { size: 11 } },
                    },
                    y: {
                        grid:   { color: '#f1f5f9' },
                        border: { display: false },
                        title:  { display: true, text: 'Rata-rata SEM', color: '#94a3b8', font: { size: 11 } },
                    },
                },
            },
        });
    }

} // end isCbt

/* ═══════════════════════════════════════════════════════════════════════
   FILTER UX HELPERS
═══════════════════════════════════════════════════════════════════════ */

/** Toggle buka/tutup filter panel */
function toggleFilter() {
    const body = document.getElementById('filterBody');
    const icon = document.getElementById('filterChevron');
    const open = body.style.display !== 'none';
    body.style.display  = open ? 'none' : 'block';
    icon.style.transform = open ? '' : 'rotate(180deg)';
}

/** Cascade: filter kelas berdasarkan sekolah yang dipilih (admin only) */
function cascadeKelas() {
    const sekolahId = document.getElementById('fSekolah')?.value;
    document.querySelectorAll('#fKelas option').forEach(o => {
        o.style.display = (!sekolahId || o.value === '' || o.dataset.sekolah === sekolahId) ? '' : 'none';
    });
    document.getElementById('fKelas').value = '';
}

/** Cascade: filter indikator berdasarkan variabel yang dipilih */
function cascadeIndikator() {
    const variabelId = document.getElementById('fVariabel')?.value;
    document.querySelectorAll('#fIndikator option').forEach(o => {
        o.style.display = (!variabelId || o.value === '' || o.dataset.variabel === variabelId) ? '' : 'none';
    });
    document.getElementById('fIndikator').value = '';
}

/** Export tabel rekap siswa ke file CSV */
function exportCSV() {
    const tbl = document.getElementById('tblSiswa');
    if (!tbl) return;

    const csv = Array.from(tbl.querySelectorAll('tr'))
        .map(r => Array.from(r.querySelectorAll('th,td'))
            .map(c => '"' + c.innerText.replace(/"/g, '""').trim() + '"')
            .join(','))
        .join('\n');

    const blob = new Blob(['﻿' + csv], { type: 'text/csv;charset=utf-8' });
    const a    = document.createElement('a');
    a.href     = URL.createObjectURL(blob);
    a.download = 'analisis_ujian.csv';
    a.click();
}
</script>
