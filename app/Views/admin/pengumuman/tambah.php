<?= $this->extend('templates/admin/admin_template') ?>

<?= $this->section('title') ?>Tambah Pengumuman<?= $this->endSection() ?>

<?= $this->section('content') ?>
<br><br><br>
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tambah Pengumuman</h1>
        <a href="<?= base_url('admin/pengumuman') ?>" class="btn btn-secondary btn-icon-split">
            <span class="icon text-white-50">
                <i class="fas fa-arrow-left"></i>
            </span>
            <span class="text">Kembali</span>
        </a>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->get('errors')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Terjadi kesalahan:</strong>
            <ul class="mb-0">
                <?php foreach (session()->get('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach ?>
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <!-- Form Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Tambah Pengumuman</h6>
        </div>
        <div class="card-body">
            <form action="<?= base_url('admin/pengumuman/tambah') ?>" method="post">
                <?= csrf_field() ?>
                
                <div class="form-group">
                    <label for="judul">Judul Pengumuman <span class="text-danger">*</span></label>
                    <input type="text" 
                           class="form-control <?= (session()->get('errors.judul')) ? 'is-invalid' : '' ?>" 
                           id="judul" 
                           name="judul" 
                           value="<?= old('judul') ?>" 
                           placeholder="Masukkan judul pengumuman"
                           maxlength="200"
                           required>
                    <?php if (session()->get('errors.judul')): ?>
                        <div class="invalid-feedback">
                            <?= session()->get('errors.judul') ?>
                        </div>
                    <?php endif; ?>
                    <small class="form-text text-muted">Maksimal 200 karakter</small>
                </div>

                <div class="form-group">
                    <label for="isi_pengumuman">Isi Pengumuman <span class="text-danger">*</span></label>
                    <textarea class="form-control <?= (session()->get('errors.isi_pengumuman')) ? 'is-invalid' : '' ?>" 
                              id="isi_pengumuman" 
                              name="isi_pengumuman" 
                              rows="8"
                              placeholder="Masukkan isi pengumuman..."
                              required><?= old('isi_pengumuman') ?></textarea>
                    <?php if (session()->get('errors.isi_pengumuman')): ?>
                        <div class="invalid-feedback">
                            <?= session()->get('errors.isi_pengumuman') ?>
                        </div>
                    <?php endif; ?>
                    <small class="form-text text-muted">Anda dapat menggunakan HTML sederhana untuk formatting</small>
                </div>

                <div class="form-group">
                    <label for="tanggal_berakhir">Tanggal Berakhir (Opsional)</label>
                    <input type="datetime-local" 
                           class="form-control <?= (session()->get('errors.tanggal_berakhir')) ? 'is-invalid' : '' ?>" 
                           id="tanggal_berakhir" 
                           name="tanggal_berakhir" 
                           value="<?= old('tanggal_berakhir') ?>">
                    <?php if (session()->get('errors.tanggal_berakhir')): ?>
                        <div class="invalid-feedback">
                            <?= session()->get('errors.tanggal_berakhir') ?>
                        </div>
                    <?php endif; ?>
                    <small class="form-text text-muted">Kosongkan jika pengumuman tidak memiliki batas waktu</small>
                </div>

                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="preview_mode">
                        <label class="custom-control-label" for="preview_mode">Mode Preview</label>
                    </div>
                    <small class="form-text text-muted">Centang untuk melihat preview pengumuman</small>
                </div>

                <!-- Preview Section -->
                <div id="preview_section" class="card border-info mb-3" style="display: none;">
                    <div class="card-header bg-info text-white">
                        <h6 class="m-0">Preview Pengumuman</h6>
                    </div>
                    <div class="card-body">
                        <h5 id="preview_judul" class="card-title"></h5>
                        <div id="preview_isi" class="card-text"></div>
                        <hr>
                        <small class="text-muted">
                            <i class="fas fa-calendar"></i> Dipublikasi: <span id="preview_tanggal_publish"><?= date('d/m/Y H:i') ?></span>
                            <span id="preview_tanggal_berakhir_section" style="display: none;">
                                | <i class="fas fa-clock"></i> Berakhir: <span id="preview_tanggal_berakhir_text"></span>
                            </span>
                        </small>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Pengumuman
                    </button>
                    <a href="<?= base_url('admin/pengumuman') ?>" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Set minimum datetime to current time + 1 hour
    function setMinDateTime() {
        var now = new Date();
        now.setHours(now.getHours() + 1); // Minimum 1 jam dari sekarang
        
        var year = now.getFullYear();
        var month = String(now.getMonth() + 1).padStart(2, '0');
        var day = String(now.getDate()).padStart(2, '0');
        var hours = String(now.getHours()).padStart(2, '0');
        var minutes = String(now.getMinutes()).padStart(2, '0');
        
        var minDateTime = `${year}-${month}-${day}T${hours}:${minutes}`;
        $('#tanggal_berakhir').attr('min', minDateTime);
    }
    
    setMinDateTime();
    
    // Validasi form sebelum submit
    $('form').on('submit', function(e) {
        var tanggalBerakhir = $('#tanggal_berakhir').val();
        
        if (tanggalBerakhir) {
            var selectedDate = new Date(tanggalBerakhir);
            var now = new Date();
            
            if (selectedDate <= now) {
                e.preventDefault();
                alert('Tanggal berakhir harus lebih dari waktu sekarang!');
                $('#tanggal_berakhir').focus();
                return false;
            }
        }
        
        return true;
    });

    // Toggle preview mode
    $('#preview_mode').change(function() {
        if ($(this).is(':checked')) {
            $('#preview_section').show();
            updatePreview();
        } else {
            $('#preview_section').hide();
        }
    });

    // Update preview when typing
    $('#judul, #isi_pengumuman, #tanggal_berakhir').on('input change', function() {
        if ($('#preview_mode').is(':checked')) {
            updatePreview();
        }
    });

    function updatePreview() {
        var judul = $('#judul').val() || 'Judul Pengumuman';
        var isi = $('#isi_pengumuman').val() || 'Isi pengumuman akan ditampilkan di sini...';
        var tanggalBerakhir = $('#tanggal_berakhir').val();

        $('#preview_judul').text(judul);
        $('#preview_isi').html(isi.replace(/\n/g, '<br>'));

        if (tanggalBerakhir) {
            try {
                var date = new Date(tanggalBerakhir);
                var formattedDate = date.toLocaleDateString('id-ID', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
                $('#preview_tanggal_berakhir_text').text(formattedDate);
                $('#preview_tanggal_berakhir_section').show();
            } catch (e) {
                $('#preview_tanggal_berakhir_section').hide();
            }
        } else {
            $('#preview_tanggal_berakhir_section').hide();
        }
    }

    // Character counter for judul
    $('#judul').on('input', function() {
        var length = $(this).val().length;
        var maxLength = 200;
        var remaining = maxLength - length;
        
        if (remaining < 20) {
            $(this).next('.form-text').html(`Tersisa ${remaining} karakter`);
            if (remaining < 0) {
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        } else {
            $(this).next('.form-text').html('Maksimal 200 karakter');
            $(this).removeClass('is-invalid');
        }
    });

    // Validasi real-time untuk tanggal berakhir
    $('#tanggal_berakhir').on('change blur', function() {
        var selectedValue = $(this).val();
        
        if (selectedValue) {
            var selectedDate = new Date(selectedValue);
            var now = new Date();
            
            if (selectedDate <= now) {
                $(this).addClass('is-invalid');
                if (!$(this).next('.invalid-feedback').length) {
                    $(this).after('<div class="invalid-feedback">Tanggal berakhir harus lebih dari waktu sekarang</div>');
                }
            } else {
                $(this).removeClass('is-invalid');
                $(this).next('.invalid-feedback').remove();
            }
        } else {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').remove();
        }
    });
});
</script>
<?= $this->endSection() ?>