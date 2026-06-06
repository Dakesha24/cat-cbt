<?= $this->extend('templates/admin/admin_template') ?>
<?= $this->section('title') ?>Biodata Tambahan<?= $this->endSection() ?>
<?= $this->section('content') ?>

<style>
.fb-wrap { max-width: 100%; }

/* Row */
.fb-row {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 13px 18px;
    border-bottom: 1px solid #f3f4f6;
    background: #fff;
    transition: background .08s;
}
.fb-row:last-child { border-bottom: none; }
.fb-row:hover      { background: #fafafa; }

/* Nomor urutan */
.fb-num {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: #f3f4f6;
    font-size: .7rem;
    font-weight: 600;
    color: #6b7280;
    flex-shrink: 0;
}

/* Drag handle — 2×2 dot grid */
.fb-handle {
    width: 10px;
    height: 10px;
    background-image: radial-gradient(circle, #c4c9d4 1.5px, transparent 1.5px);
    background-size: 5px 5px;
    background-repeat: repeat;
    cursor: grab;
    flex-shrink: 0;
    transition: background-image .1s;
}
.fb-handle:hover  { background-image: radial-gradient(circle, #6b7280 1.5px, transparent 1.5px); }
.fb-handle:active { cursor: grabbing; }
.sortable-ghost   { opacity: .25; }
.sortable-drag    { box-shadow: 0 4px 20px rgba(0,0,0,.08); background: #fff; }

/* Main content */
.fb-info  { flex: 1; min-width: 0; }
.fb-name { font-size: .875rem; font-weight: 500; color: #111827; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.fb-sub  { font-size: .75rem; color: #9ca3af; margin-top: 2px; }

/* Action buttons */
.fb-actions { display: flex; gap: 2px; flex-shrink: 0; }
.fb-act {
    display: inline-flex; align-items: center; justify-content: center;
    width: 30px; height: 30px; border-radius: 6px;
    border: none; background: transparent;
    color: #9ca3af; font-size: .8rem; cursor: pointer;
    transition: background .1s, color .1s;
}
.fb-act:hover          { background: #f3f4f6; color: #374151; }
.fb-act.danger:hover   { background: #fef2f2; color: #ef4444; }
.fb-act:disabled       { opacity: .2; cursor: not-allowed; pointer-events: none; }

/* Section label */
.fb-section {
    padding: 8px 18px;
    background: #f9fafb;
    border-bottom: 1px solid #f3f4f6;
    font-size: .7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .07em;
    color: #9ca3af;
}
</style>

<div class="container-fluid py-4">
<div class="fb-wrap">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">Biodata Tambahan</h2>
            <p class="text-muted mb-0">Field di bawah ini tampil di halaman profil siswa</p>
        </div>
        <button type="button" class="btn btn-primary shadow-sm"
                data-bs-toggle="modal" data-bs-target="#modalTambahField">
            <i class="bi bi-plus-lg me-2"></i>Tambah Field
        </button>
    </div>

    <?php foreach (['success','error'] as $t): ?>
        <?php if ($msg = session()->getFlashdata($t)): ?>
            <div class="alert alert-<?= $t ?> alert-dismissible fade show border-0 mb-4" role="alert">
                <?= esc($msg) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>

    <div class="card border-0 shadow-sm overflow-hidden">

        <!-- Header -->
        <div style="padding:14px 18px;border-bottom:1px solid #f3f4f6;display:flex;justify-content:space-between;align-items:center">
            <span style="font-size:.875rem;font-weight:600;color:#374151">Daftar Field</span>
            <span style="font-size:.8rem;color:#9ca3af" id="fieldCountLabel"><?= count($fields) + 5 ?> field total</span>
        </div>

        <!-- Bawaan -->
        <div class="fb-section">Bawaan</div>

        <?php
        $builtins = [
            ['label'=>'NIS / Nomor Peserta', 'tipe'=>'Text',    'tipe_raw'=>'text',  'options'=>[], 'note'=>''],
            ['label'=>'Nama Lengkap',         'tipe'=>'Text',    'tipe_raw'=>'text',  'options'=>[], 'note'=>''],
            ['label'=>'Jenis Kelamin',        'tipe'=>'Pilihan', 'tipe_raw'=>'select','options'=>['Laki-laki','Perempuan'],'note'=>''],
            ['label'=>'Sekolah',              'tipe'=>'Pilihan', 'tipe_raw'=>'select','options'=>[],'note'=>'Opsi diisi otomatis dari daftar sekolah yang terdaftar di sistem.'],
            ['label'=>'Kelas',                'tipe'=>'Pilihan', 'tipe_raw'=>'select','options'=>[],'note'=>'Opsi muncul setelah siswa memilih sekolah terlebih dahulu.'],
        ];
        foreach ($builtins as $idx => $bi):
        ?>
        <div class="fb-row">
            <span style="width:16px;flex-shrink:0"></span>
            <span class="fb-num"><?= $idx + 1 ?></span>
            <div class="fb-info">
                <div class="fb-name"><?= esc($bi['label']) ?></div>
                <div class="fb-sub"><?= $bi['tipe'] ?> &middot; Wajib</div>
            </div>
            <div class="fb-actions">
                <button class="fb-act" title="Pratinjau"
                        onclick="showBuiltinPreview(<?= htmlspecialchars(json_encode($bi), ENT_QUOTES) ?>)">
                    <i class="bi bi-eye"></i>
                </button>
                <button class="fb-act" disabled title="Tidak dapat diedit"><i class="bi bi-pencil"></i></button>
                <button class="fb-act danger" disabled title="Tidak dapat dihapus"><i class="bi bi-trash"></i></button>
            </div>
        </div>
        <?php endforeach; ?>

        <!-- Tambahan -->
        <div class="fb-section">Tambahan</div>

        <div id="fieldList">
            <?php if (empty($fields)): ?>
                <div id="emptyState" style="padding:40px 20px;text-align:center;color:#9ca3af;font-size:.875rem">
                    Belum ada field tambahan.
                    Klik <strong style="color:#6b7280">Tambah Field</strong> untuk memulai.
                </div>
            <?php else: ?>
                <?php foreach ($fields as $i => $field): ?>
                    <?= view('admin/form_builder/_field_item', ['field' => $field, 'nomor' => $i + 6]) ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

</div>
</div>

<!-- Modal Tambah / Edit Field -->
<div class="modal fade" id="modalTambahField" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:460px">
        <div class="modal-content border-0 shadow">
            <div class="modal-header" style="border-bottom:1px solid #f3f4f6;padding:16px 20px">
                <h6 class="modal-title fw-semibold mb-0" id="modalFieldTitle">Tambah Field</h6>
                <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding:20px">
                <input type="hidden" id="editFieldId" value="0">

                <div class="mb-3">
                    <label class="form-label fw-semibold" style="font-size:.8125rem">Label <span class="text-danger">*</span></label>
                    <input type="text" id="fieldLabel" class="form-control form-control-sm" placeholder="Contoh: Asal SMA">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold" style="font-size:.8125rem">Tipe Input <span class="text-danger">*</span></label>
                    <select id="fieldTipe" class="form-select form-select-sm" onchange="onTipeChange()">
                        <option value="text">Text</option>
                        <option value="number">Angka</option>
                        <option value="select">Pilihan (dropdown)</option>
                        <option value="date">Tanggal</option>
                        <option value="textarea">Teks Panjang</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold" style="font-size:.8125rem">Placeholder <span class="text-muted fw-normal">(opsional)</span></label>
                    <input type="text" id="fieldPlaceholder" class="form-control form-control-sm" placeholder="Contoh isian untuk siswa">
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="fieldRequired">
                        <label class="form-check-label" for="fieldRequired" style="font-size:.875rem">Wajib diisi</label>
                    </div>
                </div>

                <div id="opsiSection" style="display:none">
                    <div style="height:1px;background:#f3f4f6;margin:4px 0 16px"></div>
                    <label class="form-label fw-semibold d-block mb-2" style="font-size:.8125rem">
                        Opsi Jawaban <span class="text-danger">*</span>
                    </label>
                    <div id="opsiList" class="mb-2"></div>
                    <div class="input-group input-group-sm">
                        <input type="text" id="newOpsiLabel" class="form-control" placeholder="Ketik opsi lalu tekan Enter atau klik Tambah">
                        <button class="btn btn-outline-secondary" type="button" onclick="submitOpsi()">Tambah</button>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="border-top:1px solid #f3f4f6;padding:12px 20px">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-sm btn-primary" onclick="submitField()">Simpan Field</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Preview -->
<div class="modal fade" id="modalPreview" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:420px">
        <div class="modal-content border-0 shadow">
            <div class="modal-header" style="border-bottom:1px solid #f3f4f6;padding:16px 20px">
                <div>
                    <h6 class="modal-title fw-semibold mb-0">Pratinjau Field</h6>
                    <div style="font-size:.75rem;color:#9ca3af;margin-top:2px">Tampilan dari sudut pandang siswa</div>
                </div>
                <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding:20px">
                <div id="previewContent"></div>
            </div>
            <div class="modal-footer" style="border-top:1px solid #f3f4f6;padding:12px 20px">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script src="<?= base_url('js/sortable.min.js') ?>"></script>
<script>
const BASE_URL = '<?= base_url() ?>';

function onTipeChange() {
    const isSelect = document.getElementById('fieldTipe').value === 'select';
    document.getElementById('opsiSection').style.display = isSelect ? 'block' : 'none';
    if (isSelect) document.getElementById('newOpsiLabel').focus();
}

// ── Submit field ──────────────────────────────────────────────
async function submitField() {
    const label      = document.getElementById('fieldLabel').value.trim();
    const tipe       = document.getElementById('fieldTipe').value;
    const ph         = document.getElementById('fieldPlaceholder').value.trim();
    const isRequired = document.getElementById('fieldRequired').checked ? 1 : 0;
    const fieldId    = parseInt(document.getElementById('editFieldId').value);

    if (!label) { alert('Label field wajib diisi.'); return; }
    if (tipe === 'select' && !document.querySelector('#opsiList .opsi-item')) {
        alert('Tambahkan minimal 1 opsi jawaban.'); return;
    }

    const fd = new FormData();
    fd.append('field_id', fieldId);
    fd.append('label', label);
    fd.append('tipe', tipe);
    fd.append('placeholder', ph);
    fd.append('is_required', isRequired);
    if (fieldId === 0 && tipe === 'select') {
        fd.append('pending_options', JSON.stringify(pendingOptions.map(o => o.label)));
    }
    fd.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

    const res  = await fetch(BASE_URL + 'admin/form-builder/field/simpan', { method:'POST', body:fd });
    const json = await res.json();
    if (!json.success) { alert(json.message || 'Gagal menyimpan.'); return; }

    pendingOptions = [];
    renderField(json.field);
    updateCount();
    initSortable();
    bootstrap.Modal.getInstance(document.getElementById('modalTambahField')).hide();
}

// ── Render field row ──────────────────────────────────────────
function renderField(field) {
    const existing = document.getElementById('field-' + field.field_id);
    const html = buildFieldHtml(field);
    if (existing) { existing.outerHTML = html; }
    else {
        document.getElementById('emptyState')?.remove();
        document.getElementById('fieldList').insertAdjacentHTML('beforeend', html);
    }
}

function buildFieldHtml(field) {
    const tipeLabels = { text:'Text', number:'Angka', select:'Pilihan', date:'Tanggal', textarea:'Teks Panjang' };
    const req = field.is_required == 1 ? 'Wajib' : 'Opsional';

    let optSpans = '';
    if (field.tipe === 'select' && field.options?.length) {
        optSpans = field.options.map(o =>
            `<span data-option-id="${o.option_id}" style="display:none">${escHtml(o.label)}</span>`
        ).join('');
    }

    const fj = escAttr(JSON.stringify({
        label: field.label, tipe: field.tipe, tipe_raw: field.tipe,
        options: field.options ? field.options.map(o => o.label) : []
    }));

    const existingCount = document.querySelectorAll('#fieldList .fb-row[data-id]').length;
    const num = existingCount + 6;

    return `<div class="fb-row" id="field-${field.field_id}"
        data-id="${field.field_id}" data-label="${escAttr(field.label)}"
        data-tipe="${field.tipe}" data-placeholder="${escAttr(field.placeholder ?? '')}"
        data-required="${field.is_required == 1 ? '1' : '0'}" data-field-json="${fj}">
        ${optSpans}
        <div class="fb-handle" title="Ubah urutan"></div>
        <span class="fb-num" style="min-width:22px">${num}</span>
        <div class="fb-info">
            <div class="fb-name">${escHtml(field.label)}</div>
            <div class="fb-sub">${tipeLabels[field.tipe] ?? field.tipe} &middot; ${req}</div>
        </div>
        <div class="fb-actions">
            <button class="fb-act" title="Pratinjau" onclick="showFieldPreview(${field.field_id})"><i class="bi bi-eye"></i></button>
            <button class="fb-act" title="Edit" onclick="editField(${field.field_id})"><i class="bi bi-pencil"></i></button>
            <button class="fb-act danger" title="Hapus" onclick="hapusField(${field.field_id})"><i class="bi bi-trash"></i></button>
        </div>
    </div>`;
}

// ── Edit ──────────────────────────────────────────────────────
function editField(fieldId) {
    const card = document.getElementById('field-' + fieldId);
    if (!card) return;
    document.getElementById('editFieldId').value          = fieldId;
    document.getElementById('modalFieldTitle').textContent = 'Edit Field';
    document.getElementById('fieldLabel').value            = card.dataset.label ?? '';
    document.getElementById('fieldTipe').value             = card.dataset.tipe  ?? 'text';
    document.getElementById('fieldPlaceholder').value      = card.dataset.placeholder ?? '';
    document.getElementById('fieldRequired').checked       = card.dataset.required === '1';
    onTipeChange();
    if (card.dataset.tipe === 'select') {
        document.getElementById('opsiList').innerHTML = '';
        card.querySelectorAll('[data-option-id]').forEach(s =>
            renderOpsi({ option_id: s.dataset.optionId, label: s.textContent.trim() })
        );
    }
    bootstrap.Modal.getOrCreateInstance(document.getElementById('modalTambahField')).show();
}

// ── Hapus ─────────────────────────────────────────────────────
async function hapusField(fieldId) {
    if (!confirm('Hapus field ini? Data jawaban siswa untuk field ini ikut terhapus.')) return;
    const fd = new FormData();
    fd.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
    const res  = await fetch(BASE_URL + 'admin/form-builder/field/hapus/' + fieldId, { method:'POST', body:fd });
    const json = await res.json();
    if (json.success) {
        document.getElementById('field-' + fieldId)?.remove();
        renumberFields();
        updateCount();
        if (!document.querySelector('#fieldList .fb-row')) {
            document.getElementById('fieldList').innerHTML =
                '<div id="emptyState" style="padding:40px 20px;text-align:center;color:#9ca3af;font-size:.875rem">Belum ada field tambahan.</div>';
        }
    }
}

// ── Preview ───────────────────────────────────────────────────
function showFieldPreview(fieldId) {
    const card = document.getElementById('field-' + fieldId);
    if (!card) return;
    const options = [];
    card.querySelectorAll('[data-option-id]').forEach(s => options.push(s.textContent.trim()));
    renderPreviewModal(card.dataset.label, card.dataset.tipe, card.dataset.placeholder, card.dataset.required === '1', options, '');
    bootstrap.Modal.getOrCreateInstance(document.getElementById('modalPreview')).show();
}

function showBuiltinPreview(field) {
    renderPreviewModal(field.label, field.tipe_raw, '', true, field.options, field.note ?? '');
    bootstrap.Modal.getOrCreateInstance(document.getElementById('modalPreview')).show();
}

function renderPreviewModal(label, tipe, placeholder, isRequired, options, note) {
    let inputHtml = '';
    const ph = placeholder || 'Isi di sini...';

    if (tipe === 'text')     inputHtml = `<input type="text" class="form-control form-control-sm" placeholder="${escHtml(ph)}" disabled>`;
    else if (tipe === 'number')   inputHtml = `<input type="number" class="form-control form-control-sm" placeholder="0" disabled>`;
    else if (tipe === 'date')     inputHtml = `<input type="date" class="form-control form-control-sm" disabled>`;
    else if (tipe === 'textarea') inputHtml = `<textarea class="form-control form-control-sm" rows="3" placeholder="${escHtml(ph)}" disabled></textarea>`;
    else if (tipe === 'select') {
        if (options.length) {
            inputHtml = `<select class="form-select form-select-sm" disabled>
                <option>-- Pilih --</option>
                ${options.map(o => `<option>${escHtml(o)}</option>`).join('')}
            </select>`;
        } else if (note) {
            inputHtml = `<select class="form-select form-select-sm" disabled><option>-- Pilih --</option></select>
                <div class="mt-2 p-2 rounded" style="background:#f0f4ff;font-size:.8125rem;color:#3b82f6">
                    <i class="bi bi-info-circle me-1"></i>${escHtml(note)}
                </div>`;
        } else {
            inputHtml = `<select class="form-select form-select-sm" disabled><option>-- Belum ada opsi --</option></select>`;
        }
    }

    document.getElementById('previewContent').innerHTML = `
        <label class="form-label fw-semibold" style="font-size:.875rem">
            ${escHtml(label)}${isRequired ? ' <span class="text-danger">*</span>' : ''}
        </label>
        ${inputHtml}
        ${options.length && tipe === 'select' ? `
            <div class="mt-3">
                <div style="font-size:.72rem;color:#9ca3af;font-weight:600;text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px">Opsi tersedia</div>
                <div style="display:flex;flex-wrap:wrap;gap:5px">
                    ${options.map(o => `<span style="font-size:.8125rem;background:#f9fafb;border:1px solid #e5e7eb;padding:3px 10px;border-radius:5px;color:#374151">${escHtml(o)}</span>`).join('')}
                </div>
            </div>` : ''}
    `;
}

// ── Opsi ──────────────────────────────────────────────────────
let pendingOptions = [];

function clearOpsiInput() { document.getElementById('newOpsiLabel').value = ''; }

async function submitOpsi() {
    const label   = document.getElementById('newOpsiLabel').value.trim();
    const fieldId = parseInt(document.getElementById('editFieldId').value);
    if (!label) { alert('Label opsi tidak boleh kosong.'); return; }

    if (fieldId > 0) {
        const fd = new FormData();
        fd.append('field_id', fieldId);
        fd.append('label', label);
        fd.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
        const res  = await fetch(BASE_URL + 'admin/form-builder/opsi/simpan', { method:'POST', body:fd });
        const json = await res.json();
        if (json.success) { renderOpsi(json.option); clearOpsiInput(); }
    } else {
        const tempId = 'tmp_' + Date.now();
        pendingOptions.push({ option_id: tempId, label });
        renderOpsi({ option_id: tempId, label });
        clearOpsiInput();
    }
}

function renderOpsi(option) {
    document.getElementById('opsiList').insertAdjacentHTML('beforeend',
        `<div class="opsi-item d-flex align-items-center gap-2 mb-1 px-2 py-2 rounded"
              style="background:#f9fafb;border:1px solid #f3f4f6" id="opsi-${option.option_id}">
            <span style="flex:1;font-size:.875rem;color:#374151">${escHtml(option.label)}</span>
            <button onclick="hapusOpsi('${option.option_id}')"
                style="width:22px;height:22px;border:none;background:transparent;color:#9ca3af;cursor:pointer;font-size:.8rem;display:flex;align-items:center;justify-content:center;border-radius:4px"
                onmouseover="this.style.background='#fee2e2';this.style.color='#ef4444'"
                onmouseout="this.style.background='transparent';this.style.color='#9ca3af'">
                <i class="bi bi-x"></i>
            </button>
        </div>`
    );
}

async function hapusOpsi(optionId) {
    if (String(optionId).startsWith('tmp_')) {
        pendingOptions = pendingOptions.filter(o => String(o.option_id) !== String(optionId));
        document.getElementById('opsi-' + optionId)?.remove();
    } else {
        const fd = new FormData();
        fd.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
        const res  = await fetch(BASE_URL + 'admin/form-builder/opsi/hapus/' + optionId, { method:'POST', body:fd });
        const json = await res.json();
        if (json.success) document.getElementById('opsi-' + optionId)?.remove();
    }
}

// ── Helpers ───────────────────────────────────────────────────
function renumberFields() {
    document.querySelectorAll('#fieldList .fb-row[data-id]').forEach((row, i) => {
        const numEl = row.querySelector('.fb-num');
        if (numEl) numEl.textContent = i + 6;
    });
}

function updateCount() {
    const n = document.querySelectorAll('#fieldList .fb-row').length;
    document.getElementById('fieldCountLabel').textContent = (n + 5) + ' field total';
}

function escHtml(s)  { return String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
function escAttr(s)  { return String(s ?? '').replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/'/g,'&#39;'); }

document.getElementById('newOpsiLabel').addEventListener('keydown', e => {
    if (e.key === 'Enter') { e.preventDefault(); submitOpsi(); }
});

document.getElementById('modalTambahField').addEventListener('hidden.bs.modal', () => {
    document.getElementById('editFieldId').value           = '0';
    document.getElementById('fieldLabel').value             = '';
    document.getElementById('fieldTipe').value              = 'text';
    document.getElementById('fieldPlaceholder').value       = '';
    document.getElementById('fieldRequired').checked        = false;
    document.getElementById('opsiSection').style.display    = 'none';
    document.getElementById('opsiList').innerHTML            = '';
    document.getElementById('modalFieldTitle').textContent   = 'Tambah Field';
    pendingOptions = [];
    clearOpsiInput();
});

// ── Drag & Drop ───────────────────────────────────────────────
let sortableInstance = null;

function initSortable() {
    const list = document.getElementById('fieldList');
    if (!list || typeof Sortable === 'undefined') return;
    if (sortableInstance) sortableInstance.destroy();
    sortableInstance = Sortable.create(list, {
        handle: '.fb-handle',
        animation: 120,
        ghostClass: 'sortable-ghost',
        dragClass: 'sortable-drag',
        onEnd: async () => {
            renumberFields();
            const ids = Array.from(list.querySelectorAll('.fb-row[data-id]')).map(r => r.dataset.id);
            if (!ids.length) return;
            const fd = new FormData();
            ids.forEach(id => fd.append('urutan[]', id));
            fd.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
            await fetch(BASE_URL + 'admin/form-builder/field/urutan', { method:'POST', body:fd });
        }
    });
}

document.addEventListener('DOMContentLoaded', initSortable);
</script>

<?= $this->endSection() ?>
