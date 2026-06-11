<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
session_start();

require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pemilik') {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

$id_kos = $_POST['id_kos'] ?? '';
$id_pemilik = $_SESSION['user_id'];

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
$fotos_to_delete = $_POST['fotos_to_delete'] ?? ''; // Foto yang akan dihapus (dipisah koma)

// Validasi kepemilikan kos
$check = $conn->prepare("SELECT id_kos FROM kos WHERE id_kos = ? AND id_pemilik = ?");
$check->execute([$id_kos, $id_pemilik]);
if ($check->rowCount() == 0) {
    echo json_encode(["status" => "error", "message" => "Anda tidak memiliki akses ke kos ini"]);
    exit;
}

// Hapus foto yang dipilih
if (!empty($fotos_to_delete)) {
    $deleteArray = explode(',', $fotos_to_delete);
    foreach ($deleteArray as $foto) {
        if (!empty($foto)) {
            // Hapus dari database
            $del = $conn->prepare("DELETE FROM foto_kos WHERE id_kos = ? AND file_nama = ?");
            $del->execute([$id_kos, $foto]);
            
            // Hapus file fisik
            $filePath = "../../uploads/foto_kos/" . $foto;
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
    }
}

// Upload foto baru
$target_dir = "../../uploads/foto_kos/";
if (!is_dir($target_dir)) {
    mkdir($target_dir, 0777, true);
}

if (isset($_FILES['foto_kos']) && !empty($_FILES['foto_kos']['name'][0])) {
    foreach ($_FILES['foto_kos']['name'] as $key => $name) {
        if ($_FILES['foto_kos']['error'][$key] === 0) {
            $tmp_name = $_FILES['foto_kos']['tmp_name'][$key];
            $unique_name = time() . "_" . uniqid() . "_" . basename($name);
            $target_file = $target_dir . $unique_name;
            
            if (move_uploaded_file($tmp_name, $target_file)) {
                $stmt_foto = $conn->prepare("INSERT INTO foto_kos (id_kos, file_nama) VALUES (?, ?)");
                $stmt_foto->execute([$id_kos, $unique_name]);
            }
        }
    }
}

try {
    // Update data kos
    $query = "UPDATE kos SET 
              nama_kos = ?, jumlah_kamar = ?, tipe_kos = ?, kota = ?, 
              harga_per_bulan = ?, deskripsi = ?, alamat_lengkap = ?, 
              fasilitas_utama = ?, no_hp_kos = ?, link_maps = ?, 
              peraturan_kos = ?, area_sekitar_kos = ? 
              WHERE id_kos = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$nama_kos, $jumlah_kamar, $tipe_kos, $kota, 
                    $harga_per_bulan, $deskripsi, $alamat_lengkap, 
                    $fasilitas_utama, $no_hp_kos, $link_maps, 
                    $peraturan_kos, $area_sekitar_kos, $id_kos]);
    
    echo json_encode(["status" => "success", "message" => "Kos berhasil diperbarui"]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>