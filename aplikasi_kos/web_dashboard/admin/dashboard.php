<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}
require_once '../../backend_api/config/database.php';

try {
    // 1. Jumlah Kos (hanya dari pemilik aktif)
    $count_kos = $conn->query("SELECT COUNT(*) FROM kos k INNER JOIN users u ON k.id_pemilik = u.id_user WHERE u.status = 'aktif'")->fetchColumn();

    // 2. Jumlah TOTAL kamar (hanya dari pemilik aktif)
    $total_kamar = $conn->query("SELECT COALESCE(SUM(k.jumlah_kamar), 0) FROM kos k INNER JOIN users u ON k.id_pemilik = u.id_user WHERE u.status = 'aktif'")->fetchColumn();

    // 3. Jumlah Blog
    $count_blog = $conn->query("SELECT COUNT(*) FROM blog")->fetchColumn();
    
    // 4. Jumlah Pemilik Aktif
    $count_aktif = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'pemilik' AND status = 'aktif'")->fetchColumn();
    
    // 5. Jumlah Pemilik Non-Aktif
    $count_nonaktif = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'pemilik' AND status = 'nonaktif'")->fetchColumn();
    
    // 6. Jumlah Pemilik Pending
    $count_pending = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'pemilik' AND status = 'pending'")->fetchColumn();
    
} catch (PDOException $e) {
    die("Error Database: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - MyKos</title>
    <!-- CSS Dependencies -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style.css?v=1.1">
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>

        <main class="main-content">
            <div class="mb-5">
                <h2 class="fw-bold text-dark">Ringkasan Dashboard</h2>
                <p class="text-muted">Selamat datang kembali, berikut adalah statistik terbaru MyKos.</p>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card card-stat shadow-sm p-3">
                        <div class="d-flex align-items-center">
                            <div class="icon-box bg-primary text-white"><i class="bi bi-building"></i></div>
                            <div class="ms-3">
                                <h6 class="text-muted mb-0">Total Kos</h6>
                                <h3 class="fw-bold mb-0"><?= $count_kos ?></h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card card-stat shadow-sm p-3">
                        <div class="d-flex align-items-center">
                            <div class="icon-box bg-secondary text-white"><i class="bi bi-door-closed"></i></div>
                            <div class="ms-3">
                                <h6 class="text-muted mb-0">Kamar Tersedia</h6>
                                <h3 class="fw-bold mb-0"><?= number_format($total_kamar) ?></h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card card-stat shadow-sm p-3">
                        <div class="d-flex align-items-center">
                            <div class="icon-box bg-info text-white"><i class="bi bi-journal-text"></i></div>
                            <div class="ms-3">
                                <h6 class="text-muted mb-0">Total Artikel Blog</h6>
                                <h3 class="fw-bold mb-0"><?= $count_blog ?></h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card card-stat shadow-sm p-3">
                        <div class="d-flex align-items-center">
                            <div class="icon-box bg-success text-white"><i class="bi bi-person-check"></i></div>
                            <div class="ms-3">
                                <h6 class="text-muted mb-0">Pemilik Aktif</h6>
                                <h3 class="fw-bold mb-0"><?= $count_aktif ?></h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card card-stat shadow-sm p-3">
                        <div class="d-flex align-items-center">
                            <div class="icon-box bg-warning text-white"><i class="bi bi-hourglass-split"></i></div>
                            <div class="ms-3">
                                <h6 class="text-muted mb-0">Menunggu Validasi</h6>
                                <h3 class="fw-bold mb-0"><?= $count_pending ?></h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card card-stat shadow-sm p-3">
                        <div class="d-flex align-items-center">
                            <div class="icon-box bg-danger text-white"><i class="bi bi-person-x"></i></div>
                            <div class="ms-3">
                                <h6 class="text-muted mb-0">Pemilik Non-Aktif</h6>
                                <h3 class="fw-bold mb-0"><?= $count_nonaktif ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Banner -->
            <div class="mt-5 card border-0 shadow-sm p-4 banner-gradient">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="text-white fw-bold">Ada pendaftar baru yang menunggu!</h4>
                        <p class="text-white-50 mb-md-0">Segera lakukan validasi profil pemilik kos agar mereka bisa mulai mengelola data kos.</p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <a href="manajemen_akun.php" class="btn btn-custom-secondary px-4 py-2 fw-bold shadow-sm">Proses Sekarang</a>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>