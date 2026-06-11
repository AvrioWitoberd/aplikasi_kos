<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
session_start();

require_once '../config/database.php';

$email = isset($_POST['email']) ? trim($_POST['email']) : "";
$nama_lengkap = isset($_POST['nama_lengkap']) ? trim($_POST['nama_lengkap']) : "";
$google_id = isset($_POST['google_id']) ? trim($_POST['google_id']) : "";

if (empty($email)) {
    echo json_encode(["status" => "error", "message" => "Email tidak ditemukan"]);
    exit;
}

try {
    // Cek apakah user sudah ada
    $query = "SELECT * FROM users WHERE email = :email";
    $stmt = $conn->prepare($query);
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        // User sudah ada, update google_id jika belum
        if (empty($user['google_id'])) {
            $update = $conn->prepare("UPDATE users SET google_id = :google_id WHERE id_user = :id_user");
            $update->execute(['google_id' => $google_id, 'id_user' => $user['id_user']]);
        }
        
        $_SESSION['user_id'] = $user['id_user'];
        $_SESSION['role'] = $user['role'];
        
        echo json_encode([
            "status" => "success",
            "data" => [
                "id_user" => $user['id_user'],
                "nama_lengkap" => $user['nama_lengkap'],
                "email" => $user['email'],
                "no_hp" => $user['no_hp'],
                "role" => $user['role']
            ]
        ]);
    } else {
        // User baru, buat akun dengan role pencari
        $insert = $conn->prepare("INSERT INTO users (email, nama_lengkap, google_id, role, status) VALUES (?, ?, ?, 'pencari', 'aktif')");
        $insert->execute([$email, $nama_lengkap, $google_id]);
        $new_id = $conn->lastInsertId();
        
        $_SESSION['user_id'] = $new_id;
        $_SESSION['role'] = 'pencari';
        
        echo json_encode([
            "status" => "success",
            "data" => [
                "id_user" => $new_id,
                "nama_lengkap" => $nama_lengkap,
                "email" => $email,
                "no_hp" => "",
                "role" => "pencari"
            ]
        ]);
    }
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>