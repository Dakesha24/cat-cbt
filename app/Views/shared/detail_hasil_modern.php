<?php
$role = $detailRole ?? 'admin';
$attemptQuery = !empty($hasil['attempt_id']) ? '?attempt_id=' . urlencode((string) $hasil['attempt_id']) : '';

$defaultBackUrl = match ($role) {
    'guru' => base_url('guru/hasil-ujian/siswa/' . ($hasil['jadwal_id'] ?? '')),
    'siswa' => base_url('siswa/hasil/ujian/' . ($hasil['peserta_ujian_id'] ?? '')),
    default => base_url('admin/hasil-ujian/siswa/' . ($hasil['jadwal_id'] ?? '')),
};

$downloadExcelUrl = match ($role) {
    'guru' => base_url('guru/hasil-ujian/download-excel-html/' . ($hasil['peserta_ujian_id'] ?? '')) . $attemptQuery,
    'siswa' => null,
    default => base_url('admin/hasil-ujian/download-excel/' . ($hasil['peserta_ujian_id'] ?? '')) . $attemptQuery,
};
$downloadPdfUrl = match ($role) {
    'guru' => base_url('guru/hasil-ujian/download-pdf-html/' . ($hasil['peserta_ujian_id'] ?? '')) . $attemptQuery,
    'siswa' => base_url('siswa/hasil/unduh/' . ($hasil['attempt_id'] ?? '')),
    default => base_url('admin/hasil-ujian/download-pdf/' . ($hasil['peserta_ujian_id'] ?? '')) . $attemptQuery,
};
$deleteUrl = $role === 'admin' ? base_url('admin/hasil-ujian/hapus/' . ($hasil['peserta_ujian_id'] ?? '')) : null;
$backTarget = $backUrl ?? $defaultBackUrl;

$scoreValue = $isCatMode ? ($kemampuanKognitif['skor'] ?? 0) : ($finalScore ?? $skor ?? $hasil['nilai_akhir'] ?? 0);
$scoreValue = is_numeric($scoreValue) ? number_format((float) $scoreValue, 2) : $scoreValue;
$scoreLabel = $isCatMode ? 'Skor CAT' : 'Nilai CBT';
$wrongCount = max(0, (int) $totalSoal - (int) $jawabanBenar);
$accuracyRaw = (int) $totalSoal > 0 ? round(((int) $jawabanBenar / (int) $totalSoal) * 100) : 0;
$accuracy = min(100, max(0, $accuracyRaw));
$thetaValue = $thetaAkhir ?? $hasil['theta_akhir'] ?? null;
$seValue = $seAkhir ?? $hasil['sem_akhir'] ?? null;
$canShowDiscussion = !empty($hasil['tampilkan_pembahasan']);
$prefix = 'dh' . ucfirst($role);

$bgClass = $klasifikasiKognitif['bg_class'] ?? 'bg-info';
$scoreColor = match ($bgClass) {
    'bg-success' => '#15803d',
    'bg-warning' => '#b45309',
    'bg-danger' => '#b91c1c',
    default => '#0369a1',
};
$categoryPill = match ($bgClass) {
    'bg-success' => 'pill-ok',
    'bg-warning' => 'pill-warn',
    'bg-danger' => 'pill-err',
    default => 'pill-info',
};
$accuracyColor = $accuracy >= 75 ? '#15803d' : ($accuracy >= 50 ? '#b45309' : '#b91c1c');
?>

<style>
.dh-page { padding:20px 0 40px; color:#1f2937; }
.dh-header { display:flex; justify-content:space-between; align-items:flex-start; gap:16px; margin-bottom:18px; }
.dh-title { margin:0; font-size:1.25rem; font-weight:700; color:#111827; }
.dh-subtitle { margin-top:5px; color:#334155; font-size:.9rem; font-weight:500; overflow-wrap:anywhere; }
.dh-meta { margin-top:3px; color:#475569; font-size:.82rem; overflow-wrap:anywhere; }
.dh-actions { display:flex; gap:8px; align-items:center; justify-content:flex-end; flex-wrap:wrap; }
.dh-card { background:#fff; border:1px solid #d9e1ec; border-radius:8px; margin-bottom:16px; overflow:hidden; box-shadow:0 4px 14px rgba(15,23,42,.04); }
.dh-card-head { display:flex; justify-content:space-between; align-items:center; gap:12px; padding:14px 18px; border-bottom:1px solid #e6edf5; background:#fff; }
.dh-card-head h6 { margin:0; color:#111827; font-size:.98rem; font-weight:650; }
.dh-card-note { margin-top:3px; color:#475569; font-size:.8rem; }
.dh-card-body { padding:18px; }
.section-label { margin-bottom:10px; color:#334155; font-size:.74rem; font-weight:650; text-transform:uppercase; letter-spacing:.04em; }
.info-row { display:grid; grid-template-columns:150px minmax(0,1fr); gap:14px; padding:8px 0; border-bottom:1px solid #edf2f7; align-items:start; }
.info-row:last-child { border-bottom:0; }
.info-key { color:#475569; font-size:.84rem; font-weight:500; }
.info-val { color:#111827; font-size:.88rem; font-weight:500; overflow-wrap:anywhere; word-break:break-word; }
.info-desc { display:block; max-height:4.8rem; overflow:auto; padding-right:6px; line-height:1.5; }
.score-wrap { display:grid; grid-template-columns:220px minmax(0,1fr); gap:24px; align-items:center; }
.score-block { text-align:center; padding:4px 0; }
.score-num { font-size:2.75rem; font-weight:700; line-height:1; letter-spacing:0; }
.score-label { margin-top:7px; color:#475569; font-size:.78rem; font-weight:600; text-transform:uppercase; letter-spacing:.03em; }
.stat-grid { display:grid; grid-template-columns:repeat(3,minmax(110px,1fr)); gap:10px; }
.stat-box { padding:13px 10px; text-align:center; border-radius:8px; border:1px solid #e2e8f0; background:#f8fafc; }
.stat-total { background:#eef2ff; border-color:#c7d2fe; } .stat-total .stat-num { color:#3730a3; }
.stat-ok { background:#ecfdf5; border-color:#bbf7d0; } .stat-ok .stat-num { color:#15803d; }
.stat-err { background:#fef2f2; border-color:#fecaca; } .stat-err .stat-num { color:#b91c1c; }
.stat-num { font-size:1.35rem; font-weight:700; line-height:1; }
.stat-label { margin-top:5px; color:#475569; font-size:.72rem; font-weight:600; text-transform:uppercase; letter-spacing:.03em; }
.accuracy-head { display:flex; justify-content:space-between; gap:12px; margin:14px 0 6px; color:#334155; font-size:.82rem; font-weight:500; }
.accuracy-bar { height:7px; background:#e2e8f0; border-radius:999px; overflow:hidden; }
.accuracy-fill { height:100%; border-radius:999px; }
.irt-grid { display:flex; flex-wrap:wrap; gap:22px; margin-top:14px; padding-top:14px; border-top:1px solid #e8eef5; }
.metric-label { color:#475569; font-size:.75rem; font-weight:500; }
.metric-value { color:#111827; font-weight:600; }
.mono, .mono-sm { font-family:Consolas, "SFMono-Regular", monospace; }
.mono { font-size:.84rem; }
.mono-sm { font-size:.76rem; }
.pill { display:inline-flex; align-items:center; justify-content:center; gap:5px; padding:4px 9px; border-radius:999px; font-size:.72rem; font-weight:600; white-space:nowrap; }
.pill-ok { background:#dcfce7; color:#166534; }
.pill-err { background:#fee2e2; color:#991b1b; }
.pill-warn { background:#fef3c7; color:#92400e; }
.pill-info { background:#e0f2fe; color:#075985; }
.pill-gray { background:#f1f5f9; color:#334155; }
.pill-primary { background:#ede9fe; color:#4c1d95; }
.ans { display:inline-flex; align-items:center; justify-content:center; width:30px; height:30px; border-radius:7px; font-size:.9rem; font-weight:650; }
.ans-ok { background:#dcfce7; color:#166534; }
.ans-err { background:#fee2e2; color:#991b1b; }
.ans-key { background:#e2e8f0; color:#1f2937; }
.ans-big { width:58px; height:58px; border-radius:12px; font-size:1.75rem; }
.result-table { width:100%; min-width:1000px; border-collapse:collapse; }
.result-table thead th { background:#f8fafc; color:#64748b; padding:10px 14px; border-bottom:2px solid #e2e8f0; text-align:center; vertical-align:middle; font-size:.75rem; font-weight:600; text-transform:uppercase; letter-spacing:.04em; white-space:nowrap; }
.result-table thead th.text-start { text-align:left; }
.result-table tbody td { padding:11px 14px; border-bottom:1px solid #f1f5f9; background:#fff; color:#374151; text-align:center; vertical-align:middle; font-size:.875rem; }
.result-table tbody td.text-start { text-align:left; }
.result-table tbody tr.row-ok td { background:#fafffe; }
.result-table tbody tr.row-err td { background:#fffdfd; }
.result-table tbody tr:last-child td { border-bottom:none; }
.result-table tbody tr:hover td { background:#f5f7fa !important; }
.question-preview { max-width:260px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; color:#111827; font-weight:500; font-size:.875rem; }
.question-code { margin-top:3px; color:#64748b; font-size:.72rem; font-weight:400; }
.cell-muted { color:#64748b; font-weight:400; }
.btn-action { display:inline-flex; align-items:center; justify-content:center; gap:6px; min-width:76px; border:1px solid #c7d2fe; background:#eef2ff; color:#3730a3; border-radius:7px; padding:6px 10px; font-size:.78rem; font-weight:600; line-height:1.2; }
.btn-action:hover { background:#e0e7ff; color:#312e81; border-color:#a5b4fc; }
.btn-action-success { border-color:#bbf7d0; background:#ecfdf5; color:#166534; }
.btn-action-success:hover { background:#dcfce7; color:#14532d; border-color:#86efac; }
.modal-question { background:#f8fafc; border:1px solid #dbe3ee; border-radius:8px; padding:14px; margin-bottom:16px; color:#1f2937; font-size:.9rem; line-height:1.6; overflow-wrap:anywhere; }
.opt-row { transition:filter .12s ease, box-shadow .12s ease; }
.opt-row:hover { filter:brightness(.96); box-shadow:0 1px 4px rgba(0,0,0,.08); }
.metric-box { transition:filter .12s ease; }
.metric-box:hover { filter:brightness(.95); }
.z-zone { position:absolute; top:0; height:100%; cursor:default; }
.z-zone::after {
    content: attr(data-label);
    position: absolute;
    bottom: calc(100% + 7px);
    left: 50%;
    transform: translateX(-50%);
    background: #1e293b;
    color: #f8fafc;
    font-size: .6875rem;
    font-weight: 500;
    padding: 4px 9px;
    border-radius: 5px;
    white-space: nowrap;
    opacity: 0;
    pointer-events: none;
    transition: opacity .15s ease;
    box-shadow: 0 2px 6px rgba(0,0,0,.18);
}
.z-zone:hover::after { opacity: 1; }
.modal-question img, .modal-body img { max-width:100%; height:auto; }
.modal-info { display:flex; gap:20px; flex-wrap:wrap; padding:12px 14px; margin-bottom:16px; background:#f8fafc; border:1px solid #dbe3ee; border-radius:8px; }
@media (max-width:768px) {
    .dh-header, .dh-actions, .dh-card-head { flex-direction:column; align-items:flex-start; }
    .score-wrap, .stat-grid { grid-template-columns:1fr; }
    .info-row { grid-template-columns:1fr; gap:3px; }
    .dh-page { padding-top:12px; }
}
</style>

<div class="<?= $role === 'siswa' ? 'container' : 'container-fluid' ?> dh-page">
    <?php if ($role === 'siswa'): ?>
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb small mb-0">
                <li class="breadcrumb-item"><a href="<?= base_url('siswa/hasil') ?>">Riwayat Ujian</a></li>
                <li class="breadcrumb-item"><a href="<?= esc($defaultBackUrl) ?>">Daftar Percobaan</a></li>
                <li class="breadcrumb-item active">Detail</li>
            </ol>
        </nav>
    <?php endif; ?>

    <div class="dh-header">
        <div>
            <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                <h4 class="dh-title">Detail Hasil Ujian</h4>
                <?php if (!empty($hasil['nomor_attempt'])): ?>
                    <span class="pill pill-primary">Percobaan <?= (int) $hasil['nomor_attempt'] ?></span>
                <?php endif; ?>
                <span class="pill <?= $isCatMode ? 'pill-info' : 'pill-gray' ?>"><?= $isCatMode ? 'CAT' : 'CBT' ?></span>
            </div>
            <div class="dh-subtitle"><?= esc($hasil['nama_ujian'] ?? '-') ?> - <?= esc($hasil['nama_jenis'] ?? '-') ?></div>
            <div class="dh-meta">Kode ujian: <code><?= esc($hasil['kode_ujian'] ?? $hasil['kode_akses'] ?? '-') ?></code></div>
        </div>
        <div class="dh-actions">
            <?php if ($downloadExcelUrl): ?>
                <a href="<?= esc($downloadExcelUrl) ?>" class="btn btn-sm btn-success" target="_blank"><i class="bi bi-file-earmark-excel me-1"></i>Excel</a>
            <?php endif; ?>
            <?php if ($downloadPdfUrl): ?>
                <a href="<?= esc($downloadPdfUrl) ?>" class="btn btn-sm btn-primary" target="_blank"><i class="bi bi-file-earmark-pdf me-1"></i>PDF</a>
            <?php endif; ?>
            <a href="<?= esc($backTarget) ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
            <?php if ($deleteUrl): ?>
                <a href="<?= esc($deleteUrl) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus hasil ujian siswa ini? Siswa akan direset ke status belum mulai.')"><i class="bi bi-trash me-1"></i>Hapus</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="dh-card">
        <div class="dh-card-head"><h6><?= $role === 'siswa' ? 'Informasi Ujian dan Waktu Pengerjaan' : 'Identitas Peserta dan Waktu Pengerjaan' ?></h6></div>
        <div class="dh-card-body">
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="section-label"><?= $role === 'siswa' ? 'Informasi Ujian' : 'Identitas Peserta' ?></div>
                    <?php if ($role !== 'siswa'): ?>
                        <div class="info-row"><span class="info-key">Nama Siswa</span><span class="info-val"><?= esc($hasil['nama_lengkap'] ?? '-') ?></span></div>
                        <div class="info-row"><span class="info-key">No. Peserta</span><span class="info-val"><?= esc($hasil['nomor_peserta'] ?? '-') ?></span></div>
                        <div class="info-row"><span class="info-key">Kelas</span><span class="info-val"><?= esc($hasil['nama_kelas'] ?? '-') ?><?= !empty($hasil['nama_sekolah']) ? ', ' . esc($hasil['nama_sekolah']) : '' ?></span></div>
                    <?php else: ?>
                        <div class="info-row"><span class="info-key">Nama Ujian</span><span class="info-val"><?= esc($hasil['nama_ujian'] ?? '-') ?></span></div>
                        <div class="info-row"><span class="info-key">Jenis Ujian</span><span class="info-val"><?= esc($hasil['nama_jenis'] ?? '-') ?></span></div>
                        <div class="info-row"><span class="info-key">Percobaan</span><span class="info-val">Percobaan <?= (int) ($hasil['nomor_attempt'] ?? 1) ?></span></div>
                    <?php endif; ?>
                    <?php if (!empty($hasil['deskripsi'])): ?>
                        <div class="info-row"><span class="info-key">Deskripsi</span><span class="info-val info-desc"><?= nl2br(esc($hasil['deskripsi'])) ?></span></div>
                    <?php endif; ?>
                </div>
                <div class="col-lg-6">
                    <div class="section-label">Waktu Pengerjaan</div>
                    <div class="info-row"><span class="info-key">Waktu Mulai</span><span class="info-val"><?= esc($hasil['waktu_mulai_format'] ?? '-') ?></span></div>
                    <div class="info-row"><span class="info-key">Waktu Selesai</span><span class="info-val"><?= esc($hasil['waktu_selesai_format'] ?? '-') ?></span></div>
                    <div class="info-row"><span class="info-key">Total Durasi</span><span class="info-val"><?= esc($hasil['durasi_total_format'] ?? '-') ?></span></div>
                    <div class="info-row"><span class="info-key">Rata-rata / Soal</span><span class="info-val"><?= esc($rataRataWaktuFormat ?? '-') ?></span></div>
                </div>
            </div>
        </div>
    </div>

    <div class="dh-card">
        <div class="dh-card-head"><h6>Nilai dan Statistik</h6></div>
        <div class="dh-card-body">
            <div class="score-wrap">
                <div class="score-block">
                    <div class="score-num" style="color:<?= $scoreColor ?>"><?= esc((string) $scoreValue) ?></div>
                    <div class="score-label"><?= esc($scoreLabel) ?></div>
                    <span class="pill <?= $categoryPill ?> mt-2"><?= esc($klasifikasiKognitif['kategori'] ?? 'Tidak diklasifikasi') ?></span>
                </div>
                <div>
                    <div class="stat-grid">
                        <div class="stat-box stat-total"><div class="stat-num"><?= (int) $totalSoal ?></div><div class="stat-label">Total Soal</div></div>
                        <div class="stat-box stat-ok"><div class="stat-num"><?= (int) $jawabanBenar ?></div><div class="stat-label">Benar</div></div>
                        <div class="stat-box stat-err"><div class="stat-num"><?= $wrongCount ?></div><div class="stat-label">Salah</div></div>
                    </div>
                    <div class="accuracy-head"><span>Akurasi Jawaban</span><span><?= $accuracy ?>%</span></div>
                    <div class="accuracy-bar"><div class="accuracy-fill" style="width:<?= $accuracy ?>%;background:<?= $accuracyColor ?>"></div></div>
                    <div class="irt-grid">
                        <?php if ($isCatMode): ?>
                            <div><div class="metric-label">Theta Akhir</div><div class="mono metric-value"><?= $thetaValue !== null ? number_format((float) $thetaValue, 4) : '-' ?></div></div>
                            <div><div class="metric-label">Standard Error</div><div class="mono metric-value"><?= $seValue !== null ? number_format((float) $seValue, 4) : '-' ?></div></div>
                        <?php else: ?>
                            <div><div class="metric-label">Theta EAP</div><div class="mono metric-value"><?= $thetaValue !== null ? number_format((float) $thetaValue, 4) : '-' ?></div></div>
                            <div><div class="metric-label">SEM</div><div class="mono metric-value"><?= $seValue !== null ? number_format((float) $seValue, 4) : '-' ?></div></div>
                            <div><div class="metric-label">Rumus Nilai</div><div class="metric-value" style="font-size:.82rem">50 + (10 x Theta EAP)</div></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="dh-card">
        <div class="dh-card-head">
            <div>
                <h6>Detail Jawaban Per Soal</h6>
                <div class="dh-card-note"><?= (int) $totalSoal ?> soal - <?= (int) $jawabanBenar ?> benar - <?= $wrongCount ?> salah</div>
            </div>
            <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#<?= $prefix ?>Legend">
                <i class="bi bi-info-circle me-1"></i>Keterangan Kolom
            </button>
        </div>
        <div class="collapse" id="<?= $prefix ?>Legend">
            <div class="px-4 py-3 border-bottom" style="background:#f8fafc;color:#334155;font-size:.82rem">
                <?php if ($isCatMode): ?>
                    <span class="me-4"><code>Pi</code> probabilitas benar</span>
                    <span class="me-4"><code>Qi</code> 1 - Pi</span>
                    <span class="me-4"><code>Ii</code> informasi butir</span>
                    <span class="me-4"><code>SE</code> standard error</span>
                    <span class="me-4"><code>Delta SE</code> perubahan SE</span>
                    <span><code>Theta</code> estimasi kemampuan</span>
                <?php else: ?>
                    <span class="me-4"><code>b</code> &lt;-1 mudah, -1..1 sedang, &gt;=1 sulit</span>
                    <span class="me-4"><code>P(Theta)</code> probabilitas benar pada Theta EAP</span>
                    <span class="me-4"><code>z</code> standard residual</span>
                    <span><code>Keterangan</code> Lucky Guess z&gt;2, Ceroboh z&lt;-2</span>
                <?php endif; ?>
            </div>
        </div>
        <div class="table-responsive">
            <table class="result-table">
                <thead>
                    <tr>
                        <th class="text-start" style="width:54px">No</th>
                        <th class="text-start">Soal</th>
                        <th style="width:92px">Kesulitan</th>
                        <th style="width:78px">Jawaban</th>
                        <th style="width:78px">Kunci</th>
                        <th style="width:96px">Status</th>
                        <?php if ($isCatMode): ?>
                            <th style="width:58px">Pi</th>
                            <th style="width:58px">Qi</th>
                            <th style="width:58px">Ii</th>
                            <th style="width:58px">SE</th>
                            <th style="width:76px">Delta SE</th>
                            <th style="width:76px">Theta</th>
                        <?php else: ?>
                            <th style="width:118px">Keterangan</th>
                            <th style="width:82px">P(Theta)</th>
                            <th style="width:70px">z</th>
                        <?php endif; ?>
                        <th style="width:112px">Durasi</th>
                        <th style="width:96px">Detail</th>
                        <?php if ($canShowDiscussion): ?><th style="width:108px">Bahas</th><?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($detailJawaban)): ?>
                    <tr><td colspan="<?= ($isCatMode ? 14 : 11) + ($canShowDiscussion ? 1 : 0) ?>" class="py-4 cell-muted">Belum ada detail jawaban.</td></tr>
                <?php endif; ?>
                <?php foreach ($detailJawaban as $i => $jawaban): ?>
                    <?php
                    $isCorrect = (int) ($jawaban['is_correct'] ?? 0) === 1;
                    $b = (float) ($jawaban['tingkat_kesulitan'] ?? 0);
                    $difficulty = $b >= 1 ? 'Sulit' : ($b >= -1 ? 'Sedang' : 'Mudah');
                    $difficultyPill = $b >= 1 ? 'pill-err' : ($b >= -1 ? 'pill-warn' : 'pill-ok');
                    $modalId = $prefix . 'Detail' . ($jawaban['nomor_soal'] ?? $i);
                    $discussionId = $prefix . 'Discussion' . $i;
                    $questionText = trim(strip_tags((string) ($jawaban['pertanyaan'] ?? '')));
                    $residual = $jawaban['keterangan_residu'] ?? null;
                    $residualPill = match ($residual) {
                        'Lucky Guess' => 'pill-warn',
                        'Ceroboh' => 'pill-err',
                        null => 'pill-gray',
                        default => 'pill-ok',
                    };
                    ?>
                    <?php
                        $diffColor = $b >= 1 ? '#dc2626' : ($b >= -1 ? '#d97706' : '#16a34a');
                        $zv = isset($jawaban['z_score']) ? (float)$jawaban['z_score'] : null;
                        $rvColor = match($residual) { 'Lucky Guess' => '#b45309', 'Ceroboh' => '#b91c1c', default => '#15803d' };
                    ?>
                    <tr class="<?= $isCorrect ? 'row-ok' : 'row-err' ?>">
                        <td class="text-start" style="color:#94a3b8;font-size:.8125rem;font-weight:500"><?= (int)($jawaban['nomor_soal'] ?? ($i + 1)) ?></td>
                        <td class="text-start">
                            <div class="question-preview" title="<?= esc($questionText) ?>"><?= esc($questionText ?: '-') ?></div>
                            <div class="question-code"><?= esc($jawaban['kode_soal'] ?? ('ID: '.($jawaban['soal_id'] ?? '-'))) ?></div>
                        </td>
                        <td>
                            <span style="font-size:.8125rem;font-weight:600;color:<?= $diffColor ?>"><?= $difficulty ?></span>
                            <div class="mono-sm" style="color:#94a3b8;margin-top:2px"><?= number_format($b, 2) ?></div>
                        </td>
                        <td>
                            <?php $jwbSiswa = trim($jawaban['jawaban_siswa'] ?? ''); ?>
                            <?php if ($jwbSiswa !== ''): ?>
                                <span class="ans <?= $isCorrect ? 'ans-ok' : 'ans-err' ?>"><?= esc($jwbSiswa) ?></span>
                            <?php else: ?>
                                <span style="color:#d1d5db;font-size:.8rem">—</span>
                            <?php endif; ?>
                        </td>
                        <td><span class="ans ans-key"><?= esc(trim($jawaban['jawaban_benar'] ?? '') ?: '—') ?></span></td>
                        <td>
                            <?php if ($isCorrect): ?>
                                <span style="display:inline-flex;align-items:center;gap:5px;font-size:.8125rem;font-weight:600;color:#15803d">
                                    <span style="width:7px;height:7px;border-radius:50%;background:#22c55e;flex-shrink:0"></span>Benar
                                </span>
                            <?php else: ?>
                                <span style="display:inline-flex;align-items:center;gap:5px;font-size:.8125rem;font-weight:600;color:#b91c1c">
                                    <span style="width:7px;height:7px;border-radius:50%;background:#ef4444;flex-shrink:0"></span>Salah
                                </span>
                            <?php endif; ?>
                        </td>
                        <?php if ($isCatMode): ?>
                            <td class="mono-sm" style="color:#374151"><?= isset($jawaban['pi_saat_ini'])       ? number_format((float)$jawaban['pi_saat_ini'], 3)           : '<span style="color:#cbd5e1">—</span>' ?></td>
                            <td class="mono-sm" style="color:#374151"><?= isset($jawaban['qi_saat_ini'])       ? number_format((float)$jawaban['qi_saat_ini'], 3)           : '<span style="color:#cbd5e1">—</span>' ?></td>
                            <td class="mono-sm" style="color:#374151"><?= isset($jawaban['ii_saat_ini'])       ? number_format((float)$jawaban['ii_saat_ini'], 3)           : '<span style="color:#cbd5e1">—</span>' ?></td>
                            <td class="mono-sm" style="color:#374151"><?= isset($jawaban['se_saat_ini'])       ? number_format((float)$jawaban['se_saat_ini'], 3)           : '<span style="color:#cbd5e1">—</span>' ?></td>
                            <td class="mono-sm" style="color:#374151"><?= isset($jawaban['delta_se_saat_ini']) ? number_format(abs((float)$jawaban['delta_se_saat_ini']), 3) : '<span style="color:#cbd5e1">—</span>' ?></td>
                            <td class="mono-sm" style="color:#1e293b;font-weight:600"><?= isset($jawaban['theta_saat_ini']) ? number_format((float)$jawaban['theta_saat_ini'], 3) : '<span style="color:#cbd5e1">—</span>' ?></td>
                        <?php else: ?>
                            <td>
                                <span style="font-size:.8125rem;font-weight:600;color:<?= $rvColor ?>">
                                    <?= esc($residual ?? '—') ?>
                                </span>
                            </td>
                            <td class="mono-sm" style="color:#374151"><?= isset($jawaban['p_residu']) ? number_format((float)$jawaban['p_residu'], 3) : '<span style="color:#cbd5e1">—</span>' ?></td>
                            <td class="mono-sm" style="color:<?= $zv !== null && abs($zv) > 2 ? ($zv > 0 ? '#b45309' : '#b91c1c') : '#374151' ?>;font-weight:<?= $zv !== null && abs($zv) > 2 ? '600' : '400' ?>">
                                <?= $zv !== null ? number_format($zv, 3) : '<span style="color:#cbd5e1">—</span>' ?>
                            </td>
                        <?php endif; ?>
                        <td>
                            <div style="font-size:.875rem;color:#374151"><?= esc($jawaban['durasi_pengerjaan_format'] ?? '-') ?></div>
                            <div class="question-code"><?= esc($jawaban['waktu_menjawab_format'] ?? '-') ?></div>
                        </td>
                        <td><button type="button" class="btn-action" data-bs-toggle="modal" data-bs-target="#<?= $modalId ?>"><i class="bi bi-search"></i><span>Detail</span></button></td>
                        <?php if ($canShowDiscussion): ?>
                            <td>
                                <?php if (!empty($jawaban['pembahasan'])): ?>
                                    <button type="button" class="btn-action btn-action-success" data-bs-toggle="modal" data-bs-target="#<?= $discussionId ?>"><i class="bi bi-book"></i><span>Bahas</span></button>
                                <?php else: ?>
                                    <span style="color:#cbd5e1">—</span>
                                <?php endif; ?>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php foreach ($detailJawaban as $i => $jawaban): ?>
        <?php
        $isCorrect = (int) ($jawaban['is_correct'] ?? 0) === 1;
        $b = (float) ($jawaban['tingkat_kesulitan'] ?? 0);
        $difficulty = $b >= 1 ? 'Sulit' : ($b >= -1 ? 'Sedang' : 'Mudah');
        $difficultyPill = $b >= 1 ? 'pill-err' : ($b >= -1 ? 'pill-warn' : 'pill-ok');
        $modalId = $prefix . 'Detail' . ($jawaban['nomor_soal'] ?? $i);
        $discussionId = $prefix . 'Discussion' . $i;
        $residual = $jawaban['keterangan_residu'] ?? null;
        $residualPill = match ($residual) {
            'Lucky Guess' => 'pill-warn',
            'Ceroboh' => 'pill-err',
            null => 'pill-gray',
            default => 'pill-ok',
        };
        ?>
        <div class="modal fade" id="<?= $modalId ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content" style="border-radius:12px;border:0">
                    <div class="modal-header">
                        <div>
                            <div style="font-weight:600">Detail Soal #<?= (int) ($jawaban['nomor_soal'] ?? ($i + 1)) ?></div>
                            <div class="question-code"><?= esc($jawaban['kode_soal'] ?? ('Soal ID: ' . ($jawaban['soal_id'] ?? '-'))) ?></div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="modal-question"><?= $jawaban['pertanyaan'] ?? '-' ?></div>

                        <?php
                            $pilihanMap = [
                                'A' => $jawaban['pilihan_a'] ?? null,
                                'B' => $jawaban['pilihan_b'] ?? null,
                                'C' => $jawaban['pilihan_c'] ?? null,
                                'D' => $jawaban['pilihan_d'] ?? null,
                                'E' => $jawaban['pilihan_e'] ?? null,
                            ];
                            $pilihanMap = array_filter($pilihanMap, fn($v) => $v !== null && trim(strip_tags($v)) !== '');
                            $siswaPilih  = strtoupper(trim($jawaban['jawaban_siswa'] ?? ''));
                            $kunciJawab  = strtoupper(trim($jawaban['jawaban_benar'] ?? ''));
                        ?>
                        <?php if ($siswaPilih === '' && $kunciJawab === ''): ?>
                            <div class="alert alert-warning py-2 mb-3" style="font-size:.8125rem">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                Data jawaban tidak tersedia — rekaman ujian ini menggunakan format lama yang tidak menyimpan detail jawaban.
                            </div>
                        <?php endif; ?>
                        <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:16px">
                            <?php foreach ($pilihanMap as $huruf => $teks): ?>
                                <?php
                                    $isChosen  = ($huruf === $siswaPilih);
                                    $isCorrect2 = ($huruf === $kunciJawab);

                                    if ($isChosen && $isCorrect2) {
                                        // Dipilih & benar
                                        $optBg     = '#f0fdf4';
                                        $optBorder = '1.5px solid #16a34a';
                                        $badgeBg   = '#16a34a'; $badgeColor = '#fff';
                                        $icon      = '✓';
                                    } elseif ($isChosen && !$isCorrect2) {
                                        // Dipilih & salah
                                        $optBg     = '#fef2f2';
                                        $optBorder = '1.5px solid #dc2626';
                                        $badgeBg   = '#dc2626'; $badgeColor = '#fff';
                                        $icon      = '✗';
                                    } elseif (!$isChosen && $isCorrect2) {
                                        // Tidak dipilih tapi ini kunci benar
                                        $optBg     = '#f0fdf4';
                                        $optBorder = '1.5px dashed #16a34a';
                                        $badgeBg   = '#dcfce7'; $badgeColor = '#15803d';
                                        $icon      = '✓';
                                    } else {
                                        // Opsi biasa
                                        $optBg     = '#f8fafc';
                                        $optBorder = '1.5px solid #e4e8ef';
                                        $badgeBg   = '#e4e8ef'; $badgeColor = '#374151';
                                        $icon      = '';
                                    }
                                ?>
                                <div class="opt-row" style="display:flex;align-items:flex-start;gap:10px;padding:10px 12px;border-radius:8px;background:<?= $optBg ?>;border:<?= $optBorder ?>">
                                    <span style="flex-shrink:0;display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:6px;font-size:.8125rem;font-weight:700;background:<?= $badgeBg ?>;color:<?= $badgeColor ?>">
                                        <?= $huruf ?>
                                    </span>
                                    <div style="flex:1;font-size:.875rem;line-height:1.5;color:#1f2937;padding-top:2px"><?= $teks ?></div>
                                    <?php if ($icon): ?>
                                        <span style="flex-shrink:0;font-size:.875rem;font-weight:700;color:<?= $isCorrect2 ? '#16a34a' : '#dc2626' ?>;padding-top:4px"><?= $icon ?></span>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="modal-info">
                            <div><div class="metric-label">Kesulitan (b)</div><div class="mono metric-value"><?= number_format($b, 4) ?></div></div>
                            <div><div class="metric-label">Kategori</div><span class="pill <?= $difficultyPill ?>"><?= $difficulty ?></span></div>
                            <div><div class="metric-label">Waktu Jawab</div><div class="metric-value"><?= esc($jawaban['waktu_menjawab_format'] ?? '-') ?></div></div>
                            <div><div class="metric-label">Durasi</div><div class="metric-value"><?= esc($jawaban['durasi_pengerjaan_format'] ?? '-') ?></div></div>
                        </div>
                        <?php if ($isCatMode): ?>
                            <div class="section-label">Parameter IRT per Soal</div>
                            <table class="table table-sm table-bordered mb-0">
                                <tbody>
                                    <tr><td>Pi</td><td class="mono"><?= isset($jawaban['pi_saat_ini']) ? number_format((float) $jawaban['pi_saat_ini'], 4) : '-' ?></td><td>Probabilitas benar pada Theta saat ini</td></tr>
                                    <tr><td>Qi</td><td class="mono"><?= isset($jawaban['qi_saat_ini']) ? number_format((float) $jawaban['qi_saat_ini'], 4) : '-' ?></td><td>1 - Pi</td></tr>
                                    <tr><td>Ii</td><td class="mono"><?= isset($jawaban['ii_saat_ini']) ? number_format((float) $jawaban['ii_saat_ini'], 4) : '-' ?></td><td>Informasi butir</td></tr>
                                    <tr><td>SE</td><td class="mono"><?= isset($jawaban['se_saat_ini']) ? number_format((float) $jawaban['se_saat_ini'], 4) : '-' ?></td><td>Standard error estimasi</td></tr>
                                    <tr><td>Delta SE</td><td class="mono"><?= isset($jawaban['delta_se_saat_ini']) ? number_format(abs((float) $jawaban['delta_se_saat_ini']), 4) : '-' ?></td><td>Perubahan SE</td></tr>
                                    <tr><td>Theta</td><td class="mono"><?= isset($jawaban['theta_saat_ini']) ? number_format((float) $jawaban['theta_saat_ini'], 4) : '-' ?></td><td>Estimasi setelah soal ini</td></tr>
                                </tbody>
                            </table>
                        <?php elseif ($residual !== null): ?>
                            <?php
                                $pVal = isset($jawaban['p_residu']) ? (float)$jawaban['p_residu'] : null;
                                $zVal = isset($jawaban['z_score']) ? (float)$jawaban['z_score'] : null;

                                // Tentukan warna & pesan sesuai keterangan
                                if ($residual === 'Lucky Guess') {
                                    $resBoxBg  = '#fef3c7'; $resBoxBd = '#f59e0b';
                                    $resTitle  = 'Lucky Guess';
                                    $resTitleColor = '#92400e';
                                    $resIcon   = '⚡';
                                    $resDesc   = 'Soal ini tergolong <strong>' . $difficulty . '</strong> (peluang benar rendah, P(θ) = ' . ($pVal !== null ? number_format($pVal, 3) : '?') . '), namun dijawab <strong>BENAR</strong>. Nilai z = <strong>' . ($zVal !== null ? number_format($zVal, 2) : '?') . '</strong> melebihi +2, mengindikasikan jawaban benar yang tidak sesuai ekspektasi kemampuan.';
                                    $resCriteria = 'Terpicu jika: soal <strong>Sulit</strong> (b ≥ 1) <em>dan</em> z &gt; 2';
                                } elseif ($residual === 'Ceroboh') {
                                    $resBoxBg  = '#fee2e2'; $resBoxBd = '#f87171';
                                    $resTitle  = 'Ceroboh';
                                    $resTitleColor = '#991b1b';
                                    $resIcon   = '⚠';
                                    $resDesc   = 'Soal ini tergolong <strong>' . $difficulty . '</strong> (peluang benar tinggi, P(θ) = ' . ($pVal !== null ? number_format($pVal, 3) : '?') . '), namun dijawab <strong>SALAH</strong>. Nilai z = <strong>' . ($zVal !== null ? number_format($zVal, 2) : '?') . '</strong> di bawah −2, mengindikasikan kesalahan yang tidak sesuai ekspektasi kemampuan.';
                                    $resCriteria = 'Terpicu jika: soal <strong>Mudah</strong> (b &lt; −1) <em>dan</em> z &lt; −2';
                                } else {
                                    $resBoxBg  = '#dcfce7'; $resBoxBd = '#4ade80';
                                    $resTitle  = 'Normal';
                                    $resTitleColor = '#166534';
                                    $resIcon   = '✓';
                                    $resDesc   = 'Jawaban sesuai ekspektasi kemampuan. Nilai z = <strong>' . ($zVal !== null ? number_format($zVal, 2) : '?') . '</strong> berada dalam rentang normal.';
                                    $resCriteria = 'Rentang normal: −2 ≤ z ≤ 2';
                                }
                            ?>
                            <div class="section-label">Analisis Residu IRT 3PL</div>

                            <!-- Parameter -->
                            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;margin-bottom:12px">
                                <div class="metric-box" style="padding:10px 12px;background:#f0f4ff;border:1px solid #c7d2fe;border-radius:8px">
                                    <div style="font-size:.6875rem;color:#6b7280;margin-bottom:3px">Kesulitan Soal (b)</div>
                                    <div class="mono" style="font-weight:700;font-size:.9375rem;color:#1a1d23"><?= number_format($b, 3) ?></div>
                                    <div style="margin-top:4px"><span class="pill <?= $difficultyPill ?>"><?= $difficulty ?></span></div>
                                    <div style="font-size:.6875rem;color:#6b7280;margin-top:4px">
                                        Mudah b&lt;−1 &bull; Sedang −1..1 &bull; Sulit b≥1
                                    </div>
                                </div>
                                <div class="metric-box" style="padding:10px 12px;background:#f0f4ff;border:1px solid #c7d2fe;border-radius:8px">
                                    <div style="font-size:.6875rem;color:#6b7280;margin-bottom:3px">P(θ) — Prob. Benar</div>
                                    <div class="mono" style="font-weight:700;font-size:.9375rem;color:#1a1d23"><?= $pVal !== null ? number_format($pVal, 4) : '—' ?></div>
                                    <div style="font-size:.6875rem;color:#6b7280;margin-top:6px">Peluang menjawab benar<br>berdasarkan kemampuan θ_EAP</div>
                                </div>
                                <div class="metric-box" style="padding:10px 12px;background:#f0f4ff;border:1px solid #c7d2fe;border-radius:8px">
                                    <div style="font-size:.6875rem;color:#6b7280;margin-bottom:3px">z — Std. Residual</div>
                                    <div class="mono" style="font-weight:700;font-size:.9375rem;color:<?= $zVal !== null && abs($zVal) > 2 ? ($zVal > 0 ? '#d97706' : '#dc2626') : '#1a1d23' ?>">
                                        <?= $zVal !== null ? number_format($zVal, 4) : '—' ?>
                                    </div>
                                    <div style="font-size:.6875rem;color:#6b7280;margin-top:6px">
                                        Normal: −2 ≤ z ≤ 2<br>
                                        Rumus: (u − P) / √(P·Q)
                                    </div>
                                </div>
                            </div>

                            <!-- Skala z visual -->
                            <?php if ($zVal !== null): ?>
                            <div style="margin-bottom:12px;padding:10px 12px;background:#f0f4ff;border:1px solid #c7d2fe;border-radius:8px">
                                <div style="font-size:.6875rem;color:#9ca3af;margin-bottom:6px">Posisi z pada skala</div>
                                <?php
                                    $zClamped = max(-4, min(4, $zVal));
                                    $zPct     = round(($zClamped + 4) / 8 * 100);
                                    // z=-2 → 25%, z=+2 → 75%
                                ?>
                                <div style="position:relative;height:10px;border-radius:5px;background:linear-gradient(to right,#fca5a5 0%,#fde68a 28%,#86efac 42%,#86efac 58%,#fde68a 72%,#fca5a5 100%);overflow:visible">
                                    <!-- Zona hover: Ceroboh (0–25%) -->
                                    <div class="z-zone" style="left:0;width:25%;border-radius:5px 0 0 5px"
                                         data-label="Ceroboh — z &lt; −2 (soal mudah, dijawab salah)"></div>
                                    <!-- Zona hover: Normal (25–75%) -->
                                    <div class="z-zone" style="left:25%;width:50%"
                                         data-label="Normal — −2 ≤ z ≤ 2 (sesuai ekspektasi kemampuan)"></div>
                                    <!-- Zona hover: Lucky Guess (75–100%) -->
                                    <div class="z-zone" style="left:75%;width:25%;border-radius:0 5px 5px 0"
                                         data-label="Lucky Guess — z &gt; +2 (soal sulit, dijawab benar)"></div>
                                    <!-- Marker posisi z -->
                                    <div style="position:absolute;top:-3px;left:<?= $zPct ?>%;transform:translateX(-50%);width:16px;height:16px;border-radius:50%;background:<?= abs($zVal) > 2 ? ($zVal > 0 ? '#d97706' : '#dc2626') : '#16a34a' ?>;border:2px solid #fff;box-shadow:0 1px 3px rgba(0,0,0,.25);z-index:1;pointer-events:none"></div>
                                    <!-- Garis batas ±2 -->
                                    <div style="position:absolute;top:0;left:25%;width:1px;height:10px;background:rgba(0,0,0,.3);pointer-events:none"></div>
                                    <div style="position:absolute;top:0;left:75%;width:1px;height:10px;background:rgba(0,0,0,.3);pointer-events:none"></div>
                                </div>
                                <div style="display:flex;justify-content:space-between;font-size:.625rem;color:#9ca3af;margin-top:6px">
                                    <span>−4</span>
                                    <span style="color:#dc2626;font-weight:600">−2</span>
                                    <span style="color:#15803d;font-weight:600">Normal</span>
                                    <span style="color:#d97706;font-weight:600">+2</span>
                                    <span>+4</span>
                                </div>
                            </div>
                            <?php endif; ?>

                            <!-- Hasil kesimpulan -->
                            <div style="padding:12px 14px;border-radius:8px;background:<?= $resBoxBg ?>;border:1.5px solid <?= $resBoxBd ?>">
                                <div style="font-weight:700;color:<?= $resTitleColor ?>;margin-bottom:6px;font-size:.9375rem">
                                    <?= $resIcon ?> <?= $resTitle ?>
                                </div>
                                <div style="font-size:.8125rem;color:#374151;margin-bottom:8px;line-height:1.6"><?= $resDesc ?></div>
                                <div style="font-size:.75rem;color:#6b7280;padding:6px 10px;background:rgba(0,0,0,.04);border-radius:6px">
                                    <strong>Kriteria:</strong> <?= $resCriteria ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button></div>
                </div>
            </div>
        </div>

        <?php if ($canShowDiscussion && !empty($jawaban['pembahasan'])): ?>
            <div class="modal fade" id="<?= $discussionId ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                    <div class="modal-content" style="border-radius:12px;border:0">
                        <div class="modal-header">
                            <div style="font-weight:600">Pembahasan Soal #<?= (int) ($jawaban['nomor_soal'] ?? ($i + 1)) ?></div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                        </div>
                        <div class="modal-body" style="font-size:.9rem;line-height:1.7;color:#1f2937;overflow-wrap:anywhere"><?= $jawaban['pembahasan'] ?></div>
                        <div class="modal-footer"><button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button></div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>

    <?php if ($isCatMode && !empty($detailJawaban)): ?>
        <div class="row g-3 mb-2">
            <div class="col-md-6"><div class="dh-card"><div class="dh-card-head"><h6>Grafik Theta</h6></div><div class="dh-card-body"><canvas id="<?= $prefix ?>ThetaChart" height="230"></canvas></div></div></div>
            <div class="col-md-6"><div class="dh-card"><div class="dh-card-head"><h6>Grafik Standard Error</h6></div><div class="dh-card-body"><canvas id="<?= $prefix ?>SeChart" height="230"></canvas></div></div></div>
        </div>
        <script src="<?= base_url('js/chart.umd.min.js') ?>"></script>
        <script>
            (function () {
                const labels = <?= json_encode(array_map(static fn($i) => 'Q' . ($i['nomor_soal'] ?? ''), $detailJawaban)) ?>;
                const theta = <?= json_encode(array_map(static fn($i) => (float) ($i['theta_saat_ini'] ?? 0), $detailJawaban)) ?>;
                const se = <?= json_encode(array_map(static fn($i) => (float) ($i['se_saat_ini'] ?? 0), $detailJawaban)) ?>;
                const baseOptions = {responsive:true,plugins:{legend:{display:false}},scales:{y:{grid:{color:'#e8eef5'},border:{display:false}},x:{grid:{display:false},border:{display:false}}}};
                new Chart('<?= $prefix ?>ThetaChart', {type:'line',data:{labels,datasets:[{data:theta,borderColor:'#4f46e5',backgroundColor:'rgba(79,70,229,.08)',tension:.3,fill:true,pointRadius:3,borderWidth:2}]},options:baseOptions});
                new Chart('<?= $prefix ?>SeChart', {type:'line',data:{labels,datasets:[{data:se,borderColor:'#0891b2',backgroundColor:'rgba(8,145,178,.08)',tension:.3,fill:true,pointRadius:3,borderWidth:2}]},options:baseOptions});
            })();
        </script>
    <?php endif; ?>
</div>
