<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

require_once '../config/database.php';

$id_user = $_POST['id_user'] ?? '';
$id_kos = $_POST['id_kos'] ?? '';

if (empty($id_user) || empty($id_kos)) {
    echo json_encode(["status" => "error", "message" => "ID User atau ID Kos tidak ditemukan"]);
    exit;
}

try {
    // Cek apakah sudah difavorit
    $check = $conn->prepare("SELECT id_favorit FROM favorit WHERE id_user = ? AND id_kos = ?");
    $check->execute([$id_user, $id_kos]);
    
    if ($check->rowCount() > 0) {
        // Jika sudah ada, hapus (unfavorit)
        $delete = $conn->prepare("DELETE FROM favorit WHERE id_user = ? AND id_kos = ?");
        $delete->execute([$id_user, $id_kos]);
        echo json_encode(["status" => "success", "action" => "removed", "message" => "Dihapus dari favorit"]);
    } else {
        // Jika belum, tambah
        $insert = $conn->prepare("INSERT INTO favorit (id_user, id_kos) VALUES (?, ?)");
        $insert->execute([$id_user, $id_kos]);
        echo json_encode(["status" => "success", "action" => "added", "message" => "Ditambahkan ke favorit"]);
    }
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>