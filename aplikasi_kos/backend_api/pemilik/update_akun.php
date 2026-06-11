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
$new_email = $_POST['new_email'] ?? '';
$new_password = $_POST['new_password'] ?? '';
$old_password = $_POST['old_password'] ?? '';

if (empty($new_email) || empty($old_password)) {
    echo json_encode(["status" => "error", "message" => "Email baru dan password lama wajib diisi"]);
    exit;
}

try {
    // Cek user
    $stmt = $conn->prepare("SELECT password FROM users WHERE id_user = ?");
    $stmt->execute([$id_user]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        echo json_encode(["status" => "error", "message" => "User tidak ditemukan"]);
        exit;
    }
    
    // VERIFIKASI PASSWORD - Karena password plain text, bandingkan langsung
    if ($old_password !== $user['password']) {
        echo json_encode(["status" => "error", "message" => "Password lama salah"]);
        exit;
    }
    
    // Cek email sudah digunakan user lain
    $checkEmail = $conn->prepare("SELECT id_user FROM users WHERE email = ? AND id_user != ?");
    $checkEmail->execute([$new_email, $id_user]);
    if ($checkEmail->rowCount() > 0) {
        echo json_encode(["status" => "error", "message" => "Email sudah digunakan"]);
        exit;
    }
    
    // Update email
    $update = $conn->prepare("UPDATE users SET email = ? WHERE id_user = ?");
    $update->execute([$new_email, $id_user]);
    
    // Update password jika diisi
    if (!empty($new_password)) {
        $updatePass = $conn->prepare("UPDATE users SET password = ? WHERE id_user = ?");
        $updatePass->execute([$new_password, $id_user]);
    }
    
    echo json_encode(["status" => "success", "message" => "Email dan password berhasil diperbarui"]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>