<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
session_start();

require_once '../config/database.php';

// Cek login admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

$id_kos = $_POST['id_kos'] ?? '';

if (empty($id_kos)) {
    echo json_encode(["status" => "error", "message" => "ID Kos tidak ditemukan"]);
    exit;
}

try {
    // Mulai transaksi
    $conn->beginTransaction();
    
    // Ambil nama file foto kos yang akan dihapus
    $stmt = $conn->prepare("SELECT file_nama FROM foto_kos WHERE id_kos = ?");
    $stmt->execute([$id_kos]);
    $foto_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Hapus data di tabel favorit (jika ada)
    $delFavorit = $conn->prepare("DELETE FROM favorit WHERE id_kos = ?");
    $delFavorit->execute([$id_kos]);
    
    // Hapus data di tabel rating (jika ada)
    $delRating = $conn->prepare("DELETE FROM rating WHERE id_kos = ?");
    $delRating->execute([$id_kos]);
    
    // Hapus data di tabel foto_kos
    $delFoto = $conn->prepare("DELETE FROM foto_kos WHERE id_kos = ?");
    $delFoto->execute([$id_kos]);
    
    // Hapus data di tabel kos
    $delKos = $conn->prepare("DELETE FROM kos WHERE id_kos = ?");
    $delKos->execute([$id_kos]);
    
    $conn->commit();
    
    // Hapus file foto fisik
    $basePath = __DIR__ . '/../../uploads/foto_kos/';
    foreach ($foto_list as $foto) {
        if (!empty($foto['file_nama'])) {
            $filePath = $basePath . $foto['file_nama'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
    }
    
    echo json_encode(["status" => "success", "message" => "Kos berhasil dihapus"]);
    
} catch (PDOException $e) {
    $conn->rollBack();
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>