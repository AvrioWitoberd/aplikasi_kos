<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
session_start();

require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

$id = $_POST['id'] ?? '';

if (empty($id)) {
    echo json_encode(["status" => "error", "message" => "ID tidak ditemukan"]);
    exit;
}

try {
    $query = "DELETE FROM kebijakan_privasi WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$id]);
    
    echo json_encode(["status" => "success", "message" => "Section berhasil dihapus"]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>