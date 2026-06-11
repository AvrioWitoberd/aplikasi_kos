<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

require_once '../config/database.php';

try {
    $query = "SELECT * FROM blog ORDER BY tgl_dibuat DESC";
    $stmt = $conn->query($query);
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