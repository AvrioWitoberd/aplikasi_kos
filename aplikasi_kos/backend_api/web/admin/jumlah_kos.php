<?php
require_once '../../config/database.php';
session_start();

$tahun_ini = date('Y');
$data_kos = [];
$total_kos = 0;

try {
    // 1. Query Utama Tabel Kos
    $queryTabel = "SELECT k.id_kos, k.nama_kos, k.kota, k.alamat_lengkap, p.nama_pemilik, 
                    u.status AS status_akun_pemilik, u.id_user,
                    (SELECT file_nama FROM foto_kos WHERE id_kos = k.id_kos LIMIT 1) as foto_utama
                   FROM kos k
                   LEFT JOIN profil_kos p ON k.id_pemilik = p.id_user
                   LEFT JOIN users u ON k.id_pemilik = u.id_user
                   ORDER BY k.id_kos DESC";
    $stmtTabel = $conn->query($queryTabel);
    $data_kos = $stmtTabel->fetchAll(PDO::FETCH_ASSOC);
    $total_kos = count($data_kos);

    $monthlyCounts = [0, 0, 0, $total_kos, 0, 0, 0, 0, 0, 0, 0, 0];
    $jsonStatData = json_encode($monthlyCounts);

} catch (PDOException $e) {
    die("Error Database: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Statistik Kos - MyKos Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../web/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
        .stat-badge { font-size: 0.8rem; font-weight: 500; border-radius: 8px; }
        .text-dark-blue { color: #000000; }
        /* Style tambahan untuk footer tabel */
        .table-footer { background-color: #fcfcfc; border-top: 1px solid #eee; margin-top: -1px; border-radius: 0 0 18px 18px; }
    </style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main-content">
    <div class="container-fluid px-4 py-3">
        <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
            <div>
                <h3 class="fw-bold text-dark-blue mb-1">Jumlah Statistik kos</h3>
                <p class="text-muted mb-0">Visualisasi data pendaftaran unit kos tahun <?= $tahun_ini ?>.</p>
            </div>
            <div class="text-end">
                <span class="text-muted small">Total Unit Terdaftar</span>
                <h1 class="fw-bold text-primary mb-0"><?= number_format($total_kos) ?></h1>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm p-4" style="border-radius: 18px; background: white;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-semibold text-dark-blue mb-0">Tren Pendaftaran Unit Kos Baru</h5>
                        <span class="badge bg-light text-muted border px-3 py-2 rounded-pill small">Tahun <?= $tahun_ini ?></span>
                    </div>
                    <div style="height: 380px; width: 100%;">
                        <canvas id="kosStatsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 18px; background: white;">
            <div class="p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-semibold text-dark-blue mb-0">Daftar Semua Unit Kos</h5>
                    <div class="input-group" style="max-width: 320px;">
                        <span class="input-group-text bg-white border-end-0 text-muted" style="border-radius: 10px 0 0 10px;">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" id="cariKos" class="form-control border-start-0 ps-1" placeholder="Cari unit atau pemilik..." style="border-radius: 0 10px 10px 0;">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="tabelKosUtama">
                        <thead class="table-light text-uppercase fs-7 text-muted">
                            <tr>
                                <th class="py-3 ps-4" width="60">No</th>
                                <th>Unit Kos & Alamat</th>
                                <th>Pemilik</th>
                                <th>Kota</th>
                                <th>Status Akun</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tabelBody" class="fs-7">
                            <?php if(empty($data_kos)): ?>
                                <tr><td colspan="6" class="text-center py-5 text-muted">Belum ada data kos terdaftar.</td></tr>
                            <?php else: $no = 1; foreach ($data_kos as $row): ?>
                            <tr class="kos-row">
                                <td class="ps-4 fw-medium text-muted"><?= $no++ ?>.</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php 
                                            $path_kos = "../../uploads/kos/" . ($row['foto_utama'] ?? '');
                                            $foto_kos = (!empty($row['foto_utama']) && file_exists($path_kos)) ? $path_kos : "https://via.placeholder.com/100?text=MyKos";
                                        ?>
                                        <img src="<?= $foto_kos ?>" class="rounded-3 me-3" style="width: 50px; height: 50px; object-fit: cover; border: 1px solid #eee;">
                                        <div>
                                            <div class="fw-semibold text-dark fs-6"><?= htmlspecialchars($row['nama_kos'] ?? 'Tanpa Nama') ?></div>
                                            <small class="text-muted d-block text-truncate" style="max-width: 250px;">
                                                <?= htmlspecialchars($row['alamat_lengkap'] ?? '-') ?>
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td class="fw-medium text-secondary"><?= strtoupper($row['nama_pemilik'] ?? 'Anonim') ?></td>
                                <td>
                                    <div class="small text-muted">
                                        <span class="text-danger"><i class="bi bi-geo-alt-fill me-1"></i></span><?= $row['kota'] ?? '-' ?>
                                    </div>
                                </td>
                                <td>
                                    <?php 
                                    $status = strtolower($row['status_akun_pemilik'] ?? 'pending'); 
                                    if ($status == 'aktif'): ?>
                                        <span class="badge bg-success-subtle text-success px-2 py-1 stat-badge">
                                            <i class="bi bi-check-circle-fill me-1"></i>AKTIF
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-warning-subtle text-warning px-2 py-1 stat-badge">
                                            <i class="bi bi-exclamation-circle-fill me-1"></i>PENDING
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <a href="data_pemilik_kos.php?id=<?= $row['id_user'] ?>" class="btn btn-sm text-primary rounded-pill px-3" style="background-color: #e8f1fd; border: none;">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="table-footer p-3 px-4 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <span class="text-muted small me-2">Tampilkan</span>
                    <select class="form-select form-select-sm border-0 bg-light fw-medium" id="limitEntries" style="width: auto; border-radius: 8px; cursor: pointer;">
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="100">100</option>
                        <option value="all">Semua</option>
                    </select>
                    <span class="text-muted small ms-2">Pemilik Kos</span>
                </div>
                <div class="text-muted small">
                    Menampilkan <span id="countVisible">0</span> dari <?= $total_kos ?> data
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // 1. Chart Configuration
    const realDatabaseData = <?= $jsonStatData ?>; 
    const ctx = document.getElementById('kosStatsChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            datasets: [{
                label: 'Unit Kos Baru',
                data: realDatabaseData,
                backgroundColor: '#0d6efd',
                borderRadius: 8,
                barThickness: 25
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, ticks: { color: '#101a29', font: { weight: '500' } } },
                y: { 
                    beginAtZero: true, 
                    ticks: { stepSize: 1, precision: 0, color: '#718096', font: { weight: '500' } },
                    grid: { display: true, drawTicks: false, color: '#d4dbe3', lineWidth: 1 },
                    border: { display: false }
                }
            }
        }
    });

    // 2. LOGIKA LIMIT ENTRIES & SEARCH (Gabungan)
    const tableBody = document.getElementById('tabelBody');
    const rows = tableBody.getElementsByClassName('kos-row');
    const limitSelect = document.getElementById('limitEntries');
    const searchInput = document.getElementById('cariKos');
    const countDisplay = document.getElementById('countVisible');

    function updateTable() {
        let filter = searchInput.value.toUpperCase();
        let limit = limitSelect.value;
        let visibleCount = 0;
        let limitReached = false;

        for (let i = 0; i < rows.length; i++) {
            let text = rows[i].textContent.toUpperCase();
            let isMatch = text.includes(filter);

            if (isMatch && !limitReached) {
                if (limit === 'all' || visibleCount < parseInt(limit)) {
                    rows[i].style.display = "";
                    visibleCount++;
                } else {
                    rows[i].style.display = "none";
                }
            } else {
                rows[i].style.display = "none";
            }
        }
        countDisplay.innerText = visibleCount;
    }

    // Event Listeners
    limitSelect.addEventListener('change', updateTable);
    searchInput.addEventListener('keyup', updateTable);

    // Inisialisasi awal
    updateTable();
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>