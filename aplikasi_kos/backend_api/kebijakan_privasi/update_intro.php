<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
session_start();

require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

$intro_text = $_POST['intro_text'] ?? '';

try {
    // Update atau insert intro text
    $check = $conn->prepare("SELECT id FROM kebijakan_privasi WHERE id = 1");
    $check->execute();
    if ($check->rowCount() > 0) {
        $query = "UPDATE kebijakan_privasi SET intro_text = ? WHERE id = 1";
    } else {
        $query = "INSERT INTO kebijakan_privasi (id, intro_text) VALUES (1, ?)";
    }
    $stmt = $conn->prepare($query);
    $stmt->execute([$intro_text]);
    
    echo json_encode(["status" => "success", "message" => "Intro teks berhasil diperbarui"]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>