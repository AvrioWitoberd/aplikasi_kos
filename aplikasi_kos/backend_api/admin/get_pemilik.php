<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *"); // Sesuaikan untuk keamanan nantinya

require_once '../config/database.php';

try {
    // Ambil data user dengan role pemilik
    $query = $conn->query("SELECT id_user, nama_lengkap, email, status, created_at 
                           FROM users 
                           WHERE role = 'pemilik' 
                           ORDER BY created_at DESC");
    $data = $query->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "status" => "success",
        "data" => $data
    ]);

} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
?>