<?= $this->extend('templates/guru/guru_template') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4">
    <h1 class="mt-4">Bank Soal</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/guru/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item active">Bank Soal</li>
    </ol>

    <a href="/guru/bank-soal" class="btn btn-primary mb-3">Tambah Soal</a>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Daftar Soal
        </div>
        <div class="card-body">
            <table id="dataSoal" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Mata Pelajaran</th>
                        <th>Pertanyaan</th>
                        <th>Tingkat Kesulitan</th>
                        <th>Tanggal Dibuat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    foreach ($soal as $s): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $s['nama_ujian'] ?></td>
                            <td><?= $s['pertanyaan'] ?></td>

                            <td><?= date('d/m/Y H:i', strtotime($s['created_at'])) ?></td>
                            <td>
                                <button class="btn btn-sm btn-info" onclick="editSoal(<?= $s['soal_id'] ?>)">Edit</button>
                                <button class="btn btn-sm btn-danger" onclick="deleteSoal(<?= $s['soal_id'] ?>)">Hapus</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#dataSoal').DataTable();
        });

        function editSoal(soalId) {
            window.location.href = `/guru/bank-soal/edit/${soalId}`;
        }

        function deleteSoal(soalId) {
            if (confirm('Apakah Anda yakin ingin menghapus soal ini?')) {
                fetch(`/guru/bank-soal/delete/${soalId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Gagal menghapus soal');
                        }
                    });
            }
        }
    </script>
    <?= $this->endSection() ?>