<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
session_start();

require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

$judul = $_POST['judul'] ?? 'Kebijakan Privasi';
$subtitle = $_POST['subtitle'] ?? 'Aplikasi My Kos (Icarus Developer)';
$intro_text = $_POST['intro_text'] ?? '';
$konten = $_POST['konten'] ?? '';

if (empty($konten)) {
    echo json_encode(["status" => "error", "message" => "Konten tidak boleh kosong"]);
    exit;
}

try {
    $query = "UPDATE kebijakan_privasi SET 
              judul = ?, subtitle = ?, intro_text = ?, konten = ? 
              WHERE id = 1";
    $stmt = $conn->prepare($query);
    $stmt->execute([$judul, $subtitle, $intro_text, $konten]);
    
    echo json_encode(["status" => "success", "message" => "Kebijakan privasi berhasil diperbarui"]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>