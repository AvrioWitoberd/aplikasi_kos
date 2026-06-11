<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
session_start();

require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

$id_blog = $_POST['id_blog'] ?? '';

if (empty($id_blog)) {
    echo json_encode(["status" => "error", "message" => "ID tidak ditemukan"]);
    exit;
}

try {
    // Ambil nama file foto
    $query_foto = "SELECT foto_thumbnail FROM blog WHERE id_blog = ?";
    $stmt_foto = $conn->prepare($query_foto);
    $stmt_foto->execute([$id_blog]);
    $data = $stmt_foto->fetch(PDO::FETCH_ASSOC);
    
    if ($data) {
        $path = "../../uploads/blog/" . $data['foto_thumbnail'];
        if (file_exists($path)) {
            unlink($path);
        }
    }
    
    // Hapus data
    $query_hapus = "DELETE FROM blog WHERE id_blog = ?";
    $stmt_hapus = $conn->prepare($query_hapus);
    $stmt_hapus->execute([$id_blog]);
    
    echo json_encode(["status" => "success", "message" => "Blog berhasil dihapus"]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>