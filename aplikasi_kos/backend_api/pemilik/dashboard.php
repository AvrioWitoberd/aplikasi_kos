<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
session_start();

require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pemilik') {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

$id_pemilik = $_SESSION['user_id'];

try {
    // Total Unit Kos
    $stmt1 = $conn->prepare("SELECT COUNT(*) FROM kos WHERE id_pemilik = ?");
    $stmt1->execute([$id_pemilik]);
    $total_kos = $stmt1->fetchColumn() ?: 0;
    
    // Total Stok Kamar
    $stmt2 = $conn->prepare("SELECT COALESCE(SUM(jumlah_kamar), 0) FROM kos WHERE id_pemilik = ?");
    $stmt2->execute([$id_pemilik]);
    $total_stok = $stmt2->fetchColumn() ?: 0;
    
    // 5 Kos Terbaru
    $stmt3 = $conn->prepare("SELECT id_kos, nama_kos, tipe_kos, harga_per_bulan, kota FROM kos WHERE id_pemilik = ? ORDER BY id_kos DESC LIMIT 5");
    $stmt3->execute([$id_pemilik]);
    $kos_terbaru = $stmt3->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        "status" => "success",
        "data" => [
            "total_kos" => $total_kos,
            "total_stok" => $total_stok,
            "kos_terbaru" => $kos_terbaru
        ]
    ]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>