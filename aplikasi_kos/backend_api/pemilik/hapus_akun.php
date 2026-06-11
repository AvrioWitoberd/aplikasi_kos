<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
session_start();

require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pemilik') {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

$id_user = $_SESSION['user_id'];
$confirm_password = $_POST['confirm_password'] ?? '';

if (empty($confirm_password)) {
    echo json_encode(["status" => "error", "message" => "Password wajib diisi"]);
    exit;
}

try {
    // Cek password
    $stmt = $conn->prepare("SELECT password FROM users WHERE id_user = ?");
    $stmt->execute([$id_user]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user || $confirm_password !== $user['password']) {
        echo json_encode(["status" => "error", "message" => "Password salah"]);
        exit;
    }
    
    $conn->beginTransaction();
    
    // Ambil semua id_kos milik user
    $stmtKos = $conn->prepare("SELECT id_kos FROM kos WHERE id_pemilik = ?");
    $stmtKos->execute([$id_user]);
    $kosList = $stmtKos->fetchAll(PDO::FETCH_COLUMN);
    
    // Hapus data terkait setiap kos
    foreach ($kosList as $id_kos) {
        // Hapus favorit
        $conn->prepare("DELETE FROM favorit WHERE id_kos = ?")->execute([$id_kos]);
        // Hapus rating
        $conn->prepare("DELETE FROM rating WHERE id_kos = ?")->execute([$id_kos]);
        // Hapus foto
        $conn->prepare("DELETE FROM foto_kos WHERE id_kos = ?")->execute([$id_kos]);
    }
    
    // Hapus kos
    $conn->prepare("DELETE FROM kos WHERE id_pemilik = ?")->execute([$id_user]);
    
    // Hapus profil_kos jika ada
    $conn->prepare("DELETE FROM profil_kos WHERE id_user = ?")->execute([$id_user]);
    
    // Hapus user
    $conn->prepare("DELETE FROM users WHERE id_user = ?")->execute([$id_user]);
    
    $conn->commit();
    
    // Hapus session
    session_destroy();
    
    echo json_encode(["status" => "success", "message" => "Akun berhasil dihapus"]);
} catch (PDOException $e) {
    $conn->rollBack();
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>