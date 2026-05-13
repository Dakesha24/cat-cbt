<?= $this->extend('templates/admin/admin_template') ?>

<?= $this->section('content') ?>
<br><br>
<div class="container">
    <div class="row mb-4 py-4">
        <div class="col">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Tambah Guru</h2>
                <a href="<?= base_url('admin/guru') ?>" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Kembali
                </a>
            </div>

            <!-- Flash Messages -->
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->get('errors')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        <?php foreach (session()->get('errors') as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <form action="<?= base_url('admin/guru/tambah') ?>" method="post">
                        <?= csrf_field() ?>
                        
                        <div class="row">
                            <!-- Data Login -->
                            <div class="col-md-6">
                                <h5 class="mb-3">Data Login</h5>
                                
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username *</label>
                                    <input type="text" class="form-control" id="username" name="username" 
                                           value="<?= old('username') ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">Email *</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?= old('email') ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label">Password *</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                    <div class="form-text">Minimal 6 karakter</div>
                                </div>
                            </div>

                            <!-- Data Guru -->
                            <div class="col-md-6">
                                <h5 class="mb-3">Data Guru</h5>
                                
                                <div class="mb-3">
                                    <label for="nama_lengkap" class="form-label">Nama Lengkap *</label>
                                    <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" 
                                           value="<?= old('nama_lengkap') ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="nip" class="form-label">NIP</label>
                                    <input type="text" class="form-control" id="nip" name="nip" 
                                           value="<?= old('nip') ?>">
                                    <div class="form-text">Opsional</div>
                                </div>

                                <div class="mb-3">
                                    <label for="mata_pelajaran" class="form-label">Mata Pelajaran *</label>
                                    <input type="text" class="form-control" id="mata_pelajaran" name="mata_pelajaran" 
                                           value="<?= old('mata_pelajaran') ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="sekolah_id" class="form-label">Sekolah *</label>
                                    <select class="form-select" id="sekolah_id" name="sekolah_id" required onchange="filterKelas()">
                                        <option value="">Pilih Sekolah</option>
                                        <?php foreach ($sekolah as $s): ?>
                                            <option value="<?= $s['sekolah_id'] ?>" 
                                                    <?= (old('sekolah_id') == $s['sekolah_id']) ? 'selected' : '' ?>>
                                                <?= esc($s['nama_sekolah']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Pilih Kelas (Opsional) -->
                        <hr>
                        <h5 class="mb-3">Kelas yang Diajar (Opsional)</h5>
                        <div class="mb-3">
                            <label for="kelas_ids" class="form-label">Pilih Kelas</label>
                            <select class="form-select" id="kelas_ids" name="kelas_ids[]" multiple disabled>
                                <option value="">Pilih sekolah terlebih dahulu untuk melihat kelas yang tersedia</option>
                            </select>
                            <div class="form-text">
                                Anda dapat memilih multiple kelas dengan menekan Ctrl (Windows) atau Cmd (Mac) sambil mengklik.
                                Kelas juga dapat ditambahkan setelah guru dibuat.
                            </div>
                        </div>

                        <!-- Preview Kelas Terpilih -->
                        <div id="selectedKelasPreview" class="mb-3" style="display: none;">
                            <label class="form-label">Kelas yang Dipilih:</label>
                            <div id="selectedKelasList" class="border rounded p-2 bg-light">
                                <!-- JavaScript will populate this -->
                            </div>
                        </div>

                        <hr>
                        
                        <div class="d-flex justify-content-end gap-2">
                            <a href="<?= base_url('admin/guru') ?>" class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i>Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Data kelas dari PHP
const kelasData = <?= json_encode($kelas) ?>;

function filterKelas() {
    const sekolahId = document.getElementById('sekolah_id').value;
    const kelasSelect = document.getElementById('kelas_ids');
    
    // Clear existing options
    kelasSelect.innerHTML = '';
    
    if (sekolahId) {
        kelasSelect.disabled = false;
        
        // Filter kelas berdasarkan sekolah yang dipilih
        const filteredKelas = kelasData.filter(k => k.sekolah_id == sekolahId);
        
        if (filteredKelas.length > 0) {
            filteredKelas.forEach(kelas => {
                const option = new Option(
                    `${kelas.nama_kelas} - ${kelas.tahun_ajaran}`,
                    kelas.kelas_id
                );
                kelasSelect.add(option);
            });
        } else {
            const option = new Option('Tidak ada kelas tersedia di sekolah ini', '');
            option.disabled = true;
            kelasSelect.add(option);
        }
        
        // Restore selected values if exists (for validation errors)
        const oldKelasIds = <?= json_encode(old('kelas_ids') ?? []) ?>;
        if (oldKelasIds.length > 0) {
            Array.from(kelasSelect.options).forEach(option => {
                if (oldKelasIds.includes(option.value)) {
                    option.selected = true;
                }
            });
            updateSelectedPreview();
        }
    } else {
        kelasSelect.disabled = true;
        kelasSelect.innerHTML = '<option value="">Pilih sekolah terlebih dahulu</option>';
        hideSelectedPreview();
    }
}

function updateSelectedPreview() {
    const kelasSelect = document.getElementById('kelas_ids');
    const preview = document.getElementById('selectedKelasPreview');
    const previewList = document.getElementById('selectedKelasList');
    
    const selectedOptions = Array.from(kelasSelect.selectedOptions);
    
    if (selectedOptions.length > 0) {
        preview.style.display = 'block';
        previewList.innerHTML = selectedOptions.map(option => 
            `<span class="badge bg-primary me-2">${option.text}</span>`
        ).join('');
    } else {
        hideSelectedPreview();
    }
}

function hideSelectedPreview() {
    document.getElementById('selectedKelasPreview').style.display = 'none';
}

// Event listener for kelas selection change
document.getElementById('kelas_ids').addEventListener('change', updateSelectedPreview);

// Initialize form when page loads
document.addEventListener('DOMContentLoaded', function() {
    const sekolahId = document.getElementById('sekolah_id').value;
    if (sekolahId) {
        filterKelas();
    }
});
</script>

<?= $this->endSection() ?>