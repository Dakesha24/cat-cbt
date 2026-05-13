<?= $this->extend('templates/admin_header') ?>

<?= $this->section('content') ?>
<div class="container-fluid my-4">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3">
            <div class="list-group">
                <a href="<?= base_url('admin/dashboard') ?>" class="list-group-item list-group-item-action">
                    Dashboard
                </a>
                <a href="<?= base_url('admin/feedback') ?>" class="list-group-item list-group-item-action active">
                    Kritik & Saran
                </a>
            </div>
        </div>

        <!-- Content -->
        <div class="col-md-9">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Daftar Kritik & Saran</h4>
                    <div>
                        <button class="btn btn-success me-2" id="markAllRead">
                            Tandai Semua Dibaca
                        </button>
                        <button class="btn btn-danger" id="deleteSelected">
                            Hapus Yang Dipilih
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filter -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <select class="form-select" id="statusFilter">
                                <option value="">Semua Status</option>
                                <option value="unread">Belum Dibaca</option>
                                <option value="read">Sudah Dibaca</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="date" class="form-control" id="dateFilter">
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" placeholder="Cari..." id="searchFilter">
                        </div>
                    </div>

                    <!-- Feedback Table -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>
                                        <input type="checkbox" id="selectAll">
                                    </th>
                                    <th>Status</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Pesan</th>
                                    <th>Tanggal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($feedbacks as $feedback): ?>
                                <tr class="<?= $feedback['status'] == 'unread' ? 'table-light' : '' ?>">
                                    <td>
                                        <input type="checkbox" class="feedback-select" value="<?= $feedback['id'] ?>">
                                    </td>
                                    <td>
                                        <span class="badge <?= $feedback['status'] == 'unread' ? 'bg-warning' : 'bg-success' ?>">
                                            <?= $feedback['status'] == 'unread' ? 'Belum Dibaca' : 'Sudah Dibaca' ?>
                                        </span>
                                    </td>
                                    <td><?= $feedback['name'] ?></td>
                                    <td><?= $feedback['email'] ?></td>
                                    <td>
                                        <?= substr($feedback['message'], 0, 50) ?>...
                                        <button class="btn btn-link p-0 ms-1" data-bs-toggle="modal" data-bs-target="#messageModal<?= $feedback['id'] ?>">
                                            Baca Selengkapnya
                                        </button>
                                    </td>
                                    <td><?= date('d/m/Y H:i', strtotime($feedback['created_at'])) ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-danger delete-feedback" data-id="<?= $feedback['id'] ?>">
                                            Hapus
                                        </button>
                                    </td>
                                </tr>

                                <!-- Message Modal -->
                                <div class="modal fade" id="messageModal<?= $feedback['id'] ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Pesan dari <?= $feedback['name'] ?></h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p class="mb-2"><strong>Email:</strong> <?= $feedback['email'] ?></p>
                                                <p class="mb-2"><strong>Tanggal:</strong> <?= date('d/m/Y H:i', strtotime($feedback['created_at'])) ?></p>
                                                <hr>
                                                <p><?= nl2br($feedback['message']) ?></p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                <?php if($feedback['status'] == 'unread'): ?>
                                                <button type="button" class="btn btn-primary mark-read" data-id="<?= $feedback['id'] ?>">
                                                    Tandai Sudah Dibaca
                                                </button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <nav class="mt-3">
                        <ul class="pagination justify-content-center">
                            <?= $pager->links() ?>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select All Checkbox
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.getElementsByClassName('feedback-select');
        for(let checkbox of checkboxes) {
            checkbox.checked = this.checked;
        }
    });

    // Mark as Read
    const markReadButtons = document.getElementsByClassName('mark-read');
    for(let button of markReadButtons) {
        button.addEventListener('click', async function() {
            const id = this.dataset.id;
            try {
                const response = await fetch(`/admin/feedback/mark-read/${id}`, {
                    method: 'POST'
                });
                if(response.ok) {
                    location.reload();
                }
            } catch(error) {
                console.error('Error:', error);
            }
        });
    }

    // Delete Feedback
    const deleteButtons = document.getElementsByClassName('delete-feedback');
    for(let button of deleteButtons) {
        button.addEventListener('click', async function() {
            if(!confirm('Apakah Anda yakin ingin menghapus feedback ini?')) return;
            
            const id = this.dataset.id;
            try {
                const response = await fetch(`/admin/feedback/delete/${id}`, {
                    method: 'POST'
                });
                if(response.ok) {
                    location.reload();
                }
            } catch(error) {
                console.error('Error:', error);
            }
        });
    }

    // Filters
    const statusFilter = document.getElementById('statusFilter');
    const dateFilter = document.getElementById('dateFilter');
    const searchFilter = document.getElementById('searchFilter');

    function applyFilters() {
        const params = new URLSearchParams();
        if(statusFilter.value) params.append('status', statusFilter.value);
        if(dateFilter.value) params.append('date', dateFilter.value);
        if(searchFilter.value) params.append('search', searchFilter.value);
        
        window.location.href = `/admin/feedback?${params.toString()}`;
    }

    statusFilter.addEventListener('change', applyFilters);
    dateFilter.addEventListener('change', applyFilters);
    let searchTimeout;
    searchFilter.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(applyFilters, 500);
    });
});
</script>
<?= $this->endSection() ?>