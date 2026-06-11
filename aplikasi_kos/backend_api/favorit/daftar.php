<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

require_once '../config/database.php';

$id_user = $_GET['id_user'] ?? '';

if (empty($id_user)) {
    echo json_encode(["status" => "error", "message" => "ID User tidak ditemukan"]);
    exit;
}

try {
    $sql = "SELECT 
                k.id_kos, 
                k.nama_kos, 
                k.alamat_lengkap, 
                k.kota, 
                k.tipe_kos, 
                k.harga_per_bulan,
                k.jumlah_kamar,
                (SELECT file_nama FROM foto_kos WHERE id_kos = k.id_kos LIMIT 1) as foto_utama,
                (SELECT IFNULL(AVG(skor_rating), 0) FROM rating WHERE id_kos = k.id_kos) as rata_rating
            FROM favorit f
            INNER JOIN kos k ON f.id_kos = k.id_kos
            WHERE f.id_user = ?
            ORDER BY f.id_favorit DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id_user]);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        "status" => "success",
        "total" => count($data),
        "data" => $data
    ]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>