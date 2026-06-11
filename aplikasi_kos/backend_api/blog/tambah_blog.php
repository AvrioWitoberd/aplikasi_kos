<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
session_start();

require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

$id_admin = $_SESSION['user_id'];
$judul = $_POST['judul'] ?? '';
$kategori = $_POST['kategori'] ?? '';
$isi_konten = $_POST['isi_konten'] ?? '';

if (empty($judul) || empty($kategori) || empty($isi_konten)) {
    echo json_encode(["status" => "error", "message" => "Data tidak lengkap"]);
    exit;
}

// Upload foto
$foto_name = $_FILES['foto_thumbnail']['name'] ?? '';
$foto_final = '';

if (!empty($foto_name)) {
    $tmp_name = $_FILES['foto_thumbnail']['tmp_name'];
    $unique_name = time() . "_" . $foto_name;
    $target_dir = "../../uploads/blog/";
    
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    if (move_uploaded_file($tmp_name, $target_dir . $unique_name)) {
        $foto_final = $unique_name;
    } else {
        echo json_encode(["status" => "error", "message" => "Gagal upload foto"]);
        exit;
    }
} else {
    echo json_encode(["status" => "error", "message" => "Foto thumbnail wajib diisi"]);
    exit;
}

$tgl_dibuat = date('Y-m-d H:i:s');

try {
    $query = "INSERT INTO blog (id_admin, judul, foto_thumbnail, kategori, isi_konten, tgl_dibuat) 
              VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->execute([$id_admin, $judul, $foto_final, $kategori, $isi_konten, $tgl_dibuat]);
    
    echo json_encode(["status" => "success", "message" => "Blog berhasil ditambahkan"]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>