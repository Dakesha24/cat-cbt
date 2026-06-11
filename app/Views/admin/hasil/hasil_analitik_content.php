<div class="row mb-4">
    <div class="col-12">
        <div class="analytics-card" style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="m-0 fw-bold" style="color: #1e293b;">
                    <i class="bi bi-graph-up-arrow text-primary me-2"></i>Grafik Perkembangan Nilai
                </h6>
                
                <select id="filterSiswaGrafik" class="form-select form-select-sm w-auto" onchange="fetchGrafikPerkembangan()">
                    <option value="">-- Semua Siswa --</option>
                    <?php if (!empty($studentRows)): ?>
                        <?php foreach ($studentRows as $s): ?>
                            <option value="<?= esc($s['siswa_id'] ?? $s['user_id'] ?? '') ?>">
                                <?= esc($s['nama_lengkap'] ?? $s['nama_siswa'] ?? 'Siswa') ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <div style="position: relative; height: 320px; width: 100%;">
                <canvas id="growthLineChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
let growthChartInstance = null;

function fetchGrafikPerkembangan() {
    // Tangkap parameter filter yang sudah ada di URL browser (Variabel, Indikator, Materi)
    const urlParams = new URLSearchParams(window.location.search);
    
    // Tangkap pilihan siswa dari dropdown
    const siswaId = document.getElementById('filterSiswaGrafik').value;
    if (siswaId) urlParams.set('siswa_id', siswaId);

    // Ambil data ke fungsi baru di Controller
    fetch('<?= base_url('admin/grafik-perkembangan-ajax') ?>?' + urlParams.toString())
        .then(response => response.json())
        .then(data => {
            renderGrowthChart(data.labels, data.data);
        })
        .catch(error => console.error('Gagal mengambil data grafik:', error));
}

function renderGrowthChart(labels, dataPoints) {
    const ctx = document.getElementById('growthLineChart');
    if (!ctx) return;

    // Reset grafik jika sudah pernah digambar sebelumnya
    if (growthChartInstance) {
        growthChartInstance.destroy(); 
    }

    growthChartInstance = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Rata-Rata Capaian / Skor',
                data: dataPoints,
                borderColor: '#4f46e5',
                backgroundColor: 'rgba(79, 70, 229, 0.06)',
                borderWidth: 3,
                tension: 0.35,
                fill: true,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#4f46e5',
                pointBorderWidth: 2,
                pointRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
}

// Panggil otomatis saat halaman pertama kali dibuka
document.addEventListener("DOMContentLoaded", function() {
    fetchGrafikPerkembangan();
});
</script>