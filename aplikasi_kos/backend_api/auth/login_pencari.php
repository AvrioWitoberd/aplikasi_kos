<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
session_start();

require_once '../config/database.php'; 

$email = isset($_POST['email']) ? trim($_POST['email']) : "";
$password = isset($_POST['password']) ? trim($_POST['password']) : "";

if (empty($email) || empty($password)) {
    echo json_encode(["status" => "error", "message" => "Email dan Password tidak boleh kosong!"]);
    exit;
}

// Cek user dengan role pencari
$query = "SELECT * FROM users WHERE email = :email AND role = 'pencari' LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->execute(['email' => $email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode(["status" => "error", "message" => "Email tidak terdaftar sebagai pencari kos"]);
    exit;
}

// Cek password (plain text dulu, nanti bisa diubah ke hash)
if ($password !== $user['password']) {
    echo json_encode(["status" => "error", "message" => "Password salah!"]);
    exit;
}

// Cek status user
if ($user['status'] != 'aktif') {
    echo json_encode(["status" => "error", "message" => "Akun Anda belum aktif. Silakan hubungi admin."]);
    exit;
}

// Login sukses
$_SESSION['user_id'] = $user['id_user'];
$_SESSION['role'] = 'pencari';

echo json_encode([
    "status" => "success",
    "data" => [
        "id_user" => $user['id_user'],
        "nama_lengkap" => $user['nama_lengkap'],
        "email" => $user['email'],
        "role" => $user['role']
    ]
]);
?>