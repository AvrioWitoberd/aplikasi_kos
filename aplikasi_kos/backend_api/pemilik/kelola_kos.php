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
    $query = "SELECT k.*, 
              (SELECT file_nama FROM foto_kos WHERE id_kos = k.id_kos LIMIT 1) as foto_utama 
              FROM kos k 
              WHERE k.id_pemilik = ? 
              ORDER BY k.id_kos DESC";
    $stmt = $conn->prepare($query);
    $stmt->execute([$id_pemilik]);
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