<?= $this->extend('templates/siswa/siswa_template') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h2>Profil Siswa</h2>
        </div>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Data Siswa</h5>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('siswa/profil/save') ?>" method="POST">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label for="nomor_peserta" class="form-label">NIS <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nomor_peserta" name="nomor_peserta"
                                value="<?= old('nomor_peserta', isset($siswa['nomor_peserta']) ? $siswa['nomor_peserta'] : '') ?>" required>
                            <?php if (session()->getFlashdata('errors')): ?>
                                <div class="invalid-feedback d-block">
                                    <?= session()->getFlashdata('errors')['nomor_peserta'] ?? '' ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="nama_lengkap" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap"
                                value="<?= old('nama_lengkap', isset($siswa['nama_lengkap']) ? $siswa['nama_lengkap'] : '') ?>" required>
                            <?php if (session()->getFlashdata('errors')): ?>
                                <div class="invalid-feedback d-block">
                                    <?= session()->getFlashdata('errors')['nama_lengkap'] ?? '' ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="jenis_kelamin" class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                            <select class="form-select" id="jenis_kelamin" name="jenis_kelamin" required>
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="Laki-laki"
                                    <?= old('jenis_kelamin', isset($siswa['jenis_kelamin']) ? $siswa['jenis_kelamin'] : '') == 'Laki-laki' ? 'selected' : '' ?>>
                                    Laki-laki
                                </option>
                                <option value="Perempuan"
                                    <?= old('jenis_kelamin', isset($siswa['jenis_kelamin']) ? $siswa['jenis_kelamin'] : '') == 'Perempuan' ? 'selected' : '' ?>>
                                    Perempuan
                                </option>
                            </select>
                            <?php if (session()->getFlashdata('errors')): ?>
                                <div class="invalid-feedback d-block">
                                    <?= session()->getFlashdata('errors')['jenis_kelamin'] ?? '' ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="sekolah_id" class="form-label">Sekolah <span class="text-danger">*</span></label>
                            <select class="form-select" id="sekolah_id" name="sekolah_id" required>
                                <option value="">Pilih Sekolah</option>
                                <?php foreach ($sekolah as $s): ?>
                                    <option value="<?= $s['sekolah_id'] ?>"
                                        <?= old('sekolah_id', isset($siswa['sekolah_id']) ? $siswa['sekolah_id'] : '') == $s['sekolah_id'] ? 'selected' : '' ?>>
                                        <?= $s['nama_sekolah'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (session()->getFlashdata('errors')): ?>
                                <div class="invalid-feedback d-block">
                                    <?= session()->getFlashdata('errors')['sekolah_id'] ?? '' ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="kelas_id" class="form-label">Kelas <span class="text-danger">*</span></label>
                            <select class="form-select" id="kelas_id" name="kelas_id" required disabled>
                                <option value="">Pilih Sekolah Terlebih Dahulu</option>
                            </select>
                            <div class="spinner-border spinner-border-sm d-none" id="kelas-loading" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <?php if (session()->getFlashdata('errors')): ?>
                                <div class="invalid-feedback d-block">
                                    <?= session()->getFlashdata('errors')['kelas_id'] ?? '' ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i> Simpan Profil
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sekolahSelect = document.getElementById('sekolah_id');
        const kelasSelect = document.getElementById('kelas_id');
        const kelasLoading = document.getElementById('kelas-loading');

        // Current values for edit mode
        const currentSekolahId = '<?= old('sekolah_id', isset($siswa['sekolah_id']) ? $siswa['sekolah_id'] : '') ?>';
        const currentKelasId = '<?= old('kelas_id', isset($siswa['kelas_id']) ? $siswa['kelas_id'] : '') ?>';

        // Load kelas when sekolah changes
        sekolahSelect.addEventListener('change', function() {
            const sekolahId = this.value;

            if (sekolahId) {
                loadKelas(sekolahId);
            } else {
                resetKelasSelect();
            }
        });

        // Load kelas on page load if sekolah already selected
        if (currentSekolahId) {
            loadKelas(currentSekolahId, currentKelasId);
        }

        function loadKelas(sekolahId, selectedKelasId = null) {
            kelasLoading.classList.remove('d-none');
            kelasSelect.disabled = true;
            kelasSelect.innerHTML = '<option value="">Loading...</option>';

            fetch(`<?= base_url('siswa/api/kelas-by-sekolah') ?>/${sekolahId}`)
                .then(response => response.json())
                .then(data => {
                    kelasLoading.classList.add('d-none');
                    kelasSelect.disabled = false;

                    if (data.success) {
                        kelasSelect.innerHTML = '<option value="">Pilih Kelas</option>';

                        data.kelas.forEach(kelas => {
                            const option = document.createElement('option');
                            option.value = kelas.kelas_id;
                            option.textContent = kelas.nama_kelas;
                            // Select if this was the previously selected kelas
                            if (selectedKelasId && selectedKelasId == kelas.kelas_id) {
                                option.selected = true;
                            }

                            kelasSelect.appendChild(option);
                        });
                    } else {
                        kelasSelect.innerHTML = '<option value="">Tidak ada kelas tersedia</option>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    kelasLoading.classList.add('d-none');
                    kelasSelect.disabled = false;
                    kelasSelect.innerHTML = '<option value="">Error memuat data kelas</option>';
                });
        }

        function resetKelasSelect() {
            kelasSelect.disabled = true;
            kelasSelect.innerHTML = '<option value="">Pilih Sekolah Terlebih Dahulu</option>';
        }
    });
</script>

<?= $this->endSection() ?>