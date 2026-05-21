<?php
require_once '../../config/auth_check.php'; 
require_once '../../config/database.php';
cekAkses('pemilik');

$id_pemilik = $_SESSION['user_id'];

// Ambil data kos
$query = "SELECT k.*, 
          (SELECT file_nama FROM foto_kos WHERE id_kos = k.id_kos LIMIT 1) as foto_utama 
          FROM kos k 
          WHERE k.id_pemilik = ? 
          ORDER BY k.id_kos DESC";
$stmt = $conn->prepare($query);
$stmt->execute([$id_pemilik]);
$daftar_kos = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Kos - MyKos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- <link rel="stylesheet" href="style_admin.css"> -->
    <link rel="stylesheet" href="../../web/style.css">
    <style>
    /* Untuk membuat tombol aksi seragam dan sejajar */
    .btn-action {
        width: 36px;
        height: 36px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin: 0 2px;
        border-radius: 8px;
    }
    
    /* Mencegah teks di kolom aksi pindah ke baris baru */
    td.text-center {
        white-space: nowrap;
    }
    
    /* Perbaikan untuk tabel pada layar kecil (opsional) */
    @media (max-width: 768px) {
        .table-responsive {
            overflow-x: auto;
        }
    }
</style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">Daftar Properti Kos</h3>
            <p class="text-muted small">Kelola ketersediaan unit kos Anda.</p>
        </div>
        <a href="tambah_kos.php" class="btn btn-primary px-4 py-2">
            <i class="bi bi-plus-lg me-2"></i>Tambah Unit
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">No.</th> <th>Unit</th>
                        <th>Tipe & Kota</th>
                        <th>Harga /Bulan</th>
                        <th>Sisa Kamar</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($daftar_kos) > 0): ?>
                        <?php 
                        $no = 1; // Inisialisasi nomor urut
                        foreach ($daftar_kos as $row): 
                        ?>
                            <tr>
                                <td class="ps-4 align-middle"><?= $no++ ?>.</td> <td>
                                    <div class="d-flex align-items-center">
                                        <?php 
                                            $path = "../../uploads/kos/" . $row['foto_utama'];
                                            $foto = (!empty($row['foto_utama']) && file_exists($path)) ? $path : "https://via.placeholder.com/80";
                                        ?>
                                        <img src="<?= $foto ?>" class="img-thumbnail-custom me-3" alt="Foto">
                                        <div>
                                            <div class="fw-bold text-dark"><?= htmlspecialchars($row['nama_kos']) ?></div>
                                            <small class="text-muted"><?= htmlspecialchars($row['alamat_lengkap']) ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td class="align-middle">
                                    <span class="badge bg-light text-primary border border-primary mb-1"><?= strtoupper($row['tipe_kos']) ?></span>
                                    <div class="small text-muted"><?= htmlspecialchars($row['kota']) ?></div>
                                </td>
                                <td class="align-middle"><div class="fw-bold text-dark">Rp <?= number_format($row['harga_per_bulan'], 0, ',', '.') ?></div></td>
                                <td class="align-middle"><?= ($row['jumlah_kamar'] > 0) ? '<span class="text-success fw-bold">'.$row['jumlah_kamar'].' Kamar</span>' : '<span class="text-danger fw-bold">Penuh</span>' ?></td>
                                <td class="text-center align-middle">
                                    <a href="detail_kos.php?id=<?= $row['id_kos'] ?>" class="btn btn-outline-primary btn-action">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="edit_kos.php?id=<?= $row['id_kos'] ?>" class="btn btn-outline-warning btn-action">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <button onclick="siapkanHapus(<?= $row['id_kos'] ?>, '<?= htmlspecialchars($row['nama_kos'], ENT_QUOTES) ?>')" 
                                            class="btn btn-outline-danger btn-action" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalHapus"
                                            title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center py-5">Belum ada unit.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="modalHapus" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <i class="bi bi-exclamation-triangle text-danger mb-3" style="font-size: 3rem;"></i>
                    <p class="mb-0">Apakah Anda yakin ingin menghapus <strong><span id="namaKosHapus"></span></strong>?</p>
                    <small class="text-muted">Tindakan ini tidak dapat dibatalkan dan semua data terkait akan hilang.</small>
                </div>
                <div class="modal-footer border-0 justify-content-center">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                    <a href="#" id="btnKonfirmasiHapus" class="btn btn-danger px-4">Hapus Sekarang</a>
                </div>
            </div>
        </div>
    </div>

    <script>
    function siapkanHapus(id, nama) {
        document.getElementById('namaKosHapus').innerText = nama;
        document.getElementById('btnKonfirmasiHapus').href = 'proses_hapus_kos.php?id=' + id;
    }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</div>
</body>
</html>