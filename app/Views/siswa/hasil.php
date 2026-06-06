<?= $this->extend('templates/siswa/siswa_template') ?>
<?= $this->section('content') ?>

<style>
.hs-wrap { padding:72px 0 48px; background:#f4f6f9; min-height:100vh; }
.hs-head { display:flex; flex-wrap:wrap; justify-content:space-between; align-items:flex-start; gap:1rem; margin-bottom:1.25rem; }
.hs-title { font-size:1.05rem; font-weight:700; color:#0f172a; margin:0 0 .2rem; }
.hs-meta { font-size:.8rem; color:#6b7280; }

.exam-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(280px,1fr)); gap:12px; }
.exam-card { background:#fff; border:1px solid #e2e8f0; border-radius:8px; overflow:hidden; display:flex; flex-direction:column; transition:box-shadow .15s; }
.exam-card:hover { box-shadow:0 4px 16px rgba(15,23,42,.08); }
.exam-card-body { padding:16px 18px; flex:1; }
.exam-name { font-size:.95rem; font-weight:700; color:#0f172a; margin-bottom:3px; }
.exam-sub { font-size:.78rem; color:#6b7280; margin-bottom:10px; }
.exam-desc { font-size:.78rem; color:#9ca3af; margin-bottom:10px; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }

.last-result { background:#f8fafc; border:1px solid #f1f5f9; border-radius:6px; padding:10px 12px; margin-bottom:10px; }
.last-result-lbl { font-size:.68rem; color:#9ca3af; text-transform:uppercase; letter-spacing:.04em; margin-bottom:4px; }
.last-result-val { font-size:1.1rem; font-weight:700; color:#0f172a; line-height:1; }
.last-result-meta { font-size:.75rem; color:#9ca3af; margin-top:4px; }

.exam-card-foot { padding:12px 18px; border-top:1px solid #f1f5f9; }
.hs-btn { display:inline-flex; align-items:center; justify-content:center; gap:.3rem; font-size:.82rem; font-weight:500; padding:.35rem .75rem; border-radius:6px; border:1px solid; text-decoration:none; background:transparent; line-height:1.4; white-space:nowrap; transition:background .12s; width:100%; }
.hs-btn-primary { border-color:#bfdbfe; color:#1d4ed8; } .hs-btn-primary:hover { background:#eff6ff; }

.type-cat { background:#dbeafe; color:#1e40af; font-size:.68rem; font-weight:700; padding:2px 7px; border-radius:4px; }
.type-cbt { background:#d1fae5; color:#065f46; font-size:.68rem; font-weight:700; padding:2px 7px; border-radius:4px; }

.empty-card { background:#fff; border:1px solid #e2e8f0; border-radius:8px; padding:3rem; text-align:center; color:#9ca3af; }
</style>

<div class="hs-wrap">
<div class="container px-3">

  <div class="hs-head">
    <div>
      <div class="hs-title">Riwayat Ujian</div>
      <div class="hs-meta">Ujian yang telah Anda ikuti</div>
    </div>
  </div>

  <?php if (empty($riwayatUjian)): ?>
    <div class="empty-card">
      <i class="bi bi-clipboard-x d-block mb-2" style="font-size:2.5rem;opacity:.4"></i>
      <strong>Belum ada riwayat ujian</strong><br>
      <span style="font-size:.85rem">Anda belum mengikuti ujian apapun.</span>
    </div>
  <?php else: ?>
    <div class="exam-grid">
      <?php foreach ($riwayatUjian as $ujian): ?>
        <?php
          $isCbt = ($ujian['tipe_ujian'] ?? 'CAT') === 'CBT';
          $att   = $ujian['attempt_terakhir'] ?? null;
        ?>
        <div class="exam-card">
          <div class="exam-card-body">
            <div class="d-flex justify-content-between align-items-start mb-2">
              <span class="<?= $isCbt ? 'type-cbt' : 'type-cat' ?>"><?= $isCbt ? 'CBT' : 'CAT' ?></span>
              <span style="font-size:.75rem;color:#9ca3af"><?= $ujian['jumlah_attempt'] ?> percobaan</span>
            </div>
            <div class="exam-name"><?= esc($ujian['nama_ujian']) ?></div>
            <div class="exam-sub"><?= esc($ujian['nama_jenis']) ?> &bull; <code style="font-size:.72rem"><?= esc($ujian['kode_ujian']) ?></code></div>
            <?php if (!empty($ujian['deskripsi'])): ?>
              <div class="exam-desc"><?= esc($ujian['deskripsi']) ?></div>
            <?php endif; ?>

            <?php if ($att): ?>
              <div class="last-result">
                <div class="last-result-lbl">Percobaan <?= esc($att['nomor_attempt'] ?? '-') ?> — Terakhir</div>
                <div class="last-result-val">
                  <?php if ($isCbt): ?>
                    <?= number_format((float)($att['nilai_akhir'] ?? 0), 2) ?>
                  <?php else: ?>
                    <?= number_format(50 + (16.67 * (float)($att['nilai_akhir'] ?? 0)), 2) ?>
                  <?php endif; ?>
                </div>
                <div class="last-result-meta">Selesai: <?= esc($att['waktu_selesai_format'] ?? '-') ?></div>
              </div>
            <?php endif; ?>
          </div>
          <div class="exam-card-foot">
            <a href="<?= base_url('siswa/hasil/ujian/' . $ujian['peserta_ujian_id']) ?>" class="hs-btn hs-btn-primary">
              <i class="bi bi-list-ul"></i>Lihat Percobaan
            </a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

</div>
</div>

<?= $this->endSection() ?>
