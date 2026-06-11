<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

require_once '../config/database.php';

$id_blog = $_GET['id'] ?? '';

if (empty($id_blog)) {
    echo json_encode(["status" => "error", "message" => "ID tidak ditemukan"]);
    exit;
}

try {
    $query = "SELECT * FROM blog WHERE id_blog = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$id_blog]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$data) {
        echo json_encode(["status" => "error", "message" => "Data tidak ditemukan"]);
        exit;
    }
    
    echo json_encode(["status" => "success", "data" => $data]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>