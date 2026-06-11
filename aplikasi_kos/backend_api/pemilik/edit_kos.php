<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
session_start();

require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pemilik') {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

$id_kos = $_GET['id'] ?? '';

if (empty($id_kos)) {
    echo json_encode(["status" => "error", "message" => "ID Kos tidak ditemukan"]);
    exit;
}

$id_pemilik = $_SESSION['user_id'];

try {
    $query = "SELECT * FROM kos WHERE id_kos = ? AND id_pemilik = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$id_kos, $id_pemilik]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Ambil foto-foto kos
    $stmt_foto = $conn->prepare("SELECT file_nama FROM foto_kos WHERE id_kos = ?");
    $stmt_foto->execute([$id_kos]);
    $fotos = $stmt_foto->fetchAll(PDO::FETCH_COLUMN);
    
    if (!$data) {
        echo json_encode(["status" => "error", "message" => "Data tidak ditemukan"]);
        exit;
    }
    
    echo json_encode([
        "status" => "success",
        "data" => $data,
        "fotos" => $fotos
    ]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>