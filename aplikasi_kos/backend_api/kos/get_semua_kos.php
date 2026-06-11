<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
require_once '../config/database.php'; 

$search    = $_GET['search'] ?? '';
$tipe      = $_GET['tipe'] ?? '';
$min_harga = (int)($_GET['min_harga'] ?? 0);
$max_harga = (int)($_GET['max_harga'] ?? 999999999);

try {
    $sql = "SELECT 
                k.id_kos, 
                k.nama_kos, 
                k.alamat_lengkap, 
                k.kota, 
                k.tipe_kos, 
                k.harga_per_bulan,
                k.jumlah_kamar,
                (SELECT file_nama FROM foto_kos WHERE id_kos = k.id_kos ORDER BY id_foto ASC LIMIT 1) as foto_utama,
                (SELECT IFNULL(AVG(skor_rating), 0) FROM rating WHERE id_kos = k.id_kos) as rata_rating
            FROM kos k
            INNER JOIN users u ON k.id_pemilik = u.id_user
            WHERE u.status = 'aktif'";

    $params = [];

    // Filter pencarian
    if (!empty($search)) {
        $sql .= " AND (k.nama_kos LIKE ? OR k.kota LIKE ? OR k.alamat_lengkap LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }

    // Filter tipe
    if (!empty($tipe)) {
        $sql .= " AND k.tipe_kos = ?";
        $params[] = $tipe;
    }
    
    // Filter harga
    $sql .= " AND k.harga_per_bulan BETWEEN ? AND ?";
    $params[] = $min_harga;
    $params[] = $max_harga;

    $sql .= " GROUP BY k.id_kos ORDER BY rata_rating DESC, k.id_kos DESC";

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "status" => "success",
        "total"  => count($data),
        "data"   => $data
    ]);

} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
?>