<?= $this->extend('templates/admin/admin_template') ?>

<?= $this->section('title') ?>Detail Pengumuman<?= $this->endSection() ?>

<?= $this->section('content') ?>

<br><br><br>
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Pengumuman</h1>
        <div>
            <a href="<?= base_url('admin/pengumuman/edit/' . $pengumuman['pengumuman_id']) ?>" class="btn btn-warning btn-icon-split">
                <span class="icon text-white-50">
                    <i class="fas fa-edit"></i>
                </span>
                <span class="text">Edit</span>
            </a>
            <a href="<?= base_url('admin/pengumuman') ?>" class="btn btn-secondary btn-icon-split">
                <span class="icon text-white-50">
                    <i class="fas fa-arrow-left"></i>
                </span>
                <span class="text">Kembali</span>
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Pengumuman Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Pengumuman</h6>
                        <?php
                        $now = date('Y-m-d H:i:s');
                        $isActive = true;
                        
                        if ($pengumuman['tanggal_berakhir']) {
                            $isActive = $now <= $pengumuman['tanggal_berakhir'];
                        }
                        ?>
                        <?php if ($isActive): ?>
                            <span class="badge badge-success badge-lg">Aktif</span>
                        <?php else: ?>
                            <span class="badge badge-secondary badge-lg">Berakhir</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body">
                    <h4 class="card-title text-primary mb-3">
                        <?= esc($pengumuman['judul']) ?>
                    </h4>
                    
                    <div class="card-text">
                        <?= nl2br(esc($pengumuman['isi_pengumuman'])) ?>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="row text-muted">
                        <div class="col-md-6">
                            <small>
                                <i class="fas fa-calendar"></i> 
                                <strong>Dipublikasi:</strong><br>
                                <?= date('l, d F Y - H:i', strtotime($pengumuman['tanggal_publish'])) ?> WIB
                            </small>
                        </div>
                        <div class="col-md-6">
                            <small>
                                <i class="fas fa-user"></i> 
                                <strong>Dibuat oleh:</strong><br>
                                <?= esc($pengumuman['pembuat'] ?? 'Admin') ?>
                            </small>
                        </div>
                    </div>
                    
                    <?php if ($pengumuman['tanggal_berakhir']): ?>
                        <hr class="my-3">
                        <div class="text-muted">
                            <small>
                                <i class="fas fa-clock"></i> 
                                <strong>Berakhir pada:</strong><br>
                                <?= date('l, d F Y - H:i', strtotime($pengumuman['tanggal_berakhir'])) ?> WIB
                                
                                <?php if (!$isActive): ?>
                                    <span class="text-danger ml-2">
                                        <i class="fas fa-exclamation-triangle"></i> Sudah berakhir
                                    </span>
                                <?php else: ?>
                                    <?php
                                    $timeLeft = strtotime($pengumuman['tanggal_berakhir']) - time();
                                    if ($timeLeft > 0) {
                                        $days = floor($timeLeft / (60 * 60 * 24));
                                        $hours = floor(($timeLeft % (60 * 60 * 24)) / (60 * 60));
                                        $minutes = floor(($timeLeft % (60 * 60)) / 60);
                                        
                                        if ($days > 0) {
                                            $timeText = "{$days} hari lagi";
                                        } elseif ($hours > 0) {
                                            $timeText = "{$hours} jam {$minutes} menit lagi";
                                        } else {
                                            $timeText = "{$minutes} menit lagi";
                                        }
                                        
                                        echo "<span class='text-warning ml-2'><i class='fas fa-hourglass-half'></i> {$timeText}</span>";
                                    }
                                    ?>
                                <?php endif; ?>
                            </small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Actions Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Aksi</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="<?= base_url('admin/pengumuman/edit/' . $pengumuman['pengumuman_id']) ?>" 
                           class="btn btn-warning btn-block">
                            <i class="fas fa-edit"></i> Edit Pengumuman
                        </a>
                        
                        <hr>
                        
                        <button type="button" 
                                class="btn btn-danger btn-block" 
                                onclick="confirmDelete(<?= $pengumuman['pengumuman_id'] ?>)">
                            <i class="fas fa-trash"></i> Hapus Pengumuman
                        </button>
                        
                        <hr>
                        
                        <a href="<?= base_url('admin/pengumuman') ?>" class="btn btn-secondary btn-block">
                            <i class="fas fa-list"></i> Daftar Pengumuman
                        </a>
                    </div>
                </div>
            </div>

            <!-- Info Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td><strong>ID:</strong></td>
                            <td><?= $pengumuman['pengumuman_id'] ?></td>
                        </tr>
                        <tr>
                            <td><strong>Status:</strong></td>
                            <td>
                                <?php if ($isActive): ?>
                                    <span class="badge badge-success">Aktif</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">Berakhir</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Panjang Isi:</strong></td>
                            <td><?= strlen($pengumuman['isi_pengumuman']) ?> karakter</td>
                        </tr>
                        <tr>
                            <td><strong>Tipe:</strong></td>
                            <td>
                                <?php if ($pengumuman['tanggal_berakhir']): ?>
                                    <span class="badge badge-info">Terbatas</span>
                                <?php else: ?>
                                    <span class="badge badge-primary">Permanen</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Preview Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Preview untuk Siswa/Guru</h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info border-info">
                        <h5 class="alert-heading"><?= esc($pengumuman['judul']) ?></h5>
                        <p class="mb-2"><?= nl2br(substr(esc($pengumuman['isi_pengumuman']), 0, 150)) ?>
                        <?php if (strlen($pengumuman['isi_pengumuman']) > 150): ?>...<?php endif; ?></p>
                        <hr>
                        <small class="text-muted">
                            <i class="fas fa-calendar"></i> <?= date('d/m/Y H:i', strtotime($pengumuman['tanggal_publish'])) ?>
                        </small>
                    </div>
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i> Ini adalah tampilan singkat pengumuman seperti yang dilihat pengguna lain
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus pengumuman ini?</p>
                <div class="alert alert-warning">
                    <strong>Peringatan:</strong> Tindakan ini tidak dapat dibatalkan!
                </div>
                <p><strong>Judul:</strong> <?= esc($pengumuman['judul']) ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <a href="#" id="deleteConfirmBtn" class="btn btn-danger">Ya, Hapus</a>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(pengumumanId) {
    $('#deleteConfirmBtn').attr('href', '<?= base_url("admin/pengumuman/hapus/") ?>' + pengumumanId);
    $('#deleteModal').modal('show');
}

$(document).ready(function() {
    // Auto-refresh status setiap 30 detik jika pengumuman akan berakhir dalam 1 jam
    <?php if ($pengumuman['tanggal_berakhir'] && $isActive): ?>
        <?php
        $timeLeft = strtotime($pengumuman['tanggal_berakhir']) - time();
        if ($timeLeft > 0 && $timeLeft <= 3600): // 1 jam
        ?>
        setInterval(function() {
            location.reload();
        }, 30000); // 30 detik
        <?php endif; ?>
    <?php endif; ?>
});
</script>
<?= $this->endSection() ?>