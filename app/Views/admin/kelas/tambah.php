<?= $this->extend('templates/admin/admin_template') ?>

<?= $this->section('title') ?>Tambah Kelas<?= $this->endSection() ?>

<?= $this->section('content') ?>
<br><br><br>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Tambah Kelas Baru</h4>
                    <a href="<?= base_url('admin/kelas') ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('errors')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                    <li><?= $error ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('admin/kelas/tambah') ?>" method="POST">
                        <?= csrf_field() ?>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="sekolah_id" class="form-label">Sekolah <span class="text-danger">*</span></label>
                                    <select class="form-select" id="sekolah_id" name="sekolah_id" required>
                                        <option value="">Pilih Sekolah</option>
                                        <?php foreach ($sekolah as $s): ?>
                                            <option value="<?= $s['sekolah_id'] ?>" 
                                                    <?= old('sekolah_id') == $s['sekolah_id'] ? 'selected' : '' ?>>
                                                <?= esc($s['nama_sekolah']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nama_kelas" class="form-label">Nama Kelas <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="nama_kelas" 
                                           name="nama_kelas" 
                                           value="<?= old('nama_kelas') ?>"
                                           placeholder="Contoh: XII IPA 1, XI IPS 2, X Multimedia"
                                           required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tahun_ajaran" class="form-label">Tahun Ajaran <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="tahun_ajaran" 
                                           name="tahun_ajaran" 
                                           value="<?= old('tahun_ajaran', date('Y') . '/' . (date('Y') + 1)) ?>"
                                           placeholder="Contoh: 2024/2025"
                                           pattern="^\d{4}\/\d{4}$"
                                           required>
                                    <div class="form-text">Format: YYYY/YYYY (contoh: 2024/2025)</div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Quick Select Tahun Ajaran</label>
                                    <div class="d-flex gap-2">
                                        <?php 
                                        $currentYear = date('Y');
                                        for ($i = -1; $i <= 2; $i++): 
                                            $year = $currentYear + $i;
                                            $nextYear = $year + 1;
                                            $tahunAjaran = $year . '/' . $nextYear;
                                        ?>
                                            <button type="button" class="btn btn-outline-primary btn-sm" 
                                                    onclick="document.getElementById('tahun_ajaran').value = '<?= $tahunAjaran ?>'">
                                                <?= $tahunAjaran ?>
                                            </button>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="<?= base_url('admin/kelas') ?>" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Batal
                            </a>
                            <button type="reset" class="btn btn-outline-warning">
                                <i class="fas fa-undo"></i> Reset
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-format tahun ajaran input
document.getElementById('tahun_ajaran').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, ''); // Remove non-digits
    if (value.length >= 4) {
        value = value.slice(0, 4) + '/' + value.slice(4, 8);
    }
    e.target.value = value;
});
</script>

<?= $this->endSection() ?>