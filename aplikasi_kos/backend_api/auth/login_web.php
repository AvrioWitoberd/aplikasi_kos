<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
session_start();

// Sesuaikan path ke file database kamu
require_once '../config/database.php'; 

$email = isset($_POST['email']) ? trim($_POST['email']) : "";
$password = isset($_POST['password']) ? trim($_POST['password']) : "";

if (!empty($email) && !empty($password)) {
    // 1. Cek apakah email sudah terdaftar?
    $query = "SELECT * FROM users WHERE email = :email LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        // --- KONDISI: USER BARU (DAFTAR OTOMATIS) ---
        $insert = $conn->prepare("INSERT INTO users (email, password, nama_lengkap, role, status) VALUES (?, ?, ?, 'pemilik', 'pending')");
        if ($insert->execute([$email, $password, 'Calon Pemilik'])) {
            $new_id = $conn->lastInsertId();
            
            // TAMBAHKAN BARIS INI AGAR TIDAK KOMBAL-KAMPUL KE LOGIN LAGI
            $_SESSION['user_id'] = $new_id; 
            $_SESSION['role'] = 'pemilik';

            echo json_encode([
                "status" => "need_profile",
                "id_user" => $new_id,
                "message" => "Akun baru berhasil dibuat!"
            ]);
        }
    } else {
        // --- KONDISI: USER LAMA (CEK PASSWORD) ---
        if ($password == $user['password']) { // Menggunakan plain text sesuai alur kamu
            $uid = $user['id_user'];

            // KHUSUS ADMIN
            if ($user['role'] == 'admin') {
                $_SESSION['user_id'] = $uid;
                $_SESSION['role'] = 'admin';
                echo json_encode(["status" => "success", "role" => "admin"]);
                exit;
            }

            // --- LOGIKA PEMILIK: CEK PROFIL ---
            $stmtProfil = $conn->prepare("SELECT id_user FROM profil_kos WHERE id_user = ?");
            $stmtProfil->execute([$uid]);
            $profilExist = $stmtProfil->fetch();

            if (!$profilExist) {
                $_SESSION['user_id'] = $uid; // TAMBAHKAN INI
                $_SESSION['role'] = 'pemilik'; // TAMBAHKAN INI
                echo json_encode(["status" => "need_profile", "id_user" => $uid]);
            } else {
                // Sudah isi profil, cek status ACC admin
                if ($user['status'] == 'pending') {
                    echo json_encode(["status" => "error_message", "message" => "Akun anda dalam masa pengajuan, tunggu admin memvalidasi akun dalam waktu 24 jam."]);
                } elseif ($user['status'] == 'nonaktif') {
                    $link = 'https://wa.me/6281332077170?text=Halo%20Admin%20MyKos';
                    echo json_encode(["status" => "error_message", "message" => "Akun anda dinonaktifkan. <a href='$link' target='_blank' class='text-success'>Hubungi Pusat Bantuan</a>."]);
                } else {
                    // STATUS AKTIF
                    $_SESSION['user_id'] = $uid;
                    $_SESSION['role'] = $user['role'];
                    echo json_encode(["status" => "success", "role" => "pemilik"]);
                }
            }
        } else {
            echo json_encode(["status" => "error_message", "message" => "Email sudah terdaftar, tapi password salah!"]);
        }
    }
} else {
    echo json_encode(["status" => "error_message", "message" => "Email dan Password tidak boleh kosong!"]);
}