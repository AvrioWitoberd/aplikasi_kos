<?php
require_once '../../config/database.php';
session_start();

$id_user = $_GET['id'] ?? null;

if (!$id_user) {
    header("Location: dashboard.php");
    exit;
}

try {
    // Gunakan Alias jika diperlukan, tapi pastikan kolom p.nama_kos dsb terpanggil
    $query = "SELECT u.email, u.role, u.status, u.foto_profil, 
                     p.id_profil, p.nama_kos, p.deskripsi, p.alamat_lengkap, 
                     p.kota, p.foto_kos, p.foto_ktp, p.nama_pemilik, p.kontak
              FROM users u 
              LEFT JOIN profil_kos p ON u.id_user = p.id_user 
              WHERE u.id_user = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->execute([$id_user]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
        echo "<script>alert('Data tidak ditemukan di database!'); window.location='dashboard.php';</script>";
        exit;
    }

    // Ambil daftar unit kos milik user ini dari tabel 'kos'
    $id_pemilik = $id_user;
    $query = "SELECT k.*, 
            (SELECT file_nama FROM foto_kos WHERE id_kos = k.id_kos LIMIT 1) as foto_utama 
            FROM kos k 
            WHERE k.id_pemilik = ? 
            ORDER BY k.id_kos DESC";
    $stmt = $conn->prepare($query);
    $stmt->execute([$id_pemilik]);
    $daftar_kos = $stmt->fetchAll();

} catch (PDOException $e) {
    die("Error Database: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pemilik - MyKos Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css"> 
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
    </style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main-content">
    <div class="container-fluid">
        <div class="d-flex align-items-center mb-4">
            <a href="dashboard.php" class="btn btn-light rounded-circle me-3 shadow-sm">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h3 class="fw-bold mb-0">Detail Profil Pemilik</h3>
                <p class="text-muted mb-0">Informasi lengkap akun dan profil bisnis MyKos.</p>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow-sm p-4 text-center">
                    <div class="mb-3">
                        <?php if (!empty($data['foto_profil'])): ?>
                            <img src="../../backend_api/uploads/profiles/<?= $data['foto_profil'] ?>" class="rounded-circle shadow-sm" style="width: 130px; height: 130px; object-fit: cover; border: 4px solid #f8f9fa;">
                        <?php else: ?>
                            <img src="https://ui-avatars.com/api/?name=<?= urlencode($data['nama_pemilik'] ?? $data['nama_lengkap']) ?>&background=random&size=128" class="rounded-circle shadow-sm">
                        <?php endif; ?>
                    </div>
                    
                    <h4 class="fw-bold mb-1"><?= strtoupper($data['nama_pemilik'] ?? $data['nama_lengkap'] ?? 'USER') ?></h4>
                    <span class="badge bg-light text-primary px-3 py-2 mb-4" style="border-radius: 10px;">
                        <i class="bi bi-patch-check-fill me-1"></i> <?= ucfirst($data['role'] ?? 'Pemilik') ?>
                    </span>
                    
                    <div class="text-start border-top pt-3">
                        <div class="mb-3">
                            <label class="text-muted small d-block">Email Akun</label>
                            <span class="fw-medium"><?= $data['email'] ?></span>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small d-block">No. WhatsApp / Kontak</label>
                            <span class="fw-medium text-success"><?= $data['kontak'] ?? $data['no_hp'] ?? '-' ?></span>
                        </div>
                        <div class="mb-2">
                            <label class="text-muted small d-block">Status Akun</label>
                            <span class="badge <?= ($data['status'] ?? '') == 'aktif' ? 'bg-success' : 'bg-danger' ?> px-3">
                                <?= strtoupper($data['status'] ?? 'AKTIF') ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card border-0 shadow-sm p-4">
                    <h5 class="fw-bold mb-4">Informasi Bisnis & Profil</h5>
                    
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="text-muted small d-block mb-1">Nama Bisnis / Kos</label>
                            <p class="fw-bold fs-5 text-dark"><?= !empty($data['nama_kos']) ? $data['nama_kos'] : '<span class="text-muted fw-normal">Belum diisi di database</span>' ?></p>
                        </div>

                        <div class="col-md-6">
                            <label class="text-muted small d-block mb-1">Alamat Lengkap</label>
                            <p class="fw-semibold"><?= !empty($data['alamat_lengkap']) ? $data['alamat_lengkap'] : '<span class="text-muted fw-normal">Alamat Kosong</span>' ?></p>
                        </div>

                        <div class="col-12">
                            <label class="text-muted small d-block mb-1">Tentang Pemilik / Deskripsi</label>
                            <div class="p-3 bg-light rounded-3">
                                <p class="mb-0 text-secondary"><?= nl2br($data['deskripsi'] ?? 'Tidak ada deskripsi profil.') ?></p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="text-muted small d-block mb-1">Kota</label>
                            <p class="fw-semibold"><i class="bi bi-geo-alt me-1"></i> <?= $data['kota'] ?? '-' ?></p>
                        </div>

                        <div class="col-md-6">
                            <label class="text-muted small d-block mb-1">Bergabung Sejak</label>
                            <p class="fw-semibold"><i class="bi bi-calendar3 me-1"></i> <?= isset($data['created_at']) ? date('d F Y', strtotime($data['created_at'])) : '22 April 2026' ?></p>
                        </div>
                    </div>

                    <div class="mt-5 pt-3 border-top d-flex justify-content-between align-items-center">
                        <p class="text-muted small mb-0">ID Profil: #<?= $data['id_profil'] ?? '0' ?></p>
                        <button class="btn btn-primary px-4 fw-bold" onclick="window.print()" style="border-radius: 12px;">
                            <i class="bi bi-printer me-2"></i> Cetak Data
                        </button>
                    </div>
                </div>

                <div class="card border-0 shadow-sm p-4 mt-4">
                    <h5 class="fw-bold mb-4">Dokumen & Lampiran</h5>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="text-muted small d-block mb-2">Foto Unit Kos</label>
                                <div class="position-relative">
                                    <?php 
                                    if (!empty($data['foto_kos'])): 
                                        // Ambil foto pertama saja
                                        $fotos = explode(',', $data['foto_kos']);
                                        $foto_pertama = trim($fotos[0]);
                                    ?>
                                        <img src="../../uploads/profiles/<?= rawurlencode($foto_pertama) ?>" 
                                            class="img-fluid rounded-3 shadow-sm w-100" 
                                            style="height: 200px; object-fit: cover;"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#viewFotoKos"
                                            onerror="this.src='https://placehold.co/600x400?text=Foto+Kos+Tidak+Ditemukan'">
                                    <?php else: ?>
                                        <div class="bg-light rounded-3 d-flex align-items-center justify-content-center" style="height: 200px;">
                                            <span class="text-muted small">Foto Kos belum diunggah</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="text-muted small d-block mb-2">Foto KTP Pemilik</label>
                                <div class="position-relative">
                                    <?php if (!empty($data['foto_ktp'])): ?>
                                        <img src="../../uploads/profiles/<?= rawurlencode($data['foto_ktp']) ?>" 
                                            class="img-fluid rounded-3 shadow-sm w-100" 
                                            style="height: 200px; object-fit: cover; cursor: pointer; filter: blur(4px);" 
                                            onmouseover="this.style.filter='none'" 
                                            onmouseout="this.style.filter='blur(4px)'"
                                            data-bs-toggle="modal" data-bs-target="#viewFotoKTP"
                                            onerror="this.src='https://placehold.co/600x400?text=Foto+KTP+Tidak+Ditemukan'">
                                    <?php else: ?>
                                        <div class="bg-light rounded-3 d-flex align-items-center justify-content-center" style="height: 200px;">
                                            <span class="text-muted small">Foto KTP belum diunggah</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>                
        </div>
        
        <div class="card border-0 shadow-sm p-4 mt-4">
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
        </div>
    </div>
</div>


<div class="modal fade" id="viewFotoKos" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content bg-transparent border-0">
            <div class="modal-body p-0 text-center position-relative">
                <!-- Tombol Close (X) di pojok kanan atas -->
                <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" 
                        data-bs-dismiss="modal" 
                        aria-label="Close"
                        style="z-index: 1050; background-color: rgba(0,0,0,0.5); border-radius: 50%; padding: 10px;">
                </button>
                
                <!-- Gambar -->
                <img src="../../uploads/profiles/<?= rawurlencode(explode(',', $data['foto_kos'] ?? '')[0]) ?>" 
                     class="img-fluid rounded-3 shadow"
                     alt="Foto Kos">
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="viewFotoKTP" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Verifikasi KTP</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center bg-light">
                <img src="../../uploads/profiles/<?= rawurlencode($data['foto_ktp'] ?? '') ?>" class="img-fluid rounded-3 shadow">
            </div>
        </div>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>