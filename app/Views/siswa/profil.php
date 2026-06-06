<?= $this->extend('templates/siswa/siswa_template') ?>
<?= $this->section('content') ?>

<style>
.profil-wrap { background: #f4f6f9; min-height: calc(100vh - 60px); padding: 28px 0 48px; }
.profil-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; margin-bottom: 20px; }
.profil-card-head { padding: 14px 24px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; gap: 10px; }
.profil-card-head h6 { margin: 0; font-size: .8125rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #64748b; }
.profil-card-body { padding: 24px; }
.form-label { font-size: .875rem; font-weight: 600; color: #374151; margin-bottom: 5px; }
.form-control, .form-select { font-size: .9rem; border-color: #e2e8f0; border-radius: 7px; padding: .5rem .75rem; }
.form-control:focus, .form-select:focus { border-color: #93c5fd; box-shadow: 0 0 0 3px rgba(59,130,246,.1); }
.form-control:disabled, .form-select:disabled { background: #f9fafb; color: #9ca3af; }
</style>

<div class="profil-wrap">
<div class="container-fluid px-3 px-md-4" style="max-width:860px">

    <?php if ($notice = session()->getFlashdata('profil_incomplete')): ?>
        <div class="alert border-0 d-flex align-items-start gap-3 mb-4"
             style="background:#fff7ed;border:1px solid #fed7aa;border-radius:8px">
            <i class="bi bi-exclamation-circle-fill" style="color:#f97316;flex-shrink:0;margin-top:2px"></i>
            <div>
                <div class="fw-semibold" style="color:#c2410c;font-size:.875rem">Profil Belum Lengkap</div>
                <div style="color:#9a3412;font-size:.8125rem;margin-top:2px"><?= esc($notice) ?></div>
            </div>
        </div>
    <?php endif; ?>

    <?php foreach (['error','success'] as $t): ?>
        <?php if ($msg = session()->getFlashdata($t)): ?>
            <div class="alert alert-<?= $t ?> alert-dismissible fade show border-0 mb-4" role="alert">
                <?= esc($msg) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>

    <?php $errors = session()->getFlashdata('errors') ?? []; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">Profil Saya</h2>
            <p class="text-muted mb-0">Lengkapi data diri Anda sebelum mengikuti ujian</p>
        </div>
    </div>

    <form action="<?= base_url('siswa/profil/save') ?>" method="POST">
        <?= csrf_field() ?>

        <!-- Data Utama -->
        <div class="profil-card">
            <div class="profil-card-head">
                <i class="bi bi-person" style="color:#64748b"></i>
                <h6>Data Utama</h6>
            </div>
            <div class="profil-card-body">
                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">NIS <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control <?= isset($errors['nomor_peserta']) ? 'is-invalid' : '' ?>"
                               name="nomor_peserta"
                               value="<?= esc(old('nomor_peserta', $siswa['nomor_peserta'] ?? '')) ?>"
                               placeholder="Nomor Induk Siswa" required>
                        <?php if (isset($errors['nomor_peserta'])): ?>
                            <div class="invalid-feedback"><?= esc($errors['nomor_peserta']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                        <select class="form-select <?= isset($errors['jenis_kelamin']) ? 'is-invalid' : '' ?>"
                                name="jenis_kelamin" required>
                            <option value="">-- Pilih --</option>
                            <option value="Laki-laki" <?= old('jenis_kelamin', $siswa['jenis_kelamin'] ?? '') === 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                            <option value="Perempuan" <?= old('jenis_kelamin', $siswa['jenis_kelamin'] ?? '') === 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
                        </select>
                        <?php if (isset($errors['jenis_kelamin'])): ?>
                            <div class="invalid-feedback"><?= esc($errors['jenis_kelamin']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control <?= isset($errors['nama_lengkap']) ? 'is-invalid' : '' ?>"
                               name="nama_lengkap"
                               value="<?= esc(old('nama_lengkap', $siswa['nama_lengkap'] ?? '')) ?>"
                               placeholder="Nama lengkap sesuai dokumen" required>
                        <?php if (isset($errors['nama_lengkap'])): ?>
                            <div class="invalid-feedback"><?= esc($errors['nama_lengkap']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Sekolah <span class="text-danger">*</span></label>
                        <select class="form-select <?= isset($errors['sekolah_id']) ? 'is-invalid' : '' ?>"
                                id="sekolah_id" name="sekolah_id" required>
                            <option value="">-- Pilih Sekolah --</option>
                            <?php foreach ($sekolah as $s): ?>
                                <option value="<?= $s['sekolah_id'] ?>"
                                    <?= old('sekolah_id', $siswa['sekolah_id'] ?? '') == $s['sekolah_id'] ? 'selected' : '' ?>>
                                    <?= esc($s['nama_sekolah']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['sekolah_id'])): ?>
                            <div class="invalid-feedback"><?= esc($errors['sekolah_id']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Kelas <span class="text-danger">*</span></label>
                        <div class="position-relative">
                            <select class="form-select <?= isset($errors['kelas_id']) ? 'is-invalid' : '' ?>"
                                    id="kelas_id" name="kelas_id" required disabled>
                                <option value="">Pilih sekolah terlebih dahulu</option>
                            </select>
                            <div class="spinner-border spinner-border-sm d-none position-absolute"
                                 id="kelas-loading" role="status"
                                 style="right:36px;top:50%;transform:translateY(-50%)">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                        <?php if (isset($errors['kelas_id'])): ?>
                            <div class="invalid-feedback d-block"><?= esc($errors['kelas_id']) ?></div>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </div>

        <!-- Biodata Tambahan -->
        <?php if (!empty($formFields)): ?>
        <div class="profil-card">
            <div class="profil-card-head">
                <i class="bi bi-list-check" style="color:#64748b"></i>
                <h6><?= esc($formTemplate['nama']) ?></h6>
            </div>
            <div class="profil-card-body">
                <div class="row g-3">
                    <?php foreach ($formFields as $field):
                        $fieldName  = 'form_field[' . $field['field_id'] . ']';
                        $fieldValue = old($fieldName, $formValues[$field['field_id']] ?? '');
                        $isRequired = (bool) $field['is_required'];
                        $errKey     = 'form_field_' . $field['field_id'];
                        $colClass   = in_array($field['tipe'], ['textarea']) ? 'col-12' : 'col-md-6';
                    ?>
                    <div class="<?= $colClass ?>">
                        <label class="form-label">
                            <?= esc($field['label']) ?>
                            <?php if ($isRequired): ?><span class="text-danger">*</span><?php endif; ?>
                        </label>

                        <?php if ($field['tipe'] === 'text'): ?>
                            <input type="text"
                                   class="form-control <?= isset($errors[$errKey]) ? 'is-invalid' : '' ?>"
                                   name="<?= $fieldName ?>"
                                   value="<?= esc($fieldValue) ?>"
                                   placeholder="<?= esc($field['placeholder'] ?? '') ?>"
                                   <?= $isRequired ? 'required' : '' ?>>

                        <?php elseif ($field['tipe'] === 'number'): ?>
                            <input type="number"
                                   class="form-control <?= isset($errors[$errKey]) ? 'is-invalid' : '' ?>"
                                   name="<?= $fieldName ?>"
                                   value="<?= esc($fieldValue) ?>"
                                   placeholder="<?= esc($field['placeholder'] ?? '') ?>"
                                   <?= $isRequired ? 'required' : '' ?>>

                        <?php elseif ($field['tipe'] === 'date'): ?>
                            <input type="date"
                                   class="form-control <?= isset($errors[$errKey]) ? 'is-invalid' : '' ?>"
                                   name="<?= $fieldName ?>"
                                   value="<?= esc($fieldValue) ?>"
                                   <?= $isRequired ? 'required' : '' ?>>

                        <?php elseif ($field['tipe'] === 'textarea'): ?>
                            <textarea class="form-control <?= isset($errors[$errKey]) ? 'is-invalid' : '' ?>"
                                      name="<?= $fieldName ?>"
                                      rows="3"
                                      placeholder="<?= esc($field['placeholder'] ?? '') ?>"
                                      <?= $isRequired ? 'required' : '' ?>><?= esc($fieldValue) ?></textarea>

                        <?php elseif ($field['tipe'] === 'select'): ?>
                            <select class="form-select <?= isset($errors[$errKey]) ? 'is-invalid' : '' ?>"
                                    name="<?= $fieldName ?>"
                                    <?= $isRequired ? 'required' : '' ?>>
                                <option value="">-- Pilih --</option>
                                <?php foreach ($field['options'] as $opt): ?>
                                    <option value="<?= esc($opt['label']) ?>"
                                        <?= $fieldValue === $opt['label'] ? 'selected' : '' ?>>
                                        <?= esc($opt['label']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        <?php endif; ?>

                        <?php if (isset($errors[$errKey])): ?>
                            <div class="invalid-feedback d-block"><?= esc($errors[$errKey]) ?></div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Simpan -->
        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary px-5 shadow-sm">
                <i class="bi bi-check-lg me-2"></i>Simpan Profil
            </button>
        </div>

    </form>

</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const sekolahSelect    = document.getElementById('sekolah_id');
    const kelasSelect      = document.getElementById('kelas_id');
    const kelasLoading     = document.getElementById('kelas-loading');
    const currentSekolahId = '<?= esc(old('sekolah_id', $siswa['sekolah_id'] ?? '')) ?>';
    const currentKelasId   = '<?= esc(old('kelas_id',   $siswa['kelas_id']   ?? '')) ?>';

    sekolahSelect.addEventListener('change', function () {
        this.value ? loadKelas(this.value) : resetKelas();
    });

    if (currentSekolahId) loadKelas(currentSekolahId, currentKelasId);

    function loadKelas(sekolahId, selectedId = null) {
        kelasLoading.classList.remove('d-none');
        kelasSelect.disabled = true;
        kelasSelect.innerHTML = '<option value="">Memuat...</option>';

        fetch(`<?= base_url('siswa/api/kelas-by-sekolah') ?>/${sekolahId}`)
            .then(r => r.json())
            .then(data => {
                kelasLoading.classList.add('d-none');
                kelasSelect.disabled = false;
                if (data.success) {
                    kelasSelect.innerHTML = '<option value="">Pilih Kelas</option>';
                    data.kelas.forEach(k => {
                        const o = document.createElement('option');
                        o.value = k.kelas_id;
                        o.textContent = k.nama_kelas;
                        if (selectedId && selectedId == k.kelas_id) o.selected = true;
                        kelasSelect.appendChild(o);
                    });
                } else {
                    kelasSelect.innerHTML = '<option value="">Tidak ada kelas</option>';
                }
            })
            .catch(() => {
                kelasLoading.classList.add('d-none');
                kelasSelect.disabled = false;
                kelasSelect.innerHTML = '<option value="">Gagal memuat kelas</option>';
            });
    }

    function resetKelas() {
        kelasSelect.disabled = true;
        kelasSelect.innerHTML = '<option value="">Pilih sekolah terlebih dahulu</option>';
    }
});
</script>

<?= $this->endSection() ?>
