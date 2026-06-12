<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
session_start();

// Enable error reporting untuk debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/database.php'; 

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Sesi habis, silakan login kembali."]);
    exit;
}

$id_user = $_SESSION['user_id'];

// Definisikan Folder Penyimpanan (Path dari file API ini)
// File API di: backend_api/profile/submit_profil_pemilik.php
$dir_kos   = dirname(__DIR__, 2) . "/uploads/profil_kos/";      // mykos_project/uploads/profil_kos/
$dir_ktp   = dirname(__DIR__, 2) . "/uploads/ktp/";      // mykos_project/uploads/ktp/
$dir_bukti = dirname(__DIR__, 2) . "/uploads/bukti_bayar/"; // mykos_project/uploads/bukti_bayar/

// Buat folder jika belum ada
if (!is_dir($dir_kos))   mkdir($dir_kos, 0777, true);
if (!is_dir($dir_ktp))   mkdir($dir_ktp, 0777, true);
if (!is_dir($dir_bukti)) mkdir($dir_bukti, 0777, true);

// Cek apakah folder bisa ditulisi
if (!is_writable($dir_ktp)) {
    echo json_encode(["status" => "error", "message" => "Folder KTP tidak bisa ditulisi: " . $dir_ktp]);
    exit;
}

function uploadFile($file, $target_dir) {
    // Debug: log apa yang diterima
    error_log("Uploading file to: " . $target_dir);
    error_log("File data: " . print_r($file, true));
    
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        error_log("File error code: " . ($file['error'] ?? 'no file'));
        return false;
    }
    
    $extension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $fileName = time() . '_' . uniqid() . '.' . $extension;
    $targetFile = $target_dir . $fileName;

    if ($file["size"] > 2097152) {
        error_log("File too big: " . $file["size"]);
        return false;
    }
    
    if (!in_array($extension, ['jpg', 'png', 'jpeg'])) {
        error_log("Invalid extension: " . $extension);
        return false;
    }

    if (move_uploaded_file($file["tmp_name"], $targetFile)) {
        error_log("File uploaded successfully: " . $fileName);
        return $fileName;
    }
    
    error_log("Failed to move uploaded file from " . $file["tmp_name"] . " to " . $targetFile);
    return false;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil input text
    $nama_kos = $_POST['nama_kos'] ?? '';
    $deskripsi = $_POST['deskripsi'] ?? '';
    $kota = $_POST['kota'] ?? '';
    $alamat_lengkap = $_POST['alamat_lengkap'] ?? '';
    $nama_pemilik = $_POST['nama_pemilik'] ?? '';
    $usia = $_POST['usia'] ?? '';
    $kontak = $_POST['kontak'] ?? '';

    // Proses Upload
    $foto_ktp    = uploadFile($_FILES['foto_ktp'], $dir_ktp);
    $bukti_bayar = uploadFile($_FILES['bukti_bayar'], $dir_bukti);

    $foto_galeri = [];
    if (isset($_FILES['foto_kos'])) {
        foreach ($_FILES['foto_kos']['name'] as $key => $val) {
            if ($_FILES['foto_kos']['error'][$key] === UPLOAD_ERR_OK) {
                $file_arr = [
                    'name'     => $_FILES['foto_kos']['name'][$key],
                    'tmp_name' => $_FILES['foto_kos']['tmp_name'][$key],
                    'error'    => $_FILES['foto_kos']['error'][$key],
                    'size'     => $_FILES['foto_kos']['size'][$key]
                ];
                $up = uploadFile($file_arr, $dir_kos);
                if ($up) $foto_galeri[] = $up;
            }
        }
    }
    $all_photos = implode(',', $foto_galeri);

    // Debug: cek hasil upload
    error_log("foto_ktp: " . ($foto_ktp ?: 'FAILED'));
    error_log("bukti_bayar: " . ($bukti_bayar ?: 'FAILED'));
    error_log("foto_galeri count: " . count($foto_galeri));

    // Validasi
    $errors = [];
    if (!$foto_ktp) $errors[] = "Foto KTP gagal upload";
    if (!$bukti_bayar) $errors[] = "Bukti bayar gagal upload";
    if (count($foto_galeri) == 0) $errors[] = "Foto kos gagal upload (minimal 1 foto)";
    
    if (empty($errors)) {
        try {
            $conn->beginTransaction();

            $sql = "INSERT INTO profil_kos (id_user, nama_kos, deskripsi, kota, alamat_lengkap, foto_kos, foto_ktp, nama_pemilik, usia, kontak, bukti_bayar) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$id_user, $nama_kos, $deskripsi, $kota, $alamat_lengkap, $all_photos, $foto_ktp, $nama_pemilik, $usia, $kontak, $bukti_bayar]);

            $updateUser = $conn->prepare("UPDATE users SET nama_lengkap = ? WHERE id_user = ?");
            $updateUser->execute([$nama_pemilik, $id_user]);

            $conn->commit();
            echo json_encode(["status" => "success", "nama_pemilik" => $nama_pemilik]);
        } catch (Exception $e) {
            $conn->rollBack();
            echo json_encode(["status" => "error", "message" => "Database Error: " . $e->getMessage()]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => implode(", ", $errors)]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Method tidak diizinkan"]);
}
?>