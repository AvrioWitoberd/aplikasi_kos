<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
session_start();

require_once '../config/database.php';

// Hapus validasi role - semua user bisa akses (atau hanya yang sudah login)
// Tapi biarkan yang belum login juga bisa lihat (opsional)
// if (!isset($_SESSION['user_id'])) {
//     echo json_encode(["status" => "error", "message" => "Unauthorized"]);
//     exit;
// }

try {
    // Ambil intro text (dari id=1)
    $queryIntro = "SELECT intro_text FROM kebijakan_privasi WHERE id = 1";
    $stmtIntro = $conn->prepare($queryIntro);
    $stmtIntro->execute();
    $intro = $stmtIntro->fetch(PDO::FETCH_ASSOC);
    
    // Ambil semua section
    $query = "SELECT * FROM kebijakan_privasi ORDER BY urutan ASC";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $sections = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        "status" => "success",
        "intro_text" => $intro['intro_text'] ?? '',
        "data" => $sections
    ]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>