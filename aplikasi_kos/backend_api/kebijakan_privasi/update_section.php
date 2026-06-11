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
$judul_section = $_POST['judul_section'] ?? '';
$isi_konten = $_POST['isi_konten'] ?? '';

if (empty($id)) {
    echo json_encode(["status" => "error", "message" => "ID tidak ditemukan"]);
    exit;
}
// Judul section boleh kosong

try {
    $query = "UPDATE kebijakan_privasi SET judul_section = ?, isi_konten = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$judul_section, $isi_konten, $id]);
    
    echo json_encode(["status" => "success", "message" => "Section berhasil diperbarui"]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>