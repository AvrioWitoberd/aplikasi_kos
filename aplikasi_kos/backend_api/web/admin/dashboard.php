<?php
session_start();
require_once '../../config/auth_check.php';
require_once '../../config/database.php';

// 1. Hitung Statistik (Gunakan try-catch agar tidak crash jika tabel belum ada)
try {
    $totalPemilik = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'pemilik'")->fetchColumn();
    $totalKos = $conn->query("SELECT COUNT(*) FROM kos")->fetchColumn();
    // Gunakan pengecekan tabel blog
    $checkBlog = $conn->query("SHOW TABLES LIKE 'blog'")->rowCount();
    $totalBlog = ($checkBlog > 0) ? $conn->query("SELECT COUNT(*) FROM blog")->fetchColumn() : 0;
} catch (PDOException $e) {
    $totalPemilik = $totalKos = $totalBlog = 0;
}

// 2. Logika Filter & Pencarian
$search = $_GET['search'] ?? '';
$filter = $_GET['filter'] ?? 'terbaru';
$order = ($filter == 'terlama') ? 'ASC' : 'DESC';

// 3. Ambil data pemilik kos
$queryPemilik = "SELECT id_user, nama_lengkap, email, status FROM users 
                 WHERE role = 'pemilik' AND (nama_lengkap LIKE :search OR email LIKE :search)
                 ORDER BY id_user $order";
$stmt = $conn->prepare($queryPemilik);
$stmt->execute(['search' => "%$search%"]);
$daftarPemilik = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - MyKos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet"> 
    <link rel="stylesheet" href="../../web/style.css">
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main-content">
    <h2 class="fw-bold mb-4">Dashboard</h2>

    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="stat-card bg-blue h-100">
                <h1 class="display-5 fw-bold mb-0"><?= $totalPemilik ?></h1>
                <p class="mb-0 opacity-75">Pemilik Kos</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card bg-orange h-100">
                <h1 class="display-5 fw-bold mb-0"><?= $totalKos ?></h1>
                <p class="mb-0 opacity-75">Jumlah Kos</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card bg-dark-blue h-100">
                <h1 class="display-5 fw-bold mb-0"><?= $totalBlog ?></h1>
                <p class="mb-0 opacity-75">Blog</p>
            </div>
        </div>
    </div>

    <div class="row align-items-center mb-4">
        <div class="col-md-4">
            <h4 class="fw-bold mb-0">Kelola Pemilik Kos</h4>
        </div>
        <div class="col-md-8">
            <form method="GET" class="d-flex gap-2 justify-content-end" id="formSearch">
                <div class="input-group shadow-sm w-100">
                    <input type="text" name="search" id="searchInput" class="form-control border-0" placeholder="Cari pemilik..." value="<?= htmlspecialchars($search) ?>">
                    <button class="btn btn-outline-secondary border-0 bg-white" type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                    <?php if (!empty($search)): ?>
                    <button class="btn btn-outline-secondary border-0 bg-white" type="button" id="btnReset">
                        <i class="bi bi-x-circle-fill text-danger"></i>
                    </button>
                    <?php endif; ?>
                </div>
                <select name="filter" class="form-select border-0 shadow-sm w-auto" onchange="this.form.submit()">
                    <option value="terbaru" <?= $filter == 'terbaru' ? 'selected' : '' ?>>Terbaru</option>
                    <option value="terlama" <?= $filter == 'terlama' ? 'selected' : '' ?>>Terlama</option>
                </select>
            </form>
        </div>
    </div>

    <div class="owner-list">
        <?php foreach ($daftarPemilik as $index => $p): ?>
        <div class="owner-row shadow-sm">
            <div class="text-muted fw-bold me-3" style="width: 30px;"><?= $index + 1 ?></div>
            
            <div class="owner-info w-200"> <div class="avatar-sm">
                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($p['nama_lengkap']) ?>&background=0D6EFD&color=fff&bold=true&rounded=true" alt="avatar">
                </div>
                <div class="owner-details-inline d-flex justify-content-between align-items-center w-100">
                    <span class="owner-name ms-3"><?= htmlspecialchars($p['nama_lengkap']) ?></span>
                    
                    <span class="owner-email text-muted"><?= htmlspecialchars($p['email']) ?></span>
                    
                    <div style="visibility: hidden; min-width: 150px;"></div> 
                </div>
            </div>
            
            <div class="status-container">
                <?php 
                    $statusClass = $p['status'] == 'aktif' ? 'text-success border-success bg-success-subtle' : 
                                ($p['status'] == 'pending' ? 'text-warning border-warning bg-warning-subtle' : 'text-danger border-danger bg-danger-subtle');
                ?>
                <span class="badge rounded-pill border <?= $statusClass ?> px-3 py-2">
                    <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i> <?= ucfirst($p['status']) ?>
                </span>
            </div>

            <div class="action-container ms-4">
                <a href="data_pemilik_kos.php?id=<?= $p['id_user'] ?>" class="btn-action text-primary" title="Lihat Detail">
                    <i class="bi bi-eye-fill"></i>
                </a>
                <button class="btn-action text-warning" onclick="openModalStatus(<?= $p['id_user'] ?>, '<?= addslashes($p['nama_lengkap']) ?>', '<?= $p['status'] ?>')" title="Edit Status">
                    <i class="bi bi-pencil-square"></i>
                </button>
                <button class="btn-action text-danger" onclick="openModalHapus(<?= $p['id_user'] ?>, '<?= $p['nama_lengkap'] ?>')" title="Hapus Akun">
                    <i class="bi bi-trash3-fill"></i>
                </button>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- modal edit status -->
<div class="modal fade" id="modalStatus" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold">Ubah Status Akun</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="update_status.php" method="POST">
                <div class="modal-body pt-3">
                    <p class="text-muted small mb-3">Pilih status untuk: <strong id="modalUserName"></strong></p>
                    <input type="hidden" name="id_user" id="modalIdUser">
                    
                    <input type="radio" name="status" value="aktif" id="statusAktif" class="form-check-input d-none">
                    <label for="statusAktif" class="w-100 mb-2">
                        <div class="status-option p-3 border rounded-3 border-2">
                            <i class="bi bi-check-circle-fill text-success me-2"></i> Aktif
                        </div>
                    </label>

                    <input type="radio" name="status" value="nonaktif" id="statusNonAktif" class="form-check-input d-none">
                    <label for="statusNonAktif" class="w-100">
                        <div class="status-option p-3 border rounded-3 border-2">
                            <i class="bi bi-x-circle-fill text-danger me-2"></i> Non-Aktif
                        </div>
                    </label>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-3 px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-3 px-4">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- modal hapus -->
 <div class="modal fade" id="modalHapus" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow" style="border-radius: 20px;">
            <div class="modal-body text-center p-4">
                <div class="text-danger mb-3">
                    <i class="bi bi-exclamation-octagon-fill" style="font-size: 3rem;"></i>
                </div>
                <h5 class="fw-bold">Hapus Akun?</h5>
                <p class="text-muted small">Anda yakin ingin menghapus akun <strong id="hapusUserName"></strong>? Tindakan ini tidak dapat dibatalkan.</p>
                
                <form action="hapus_user.php" method="POST" class="mt-4">
                    <input type="hidden" name="id_user" id="hapusIdUser">
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-light w-100 rounded-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger w-100 rounded-3">Ya, Hapus</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// inisialisaasi modal edit status
const myModal = new bootstrap.Modal(document.getElementById('modalStatus'));
function openModalStatus(id, nama, status) {
    document.getElementById('modalIdUser').value = id;
    document.getElementById('modalUserName').innerText = nama;
    
    if (status === 'aktif') {
        document.getElementById('statusAktif').checked = true;
    } else {
        document.getElementById('statusNonAktif').checked = true;
    }
    
    myModal.show();
}

// Inisialisasi Modal Hapus
const modalHapus = new bootstrap.Modal(document.getElementById('modalHapus'));
function openModalHapus(id, nama) {
    document.getElementById('hapusIdUser').value = id;
    document.getElementById('hapusUserName').innerText = nama;
    modalHapus.show();
}

// Fungsi untuk reset search
document.getElementById('btnReset')?.addEventListener('click', function() {
    // Hapus parameter search dari URL
    const url = new URL(window.location.href);
    url.searchParams.delete('search');
    window.location.href = url.toString();
});

// Optional: Enter key submit form
document.getElementById('searchInput')?.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        document.getElementById('formSearch').submit();
    }
});
</script>
</body>
</html>