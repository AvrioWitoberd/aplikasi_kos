<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
session_start();

require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

$judul_section = $_POST['judul_section'] ?? '';
$isi_konten = $_POST['isi_konten'] ?? '';

// Judul section boleh kosong

try {
    // Cari urutan terbesar (paling bawah)
    $stmt = $conn->prepare("SELECT MAX(urutan) as max_urutan FROM kebijakan_privasi");
    $stmt->execute();
    $result = $stmt->fetch();
    $urutan = ($result['max_urutan'] ?? 0) + 1;
    
    $query = "INSERT INTO kebijakan_privasi (judul_section, isi_konten, urutan) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->execute([$judul_section, $isi_konten, $urutan]);
    
    echo json_encode(["status" => "success", "message" => "Section berhasil ditambahkan", "id" => $conn->lastInsertId()]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>