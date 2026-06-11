<?php
header("Content-Type: application/json");
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_user = $_POST['id_user'] ?? '';
    $status  = $_POST['status'] ?? '';

    if (empty($id_user) || empty($status)) {
        echo json_encode(["status" => "error", "message" => "Data tidak lengkap"]);
        exit;
    }

    try {
        $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id_user = ?");
        $stmt->execute([$status, $id_user]);

        echo json_encode(["status" => "success", "message" => "Status berhasil diperbarui"]);
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Method tidak diizinkan"]);
}
?>