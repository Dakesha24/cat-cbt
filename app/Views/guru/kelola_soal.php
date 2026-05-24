<?= $this->extend('templates/guru/guru_template') ?>

<?= $this->section('content') ?>

<div class="container-fluid py-4">
  <!-- Header -->
  <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div>
      <nav aria-label="breadcrumb" class="mb-1">
        <ol class="breadcrumb small mb-0">
          <li class="breadcrumb-item"><a href="<?= base_url('guru/ujian') ?>">Kelola Ujian</a></li>
          <li class="breadcrumb-item active"><?= esc($ujian['nama_ujian']) ?></li>
        </ol>
      </nav>
      <div class="d-flex align-items-center gap-3">
        <h4 class="fw-bold text-dark mb-0"><?= esc($ujian['nama_ujian']) ?></h4>
        <span class="badge bg-<?= ($ujian['tipe_ujian'] ?? 'CAT') == 'CAT' ? 'primary' : 'success' ?> bg-opacity-10 text-<?= ($ujian['tipe_ujian'] ?? 'CAT') == 'CAT' ? 'primary' : 'success' ?> px-3 py-1">
          <?= ($ujian['tipe_ujian'] ?? 'CAT') == 'CAT' ? 'CAT Adaptif' : 'CBT Fixed' ?>
        </span>
      </div>
    </div>
    <a href="<?= base_url('guru/ujian') ?>" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
  </div>

  <!-- Alerts -->
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

  <style>
    .cbt-step-flow {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: .55rem;
      padding: .65rem 1rem;
      margin-bottom: .45rem;
      background: #fff;
      border: 1px solid #e9ecef;
      box-shadow: 0 .125rem .5rem rgba(15, 23, 42, .04);
    }
    .cbt-step-card,
    .cbt-step-card .card-header,
    .cbt-step-card .card-footer,
    .cbt-summary-strip,
    .cbt-info-box,
    .cbt-table-wrap {
      border-radius: 0 !important;
    }
    .cbt-step-card {
      border: 1px solid #e9ecef;
      box-shadow: 0 .25rem 1rem rgba(15, 23, 42, .05);
    }
    .cbt-step-card .card-header,
    .cbt-step-card .card-footer {
      background: #fff;
    }
    .cbt-step-card {
      margin-top: 0 !important;
    }
    .cbt-step-card .card-header {
      padding-top: .8rem !important;
      padding-bottom: .8rem !important;
    }
    .cbt-step-chip {
      min-width: 84px;
      text-align: center;
      letter-spacing: .04em;
    }
    .cbt-summary-strip {
      padding: .85rem 1rem;
      border: 1px solid #e9ecef;
      background: #f8f9fa;
    }
    .cbt-info-box {
      border: 1px solid #e9ecef;
      background: #f8f9fa;
    }
    .cbt-table-wrap {
      border: 1px solid #d7dee8;
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
    }
    .cbt-bank-table {
      font-size: .84rem;
    }
    .cbt-bank-table thead th {
      background: #fbfcfe !important;
      border-bottom: 1px solid #d7dee8 !important;
      color: #596579 !important;
      font-size: .7rem !important;
      font-weight: 600 !important;
      letter-spacing: .055em !important;
      padding: .62rem .75rem !important;
    }
    .cbt-bank-table tbody td {
      border-color: #edf1f5;
      padding: .68rem .75rem !important;
      vertical-align: middle;
    }
    .cbt-code-pill {
      background: #f7f9fc;
      border: 1px solid #dbe3ed;
      color: #344054;
      display: inline-flex;
      font-size: .74rem;
      font-weight: 500;
      letter-spacing: .02em;
      padding: .18rem .45rem;
    }
    .cbt-question-preview {
      color: #1f2937;
      font-size: .84rem;
      line-height: 1.35;
      max-width: 360px;
    }
    .cbt-answer-pill,
    .cbt-difficulty-pill,
    .cbt-meta-pill {
      align-items: center;
      display: inline-flex;
      font-size: .74rem;
      font-weight: 500;
      justify-content: center;
      min-width: 34px;
      padding: .18rem .48rem;
    }
    .cbt-answer-pill {
      background: #edfdf3;
      border: 1px solid #b7ebc8;
      color: #137547;
    }
    .cbt-difficulty-pill {
      background: #f8fafc;
      border: 1px solid #dbe3ed;
      color: #475467;
    }
    .cbt-meta-pill {
      background: #f8fafc;
      border: 1px solid #dbe3ed;
      color: #475467;
      font-size: .68rem;
      justify-content: flex-start;
      font-weight: 500;
    }
    .cbt-row-actions {
      display: inline-flex;
      gap: .35rem;
    }
    .cbt-row-actions .btn {
      align-items: center;
      border-radius: 0 !important;
      display: inline-flex;
      height: 30px;
      justify-content: center;
      padding: 0;
      width: 32px;
    }
    .cbt-count-chip {
      background: #eef5ff;
      border: 1px solid #cfe2ff;
      color: #0b5ed7;
      font-size: .72rem;
      font-weight: 600;
      padding: .2rem .55rem;
    }
    .cbt-step-card .card-header h5 {
      font-size: 1rem;
      line-height: 1.25;
    }
    .cbt-step-card .card-header .small,
    .cbt-step-card .card-footer .small {
      font-size: .78rem;
      line-height: 1.45;
    }
    .cbt-step-section {
      border: 1px solid #dfe5ec;
      background: #fff;
      padding: 1rem;
    }
    .cbt-step-section + .cbt-step-section {
      margin-top: 1rem;
    }
    .cbt-section-head {
      align-items: flex-start;
      display: flex;
      justify-content: space-between;
      gap: 1rem;
      margin-bottom: .9rem;
    }
    .cbt-section-title {
      color: #1f2937;
      font-size: .92rem;
      font-weight: 700;
      margin-bottom: .12rem;
    }
    .cbt-section-subtitle {
      color: #667085;
      font-size: .77rem;
      line-height: 1.4;
    }
    .cbt-step-footer {
      gap: 1rem;
      padding: .85rem 1.25rem;
    }
    .cat-manage-card,
    .cat-manage-card .card-header,
    .cat-manage-card .card-footer,
    .cat-summary-panel,
    .cat-summary-item,
    .cat-table-wrap,
    .cat-empty-state {
      border-radius: 0 !important;
    }
    .cat-manage-card {
      border: 1px solid #dbe3ed;
      box-shadow: 0 .25rem 1rem rgba(15, 23, 42, .05);
    }
    .cat-header-note {
      color: #667085;
      font-size: .78rem;
      line-height: 1.45;
      max-width: 720px;
    }
    .cat-action-group {
      display: flex;
      flex-wrap: wrap;
      gap: .55rem;
    }
    .cat-summary-panel {
      display: grid;
      gap: .9rem;
      grid-template-columns: repeat(4, minmax(0, 1fr));
      margin-bottom: 1rem;
    }
    .cat-summary-item {
      background: #fff;
      border: 1px solid #dbe3ed;
      padding: .85rem .95rem;
    }
    .cat-summary-label {
      color: #667085;
      font-size: .68rem;
      font-weight: 600;
      letter-spacing: .05em;
      margin-bottom: .28rem;
      text-transform: uppercase;
    }
    .cat-summary-value {
      color: #101828;
      font-size: 1rem;
      font-weight: 700;
      line-height: 1.2;
    }
    .cat-summary-help {
      color: #667085;
      font-size: .72rem;
      margin-top: .18rem;
    }
    .cat-table-wrap {
      border: 1px solid #dbe3ed;
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
    }
    .cat-bank-table {
      font-size: .84rem;
    }
    .cat-bank-table thead th {
      background: #fbfcfe !important;
      border-bottom: 1px solid #dbe3ed !important;
      color: #596579 !important;
      font-size: .69rem !important;
      font-weight: 600 !important;
      letter-spacing: .05em !important;
      padding: .65rem .72rem !important;
    }
    .cat-bank-table tbody td {
      border-color: #edf1f5;
      padding: .72rem !important;
      vertical-align: middle;
    }
    .cat-question-text {
      color: #1f2937;
      font-size: .84rem;
      line-height: 1.4;
      max-width: 360px;
    }
    .cat-code-pill,
    .cat-origin-pill,
    .cat-value-pill,
    .cat-meta-pill {
      align-items: center;
      display: inline-flex;
      font-size: .72rem;
      font-weight: 500;
      padding: .2rem .48rem;
    }
    .cat-code-pill {
      background: #f8fafc;
      border: 1px solid #dbe3ed;
      color: #344054;
    }
    .cat-origin-pill.is-bank {
      background: #eef6ff;
      border: 1px solid #cfe2ff;
      color: #0b5ed7;
    }
    .cat-origin-pill.is-custom {
      background: #f4f0ff;
      border: 1px solid #ddd6fe;
      color: #6941c6;
    }
    .cat-value-pill {
      background: #f8fafc;
      border: 1px solid #dbe3ed;
      color: #475467;
      justify-content: center;
      min-width: 42px;
    }
    .cat-meta-pill {
      background: #f8fafc;
      border: 1px solid #dbe3ed;
      color: #475467;
      font-size: .67rem;
    }
    .cat-empty-state {
      background: #fff;
      border: 1px dashed #d0d7e2;
      color: #667085;
      padding: 2.5rem 1rem;
      text-align: center;
    }
    .cat-empty-state .icon {
      color: #98a2b3;
      font-size: 2rem;
      margin-bottom: .55rem;
    }
    @media (max-width: 991.98px) {
      .cat-summary-panel {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }
    }
    @media (max-width: 575.98px) {
      .cat-summary-panel {
        grid-template-columns: 1fr;
      }
    }
    .cbt-step-footer .btn {
      border-radius: 0 !important;
      font-size: .84rem;
      font-weight: 700;
      min-width: 150px;
      padding: .52rem .95rem;
    }
    .cbt-empty-state {
      background: #fbfcfe;
      border: 1px solid #dfe5ec !important;
      padding: 2.25rem 1rem !important;
    }
    .cbt-empty-state p {
      font-size: .9rem;
    }
    .cbt-empty-state small {
      font-size: .78rem;
    }
    .cbt-empty-state .empty-icon {
      align-items: center;
      background: #f1f5f9;
      border: 1px solid #dfe5ec;
      color: #94a3b8;
      display: inline-flex;
      font-size: 1.45rem;
      height: 48px;
      justify-content: center;
      width: 48px;
    }
    .cbt-action-row {
      align-items: center;
      display: flex;
      flex-wrap: wrap;
      gap: .55rem;
      justify-content: flex-end;
    }
    .cbt-action-row .btn,
    .cbt-action-secondary,
    .cbt-draft-submit {
      border-radius: 0 !important;
      font-size: .8rem;
      font-weight: 700;
      line-height: 1.25;
      padding: .45rem .8rem;
    }
    .cbt-draft-submit {
      padding: .62rem 1rem;
    }
    .cbt-final-actions .btn {
      min-width: 132px;
      padding: .42rem .85rem;
      font-size: .82rem;
      font-weight: 600;
      line-height: 1.2;
      border-radius: 0 !important;
    }
    .cbt-final-actions .bi {
      font-size: .9rem;
    }
    .cbt-review-modal .modal-content {
      border-radius: 0;
      border: 1px solid #d7dee8;
    }
    .cbt-review-modal .modal-header {
      border-bottom: 1px solid #d7dee8;
      background: #fbfcfd;
    }
    .cbt-soal-modal .modal-dialog {
      max-width: 980px;
      margin: 1.75rem auto;
      height: calc(100% - 3.5rem);
    }
    .cbt-soal-modal .modal-content {
      border: 1px solid #d7dee8 !important;
      border-radius: 0 !important;
      display: flex;
      flex-direction: column;
      max-height: 100%;
      overflow: hidden;
    }
    .cbt-soal-modal .modal-content > form {
      flex: 1 1 auto;
      display: flex;
      flex-direction: column;
      min-height: 0;
      overflow: hidden;
    }
    .cbt-soal-modal .modal-scroll-area {
      flex: 1 1 auto;
      min-height: 0;
      overflow-y: auto;
      overscroll-behavior: contain;
    }
    .cbt-soal-modal .modal-header {
      background: linear-gradient(135deg, #eef5ff 0%, #f8fbff 58%, #ffffff 100%) !important;
      border-bottom: 1px solid #bfdbfe;
      color: #1f2937 !important;
      flex-shrink: 0;
      padding: .9rem 1.15rem !important;
    }
    .cbt-soal-modal .modal-title {
      font-size: .98rem;
      font-weight: 800 !important;
    }
    .cbt-soal-modal .modal-body {
      padding: 1rem 1.15rem !important;
    }
    .cbt-soal-modal .note-editor.note-frame {
      border: 1px solid #d7dee8;
    }
    .cbt-soal-modal .modal-footer {
      background: #fbfcfe !important;
      border-top: 1px solid #d7dee8 !important;
      flex-shrink: 0;
      padding: .8rem 1.15rem !important;
    }
    .cbt-soal-modal .form-label {
      color: #475467;
      font-size: .74rem !important;
      font-weight: 800 !important;
      letter-spacing: .015em;
      margin-bottom: .35rem;
    }
    .cbt-form-section {
      border: 1px solid #e1e7ef;
      padding: .9rem;
      background: #fff;
    }
    .cbt-form-section + .cbt-form-section {
      margin-top: .85rem;
    }
    .cbt-form-section-title {
      color: #344054;
      font-size: .8rem;
      font-weight: 800;
      margin-bottom: .75rem;
    }
    .cbt-review-list {
      display: grid;
      gap: .65rem;
    }
    .cbt-review-item {
      border: 1px solid #d7dee8;
      background: #fff;
      padding: .75rem .85rem;
    }
    .cbt-review-number {
      min-width: 28px;
      height: 24px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      border: 1px solid #d7dee8;
      background: #fff;
      color: #495057;
      font-weight: 700;
      font-size: .75rem;
    }
    .cbt-review-question {
      color: #212529;
      font-weight: 600;
      line-height: 1.4;
      font-size: .88rem;
    }
    .cbt-review-code {
      display: inline-flex;
      align-items: center;
      padding: .16rem .45rem;
      border: 1px solid #ced4da;
      background: #f8f9fa;
      color: #343a40;
      font-size: .72rem;
      font-weight: 700;
    }
    .cbt-review-head {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: .75rem;
      margin-bottom: .55rem;
    }
    .cbt-review-options {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: .35rem;
      margin-top: .65rem;
    }
    .cbt-review-option {
      display: grid;
      grid-template-columns: 24px 1fr;
      gap: .45rem;
      padding: .38rem .5rem;
      border: 1px solid #edf0f3;
      background: #fbfcfd;
      font-size: .8rem;
      line-height: 1.35;
    }
    .cbt-review-option.is-correct {
      border-color: #badbcc;
      background: #f6fffa;
    }
    .cbt-review-option-key {
      font-weight: 700;
      color: #495057;
    }
    .cbt-review-option.is-correct .cbt-review-option-key {
      color: #198754;
    }
    .cbt-review-explain {
      margin-top: .65rem;
      padding: .55rem .65rem;
      border: 1px solid #dbe7ff;
      background: #f8fbff;
      color: #495057;
      font-size: .8rem;
      line-height: 1.4;
    }
    .cbt-review-meta {
      display: flex;
      flex-wrap: wrap;
      gap: .35rem;
      margin-top: .55rem;
      color: #6c757d;
      font-size: .74rem;
    }
    .cbt-review-pill {
      display: inline-flex;
      align-items: center;
      padding: .15rem .42rem;
      border: 1px solid #dee2e6;
      background: #fbfcfd;
      color: #495057;
      font-weight: 600;
    }
    .cbt-final-stats {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: .75rem;
      border: 0;
      background: #fff;
    }
    .cbt-final-stat {
      padding: .85rem .95rem;
      border: 1px solid #cfd8e3;
      background: #fff;
    }
    .cbt-final-stat .label {
      color: #5f6b7a;
      font-size: .76rem;
      font-weight: 600;
      margin-bottom: .25rem;
    }
    .cbt-final-stat .value {
      color: #212529;
      font-size: .98rem;
      font-weight: 700;
      line-height: 1.2;
    }
    .cbt-status-chip {
      display: inline-flex;
      align-items: center;
      padding: .22rem .55rem;
      border: 1px solid #badbcc;
      background: #f6fffa;
      color: #146c43;
      font-size: .78rem;
      font-weight: 700;
    }
    .cbt-status-chip.locked {
      border-color: #d3d6da;
      background: #f8f9fa;
      color: #495057;
    }
    .cbt-final-review {
      border: 1px solid #cfd8e3;
      background: #fff;
    }
    .cbt-final-review thead th {
      background: #fff !important;
      color: #495057;
      font-size: .74rem;
      font-weight: 700;
      text-transform: uppercase;
      border-bottom: 1px solid #cfd8e3;
    }
    .cbt-final-review tbody td {
      border-color: #dee2e6;
      vertical-align: middle;
      font-size: .86rem;
    }
    .cbt-final-review tbody tr:last-child td {
      border-bottom: 0;
    }
    .cbt-soft-count {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-width: 38px;
      padding: .25rem .55rem;
      border: 1px solid #dbe4ff;
      color: #0d6efd;
      background: #f8fbff;
      font-weight: 700;
      font-size: .78rem;
    }
    @media (max-width: 768px) {
      .cbt-final-stats {
        grid-template-columns: 1fr;
      }
      .cbt-final-stat {
        border: 1px solid #cfd8e3;
      }
    }
  </style>

  <?php if (($ujian['tipe_ujian'] ?? 'CAT') == 'CBT'): ?>
    <!-- ==================== CBT MODE ==================== -->
    <?php
      $step1Done = !empty($assignedBanks);
      $step2Done = !empty($paketList) && empty($draftPaket['packages'] ?? []);
      $hasDraft = !empty($draftPaket['packages']);
    ?>
    <div class="cbt-step-flow flex-wrap">
      <div class="d-flex align-items-center">
        <span class="step-badge <?= ($currentStep ?? 1) === 1 ? 'step-active' : ($step1Done ? 'step-done' : '') ?>"><?= $step1Done ? '<i class="bi bi-check-lg"></i>' : '1' ?></span>
        <span class="ms-2 small fw-semibold <?= $step1Done ? 'text-success' : '' ?>">Pilih Bank & Soal</span>
      </div>
      <span class="text-muted mx-1">></span>
      <div class="d-flex align-items-center">
        <span class="step-badge <?= ($currentStep ?? 1) === 2 ? 'step-active' : ($step2Done ? 'step-done' : '') ?>"><?= $step2Done ? '<i class="bi bi-check-lg"></i>' : '2' ?></span>
        <span class="ms-2 small fw-semibold <?= $step2Done ? 'text-success' : '' ?>">Buat Draft Paket</span>
      </div>
      <span class="text-muted mx-1">></span>
      <div class="d-flex align-items-center">
        <span class="step-badge <?= ($currentStep ?? 1) === 3 ? 'step-active' : ($step2Done ? 'step-done' : '') ?>"><?= $step2Done ? '<i class="bi bi-check-lg"></i>' : '3' ?></span>
        <span class="ms-2 small fw-semibold <?= $step2Done ? 'text-success' : '' ?>">Paket Final</span>
      </div>
    </div>
    <div class="d-none card border-0 bg-light mb-4">
      <div class="card-body py-3 px-4">
        <?php if (($currentStep ?? 1) === 1): ?>
          <div class="fw-semibold text-dark mb-1">Langkah 1: Pilih bank soal lalu kelola isi bank.</div>
          <div class="small text-muted">Setelah bank dipilih, Anda bisa menambah, mengedit, dan menghapus soal pada bank tersebut. Jika isi soal sudah siap, lanjut ke langkah berikutnya.</div>
        <?php elseif (($currentStep ?? 1) === 2): ?>
          <div class="fw-semibold text-dark mb-1">Langkah 2: Buat draft paket dari bank soal yang sudah dipilih.</div>
          <div class="small text-muted">Buat draft paket, review hasilnya, lalu simpan jika susunannya sudah final.</div>
        <?php else: ?>
          <div class="fw-semibold text-success mb-1">Langkah 3: Paket final berhasil dibuat.</div>
          <div class="small text-muted">Paket final sudah tersimpan di database dan siap dipakai oleh sistem CBT. Hindari perubahan setelah siswa mulai mengerjakan ujian.</div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Step Indicator -->
    <div class="d-none d-flex align-items-center justify-content-center mb-4 gap-2">
      <div class="d-flex align-items-center">
        <span class="step-badge <?= empty($assignedBanks) ? 'step-active' : 'step-done' ?>"><?= empty($assignedBanks) ? '1' : '<i class="bi bi-check-lg"></i>' ?></span>
        <span class="ms-2 small fw-semibold <?= empty($assignedBanks) ? '' : 'text-success' ?>">Pilih Bank</span>
      </div>
      <span class="text-muted mx-1">→</span>
      <div class="d-flex align-items-center">
        <span class="step-badge <?= !empty($assignedBanks) && empty($paketList) ? 'step-active' : (!empty($paketList) ? 'step-done' : '') ?>"><?= !empty($paketList) ? '<i class="bi bi-check-lg"></i>' : '2' ?></span>
        <span class="ms-2 small fw-semibold <?= !empty($paketList) ? 'text-success' : '' ?>">Buat Draft Paket</span>
      </div>
      <span class="text-muted mx-1">→</span>
      <div class="d-flex align-items-center">
        <span class="step-badge">3</span>
        <span class="ms-2 small fw-semibold">Ujian Siap</span>
      </div>
    </div>

    <?php if (($currentStep ?? 1) === 1): ?>
      <div class="card cbt-step-card mb-3">
        <div class="card-header bg-white px-4 py-3">
          <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
            <div>
              <h5 class="mb-1 fw-bold">Langkah 1: Pilih Bank & Kelola Soal</h5>
              <div class="small text-muted">Pilih satu bank soal sebagai sumber CBT, lalu kelola soal di dalam bank tersebut sebelum lanjut ke penyusunan paket.</div>
            </div>
            <span class="badge bg-primary-subtle text-primary px-3 py-2 cbt-step-chip">Step 1</span>
          </div>
        </div>
        <div class="card-body p-4">
          <div class="cbt-summary-strip small text-muted mb-3">
            Mulai dari memilih satu bank soal yang benar. Setelah itu, semua soal pada langkah ini bisa ditambah, diedit, atau dihapus sebelum paket dibentuk.
          </div>
          <?php $bankLocked = !empty($paketList); ?>
          <div class="cbt-step-section">
            <div class="cbt-section-head">
              <div>
                <div class="cbt-section-title">Sumber Bank Soal</div>
                <div class="cbt-section-subtitle">Pilih sumber soal berurutan dari sekolah sampai bank soal.</div>
              </div>
            </div>
            <?php if ($bankLocked): ?>
              <div class="alert alert-warning small mb-3">
                <i class="bi bi-lock-fill me-1"></i>Sumber bank soal dikunci setelah paket terbentuk. Hapus semua paket terlebih dahulu jika ingin mengganti bank sumber.
              </div>
              <?php $activeBank = $assignedBanks[0] ?? null; ?>
              <div class="cbt-info-box p-3">
                <div class="small text-muted mb-2">Bank sumber aktif</div>
                <?php if ($activeBank): ?>
                  <div class="fw-semibold text-dark"><?= esc($activeBank['nama_ujian'] ?? '-') ?></div>
                  <div class="small text-muted mt-1"><?= esc($activeBank['kategori'] ?? '-') ?><?php if (!empty($activeBank['nama_jenis'])): ?> • <?= esc($activeBank['nama_jenis']) ?><?php endif; ?></div>
                <?php else: ?>
                  <div class="small text-muted">Bank sumber tidak ditemukan.</div>
                <?php endif; ?>
              </div>
            <?php elseif (!empty($assignedBanks)): ?>
              <?php $activeBank = $assignedBanks[0] ?? null; ?>
              <div class="cbt-info-box p-3">
                <div class="small text-muted mb-2">Bank sumber aktif</div>
                <?php if ($activeBank): ?>
                  <div class="fw-semibold text-dark"><?= esc($activeBank['nama_ujian'] ?? '-') ?></div>
                  <div class="small text-muted mt-1"><?= esc($activeBank['kategori'] ?? '-') ?><?php if (!empty($activeBank['nama_jenis'])): ?> &bull; <?= esc($activeBank['nama_jenis']) ?><?php endif; ?></div>
                  <div class="small text-muted mt-2">Daftar soal di bawah diambil dari bank sumber ini.</div>
                <?php else: ?>
                  <div class="small text-muted">Bank sumber tidak ditemukan.</div>
                <?php endif; ?>
                <form method="post" action="<?= base_url('guru/ujian/' . $ujian['id_ujian'] . '/bank/sync') ?>" class="mt-3">
                  <button type="submit" class="btn btn-outline-secondary btn-sm cbt-action-secondary" onclick="return confirm('Ganti bank sumber? Pilihan bank saat ini akan dikosongkan dan daftar soal tidak akan ditampilkan sampai Anda memilih bank baru.')">
                    <i class="bi bi-arrow-repeat me-1"></i>Ganti Bank Sumber
                  </button>
                </form>
              </div>
            <?php else: ?>
              <form method="post" action="<?= base_url('guru/ujian/' . $ujian['id_ujian'] . '/bank/sync') ?>" class="cbt-bank-picker">
                <div class="cbt-bank-grid mb-3">
                  <div class="cbt-bank-field">
                    <label class="form-label">Pilih Sekolah</label>
                    <select id="bankSekolah" class="form-select">
                      <option value="">Pilih Sekolah</option>
                      <option value="__umum">Umum / Semua Sekolah</option>
                      <?php foreach (($sekolah ?? []) as $sk): ?>
                        <option value="<?= esc($sk['sekolah_id']) ?>"><?= esc($sk['nama_sekolah']) ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  <div class="cbt-bank-field">
                    <label class="form-label">Pilih Kelas</label>
                    <select id="bankKelas" class="form-select" disabled><option value="">Pilih sekolah dulu</option></select>
                  </div>
                  <div class="cbt-bank-field">
                    <label class="form-label">Mata Pelajaran</label>
                    <select id="bankMapel" class="form-select" disabled><option value="">Pilih kelas dulu</option></select>
                  </div>
                  <div class="cbt-bank-field">
                    <label class="form-label">Bank Soal</label>
                    <select id="bankId" class="form-select" disabled><option value="">Pilih mata pelajaran dulu</option></select>
                  </div>
                </div>
                <div id="bankInfo" class="d-none mb-3">
                  <div class="cbt-bank-result">
                    <div class="cbt-bank-result-icon"><i class="bi bi-check2-circle"></i></div>
                    <div>
                      <div class="cbt-bank-result-title">Bank soal siap digunakan</div>
                      <div class="cbt-bank-result-text"><span id="bankSoalCount">-</span> soal tersedia di bank yang dipilih.</div>
                    </div>
                  </div>
                </div>
                <?php $assignedIds = array_column((array)$assignedBanks, 'bank_ujian_id'); ?>
                <input type="hidden" id="bankKat" value="">
                <input type="hidden" name="bank_ids[]" id="bankHidden" value="<?= $assignedIds[0] ?? '' ?>">
                <button type="submit" class="btn btn-success cbt-bank-save" id="bankSimpanBtn" disabled>
                  <i class="bi bi-check-lg me-1"></i>Simpan Bank Sumber
                </button>
              </form>
            <?php endif; ?>
          </div>

          <div class="cbt-step-section">
            <div class="cbt-section-head">
              <div>
                <div class="cbt-section-title">Daftar Soal Bank</div>
                <div class="cbt-section-subtitle">Tambahkan, edit, atau hapus soal pada bank yang sudah dipilih.</div>
              </div>
              <div class="cbt-action-row">
                <?php if (!empty($assignedBanks)): ?>
                  <span class="cbt-count-chip"><?= (int)$totalSoal ?> soal</span>
                <?php endif; ?>
                <button class="btn btn-outline-dark btn-sm cbt-action-secondary" data-bs-toggle="modal" data-bs-target="#modalTambahSoalCBTGuru" <?= empty($assignedBanks) ? 'disabled' : '' ?>><i class="bi bi-plus-lg me-1"></i>Tambah Soal</button>
              </div>
            </div>
            <?php if (empty($assignedBanks)): ?>
              <div class="text-center text-muted cbt-empty-state">
                <div class="empty-icon"><i class="bi bi-journals"></i></div>
                <p class="fw-semibold mt-3 mb-1 text-secondary">Belum ada soal</p>
                <small>Pilih bank soal terlebih dahulu agar daftar soal muncul di sini.</small>
              </div>
            <?php else: ?>
              <?php
                $bankSoalG = [];
                $dbG = \Config\Database::connect();
                $bankSoalG = $dbG->table('soal_ujian')
                    ->where('bank_ujian_id', $assignedBanks[0]['bank_ujian_id'])
                    ->where('is_bank_soal', 1)
                    ->orderBy('created_at', 'DESC')
                    ->get()->getResultArray();
              ?>
              <div class="table-responsive cbt-table-wrap">
                <table class="table table-hover align-middle mb-0 cbt-bank-table">
                  <thead><tr><th class="ps-4">#</th><th>Kode</th><th>Pertanyaan</th><th class="text-center">Benar</th><th class="text-center">Kesulitan</th><th>Metadata</th><th class="text-center">Aksi</th></tr></thead>
                  <tbody>
                    <?php if (empty($bankSoalG)): ?>
                      <tr><td colspan="7" class="text-center py-4 text-muted">Belum ada soal di bank ini. Klik "Tambah Soal".</td></tr>
                    <?php else: $no = 1; foreach ($bankSoalG as $s): ?>
                      <tr>
                        <td class="ps-4 text-muted small"><?= $no++ ?></td>
                        <td><span class="cbt-code-pill"><?= esc($s['kode_soal']) ?></span></td>
                        <td><div class="text-truncate cbt-question-preview" title="<?= esc(strip_tags($s['pertanyaan'])) ?>"><?= esc(strip_tags($s['pertanyaan'])) ?: '-' ?></div></td>
                        <td class="text-center"><span class="cbt-answer-pill"><?= esc($s['jawaban_benar']) ?></span></td>
                        <td class="text-center"><span class="cbt-difficulty-pill"><?= number_format((float)$s['tingkat_kesulitan'], 2) ?></span></td>
                        <td><div class="d-flex flex-wrap gap-1">
                          <?php if(!empty($s['variabel_id'])): foreach($variabel as $v): if($v['variabel_id']==$s['variabel_id']): ?><span class="cbt-meta-pill"><?= esc($v['nama_variabel']) ?></span><?php endif; endforeach; endif; ?>
                          <?php if(!empty($s['materi_id'])): foreach($materi as $m): if($m['materi_id']==$s['materi_id']): ?><span class="cbt-meta-pill"><?= esc($m['nama_materi']) ?></span><?php endif; endforeach; endif; ?>
                          <?php if (empty($s['variabel_id']) && empty($s['materi_id'])): ?><span class="text-muted small">-</span><?php endif; ?>
                        </div></td>
                        <td class="text-center">
                          <div class="cbt-row-actions">
                            <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalEditSoalGuru<?= $s['soal_id'] ?>" title="Edit"><i class="bi bi-pencil"></i></button>
                            <a href="<?= base_url('guru/soal/hapus/' . $s['soal_id'] . '/' . $ujian['id_ujian']) ?>" class="btn btn-sm btn-outline-danger" title="Hapus" onclick="return confirm('Hapus soal?')"><i class="bi bi-trash"></i></a>
                          </div>
                        </td>
                      </tr>
                    <?php endforeach; endif; ?>
                  </tbody>
                </table>
              </div>
            <?php endif; ?>
          </div>
        </div>
        <div class="card-footer bg-white d-flex justify-content-between align-items-center flex-wrap cbt-step-footer">
          <div class="small text-muted">Pastikan bank soal dan daftar soalnya sudah benar sebelum membuat draft paket.</div>
          <a href="<?= $step1Done ? base_url('guru/soal/' . $ujian['id_ujian'] . '?step=2&panel=generate') : '#' ?>" class="btn btn-primary <?= $step1Done ? '' : 'disabled' ?>">
            Buat Draft Paket <i class="bi bi-arrow-right ms-1"></i>
          </a>
        </div>
      </div>
    <?php endif; ?>

    <div class="row g-4 mb-4">
      <div class="d-none">
        <div class="card cbt-step-card h-100">
          <?php $bankLocked = !empty($paketList); ?>
          <div class="card-header d-flex align-items-center">
            <div class="icon-circle bg-primary bg-opacity-10 text-primary me-3"><i class="bi bi-database fs-5"></i></div>
            <div>
              <h6 class="mb-0 fw-bold">Sumber Bank Soal</h6>
              <small class="text-muted"><?= $bankLocked ? 'Terkunci karena paket sudah terbentuk' : 'Pilih satu bank sebagai sumber soal' ?></small>
            </div>
          </div>
          <div class="card-body p-4">
            <?php if ($bankLocked): ?>
              <div class="alert alert-warning small mb-3">
                <i class="bi bi-lock-fill me-1"></i>Sumber bank soal dikunci setelah paket terbentuk. Hapus semua paket terlebih dahulu jika ingin mengganti bank sumber.
              </div>
              <?php $activeBank = $assignedBanks[0] ?? null; ?>
              <div class="border rounded p-3 bg-light">
                <div class="small text-muted mb-2">Bank sumber aktif</div>
                <?php if ($activeBank): ?>
                  <div class="fw-semibold text-dark"><?= esc($activeBank['nama_ujian'] ?? '-') ?></div>
                  <div class="small text-muted mt-1"><?= esc($activeBank['kategori'] ?? '-') ?><?php if (!empty($activeBank['nama_jenis'])): ?> • <?= esc($activeBank['nama_jenis']) ?><?php endif; ?></div>
                  <div class="small text-primary mt-2"><i class="bi bi-check-circle-fill me-1"></i>Tetap dipakai untuk semua paket yang sudah terbentuk</div>
                <?php else: ?>
                  <div class="small text-muted">Bank sumber tidak ditemukan.</div>
                <?php endif; ?>
              </div>
            <?php else: ?>
              <form method="post" action="<?= base_url('guru/ujian/' . $ujian['id_ujian'] . '/bank/sync') ?>">
                <div class="row g-2 mb-3">
                  <div class="col-12">
                    <label class="form-label small fw-semibold">Kategori</label>
                    <select id="bankKatLegacy" class="form-select form-select-sm"><option value="">Pilih Kategori</option></select>
                  </div>
                  <div class="col-12">
                    <label class="form-label small fw-semibold">Mata Pelajaran</label>
                    <select id="bankMapelLegacy" class="form-select form-select-sm" disabled><option value="">Pilih Kategori dulu</option></select>
                  </div>
                  <div class="col-12">
                    <label class="form-label small fw-semibold">Bank Soal</label>
                    <select id="bankIdLegacy" class="form-select form-select-sm" disabled><option value="">Pilih Mapel dulu</option></select>
                  </div>
                </div>
                <div id="bankInfoLegacy" class="d-none mb-3">
                  <div class="stat-box mb-2">
                    <div class="stat-value text-primary" id="bankSoalCountLegacy">-</div>
                    <div class="stat-label">Soal Tersedia</div>
                  </div>
                </div>
                <?php $assignedIds = array_column((array)$assignedBanks, 'bank_ujian_id'); ?>
                <input type="hidden" name="bank_ids[]" id="bankHiddenLegacy" value="<?= $assignedIds[0] ?? '' ?>">
                <button type="submit" class="btn btn-primary w-100 fw-semibold" id="bankSimpanBtnLegacy" disabled>
                  <i class="bi bi-check-lg me-1"></i>Simpan
                </button>
              </form>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- Draft Paket / Review Draft (toggle) -->
      <div class="<?= ($currentStep ?? 1) === 2 ? 'col-12' : 'd-none' ?>">
        <div class="card shadow-sm h-100">

          <!-- Panel: Generate -->
          <div id="panelGenerateG" <?= (($panelAktif ?? 'generate') !== 'generate' || ($currentStep ?? 1) !== 2) ? 'class="d-none"' : '' ?>>
            <div class="card-header d-flex justify-content-between align-items-center">
              <div class="d-flex align-items-center">
                <div class="icon-circle bg-warning bg-opacity-10 text-warning me-3"><i class="bi bi-lightning fs-5"></i></div>
                <div>
                  <h6 class="mb-0 fw-bold">Buat Draft Paket Soal</h6>
                  <small class="text-muted">Susun draft paket, review hasilnya, lalu simpan sebagai paket final</small>
                </div>
              </div>
              <?php if ($hasDraft || !empty($paketList)): ?>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="cbtShowPanelG('paket')">
                  <i class="bi bi-list-ul me-1"></i><?= $hasDraft ? 'Review Draft' : 'Lihat Draft' ?>
                </button>
              <?php endif; ?>
            </div>
            <div class="card-body p-4">
              <?php if (empty($assignedBanks)): ?>
                <div class="text-center py-5 text-muted">
                  <div style="font-size:2.5rem;opacity:0.25"><i class="bi bi-bank2"></i></div>
                  <p class="fw-semibold mt-3 mb-1 text-secondary">Belum ada bank dipilih</p>
                  <small>Pilih sumber bank di kolom kiri untuk memulai</small>
                </div>
              <?php else: ?>
                <div class="row g-3 mb-4">
                  <div class="col-6">
                    <div class="stat-box">
                      <div class="stat-icon text-success"><i class="bi bi-layers-fill"></i></div>
                      <div class="stat-value text-success"><?= $totalSoal ?></div>
                      <div class="stat-label">Soal Tersedia</div>
                    </div>
                  </div>
                  <div class="col-6">
                    <div class="stat-box">
                      <div class="stat-icon <?= ($hasDraft || count($paketList) > 0) ? 'text-warning' : 'text-muted' ?>"><i class="bi bi-box-seam-fill"></i></div>
                      <div class="stat-value <?= ($hasDraft || count($paketList) > 0) ? 'text-warning' : 'text-muted' ?>"><?= $hasDraft ? count($draftPaket['packages']) : count($paketList) ?></div>
                      <div class="stat-label"><?= $hasDraft ? 'Draft Paket' : 'Paket Final' ?></div>
                    </div>
                  </div>
                </div>
                <div class="cbt-summary-strip small mb-3">
                  <div class="fw-semibold text-dark mb-1"><i class="bi bi-info-circle me-1"></i>Alur draft paket</div>
                  <div class="text-muted">Klik <strong>Buat Draft</strong> untuk menyusun paket acak. Draft belum dipakai siswa dan belum disimpan sebagai paket final.</div>
                  <div class="text-muted mt-1">Setelah review selesai, klik <strong>Simpan Paket Final</strong> untuk mengunci paket ke database.</div>
                </div>
                <form method="post" action="<?= base_url('guru/ujian/' . $ujian['id_ujian'] . '/generate-paket/proses') ?>" onsubmit="return confirm('Buat draft paket baru untuk direview?<?= !empty($paketList) ? ' Paket final lama tidak akan berubah sampai Anda menekan Simpan Paket.' : '' ?>')">
                  <div class="row g-3 mb-3">
                    <div class="col-6">
                      <label class="form-label fw-semibold small text-dark">Jumlah Paket</label>
                      <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-stack"></i></span>
                        <input type="number" name="jumlah_paket" class="form-control" value="<?= esc($draftPaket['jumlah_paket'] ?? 3) ?>" min="1" max="20" required>
                      </div>
                    </div>
                    <div class="col-6">
                      <label class="form-label fw-semibold small text-dark">Soal per Paket</label>
                      <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-list-ol"></i></span>
                        <input type="number" name="soal_per_paket" class="form-control" value="<?= esc($draftPaket['soal_per_paket'] ?? 25) ?>" min="1" max="100" required>
                      </div>
                    </div>
                  </div>
                  <div class="rule-box mb-3">
                    <i class="bi bi-info-circle text-primary me-2"></i>
                    <span>Soal per paket <strong>&le;</strong> total soal tersedia. Overlap <strong>diperbolehkan</strong>.</span>
                  </div>
                  <button type="submit" class="btn btn-outline-dark w-100 cbt-draft-submit">
                    <i class="bi bi-shuffle me-2"></i>Buat Draft
                  </button>
                </form>
              <?php endif; ?>
            </div>
          </div>

          <!-- Panel: Daftar Paket -->
          <div id="panelPaketG" <?= (($panelAktif ?? 'generate') !== 'paket' || ($currentStep ?? 1) !== 2) ? 'class="d-none"' : '' ?>>
            <div class="card-header d-flex justify-content-between align-items-center">
              <div class="d-flex align-items-center">
                <div class="icon-circle bg-secondary bg-opacity-10 text-secondary me-3"><i class="bi bi-collection fs-5"></i></div>
                <h6 class="mb-0 fw-bold">Review Draft Paket <span class="badge bg-secondary ms-1"><?= count($draftPaket['packages'] ?? []) ?></span></h6>
              </div>
              <div class="cbt-action-row">
                <?php if ($hasDraft): ?>
                  <button type="button" class="btn btn-sm btn-outline-secondary" onclick="cbtShowPanelG('generate')">
                    <i class="bi bi-arrow-repeat me-1"></i>Atur Ulang Draft
                  </button>
                  <form method="post" action="<?= base_url('guru/ujian/' . $ujian['id_ujian'] . '/generate-paket/simpan') ?>" onsubmit="return confirm('Simpan draft ini menjadi paket final? Setelah disimpan, paket akan dipakai sistem CBT.')">
                    <button type="submit" class="btn btn-sm btn-success">
                      <i class="bi bi-lock-fill me-1"></i>Simpan Paket Final
                    </button>
                  </form>
                <?php endif; ?>
              </div>
            </div>
            <div class="card-body p-0">
              <?php if(!$hasDraft): ?>
                <div class="text-center py-4 text-muted"><small>Belum ada draft paket untuk direview.</small></div>
              <?php else: ?>
                <div class="cbt-summary-strip small border-bottom">
                  <div class="fw-semibold text-dark mb-1">Penjelasan aksi</div>
                  <div class="text-muted"><strong>Atur Ulang Draft</strong> akan membawa Anda kembali ke form generate. Draft baru akan mengganti draft sebelumnya saat Anda membuat ulang.</div>
                  <div class="text-muted">Klik <strong>Simpan Paket Final</strong> jika susunan soal sudah final dan siap dikunci.</div>
                </div>
                <div class="table-responsive cbt-table-wrap">
                  <table class="table table-hover mb-0 small">
                    <thead class="table-light"><tr><th class="ps-4">#</th><th>Nama</th><th class="text-center">Soal</th><th></th></tr></thead>
                    <tbody>
                      <?php foreach(($draftPaket['packages'] ?? []) as $idx => $p): ?>
                        <tr>
                          <td class="ps-4 fw-bold"><?= $p['nomor_paket'] ?? ($idx + 1) ?></td>
                          <td><?= esc($p['nama_paket']) ?></td>
                          <td class="text-center"><span class="badge bg-secondary"><?= $p['jumlah_soal'] ?? count($p['soal_ids'] ?? []) ?></span></td>
                          <td class="text-end pe-3">
                            <button type="button" class="btn btn-sm btn-light me-1" onclick='lihatDraftPaketG(<?= $idx + 1 ?>, <?= json_encode($p["nama_paket"] ?? "") ?>)' title="Review soal"><i class="bi bi-eye"></i></button>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              <?php endif; ?>
            </div>
          </div>
          <?php if (($currentStep ?? 1) === 2): ?>
            <div class="card-footer bg-white d-flex justify-content-between align-items-center flex-wrap cbt-step-footer">
              <div class="small text-muted">Review draft paket sebelum menyimpannya sebagai paket final.</div>
              <div class="cbt-action-row">
                <a href="<?= base_url('guru/soal/' . $ujian['id_ujian'] . '?step=1') ?>" class="btn btn-outline-secondary">
                  <i class="bi bi-arrow-left me-1"></i>Previous
                </a>
                <?php if ($step2Done): ?>
                  <a href="<?= base_url('guru/soal/' . $ujian['id_ujian'] . '?step=3') ?>" class="btn btn-primary">
                    Next <i class="bi bi-arrow-right ms-1"></i>
                  </a>
                <?php else: ?>
                  <button type="button" class="btn btn-primary" disabled>Next</button>
                <?php endif; ?>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <?php if (($currentStep ?? 1) === 3 && $step2Done): ?>
      <div class="card cbt-step-card border-success mb-3">
        <div class="card-body p-4">
          <div class="d-flex align-items-start">
            <div class="icon-circle bg-success bg-opacity-10 text-success me-3"><i class="bi bi-check2-circle fs-5"></i></div>
            <div class="flex-grow-1">
              <h5 class="mb-1 fw-bold text-dark">Paket Final Siap</h5>
              <p class="mb-2 text-muted small">Paket final sudah tersimpan dan siap dipakai pada ujian CBT ini.</p>
              <div class="cbt-final-stats mt-3">
                <div class="cbt-final-stat">
                  <div class="label">Total Paket</div>
                  <div class="value"><?= count($paketList) ?></div>
                </div>
                <div class="cbt-final-stat">
                  <div class="label">Attempt Tercatat</div>
                  <div class="value"><?= (int)($attemptCount ?? 0) ?></div>
                </div>
                <div class="cbt-final-stat">
                  <div class="label">Status Reset</div>
                  <div class="value">
                    <span class="cbt-status-chip <?= empty($paketSudahDipakai) ? '' : 'locked' ?>">
                      <?= empty($paketSudahDipakai) ? 'Tersedia' : 'Terkunci' ?>
                    </span>
                  </div>
                </div>
              </div>
              <div class="mt-4">
                <div class="d-flex align-items-center justify-content-between mb-2">
                  <div>
                    <div class="fw-semibold text-dark">Review Paket Final</div>
                    <div class="small text-muted">Periksa isi paket final sebelum ujian digunakan siswa.</div>
                  </div>
                </div>
                <?php if (empty($paketList)): ?>
                  <div class="text-muted">Belum ada paket final untuk direview.</div>
                <?php else: ?>
                  <div class="table-responsive cbt-final-review">
                    <table class="table table-hover mb-0 align-middle">
                      <thead>
                        <tr>
                          <th class="ps-3">#</th>
                          <th>Nama Paket</th>
                          <th class="text-center">Jumlah Soal</th>
                          <th class="text-end pe-3">Review</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($paketList as $idx => $paket): ?>
                          <tr>
                            <td class="ps-3 fw-semibold"><?= $paket['nomor_paket'] ?? ($idx + 1) ?></td>
                            <td><?= esc($paket['nama_paket']) ?></td>
                            <td class="text-center"><span class="cbt-soft-count"><?= esc($paket['jumlah_soal'] ?? 0) ?></span></td>
                            <td class="text-end pe-3">
                              <button type="button" class="btn btn-sm btn-outline-primary" onclick='lihatPaketG(<?= $paket['paket_id'] ?>, <?= json_encode($paket["nama_paket"] ?? "") ?>)'>
                                <i class="bi bi-eye me-1"></i>Review Soal
                              </button>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
        <div class="card-footer bg-white d-flex justify-content-end align-items-center flex-wrap gap-2">
          <div class="cbt-final-actions d-flex gap-2">
            <?php if (empty($paketSudahDipakai)): ?>
              <a href="<?= base_url('guru/ujian/' . $ujian['id_ujian'] . '/paket/hapus-semua') ?>" class="btn btn-outline-danger" onclick="return confirm('Semua paket final akan dihapus dan Anda akan kembali ke langkah awal. Lanjutkan?')">
                <i class="bi bi-arrow-counterclockwise me-1"></i>Reset Paket
              </a>
            <?php else: ?>
              <button type="button" class="btn btn-outline-secondary" disabled>
                <i class="bi bi-lock me-1"></i>Paket Sudah Dipakai Siswa
              </button>
            <?php endif; ?>
            <a href="<?= base_url('guru/ujian') ?>" class="btn btn-success">
              <i class="bi bi-check-lg me-1"></i>Kembali ke Ujian
            </a>
          </div>
        </div>
      </div>
    <?php endif; ?>

    <!-- Daftar Soal Bank -->
    <?php if (false && ($currentStep ?? 1) === 1): ?>
    <div class="card shadow-sm mb-3">
      <div class="card-header d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
          <div class="icon-circle bg-primary bg-opacity-10 text-primary me-2"><i class="bi bi-list-ul"></i></div>
          <h6 class="mb-0 fw-bold ms-1">Daftar Soal Bank<?php if (!empty($assignedBanks)): ?> <span class="badge bg-secondary ms-2"><?= $totalSoal ?></span><?php endif; ?></h6>
        </div>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahSoalCBTGuru"><i class="bi bi-plus-lg me-1"></i>Tambah Soal</button>
      </div>
      <div class="card-body p-0">
            <?php if (empty($assignedBanks)): ?>
              <div class="text-center py-5 text-muted">
                <div style="font-size:2.5rem;opacity:0.25"><i class="bi bi-journals"></i></div>
                <p class="fw-semibold mt-3 mb-1 text-secondary">Belum ada soal</p>
                <small>Hubungkan bank soal terlebih dahulu melalui panel di atas</small>
              </div>
            <?php else: ?>
              <?php
                $bankSoalG = [];
                if (!empty($assignedBanks)) {
                    $dbG = \Config\Database::connect();
                    $bankSoalG = $dbG->table('soal_ujian')
                        ->where('bank_ujian_id', $assignedBanks[0]['bank_ujian_id'])
                        ->where('is_bank_soal', 1)
                        ->orderBy('created_at', 'DESC')
                        ->get()->getResultArray();
                }
              ?>
              <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                  <thead class="table-light small"><tr><th class="ps-4">#</th><th>Kode</th><th>Pertanyaan</th><th class="text-center">Benar</th><th class="text-center">Kesulitan</th><th>Metadata</th><th class="text-center">Aksi</th></tr></thead>
                  <tbody>
                    <?php if (empty($bankSoalG)): ?>
                      <tr><td colspan="7" class="text-center py-4 text-muted">Belum ada soal. Klik "Tambah Soal".</td></tr>
                    <?php else: $no = 1; foreach ($bankSoalG as $s): ?>
                      <tr>
                        <td class="ps-4 text-muted small"><?= $no++ ?></td>
                        <td><code><?= esc($s['kode_soal']) ?></code></td>
                        <td><div class="text-truncate" style="max-width:280px" title="<?= esc(strip_tags($s['pertanyaan'])) ?>"><?= esc(strip_tags($s['pertanyaan'])) ?></div></td>
                        <td class="text-center"><span class="badge bg-success"><?= $s['jawaban_benar'] ?></span></td>
                        <td class="text-center small"><?= number_format($s['tingkat_kesulitan'], 2) ?></td>
                        <td><div class="d-flex flex-wrap gap-1">
                          <?php if(!empty($s['variabel_id'])): foreach($variabel as $v): if($v['variabel_id']==$s['variabel_id']): ?><span class="badge bg-info bg-opacity-10 text-info" style="font-size:0.65rem"><?= esc($v['nama_variabel']) ?></span><?php endif; endforeach; endif; ?>
                          <?php if(!empty($s['materi_id'])): foreach($materi as $m): if($m['materi_id']==$s['materi_id']): ?><span class="badge bg-success bg-opacity-10 text-success" style="font-size:0.65rem"><?= esc($m['nama_materi']) ?></span><?php endif; endforeach; endif; ?>
                        </div></td>
                        <td class="text-center">
                          <button class="btn btn-sm btn-light me-1" data-bs-toggle="modal" data-bs-target="#modalEditSoalGuru<?= $s['soal_id'] ?>" title="Edit"><i class="bi bi-pencil"></i></button>
                          <a href="<?= base_url('guru/soal/hapus/' . $s['soal_id'] . '/' . $ujian['id_ujian']) ?>" class="btn btn-sm btn-light text-danger" title="Hapus" onclick="return confirm('Hapus soal?')"><i class="bi bi-trash"></i></a>
                        </td>
                      </tr>
                    <?php endforeach; endif; ?>
                  </tbody>
                </table>
              </div>
            <?php endif; ?>
          </div>
        </div>
        <div class="card-footer bg-white d-flex justify-content-between align-items-center">
          <div class="small text-muted">Pastikan bank soal dan daftar soalnya sudah benar sebelum membuat draft paket.</div>
          <a href="<?= $step1Done ? base_url('guru/soal/' . $ujian['id_ujian'] . '?step=2&panel=generate') : '#' ?>" class="btn btn-primary <?= $step1Done ? '' : 'disabled' ?>">
            Buat Draft Paket <i class="bi bi-arrow-right ms-1"></i>
          </a>
        </div>
      </div>
    <?php endif; ?>


<!-- Modal Lihat Soal Paket Guru -->
<div class="modal fade cbt-review-modal" id="modalLihatPaketG" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-white px-4 py-3 border-bottom">
        <h5 class="modal-title fw-bold" id="modalLihatPaketTitleG">Soal dalam Paket</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body px-4 py-3" id="modalLihatPaketBodyG">
        <div class="text-center py-3"><div class="spinner-border text-primary"></div></div>
      </div>
    </div>
  </div>
</div>

<script>
function cleanupReviewModalStateG(modalEl) {
  if (!modalEl) {
    return;
  }
  modalEl.classList.remove('show');
  modalEl.setAttribute('aria-hidden', 'true');
  modalEl.removeAttribute('aria-modal');
  modalEl.style.removeProperty('display');
  document.querySelectorAll('.modal-backdrop').forEach(function(backdrop) {
    backdrop.remove();
  });
  document.body.classList.remove('modal-open');
  document.body.style.removeProperty('padding-right');
  document.body.style.removeProperty('overflow');
  document.documentElement.classList.remove('modal-open');
  document.documentElement.style.removeProperty('overflow');
  document.body.style.pointerEvents = '';
  document.documentElement.style.pointerEvents = '';
}

function openReviewModalG(modalId, titleId, bodyId, titleText) {
  const modalEl = document.getElementById(modalId);
  if (!modalEl) {
    return null;
  }

  if (titleId) {
    document.getElementById(titleId).textContent = titleText;
  }
  if (bodyId) {
    document.getElementById(bodyId).innerHTML = '<div class="text-center py-3"><div class="spinner-border text-primary"></div></div>';
  }

  // Putuskan MutationObserver dari resetPageScrollState agar tidak mengganggu saat Bootstrap
  // menambah class modal-open ke body ketika membuka modal ini
  if (typeof _pageScrollObserver !== 'undefined' && _pageScrollObserver) {
    _pageScrollObserver.disconnect();
    _pageScrollObserver = null;
  }

  const existing = bootstrap.Modal.getInstance(modalEl);

  if (existing) {
    if (existing._isTransitioning) {
      // Modal sedang animasi menutup (backdrop-click), tunggu selesai lalu buka ulang fresh
      modalEl.addEventListener('hidden.bs.modal', function() {
        if (typeof _pageScrollObserver !== 'undefined' && _pageScrollObserver) {
          _pageScrollObserver.disconnect();
          _pageScrollObserver = null;
        }
        existing.dispose();
        cleanupReviewModalStateG(modalEl);
        new bootstrap.Modal(modalEl).show();
      }, { once: true });
    } else {
      // Instance ada tapi tidak sedang animasi — dispose dan buka fresh
      existing.dispose();
      cleanupReviewModalStateG(modalEl);
      new bootstrap.Modal(modalEl).show();
    }
  } else {
    cleanupReviewModalStateG(modalEl);
    new bootstrap.Modal(modalEl).show();
  }

  return modalEl;
}

function cbtShowPanelG(panel) {
  document.getElementById('panelGenerateG').classList.toggle('d-none', panel !== 'generate');
  document.getElementById('panelPaketG').classList.toggle('d-none', panel !== 'paket');
}

function stripHtmlG(text) {
  return (text || '').replace(/<[^>]*>/g, '').replace(/\s+/g, ' ').trim();
}

function renderReviewSoalG(data, type) {
  let html = '<div class="cbt-review-list">';
  data.forEach((s, i) => {
    const question = stripHtmlG(s.pertanyaan);
    const kode = s.kode_soal ? stripHtmlG(s.kode_soal) : '-';
    const benar = s.jawaban_benar || '-';
    const difficulty = s.tingkat_kesulitan !== undefined && s.tingkat_kesulitan !== null && s.tingkat_kesulitan !== ''
      ? parseFloat(s.tingkat_kesulitan).toFixed(2)
      : '-';
    const options = {
      A: s.pilihan_a || '',
      B: s.pilihan_b || '',
      C: s.pilihan_c || '',
      D: s.pilihan_d || '',
      E: s.pilihan_e || ''
    };
    const pembahasan = stripHtmlG(s.pembahasan);
    let item = '<div class="cbt-review-item">';
    item += '<div class="cbt-review-head">';
    item += '<div class="cbt-review-code"><i class="bi bi-upc-scan me-1"></i>' + kode + '</div>';
    item += '<span class="cbt-review-number">#' + (i + 1) + '</span>';
    item += '</div>';
    item += '<div class="cbt-review-question">' + (question || 'Pertanyaan tidak tersedia') + '</div>';
    item += '<div class="cbt-review-options">';
    Object.keys(options).forEach((key) => {
      const value = stripHtmlG(options[key]);
      if (!value) return;
      item += '<div class="cbt-review-option ' + (key === benar ? 'is-correct' : '') + '">';
      item += '<div class="cbt-review-option-key">' + key + '.</div>';
      item += '<div>' + value + '</div>';
      item += '</div>';
    });
    item += '</div>';
    item += '<div class="cbt-review-meta">';
    item += '<span class="cbt-review-pill"><i class="bi bi-check2-circle me-1 text-success"></i>Benar: ' + benar + '</span>';
    item += '<span class="cbt-review-pill"><i class="bi bi-bar-chart me-1 text-primary"></i>Kesulitan: ' + difficulty + '</span>';
    if (type === 'draft') item += '<span class="cbt-review-pill"><i class="bi bi-pencil-square me-1 text-secondary"></i>Draft</span>';
    item += '</div>';
    if (pembahasan) {
      item += '<div class="cbt-review-explain"><strong>Pembahasan:</strong> ' + pembahasan + '</div>';
    }
    item += '</div>';
    html += item;
  });
  html += '</div>';
  return html;
}

function lihatPaketG(paketId, nama) {
  openReviewModalG('modalLihatPaketG', 'modalLihatPaketTitleG', 'modalLihatPaketBodyG', 'Soal dalam ' + nama);
  fetch('<?= base_url('guru/ujian/paket/') ?>' + paketId + '/soal')
    .then(r => r.json())
    .then(data => {
      if (!data.length) {
        document.getElementById('modalLihatPaketBodyG').innerHTML = '<p class="text-muted text-center py-4">Tidak ada soal.</p>';
        return;
      }
      document.getElementById('modalLihatPaketBodyG').innerHTML = renderReviewSoalG(data, 'final');
    })
    .catch(() => { document.getElementById('modalLihatPaketBodyG').innerHTML = '<p class="text-danger text-center py-4">Gagal memuat data.</p>'; });
}

function lihatDraftPaketG(index, nama) {
  openReviewModalG('modalLihatPaketG', 'modalLihatPaketTitleG', 'modalLihatPaketBodyG', 'Draft ' + nama);
  fetch('<?= base_url('guru/ujian/' . $ujian['id_ujian'] . '/draft-paket/') ?>' + index + '/soal')
    .then(r => r.json())
    .then(data => {
      if (!data.length) {
        document.getElementById('modalLihatPaketBodyG').innerHTML = '<p class="text-muted text-center py-4">Tidak ada soal dalam draft ini.</p>';
        return;
      }
      document.getElementById('modalLihatPaketBodyG').innerHTML = renderReviewSoalG(data, 'draft');
    })
    .catch(() => {
      document.getElementById('modalLihatPaketBodyG').innerHTML = '<p class="text-danger text-center py-4">Gagal memuat draft paket.</p>';
    });
}

// Modal review selalu di-dispose saat ditutup dan dibuat ulang saat dibuka kembali
</script>

  <?php else: ?>
    <!-- ==================== CAT MODE ==================== -->
    <?php
      $catTotal = count($soal ?? []);
      $catBankCount = 0;
      $catCustomCount = 0;
      foreach (($soal ?? []) as $catRow) {
        if (!empty($catRow['is_bank_soal'])) {
          $catBankCount++;
        } else {
          $catCustomCount++;
        }
      }
    ?>
    <div class="card cat-manage-card mb-3">
      <div class="card-header bg-white border-bottom px-4 py-3">
        <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
          <div>
            <h5 class="mb-1 fw-bold text-dark">Kelola Pool Soal Ujian CAT</h5>
            <div class="cat-header-note">Pada CAT tidak ada paket soal. Yang dikelola adalah <strong>pool soal ujian</strong>: soal bisa diambil dari bank atau dibuat khusus untuk ujian ini.</div>
          </div>
          <div class="cat-action-group">
            <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalPilihDariBankGuru">
              <i class="bi bi-collection me-1"></i>Pilih dari Bank
            </button>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#tambahSoalModalGuru">
              <i class="bi bi-plus-lg me-1"></i>Tambah Soal Custom
            </button>
          </div>
        </div>
      </div>
      <div class="card-body p-4">
        <div class="cat-summary-panel">
          <div class="cat-summary-item">
            <div class="cat-summary-label">Total Pool</div>
            <div class="cat-summary-value"><?= $catTotal ?> soal</div>
            <div class="cat-summary-help">Semua soal yang akan dipakai CAT.</div>
          </div>
          <div class="cat-summary-item">
            <div class="cat-summary-label">Dari Bank</div>
            <div class="cat-summary-value"><?= $catBankCount ?> soal</div>
            <div class="cat-summary-help">Tetap tersimpan di bank soal asal.</div>
          </div>
          <div class="cat-summary-item">
            <div class="cat-summary-label">Soal Custom</div>
            <div class="cat-summary-value"><?= $catCustomCount ?> soal</div>
            <div class="cat-summary-help">Dibuat khusus dari halaman ujian CAT.</div>
          </div>
          <div class="cat-summary-item">
            <div class="cat-summary-label">Aksi Lepas/Hapus</div>
            <div class="cat-summary-value small">Lepas untuk soal bank, hapus untuk soal custom.</div>
            <div class="cat-summary-help">Agar user tidak salah menghapus master bank.</div>
          </div>
        </div>

        <?php if (empty($soal)): ?>
          <div class="cat-empty-state">
            <div class="icon"><i class="bi bi-diagram-3"></i></div>
            <div class="fw-semibold text-dark mb-1">Pool soal CAT masih kosong</div>
            <div class="small">Tambahkan soal custom atau pilih soal dari bank untuk mulai membentuk pool ujian.</div>
          </div>
        <?php else: ?>
          <div class="table-responsive cat-table-wrap">
            <table class="table table-hover align-middle mb-0 cat-bank-table">
              <thead>
                <tr>
                  <th class="ps-4" width="5%">#</th>
                  <th width="12%">Kode</th>
                  <th width="11%">Sumber</th>
                  <th width="26%">Pertanyaan</th>
                  <th width="7%" class="text-center">Benar</th>
                  <th width="7%" class="text-center">b</th>
                  <th width="5%" class="text-center">a</th>
                  <th width="5%" class="text-center">c</th>
                  <th width="12%">Metadata</th>
                  <th width="10%" class="text-center">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php $no = 1; foreach ($soal as $s): ?>
                  <tr>
                    <td class="ps-4 text-muted small"><?= $no++ ?></td>
                    <td><span class="cat-code-pill"><?= esc($s['kode_soal']) ?></span></td>
                    <td>
                      <?php if (!empty($s['is_bank_soal'])): ?>
                        <span class="cat-origin-pill is-bank" title="<?= esc($s['nama_bank_ujian'] ?? 'Bank tidak tersedia') ?>">
                          <?= !empty($s['nama_bank_ujian']) ? esc($s['nama_bank_ujian']) : 'Bank tidak tersedia' ?>
                        </span>
                      <?php else: ?>
                        <span class="cat-origin-pill is-custom">Soal Custom</span>
                      <?php endif; ?>
                    </td>
                    <td><div class="text-truncate cat-question-text" title="<?= esc(strip_tags($s['pertanyaan'])) ?>"><?= esc(strip_tags($s['pertanyaan'])) ?: '-' ?></div></td>
                    <td class="text-center"><span class="cat-value-pill"><?= esc($s['jawaban_benar']) ?></span></td>
                    <td class="text-center"><span class="cat-value-pill"><?= number_format((float) ($s['tingkat_kesulitan'] ?? 0), 3) ?></span></td>
                    <td class="text-center"><span class="cat-value-pill"><?= number_format((float) ($s['a'] ?? 1), 3) ?></span></td>
                    <td class="text-center"><span class="cat-value-pill"><?= number_format((float) ($s['c'] ?? 0), 3) ?></span></td>
                    <td>
                      <div class="d-flex flex-wrap gap-1">
                        <?php if (!empty($s['variabel_id'])): foreach ($variabel as $v): if ($v['variabel_id'] == $s['variabel_id']): ?>
                          <span class="cat-meta-pill"><?= esc($v['nama_variabel']) ?></span>
                        <?php endif; endforeach; endif; ?>
                        <?php if (!empty($s['materi_id'])): foreach ($materi as $m): if ($m['materi_id'] == $s['materi_id']): ?>
                          <span class="cat-meta-pill"><?= esc($m['nama_materi']) ?></span>
                        <?php endif; endforeach; endif; ?>
                        <?php if (empty($s['variabel_id']) && empty($s['materi_id'])): ?>
                          <span class="text-muted small">-</span>
                        <?php endif; ?>
                      </div>
                    </td>
                    <td class="text-center">
                      <div class="cbt-row-actions">
                        <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editSoalCatGuru<?= $s['soal_id'] ?>" title="Edit"><i class="bi bi-pencil"></i></button>
                        <?php if (!empty($s['is_bank_soal'])): ?>
                          <a href="<?= base_url('guru/soal/unassign/' . $s['soal_id'] . '/' . $ujian['id_ujian']) ?>" class="btn btn-sm btn-outline-secondary" title="Lepas dari ujian" onclick="return confirm('Lepas soal ini dari pool ujian CAT? Soal tetap aman di bank soal.')"><i class="bi bi-link-45deg"></i></a>
                        <?php else: ?>
                          <a href="<?= base_url('guru/soal/hapus/' . $s['soal_id'] . '/' . $ujian['id_ujian']) ?>" class="btn btn-sm btn-outline-danger" title="Hapus soal custom" onclick="return confirm('Hapus soal custom ini dari pool ujian CAT?')"><i class="bi bi-trash"></i></a>
                        <?php endif; ?>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>
    </div>
  <?php endif; ?>
</div>

<!-- ==================== MODAL CBT: Tambah Soal ke Bank ==================== -->
<div class="modal fade" id="modalTambahSoalCBTGuru" tabindex="-1" data-bs-focus="false">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-primary text-white px-4 py-3">
        <h5 class="modal-title fw-semibold"><i class="bi bi-plus-circle me-2"></i>Tambah Soal ke Bank</h5>
        <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form id="formTambahSoalCBTGuru" method="post">
        <input type="hidden" name="bank_ujian_id" value="<?= esc($assignedBanks[0]['bank_ujian_id'] ?? '') ?>">
        <div class="modal-body px-4 py-4">
          <div id="ajaxMsgCBTG" class="mb-3"></div>
          <div class="row g-3">
            <div class="col-md-6"><label class="form-label small fw-semibold">Kode Soal <span class="text-danger">*</span></label><input type="text" name="kode_soal" class="form-control" required></div>
            <div class="col-md-6"><label class="form-label small fw-semibold">Jawaban <span class="text-danger">*</span></label><select name="jawaban_benar" class="form-select" required><?php foreach(['A','B','C','D','E'] as $j): ?><option value="<?=$j?>"><?=$j?></option><?php endforeach; ?></select></div>
            <div class="col-md-4"><label class="form-label small fw-semibold">Diskriminasi (a)</label><input type="number" name="a" class="form-control" step="0.001" value="1.000"></div>
            <div class="col-md-4"><label class="form-label small fw-semibold">Kesulitan (b) <span class="text-danger">*</span></label><div class="input-group"><input type="number" name="tingkat_kesulitan" class="form-control" step="0.001" value="0.000" required><span class="input-group-text">-3..+3</span></div></div>
            <div class="col-md-4"><label class="form-label small fw-semibold">Guessing (c)</label><input type="number" name="c" class="form-control" step="0.001" value="0.000"></div>
            <div class="col-12"><label class="form-label small fw-semibold">Pertanyaan <span class="text-danger">*</span></label><textarea id="pertanyaan_tambah_cbt_g" name="pertanyaan" class="form-control summernote" required></textarea></div>
            <?php foreach(['a'=>'A','b'=>'B','c'=>'C','d'=>'D','e'=>'E (opsional)'] as $k=>$l): ?><div class="col-md-6"><label class="form-label small fw-semibold">Pilihan <?=$l?></label><textarea id="pilihan_<?=$k?>_tambah_cbt_g" name="pilihan_<?=$k?>" class="form-control summernote-sm" <?=$k!=='e'?'required':''?>></textarea></div><?php endforeach; ?>
            <div class="col-md-4"><label class="form-label small fw-semibold">Variabel</label><select name="variabel_id" class="form-select" onchange="loadIndikatorCBTG(this.value)"><option value="">-- Tidak ada --</option><?php foreach($variabel as $v): ?><option value="<?=$v['variabel_id']?>"><?=esc($v['nama_variabel'])?></option><?php endforeach; ?></select></div>
            <div class="col-md-4"><label class="form-label small fw-semibold">Indikator</label><select name="indikator_id" id="indikatorCBTG" class="form-select"><option value="">-- Pilih Variabel dulu --</option></select></div>
            <div class="col-md-4"><label class="form-label small fw-semibold">Materi</label><select name="materi_id" class="form-select"><option value="">-- Tidak ada --</option><?php foreach($materi as $m): ?><option value="<?=$m['materi_id']?>"><?=esc($m['nama_materi'])?></option><?php endforeach; ?></select></div>
            <div class="col-12"><label class="form-label small fw-semibold">Pembahasan</label><textarea id="pembahasan_tambah_cbt_g" name="pembahasan" class="form-control summernote"></textarea></div>
          </div>
        </div>
        <div class="modal-footer border-0 bg-light px-4 py-3">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-lg me-1"></i>Simpan Soal</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Soal Modals CBT -->
<?php if(($ujian['tipe_ujian']??'CAT')=='CBT' && !empty($bankSoalG)): foreach($bankSoalG as $s): ?>
<div class="modal fade" id="modalEditSoalGuru<?=$s['soal_id']?>" tabindex="-1" data-bs-focus="false">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-warning text-dark px-4 py-3"><h5 class="modal-title fw-semibold"><i class="bi bi-pencil me-2"></i>Edit Soal</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <form action="<?=base_url('guru/soal/edit/'.$s['soal_id'])?>" method="post">
        <input type="hidden" name="ujian_id" value="<?=$ujian['id_ujian']?>">
        <div class="modal-body px-4 py-4"><div class="row g-3">
          <div class="col-md-6"><label class="form-label small fw-semibold">Kode Soal</label><input type="text" name="kode_soal" class="form-control" value="<?=esc($s['kode_soal'])?>" required></div>
          <div class="col-md-6"><label class="form-label small fw-semibold">Jawaban Benar</label><select name="jawaban_benar" class="form-select"><?php foreach(['A','B','C','D','E'] as $j): ?><option value="<?=$j?>" <?=$s['jawaban_benar']==$j?'selected':''?>><?=$j?></option><?php endforeach; ?></select></div>
          <div class="col-md-4"><label class="form-label small fw-semibold">Diskriminasi (a)</label><input type="number" name="a" class="form-control" step="0.001" value="<?=$s['a']??1?>"></div>
          <div class="col-md-4"><label class="form-label small fw-semibold">Kesulitan (b)</label><div class="input-group"><input type="number" name="tingkat_kesulitan" class="form-control" step="0.001" value="<?=$s['tingkat_kesulitan']?>" required><span class="input-group-text">-3..+3</span></div></div>
          <div class="col-md-4"><label class="form-label small fw-semibold">Guessing (c)</label><input type="number" name="c" class="form-control" step="0.001" value="<?=$s['c']??0?>"></div>
          <div class="col-12"><label class="form-label small fw-semibold">Pertanyaan</label><textarea id="pertanyaan_edit_cbt_g_<?=$s['soal_id']?>" name="pertanyaan" class="form-control summernote" required><?=esc($s['pertanyaan'])?></textarea></div>
          <?php foreach(['a'=>'A','b'=>'B','c'=>'C','d'=>'D','e'=>'E'] as $k=>$l): ?><div class="col-md-6"><label class="form-label small fw-semibold">Pilihan <?=$l?></label><textarea id="pilihan_<?=$k?>_edit_cbt_g_<?=$s['soal_id']?>" name="pilihan_<?=$k?>" class="form-control summernote-sm" <?=$k!=='e'?'required':''?>><?=esc($s['pilihan_'.$k]??'')?></textarea></div><?php endforeach; ?>
          <div class="col-md-4"><label class="form-label small fw-semibold">Variabel</label><select name="variabel_id" class="form-select" onchange="loadIndikatorCBTG(this.value,'indikatorEditG<?=$s['soal_id']?>')"><option value="">-- Tidak ada --</option><?php foreach($variabel as $v): ?><option value="<?=$v['variabel_id']?>" <?= (string)($s['variabel_id'] ?? '') === (string)$v['variabel_id'] ? 'selected' : '' ?>><?=esc($v['nama_variabel'])?></option><?php endforeach; ?></select></div>
          <div class="col-md-4"><label class="form-label small fw-semibold">Indikator</label><select name="indikator_id" id="indikatorEditG<?=$s['soal_id']?>" class="form-select"><option value="">-- Tidak ada --</option><?php foreach($indikator as $i): if ((string)($i['variabel_id'] ?? '') !== (string)($s['variabel_id'] ?? '')) continue; ?><option value="<?=$i['indikator_id']?>" <?= (string)($s['indikator_id'] ?? '') === (string)$i['indikator_id'] ? 'selected' : '' ?>><?=esc($i['nama_indikator'])?></option><?php endforeach; ?></select></div>
          <div class="col-md-4"><label class="form-label small fw-semibold">Materi</label><select name="materi_id" class="form-select"><option value="">-- Tidak ada --</option><?php foreach($materi as $m): ?><option value="<?=$m['materi_id']?>" <?= (string)($s['materi_id'] ?? '') === (string)$m['materi_id'] ? 'selected' : '' ?>><?=esc($m['nama_materi'])?></option><?php endforeach; ?></select></div>
          <div class="col-12"><label class="form-label small fw-semibold">Pembahasan</label><textarea id="pembahasan_edit_cbt_g_<?=$s['soal_id']?>" name="pembahasan" class="form-control summernote"><?=esc($s['pembahasan']??'')?></textarea></div>
        </div></div>
        <div class="modal-footer border-0 bg-light px-4 py-3"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-warning px-4">Simpan</button></div>
      </form>
    </div>
  </div>
</div>
<?php endforeach; endif; ?>

<!-- Import Modal CBT -->
<div class="modal fade" id="modalImportBankSoalGuru" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-white px-4 py-3 border-bottom"><h5 class="modal-title fw-semibold"><i class="bi bi-download me-2"></i>Import Soal dari Bank</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body px-4 py-4">
        <form id="formImportG" method="post" action="<?=base_url('guru/soal/import-bank')?>"><input type="hidden" name="ujian_id" value="<?=$ujian['id_ujian']?>">
          <div class="row g-3 mb-4">
            <div class="col-md-3"><label class="form-label small fw-semibold">Kategori</label><select id="filterKatG" class="form-select"><option value="">Pilih Kategori</option></select></div>
            <div class="col-md-3"><label class="form-label small fw-semibold">Mata Pelajaran</label><select id="filterMapelG" class="form-select" disabled><option value="">Pilih dulu</option></select></div>
            <div class="col-md-3"><label class="form-label small fw-semibold">Bank Soal</label><select id="filterBankG" class="form-select" disabled><option value="">Pilih dulu</option></select></div>
            <div class="col-md-3"><label class="form-label small fw-semibold">Cari</label><input type="text" id="searchBankG" class="form-control" placeholder="Cari..." disabled></div>
          </div>
          <div id="loadingBankG" class="text-center py-3 d-none"><div class="spinner-border text-primary"></div></div>
          <div id="bankContainerG" class="d-none"><div class="table-responsive"><table class="table table-sm table-hover align-middle"><thead class="table-light"><tr><th><input type="checkbox" id="selectAllBankG"></th><th>#</th><th>Kode</th><th>Pertanyaan</th><th>Jawaban</th><th>Kesulitan</th></tr></thead><tbody id="bankBodyG"></tbody></table></div></div>
          <div id="noBankMsgG" class="text-center py-4 text-muted">Pilih kategori, mapel, dan bank.</div>
        </form>
      </div>
      <div class="modal-footer border-0 bg-light px-4 py-3"><button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button id="btnImportG" class="btn btn-primary" disabled><i class="bi bi-download me-1"></i>Import Terpilih</button></div>
    </div>
  </div>
</div>

<?php if(($ujian['tipe_ujian']??'CAT')!='CBT'): include_once __DIR__.'/kelola_soal_cat_modals_guru.php'; endif; ?>

<!-- ===== MODAL: Pilih dari Bank (CAT - Guru) ===== -->
<div class="modal fade" id="modalPilihDariBankGuru" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-primary text-white px-4 py-3">
        <h5 class="modal-title fw-semibold"><i class="bi bi-collection me-2"></i>Pilih Soal dari Bank</h5>
        <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form id="formPilihBankG" method="post" action="<?= base_url('guru/soal/assign-bank') ?>">
        <input type="hidden" name="ujian_id" value="<?= $ujian['id_ujian'] ?>">
        <div class="modal-body px-4 py-4">
          <p class="text-muted small mb-3"><i class="bi bi-info-circle me-1"></i>Soal ditautkan tanpa duplikasi. Soal tetap di bank.</p>
          <div class="row g-3 mb-3">
            <div class="col-md-3"><label class="form-label small">Sekolah</label><select id="pilihSekolahG" class="form-select"><option value="">Pilih Sekolah</option><option value="__umum">Umum / Semua Sekolah</option><?php foreach (($sekolah ?? []) as $sk): ?><option value="<?= esc($sk['sekolah_id']) ?>"><?= esc($sk['nama_sekolah']) ?></option><?php endforeach; ?></select></div>
            <div class="col-md-3"><label class="form-label small">Kelas</label><select id="pilihKelasG" class="form-select" disabled><option value="">Pilih sekolah dulu</option></select></div>
            <div class="col-md-3"><label class="form-label small">Mata Pelajaran</label><select id="pilihMapelG" class="form-select" disabled><option value="">Pilih kelas dulu</option></select></div>
            <div class="col-md-3"><label class="form-label small">Bank</label><select id="pilihBankG" class="form-select" disabled><option value="">Pilih mata pelajaran dulu</option></select></div>
            <input type="hidden" id="pilihKatG" value="">
            <div class="col-12"><label class="form-label small">Cari</label><input type="text" id="pilihCariG" class="form-control" placeholder="Cari soal..." disabled></div>
          </div>
          <div id="pilihLoadingG" class="text-center py-3 d-none"><div class="spinner-border text-primary"></div></div>
          <div id="pilihContainerG" class="d-none"><div class="table-responsive"><table class="table table-sm table-hover"><thead class="table-light"><tr><th><input type="checkbox" id="pilihAllG"></th><th>#</th><th>Kode</th><th>Pertanyaan</th><th>Jawaban</th><th>Kesulitan</th></tr></thead><tbody id="pilihBodyG"></tbody></table></div></div>
          <div id="pilihKosongG" class="text-center py-4 text-muted">Pilih sekolah, kelas, mata pelajaran, dan bank.</div>
        </div>
        <div class="modal-footer border-0 bg-light px-4 py-3">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" id="btnPilihG" class="btn btn-primary" disabled><i class="bi bi-link me-1"></i>Tautkan Terpilih</button>
        </div>
      </form>
    </div>
  </div>
</div>

<style>
.modal{overflow-y:auto!important}
.modal-dialog-scrollable .modal-content > form{flex:1 1 auto;display:flex;flex-direction:column;min-height:0;overflow:hidden}
.modal-dialog-scrollable .modal-content > form > .modal-body{flex:1 1 auto;min-height:0}
.step-badge{width:28px;height:28px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:0.75rem;font-weight:700;color:#868e96;background:#e9ecef;transition:all 0.3s}
.step-badge.step-active{background:#0d6efd;color:#fff;box-shadow:0 0 0 3px rgba(13,110,253,0.2)}
.step-badge.step-done{background:#157347;color:#fff}
.icon-circle{width:36px;height:36px;border-radius:6px;display:inline-flex;align-items:center;justify-content:center;flex-shrink:0}
.stat-box{background:#f8fafc;border:1px solid #eaecf0;border-radius:0;padding:1.25rem 1rem;text-align:center}
.stat-icon{font-size:1.25rem;line-height:1;margin-bottom:0.5rem}
.stat-value{font-size:1.75rem;font-weight:700;line-height:1.1}
.stat-label{font-size:0.68rem;color:#adb5bd;text-transform:uppercase;letter-spacing:0.07em;margin-top:4px}
.rule-box{background:#e7f1ff;border:1px solid #b6d4fe;border-radius:0;padding:8px 14px;font-size:0.78rem;color:#052c65}
.bank-card{border:1px solid #dee2e6;border-radius:0;cursor:pointer;transition:all 0.15s}
.bank-card:hover{border-color:#86b7fe;background:#f8f9ff}
.bank-card.selected{border-color:#0d6efd;background:rgba(13,110,253,0.03)}
.card{border-radius:0;border:1px solid #e2e5ea}
.card.shadow-sm{box-shadow:0 2px 12px rgba(0,0,0,0.08)!important}
.card-header{background:#fff;border-bottom:1px solid #edf0f3;padding:1rem 1.5rem;border-radius:0!important}
.card .table>:not(caption)>*>*{padding:0.55rem 0.75rem}
.card .table thead th{font-size:0.72rem;font-weight:600;text-transform:uppercase;letter-spacing:0.04em;color:#6c757d;background:#f8f9fa;border-bottom:2px solid #dee2e6}
.btn{border-radius:4px;font-weight:500}
.btn-sm{border-radius:4px;padding:0.35rem 0.75rem;font-size:0.8rem}
.cbt-bank-picker{background:#fbfcfe;border:1px solid #e1e7ef;padding:1.1rem}
.cbt-bank-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:1rem 1.15rem}
.cbt-bank-field .form-label{color:#475467;font-size:0.74rem;font-weight:700;letter-spacing:0.025em;margin-bottom:0.35rem}
.cbt-bank-field .form-select{background-color:#fff;min-height:42px}
.cbt-bank-field .form-select:disabled{background-color:#eef2f6;border-color:#d7dee8;color:#8a94a3;cursor:not-allowed;opacity:1}
.cbt-bank-result{align-items:center;background:#f0f7ff;border:1px solid #bfdcff;color:#123a63;display:flex;gap:0.75rem;padding:0.75rem 0.9rem}
.cbt-bank-result-icon{align-items:center;background:#fff;border:1px solid #c9e2ff;color:#0d6efd;display:inline-flex;height:34px;justify-content:center;width:34px}
.cbt-bank-result-title{color:#1f2937;font-size:0.86rem;font-weight:700}
.cbt-bank-result-text{color:#667085;font-size:0.78rem}
.cbt-bank-save{border-radius:0!important;font-size:0.82rem;font-weight:700;padding:0.52rem 0.9rem}
@media (max-width:991.98px){.cbt-bank-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media (max-width:575.98px){.cbt-bank-grid{grid-template-columns:1fr}}
.form-control,.form-select{border-radius:0;border-color:#dee2e6;font-size:0.85rem}
.form-control:focus,.form-select:focus{border-color:#86b7fe;box-shadow:0 0 0 0.2rem rgba(13,110,253,0.15)}
.alert{border-radius:0;font-size:0.85rem}
.badge{font-weight:500;letter-spacing:0.02em}
.modal-content{border-radius:0!important;border:none}
</style>

<script>
// Cascade Bank: Sekolah -> Kelas -> Mapel -> Bank
(function(){
  const sekolah=document.getElementById('bankSekolah');
  const kelas=document.getElementById('bankKelas');
  const kategori=document.getElementById('bankKat');
  const mapel=document.getElementById('bankMapel');
  const bank=document.getElementById('bankId');
  const info=document.getElementById('bankInfo');
  const save=document.getElementById('bankSimpanBtn');
  const hidden=document.getElementById('bankHidden');
  const count=document.getElementById('bankSoalCount');
  if(!sekolah||!kelas||!kategori||!mapel||!bank)return;

  function addOption(select,value,text,dataset){
    const option=document.createElement('option');
    option.value=value;
    option.textContent=text;
    if(dataset)Object.keys(dataset).forEach(key=>{option.dataset[key]=dataset[key];});
    select.appendChild(option);
  }

  function setEmpty(select,message){
    select.innerHTML='<option value="">'+message+'</option>';
    select.disabled=true;
  }

  function resetMapel(message){
    setEmpty(mapel,message);
    resetBank('Pilih mata pelajaran dulu');
  }

  function resetBank(message){
    setEmpty(bank,message);
    info?.classList.add('d-none');
    if(save)save.disabled=true;
    if(hidden)hidden.value='';
  }

  function loadMapelByKategori(category){
    mapel.innerHTML='<option value="">Loading...</option>';
    mapel.disabled=false;
    resetBank('Pilih mata pelajaran dulu');
    fetch('<?=base_url('guru/bank-soal/api/jenis-ujian')?>?kategori='+encodeURIComponent(category))
      .then(r=>r.json()).then(d=>{
        mapel.innerHTML='<option value="">Pilih Mata Pelajaran</option>';
        if(d.status==='success'&&d.data.length){
          d.data.forEach(j=>addOption(mapel,j.jenis_ujian_id,j.nama_jenis));
          mapel.disabled=false;
        }else{
          setEmpty(mapel,category==='umum'?'Belum ada mata pelajaran umum':'Belum ada mata pelajaran');
        }
      }).catch(()=>{mapel.innerHTML='<option value="">Gagal memuat mata pelajaran</option>';mapel.disabled=true;});
  }

  if(sekolah.options.length <= 2){
    setEmpty(sekolah,'Belum ada sekolah');
    resetMapel('Belum ada kelas');
  }

  sekolah.addEventListener('change',function(){
    kategori.value='';
    kelas.innerHTML='<option value="">Loading...</option>';
    kelas.disabled=!this.value;
    resetMapel('Pilih kelas dulu');
    if(!this.value){kelas.innerHTML='<option value="">Pilih sekolah dulu</option>';kelas.disabled=true;return;}
    if(this.value==='__umum'){
      kategori.value='umum';
      kelas.innerHTML='<option value="umum">Umum / Tanpa Kelas</option>';
      kelas.disabled=true;
      loadMapelByKategori('umum');
      return;
    }
    fetch('<?=base_url('guru/api/kelas-by-sekolah/')?>'+encodeURIComponent(this.value))
      .then(r=>r.json()).then(d=>{
        kelas.innerHTML='<option value="">Pilih Kelas</option>';
        if(d.status==='success'&&d.data.length){
          d.data.forEach(k=>addOption(kelas,k.kelas_id,k.nama_kelas+(k.tahun_ajaran?' - '+k.tahun_ajaran:''),{kategori:k.nama_kelas}));
          kelas.disabled=false;
        }else{
          setEmpty(kelas,'Belum ada kelas');
        }
      }).catch(()=>{kelas.innerHTML='<option value="">Gagal memuat kelas</option>';kelas.disabled=true;});
  });

  kelas.addEventListener('change',function(){
    const mp=document.getElementById('bankMapel');
    const selected=this.selectedOptions[0];
    kategori.value=selected?.dataset.kategori||'';
    mp.innerHTML='<option value="">Loading...</option>';mp.disabled=!kategori.value;
    resetBank('Pilih mata pelajaran dulu');
    if(!kategori.value){resetMapel('Pilih kelas dulu');return;}
    loadMapelByKategori(kategori.value);
  });
  document.getElementById('bankMapel')?.addEventListener('change',function(){
    const b=document.getElementById('bankId');
    b.innerHTML='<option value="">Loading...</option>';b.disabled=!this.value;
    info?.classList.add('d-none');if(save)save.disabled=true;
    if(!this.value){resetBank('Pilih mata pelajaran dulu');return;}
    fetch('<?=base_url('guru/bank-soal/api/bank-ujian')?>?kategori='+encodeURIComponent(kategori.value)+'&jenis_ujian_id='+this.value)
      .then(r=>r.json()).then(d=>{
        b.innerHTML='<option value="">Pilih Bank Soal</option>';
        if(d.status==='success'&&d.data.length){
          d.data.forEach(bk=>addOption(b,bk.bank_ujian_id,bk.nama_ujian,{soal:bk.jumlah_soal}));
          b.disabled=false;
        }else{
          setEmpty(b,'Belum ada bank soal');
        }
      }).catch(()=>{b.innerHTML='<option value="">Gagal memuat bank soal</option>';b.disabled=true;});
  });
  document.getElementById('bankId')?.addEventListener('change',function(){
    const opt=this.selectedOptions[0];
    if(hidden)hidden.value=this.value||'';
    if(save)save.disabled=!this.value;
    if(this.value){
      info?.classList.remove('d-none');
      if(count)count.textContent=opt?.dataset.soal||'0';
    }else info?.classList.add('d-none');
  });
})();

function loadIndikatorCBTG(variabelId,targetId='indikatorCBTG'){const sel=document.getElementById(targetId);if(!sel)return;sel.innerHTML='<option value="">Loading...</option>';if(!variabelId){sel.innerHTML='<option value="">-- Pilih Variabel dulu --</option>';return;}fetch('<?=base_url('guru/api/indikator-by-variabel/')?>'+variabelId).then(r=>r.json()).then(d=>{let opts='<option value="">-- Tidak ada --</option>';d.forEach(i=>{opts+='<option value="'+i.indikator_id+'">'+i.nama_indikator+'</option>';});sel.innerHTML=opts;}).catch(()=>sel.innerHTML='<option value="">Gagal</option>');}

// Summernote
function hardenSummernoteButtons(scope){(scope||document).querySelectorAll('.note-editor button, .note-modal button').forEach(function(btn){if(!btn.getAttribute('type')||btn.getAttribute('type').toLowerCase()==='submit'){btn.setAttribute('type','button');}});}
function uploadImageSimple(file,editor){if(!file||!file.type||!file.type.startsWith('image/')){alert('Pilih file gambar.');return;}if(file.size>2*1024*1024){alert('File terlalu besar. Maksimal 2MB.');return;}var $editor=$(editor);if($editor.data('uploading'))return;$editor.data('uploading',true);var formData=new FormData();formData.append('upload',file);$.ajax({url:'<?=base_url('guru/upload-summernote-image')?>',type:'POST',data:formData,processData:false,contentType:false,timeout:30000,success:function(r){$editor.data('uploading',false);if(r.success&&r.url){$editor.summernote('focus');$editor.summernote('insertImage',r.url,function($img){$img.css({'max-width':'100%','height':'auto'}).addClass('img-fluid');});}else{alert(r.error||'Gagal upload gambar');}},error:function(){$editor.data('uploading',false);alert('Gagal upload gambar');}});}
const snCfg={height:200,dialogsInBody:true,dialogsFade:false,container:'body',toolbar:[['style',['bold','italic','underline','clear']],['fontsize',['fontsize']],['color',['color']],['para',['ul','ol','paragraph']],['table',['table']],['insert',['link','picture']]],callbacks:{onInit:function(){const modal=this.closest('.modal');setTimeout(function(){hardenSummernoteButtons(modal||document);},0);},onImageUpload:function(files){if(files&&files.length>0)uploadImageSimple(files[0],this);}}};
const snSm={height:150,dialogsInBody:true,dialogsFade:false,container:'body',toolbar:[['style',['bold','italic','underline','clear']],['fontsize',['fontsize']],['color',['color']],['para',['ul','ol','paragraph']],['table',['table']],['insert',['link','picture']]],callbacks:snCfg.callbacks};
function snInit(m){m.querySelectorAll('.summernote').forEach(function(e){if(!$(e).data('summernote'))$(e).summernote(snCfg);});m.querySelectorAll('.summernote-sm').forEach(function(e){if(!$(e).data('summernote'))$(e).summernote(snSm);});hardenSummernoteButtons(m);}
function restoreScrollableTables(){document.querySelectorAll('.cat-table-wrap, .table-responsive').forEach(function(el){el.style.overflowX='auto';el.style.webkitOverflowScrolling='touch';if(el.classList.contains('cat-table-wrap')){el.style.overflowY='hidden';el.style.pointerEvents='auto';el.style.position='relative';el.style.zIndex='1';}});}
function normalizeHiddenModals(){if(document.querySelector('.modal.show, .note-modal.show'))return;document.querySelectorAll('.modal, .note-modal').forEach(function(modal){modal.classList.remove('show');modal.setAttribute('aria-hidden','true');modal.removeAttribute('aria-modal');modal.style.removeProperty('display');modal.style.removeProperty('padding-right');});document.querySelectorAll('.modal-dialog, .modal-content').forEach(function(el){el.style.removeProperty('transform');el.style.removeProperty('padding-right');});}
var _pageScrollObserver=null;function resetPageScrollState(force){if(_pageScrollObserver){_pageScrollObserver.disconnect();_pageScrollObserver=null;}var cleanup=function(){if(!force&&document.querySelector('.modal.show'))return;document.body.classList.remove('modal-open');document.documentElement.classList.remove('modal-open');['overflow','overflow-y','padding-right','position','top','width'].forEach(function(p){document.body.style.removeProperty(p);document.documentElement.style.removeProperty(p);});document.body.style.overflow='auto';document.documentElement.style.overflow='auto';document.body.style.pointerEvents='';document.documentElement.style.pointerEvents='';document.querySelectorAll('.modal-backdrop, .note-modal-backdrop').forEach(function(el){el.remove();});normalizeHiddenModals();restoreScrollableTables();};cleanup();_pageScrollObserver=new MutationObserver(function(){cleanup();});_pageScrollObserver.observe(document.body,{attributes:true,attributeFilter:['style','class']});setTimeout(function(){cleanup();if(_pageScrollObserver){_pageScrollObserver.disconnect();_pageScrollObserver=null;}},1500);}
function destroySummernote(ids){ids.forEach(function(id){var $el=$(id);if($el.length&&$el.data('summernote')){try{$el.summernote('destroy');}catch(e){}$el.siblings('.note-editor').remove();$el.show();}});resetPageScrollState(false);}
function snDestroy(m){m.querySelectorAll('.summernote,.summernote-sm').forEach(function(e){if($(e).data('summernote'))$(e).summernote('destroy');});resetPageScrollState(false);}
function initSummernoteAddCBTG(){destroySummernote(['#pertanyaan_tambah_cbt_g','#pilihan_a_tambah_cbt_g','#pilihan_b_tambah_cbt_g','#pilihan_c_tambah_cbt_g','#pilihan_d_tambah_cbt_g','#pilihan_e_tambah_cbt_g','#pembahasan_tambah_cbt_g']);setTimeout(function(){$('#pertanyaan_tambah_cbt_g').summernote(snCfg);$('#pilihan_a_tambah_cbt_g,#pilihan_b_tambah_cbt_g,#pilihan_c_tambah_cbt_g,#pilihan_d_tambah_cbt_g,#pilihan_e_tambah_cbt_g').summernote(snSm);$('#pembahasan_tambah_cbt_g').summernote(snCfg);},150);}
function initSummernoteEditCBTG(soalId){var ids=['#pertanyaan_edit_cbt_g_'+soalId,'#pilihan_a_edit_cbt_g_'+soalId,'#pilihan_b_edit_cbt_g_'+soalId,'#pilihan_c_edit_cbt_g_'+soalId,'#pilihan_d_edit_cbt_g_'+soalId,'#pilihan_e_edit_cbt_g_'+soalId,'#pembahasan_edit_cbt_g_'+soalId];destroySummernote(ids);setTimeout(function(){$('#pertanyaan_edit_cbt_g_'+soalId).summernote(snCfg);$('#pilihan_a_edit_cbt_g_'+soalId+',#pilihan_b_edit_cbt_g_'+soalId+',#pilihan_c_edit_cbt_g_'+soalId+',#pilihan_d_edit_cbt_g_'+soalId+',#pilihan_e_edit_cbt_g_'+soalId).summernote(snSm);$('#pembahasan_edit_cbt_g_'+soalId).summernote(snCfg);},150);}
function initSummernoteAddCATGuru(){destroySummernote(['#pertanyaan_cat_g_add','#pilihan_a_cat_g_add','#pilihan_b_cat_g_add','#pilihan_c_cat_g_add','#pilihan_d_cat_g_add','#pilihan_e_cat_g_add','#pembahasan_cat_g_add']);setTimeout(function(){$('#pertanyaan_cat_g_add').summernote(snCfg);$('#pilihan_a_cat_g_add,#pilihan_b_cat_g_add,#pilihan_c_cat_g_add,#pilihan_d_cat_g_add,#pilihan_e_cat_g_add').summernote(snSm);$('#pembahasan_cat_g_add').summernote(snCfg);},150);}
function initSummernoteEditCATGuru(soalId){var ids=['#pertanyaan_cat_g_edit_'+soalId,'#pilihan_a_cat_g_edit_'+soalId,'#pilihan_b_cat_g_edit_'+soalId,'#pilihan_c_cat_g_edit_'+soalId,'#pilihan_d_cat_g_edit_'+soalId,'#pilihan_e_cat_g_edit_'+soalId,'#pembahasan_cat_g_edit_'+soalId];destroySummernote(ids);setTimeout(function(){$('#pertanyaan_cat_g_edit_'+soalId).summernote(snCfg);$('#pilihan_a_cat_g_edit_'+soalId+',#pilihan_b_cat_g_edit_'+soalId+',#pilihan_c_cat_g_edit_'+soalId+',#pilihan_d_cat_g_edit_'+soalId+',#pilihan_e_cat_g_edit_'+soalId).summernote(snSm);$('#pembahasan_cat_g_edit_'+soalId).summernote(snCfg);},150);}
document.getElementById('modalTambahSoalCBTGuru')?.addEventListener('shown.bs.modal',function(){initSummernoteAddCBTG();});
document.getElementById('modalTambahSoalCBTGuru')?.addEventListener('hidden.bs.modal',function(){destroySummernote(['#pertanyaan_tambah_cbt_g','#pilihan_a_tambah_cbt_g','#pilihan_b_tambah_cbt_g','#pilihan_c_tambah_cbt_g','#pilihan_d_tambah_cbt_g','#pilihan_e_tambah_cbt_g','#pembahasan_tambah_cbt_g']);resetPageScrollState(true);});
document.getElementById('tambahSoalModalGuru')?.addEventListener('shown.bs.modal',function(){initSummernoteAddCATGuru();});
document.getElementById('tambahSoalModalGuru')?.addEventListener('hidden.bs.modal',function(){destroySummernote(['#pertanyaan_cat_g_add','#pilihan_a_cat_g_add','#pilihan_b_cat_g_add','#pilihan_c_cat_g_add','#pilihan_d_cat_g_add','#pilihan_e_cat_g_add','#pembahasan_cat_g_add']);resetPageScrollState(true);});
document.addEventListener('shown.bs.modal',function(e){if(e.target.classList.contains('note-modal'))return;if(e.target.id&&e.target.id.startsWith('modalEditSoalGuru')){initSummernoteEditCBTG(e.target.id.replace('modalEditSoalGuru',''));return;}if(e.target.id&&e.target.id.startsWith('editSoalCatGuru')){initSummernoteEditCATGuru(e.target.id.replace('editSoalCatGuru',''));return;}});
document.addEventListener('hidden.bs.modal',function(e){if(e.target.classList.contains('note-modal'))return;if(e.target.id&&e.target.id.startsWith('modalEditSoalGuru')){var id=e.target.id.replace('modalEditSoalGuru','');destroySummernote(['#pertanyaan_edit_cbt_g_'+id,'#pilihan_a_edit_cbt_g_'+id,'#pilihan_b_edit_cbt_g_'+id,'#pilihan_c_edit_cbt_g_'+id,'#pilihan_d_edit_cbt_g_'+id,'#pilihan_e_edit_cbt_g_'+id,'#pembahasan_edit_cbt_g_'+id]);resetPageScrollState(true);return;}if(e.target.id&&e.target.id.startsWith('editSoalCatGuru')){var id=e.target.id.replace('editSoalCatGuru','');destroySummernote(['#pertanyaan_cat_g_edit_'+id,'#pilihan_a_cat_g_edit_'+id,'#pilihan_b_cat_g_edit_'+id,'#pilihan_c_cat_g_edit_'+id,'#pilihan_d_cat_g_edit_'+id,'#pilihan_e_cat_g_edit_'+id,'#pembahasan_cat_g_edit_'+id]);resetPageScrollState(true);return;}});
document.addEventListener('hidden.bs.modal',function(e){if(e.target&&e.target.id==='modalLihatPaketG')return;requestAnimationFrame(function(){requestAnimationFrame(function(){resetPageScrollState(true);});});});
document.addEventListener('show.bs.modal',function(e){if(e.target.classList.contains('note-modal'))return;if(_pageScrollObserver){_pageScrollObserver.disconnect();_pageScrollObserver=null;}});
document.addEventListener('shown.bs.modal',function(e){if(e.target.classList.contains('note-modal'))hardenSummernoteButtons(e.target);});
document.addEventListener('submit',function(e){var submitter=e.submitter;if((submitter&&(submitter.closest('.note-editor')||submitter.closest('.note-modal')))||e.target.classList.contains('note-modal-form')){e.preventDefault();e.stopPropagation();return;}var m=e.target.closest('.modal');if(m){m.querySelectorAll('.summernote,.summernote-sm').forEach(function(el){if($(el).data('summernote'))el.value=$(el).summernote('code');});}},true);

document.getElementById('formTambahSoalCBTGuru')?.addEventListener('submit',function(e){e.preventDefault();const msg=document.getElementById('ajaxMsgCBTG');msg.innerHTML='<div class="alert alert-info py-2 small">Menyimpan...</div>';fetch('<?=base_url('guru/bank-soal/tambah-soal')?>',{method:'POST',headers:{'X-Requested-With':'XMLHttpRequest'},body:new FormData(this)}).then(r=>r.json().then(d=>({ok:r.ok,data:d}))).then(res=>{if(!res.ok||!res.data.success)throw new Error(res.data.message||'Gagal');msg.innerHTML='<div class="alert alert-success py-2 small">'+res.data.message+'</div>';setTimeout(()=>location.reload(),800);}).catch(err=>msg.innerHTML='<div class="alert alert-danger py-2 small">'+err.message+'</div>');});

// Import modal
document.getElementById('modalImportBankSoalGuru')?.addEventListener('shown.bs.modal',function(){fetch('<?=base_url('guru/bank-soal/api/kategori')?>').then(r=>r.json()).then(d=>{const s=document.getElementById('filterKatG');s.innerHTML='<option value="">Pilih Kategori</option>';if(d.status==='success')d.data.forEach(k=>{s.innerHTML+='<option value="'+k+'">'+k.charAt(0).toUpperCase()+k.slice(1)+'</option>';});});});
document.getElementById('filterKatG')?.addEventListener('change',function(){const mp=document.getElementById('filterMapelG');mp.innerHTML='<option value="">Loading...</option>';mp.disabled=!this.value;document.getElementById('filterBankG').innerHTML='<option value="">Pilih dulu</option>';document.getElementById('filterBankG').disabled=true;if(!this.value){mp.innerHTML='<option value="">Pilih dulu</option>';mp.disabled=true;return;}fetch('<?=base_url('guru/bank-soal/api/jenis-ujian')?>?kategori='+encodeURIComponent(this.value)).then(r=>r.json()).then(d=>{mp.innerHTML='<option value="">Pilih Mata Pelajaran</option>';if(d.status==='success')d.data.forEach(j=>{mp.innerHTML+='<option value="'+j.jenis_ujian_id+'">'+j.nama_jenis+'</option>';});mp.disabled=false;});});
document.getElementById('filterMapelG')?.addEventListener('change',function(){const b=document.getElementById('filterBankG');b.innerHTML='<option value="">Loading...</option>';b.disabled=!this.value;document.getElementById('searchBankG').disabled=true;if(!this.value){b.innerHTML='<option value="">Pilih dulu</option>';b.disabled=true;return;}fetch('<?=base_url('guru/bank-soal/api/bank-ujian')?>?kategori='+encodeURIComponent(document.getElementById('filterKatG').value)+'&jenis_ujian_id='+this.value).then(r=>r.json()).then(d=>{b.innerHTML='<option value="">Pilih Bank</option>';if(d.status==='success')d.data.forEach(bk=>{b.innerHTML+='<option value="'+bk.bank_ujian_id+'">'+bk.nama_ujian+' ('+bk.jumlah_soal+' soal)</option>';});b.disabled=false;});});
document.getElementById('filterBankG')?.addEventListener('change',function(){document.getElementById('searchBankG').disabled=!this.value;if(!this.value){document.getElementById('bankContainerG').classList.add('d-none');document.getElementById('noBankMsgG').classList.remove('d-none');return;}document.getElementById('loadingBankG').classList.remove('d-none');document.getElementById('bankContainerG').classList.add('d-none');fetch('<?=base_url('guru/bank-soal/api/soal')?>?bank_ujian_id='+this.value).then(r=>r.json()).then(d=>{document.getElementById('loadingBankG').classList.add('d-none');if(d.status==='success'){const tbody=document.getElementById('bankBodyG');tbody.innerHTML=d.data.map((s,i)=>'<tr><td><input type="checkbox" name="soal_ids[]" value="'+s.soal_id+'" class="check-import-g"></td><td>'+(i+1)+'</td><td>'+s.kode_soal+'</td><td class="text-truncate" style="max-width:200px">'+(s.pertanyaan||'').replace(/<[^>]*>/g,'').substring(0,80)+'</td><td class="text-center fw-bold">'+s.jawaban_benar+'</td><td class="text-center">'+s.tingkat_kesulitan+'</td></tr>').join('');document.getElementById('bankContainerG').classList.remove('d-none');document.getElementById('noBankMsgG').classList.add('d-none');document.querySelectorAll('.check-import-g').forEach(cb=>cb.addEventListener('change',updateImportBtnG));updateImportBtnG();}});});
document.getElementById('searchBankG')?.addEventListener('input',function(){const q=this.value.toLowerCase();document.querySelectorAll('#bankBodyG tr').forEach(tr=>tr.style.display=tr.textContent.toLowerCase().includes(q)?'':'none');});
document.getElementById('selectAllBankG')?.addEventListener('change',function(){document.querySelectorAll('.check-import-g').forEach(cb=>cb.checked=this.checked);updateImportBtnG();});
function updateImportBtnG(){const cnt=document.querySelectorAll('.check-import-g:checked').length;const btn=document.getElementById('btnImportG');if(btn){btn.disabled=cnt===0;btn.innerHTML='<i class="bi bi-download me-1"></i>Import '+(cnt>0?cnt+' Terpilih':'Terpilih');}}
document.getElementById('btnImportG')?.addEventListener('click',function(){if(!confirm('Import soal terpilih?'))return;document.getElementById('formImportG').submit();});

// === Pilih dari Bank (CAT - Guru) ===
document.getElementById('modalPilihDariBankGuru')?.addEventListener('shown.bs.modal',function(){
  const sekolah=document.getElementById('pilihSekolahG');
  const kelas=document.getElementById('pilihKelasG');
  const mapel=document.getElementById('pilihMapelG');
  const bank=document.getElementById('pilihBankG');
  document.getElementById('pilihKatG').value='';
  if(sekolah) sekolah.value='';
  if(kelas){kelas.innerHTML='<option value="">Pilih sekolah dulu</option>';kelas.disabled=true;}
  if(mapel){mapel.innerHTML='<option value="">Pilih kelas dulu</option>';mapel.disabled=true;}
  if(bank){bank.innerHTML='<option value="">Pilih mata pelajaran dulu</option>';bank.disabled=true;}
  document.getElementById('pilihCariG').disabled=true;
});
document.getElementById('pilihSekolahG')?.addEventListener('change',function(){
  const kelas=document.getElementById('pilihKelasG');
  const mapel=document.getElementById('pilihMapelG');
  const bank=document.getElementById('pilihBankG');
  document.getElementById('pilihKatG').value='';
  kelas.innerHTML='<option value="">Loading...</option>';kelas.disabled=!this.value;
  mapel.innerHTML='<option value="">Pilih kelas dulu</option>';mapel.disabled=true;
  bank.innerHTML='<option value="">Pilih mata pelajaran dulu</option>';bank.disabled=true;
  document.getElementById('pilihCariG').disabled=true;
  if(!this.value){kelas.innerHTML='<option value="">Pilih sekolah dulu</option>';kelas.disabled=true;return;}
  if(this.value==='__umum'){
    kelas.innerHTML='<option value="umum" data-kategori="umum">Kelas Umum</option>';
    kelas.value='umum';
    kelas.disabled=true;
    document.getElementById('pilihKatG').value='umum';
    fetch('<?=base_url('guru/bank-soal/api/jenis-ujian')?>?kategori=umum').then(r=>r.json()).then(d=>{
      mapel.innerHTML='<option value="">Pilih Mapel</option>';
      if(d.status==='success')d.data.forEach(j=>{mapel.innerHTML+='<option value="'+j.jenis_ujian_id+'">'+j.nama_jenis+'</option>';});
      mapel.disabled=false;
    }).catch(()=>{mapel.innerHTML='<option value="">Gagal memuat</option>';mapel.disabled=true;});
    return;
  }
  fetch('<?=base_url('guru/api/kelas-by-sekolah/')?>'+encodeURIComponent(this.value)).then(r=>r.json()).then(d=>{
    kelas.innerHTML='<option value="">Pilih Kelas</option><option value="umum" data-kategori="umum">Umum / Tanpa Kelas</option>';
    if(d.status==='success' && Array.isArray(d.data)){d.data.forEach(k=>{kelas.innerHTML+='<option value="'+k.kelas_id+'" data-kategori="'+String(k.nama_kelas).replace(/"/g,'&quot;')+'">'+k.nama_kelas+(k.tahun_ajaran?' - '+k.tahun_ajaran:'')+'</option>';});}
    kelas.disabled=false;
  }).catch(()=>{kelas.innerHTML='<option value="">Gagal memuat kelas</option>';kelas.disabled=true;});
});
document.getElementById('pilihKelasG')?.addEventListener('change',function(){
  const mp=document.getElementById('pilihMapelG');
  const bank=document.getElementById('pilihBankG');
  const kategori=this.options[this.selectedIndex]?.dataset.kategori||'';
  document.getElementById('pilihKatG').value=kategori;
  mp.innerHTML='<option value="">Loading...</option>';mp.disabled=!kategori;
  bank.innerHTML='<option value="">Pilih mata pelajaran dulu</option>';bank.disabled=true;
  document.getElementById('pilihCariG').disabled=true;
  if(!kategori){mp.innerHTML='<option value="">Pilih kelas dulu</option>';mp.disabled=true;return;}
  fetch('<?=base_url('guru/bank-soal/api/jenis-ujian')?>?kategori='+encodeURIComponent(kategori)).then(r=>r.json()).then(d=>{mp.innerHTML='<option value="">Pilih Mapel</option>';if(d.status==='success')d.data.forEach(j=>{mp.innerHTML+='<option value="'+j.jenis_ujian_id+'">'+j.nama_jenis+'</option>';});mp.disabled=false;});
});
document.getElementById('pilihMapelG')?.addEventListener('change',function(){const b=document.getElementById('pilihBankG');b.innerHTML='<option value="">Loading...</option>';b.disabled=!this.value;if(!this.value){b.innerHTML='<option value="">Pilih mata pelajaran dulu</option>';b.disabled=true;return;}fetch('<?=base_url('guru/bank-soal/api/bank-ujian')?>?kategori='+encodeURIComponent(document.getElementById('pilihKatG').value)+'&jenis_ujian_id='+this.value).then(r=>r.json()).then(d=>{b.innerHTML='<option value="">Pilih Bank</option>';if(d.status==='success')d.data.forEach(bk=>{b.innerHTML+='<option value="'+bk.bank_ujian_id+'">'+bk.nama_ujian+' ('+bk.jumlah_soal+' soal)</option>';});b.disabled=false;});});
document.getElementById('pilihBankG')?.addEventListener('change',function(){document.getElementById('pilihCariG').disabled=!this.value;if(!this.value){document.getElementById('pilihContainerG').classList.add('d-none');document.getElementById('pilihKosongG').classList.remove('d-none');return;}document.getElementById('pilihLoadingG').classList.remove('d-none');document.getElementById('pilihContainerG').classList.add('d-none');fetch('<?=base_url('guru/bank-soal/api/soal')?>?bank_ujian_id='+this.value).then(r=>r.json()).then(d=>{document.getElementById('pilihLoadingG').classList.add('d-none');if(d.status==='success'){const tbody=document.getElementById('pilihBodyG');tbody.innerHTML=d.data.map((s,i)=>'<tr><td><input type="checkbox" name="soal_ids[]" value="'+s.soal_id+'" class="check-pilih-g"></td><td>'+(i+1)+'</td><td>'+s.kode_soal+'</td><td class="text-truncate" style="max-width:220px">'+(s.pertanyaan||'').replace(/<[^>]*>/g,'').substring(0,80)+'</td><td class="text-center fw-bold">'+s.jawaban_benar+'</td><td class="text-center">'+s.tingkat_kesulitan+'</td></tr>').join('');document.getElementById('pilihContainerG').classList.remove('d-none');document.getElementById('pilihKosongG').classList.add('d-none');document.querySelectorAll('.check-pilih-g').forEach(cb=>cb.addEventListener('change',updatePilihBtnG));updatePilihBtnG();}});});
document.getElementById('pilihCariG')?.addEventListener('input',function(){const q=this.value.toLowerCase();document.querySelectorAll('#pilihBodyG tr').forEach(tr=>tr.style.display=tr.textContent.toLowerCase().includes(q)?'':'none');});
document.getElementById('pilihAllG')?.addEventListener('change',function(){document.querySelectorAll('.check-pilih-g').forEach(cb=>cb.checked=this.checked);updatePilihBtnG();});
function updatePilihBtnG(){const cnt=document.querySelectorAll('.check-pilih-g:checked').length;const btn=document.getElementById('btnPilihG');if(btn){btn.disabled=cnt===0;btn.innerHTML='<i class="bi bi-link me-1"></i>Tautkan '+(cnt>0?cnt+' Terpilih':'Terpilih');}}
</script>

<?= $this->endSection() ?>
