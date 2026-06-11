<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
session_start();

require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pemilik') {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

$id_pemilik = $_SESSION['user_id'];

// Ambil data form
$nama_kos = $_POST['nama_kos'] ?? '';
$jumlah_kamar = $_POST['jumlah_kamar'] ?? 0;
$tipe_kos = $_POST['tipe_kos'] ?? '';
$kota = $_POST['kota'] ?? '';
$harga_per_bulan = $_POST['harga_per_bulan'] ?? 0;
$deskripsi = $_POST['deskripsi'] ?? '';
$alamat_lengkap = $_POST['alamat_lengkap'] ?? '';
$fasilitas_utama = $_POST['fasilitas_utama'] ?? '';
$no_hp_kos = $_POST['no_hp_kos'] ?? '';
$link_maps = $_POST['link_maps'] ?? '';
$peraturan_kos = $_POST['peraturan_kos'] ?? '';
$area_sekitar_kos = $_POST['area_sekitar_kos'] ?? '';

// Validasi
if (empty($nama_kos) || empty($tipe_kos) || empty($kota) || empty($harga_per_bulan) || empty($alamat_lengkap)) {
    echo json_encode(["status" => "error", "message" => "Data wajib tidak lengkap"]);
    exit;
}

// Upload foto multiple
$target_dir = "../../uploads/foto_kos/";
if (!is_dir($target_dir)) {
    mkdir($target_dir, 0777, true);
}

$uploaded_files = [];
if (isset($_FILES['foto_kos']) && !empty($_FILES['foto_kos']['name'][0])) {
    foreach ($_FILES['foto_kos']['name'] as $key => $name) {
        if ($_FILES['foto_kos']['error'][$key] === 0) {
            $tmp_name = $_FILES['foto_kos']['tmp_name'][$key];
            $unique_name = time() . "_" . uniqid() . "_" . basename($name);
            $target_file = $target_dir . $unique_name;
            
            if (move_uploaded_file($tmp_name, $target_file)) {
                $uploaded_files[] = $unique_name;
            }
        }
    }
}

try {
    $conn->beginTransaction();
    
    // Insert ke tabel kos
    $query = "INSERT INTO kos (id_pemilik, nama_kos, jumlah_kamar, tipe_kos, kota, 
            harga_per_bulan, deskripsi, alamat_lengkap, fasilitas_utama, no_hp_kos, 
            link_maps, peraturan_kos, area_sekitar_kos) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->execute([$id_pemilik, $nama_kos, $jumlah_kamar, $tipe_kos, $kota, 
                    $harga_per_bulan, $deskripsi, $alamat_lengkap, $fasilitas_utama, 
                    $no_hp_kos, $link_maps, $peraturan_kos, $area_sekitar_kos]);
    
    $id_kos = $conn->lastInsertId();
    
    // Insert ke tabel foto_kos
    if (!empty($uploaded_files)) {
        $query_foto = "INSERT INTO foto_kos (id_kos, file_nama) VALUES (?, ?)";
        $stmt_foto = $conn->prepare($query_foto);
        foreach ($uploaded_files as $file) {
            $stmt_foto->execute([$id_kos, $file]);
        }
    }
    
    $conn->commit();
    echo json_encode(["status" => "success", "message" => "Kos berhasil ditambahkan"]);
} catch (PDOException $e) {
    $conn->rollBack();
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>