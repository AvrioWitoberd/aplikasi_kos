<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

require_once '../config/database.php';

$id_user = $_GET['id_user'] ?? $_GET['id'] ?? '';
$id_kos = $_GET['id'] ?? '';

if (empty($id_user) || empty($id_kos)) {
    echo json_encode(["status" => "error", "is_favorit" => false]);
    exit;
}

$check = $conn->prepare("SELECT id_favorit FROM favorit WHERE id_user = ? AND id_kos = ?");
$check->execute([$id_user, $id_kos]);

echo json_encode([
    "status" => "success",
    "is_favorit" => $check->rowCount() > 0
]);
?>