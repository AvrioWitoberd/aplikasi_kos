<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

require_once '../config/database.php';

try {
    // Ambil nomor WA admin pertama (role = 'admin' dan status = 'aktif')
    $query = "SELECT no_hp FROM users WHERE role = 'admin' AND status = 'aktif' LIMIT 1";
    $stmt = $conn->query($query);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($admin && !empty($admin['no_hp'])) {
        echo json_encode([
            "status" => "success",
            "no_hp" => $admin['no_hp']
        ]);
    } else {
        // Fallback ke nomor default jika tidak ada admin
        echo json_encode([
            "status" => "success",
            "no_hp" => "6281332077170" // nomor default
        ]);
    }
} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
?>