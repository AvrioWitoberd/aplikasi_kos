<?php
require_once '../../config/auth_check.php';
require_once '../../config/database.php';
cekAkses('pemilik');

if (isset($_GET['id'])) {
    $id_kos = $_GET['id'];
    $id_pemilik = $_SESSION['user_id'];

    // 1. Ambil nama file foto untuk dihapus dari folder uploads
    $queryFoto = "SELECT file_nama FROM foto_kos WHERE id_kos = ?";
    $stmtFoto = $conn->prepare($queryFoto);
    $stmtFoto->execute([$id_kos]);
    $fotos = $stmtFoto->fetchAll();

    foreach ($fotos as $f) {
        $path = "../../uploads/kos/" . $f['file_nama'];
        if (file_exists($path)) {
            unlink($path); // Hapus file fisik
        }
    }

    // 2. Hapus data dari database (Gunakan Transaksi atau urutan yang benar)
    // Jika di database pakai ON DELETE CASCADE, cukup hapus tabel 'kos' saja
    $queryHapus = "DELETE FROM kos WHERE id_kos = ? AND id_pemilik = ?";
    $stmtHapus = $conn->prepare($queryHapus);
    
    if ($stmtHapus->execute([$id_kos, $id_pemilik])) {
        header("Location: daftar_kos.php?pesan=berhasil_hapus");
    } else {
        header("Location: daftar_kos.php?pesan=gagal_hapus");
    }
}
?>