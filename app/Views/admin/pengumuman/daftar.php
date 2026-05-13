<?= $this->extend('templates/admin/admin_template') ?>

<?= $this->section('title') ?>Kelola Pengumuman<?= $this->endSection() ?>

<?= $this->section('content') ?>

<br><br><br>
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Kelola Pengumuman</h1>
        <a href="<?= base_url('admin/pengumuman/tambah') ?>" class="btn btn-primary btn-icon-split">
            <span class="icon text-white-50">
                <i class="fas fa-plus"></i>
            </span>
            <span class="text">Tambah Pengumuman</span>
        </a>
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

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Pengumuman</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="25%">Judul</th>
                            <th width="15%">Tanggal Publish</th>
                            <th width="15%">Tanggal Berakhir</th>
                            <th width="10%">Pembuat</th>
                            <th width="10%">Status</th>
                            <th width="20%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($pengumuman)): ?>
                            <tr>
                                <td colspan="7" class="text-center">Belum ada pengumuman</td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; ?>
                            <?php foreach ($pengumuman as $item): ?>
                                <?php
                                $now = date('Y-m-d H:i:s');
                                $isActive = true;
                                
                                if ($item['tanggal_berakhir']) {
                                    $isActive = $now <= $item['tanggal_berakhir'];
                                }
                                ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td>
                                        <strong><?= esc($item['judul']) ?></strong>
                                        <br>
                                        <small class="text-muted">
                                            <?= substr(strip_tags($item['isi_pengumuman']), 0, 100) ?>...
                                        </small>
                                    </td>
                                    <td>
                                        <?= date('d/m/Y H:i', strtotime($item['tanggal_publish'])) ?>
                                    </td>
                                    <td>
                                        <?php if ($item['tanggal_berakhir']): ?>
                                            <?= date('d/m/Y H:i', strtotime($item['tanggal_berakhir'])) ?>
                                        <?php else: ?>
                                            <span class="text-muted">Tidak ada</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">
                                            <?= esc($item['pembuat'] ?? 'Unknown') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($isActive): ?>
                                            <span class="badge badge-success">Aktif</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">Berakhir</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column gap-1">
                                            <a href="<?= base_url('admin/pengumuman/detail/' . $item['pengumuman_id']) ?>" 
                                               class="btn btn-info btn-sm">
                                                <i class="fas fa-eye me-1"></i>Detail
                                            </a>
                                            <a href="<?= base_url('admin/pengumuman/edit/' . $item['pengumuman_id']) ?>" 
                                               class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit me-1"></i>Edit
                                            </a>
                                            <a href="<?= base_url('admin/pengumuman/hapus/' . $item['pengumuman_id']) ?>" 
                                               class="btn btn-danger btn-sm"
                                               onclick="return confirm('Apakah Anda yakin ingin menghapus pengumuman ini?')">
                                                <i class="fas fa-trash me-1"></i>Hapus
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#dataTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
        },
        "order": [[ 2, "desc" ]], // Sort by tanggal publish descending
        "pageLength": 25,
        "columnDefs": [
            { 
                "orderable": false, 
                "targets": [6] // Kolom aksi tidak bisa diurutkan
            }
        ]
    });
});
</script>

<style>
.gap-1 {
    gap: 0.25rem !important;
}

/* Memastikan button dalam kolom aksi tidak terlalu kecil */
.btn-sm {
    min-width: 70px;
    font-size: 0.75rem;
}

/* Responsive untuk mobile */
@media (max-width: 768px) {
    .d-flex.flex-column {
        flex-direction: row !important;
        flex-wrap: wrap;
        gap: 0.125rem !important;
    }
    
    .btn-sm {
        min-width: 60px;
        font-size: 0.7rem;
        padding: 0.2rem 0.4rem;
    }
}
</style>
<?= $this->endSection() ?>