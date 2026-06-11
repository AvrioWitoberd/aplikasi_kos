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
$judul = $_POST['judul'] ?? '';
$kategori = $_POST['kategori'] ?? '';
$isi_konten = $_POST['isi_konten'] ?? '';
$foto_lama = $_POST['foto_lama'] ?? '';

if (empty($id_blog) || empty($judul) || empty($kategori) || empty($isi_konten)) {
    echo json_encode(["status" => "error", "message" => "Data tidak lengkap"]);
    exit;
}

$foto_final = $foto_lama;

// Upload foto baru jika ada
if (!empty($_FILES['foto_thumbnail']['name'])) {
    $tmp_name = $_FILES['foto_thumbnail']['tmp_name'];
    $unique_name = time() . "_" . $_FILES['foto_thumbnail']['name'];
    $target_dir = "../../uploads/blog/";
    
    if (move_uploaded_file($tmp_name, $target_dir . $unique_name)) {
        // Hapus foto lama
        if (!empty($foto_lama) && file_exists($target_dir . $foto_lama)) {
            unlink($target_dir . $foto_lama);
        }
        $foto_final = $unique_name;
    } else {
        echo json_encode(["status" => "error", "message" => "Gagal upload foto"]);
        exit;
    }
}

try {
    $query = "UPDATE blog SET judul = ?, kategori = ?, isi_konten = ?, foto_thumbnail = ? WHERE id_blog = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$judul, $kategori, $isi_konten, $foto_final, $id_blog]);
    
    echo json_encode(["status" => "success", "message" => "Blog berhasil diperbarui"]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>